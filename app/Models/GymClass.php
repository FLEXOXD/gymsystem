<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymClass extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'created_by',
        'updated_by',
        'name',
        'category',
        'level',
        'instructor_name',
        'room_name',
        'description',
        'price',
        'starts_at',
        'ends_at',
        'capacity',
        'allow_waitlist',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'price' => 'decimal:2',
            'capacity' => 'integer',
            'allow_waitlist' => 'boolean',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(GymClassReservation::class);
    }

    public function activeReservations(): HasMany
    {
        return $this->hasMany(GymClassReservation::class)
            ->whereIn('status', [
                GymClassReservation::STATUS_RESERVED,
                GymClassReservation::STATUS_ATTENDED,
            ]);
    }

    public function waitlistReservations(): HasMany
    {
        return $this->hasMany(GymClassReservation::class)
            ->where('status', GymClassReservation::STATUS_WAITLIST);
    }

    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where('gym_id', $gymId);
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

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $value = trim((string) $search);
        if ($value === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($value): void {
            $builder->where('name', 'like', '%'.$value.'%')
                ->orWhere('category', 'like', '%'.$value.'%')
                ->orWhere('instructor_name', 'like', '%'.$value.'%')
                ->orWhere('room_name', 'like', '%'.$value.'%');
        });
    }

    public function scheduleStartDate(): ?Carbon
    {
        return $this->starts_at?->copy()->startOfDay();
    }

    public function scheduleEndDate(): ?Carbon
    {
        return ($this->ends_at ?? $this->starts_at)?->copy()->startOfDay();
    }

    public function occursOnDate(DateTimeInterface|string $date): bool
    {
        $startDate = $this->scheduleStartDate();
        $endDate = $this->scheduleEndDate();

        if (! $startDate || ! $endDate) {
            return false;
        }

        $targetDate = $this->normalizeScheduleDate($date);

        return $targetDate->gte($startDate) && $targetDate->lte($endDate);
    }

    /**
     * @return array{start: Carbon, end: Carbon}|null
     */
    public function occurrenceWindowForDate(DateTimeInterface|string $date): ?array
    {
        if (! $this->starts_at || ! $this->ends_at || ! $this->occursOnDate($date)) {
            return null;
        }

        $targetDate = $this->normalizeScheduleDate($date);
        $startAt = $targetDate->copy()->setTime(
            $this->starts_at->hour,
            $this->starts_at->minute,
            $this->starts_at->second
        );
        $endAt = $targetDate->copy()->setTime(
            $this->ends_at->hour,
            $this->ends_at->minute,
            $this->ends_at->second
        );

        if ($endAt->lte($startAt)) {
            $endAt->addDay();
        }

        return ['start' => $startAt, 'end' => $endAt];
    }

    /**
     * @return array{start: Carbon, end: Carbon}|null
     */
    public function nextOccurrenceWindow(?DateTimeInterface $reference = null): ?array
    {
        if (! $this->starts_at || ! $this->ends_at) {
            return null;
        }

        $referenceAt = $reference instanceof DateTimeInterface
            ? Carbon::instance($reference)->setTimezone($this->starts_at->getTimezone())
            : now($this->starts_at->getTimezone());

        $startDate = $this->scheduleStartDate();
        $endDate = $this->scheduleEndDate();

        if (! $startDate || ! $endDate) {
            return null;
        }

        $cursor = $referenceAt->copy()->startOfDay();
        if ($cursor->lt($startDate)) {
            $cursor = $startDate->copy();
        }

        while ($cursor->lte($endDate)) {
            $window = $this->occurrenceWindowForDate($cursor);
            if ($window && $window['end']->gt($referenceAt)) {
                return $window;
            }

            $cursor->addDay();
        }

        return null;
    }

    public function overlapsSchedule(self $other): bool
    {
        $rangeStart = $this->scheduleStartDate();
        $rangeEnd = $this->scheduleEndDate();
        $otherRangeStart = $other->scheduleStartDate();
        $otherRangeEnd = $other->scheduleEndDate();

        if (! $rangeStart || ! $rangeEnd || ! $otherRangeStart || ! $otherRangeEnd) {
            return false;
        }

        $cursor = $rangeStart->gte($otherRangeStart) ? $rangeStart->copy() : $otherRangeStart->copy();
        $limit = $rangeEnd->lte($otherRangeEnd) ? $rangeEnd->copy() : $otherRangeEnd->copy();

        while ($cursor->lte($limit)) {
            $currentWindow = $this->occurrenceWindowForDate($cursor);
            $otherWindow = $other->occurrenceWindowForDate($cursor);

            if (
                $currentWindow
                && $otherWindow
                && $currentWindow['start']->lt($otherWindow['end'])
                && $currentWindow['end']->gt($otherWindow['start'])
            ) {
                return true;
            }

            $cursor->addDay();
        }

        return false;
    }

    public function dailyStartSortKey(): int
    {
        if (! $this->starts_at) {
            return PHP_INT_MAX;
        }

        return ($this->starts_at->hour * 3600) + ($this->starts_at->minute * 60) + $this->starts_at->second;
    }

    public function isFree(): bool
    {
        return (float) ($this->price ?? 0) <= 0;
    }

    public function formattedPrice(string $currencySymbol = '$'): string
    {
        if ($this->isFree()) {
            return 'Gratis';
        }

        return $currencySymbol.number_format((float) $this->price, 2, '.', ',');
    }

    private function normalizeScheduleDate(DateTimeInterface|string $date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date->copy()->startOfDay();
        }

        if ($date instanceof DateTimeInterface) {
            return Carbon::instance($date)->startOfDay();
        }

        return Carbon::parse($date)->startOfDay();
    }
}
