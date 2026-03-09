<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'client_id',
        'plan_id',
        'price',
        'promotion_id',
        'promotion_name',
        'promotion_type',
        'promotion_value',
        'discount_amount',
        'bonus_days',
        'starts_at',
        'ends_at',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'promotion_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'bonus_days' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    /**
     * Get the gym that owns the membership.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get the client that owns the membership.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the plan attached to this membership.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get promotion used for this membership.
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Get cash movements linked to this membership.
     */
    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    /**
     * Get the adjustment log for this membership.
     */
    public function adjustments(): HasMany
    {
        return $this->hasMany(MembershipAdjustment::class);
    }

    /**
     * Scope records for a specific gym.
     */
    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where('gym_id', $gymId);
    }

    /**
     * Scope records for multiple gyms.
     *
     * @param  array<int, int>  $gymIds
     */
    public function scopeForGyms(Builder $query, array $gymIds): Builder
    {
        $ids = collect($gymIds)
            ->map(static fn ($id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->values()
            ->all();

        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('gym_id', $ids);
    }

    /**
     * Scope by membership status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope memberships active on a specific date.
     */
    public function scopeActiveOn(Builder $query, Carbon|string $date): Builder
    {
        $value = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        return $query
            ->status('active')
            ->whereDate('starts_at', '<=', $value)
            ->whereDate('ends_at', '>=', $value);
    }
}
