<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockMovement extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'product_id',
        'product_sale_id',
        'user_id',
        'type',
        'quantity_change',
        'stock_before',
        'stock_after',
        'unit_cost',
        'note',
        'occurred_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'occurred_at' => 'datetime',
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

    public function sale(): BelongsTo
    {
        return $this->belongsTo(ProductSale::class, 'product_sale_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function scopeBetweenOccurredAt(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween($query->qualifyColumn('occurred_at'), [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
    }
}
