<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'type_key',
        'name',
        'partner',
        'loanable_amount',
        'fixed_deposit',
        'comakers',
        'interest_rate',
        'max_term_months',
        'minimum_membership_months',
        'hrmd_approval',
        'is_active',
        'approval_flow',
        'metadata',
    ];

    protected $casts = [
        'comakers' => 'json',
        'fixed_deposit' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'hrmd_approval' => 'boolean',
        'approval_flow' => 'json',
        'metadata' => 'json',
    ];

    /**
     * Get the applications associated with this loan product.
     */
    public function applications()
    {
        return $this->hasMany(LoanApplication::class);
    }
}
