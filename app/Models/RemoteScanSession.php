<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RemoteScanSession extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'created_by',
        'context',
        'channel_token',
        'status',
        'expires_at',
        'closed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(RemoteScanEvent::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->where('status', 'open')
            ->where(function (Builder $inner): void {
                $inner->whereNull('closed_at')
                    ->orWhere('closed_at', '>', now());
            });
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }

    public function isExpired(?Carbon $now = null): bool
    {
        $current = $now ?? now();

        return ! $this->expires_at || $this->expires_at->lte($current) || $this->status !== 'open';
    }
}
