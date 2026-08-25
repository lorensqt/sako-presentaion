<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'savings_amount',
        'fixed_amount',
        'effectivity_date',
        'remarks',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'savings_amount' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'effectivity_date' => 'date',
    ];

    /**
     * Get the requesting user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who evaluated and signed off the request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
