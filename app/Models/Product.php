<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'gym_id',
        'created_by',
        'name',
        'sku',
        'barcode',
        'category',
        'sale_price',
        'cost_price',
        'stock',
        'min_stock',
        'status',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
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

    public function sales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(ProductStockMovement::class);
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

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $value = trim((string) $search);
        if ($value === '') {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($value): void {
            $inner->where($inner->qualifyColumn('name'), 'like', "%{$value}%")
                ->orWhere($inner->qualifyColumn('sku'), 'like', "%{$value}%")
                ->orWhere($inner->qualifyColumn('barcode'), 'like', "%{$value}%")
                ->orWhere($inner->qualifyColumn('category'), 'like', "%{$value}%");
        });
    }
}
