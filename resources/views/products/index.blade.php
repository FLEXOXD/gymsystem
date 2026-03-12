@extends('layouts.panel')

@section('title', 'Productos')
@section('page-title', 'Productos')

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $contextGym = (string) request()->route('contextGym');
        $isGlobalScope = (bool) ($isGlobalScope ?? false);
        $activeGymId = (int) ($activeGymId ?? request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canViewSalesReports = auth()->user()?->isOwner()
            && $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'sales_inventory_reports');
        $indexRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $productsCollection = method_exists($products, 'getCollection') ? $products->getCollection() : collect($products);
        $stockProductId = (int) ($stockProductId ?? 0);
        $editingProduct = $editingProduct instanceof \App\Models\Product ? $editingProduct : null;
    @endphp

    <div class="space-y-5">
        <div class="space-y-2">
            @if (session('status'))
                <div class="ui-alert ui-alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="ui-alert ui-alert-danger">{{ $errors->first() }}</div>
            @endif
            @if ($isGlobalScope)
                <div class="ui-alert ui-alert-info">Vista global activa: puedes analizar el catalogo consolidado, pero la carga y edicion se hacen desde una sede especifica.</div>
            @endif
            @if (! $schemaReady)
                <div class="ui-alert ui-alert-warning">Falta ejecutar <code>php artisan migrate</code> para activar el modulo de productos.</div>
            @endif
        </div>

        <x-ui.card title="Navegacion del modulo" subtitle="Productos queda separado del panel financiero y del panel de clientes.">
            <div class="flex flex-wrap gap-2">
                <x-ui.button :href="route('panel.index', $indexRouteParams)" variant="ghost">Ganancias del gimnasio</x-ui.button>
                <x-ui.button :href="route('clients.index', $indexRouteParams)" variant="ghost">Panel de clientes</x-ui.button>
                <x-ui.button :href="route('sales.index', $indexRouteParams)" variant="secondary">Ventas e inventario</x-ui.button>
                @if ($canViewSalesReports && \Illuminate\Support\Facades\Route::has('reports.sales-inventory'))
                    <x-ui.button :href="route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())" variant="ghost">Reportes del modulo</x-ui.button>
                @endif
            </div>
        </x-ui.card>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Productos</p>
                <p class="mt-2 text-3xl font-black">{{ (int) ($productStats['total'] ?? 0) }}</p>
                <p class="ui-muted mt-2 text-sm">Catalogo total disponible.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Activos</p>
                <p class="mt-2 text-3xl font-black text-emerald-600">{{ (int) ($productStats['active'] ?? 0) }}</p>
                <p class="ui-muted mt-2 text-sm">Listos para vender.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Unidades en stock</p>
                <p class="mt-2 text-3xl font-black text-cyan-600">{{ (int) ($productStats['stock_units'] ?? 0) }}</p>
                <p class="ui-muted mt-2 text-sm">Existencia total cargada.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Stock bajo</p>
                <p class="mt-2 text-3xl font-black text-amber-600">{{ (int) ($productStats['low_stock'] ?? 0) }}</p>
                <p class="ui-muted mt-2 text-sm">Productos por reponer.</p>
            </x-ui.card>
        </section>

        @if ($schemaReady && ! $isGlobalScope)
            <section class="grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                <x-ui.card :title="$editingProduct ? 'Editar producto' : 'Registrar producto'" subtitle="Define precio, costo, categoría y stock mínimo del producto.">
                    <form method="POST" action="{{ $editingProduct ? route('products.update', ['contextGym' => $contextGym, 'product' => $editingProduct->id]) : route('products.store', ['contextGym' => $contextGym]) }}" class="grid gap-4 md:grid-cols-2">
                        @csrf
                        @if ($editingProduct)
                            @method('PUT')
                        @endif

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Nombre</span>
                            <input type="text" name="name" class="ui-input" required value="{{ old('name', $editingProduct?->name) }}" placeholder="Ej: Proteina whey 2lb">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>SKU interno</span>
                            <input type="text" name="sku" class="ui-input" value="{{ old('sku', $editingProduct?->sku) }}" placeholder="Dejalo vacio para generarlo">
                            <span class="block text-xs font-normal ui-muted">Se genera automatico segun categoria, nombre e ID si no lo escribes.</span>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Codigo de barras</span>
                            <input id="product-barcode-input" type="text" name="barcode" class="ui-input" value="{{ old('barcode', $editingProduct?->barcode) }}" placeholder="Escanea o dejalo vacio para generarlo">
                            <span class="block text-xs font-normal ui-muted">Puedes usar lector directo aqui o el boton flotante de escaneo en vivo.</span>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Categoria</span>
                            <input type="text" name="category" class="ui-input" value="{{ old('category', $editingProduct?->category) }}" placeholder="Bebidas, suplementos, accesorios">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Precio de venta</span>
                            <input type="number" step="0.01" min="0" name="sale_price" class="ui-input" required value="{{ old('sale_price', $editingProduct ? number_format((float) $editingProduct->sale_price, 2, '.', '') : '') }}">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Costo unitario</span>
                            <input type="number" step="0.01" min="0" name="cost_price" class="ui-input" value="{{ old('cost_price', $editingProduct ? number_format((float) $editingProduct->cost_price, 2, '.', '') : '') }}">
                        </label>

                        @if (! $editingProduct)
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Stock inicial</span>
                                <input type="number" min="0" name="initial_stock" class="ui-input" value="{{ old('initial_stock', 0) }}">
                            </label>
                        @endif

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Stock mínimo</span>
                            <input type="number" min="0" name="min_stock" class="ui-input" value="{{ old('min_stock', $editingProduct?->min_stock ?? 0) }}">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Estado</span>
                            <select name="status" class="ui-input">
                                <option value="active" @selected(old('status', $editingProduct?->status ?? 'active') === 'active')>Activo</option>
                                <option value="inactive" @selected(old('status', $editingProduct?->status) === 'inactive')>Inactivo</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Descripcion</span>
                            <textarea name="description" rows="3" class="ui-input" placeholder="Notas internas del producto">{{ old('description', $editingProduct?->description) }}</textarea>
                        </label>

                        <div class="flex flex-wrap gap-2 md:col-span-2">
                            <x-ui.button type="submit">{{ $editingProduct ? 'Guardar cambios' : 'Crear producto' }}</x-ui.button>
                            @if ($editingProduct)
                                <x-ui.button :href="route('products.index', $indexRouteParams)" variant="ghost">Cancelar edicion</x-ui.button>
                            @endif
                        </div>
                    </form>
                </x-ui.card>

                <x-ui.card title="Mover stock" subtitle="Registra entradas o ajustes manuales de inventario.">
                    <form id="stock-form" method="POST" action="{{ route('products.stock', ['contextGym' => $contextGym, 'product' => $stockProductId > 0 ? $stockProductId : (int) old('product_id', 0)]) }}" class="space-y-4">
                        @csrf

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Producto</span>
                            <select name="product_id" class="ui-input" onchange="if(this.value){ this.form.action='{{ route('products.stock', ['contextGym' => $contextGym, 'product' => '__PRODUCT__']) }}'.replace('__PRODUCT__', this.value); }">
                                <option value="">Selecciona un producto</option>
                                @foreach ($stockProducts as $productOption)
                                    <option value="{{ $productOption->id }}" @selected((int) old('product_id', $stockProductId) === (int) $productOption->id)>
                                        {{ $productOption->name }} | SKU {{ $productOption->sku ?: '---' }} | BAR {{ $productOption->barcode ?: '---' }} | stock {{ (int) $productOption->stock }} | {{ (string) $productOption->status }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Movimiento</span>
                                <select name="movement_type" class="ui-input">
                                    <option value="entry" @selected(old('movement_type') === 'entry')>Entrada</option>
                                    <option value="adjustment_add" @selected(old('movement_type') === 'adjustment_add')>Ajuste positivo</option>
                                    <option value="adjustment_remove" @selected(old('movement_type') === 'adjustment_remove')>Ajuste negativo</option>
                                </select>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Cantidad</span>
                                <input type="number" min="1" name="quantity" class="ui-input" value="{{ old('quantity', 1) }}">
                            </label>
                        </div>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Costo unitario opcional</span>
                            <input type="number" step="0.01" min="0" name="unit_cost" class="ui-input" value="{{ old('unit_cost') }}">
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Nota</span>
                            <textarea name="note" rows="3" class="ui-input" placeholder="Ej: reposicion semanal o ajuste por conteo">{{ old('note') }}</textarea>
                        </label>

                        <x-ui.button type="submit">Guardar movimiento</x-ui.button>
                    </form>
                </x-ui.card>
            </section>
        @endif

        <x-ui.card title="Catalogo de productos" subtitle="Vista operativa del inventario actual del gimnasio.">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <form method="GET" action="{{ route('products.index', ['contextGym' => $contextGym]) }}" class="flex flex-wrap items-end gap-2">
                    @if ($isGlobalScope)
                        <input type="hidden" name="scope" value="global">
                    @endif
                    <label class="space-y-1 text-sm font-semibold ui-muted">
                        <span>Buscar</span>
                        <input type="text" name="q" class="ui-input min-w-[240px]" value="{{ $search }}" placeholder="Nombre, SKU, codigo o categoria">
                    </label>
                    <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
                </form>
            </div>

            <div class="smart-list-wrap">
                <table class="ui-table w-full min-w-[960px] text-sm">
                    <thead>
                        <tr>
                            @if ($showGymColumn)
                                <th>Sede</th>
                            @endif
                            <th>Producto</th>
                            <th>Codigo</th>
                            <th>Categoria</th>
                            <th>Venta</th>
                            <th>Costo</th>
                            <th>Stock</th>
                            <th>Minimo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productsCollection as $product)
                            <tr>
                                @if ($showGymColumn)
                                    <td>{{ $product->gym?->name ?? '-' }}</td>
                                @endif
                                <td>
                                    <div class="font-semibold">{{ $product->name }}</div>
                                    <div class="ui-muted text-xs">{{ $product->description ? \Illuminate\Support\Str::limit($product->description, 56) : 'Sin descripcion' }}</div>
                                </td>
                                <td>
                                    <div class="text-xs font-semibold">SKU: {{ $product->sku ?: 'Automatico pendiente' }}</div>
                                    <div class="ui-muted text-xs">BAR: {{ $product->barcode ?: 'Automatico pendiente' }}</div>
                                </td>
                                <td>{{ $product->category ?: '-' }}</td>
                                <td>{{ $currencyFormatter::format((float) $product->sale_price, $appCurrencyCode) }}</td>
                                <td>{{ $currencyFormatter::format((float) $product->cost_price, $appCurrencyCode) }}</td>
                                <td class="{{ (int) $product->stock <= (int) $product->min_stock ? 'text-amber-600 font-bold' : '' }}">{{ (int) $product->stock }}</td>
                                <td>{{ (int) $product->min_stock }}</td>
                                <td>
                                    <x-ui.badge :variant="(string) $product->status === 'active' ? 'success' : 'muted'">
                                        {{ (string) $product->status === 'active' ? 'Activo' : 'Inactivo' }}
                                    </x-ui.badge>
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        @if (! $isGlobalScope)
                                            <x-ui.button :href="route('products.index', ['contextGym' => $contextGym, 'edit' => $product->id])" size="sm" variant="ghost">Editar</x-ui.button>
                                            <x-ui.button :href="route('products.index', ['contextGym' => $contextGym, 'stock_product' => $product->id])" size="sm" variant="ghost">Stock</x-ui.button>
                                            <form method="POST" action="{{ route('products.toggle', ['contextGym' => $contextGym, 'product' => $product->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button type="submit" size="sm" :variant="(string) $product->status === 'active' ? 'danger' : 'secondary'">
                                                    {{ (string) $product->status === 'active' ? 'Desactivar' : 'Activar' }}
                                                </x-ui.button>
                                            </form>
                                        @else
                                            <span class="ui-muted text-xs">Solo lectura</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showGymColumn ? 10 : 9 }}" class="py-8 text-center ui-muted">Todavia no hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($products, 'links'))
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </x-ui.card>

        <x-ui.card title="Ultimos movimientos de stock" subtitle="Historial reciente de entradas, ajustes y salidas por venta.">
            <div class="smart-list-wrap">
                <table class="ui-table w-full min-w-[860px] text-sm">
                    <thead>
                        <tr>
                            @if ($showGymColumn)
                                <th>Sede</th>
                            @endif
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cambio</th>
                            <th>Antes</th>
                            <th>Despues</th>
                            <th>Usuario</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentMovements as $movement)
                            <tr>
                                @if ($showGymColumn)
                                    <td>{{ $movement->gym?->name ?? '-' }}</td>
                                @endif
                                <td>{{ $movement->occurred_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="font-semibold">{{ $movement->product?->name ?? '-' }}</div>
                                    <div class="ui-muted text-xs">{{ $movement->product?->barcode ?: ($movement->product?->sku ?: 'Sin codigo') }}</div>
                                </td>
                                <td>{{ $movement->type }}</td>
                                <td class="{{ (int) $movement->quantity_change >= 0 ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold' }}">
                                    {{ (int) $movement->quantity_change >= 0 ? '+' : '' }}{{ (int) $movement->quantity_change }}
                                </td>
                                <td>{{ (int) $movement->stock_before }}</td>
                                <td>{{ (int) $movement->stock_after }}</td>
                                <td>{{ $movement->user?->name ?? '-' }}</td>
                                <td>{{ $movement->note ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showGymColumn ? 9 : 8 }}" class="py-8 text-center ui-muted">Aun no hay movimientos de stock.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>

    @include('sales_inventory.partials.remote-scanner-fab', [
        'scanContext' => 'products',
        'contextGym' => $contextGym,
        'schemaReady' => $schemaReady,
        'isGlobalScope' => $isGlobalScope,
    ])
@endsection

@push('scripts')
<script>
    (function () {
        const barcodeInput = document.getElementById('product-barcode-input');
        const lastCodeStorageKey = 'remote_scan_last_code_products';

        if (!barcodeInput) {
            return;
        }

        window.addEventListener('remote-scanner:scan', function (event) {
            const code = (event.detail?.code || '').toString().trim();
            if (code === '') {
                return;
            }

            barcodeInput.value = code;
            barcodeInput.focus();
            barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
            try {
                window.sessionStorage.setItem(lastCodeStorageKey, code);
            } catch (error) {
                // Ignore storage failures.
            }
        });

        try {
            const savedCode = window.sessionStorage.getItem(lastCodeStorageKey);
            if (savedCode && savedCode.trim() !== '' && (barcodeInput.value || '').trim() === '') {
                barcodeInput.value = savedCode;
                barcodeInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        } catch (error) {
            // Ignore storage failures.
        }
    })();
</script>
@endpush
