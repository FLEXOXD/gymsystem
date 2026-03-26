<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymClassReservation extends Model
{
    use HasFactory;

    public const STATUS_RESERVED = 'reserved';
    public const STATUS_WAITLIST = 'waitlist';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_ATTENDED = 'attended';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'gym_class_id',
        'client_id',
        'status',
        'reserved_at',
        'waitlisted_at',
        'promoted_at',
        'cancelled_at',
        'attended_at',
        'day_reminder_sent_at',
        'hour_reminder_sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
            'waitlisted_at' => 'datetime',
            'promoted_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'attended_at' => 'datetime',
            'day_reminder_sent_at' => 'datetime',
            'hour_reminder_sent_at' => 'datetime',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function gymClass(): BelongsTo
    {
        return $this->belongsTo(GymClass::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where('gym_id', $gymId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_RESERVED,
            self::STATUS_WAITLIST,
            self::STATUS_ATTENDED,
        ]);
    }
}
