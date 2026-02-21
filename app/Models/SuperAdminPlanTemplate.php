<?php

namespace App\Models;

use App\Support\PlanDuration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuperAdminPlanTemplate extends Model
{
    use HasFactory;

    protected $table = 'superadmin_plan_templates';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'duration_days',
        'duration_unit',
        'duration_months',
        'price',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'duration_months' => 'integer',
            'price' => 'decimal:2',
        ];
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
