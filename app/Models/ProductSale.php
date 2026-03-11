<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSale extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'product_id',
        'client_id',
        'cash_session_id',
        'cash_movement_id',
        'sold_by',
        'payment_method',
        'quantity',
        'unit_price',
        'unit_cost',
        'total_amount',
        'total_cost',
        'total_profit',
        'notes',
        'sold_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'total_profit' => 'decimal:2',
            'sold_at' => 'datetime',
        ];
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function cashMovement(): BelongsTo
    {
        return $this->belongsTo(CashMovement::class);
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function scopeForGym(Builder $query, int $gymId): Builder
    {
        return $query->where($query->qualifyColumn('gym_id'), $gymId);
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

        return $query->whereIn($query->qualifyColumn('gym_id'), $ids);
    }

    public function scopeBetweenSoldAt(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween($query->qualifyColumn('sold_at'), [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
    }
}
