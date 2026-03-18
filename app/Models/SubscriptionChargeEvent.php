<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionChargeEvent extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'subscription_id',
        'plan_template_id',
        'promotion_template_id',
        'plan_key',
        'plan_name',
        'event_type',
        'payment_method',
        'billing_cycles',
        'base_monthly_price',
        'effective_monthly_price',
        'base_total',
        'discount_amount',
        'total_paid',
        'bonus_days',
        'charged_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscription_id' => 'integer',
            'plan_template_id' => 'integer',
            'promotion_template_id' => 'integer',
            'billing_cycles' => 'integer',
            'base_monthly_price' => 'decimal:2',
            'effective_monthly_price' => 'decimal:2',
            'base_total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'bonus_days' => 'integer',
            'charged_at' => 'datetime',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function planTemplate(): BelongsTo
    {
        return $this->belongsTo(SuperAdminPlanTemplate::class, 'plan_template_id');
    }

    public function promotionTemplate(): BelongsTo
    {
        return $this->belongsTo(SuperAdminPromotionTemplate::class, 'promotion_template_id');
    }
}
