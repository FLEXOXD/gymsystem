<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'cash_session_id',
        'type',
        'amount',
        'method',
        'membership_id',
        'created_by',
        'description',
        'occurred_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_at' => 'datetime',
        ];
    }

    /**
     * Get the gym that owns this movement.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get the session that owns this movement.
     */
    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    /**
     * Get the membership linked to this movement.
     */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    /**
     * Get the user that created this movement.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope records for a specific gym.
     */
    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where('gym_id', $gymId);
    }

    /**
     * Scope records between date boundaries.
     */
    public function scopeBetweenOccurredAt(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('occurred_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
    }
}
