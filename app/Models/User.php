<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pin',
        'pin_attempts',
        'role',
        'hrmd_sequence',
        'admin_permissions',
        'company_id',
        'address',
        'contact_number',
        'signature',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'pin',
        'pin_attempts',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'admin_permissions' => 'array',
            'hrmd_sequence' => 'integer',
        ];
    }

    /**
     * Definitions of all configurable admin page permissions.
     */
    public static function allAdminPagePermissions(): array
    {
        return [
            'dashboard' => [
                'label' => 'Overview Panel',
                'description' => 'Access system summary metrics, KPIs, and dashboard charts.',
                'icon' => 'fa-solid fa-chart-pie',
                'group' => 'Core Panel',
                'route' => 'admin.dashboard',
            ],
            'members' => [
                'label' => 'Members Directory',
                'description' => 'View, search, register, and manage cooperative members.',
                'icon' => 'fa-solid fa-users',
                'group' => 'Registry',
                'route' => 'admin.members',
            ],
            'loans' => [
                'label' => 'Loans Directory',
                'description' => 'Browse loan accounts, statuses, and export PDF statements.',
                'icon' => 'fa-solid fa-list-check',
                'group' => 'Credit & Loans',
                'route' => 'admin.loans',
            ],
            'loan_approvals' => [
                'label' => 'Loan Approvals',
                'description' => 'Review applications and execute staged workflow approvals.',
                'icon' => 'fa-solid fa-signature',
                'group' => 'Credit & Loans',
                'route' => 'admin.loans.approvals',
            ],
            'loans_management' => [
                'label' => 'Loans Management',
                'description' => 'Configure loan products, interest rates, and available terms.',
                'icon' => 'fa-solid fa-sliders',
                'group' => 'Credit & Loans',
                'route' => 'admin.loans.management',
            ],
            'withdrawals' => [
                'label' => 'Withdrawals',
                'description' => 'Review and process capital build-up / savings withdrawal requests.',
                'icon' => 'fa-solid fa-arrow-up-from-bracket',
                'group' => 'Treasury',
                'route' => 'admin.withdrawals',
            ],
            'deductions' => [
                'label' => 'Deduction Approvals',
                'description' => 'Approve or reject monthly payroll deduction adjustment requests.',
                'icon' => 'fa-solid fa-receipt',
                'group' => 'Treasury',
                'route' => 'admin.deductions',
            ],
            'elections' => [
                'label' => 'Governance & Elections',
                'description' => 'Create elections, manage positions, candidates, and live results.',
                'icon' => 'fa-solid fa-check-to-slot',
                'group' => 'Governance',
                'route' => 'admin.elections.index',
            ],
            'audit_logs' => [
                'label' => 'Audit & Security Logs',
                'description' => 'Monitor immutable security event logs and system audit trails.',
                'icon' => 'fa-solid fa-shield-halved',
                'group' => 'System Security',
                'route' => 'admin.audit-logs',
            ],
        ];
    }

    /**
     * Determine whether the user can access a specific admin page/module.
     */
    public function canAccessAdminPage(string $page): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        if ($this->role !== 'admin') {
            return false;
        }

        // If admin_permissions is null (legacy admin or unconfigured), allow access
        // Once configured, it is an array of explicitly granted permission keys
        if ($this->admin_permissions === null) {
            return true;
        }

        return is_array($this->admin_permissions) && in_array($page, $this->admin_permissions, true);
    }

    /**
     * Get the first route the admin is permitted to access.
     */
    public function firstAccessibleAdminRoute(): string
    {
        if ($this->role === 'super_admin') {
            return 'admin.dashboard';
        }

        $all = self::allAdminPagePermissions();
        foreach ($all as $key => $info) {
            if ($this->canAccessAdminPage($key)) {
                return $info['route'];
            }
        }

        return 'admin.dashboard';
    }

    /**
     * Roles belonging to this user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user belongs to a role.
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles->contains('slug', $roleSlug);
    }

    /**
     * Check if the user has a permission through any of their roles.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permissionSlug) {
            $query->where('slug', $permissionSlug);
        })->exists();
    }

    /**
     * Get all withdrawal requests for this user.
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /**
     * Get all loan applications submitted by this user.
     */
    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    /**
     * Get all co-maker duties/requests assigned to this user.
     */
    public function comakerDuties()
    {
        return $this->hasMany(LoanComaker::class);
    }

    /**
     * Get all payroll deduction adjustment requests for this user.
     */
    public function deductionRequests()
    {
        return $this->hasMany(DeductionRequest::class);
    }

    /**
     * Get the storage disk used for application files and signatures.
     * Enforces strict cloud bucket storage in production and standard runtime.
     */
    public static function storageDisk(): string
    {
        if (app()->runningUnitTests()) {
            return config('filesystems.default') === 'public' ? 'public' : 's3';
        }

        return 's3';
    }

    /**
     * Get the storage disk used for signatures.
     */
    public static function signatureDisk(): string
    {
        return self::storageDisk();
    }

    /**
     * Get the publicly accessible URL for the signature.
     * Uses temporary presigned URLs for private cloud buckets.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if (empty($this->signature)) {
            return null;
        }

        if (str_starts_with($this->signature, 'http://') || str_starts_with($this->signature, 'https://')) {
            return $this->signature;
        }

        $disk = self::signatureDisk();

        if ($disk === 's3') {
            try {
                // Generate a temporary presigned URL for private cloud bucket access (30 min expiry)
                return Storage::disk('s3')->temporaryUrl($this->signature, now()->addMinutes(30));
            } catch (\Throwable $e) {
                // Fallback to authenticated streaming route if temporaryUrl cannot be generated
                return route('signature.show', $this->id);
            }
        }

        return route('signature.show', $this->id);
    }

    /**
     * Determine if a signature exists strictly on the cloud storage disk.
     */
    public function hasSignature(): bool
    {
        if (empty($this->signature)) {
            return false;
        }

        $disk = self::signatureDisk();

        try {
            return Storage::disk($disk)->exists($this->signature);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get base64 data URI of the signature from the cloud storage disk (ideal for DomPDF rendering).
     */
    public function getSignatureBase64Attribute(): ?string
    {
        if (empty($this->signature)) {
            return null;
        }

        $disk = self::signatureDisk();

        try {
            if (Storage::disk($disk)->exists($this->signature)) {
                $content = Storage::disk($disk)->get($this->signature);
                $mime = Storage::disk($disk)->mimeType($this->signature) ?? 'image/png';
                return 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        } catch (\Throwable $e) {}

        return null;
    }
}
