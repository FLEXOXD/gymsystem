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
        ];
    }

    public function planTemplate(): BelongsTo
    {
        return $this->belongsTo(SuperAdminPlanTemplate::class, 'plan_template_id');
    }
}
