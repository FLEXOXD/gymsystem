<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuperAdminPromotionTemplate extends Model
{
    use HasFactory;

    protected $table = 'superadmin_promotion_templates';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'plan_template_id',
        'name',
        'description',
        'type',
        'value',
        'starts_at',
        'ends_at',
        'status',
        'max_uses',
        'duration_months',
        'duration_unit',
        'duration_days',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'max_uses' => 'integer',
            'duration_months' => 'integer',
            'duration_unit' => 'string',
            'duration_days' => 'integer',
        ];
    }

    public function planTemplate(): BelongsTo
    {
        return $this->belongsTo(SuperAdminPlanTemplate::class, 'plan_template_id');
    }

    public function resolvedDurationUnit(): string
    {
        $unit = strtolower(trim((string) ($this->duration_unit ?? '')));
        if (in_array($unit, ['days', 'months'], true)) {
            return $unit;
        }

        return $this->duration_days !== null && (int) $this->duration_days > 0
            ? 'days'
            : 'months';
    }

    public function resolvedDurationValue(): int
    {
        if ($this->resolvedDurationUnit() === 'days') {
            return max(1, (int) ($this->duration_days ?? 1));
        }

        return max(1, (int) ($this->duration_months ?? 1));
    }

    public function durationLabel(): string
    {
        $value = $this->resolvedDurationValue();

        return $this->resolvedDurationUnit() === 'days'
            ? $value.' '.($value === 1 ? 'dia' : 'dias')
            : $value.' '.($value === 1 ? 'mes' : 'meses');
    }
}
