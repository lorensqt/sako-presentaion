<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard / Overview Panel.
     */
    public function dashboard()
    {
        // Dynamic stats derived from the DB
        $totalUsersCount = User::count();
        $membersCount = User::where('role', 'member')->count();
        $adminsCount = User::whereIn('role', ['admin', 'super_admin'])->count();

        // High-fidelity presentation metrics
        $cooperativeSavings = 14249000.00; // ₱14.2M representation
        $activeLoansVolume = 4850000.00;   // ₱4.85M representation
        $pendingApprovalsCount = 8;        // Outstanding queue count
        $reservePool = 1200000.00;         // ₱1.2M emergency reserve

        // Quick list of recently joined members
        $recentMembers = User::where('role', 'member')
            ->latest()
            ->take(5)
            ->get();

        // High-fidelity mock logs to make the audit preview feel fully alive
        $recentAuditLogs = [
            [
                'action' => 'User Promotion',
                'description' => 'Promoted Laurence Castillo to administrator role.',
                'user' => 'System Root',
                'time' => '10 mins ago'
            ],
            [
                'action' => 'Loan Approved',
                'description' => 'Approved Commodity Loan #LN-49201 for ₱50,000.',
                'user' => 'Admin Executive',
                'time' => '1 hour ago'
            ],
            [
                'action' => 'System Variable Change',
                'description' => 'Updated Travel Loan term limit to 24 months.',
                'user' => 'System Root',
                'time' => '3 hours ago'
            ],
            [
                'action' => 'User Creation',
                'description' => 'Registered new member Jane Doe (ID: 20248217).',
                'user' => 'Admin Executive',
                'time' => '5 hours ago'
            ]
        ];

        return view('admin.dashboard', compact(
            'totalUsersCount',
            'membersCount',
            'adminsCount',
            'cooperativeSavings',
            'activeLoansVolume',
            'pendingApprovalsCount',
            'reservePool',
            'recentMembers',
            'recentAuditLogs'
        ));
    }

    /**
     * Display the System Audit & Security Logs page.
     */
    public function auditLogs(Request $request)
    {
        $query = \App\Models\AuditLog::with('actor');

        // Filter by Search Query
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('actor', function ($actorQuery) use ($search) {
                      $actorQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        // Filter by Action Type
        if ($request->filled('action_type')) {
            $actionType = $request->input('action_type');
            if ($actionType === 'auth') {
                $query->where(function ($q) {
                    $q->where('action', 'like', 'auth_%')->orWhere('action', 'like', 'security_%');
                });
            } elseif ($actionType === 'member') {
                $query->where('action', 'like', 'member_%');
            } elseif ($actionType === 'withdrawal') {
                $query->where('action', 'like', 'withdrawal_%');
            } elseif ($actionType === 'loan') {
                $query->where('action', 'like', 'loan_%');
            } elseif ($actionType === 'deduction') {
                $query->where('action', 'like', 'deduction_%');
            } elseif ($actionType === 'compliance') {
                $query->where('action', 'like', 'compliance_%');
            }
        }

        // Date Filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        if ($request->ajax()) {
            return view('admin.partials.audit-logs-table', compact('logs'));
        }

        // Calculate counts for KPI cards
        $totalCount = \App\Models\AuditLog::count();
        $authCount = \App\Models\AuditLog::where(function ($q) {
            $q->where('action', 'like', 'auth_%')->orWhere('action', 'like', 'security_%');
        })->count();
        $warningCount = \App\Models\AuditLog::where('severity', 'warning')->count();
        $dangerCount = \App\Models\AuditLog::where('severity', 'danger')->count();

        return view('admin.audit-logs', compact('logs', 'totalCount', 'authCount', 'warningCount', 'dangerCount'));
    }

    /**
     * Display the Cooperative Members Directory page.
     */
    public function members(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('roles')
            ->where('role', 'member')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_id', 'like', "%{$search}%")
                      ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $roles = Role::all();

        return view('admin.members', compact('users', 'search', 'roles'));
    }

    /**
     * Store a newly created cooperative member.
     */
    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'company_id' => 'required|string|max:50|unique:users',
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $password = $validated['password'] ?? 'password';

        $disk = User::signatureDisk();
        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', $disk);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_id' => $validated['company_id'],
            'role' => 'member',
            'admin_permissions' => null,
            'contact_number' => $validated['contact_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'signature' => $signaturePath,
            'password' => Hash::make($password),
        ]);

        AuditLogger::log('member_created', "Admin " . auth()->user()->name . " registered a new cooperative member: {$user->name} ({$user->company_id}).", 'info', $user, null, $user->only(['name', 'email', 'company_id', 'role', 'signature']));

        return redirect()->route('admin.members')->with('success', 'Cooperative member registered successfully!');
    }

    /**
     * Update an existing cooperative member's details.
     */
    public function updateMember(Request $request, User $user)
    {
        if ($user->role !== 'member') {
            return redirect()->route('admin.members')->with('error', 'Action restricted: Only cooperative members can be edited from this directory.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'company_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users')->ignore($user->id),
            ],
            'contact_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'remove_signature' => 'nullable|boolean',
        ]);

        $oldValues = $user->only(['name', 'email', 'company_id', 'contact_number', 'address', 'signature']);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_id' => $validated['company_id'],
            'contact_number' => $validated['contact_number'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        $disk = User::signatureDisk();
        if ($request->boolean('remove_signature')) {
            if ($user->signature && Storage::disk($disk)->exists($user->signature)) {
                Storage::disk($disk)->delete($user->signature);
            }
            $updateData['signature'] = null;
        } elseif ($request->hasFile('signature')) {
            if ($user->signature && Storage::disk($disk)->exists($user->signature)) {
                Storage::disk($disk)->delete($user->signature);
            }
            $updateData['signature'] = $request->file('signature')->store('signatures', $disk);
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        $newValues = $user->fresh()->only(['name', 'email', 'company_id', 'contact_number', 'address', 'signature']);

        AuditLogger::log('member_updated', "Admin " . auth()->user()->name . " updated member details for {$user->name}.", 'info', $user, $oldValues, $newValues);

        return redirect()->route('admin.members')->with('success', 'Member details updated successfully!');
    }

    /**
     * Remove a member from the database.
     */
    public function deleteMember(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.members')->with('error', 'Security Violation: You cannot delete your own session account!');
        }

        if ($user->role !== 'member') {
            return redirect()->route('admin.members')->with('error', 'Action restricted: Only cooperative members can be removed from this directory.');
        }

        $oldValues = $user->only(['id', 'name', 'email', 'company_id', 'role']);

        $user->delete();

        AuditLogger::log('member_deleted', "Admin " . auth()->user()->name . " deleted member account {$oldValues['name']}.", 'danger', null, $oldValues, null);

        return redirect()->route('admin.members')->with('success', 'Cooperative member removed successfully.');
    }

    /**
     * Force reset password and/or security PIN for a cooperative member.
     */
    public function resetMemberCredentials(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.members')->with('error', 'Security Violation: You cannot force-reset your own session credentials through this panel.');
        }

        if ($user->role !== 'member') {
            return redirect()->route('admin.members')->with('error', 'Action restricted: Only cooperative members can be managed from this directory.');
        }

        if (!$request->boolean('reset_password') && !$request->boolean('reset_pin')) {
            return back()->withErrors(['reset_error' => 'Please select at least one credential option to reset (Password or PIN).']);
        }

        $request->validate([
            'reset_password' => 'nullable|boolean',
            'password' => 'required_if:reset_password,1|nullable|string|min:6',
            'reset_pin' => 'nullable|boolean',
            'pin_mode' => 'required_if:reset_pin,1|nullable|string|in:clear,manual',
            'pin' => 'required_if:pin_mode,manual|nullable|digits:6',
        ]);

        $oldPinConfigured = !is_null($user->pin);
        $oldPinAttempts = $user->pin_attempts;
        $actionsTaken = [];

        if ($request->boolean('reset_password')) {
            $user->password = Hash::make($request->input('password'));
            $actionsTaken[] = 'Password';
        }

        if ($request->boolean('reset_pin')) {
            $pinMode = $request->input('pin_mode');
            if ($pinMode === 'clear') {
                $user->pin = null;
                $actionsTaken[] = 'Security PIN cleared (User will configure on next login)';
            } elseif ($pinMode === 'manual') {
                $user->pin = Hash::make($request->input('pin'));
                $actionsTaken[] = 'Security PIN updated';
            }
            $user->pin_attempts = 0;
        } elseif ($request->boolean('reset_password')) {
            // Also reset PIN attempts to lift lockout if user forgot credentials
            $user->pin_attempts = 0;
        }

        $user->save();

        AuditLogger::log(
            'member_credential_reset',
            "Admin " . auth()->user()->name . " force-reset credentials for member {$user->name} (" . implode(', ', $actionsTaken) . ").",
            'warning',
            $user,
            [
                'pin_configured' => $oldPinConfigured,
                'pin_attempts' => $oldPinAttempts,
            ],
            [
                'password_reset' => $request->boolean('reset_password'),
                'pin_reset' => $request->boolean('reset_pin'),
                'pin_mode' => $request->input('pin_mode', null),
                'pin_attempts' => 0,
            ]
        );

        return redirect()->route('admin.members')->with('success', "Credentials for {$user->name} updated successfully (" . implode(', ', $actionsTaken) . ").");
    }

    /**
     * Display the System Administrators & Staff management page (Super Admin only).
     */
    public function administrators(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('roles')
            ->whereIn('role', ['admin', 'super_admin'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_id', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $roles = Role::all();
        if ($roles->isEmpty()) {
            \Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder', '--force' => true]);
            $roles = Role::all();
        }

        $availablePermissions = User::allAdminPagePermissions();

        // Fetch current HRMD staff list ordered by their assigned sequence for visual hierarchy reference
        $hrmdStaffList = User::whereHas('roles', fn($q) => $q->where('slug', 'hrmd_staff'))
            ->orderByRaw('hrmd_sequence IS NULL, hrmd_sequence ASC')
            ->get();

        return view('admin.administrators', compact('users', 'search', 'roles', 'availablePermissions', 'hrmdStaffList'));
    }

    /**
     * Store a newly created system administrator (Super Admin only).
     */
    public function storeAdministrator(Request $request)
    {
        $validPermissions = array_keys(User::allAdminPagePermissions());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|string|max:50|unique:users',
            'email' => 'nullable|string|email|max:255|unique:users',
            'role' => 'required|string|in:admin,super_admin',
            'password' => 'nullable|string|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'admin_permissions' => 'nullable|array',
            'admin_permissions.*' => 'string|in:' . implode(',', $validPermissions),
            'hrmd_sequence' => 'nullable|integer|min:1',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $adminPermissions = ($validated['role'] === 'admin')
            ? ($request->input('admin_permissions', []))
            : null;

        $roleIds = $request->input('roles', []);
        $hrmdRole = Role::firstOrCreate(
            ['slug' => 'hrmd_staff'],
            ['name' => 'HRMD Representative', 'description' => 'Verifying active employment status and basic salary constraints.']
        );

        // If an HRMD sequence is provided, automatically ensure the hrmd_staff role is attached
        if ($request->filled('hrmd_sequence')) {
            if (!in_array((string)$hrmdRole->id, array_map('strval', $roleIds), true)) {
                $roleIds[] = $hrmdRole->id;
            }
            $hrmdSequence = (int) $request->input('hrmd_sequence');
        } else {
            $hasHrmdRole = in_array((string)$hrmdRole->id, array_map('strval', $roleIds), true);
            $hrmdSequence = $hasHrmdRole && $request->filled('hrmd_sequence') ? (int) $request->input('hrmd_sequence') : null;
        }

        $password = $validated['password'] ?? 'password';

        $disk = User::signatureDisk();
        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', $disk);
        }

        $user = User::create([
            'name' => $validated['name'],
            'company_id' => $validated['company_id'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'],
            'hrmd_sequence' => $hrmdSequence,
            'admin_permissions' => $adminPermissions,
            'signature' => $signaturePath,
            'password' => Hash::make($password),
        ]);

        $user->roles()->sync($roleIds);

        AuditLogger::log('admin_created', "Super Admin " . auth()->user()->name . " created administrative account: {$user->name} ({$user->company_id}) with role {$user->role}.", 'warning', $user, null, $user->only(['name', 'company_id', 'email', 'role', 'hrmd_sequence', 'admin_permissions', 'signature']));

        return redirect()->route('admin.administrators')->with('success', 'Administrator account registered successfully!');
    }

    /**
     * Update an existing administrator's credentials and page access (Super Admin only).
     */
    public function updateAdministrator(Request $request, User $user)
    {
        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            return redirect()->route('admin.administrators')->with('error', 'Account is not an administrator.');
        }

        $validPermissions = array_keys(User::allAdminPagePermissions());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|string|in:admin,super_admin',
            'password' => 'nullable|string|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'admin_permissions' => 'nullable|array',
            'admin_permissions.*' => 'string|in:' . implode(',', $validPermissions),
            'hrmd_sequence' => 'nullable|integer|min:1',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'remove_signature' => 'nullable|boolean',
        ]);

        if (auth()->id() === $user->id && $validated['role'] !== 'super_admin') {
            return redirect()->route('admin.administrators')->with('error', 'Security Protection: You cannot demote your own Super Administrator account.');
        }

        $oldValues = $user->only(['name', 'company_id', 'email', 'role', 'hrmd_sequence', 'admin_permissions', 'signature']);

        $adminPermissions = ($validated['role'] === 'admin')
            ? ($request->input('admin_permissions', []))
            : null;

        $roleIds = $request->input('roles', []);
        $hrmdRole = Role::firstOrCreate(
            ['slug' => 'hrmd_staff'],
            ['name' => 'HRMD Representative', 'description' => 'Verifying active employment status and basic salary constraints.']
        );

        // If an HRMD sequence is provided, automatically ensure the hrmd_staff role is attached
        if ($request->filled('hrmd_sequence')) {
            if (!in_array((string)$hrmdRole->id, array_map('strval', $roleIds), true)) {
                $roleIds[] = $hrmdRole->id;
            }
            $hrmdSequence = (int) $request->input('hrmd_sequence');
        } else {
            $hasHrmdRole = in_array((string)$hrmdRole->id, array_map('strval', $roleIds), true);
            $hrmdSequence = $hasHrmdRole && $request->filled('hrmd_sequence') ? (int) $request->input('hrmd_sequence') : null;
        }

        $updateData = [
            'name' => $validated['name'],
            'company_id' => $validated['company_id'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'],
            'hrmd_sequence' => $hrmdSequence,
            'admin_permissions' => $adminPermissions,
        ];

        $disk = User::signatureDisk();
        if ($request->boolean('remove_signature')) {
            if ($user->signature && Storage::disk($disk)->exists($user->signature)) {
                Storage::disk($disk)->delete($user->signature);
            }
            $updateData['signature'] = null;
        } elseif ($request->hasFile('signature')) {
            if ($user->signature && Storage::disk($disk)->exists($user->signature)) {
                Storage::disk($disk)->delete($user->signature);
            }
            $updateData['signature'] = $request->file('signature')->store('signatures', $disk);
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        $user->roles()->sync($roleIds);

        $newValues = $user->fresh()->only(['name', 'company_id', 'email', 'role', 'hrmd_sequence', 'admin_permissions', 'signature']);

        AuditLogger::log('admin_updated', "Super Admin " . auth()->user()->name . " updated administrator {$user->name}.", 'warning', $user, $oldValues, $newValues);

        return redirect()->route('admin.administrators')->with('success', 'Administrator details and permissions updated successfully!');
    }

    /**
     * Update currently logged in administrator's profile and signature.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'remove_signature' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
        ];

        $disk = User::signatureDisk();
        if ($request->boolean('remove_signature')) {
            if ($user->signature && Storage::disk($disk)->exists($user->signature)) {
                Storage::disk($disk)->delete($user->signature);
            }
            $updateData['signature'] = null;
        } elseif ($request->hasFile('signature')) {
            if ($user->signature && Storage::disk($disk)->exists($user->signature)) {
                Storage::disk($disk)->delete($user->signature);
            }
            $updateData['signature'] = $request->file('signature')->store('signatures', $disk);
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        AuditLogger::log('profile_updated', "Admin {$user->name} updated their profile details and official e-signature.", 'info', $user);

        return back()->with('success', 'Profile and official e-signature updated successfully!');
    }

    /**
     * Remove / reset an e-signature for a member or administrator (Super Admin or Account Owner).
     */
    public function deleteSignature(User $user)
    {
        if (auth()->user()->role !== 'super_admin' && auth()->id() !== $user->id) {
            return back()->with('error', 'Unauthorized: Only Super Administrators can reset a user signature.');
        }

        if ($user->signature) {
            $disk = User::signatureDisk();
            try {
                if (Storage::disk($disk)->exists($user->signature)) {
                    Storage::disk($disk)->delete($user->signature);
                }
            } catch (\Throwable $e) {}

            $user->update(['signature' => null]);

            AuditLogger::log('signature_removed', "Super Admin " . auth()->user()->name . " deleted signature for user {$user->name}.", 'warning', $user);
        }

        return back()->with('success', "E-signature for {$user->name} has been removed. The user can now re-upload a fresh signature.");
    }

    /**
     * Securely stream a user's signature to authenticated clients.
     */
    public function streamSignature(User $user)
    {
        if (empty($user->signature)) {
            abort(404, 'Signature not found.');
        }

        $headers = [
            'Content-Type' => 'image/png',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self' https://loans.sako-central.org https://sako-central.org https://*.sako-central.org http://localhost:* http://127.0.0.1:*",
        ];

        $disk = User::signatureDisk();

        try {
            // 1. Cloud bucket primary check
            if (Storage::disk($disk)->exists($user->signature)) {
                return Storage::disk($disk)->response($user->signature, basename($user->signature), $headers);
            }

            // 2. Legacy local storage fallback
            if (Storage::disk('public')->exists($user->signature)) {
                return Storage::disk('public')->response($user->signature, basename($user->signature), $headers);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to stream signature for user {$user->id}: " . $e->getMessage());
        }

        abort(404, 'Signature file could not be located.');
    }

    /**
     * Remove an administrator account (Super Admin only).
     */
    public function deleteAdministrator(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.administrators')->with('error', 'Security Violation: You cannot delete your own session account!');
        }

        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            return redirect()->route('admin.administrators')->with('error', 'Account is not an administrator.');
        }

        $oldValues = $user->only(['id', 'name', 'company_id', 'email', 'role']);

        $user->delete();

        AuditLogger::log('admin_deleted', "Super Admin " . auth()->user()->name . " removed administrator {$oldValues['name']}.", 'danger', null, $oldValues, null);

        return redirect()->route('admin.administrators')->with('success', 'Administrator account removed successfully.');
    }

    /**
     * Force reset password and/or security PIN for an administrator account (Super Admin only).
     */
    public function resetAdminCredentials(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.administrators')->with('error', 'Security Protection: You cannot reset your own credentials through this panel.');
        }

        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            return redirect()->route('admin.administrators')->with('error', 'Account is not an administrator.');
        }

        if (!$request->boolean('reset_password') && !$request->boolean('reset_pin')) {
            return back()->withErrors(['reset_error' => 'Please select at least one credential option to reset (Password or PIN).']);
        }

        $request->validate([
            'reset_password' => 'nullable|boolean',
            'password' => 'required_if:reset_password,1|nullable|string|min:6',
            'reset_pin' => 'nullable|boolean',
            'pin_mode' => 'required_if:reset_pin,1|nullable|string|in:clear,manual',
            'pin' => 'required_if:pin_mode,manual|nullable|digits:6',
        ]);

        $oldPinConfigured = !is_null($user->pin);
        $oldPinAttempts = $user->pin_attempts;
        $actionsTaken = [];

        if ($request->boolean('reset_password')) {
            $user->password = Hash::make($request->input('password'));
            $actionsTaken[] = 'Password';
        }

        if ($request->boolean('reset_pin')) {
            $pinMode = $request->input('pin_mode');
            if ($pinMode === 'clear') {
                $user->pin = null;
                $actionsTaken[] = 'Security PIN cleared (Operator will configure on next login)';
            } elseif ($pinMode === 'manual') {
                $user->pin = Hash::make($request->input('pin'));
                $actionsTaken[] = 'Security PIN updated';
            }
            $user->pin_attempts = 0;
        } elseif ($request->boolean('reset_password')) {
            $user->pin_attempts = 0;
        }

        $user->save();

        AuditLogger::log(
            'admin_credential_reset',
            "Super Admin " . auth()->user()->name . " force-reset credentials for administrator {$user->name} (" . implode(', ', $actionsTaken) . ").",
            'warning',
            $user,
            [
                'pin_configured' => $oldPinConfigured,
                'pin_attempts' => $oldPinAttempts,
            ],
            [
                'password_reset' => $request->boolean('reset_password'),
                'pin_reset' => $request->boolean('reset_pin'),
                'pin_mode' => $request->input('pin_mode', null),
                'pin_attempts' => 0,
            ]
        );

        return redirect()->route('admin.administrators')->with('success', "Credentials for administrator {$user->name} updated successfully (" . implode(', ', $actionsTaken) . ").");
    }

    /**
     * Display all Cooperative Loans Directory.
     */
    public function loans(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = \App\Models\LoanApplication::with('borrower');

        if ($search) {
            $query->whereHas('borrower', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_id', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $allLoans = $query->latest()->paginate(15)->withQueryString();

        // Calculate simple directory metrics
        $metrics = [
            'total' => \App\Models\LoanApplication::count(),
            'pending' => \App\Models\LoanApplication::where('status', 'pending')->count(),
            'approved' => \App\Models\LoanApplication::whereIn('status', ['approved', 'released'])->count(),
            'rejected' => \App\Models\LoanApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.loans', compact('allLoans', 'metrics'));
    }

    /**
     * Display the Admin Loans Queue and Decision Hub.
     */
    public function loanApprovals(Request $request)
    {
        $user = $request->user();
        
        // Find which roles/groups the logged-in admin belongs to (e.g. ['sako_staff', 'hrmd_staff'])
        $myGroupSlugs = $user->roles->pluck('slug')->toArray();

        // 1. Fetch loans sitting at this specific user's active stage
        $myInboxLoansQuery = \App\Models\LoanApplication::with(['borrower', 'approvals.actor', 'documents'])
            ->where('status', 'pending')
            ->whereIn('current_stage', $myGroupSlugs);

        // Granular HRMD sequence filter: For regular admins with hrmd_staff role,
        // only show in actionable inbox if the loan's current_hrmd_sequence matches their assigned sequence
        if ($user->role !== 'super_admin' && in_array('hrmd_staff', $myGroupSlugs, true)) {
            $myInboxLoansQuery->where(function ($query) use ($user) {
                $query->where('current_stage', '!=', 'hrmd_staff')
                    ->orWhere(function ($hrmdSub) use ($user) {
                        $hrmdSub->where('current_stage', 'hrmd_staff')
                            ->where('current_hrmd_sequence', $user->hrmd_sequence);
                    });
            });
        }

        $myInboxLoans = $myInboxLoansQuery->latest()->get();

        // 2. Fetch all cooperative loans sitting in the pipeline
        $allLoans = \App\Models\LoanApplication::with(['borrower', 'approvals.actor', 'documents'])
            ->latest()
            ->paginate(15);

        // Calculate queue metrics
        $metrics = [
            'my_inbox' => $myInboxLoans->count(),
            'total_pending' => \App\Models\LoanApplication::where('status', 'pending')->count(),
            'approved' => \App\Models\LoanApplication::where('status', 'approved')->count(),
            'rejected' => \App\Models\LoanApplication::where('status', 'rejected')->count(),
        ];

        // Resolve LoanWorkflowService to inject whole flow path details
        $workflowService = app(\App\Services\LoanWorkflowService::class);
        foreach ($myInboxLoans as $loan) {
            $loan->workflow_steps = $workflowService->getWorkflowDetails($loan);
        }
        foreach ($allLoans as $loan) {
            $loan->workflow_steps = $workflowService->getWorkflowDetails($loan);
        }

        return view('admin.loan-approvals', compact('myInboxLoans', 'allLoans', 'metrics', 'myGroupSlugs'));
    }

    /**
     * Delete/Destroy a loan application.
     */
    public function destroyApplication(\App\Models\LoanApplication $application)
    {
        $application->delete();
        return redirect()->route('admin.loans')->with('success', 'Loan application deleted successfully from directory.');
    }

    /**
     * Display the Admin Loans Product Configuration Workspace (CRUD).
     */
    public function loansManagement(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $status = $request->input('status');
        $hrmd = $request->input('hrmd');

        // Query dynamic loan products for management
        $query = Loan::orderBy('category')->orderBy('name');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('partner', 'like', "%{$search}%");
            });
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($status && $status !== 'all') {
            $isActive = $status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        if ($hrmd && $hrmd !== 'all') {
            $needsHrmd = $hrmd === 'yes' ? 1 : 0;
            $query->where('hrmd_approval', $needsHrmd);
        }

        $loanProducts = $query->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.partials.loans-table-rows', compact('loanProducts'))->render()
            ]);
        }

        return view('admin.loans-management', compact('loanProducts', 'search', 'category', 'status', 'hrmd'));
    }

    /**
     * Store a newly created loan product.
     */
    public function storeLoan(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'partner' => 'nullable|string|max:255',
            'loanable_amount' => 'nullable|string|max:255',
            'fixed_deposit' => 'required|numeric|min:0',
            'comakers' => 'required|string',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'available_terms' => 'nullable',
            'max_term_months' => 'nullable|integer|min:1',
            'minimum_membership_months' => 'nullable|integer|min:0',
            'approval_flow' => 'nullable',
        ]);

        $typeKey = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $validated['name']));

        // Handle comakers format (try parsing JSON, otherwise fallback to integer)
        $comakersValue = $validated['comakers'];
        $decoded = json_decode($comakersValue, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $comakersValue = $decoded;
        } elseif (is_numeric($comakersValue)) {
            $comakersValue = (int) $comakersValue;
        }

        // Handle approval flow format (JSON array or comma-separated string)
        $approvalFlowValue = $request->input('approval_flow');
        $decodedFlow = null;
        if (!empty($approvalFlowValue)) {
            $decoded = json_decode($approvalFlowValue, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $decodedFlow = $decoded;
            } else {
                $decodedFlow = array_filter(array_map('trim', explode(',', $approvalFlowValue)));
            }
        }

        // Parse and validate custom available terms
        $availableTerms = $this->parseAvailableTerms($request->input('available_terms'));
        $maxTermMonths = $validated['max_term_months'] ?? null;
        if (!empty($availableTerms)) {
            $highestTerm = max(array_column($availableTerms, 'months'));
            if (!$maxTermMonths || $maxTermMonths < $highestTerm) {
                $maxTermMonths = $highestTerm;
            }
        }

        $loan = Loan::create([
            'category' => $validated['category'],
            'type_key' => $typeKey,
            'name' => $validated['name'],
            'partner' => $validated['partner'] ?? null,
            'loanable_amount' => $validated['loanable_amount'] ?? null,
            'fixed_deposit' => $validated['fixed_deposit'],
            'comakers' => $comakersValue,
            'interest_rate' => $validated['interest_rate'],
            'available_terms' => $availableTerms,
            'max_term_months' => $maxTermMonths,
            'minimum_membership_months' => $validated['minimum_membership_months'] ?? null,
            'hrmd_approval' => is_array($decodedFlow) ? in_array('hrmd_staff', $decodedFlow) : $request->has('hrmd_approval'),
            'approval_flow' => $decodedFlow,
            'is_active' => true,
        ]);

        AuditLogger::log('loan_config_created', "Admin " . auth()->user()->name . " created a new loan product: {$loan->name}.", 'info', $loan, null, $loan->toArray());

        return redirect()->route('admin.loans.management')->with('success', 'New loan product created successfully!');
    }

    /**
     * Update an existing loan product's parameters.
     */
    public function updateLoan(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'partner' => 'nullable|string|max:255',
            'loanable_amount' => 'nullable|string|max:255',
            'fixed_deposit' => 'required|numeric|min:0',
            'comakers' => 'required|string',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'available_terms' => 'nullable',
            'max_term_months' => 'nullable|integer|min:1',
            'minimum_membership_months' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'approval_flow' => 'nullable',
        ]);

        // Handle comakers format (try parsing JSON, otherwise fallback to integer)
        $comakersValue = $validated['comakers'];
        $decoded = json_decode($comakersValue, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $comakersValue = $decoded;
        } elseif (is_numeric($comakersValue)) {
            $comakersValue = (int) $comakersValue;
        }

        // Handle approval flow format (JSON array or comma-separated string)
        $approvalFlowValue = $request->input('approval_flow');
        $decodedFlow = null;
        if (!empty($approvalFlowValue)) {
            $decoded = json_decode($approvalFlowValue, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $decodedFlow = $decoded;
            } else {
                $decodedFlow = array_filter(array_map('trim', explode(',', $approvalFlowValue)));
            }
        }

        // Parse and validate custom available terms
        $availableTerms = $this->parseAvailableTerms($request->input('available_terms'));
        $maxTermMonths = $validated['max_term_months'] ?? null;
        if (!empty($availableTerms)) {
            $highestTerm = max(array_column($availableTerms, 'months'));
            if (!$maxTermMonths || $maxTermMonths < $highestTerm) {
                $maxTermMonths = $highestTerm;
            }
        }

        $oldValues = $loan->toArray();

        $loan->update([
            'category' => $validated['category'],
            'name' => $validated['name'],
            'partner' => $validated['partner'] ?? null,
            'loanable_amount' => $validated['loanable_amount'] ?? null,
            'fixed_deposit' => $validated['fixed_deposit'],
            'comakers' => $comakersValue,
            'interest_rate' => $validated['interest_rate'],
            'available_terms' => $availableTerms,
            'max_term_months' => $maxTermMonths,
            'minimum_membership_months' => $validated['minimum_membership_months'] ?? null,
            'hrmd_approval' => is_array($decodedFlow) && in_array('hrmd_staff', $decodedFlow),
            'is_active' => $request->has('is_active'),
            'approval_flow' => $decodedFlow,
        ]);

        AuditLogger::log('loan_config_updated', "Admin " . auth()->user()->name . " updated parameters for loan product: {$loan->name}.", 'info', $loan, $oldValues, $loan->fresh()->toArray());

        return redirect()->route('admin.loans.management')->with('success', 'Loan product parameters updated successfully!');
    }

    /**
     * Parse and sanitize available terms input from request.
     * Expects an array or JSON string of [{ months: int, interest_rate: float }, ...].
     * Normalizes by sorting by month ascending and constraining months between 2 and 60.
     */
    protected function parseAvailableTerms(mixed $rawTerms): ?array
    {
        if (empty($rawTerms)) {
            return null;
        }

        if (is_string($rawTerms)) {
            $decoded = json_decode($rawTerms, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rawTerms = $decoded;
            } else {
                return null;
            }
        }

        if (!is_array($rawTerms)) {
            return null;
        }

        $cleaned = [];
        foreach ($rawTerms as $term) {
            if (isset($term['months']) && isset($term['interest_rate'])) {
                $months = (int) $term['months'];
                $rate = (float) $term['interest_rate'];
                if ($months >= 2 && $months <= 60 && $rate >= 0 && $rate <= 100) {
                    $cleaned[$months] = [
                        'months' => $months,
                        'interest_rate' => round($rate, 2),
                    ];
                }
            }
        }

        if (empty($cleaned)) {
            return null;
        }

        ksort($cleaned);
        return array_values($cleaned);
    }

    /**
     * Deactivate or delete a loan product.
     */
    public function deleteLoan(Loan $loan)
    {
        // For safety/historical reasons, if the product is linked to any application, deactivate it instead
        if ($loan->applications()->exists()) {
            $loan->update(['is_active' => false]);
            AuditLogger::log('loan_config_updated', "Admin " . auth()->user()->name . " deactivated loan product: {$loan->name} because it is already in use.", 'warning', $loan);
            return redirect()->route('admin.loans.management')->with('success', 'Loan product is in use by members, so it has been deactivated instead of deleted.');
        }

        $oldValues = $loan->toArray();
        $loan->delete();
        AuditLogger::log('loan_config_deleted', "Admin " . auth()->user()->name . " deleted loan product: {$oldValues['name']}.", 'danger', null, $oldValues);
        return redirect()->route('admin.loans.management')->with('success', 'Loan product deleted successfully.');
    }

    /**
     * Display the Admin Withdrawals Queue.
     */
    public function withdrawals(Request $request)
    {
        $withdrawals = \App\Models\WithdrawalRequest::with('user')
            ->latest()
            ->paginate(15);

        // Metrics for the withdrawal requests
        $metrics = [
            'pending' => \App\Models\WithdrawalRequest::where('status', 'pending')->count(),
            'processing' => \App\Models\WithdrawalRequest::where('status', 'processing')->count(),
            'released' => \App\Models\WithdrawalRequest::where('status', 'released')->count(),
        ];

        return view('admin.withdrawals', compact('withdrawals', 'metrics'));
    }

    /**
     * Update status of a withdrawal request.
     */
    public function updateWithdrawalStatus(Request $request, \App\Models\WithdrawalRequest $withdrawal)
    {
        $action = $request->input('action');

        if ($action === 'acknowledge' && $withdrawal->status === 'pending') {
            $request->validate([
                'transaction_id' => 'required|string|max:255',
            ]);

            $oldValues = $withdrawal->toArray();

            $withdrawal->update([
                'status' => 'processing',
                'transaction_id' => $request->input('transaction_id'),
            ]);

            AuditLogger::log('withdrawal_status_updated', "Admin " . auth()->user()->name . " acknowledged withdrawal request REF #WD-" . str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) . " for user {$withdrawal->user->name}.", 'info', $withdrawal, $oldValues, $withdrawal->fresh()->toArray());

            return redirect()->route('admin.withdrawals')->with([
                'success' => 'Withdrawal request REF #WD-' . str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) . ' has been acknowledged with Transaction ID ' . $request->input('transaction_id') . ' and is now processing.',
                'success_title' => 'Request Acknowledged'
            ]);
        }

        if ($action === 'release' && $withdrawal->status === 'processing') {
            $oldValues = $withdrawal->toArray();

            $withdrawal->update(['status' => 'released']);

            AuditLogger::log('withdrawal_status_updated', "Admin " . auth()->user()->name . " released funds for withdrawal request REF #WD-" . str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) . " for user {$withdrawal->user->name}.", 'info', $withdrawal, $oldValues, $withdrawal->fresh()->toArray());

            return redirect()->route('admin.withdrawals')->with([
                'success' => 'Withdrawal request REF #WD-' . str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) . ' has been marked as released / completed.',
                'success_title' => 'Funds Released'
            ]);
        }

        return redirect()->route('admin.withdrawals')->with('error', 'Invalid action or current request status.');
    }

    /**
     * Display the Admin Deductions Queue.
     */
    public function deductions()
    {
        $deductions = \App\Models\DeductionRequest::with('user')
            ->latest()
            ->paginate(15);

        // Fetch KPI counts for summary cards
        $pendingCount = \App\Models\DeductionRequest::where('status', 'pending')->count();
        $approvedCount = \App\Models\DeductionRequest::where('status', 'approved')->count();
        $rejectedCount = \App\Models\DeductionRequest::where('status', 'rejected')->count();

        return view('admin.deductions', compact('deductions', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Update status of a deduction adjustment request.
     */
    public function updateDeductionRequestStatus(Request $request, \App\Models\DeductionRequest $deductionRequest)
    {
        $action = $request->input('action'); // approve or reject

        if ($action === 'approve' && $deductionRequest->status === 'pending') {
            $oldValues = $deductionRequest->toArray();

            $deductionRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            AuditLogger::log('deduction_status_updated', "Admin " . auth()->user()->name . " approved payroll deduction request REF #ADJ-" . str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) . " for user {$deductionRequest->user->name}.", 'info', $deductionRequest, $oldValues, $deductionRequest->fresh()->toArray());

            return redirect()->route('admin.deductions')->with([
                'success' => 'Payroll deduction request REF #ADJ-' . str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) . ' has been approved.',
                'success_title' => 'Adjustment Approved'
            ]);
        }

        if ($action === 'reject' && $deductionRequest->status === 'pending') {
            $oldValues = $deductionRequest->toArray();

            $deductionRequest->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
            ]);

            AuditLogger::log('deduction_status_updated', "Admin " . auth()->user()->name . " rejected payroll deduction request REF #ADJ-" . str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) . " for user {$deductionRequest->user->name}.", 'warning', $deductionRequest, $oldValues, $deductionRequest->fresh()->toArray());

            return redirect()->route('admin.deductions')->with([
                'success' => 'Payroll deduction request REF #ADJ-' . str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) . ' has been rejected.',
                'success_title' => 'Adjustment Rejected'
            ]);
        }

        return redirect()->route('admin.deductions')->with('error', 'Invalid action or current request status.');
    }

    /**
     * Export deduction adjustment request details as a neat PDF using dompdf.
     */
    public function exportDeductionPdf(\App\Models\DeductionRequest $deductionRequest)
    {
        $deductionRequest->load(['user', 'approver']);

        AuditLogger::log('compliance_report_exported', "Admin " . auth()->user()->name . " exported deduction request PDF for REF #ADJ-" . str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) . " (User: {$deductionRequest->user->name}).", 'warning', $deductionRequest);

        // Generate PDF
        $pdf = Pdf::loadView('pdf-templates.deductions', compact('deductionRequest'));

        // Return the inline PDF stream
        return $pdf->stream("deduction-request-ADJ-" . str_pad($deductionRequest->id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }

    /**
     * Export member profile details as a neat PDF using dompdf.
     */
    public function exportMemberPdf(User $user)
    {
        $user->load('roles');

        AuditLogger::log('compliance_report_exported', "Admin " . auth()->user()->name . " exported member profile PDF for {$user->name}.", 'warning', $user);

        // Generate PDF
        $pdf = Pdf::loadView('pdf-templates.members', compact('user'));

        // Return the inline PDF stream
        return $pdf->stream("member-{$user->company_id}.pdf");
    }

    /**
     * Export loan application details as a neat PDF using dompdf.
     */
    public function exportLoanPdf(LoanApplication $application)
    {
        $application->load(['borrower', 'approvals.actor', 'documents']);

        AuditLogger::log('compliance_report_exported', "Admin " . auth()->user()->name . " exported loan application PDF for REF #LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . " (User: {$application->borrower->name}).", 'warning', $application);

        // Fetch co-makers based on the stored user IDs
        $comakerIds = $application->form_data['comakers'] ?? [];
        $comakers = \App\Models\User::whereIn('id', $comakerIds)->get();

        // Generate PDF
        $pdf = Pdf::loadView('pdf-templates.loans', compact('application', 'comakers'));

        // Return the inline PDF stream
        return $pdf->stream("loan-application-LN-" . str_pad($application->id, 5, '0', STR_PAD_LEFT) . ".pdf");
    }

    /**
     * Export selected withdrawal requests as a neat manifest PDF using dompdf.
     */
    public function exportWithdrawalsPdf(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('admin.withdrawals')->with('error', 'Please select at least one withdrawal request to generate a PDF.');
        }

        $withdrawals = \App\Models\WithdrawalRequest::with('user')
            ->whereIn('id', $ids)
            ->latest()
            ->get();

        if ($withdrawals->isEmpty()) {
            return redirect()->route('admin.withdrawals')->with('error', 'No matching withdrawal requests found.');
        }

        AuditLogger::log('compliance_report_exported', "Admin " . auth()->user()->name . " exported withdrawals batch manifest PDF (Count: " . $withdrawals->count() . ").", 'warning');

        // Generate PDF
        $pdf = Pdf::loadView('pdf-templates.withdrawals', compact('withdrawals'));

        // Return the inline PDF stream for previewing before download
        return $pdf->stream("withdrawals-manifest-" . now()->format('YmdHis') . ".pdf");
    }
}
