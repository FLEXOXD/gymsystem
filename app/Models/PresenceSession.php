<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresenceSession extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'client_id',
        'check_in_attendance_id',
        'check_in_by',
        'check_in_method',
        'check_in_at',
        'check_out_by',
        'check_out_method',
        'check_out_at',
        'check_out_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function checkInAttendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'check_in_attendance_id');
    }

    public function checkInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'check_in_by');
    }

    public function checkOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'check_out_by');
    }

    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where('gym_id', $gymId);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('check_out_at');
    }

    /**
     * @param  array<int, int>  $gymIds
     */
    public function scopeForGyms(Builder $query, array $gymIds): Builder
    {
        $ids = collect($gymIds)
            ->map(static fn ($id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->values()
            ->all();

        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('gym_id', $ids);
    }
}
