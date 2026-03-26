<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductSale;
use App\Services\CashSessionService;
use App\Services\ProductInventoryService;
use App\Services\ReportService;
use App\Support\ActiveGymContext;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SalesInventoryController extends Controller
{
    public function __construct(
        private readonly ProductInventoryService $productInventoryService,
        private readonly ReportService $reportService,
        private readonly CashSessionService $cashSessionService
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
        $hasOpenCashSession = false;

        if ($schemaReady) {
            if (! $isGlobalScope) {
                $hasOpenCashSession = $this->cashSessionService->getOpenSession($gymId) !== null;
            }

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

            $salesYearExpression = ProductSale::query()->getModel()->getConnection()->getDriverName() === 'sqlite'
                ? "CAST(strftime('%Y', sold_at) as integer)"
                : 'YEAR(sold_at)';

            $salesHistoryYears = ProductSale::query()
                ->forGyms($gymIds)
                ->selectRaw($salesYearExpression.' as year')
                ->whereNotNull('sold_at')
                ->groupByRaw($salesYearExpression)
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
            'hasOpenCashSession' => $hasOpenCashSession,
        ]);
    }

    public function storeSale(Request $request, string $contextGym): RedirectResponse
    {
        $redirectParams = $this->panelRouteParams($request);

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('sales.index', ['contextGym' => $contextGym, 'scope' => 'global'])
                ->withErrors(['sales' => 'Selecciona una sede especifica para registrar ventas.']);
        }

        if (! $this->schemaReady()) {
            return $this->redirectToPanelTarget($request, 'sales.index', $redirectParams)
                ->withErrors(['sales' => 'Falta ejecutar las migraciones del modulo de ventas.']);
        }

        $gymId = $this->resolveGymId($request);
        if (! $this->cashSessionService->getOpenSession($gymId)) {
            return $this->redirectToPanelTarget($request, 'sales.index', $redirectParams)
                ->withErrors(['sales' => 'Debes abrir caja antes de registrar ventas de productos.'])
                ->withInput(array_merge($request->except([]), ['open_sales_register_modal' => '1']));
        }

        $actor = $request->user();
        abort_unless($actor, 403, 'Usuario no autenticado.');

        $baseData = $request->validate([
            'client_id' => [
                'nullable',
                'integer',
                Rule::exists('clients', 'id')->where(fn ($query) => $query->where('gym_id', $gymId)),
            ],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'transfer'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'sale_items_payload' => ['nullable', 'string', 'max:12000'],
        ]);

        $batchItems = $this->parseSaleItemsPayload(
            rawPayload: (string) ($baseData['sale_items_payload'] ?? ''),
            gymId: $gymId
        );

        if ($batchItems !== []) {
            try {
                DB::transaction(function () use ($batchItems, $actor, $baseData): void {
                    foreach ($batchItems as $item) {
                        $this->productInventoryService->registerSale(
                            product: $item['product'],
                            actor: $actor,
                            quantity: (int) $item['quantity'],
                            paymentMethod: (string) $baseData['payment_method'],
                            clientId: ! empty($baseData['client_id']) ? (int) $baseData['client_id'] : null,
                            notes: $baseData['notes'] ?? null,
                            soldAt: Carbon::now()
                        );
                    }
                });
            } catch (RuntimeException $exception) {
                $firstProductId = isset($batchItems[0]['product']) ? (int) $batchItems[0]['product']->id : 0;

                return $this->redirectToPanelTarget($request, 'sales.index', $redirectParams + ['product_id' => $firstProductId])
                    ->withErrors(['sales' => $exception->getMessage()])
                    ->withInput();
            }

            $lineCount = count($batchItems);
            $totalUnits = array_reduce($batchItems, static function (int $carry, array $item): int {
                return $carry + (int) ($item['quantity'] ?? 0);
            }, 0);

            return $this->redirectToPanelTarget($request, 'sales.index', $redirectParams)
                ->with('status', 'Venta múltiple registrada: '.$lineCount.' producto(s), '.$totalUnits.' unidad(es).')
                ->with('clear_sales_scan_cart', 1);
        }

        $singleData = $request->validate([
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query->where('gym_id', $gymId)->where('status', 'active')),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
        ]);

        $product = Product::query()
            ->forGym($gymId)
            ->findOrFail((int) $singleData['product_id']);

        try {
            $this->productInventoryService->registerSale(
                product: $product,
                actor: $actor,
                quantity: (int) $singleData['quantity'],
                paymentMethod: (string) $baseData['payment_method'],
                clientId: ! empty($baseData['client_id']) ? (int) $baseData['client_id'] : null,
                notes: $baseData['notes'] ?? null,
                soldAt: Carbon::now()
            );
        } catch (RuntimeException $exception) {
            return $this->redirectToPanelTarget($request, 'sales.index', $redirectParams + ['product_id' => $product->id])
                ->withErrors(['sales' => $exception->getMessage()])
                ->withInput();
        }

        return $this->redirectToPanelTarget($request, 'sales.index', $redirectParams)
            ->with('status', 'Venta registrada y enviada a caja correctamente.')
            ->with('clear_sales_scan_cart', 1);
    }

    private function redirectToPanelTarget(Request $request, string $fallbackRoute, array $fallbackParams = []): RedirectResponse
    {
        $redirectTo = trim((string) $request->input('redirect_to', ''));
        if ($redirectTo !== '' && str_starts_with($redirectTo, '/') && ! str_starts_with($redirectTo, '//')) {
            return redirect()->to($redirectTo);
        }

        return redirect()->route($fallbackRoute, $fallbackParams);
    }

    /**
     * @return array<string, string>
     */
    private function panelRouteParams(Request $request): array
    {
        $params = [];
        $contextGym = trim((string) $request->route('contextGym'));
        if ($contextGym !== '') {
            $params['contextGym'] = $contextGym;
        }

        if (strtolower(trim((string) $request->query('pwa_mode', ''))) === 'standalone') {
            $params['pwa_mode'] = 'standalone';
        }

        return $params;
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

    /**
     * @return array<int, array{product: Product, quantity: int}>
     *
     * @throws ValidationException
     */
    private function parseSaleItemsPayload(string $rawPayload, int $gymId): array
    {
        $payload = trim($rawPayload);
        if ($payload === '') {
            return [];
        }

        $decoded = json_decode($payload, true);
        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'sales' => 'No se pudo leer la lista de productos escaneados.',
            ]);
        }

        $aggregated = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }

            $productId = (int) ($row['product_id'] ?? 0);
            $quantity = (int) ($row['quantity'] ?? 0);
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            if (! array_key_exists($productId, $aggregated)) {
                $aggregated[$productId] = 0;
            }

            $aggregated[$productId] += $quantity;
        }

        if ($aggregated === []) {
            return [];
        }

        if (count($aggregated) > 120) {
            throw ValidationException::withMessages([
                'sales' => 'La lista de venta supera el limite permitido de productos.',
            ]);
        }

        $productIds = array_map('intval', array_keys($aggregated));
        $products = Product::query()
            ->forGym($gymId)
            ->where('status', 'active')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        if ($products->count() !== count($productIds)) {
            throw ValidationException::withMessages([
                'sales' => 'Uno o mas productos de la lista ya no estan disponibles para venta.',
            ]);
        }

        $result = [];
        foreach ($aggregated as $productId => $quantity) {
            /** @var Product|null $product */
            $product = $products->get((int) $productId);
            if (! $product) {
                continue;
            }

            $result[] = [
                'product' => $product,
                'quantity' => (int) $quantity,
            ];
        }

        return $result;
    }
}
