<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Determine the actual current status dynamically based on current time.
     */
    public function getComputedStatusAttribute(): string
    {
        $now = Carbon::now();
        
        if ($now->lt($this->start_time)) {
            return 'upcoming';
        } elseif ($now->gt($this->end_time)) {
            return 'completed';
        }
        
        return 'active';
    }

    public function isUpcoming(): bool
    {
        return $this->computed_status === 'upcoming';
    }

    public function isActive(): bool
    {
        return $this->computed_status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->computed_status === 'completed';
    }

    /**
     * Check if a specific user has already voted in this election.
     */
    public function hasUserVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }
}
