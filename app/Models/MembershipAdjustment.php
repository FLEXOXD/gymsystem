<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipAdjustment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'client_id',
        'membership_id',
        'performed_by',
        'type',
        'reason',
        'notes',
        'previous_starts_at',
        'previous_ends_at',
        'previous_status',
        'new_starts_at',
        'new_ends_at',
        'new_status',
        'days_delta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'previous_starts_at' => 'date',
            'previous_ends_at' => 'date',
            'new_starts_at' => 'date',
            'new_ends_at' => 'date',
            'days_delta' => 'integer',
        ];
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where('gym_id', $gymId);
    }
}
