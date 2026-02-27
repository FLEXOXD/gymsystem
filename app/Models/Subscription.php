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
        'billing_owner_gym_id',
        'is_branch_managed',
        'plan_key',
        'plan_template_id',
        'feature_version',
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
            'plan_key' => 'string',
            'plan_template_id' => 'integer',
            'feature_version' => 'string',
            'billing_owner_gym_id' => 'integer',
            'is_branch_managed' => 'boolean',
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
     * Gym that owns billing for this subscription (hub gym in multi-branch).
     */
    public function billingOwnerGym(): BelongsTo
    {
        return $this->belongsTo(Gym::class, 'billing_owner_gym_id');
    }

    /**
     * Get notifications generated for this subscription.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(SubscriptionNotification::class);
    }
}
