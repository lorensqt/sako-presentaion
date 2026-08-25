<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_id',
        'loan_category',
        'loan_type',
        'requested_amount',
        'approved_amount',
        'interest_rate',
        'term_months',
        'total_interest',
        'total_payable',
        'monthly_amortization',
        'service_charge',
        'net_proceeds',
        'current_stage',
        'status',
        'release_date',
        'maturity_date',
        'form_data',
        'rejection_reason',
    ];

    protected $casts = [
        'form_data' => 'array',
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'monthly_amortization' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'net_proceeds' => 'decimal:2',
        'release_date' => 'date',
        'maturity_date' => 'date',
    ];

    /**
     * Get the borrowing user.
     */
    public function borrower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the referenced loan product template.
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get all step approvals for this loan application.
     */
    public function approvals()
    {
        return $this->hasMany(LoanApproval::class);
    }

    /**
     * Get all co-makers assigned to this application.
     */
    public function comakers()
    {
        return $this->hasMany(LoanComaker::class);
    }

    /**
     * Get all timeline/activity records for this loan application.
     */
    public function activities()
    {
        return $this->hasMany(LoanActivity::class)->orderBy('created_at', 'asc');
    }
}
