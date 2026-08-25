<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'user_id',
        'action',
        'description',
    ];

    /**
     * Get the associated loan application.
     */
    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * Get the user who triggered/acted on this event.
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
