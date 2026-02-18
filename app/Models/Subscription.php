<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'plan_name',
        'price',
        'starts_at',
        'ends_at',
        'status',
        'last_payment_method',
        'grace_days',
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
            'starts_at' => 'date',
            'ends_at' => 'date',
            'grace_days' => 'integer',
        ];
    }

    /**
     * Get the gym that owns this subscription.
     */
    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    /**
     * Get notifications generated for this subscription.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(SubscriptionNotification::class);
    }
}
