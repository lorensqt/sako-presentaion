<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'severity',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user who triggered/acted on this event.
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the associated model (polymorphic relationship).
     */
    public function auditable()
    {
        return $this->morphTo();
    }
}
