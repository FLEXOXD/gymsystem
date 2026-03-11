<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\ProductStockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductInventoryService
{
    public function __construct(
        private readonly CashSessionService $cashSessionService
    ) {
    }

    /**
     * Register a stock movement and keep the product stock synchronized.
     *
     * @throws RuntimeException
     */
    public function registerStockMovement(
        Product $product,
        ?User $actor,
        int $quantityChange,
        string $type,
        ?float $unitCost = null,
        ?string $note = null,
        ?int $productSaleId = null,
        Carbon|string|null $occurredAt = null
    ): ProductStockMovement {
        if ($quantityChange === 0) {
            throw new RuntimeException('El movimiento de stock debe cambiar al menos 1 unidad.');
        }

        return DB::transaction(function () use ($product, $actor, $quantityChange, $type, $unitCost, $note, $productSaleId, $occurredAt): ProductStockMovement {
            /** @var Product|null $lockedProduct */
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->find($product->id);

            if (! $lockedProduct) {
                throw new RuntimeException('El producto ya no existe.');
            }

            $stockBefore = (int) $lockedProduct->stock;
            $stockAfter = $stockBefore + $quantityChange;
            if ($stockAfter < 0) {
                throw new RuntimeException('No hay stock suficiente para completar la operacion.');
            }

            $normalizedUnitCost = $unitCost !== null ? round(max(0, $unitCost), 2) : null;
            $updates = [
                'stock' => $stockAfter,
            ];

            if ($normalizedUnitCost !== null && $normalizedUnitCost > 0) {
                $updates['cost_price'] = $normalizedUnitCost;
            }

            $lockedProduct->update($updates);

            return ProductStockMovement::query()->create([
                'gym_id' => (int) $lockedProduct->gym_id,
                'product_id' => (int) $lockedProduct->id,
                'product_sale_id' => $productSaleId,
                'user_id' => $actor?->id,
                'type' => trim($type) !== '' ? trim($type) : 'adjustment',
                'quantity_change' => $quantityChange,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'unit_cost' => $normalizedUnitCost ?? (float) $lockedProduct->cost_price,
                'note' => $this->normalizeNote($note),
                'occurred_at' => $this->normalizeOccurredAt($occurredAt),
            ]);
        });
    }

    /**
     * Sell one product and mirror the income inside cash movements.
     *
     * @throws RuntimeException
     */
    public function registerSale(
        Product $product,
        User $actor,
        int $quantity,
        string $paymentMethod,
        ?int $clientId = null,
        ?string $notes = null,
        Carbon|string|null $soldAt = null
    ): ProductSale {
        if ($quantity <= 0) {
            throw new RuntimeException('La cantidad vendida debe ser mayor a 0.');
        }

        return DB::transaction(function () use ($product, $actor, $quantity, $paymentMethod, $clientId, $notes, $soldAt): ProductSale {
            /** @var Product|null $lockedProduct */
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->find($product->id);

            if (! $lockedProduct) {
                throw new RuntimeException('El producto ya no existe.');
            }

            if ((string) $lockedProduct->status !== 'active') {
                throw new RuntimeException('El producto esta inactivo. Activalo antes de vender.');
            }

            if ((int) $lockedProduct->stock < $quantity) {
                throw new RuntimeException('Stock insuficiente para completar la venta.');
            }

            $client = null;
            if (($clientId ?? 0) > 0) {
                $client = Client::query()
                    ->forGym((int) $lockedProduct->gym_id)
                    ->select(['id', 'gym_id', 'first_name', 'last_name'])
                    ->find($clientId);

                if (! $client) {
                    throw new RuntimeException('El cliente seleccionado no pertenece al gimnasio actual.');
                }
            }

            $unitPrice = round((float) $lockedProduct->sale_price, 2);
            $unitCost = round((float) $lockedProduct->cost_price, 2);
            $totalAmount = round($unitPrice * $quantity, 2);
            $totalCost = round($unitCost * $quantity, 2);
            $totalProfit = round($totalAmount - $totalCost, 2);
            $normalizedSoldAt = $this->normalizeOccurredAt($soldAt);

            $cashMovement = $this->cashSessionService->addMovement(
                gymId: (int) $lockedProduct->gym_id,
                userId: (int) $actor->id,
                type: 'income',
                amount: $totalAmount,
                method: $paymentMethod,
                membershipId: null,
                description: $this->buildSaleDescription($lockedProduct->name, $quantity, $client?->full_name, $notes),
                occurredAt: $normalizedSoldAt
            );

            $sale = ProductSale::query()->create([
                'gym_id' => (int) $lockedProduct->gym_id,
                'product_id' => (int) $lockedProduct->id,
                'client_id' => $client?->id,
                'cash_session_id' => $cashMovement->cash_session_id,
                'cash_movement_id' => $cashMovement->id,
                'sold_by' => (int) $actor->id,
                'payment_method' => $paymentMethod,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'notes' => $this->normalizeNote($notes),
                'sold_at' => $normalizedSoldAt,
            ]);

            $this->registerStockMovement(
                product: $lockedProduct,
                actor: $actor,
                quantityChange: -$quantity,
                type: 'sale',
                unitCost: $unitCost,
                note: $this->normalizeNote($notes),
                productSaleId: (int) $sale->id,
                occurredAt: $normalizedSoldAt
            );

            return $sale->fresh(['product', 'soldBy', 'client', 'cashMovement']);
        });
    }

    private function buildSaleDescription(string $productName, int $quantity, ?string $clientName = null, ?string $notes = null): string
    {
        $parts = ['Venta producto: '.$productName.' x'.$quantity];

        $normalizedClientName = trim((string) $clientName);
        if ($normalizedClientName !== '') {
            $parts[] = 'Cliente: '.$normalizedClientName;
        }

        $normalizedNotes = $this->normalizeNote($notes);
        if ($normalizedNotes !== null) {
            $parts[] = $normalizedNotes;
        }

        return implode(' | ', $parts);
    }

    private function normalizeNote(?string $note): ?string
    {
        $value = trim((string) $note);

        return $value !== '' ? $value : null;
    }

    private function normalizeOccurredAt(Carbon|string|null $value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            return Carbon::parse($value);
        }

        return now();
    }
}
