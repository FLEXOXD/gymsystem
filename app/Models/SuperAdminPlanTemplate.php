<?php

namespace App\Models;

use App\Support\PlanDuration;
use App\Support\SuperAdminPlanCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class SuperAdminPlanTemplate extends Model
{
    use HasFactory;

    protected $table = 'superadmin_plan_templates';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'plan_key',
        'name',
        'duration_days',
        'duration_unit',
        'duration_months',
        'price',
        'discount_price',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'plan_key' => 'string',
            'duration_days' => 'integer',
            'duration_months' => 'integer',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
        ];
    }

    /**
     * Ensure the fixed SuperAdmin catalog exists.
     */
    public static function ensureDefaultCatalog(): void
    {
        if (! Schema::hasTable('superadmin_plan_templates')) {
            return;
        }
        if (! Schema::hasColumns('superadmin_plan_templates', ['plan_key', 'discount_price'])) {
            return;
        }

        $defaults = SuperAdminPlanCatalog::defaults();
        foreach ($defaults as $default) {
            $planKey = (string) $default['plan_key'];
            $plan = self::query()->firstOrNew(['plan_key' => $planKey]);

            $plan->name = (string) $default['name'];
            $plan->duration_unit = (string) $default['duration_unit'];
            $plan->duration_days = (int) $default['duration_days'];
            $plan->duration_months = (int) $default['duration_months'];
            $plan->status = $plan->exists
                ? (string) ($plan->status ?: $default['status'])
                : (string) $default['status'];
            $plan->price = $plan->exists
                ? (float) ($plan->price ?? $default['price'])
                : (float) $default['price'];
            $plan->discount_price = $plan->exists
                ? ($plan->discount_price !== null ? (float) $plan->discount_price : (float) $default['discount_price'])
                : (float) $default['discount_price'];

            $plan->save();
        }
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(SuperAdminPromotionTemplate::class, 'plan_template_id');
    }

    public function durationLabel(): string
    {
        return PlanDuration::label($this->duration_unit, (int) $this->duration_days, $this->duration_months);
    }
}
