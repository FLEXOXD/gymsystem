<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceDailySummary;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Membership;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\ProductStockMovement;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * @param  int|array<int, int>  $gymIdOrIds
     * @return array<int, int>
     */
    private function normalizeGymIds(int|array $gymIdOrIds): array
    {
        return collect(is_array($gymIdOrIds) ? $gymIdOrIds : [$gymIdOrIds])
            ->map(static fn ($id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->values()
            ->all();
    }

    /**
     * Get income and expense summary for a date range.
     *
     * @return array<string, float|int>
     */
    public function getIncomeSummary(int|array $gymIdOrIds, Carbon $from, Carbon $to): array
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);
        $totals = CashMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($from, $to)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense")
            ->selectRaw('COUNT(*) as total_movements')
            ->first();

        $income = (float) ($totals->total_income ?? 0);
        $expense = (float) ($totals->total_expense ?? 0);

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'balance' => round($income - $expense, 2),
            'total_movements' => (int) ($totals->total_movements ?? 0),
        ];
    }

    /**
     * Get totals grouped by payment method.
     */
    public function getIncomeByMethod(int|array $gymIdOrIds, Carbon $from, Carbon $to): Collection
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);
        return CashMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($from, $to)
            ->selectRaw('method')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END), 0) as balance")
            ->selectRaw('COUNT(*) as movements_count')
            ->groupBy('method')
            ->orderBy('method')
            ->get();
    }

    /**
     * Get attendance summary and daily attendance counts.
     *
     * @return array<string, int|Collection>
     */
    public function getAttendanceSummary(int|array $gymIdOrIds, Carbon $from, Carbon $to): array
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);
        $rawByDay = Attendance::query()
            ->forGyms($gymIds)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('date')
            ->selectRaw('COUNT(*) as attendances_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $archivedByDay = AttendanceDailySummary::query()
            ->forGyms($gymIds)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->select(['date', 'attendances_count'])
            ->orderBy('date')
            ->get();

        $countsByDate = [];

        foreach ($archivedByDay as $row) {
            $dateKey = $row->date?->toDateString() ?? (string) $row->date;
            $countsByDate[$dateKey] = (int) ($countsByDate[$dateKey] ?? 0) + (int) $row->attendances_count;
        }

        foreach ($rawByDay as $row) {
            $dateKey = $row->date instanceof Carbon ? $row->date->toDateString() : (string) $row->date;
            $countsByDate[$dateKey] = (int) ($countsByDate[$dateKey] ?? 0) + (int) $row->attendances_count;
        }

        ksort($countsByDate);

        $byDay = collect($countsByDate)->map(function (int $count, string $date) {
            return (object) [
                'date' => $date,
                'attendances_count' => $count,
            ];
        })->values();

        return [
            'total_attendances' => (int) $byDay->sum('attendances_count'),
            'by_day' => $byDay,
        ];
    }

    /**
     * Get membership status summary for today.
     *
     * @return array<string, int>
     */
    public function getMembershipStatusSummary(int|array $gymIdOrIds): array
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);
        $today = Carbon::today()->toDateString();

        $activeClientIds = Membership::query()
            ->forGyms($gymIds)
            ->status('active')
            ->whereDate('ends_at', '>=', $today)
            ->distinct()
            ->pluck('client_id');

        $expiredClientIds = Membership::query()
            ->forGyms($gymIds)
            ->where(function ($query) use ($today): void {
                $query
                    ->where('status', 'expired')
                    ->orWhereDate('ends_at', '<', $today);
            })
            ->whereNotIn('client_id', $activeClientIds)
            ->distinct()
            ->pluck('client_id');

        return [
            'active' => $activeClientIds->count(),
            'expired' => $expiredClientIds->count(),
            'total_clients' => Client::query()->forGyms($gymIds)->count(),
        ];
    }

    /**
     * Get client lists for memberships report (active/expired).
     *
     * @return array<string, Collection>
     */
    public function getMembershipClientLists(int|array $gymIdOrIds): array
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);
        $today = Carbon::today()->toDateString();

        $clients = Client::query()
            ->forGyms($gymIds)
            ->select(['id', 'gym_id', 'first_name', 'last_name', 'document_number'])
            ->with([
                'memberships' => fn ($query) => $query
                    ->forGyms($gymIds)
                    ->select(['id', 'gym_id', 'client_id', 'plan_id', 'starts_at', 'ends_at', 'status'])
                    ->orderByDesc('ends_at')
                    ->orderByDesc('id'),
            ])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $active = collect();
        $expired = collect();

        foreach ($clients as $client) {
            $latestMembership = $client->memberships->first();
            if (! $latestMembership) {
                continue;
            }

            $endsAt = $latestMembership->ends_at?->toDateString();
            $isActive = $latestMembership->status === 'active'
                && $endsAt !== null
                && $endsAt >= $today;

            $row = (object) [
                'client_id' => $client->id,
                'full_name' => $client->full_name,
                'document_number' => $client->document_number,
                'status' => $latestMembership->status,
                'starts_at' => $latestMembership->starts_at?->toDateString(),
                'ends_at' => $latestMembership->ends_at?->toDateString(),
                'plan_id' => $latestMembership->plan_id,
            ];

            if ($isActive) {
                $active->push($row);
            } else {
                $expired->push($row);
            }
        }

        return [
            'active_clients' => $active->values(),
            'expired_clients' => $expired->values(),
        ];
    }

    /**
     * Product sales summary for selected period.
     *
     * @return array<string, float|int>
     */
    public function getProductSalesSummary(int|array $gymIdOrIds, Carbon $from, Carbon $to): array
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);
        $totals = ProductSale::query()
            ->forGyms($gymIds)
            ->betweenSoldAt($from, $to)
            ->selectRaw('COUNT(*) as total_sales')
            ->selectRaw('COALESCE(SUM(quantity), 0) as units_sold')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
            ->selectRaw('COALESCE(SUM(total_cost), 0) as total_cost')
            ->selectRaw('COALESCE(SUM(total_profit), 0) as total_profit')
            ->first();

        $salesCount = (int) ($totals->total_sales ?? 0);
        $revenue = (float) ($totals->total_revenue ?? 0);

        return [
            'total_sales' => $salesCount,
            'units_sold' => (int) ($totals->units_sold ?? 0),
            'total_revenue' => $revenue,
            'total_cost' => (float) ($totals->total_cost ?? 0),
            'total_profit' => (float) ($totals->total_profit ?? 0),
            'average_ticket' => $salesCount > 0 ? round($revenue / $salesCount, 2) : 0,
        ];
    }

    public function getProductSalesByDay(int|array $gymIdOrIds, Carbon $from, Carbon $to): Collection
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);

        return ProductSale::query()
            ->forGyms($gymIds)
            ->betweenSoldAt($from, $to)
            ->selectRaw('DATE(sold_at) as date')
            ->selectRaw('COUNT(*) as sales_count')
            ->selectRaw('COALESCE(SUM(quantity), 0) as units_sold')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_revenue')
            ->selectRaw('COALESCE(SUM(total_profit), 0) as total_profit')
            ->groupByRaw('DATE(sold_at)')
            ->orderByRaw('DATE(sold_at)')
            ->get();
    }

    public function getTopSellingProducts(int|array $gymIdOrIds, Carbon $from, Carbon $to, int $limit = 8): Collection
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);

        return ProductSale::query()
            ->forGyms($gymIds)
            ->betweenSoldAt($from, $to)
            ->join('products', 'products.id', '=', 'product_sales.product_id')
            ->leftJoin('gyms', 'gyms.id', '=', 'product_sales.gym_id')
            ->select([
                'product_sales.product_id',
                'products.name as product_name',
                'products.category as product_category',
                'gyms.name as gym_name',
            ])
            ->selectRaw('COALESCE(SUM(product_sales.quantity), 0) as units_sold')
            ->selectRaw('COALESCE(SUM(product_sales.total_amount), 0) as total_revenue')
            ->selectRaw('COALESCE(SUM(product_sales.total_profit), 0) as total_profit')
            ->groupBy('product_sales.product_id', 'products.name', 'products.category', 'gyms.name')
            ->orderByDesc('units_sold')
            ->orderByDesc('total_revenue')
            ->limit(max(1, $limit))
            ->get();
    }

    public function getLowStockProducts(int|array $gymIdOrIds, int $limit = 10): Collection
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);

        return Product::query()
            ->forGyms($gymIds)
            ->leftJoin('gyms', 'gyms.id', '=', 'products.gym_id')
            ->select([
                'products.id',
                'products.gym_id',
                'products.name',
                'products.category',
                'products.stock',
                'products.min_stock',
                'products.status',
                'gyms.name as gym_name',
            ])
            ->where('products.status', 'active')
            ->whereColumn('products.stock', '<=', 'products.min_stock')
            ->orderBy('products.stock')
            ->orderBy('products.name')
            ->limit(max(1, $limit))
            ->get();
    }

    /**
     * @return array<string, float|int>
     */
    public function getInventoryMovementSummary(int|array $gymIdOrIds, Carbon $from, Carbon $to): array
    {
        $gymIds = $this->normalizeGymIds($gymIdOrIds);
        $movements = ProductStockMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($from, $to)
            ->get(['type', 'quantity_change']);

        $unitsIn = 0;
        $unitsOut = 0;
        $manualAdjustments = 0;

        foreach ($movements as $movement) {
            $change = (int) ($movement->quantity_change ?? 0);
            if ($change > 0) {
                $unitsIn += $change;
            } elseif ($change < 0) {
                $unitsOut += abs($change);
            }

            if (str_starts_with((string) ($movement->type ?? ''), 'adjustment')) {
                $manualAdjustments++;
            }
        }

        return [
            'movement_count' => (int) $movements->count(),
            'units_in' => $unitsIn,
            'units_out' => $unitsOut,
            'manual_adjustments' => $manualAdjustments,
        ];
    }
}
