<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'stage_role_slug',
        'actioned_by_user_id',
        'decision',
        'remarks',
    ];

    /**
     * Get the associated loan application.
     */
    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * Get the user who executed the action.
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actioned_by_user_id');
    }
}
