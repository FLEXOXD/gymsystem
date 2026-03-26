<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Services\CashSessionService;
use App\Services\ProductCodeService;
use App\Services\ProductInventoryService;
use App\Support\ActiveGymContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use RuntimeException;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductInventoryService $productInventoryService,
        private readonly ProductCodeService $productCodeService,
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
        $search = trim((string) $request->query('q', ''));
        $editingProductId = (int) $request->query('edit', 0);
        $stockProductId = (int) $request->query('stock_product', 0);

        $products = collect();
        $stockProducts = collect();
        $recentMovements = collect();
        $editingProduct = null;
        $hasOpenCashSession = false;
        $productStats = [
            'total' => 0,
            'active' => 0,
            'stock_units' => 0,
            'low_stock' => 0,
        ];

        if ($schemaReady) {
            if (! $isGlobalScope) {
                $hasOpenCashSession = $this->cashSessionService->getOpenSession($gymId) !== null;
            }

            $products = Product::query()
                ->forGyms($gymIds)
                ->search($search)
                ->with(['gym:id,name', 'createdBy:id,name'])
                ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->paginate(18)
                ->withQueryString();

            $editingProduct = $editingProductId > 0
                ? Product::query()->forGyms($gymIds)->find($editingProductId)
                : null;

            if (! $editingProduct instanceof Product) {
                $editingProduct = null;
            }

            $productStats = [
                'total' => (int) Product::query()->forGyms($gymIds)->count(),
                'active' => (int) Product::query()->forGyms($gymIds)->where('status', 'active')->count(),
                'stock_units' => (int) Product::query()->forGyms($gymIds)->sum('stock'),
                'low_stock' => (int) Product::query()
                    ->forGyms($gymIds)
                    ->where('status', 'active')
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->count(),
            ];

            $recentMovements = ProductStockMovement::query()
                ->forGyms($gymIds)
                ->with(['product:id,name,sku,barcode', 'user:id,name', 'gym:id,name'])
                ->orderByDesc('occurred_at')
                ->limit(10)
                ->get();

            if (! $isGlobalScope) {
                $stockProducts = Product::query()
                    ->forGym($gymId)
                    ->orderBy('name')
                    ->get(['id', 'name', 'sku', 'barcode', 'stock', 'status']);
            }
        }

        return view('products.index', [
            'schemaReady' => $schemaReady,
            'products' => $products,
            'recentMovements' => $recentMovements,
            'stockProducts' => $stockProducts,
            'productStats' => $productStats,
            'showGymColumn' => $showGymColumn,
            'isGlobalScope' => $isGlobalScope,
            'editingProduct' => $editingProduct,
            'stockProductId' => $stockProductId,
            'search' => $search,
            'activeGymId' => $gymId,
            'hasOpenCashSession' => $hasOpenCashSession,
        ]);
    }

    public function store(Request $request, string $contextGym): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym, 'scope' => 'global'])
                ->withErrors(['products' => 'Selecciona una sede especifica para crear productos.']);
        }

        if (! $this->schemaReady()) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym])
                ->withErrors(['products' => 'Falta ejecutar las migraciones del modulo de productos.']);
        }

        $gymId = $this->resolveGymId($request);
        $actor = $request->user();
        abort_unless($actor, 403, 'Usuario no autenticado.');

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'sku' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('products', 'sku')->where(fn ($query) => $query->where('gym_id', $gymId)),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('products', 'barcode')->where(fn ($query) => $query->where('gym_id', $gymId)),
            ],
            'category' => ['nullable', 'string', 'max:80'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'initial_stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'initial_payment_method' => ['nullable', Rule::in(['cash', 'card', 'transfer'])],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $initialStock = (int) ($data['initial_stock'] ?? 0);
        if ($initialStock > 0 && ! $this->cashSessionService->getOpenSession($gymId)) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym])
                ->withErrors(['products' => 'Debes abrir caja antes de cargar stock inicial con costo.'])
                ->withInput();
        }

        $product = Product::query()->create([
            'gym_id' => $gymId,
            'created_by' => (int) $actor->id,
            'name' => trim((string) $data['name']),
            'sku' => $this->productCodeService->normalizeCode($data['sku'] ?? null),
            'barcode' => $this->productCodeService->normalizeCode($data['barcode'] ?? null),
            'category' => $this->normalizeText($data['category'] ?? null),
            'sale_price' => round((float) $data['sale_price'], 2),
            'cost_price' => round((float) ($data['cost_price'] ?? 0), 2),
            'stock' => 0,
            'min_stock' => max(0, (int) ($data['min_stock'] ?? 0)),
            'status' => (string) ($data['status'] ?? 'active'),
            'description' => $this->normalizeText($data['description'] ?? null),
        ]);

        $product = $this->productCodeService->ensureCodes(
            product: $product,
            sku: $data['sku'] ?? null,
            barcode: $data['barcode'] ?? null
        );

        if ($initialStock > 0) {
            try {
                $this->productInventoryService->registerStockMovement(
                    product: $product,
                    actor: $actor,
                    quantityChange: $initialStock,
                    type: 'opening',
                    unitCost: isset($data['cost_price']) ? (float) $data['cost_price'] : null,
                    note: 'Stock inicial del producto',
                    paymentMethod: (string) ($data['initial_payment_method'] ?? 'cash')
                );
            } catch (RuntimeException $exception) {
                $product->delete();

                return redirect()
                    ->route('products.index', ['contextGym' => $contextGym])
                    ->withErrors(['products' => $exception->getMessage()])
                    ->withInput();
            }
        }

        return redirect()
            ->route('products.index', ['contextGym' => $contextGym])
            ->with('status', 'Producto creado correctamente.');
    }

    public function update(Request $request, string $contextGym, Product $product): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $this->assertProductBelongsToGym($product, $gymId);

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym, 'scope' => 'global'])
                ->withErrors(['products' => 'Selecciona una sede especifica para editar productos.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'sku' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('products', 'sku')
                    ->ignore($product->id)
                    ->where(fn ($query) => $query->where('gym_id', $gymId)),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:80',
                Rule::unique('products', 'barcode')
                    ->ignore($product->id)
                    ->where(fn ($query) => $query->where('gym_id', $gymId)),
            ],
            'category' => ['nullable', 'string', 'max:80'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $product->update([
            'name' => trim((string) $data['name']),
            'sku' => $this->productCodeService->normalizeCode($data['sku'] ?? null),
            'barcode' => $this->productCodeService->normalizeCode($data['barcode'] ?? null),
            'category' => $this->normalizeText($data['category'] ?? null),
            'sale_price' => round((float) $data['sale_price'], 2),
            'cost_price' => round((float) ($data['cost_price'] ?? 0), 2),
            'min_stock' => max(0, (int) ($data['min_stock'] ?? 0)),
            'status' => (string) ($data['status'] ?? $product->status),
            'description' => $this->normalizeText($data['description'] ?? null),
        ]);

        $product = $this->productCodeService->ensureCodes(
            product: $product,
            sku: $data['sku'] ?? null,
            barcode: $data['barcode'] ?? null
        );

        return redirect()
            ->route('products.index', ['contextGym' => $contextGym])
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function toggle(Request $request, string $contextGym, Product $product): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $this->assertProductBelongsToGym($product, $gymId);

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym, 'scope' => 'global'])
                ->withErrors(['products' => 'Selecciona una sede especifica para cambiar el estado del producto.']);
        }

        $product->update([
            'status' => (string) $product->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()
            ->route('products.index', ['contextGym' => $contextGym])
            ->with('status', 'Estado del producto actualizado.');
    }

    public function adjustStock(Request $request, string $contextGym, ?Product $product = null): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);

        if ($product instanceof Product && ! $request->filled('product_id')) {
            $request->merge([
                'product_id' => (int) $product->id,
            ]);
        }

        if (ActiveGymContext::isGlobal($request)) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym, 'scope' => 'global'])
                ->withErrors(['products' => 'Selecciona una sede especifica para mover stock.']);
        }

        $actor = $request->user();
        abort_unless($actor, 403, 'Usuario no autenticado.');

        $data = $request->validate([
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where(fn ($query) => $query->where('gym_id', $gymId)),
            ],
            'movement_type' => ['required', Rule::in(['entry', 'adjustment_add', 'adjustment_remove'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', Rule::in(['cash', 'card', 'transfer'])],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::query()->forGym($gymId)->find((int) $data['product_id']);
        if (! $product instanceof Product) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym])
                ->withErrors(['product_id' => 'Selecciona un producto valido para mover stock.'])
                ->withInput();
        }

        $quantity = (int) $data['quantity'];
        $quantityChange = match ((string) $data['movement_type']) {
            'entry', 'adjustment_add' => $quantity,
            'adjustment_remove' => -$quantity,
            default => 0,
        };

        try {
            $this->productInventoryService->registerStockMovement(
                product: $product,
                actor: $actor,
                quantityChange: $quantityChange,
                type: (string) $data['movement_type'],
                unitCost: isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
                note: $data['note'] ?? null,
                paymentMethod: $data['payment_method'] ?? null
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('products.index', ['contextGym' => $contextGym])
                ->withErrors(['stock' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('products.index', ['contextGym' => $contextGym])
            ->with('status', 'Stock actualizado correctamente.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function assertProductBelongsToGym(Product $product, int $gymId): void
    {
        abort_unless((int) $product->gym_id === $gymId, 404);
    }

    private function schemaReady(): bool
    {
        return Schema::hasTable('products')
            && Schema::hasColumn('products', 'barcode')
            && Schema::hasTable('product_sales')
            && Schema::hasTable('product_stock_movements');
    }

    private function normalizeText(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}
