<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductSale;
use App\Services\ProductInventoryService;
use App\Services\ReportService;
use App\Support\ActiveGymContext;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use RuntimeException;

class SalesInventoryController extends Controller
{
    public function __construct(
        private readonly ProductInventoryService $productInventoryService,
        private readonly ReportService $reportService
    ) {
    }

    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $showGymColumn = $isGlobalScope && count($gymIds) > 1;
        $schemaReady = $this->schemaReady();
        $selectedProductId = (int) $request->query('product_id', 0);
        $salesHistoryFilters = [
            'year' => max(0, (int) $request->query('sales_year', 0)),
            'month' => max(0, min(12, (int) $request->query('sales_month', 0))),
            'day' => max(0, min(31, (int) $request->query('sales_day', 0))),
        ];
        $openSalesHistoryModal = in_array(
            strtolower(trim((string) $request->query('sales_history_open', '0'))),
            ['1', 'true', 'yes'],
            true
        );

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $todaySummary = [
            'total_sales' => 0,
            'units_sold' => 0,
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'average_ticket' => 0,
        ];
        $weekSummary = $todaySummary;
        $monthSummary = $todaySummary;
        $inventorySummary = [
            'movement_count' => 0,
            'units_in' => 0,
            'units_out' => 0,
            'manual_adjustments' => 0,
        ];
        $topProducts = collect();
        $lowStockProducts = collect();
        $recentSales = collect();
        $saleProducts = collect();
        $saleClients = collect();
        $salesHistoryRows = collect();
        $salesHistoryYears = collect();
        $salesHistoryTotal = 0;
        $salesHistoryTruncated = false;

        if ($schemaReady) {
            $todaySummary = $this->reportService->getProductSalesSummary($gymIds, $todayStart, $todayEnd);
            $weekSummary = $this->reportService->getProductSalesSummary($gymIds, $weekStart, $weekEnd);
            $monthSummary = $this->reportService->getProductSalesSummary($gymIds, $monthStart, $monthEnd);
            $inventorySummary = $this->reportService->getInventoryMovementSummary($gymIds, $monthStart, $monthEnd);
            $topProducts = $this->reportService->getTopSellingProducts($gymIds, $monthStart, $monthEnd, 6);
            $lowStockProducts = $this->reportService->getLowStockProducts($gymIds, 6);
            $recentSales = ProductSale::query()
                ->forGyms($gymIds)
                ->with(['product:id,name,category', 'soldBy:id,name', 'client:id,first_name,last_name', 'gym:id,name'])
                ->orderByDesc('sold_at')
                ->limit(12)
                ->get();

            $salesHistoryYears = ProductSale::query()
                ->forGyms($gymIds)
                ->selectRaw('YEAR(sold_at) as year')
                ->whereNotNull('sold_at')
                ->groupByRaw('YEAR(sold_at)')
                ->orderByDesc('year')
                ->pluck('year')
                ->map(static fn ($year): int => (int) $year)
                ->filter(static fn (int $year): bool => $year > 0)
                ->values();

            if ($salesHistoryYears->isEmpty()) {
                $salesHistoryYears = collect([(int) now()->year]);
            }

            $historyBaseQuery = ProductSale::query()
                ->forGyms($gymIds)
                ->whereNotNull('sold_at');

            if ($salesHistoryFilters['year'] > 0) {
                $historyBaseQuery->whereYear('sold_at', $salesHistoryFilters['year']);
            }

            if ($salesHistoryFilters['month'] > 0) {
                $historyBaseQuery->whereMonth('sold_at', $salesHistoryFilters['month']);
            }

            if ($salesHistoryFilters['day'] > 0) {
                $historyBaseQuery->whereDay('sold_at', $salesHistoryFilters['day']);
            }

            $salesHistoryTotal = (int) (clone $historyBaseQuery)->count();
            $salesHistoryRows = (clone $historyBaseQuery)
                ->with(['product:id,name,category', 'soldBy:id,name', 'client:id,first_name,last_name', 'gym:id,name'])
                ->orderByDesc('sold_at')
                ->orderByDesc('id')
                ->limit(300)
                ->get();
            $salesHistoryTruncated = $salesHistoryTotal > 300;

            if (! $isGlobalScope) {
                $saleProducts = Product::query()
                    ->forGym($gymId)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get(['id', 'name', 'sku', 'barcode', 'stock', 'sale_price']);

                $saleClients = Client::query()
                    ->forGym($gymId)
                    ->select(['id', 'first_name', 'last_name'])
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->limit(150)
                    ->get();
            }
        }

        return view('sales.index', [
            'schemaReady' => $schemaReady,
            'isGlobalScope' => $isGlobalScope,
            'showGymColumn' => $showGymColumn,
            'todaySummary' => $todaySummary,
            'weekSummary' => $weekSummary,
            'monthSummary' => $monthSummary,
            'inventorySummary' => $inventorySummary,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'recentSales' => $recentSales,
            'saleProducts' => $saleProducts,
            'saleClients' => $saleClients,
            'selectedProductId' => $selectedProductId,
            'salesHistoryFilters' => $salesHistoryFilters,
            'salesHistoryRows' => $salesHistoryRows,
            'salesHistoryYears' => $salesHistoryYears,
            'salesHistoryTotal' => $salesHistoryTotal,
            'salesHistoryTruncated' => $salesHistoryTruncated,
            'openSalesHistoryModal' => $openSalesHistoryModal,
            'activeGymId' => $gymId,
        ]);
    }

    public function storeSale(Request $request, string $contextGym): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('sales.index', ['contextGym' => $contextGym, 'scope' => 'global'])
                ->withErrors(['sales' => 'Selecciona una sede especifica para registrar ventas.']);
        }

        if (! $this->schemaReady()) {
            return redirect()
                ->route('sales.index', ['contextGym' => $contextGym])
                ->withErrors(['sales' => 'Falta ejecutar las migraciones del modulo de ventas.']);
        }

        $gymId = $this->resolveGymId($request);
        $actor = $request->user();
        abort_unless($actor, 403, 'Usuario no autenticado.');

        $data = $request->validate([
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query->where('gym_id', $gymId)->where('status', 'active')),
            ],
            'client_id' => [
                'nullable',
                'integer',
                Rule::exists('clients', 'id')->where(fn ($query) => $query->where('gym_id', $gymId)),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'transfer'])],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::query()
            ->forGym($gymId)
            ->findOrFail((int) $data['product_id']);

        try {
            $this->productInventoryService->registerSale(
                product: $product,
                actor: $actor,
                quantity: (int) $data['quantity'],
                paymentMethod: (string) $data['payment_method'],
                clientId: ! empty($data['client_id']) ? (int) $data['client_id'] : null,
                notes: $data['notes'] ?? null,
                soldAt: Carbon::now()
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('sales.index', ['contextGym' => $contextGym, 'product_id' => $product->id])
                ->withErrors(['sales' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('sales.index', ['contextGym' => $contextGym])
            ->with('status', 'Venta registrada y enviada a caja correctamente.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function schemaReady(): bool
    {
        return Schema::hasTable('products')
            && Schema::hasColumn('products', 'barcode')
            && Schema::hasTable('product_sales')
            && Schema::hasTable('product_stock_movements');
    }
}
