<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymBranchLink extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'hub_gym_id',
        'branch_gym_id',
        'branch_plan_key',
        'cash_managed_by_hub',
        'status',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cash_managed_by_hub' => 'boolean',
        ];
    }

    public function hubGym(): BelongsTo
    {
        return $this->belongsTo(Gym::class, 'hub_gym_id');
    }

    public function branchGym(): BelongsTo
    {
        return $this->belongsTo(Gym::class, 'branch_gym_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
