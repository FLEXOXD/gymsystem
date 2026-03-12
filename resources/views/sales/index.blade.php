@extends('layouts.panel')

@section('title', 'Ventas e inventario')
@section('page-title', 'Ventas e inventario')

@push('styles')
<style>
    #sales-register-modal .sales-register-scan-sticky {
        position: sticky;
        top: 0.5rem;
        z-index: 15;
    }
</style>
@endpush

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
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];
        $monthLabels = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];
        $selectedProductId = (int) old('product_id', $selectedProductId ?? 0);
        $selectedClientId = (int) old('client_id', 0);
        $salesHistoryFilters = is_array($salesHistoryFilters ?? null) ? $salesHistoryFilters : ['year' => 0, 'month' => 0, 'day' => 0];
        $selectedSalesYear = (int) ($salesHistoryFilters['year'] ?? 0);
        $selectedSalesMonth = (int) ($salesHistoryFilters['month'] ?? 0);
        $selectedSalesDay = (int) ($salesHistoryFilters['day'] ?? 0);
        $openSalesHistoryModal = (bool) ($openSalesHistoryModal ?? false);
        $openSalesRegisterModal = old('open_sales_register_modal') === '1';
        $clearSalesScanCart = (bool) session('clear_sales_scan_cart', false);
        $saleProductsPayload = $saleProducts->map(function ($product) {
            return [
                'id' => (int) $product->id,
                'name' => (string) $product->name,
                'sku' => (string) ($product->sku ?? ''),
                'barcode' => (string) ($product->barcode ?? ''),
                'stock' => (int) $product->stock,
                'sale_price' => round((float) $product->sale_price, 2),
            ];
        })->values();
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
                <div class="ui-alert ui-alert-info">Vista global activa: aqui analizas ventas e inventario consolidados, pero las ventas y ajustes se registran solo desde una sede especifica.</div>
            @endif
            @if (! $schemaReady)
                <div class="ui-alert ui-alert-warning">Falta ejecutar <code>php artisan migrate</code> para activar el modulo de ventas e inventario.</div>
            @endif
        </div>

        <x-ui.card title="Centro comercial del gimnasio" subtitle="Separa ganancias, clientes y productos para que la operacion no quede mezclada.">
            <div class="flex flex-wrap gap-2">
                <x-ui.button :href="route('panel.index', $indexRouteParams)" variant="ghost">Ganancias del gimnasio</x-ui.button>
                <x-ui.button :href="route('clients.index', $indexRouteParams)" variant="ghost">Panel de clientes</x-ui.button>
                <x-ui.button :href="route('products.index', $indexRouteParams)" variant="secondary">Gestionar productos</x-ui.button>
                <x-ui.button type="button" id="open-sales-register-modal" variant="secondary">Registrar venta</x-ui.button>
                @if ($canViewSalesReports && \Illuminate\Support\Facades\Route::has('reports.sales-inventory'))
                    <x-ui.button :href="route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())" variant="ghost">Reportes del modulo</x-ui.button>
                @endif
            </div>
        </x-ui.card>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Ventas hoy</p>
                <p class="mt-2 text-3xl font-black text-cyan-700 dark:text-cyan-300">{{ (int) ($todaySummary['total_sales'] ?? 0) }}</p>
                <p class="ui-muted mt-2 text-sm">{{ (int) ($todaySummary['units_sold'] ?? 0) }} unidades vendidas.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Ingreso hoy</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) ($todaySummary['total_revenue'] ?? 0), $appCurrencyCode) }}</p>
                <p class="ui-muted mt-2 text-sm">Ticket promedio {{ $currencyFormatter::format((float) ($todaySummary['average_ticket'] ?? 0), $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Utilidad hoy</p>
                <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-300">{{ $currencyFormatter::format((float) ($todaySummary['total_profit'] ?? 0), $appCurrencyCode) }}</p>
                <p class="ui-muted mt-2 text-sm">Costo {{ $currencyFormatter::format((float) ($todaySummary['total_cost'] ?? 0), $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Ventas de la semana</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($weekSummary['total_sales'] ?? 0) }}</p>
                <p class="ui-muted mt-2 text-sm">{{ (int) ($weekSummary['units_sold'] ?? 0) }} unidades esta semana.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Ingreso del mes</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) ($monthSummary['total_revenue'] ?? 0), $appCurrencyCode) }}</p>
                <p class="ui-muted mt-2 text-sm">{{ (int) ($monthSummary['total_sales'] ?? 0) }} ventas registradas.</p>
            </x-ui.card>
            <x-ui.card>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Utilidad del mes</p>
                <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-300">{{ $currencyFormatter::format((float) ($monthSummary['total_profit'] ?? 0), $appCurrencyCode) }}</p>
                <p class="ui-muted mt-2 text-sm">Costo {{ $currencyFormatter::format((float) ($monthSummary['total_cost'] ?? 0), $appCurrencyCode) }}</p>
            </x-ui.card>
        </section>

        <section class="grid gap-4">
            <div id="sales-register-modal"
                 data-auto-open="{{ $openSalesRegisterModal ? '1' : '0' }}"
                 class="fixed inset-0 z-[90] hidden items-center justify-center bg-slate-950/80 p-4">
                <div class="w-full max-w-5xl max-h-[92vh] overflow-y-auto rounded-3xl border border-cyan-500/35 bg-slate-950 shadow-2xl">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-700/70 p-5">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.24em] text-cyan-300">Venta rápida</p>
                            <h3 class="mt-2 text-2xl font-black text-slate-100">Registrar venta de producto</h3>
                            <p class="mt-1 text-sm text-slate-300">Cada venta descuenta stock y se envía directo a caja.</p>
                        </div>
                        <button type="button" id="close-sales-register-modal" class="ui-button ui-button-ghost px-3 py-2 text-sm font-semibold">Cerrar</button>
                    </div>
                    <div class="p-5">
                        <x-ui.card title="Registrar venta de producto" subtitle="Cada venta descuenta stock y se envia directo a caja.">
                @if (! $schemaReady)
                    <p class="ui-alert ui-alert-warning">El formulario se activara despues de correr las migraciones del modulo.</p>
                @elseif ($isGlobalScope)
                    <p class="ui-alert ui-alert-info">Selecciona una sede puntual para registrar ventas. Desde vista global este modulo queda en modo analitico.</p>
                @elseif ($saleProducts->isEmpty())
                    <p class="ui-alert ui-alert-warning">No hay productos activos para vender. Crea al menos un producto y carga stock primero.</p>
                    <div class="mt-3">
                        <x-ui.button :href="route('products.index', ['contextGym' => $contextGym])" variant="secondary">Ir a productos</x-ui.button>
                    </div>
                @else
                    <form method="POST" action="{{ route('sales.store', ['contextGym' => $contextGym]) }}" class="grid gap-4 md:grid-cols-2" id="sales-form">
                        @csrf
                        <input type="hidden" name="open_sales_register_modal" value="1">
                        <input type="hidden" name="sale_items_payload" id="sale-items-payload" value="{{ old('sale_items_payload', '') }}">

                        <div class="sales-register-scan-sticky rounded-2xl border border-cyan-200 bg-cyan-50 p-4 dark:border-cyan-400/40 dark:bg-cyan-500/10 md:col-span-2">
                            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                                <label class="space-y-1 text-sm font-semibold ui-muted">
                                    <span>Escanear codigo</span>
                                    <input id="sale-scan-input" type="text" class="ui-input" placeholder="Usa lector o el boton flotante para enviar SKU / codigo" autocomplete="off">
                                </label>
                                <x-ui.button type="button" id="sale-scan-search" variant="secondary">Buscar codigo</x-ui.button>
                            </div>
                            <p class="mt-2 text-xs ui-muted">Con lector fisico solo enfoca este campo y escanea. Si usas el boton flotante, el codigo llega en vivo desde el celular. Si el producto ya estaba seleccionado, otro escaneo suma una unidad.</p>
                            <div id="sale-scan-feedback" class="mt-3 hidden rounded-xl border px-3 py-2 text-sm font-semibold"></div>
                            <div id="sale-selected-preview" class="mt-3 hidden rounded-2xl border border-slate-200 bg-white/80 p-3 dark:border-slate-700 dark:bg-slate-900/70">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p id="sale-preview-name" class="text-base font-black text-slate-900 dark:text-slate-100"></p>
                                        <p id="sale-preview-code" class="text-xs ui-muted"></p>
                                    </div>
                                    <div class="text-right">
                                        <p id="sale-preview-price" class="text-sm font-bold text-emerald-700 dark:text-emerald-300"></p>
                                        <p id="sale-preview-stock" class="text-xs ui-muted"></p>
                                    </div>
                                </div>
                            </div>
                            <div id="sale-scan-list" class="mt-3 hidden rounded-2xl border border-cyan-200 bg-white/80 p-3 dark:border-cyan-400/40 dark:bg-slate-900/70">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs font-black uppercase tracking-wider ui-muted">Listado rapido de escaneo</p>
                                    <button type="button" id="sale-scan-list-clear" class="ui-button ui-button-ghost px-3 py-1 text-xs font-semibold">
                                        Limpiar lista
                                    </button>
                                </div>
                                <div id="sale-scan-list-items" class="mt-2 space-y-2 pr-1"></div>
                                <p id="sale-scan-list-summary" class="mt-2 text-xs ui-muted"></p>
                            </div>
                        </div>

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Producto</span>
                            <select name="product_id" id="sale-product-select" class="ui-input">
                                <option value="">Selecciona un producto</option>
                                @foreach ($saleProducts as $product)
                                    <option value="{{ $product->id }}" @selected($selectedProductId === (int) $product->id)>
                                        {{ $product->name }} | SKU {{ $product->sku ?: '---' }} | BAR {{ $product->barcode ?: '---' }} | stock {{ (int) $product->stock }} | {{ $currencyFormatter::format((float) $product->sale_price, $appCurrencyCode) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <button type="button" id="sale-add-selected" class="ui-button ui-button-ghost px-3 py-2 text-sm font-semibold">
                                    Agregar seleccionado al carrito
                                </button>
                            </div>
                        </label>

                        <input type="hidden" min="1" max="999999" name="quantity" id="sale-quantity-input" value="{{ old('quantity', 1) }}">

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Método</span>
                            <select name="payment_method" class="ui-input" required>
                                <option value="cash" @selected(old('payment_method') === 'cash')>Efectivo</option>
                                <option value="card" @selected(old('payment_method') === 'card')>Tarjeta</option>
                                <option value="transfer" @selected(old('payment_method') === 'transfer')>Transferencia</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Cliente opcional</span>
                            <select name="client_id" class="ui-input">
                                <option value="">Venta sin cliente vinculado</option>
                                @foreach ($saleClients as $client)
                                    <option value="{{ $client->id }}" @selected($selectedClientId === (int) $client->id)>
                                        {{ $client->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                            <span>Notas</span>
                            <textarea name="notes" rows="3" class="ui-input" placeholder="Ej: bebida, guantes, proteína, promo del día">{{ old('notes') }}</textarea>
                        </label>

                        <div class="flex flex-wrap gap-2 md:col-span-2">
                            <x-ui.button type="submit">Registrar venta</x-ui.button>
                            <x-ui.button :href="route('products.index', ['contextGym' => $contextGym])" variant="ghost">Abrir productos</x-ui.button>
                            @if ($canViewSalesReports && \Illuminate\Support\Facades\Route::has('reports.sales-inventory'))
                                <x-ui.button :href="route('reports.sales-inventory', ['contextGym' => $contextGym])" variant="ghost">Ver reportes</x-ui.button>
                            @endif
                        </div>
                    </form>
                @endif
                        </x-ui.card>
                    </div>
                </div>
            </div>

            <x-ui.card title="Control rapido de inventario" subtitle="Lectura operativa del mes actual para reponer a tiempo.">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos del mes</p>
                        <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($inventorySummary['movement_count'] ?? 0) }}</p>
                    </article>
                    <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Unidades que entraron</p>
                        <p class="mt-2 text-2xl font-black text-emerald-800 dark:text-emerald-100">{{ (int) ($inventorySummary['units_in'] ?? 0) }}</p>
                    </article>
                    <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Unidades que salieron</p>
                        <p class="mt-2 text-2xl font-black text-rose-800 dark:text-rose-100">{{ (int) ($inventorySummary['units_out'] ?? 0) }}</p>
                    </article>
                    <article class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200">Ajustes manuales</p>
                        <p class="mt-2 text-2xl font-black text-amber-800 dark:text-amber-100">{{ (int) ($inventorySummary['manual_adjustments'] ?? 0) }}</p>
                    </article>
                    <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Productos con stock bajo</p>
                        <p class="mt-2 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ $lowStockProducts->count() }}</p>
                    </article>
                </div>
            </x-ui.card>
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            <x-ui.card title="Productos mas vendidos del mes" subtitle="Los articulos que mas estan empujando ingresos.">
                <div class="smart-list-wrap">
                    <table class="ui-table w-full min-w-[720px] text-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                @if ($showGymColumn)
                                    <th>Sede</th>
                                @endif
                                <th>Categoria</th>
                                <th>Unidades</th>
                                <th>Ingreso</th>
                                <th>Utilidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topProducts as $product)
                                <tr>
                                    <td class="font-semibold">{{ $product->product_name }}</td>
                                    @if ($showGymColumn)
                                        <td>{{ $product->gym_name ?? '-' }}</td>
                                    @endif
                                    <td>{{ $product->product_category ?: '-' }}</td>
                                    <td>{{ (int) $product->units_sold }}</td>
                                    <td class="text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) $product->total_revenue, $appCurrencyCode) }}</td>
                                    <td class="text-violet-700 dark:text-violet-300">{{ $currencyFormatter::format((float) $product->total_profit, $appCurrencyCode) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showGymColumn ? 6 : 5 }}" class="py-8 text-center ui-muted">Aún no hay ventas registradas en este periodo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <x-ui.card title="Stock bajo o crítico" subtitle="Productos activos que ya llegaron al mínimo.">
                <div class="smart-list-wrap">
                    <table class="ui-table w-full min-w-[660px] text-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                @if ($showGymColumn)
                                    <th>Sede</th>
                                @endif
                                <th>Categoria</th>
                                <th>Stock</th>
                                <th>Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td class="font-semibold">{{ $product->name }}</td>
                                    @if ($showGymColumn)
                                        <td>{{ $product->gym_name ?? '-' }}</td>
                                    @endif
                                    <td>{{ $product->category ?: '-' }}</td>
                                    <td class="text-amber-700 dark:text-amber-300 font-bold">{{ (int) $product->stock }}</td>
                                    <td>{{ (int) $product->min_stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showGymColumn ? 5 : 4 }}" class="py-8 text-center ui-muted">No hay productos en alerta de stock.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </section>

        <x-ui.card title="Últimas ventas registradas" subtitle="Historial operativo reciente de productos vendidos.">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <p class="text-xs ui-muted">Esta vista muestra solo las últimas 12 ventas.</p>
                <x-ui.button type="button" id="open-sales-history-modal" variant="ghost" size="sm">Ver todas por fecha</x-ui.button>
            </div>

            <div class="smart-list-wrap">
                <table class="ui-table w-full min-w-[1120px] text-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            @if ($showGymColumn)
                                <th>Sede</th>
                            @endif
                            <th>Producto</th>
                            <th>Cliente</th>
                            <th>Usuario</th>
                            <th>Método</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Utilidad</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->sold_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                @if ($showGymColumn)
                                    <td>{{ $sale->gym?->name ?? '-' }}</td>
                                @endif
                                <td>
                                    <div class="font-semibold">{{ $sale->product?->name ?? '-' }}</div>
                                    <div class="ui-muted text-xs">{{ $sale->product?->category ?: 'Sin categoria' }}</div>
                                </td>
                                <td>{{ $sale->client?->full_name ?? 'Venta sin cliente' }}</td>
                                <td>{{ $sale->soldBy?->name ?? '-' }}</td>
                                <td>{{ $methodLabels[$sale->payment_method] ?? $sale->payment_method }}</td>
                                <td>{{ (int) $sale->quantity }}</td>
                                <td class="text-emerald-700 dark:text-emerald-300 font-bold">{{ $currencyFormatter::format((float) $sale->total_amount, $appCurrencyCode) }}</td>
                                <td class="text-violet-700 dark:text-violet-300 font-bold">{{ $currencyFormatter::format((float) $sale->total_profit, $appCurrencyCode) }}</td>
                                <td>{{ $sale->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showGymColumn ? 10 : 9 }}" class="py-8 text-center ui-muted">Todavía no hay ventas de productos registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <div id="sales-history-modal"
             data-auto-open="{{ $openSalesHistoryModal ? '1' : '0' }}"
             class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/80 p-4">
            <div class="w-full max-w-7xl max-h-[90vh] overflow-hidden rounded-3xl border border-cyan-500/35 bg-slate-950 shadow-2xl">
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-700/70 p-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.24em] text-cyan-300">Historial completo</p>
                        <h3 class="mt-2 text-2xl font-black text-slate-100">Ventas por día, mes y año</h3>
                        <p class="mt-1 text-sm text-slate-300">
                            Total encontrado: {{ (int) ($salesHistoryTotal ?? 0) }} venta{{ ((int) ($salesHistoryTotal ?? 0)) === 1 ? '' : 's' }}.
                            @if ($salesHistoryTruncated ?? false)
                                Mostrando las últimas 300 para mantener la vista rápida.
                            @endif
                        </p>
                    </div>
                    <button type="button" id="close-sales-history-modal" class="ui-button ui-button-ghost px-3 py-2 text-sm font-semibold">Cerrar</button>
                </div>

                <div class="space-y-4 p-5">
                    <form method="GET" action="{{ route('sales.index', ['contextGym' => $contextGym]) }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto_auto] md:items-end">
                        @if ($isGlobalScope)
                            <input type="hidden" name="scope" value="global">
                        @endif
                        <input type="hidden" name="sales_history_open" value="1">

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Ano</span>
                            <select name="sales_year" class="ui-input">
                                <option value="0">Todos</option>
                                @foreach (($salesHistoryYears ?? collect()) as $yearOption)
                                    <option value="{{ (int) $yearOption }}" @selected($selectedSalesYear === (int) $yearOption)>{{ (int) $yearOption }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Mes</span>
                            <select name="sales_month" class="ui-input">
                                <option value="0">Todos</option>
                                @foreach ($monthLabels as $monthValue => $monthName)
                                    <option value="{{ $monthValue }}" @selected($selectedSalesMonth === $monthValue)>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="space-y-1 text-sm font-semibold ui-muted">
                            <span>Dia</span>
                            <select name="sales_day" class="ui-input">
                                <option value="0">Todos</option>
                                @for ($dayOption = 1; $dayOption <= 31; $dayOption++)
                                    <option value="{{ $dayOption }}" @selected($selectedSalesDay === $dayOption)>{{ str_pad((string) $dayOption, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </label>

                        <x-ui.button type="submit" variant="secondary">Filtrar</x-ui.button>
                        <x-ui.button :href="route('sales.index', ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []) + ['sales_history_open' => 1])" variant="ghost">Limpiar</x-ui.button>
                    </form>

                    <div class="smart-list-wrap max-h-[56vh]">
                        <table class="ui-table w-full min-w-[1240px] text-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    @if ($showGymColumn)
                                        <th>Sede</th>
                                    @endif
                                    <th>Producto</th>
                                    <th>Cliente</th>
                                    <th>Usuario</th>
                                    <th>Método</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Utilidad</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse (($salesHistoryRows ?? collect()) as $sale)
                                    <tr>
                                        <td>{{ $sale->sold_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                        @if ($showGymColumn)
                                            <td>{{ $sale->gym?->name ?? '-' }}</td>
                                        @endif
                                        <td>
                                            <div class="font-semibold">{{ $sale->product?->name ?? '-' }}</div>
                                            <div class="ui-muted text-xs">{{ $sale->product?->category ?: 'Sin categoria' }}</div>
                                        </td>
                                        <td>{{ $sale->client?->full_name ?? 'Venta sin cliente' }}</td>
                                        <td>{{ $sale->soldBy?->name ?? '-' }}</td>
                                        <td>{{ $methodLabels[$sale->payment_method] ?? $sale->payment_method }}</td>
                                        <td>{{ (int) $sale->quantity }}</td>
                                        <td class="text-emerald-700 dark:text-emerald-300 font-bold">{{ $currencyFormatter::format((float) $sale->total_amount, $appCurrencyCode) }}</td>
                                        <td class="text-violet-700 dark:text-violet-300 font-bold">{{ $currencyFormatter::format((float) $sale->total_profit, $appCurrencyCode) }}</td>
                                        <td>{{ $sale->notes ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $showGymColumn ? 10 : 9 }}" class="py-8 text-center ui-muted">No hay ventas en ese filtro.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('sales_inventory.partials.remote-scanner-fab', [
        'scanContext' => 'sales',
        'contextGym' => $contextGym,
        'schemaReady' => $schemaReady,
        'isGlobalScope' => $isGlobalScope,
    ])
@endsection

@push('scripts')
<script>
    (function () {
        const products = @json($saleProductsPayload);
        const form = document.getElementById('sales-form');
        const scanInput = document.getElementById('sale-scan-input');
        const searchButton = document.getElementById('sale-scan-search');
        const select = document.getElementById('sale-product-select');
        const addSelectedButton = document.getElementById('sale-add-selected');
        const quantityInput = document.getElementById('sale-quantity-input');
        const feedback = document.getElementById('sale-scan-feedback');
        const preview = document.getElementById('sale-selected-preview');
        const previewName = document.getElementById('sale-preview-name');
        const previewCode = document.getElementById('sale-preview-code');
        const previewPrice = document.getElementById('sale-preview-price');
        const previewStock = document.getElementById('sale-preview-stock');
        const scanList = document.getElementById('sale-scan-list');
        const scanListItems = document.getElementById('sale-scan-list-items');
        const scanListSummary = document.getElementById('sale-scan-list-summary');
        const clearListButton = document.getElementById('sale-scan-list-clear');
        const saleItemsPayloadInput = document.getElementById('sale-items-payload');
        const lastCodeStorageKey = 'remote_scan_last_code_sales';
        const scanListStorageKey = 'sales_scan_list_v1';
        const clearSalesScanCartOnLoad = Boolean(@json($clearSalesScanCart));

        if (!scanInput || !select || !quantityInput) {
            return;
        }

        let autoSearchTimer = null;
        const scanListMap = new Map();
        const productsById = new Map();

        products.forEach(function (product) {
            productsById.set(Number(product.id), product);
        });

        function normalize(value) {
            return (value || '')
                .toString()
                .trim()
                .toUpperCase()
                .replace(/\s+/g, '');
        }

        function toNumber(value) {
            const parsed = Number(value);
            return Number.isFinite(parsed) ? parsed : 0;
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function setFeedback(text, tone) {
            if (!feedback) return;

            feedback.textContent = text;
            feedback.classList.remove('hidden', 'border-emerald-300', 'bg-emerald-50', 'text-emerald-800', 'border-rose-300', 'bg-rose-50', 'text-rose-800', 'border-cyan-300', 'bg-cyan-50', 'text-cyan-800');

            if (tone === 'success') {
                feedback.classList.add('border-emerald-300', 'bg-emerald-50', 'text-emerald-800');
            } else if (tone === 'error') {
                feedback.classList.add('border-rose-300', 'bg-rose-50', 'text-rose-800');
            } else {
                feedback.classList.add('border-cyan-300', 'bg-cyan-50', 'text-cyan-800');
            }
        }

        function clearFeedback() {
            if (!feedback) return;
            feedback.textContent = '';
            feedback.classList.add('hidden');
            feedback.classList.remove('border-emerald-300', 'bg-emerald-50', 'text-emerald-800', 'border-rose-300', 'bg-rose-50', 'text-rose-800', 'border-cyan-300', 'bg-cyan-50', 'text-cyan-800');
        }

        function serializeScanList() {
            const payload = [];

            scanListMap.forEach(function (item) {
                payload.push({
                    product_id: Number(item.product_id),
                    quantity: Number(item.quantity),
                });
            });

            return payload.length > 0 ? JSON.stringify(payload) : '';
        }

        function persistScanList() {
            const serialized = serializeScanList();

            if (saleItemsPayloadInput) {
                saleItemsPayloadInput.value = serialized;
            }

            try {
                if (serialized === '') {
                    window.sessionStorage.removeItem(scanListStorageKey);
                } else {
                    window.sessionStorage.setItem(scanListStorageKey, serialized);
                }
            } catch (error) {
                // Ignore storage failures.
            }
        }

        function renderScanList() {
            if (!scanList || !scanListItems || !scanListSummary) {
                persistScanList();
                return;
            }

            if (scanListMap.size === 0) {
                scanList.classList.add('hidden');
                scanListItems.innerHTML = '';
                scanListSummary.textContent = '';
                scanListItems.classList.remove('max-h-64', 'overflow-y-auto');
                renderPreview(getSelectedProduct());
                persistScanList();
                return;
            }

            let totalProducts = 0;
            let totalUnits = 0;
            let totalAmount = 0;
            const chunks = [];

            scanListMap.forEach(function (item) {
                const lineTotal = Number(item.quantity) * Number(item.sale_price);
                totalProducts += 1;
                totalUnits += Number(item.quantity);
                totalAmount += lineTotal;

                chunks.push(
                    '<article class="rounded-xl border border-slate-200 bg-white/80 p-2 dark:border-slate-700 dark:bg-slate-900/80">' +
                        '<div class="flex items-start justify-between gap-2">' +
                            '<div class="min-w-0">' +
                                '<p class="truncate text-sm font-black text-slate-900 dark:text-slate-100">' + escapeHtml(item.name) + '</p>' +
                                '<p class="text-[11px] ui-muted">SKU ' + escapeHtml(item.sku || '---') + ' | BAR ' + escapeHtml(item.barcode || '---') + '</p>' +
                            '</div>' +
                            '<div class="text-right">' +
                                '<p class="text-sm font-black text-emerald-700 dark:text-emerald-300">$' + Number(item.sale_price).toFixed(2) + '</p>' +
                                '<p class="text-[11px] ui-muted">Stock: ' + Number(item.stock) + '</p>' +
                            '</div>' +
                        '</div>' +
                        '<div class="mt-2 flex items-center gap-2">' +
                            '<button type="button" class="ui-button ui-button-ghost px-2 py-1 text-xs font-bold" data-scan-action="dec" data-product-id="' + Number(item.product_id) + '">-</button>' +
                            '<span class="min-w-[36px] text-center text-sm font-black text-slate-900 dark:text-slate-100">' + Number(item.quantity) + '</span>' +
                            '<button type="button" class="ui-button ui-button-ghost px-2 py-1 text-xs font-bold" data-scan-action="inc" data-product-id="' + Number(item.product_id) + '">+</button>' +
                            '<button type="button" class="ui-button ui-button-ghost px-2 py-1 text-xs font-semibold text-rose-700 dark:text-rose-300" data-scan-action="remove" data-product-id="' + Number(item.product_id) + '">Quitar</button>' +
                        '</div>' +
                    '</article>'
                );
            });

            scanList.classList.remove('hidden');
            scanListItems.classList.toggle('max-h-64', scanListMap.size > 0);
            scanListItems.classList.toggle('overflow-y-auto', scanListMap.size > 0);
            scanListItems.innerHTML = chunks.join('');
            scanListSummary.textContent = totalProducts + ' producto(s) | ' + totalUnits + ' unidad(es) | Total estimado $' + totalAmount.toFixed(2);
            if (preview) {
                preview.classList.add('hidden');
            }
            persistScanList();
        }

        function renderPreview(product) {
            if (!preview || !previewName || !previewCode || !previewPrice || !previewStock) return;

            if (!product) {
                preview.classList.add('hidden');
                return;
            }

            preview.classList.remove('hidden');
            previewName.textContent = product.name;
            previewCode.textContent = 'SKU ' + (product.sku || '---') + ' | BAR ' + (product.barcode || '---');
            previewPrice.textContent = '$' + Number(product.sale_price || 0).toFixed(2);
            previewStock.textContent = 'Stock disponible: ' + Number(product.stock || 0);
        }

        function findProductByCode(rawCode) {
            const code = normalize(rawCode);
            if (code === '') return null;

            return products.find(function (product) {
                return normalize(product.barcode) === code || normalize(product.sku) === code;
            }) || null;
        }

        function getSelectedProduct() {
            const productId = Number(select.value || 0);
            return products.find(function (product) {
                return Number(product.id) === productId;
            }) || null;
        }

        function addProductToScanList(product, quantityToAdd, options) {
            if (!product) return false;

            const settings = Object.assign({
                enforceStock: true,
            }, options || {});

            const productId = Number(product.id);
            const key = String(productId);
            const quantity = Math.max(1, Math.floor(toNumber(quantityToAdd)));
            const stock = Math.max(0, Math.floor(toNumber(product.stock)));
            const existing = scanListMap.get(key);
            const currentQuantity = existing ? Number(existing.quantity) : 0;
            let nextQuantity = currentQuantity + quantity;

            if (settings.enforceStock && stock <= 0) {
                setFeedback('El producto "' + product.name + '" no tiene stock disponible.', 'error');
                return false;
            }

            if (stock > 0 && nextQuantity > stock) {
                if (settings.enforceStock) {
                    setFeedback('Stock insuficiente para "' + product.name + '". Disponible: ' + stock + '.', 'error');
                    return false;
                }

                nextQuantity = stock;
            }

            scanListMap.set(key, {
                product_id: productId,
                quantity: nextQuantity,
                name: product.name || '',
                sku: product.sku || '',
                barcode: product.barcode || '',
                sale_price: toNumber(product.sale_price),
                stock: stock,
            });

            renderScanList();
            return true;
        }

        function updateScanListItem(productId, mode) {
            const key = String(Number(productId));
            const current = scanListMap.get(key);
            if (!current) return;

            if (mode === 'remove') {
                scanListMap.delete(key);
                renderScanList();
                return;
            }

            const stock = Math.max(0, Math.floor(toNumber(current.stock)));
            const delta = mode === 'inc' ? 1 : -1;
            const nextQuantity = Number(current.quantity) + delta;

            if (nextQuantity <= 0) {
                scanListMap.delete(key);
                renderScanList();
                return;
            }

            if (mode === 'inc' && stock > 0 && nextQuantity > stock) {
                setFeedback('No puedes superar el stock de "' + current.name + '".', 'error');
                return;
            }

            current.quantity = nextQuantity;
            scanListMap.set(key, current);
            renderScanList();
        }

        function clearScanList() {
            scanListMap.clear();
            renderScanList();
        }

        function resetRegisterModalState() {
            clearScanList();
            clearFeedback();

            if (scanInput) {
                scanInput.value = '';
            }

            if (select) {
                select.value = '';
            }

            if (quantityInput) {
                quantityInput.value = '1';
            }

            if (saleItemsPayloadInput) {
                saleItemsPayloadInput.value = '';
            }

            const methodSelect = form?.querySelector('select[name="payment_method"]');
            if (methodSelect) {
                methodSelect.value = 'cash';
            }

            const clientSelect = form?.querySelector('select[name="client_id"]');
            if (clientSelect) {
                clientSelect.value = '';
            }

            const notesInput = form?.querySelector('textarea[name="notes"]');
            if (notesInput) {
                notesInput.value = '';
            }

            try {
                window.sessionStorage.removeItem(scanListStorageKey);
                window.sessionStorage.removeItem(lastCodeStorageKey);
            } catch (error) {
                // Ignore storage failures.
            }

            renderPreview(null);
            renderScanList();
        }

        window.gymSalesResetRegisterModal = resetRegisterModalState;

        function addSelectedProductToList() {
            const product = getSelectedProduct();
            if (!product) {
                setFeedback('Selecciona un producto para agregar al carrito.', 'error');
                return;
            }

            const quantityToAdd = 1;
            if (!addProductToScanList(product, quantityToAdd, { enforceStock: true })) {
                return;
            }

            quantityInput.value = '1';
            setFeedback('Producto agregado al carrito: ' + product.name + '.', 'success');
            scanInput.focus();
        }

        function restoreScanListFromPayload(serialized) {
            const raw = (serialized || '').toString().trim();
            if (raw === '') {
                return;
            }

            let parsed = null;
            try {
                parsed = JSON.parse(raw);
            } catch (error) {
                return;
            }

            if (!Array.isArray(parsed)) {
                return;
            }

            parsed.forEach(function (row) {
                const productId = Number(row && row.product_id);
                const quantity = Math.max(1, Math.floor(toNumber(row && row.quantity)));
                if (!Number.isFinite(productId) || productId <= 0) {
                    return;
                }

                const product = productsById.get(productId);
                if (!product) {
                    return;
                }

                addProductToScanList(product, quantity, { enforceStock: false });
            });
        }

        function applyProduct(product) {
            if (!product) return;

            select.value = String(product.id);
            quantityInput.value = '1';

            renderPreview(product);
        }

        function resolveScan() {
            if (autoSearchTimer) {
                clearTimeout(autoSearchTimer);
                autoSearchTimer = null;
            }

            const product = findProductByCode(scanInput.value);

            if (!product) {
                setFeedback('No encontre un producto con ese SKU o codigo de barras.', 'error');
                return;
            }

            if (!addProductToScanList(product, 1, { enforceStock: true })) {
                return;
            }

            applyProduct(product);
            quantityInput.value = '1';
            setFeedback('Producto agregado a la lista: ' + product.name, 'success');
            scanInput.select();
            scanInput.focus();
        }

        searchButton?.addEventListener('click', resolveScan);

        scanInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                resolveScan();
            }
        });

        scanInput.addEventListener('input', function () {
            if (autoSearchTimer) {
                clearTimeout(autoSearchTimer);
            }

            if (normalize(scanInput.value).length === 0) {
                clearFeedback();
            }

            autoSearchTimer = window.setTimeout(function () {
                if (normalize(scanInput.value).length >= 6) {
                    resolveScan();
                }
            }, 220);
        });

        select.addEventListener('change', function () {
            const selectedProduct = getSelectedProduct();
            renderPreview(selectedProduct);

            if (!selectedProduct) {
                clearFeedback();
                return;
            }

            if (Number(selectedProduct.stock || 0) <= 0) {
                setFeedback('El producto "' + selectedProduct.name + '" no tiene stock disponible.', 'error');
                return;
            }

            clearFeedback();
        });

        addSelectedButton?.addEventListener('click', addSelectedProductToList);

        clearListButton?.addEventListener('click', function () {
            clearScanList();
            clearFeedback();
            setFeedback('Lista de escaneo limpia.', 'info');
        });

        scanListItems?.addEventListener('click', function (event) {
            const button = event.target.closest('button[data-scan-action]');
            if (!button) {
                return;
            }

            const action = (button.getAttribute('data-scan-action') || '').trim();
            const productId = Number(button.getAttribute('data-product-id') || 0);
            if (!Number.isFinite(productId) || productId <= 0) {
                return;
            }

            updateScanListItem(productId, action);
        });

        window.addEventListener('remote-scanner:scan', function (event) {
            const code = (event.detail?.code || '').toString().trim();
            if (code === '') {
                return;
            }

            scanInput.value = code;
            resolveScan();
            try {
                window.sessionStorage.setItem(lastCodeStorageKey, code);
            } catch (error) {
                // Ignore storage failures.
            }
        });

        if (clearSalesScanCartOnLoad) {
            try {
                window.sessionStorage.removeItem(scanListStorageKey);
                window.sessionStorage.removeItem(lastCodeStorageKey);
            } catch (error) {
                // Ignore storage failures.
            }

            if (saleItemsPayloadInput) {
                saleItemsPayloadInput.value = '';
            }
            scanListMap.clear();
            clearFeedback();
        }

        const hiddenPayload = (saleItemsPayloadInput?.value || '').toString().trim();
        if (hiddenPayload !== '') {
            restoreScanListFromPayload(hiddenPayload);
        } else {
            try {
                const storedList = window.sessionStorage.getItem(scanListStorageKey);
                if (storedList && storedList.trim() !== '') {
                    restoreScanListFromPayload(storedList);
                }
            } catch (error) {
                // Ignore storage failures.
            }
        }

        try {
            const savedCode = window.sessionStorage.getItem(lastCodeStorageKey);
            if (scanListMap.size === 0 && savedCode && savedCode.trim() !== '' && (scanInput.value || '').trim() === '') {
                scanInput.value = savedCode;
                resolveScan();
            }
        } catch (error) {
            // Ignore storage failures.
        }

        form?.addEventListener('submit', function () {
            const serialized = serializeScanList();
            if (saleItemsPayloadInput) {
                saleItemsPayloadInput.value = serialized;
            }

            if (serialized !== '' && !select.value) {
                const firstItem = scanListMap.values().next().value;
                if (firstItem) {
                    select.value = String(firstItem.product_id);
                }
            }

            if (serialized !== '' && (!quantityInput.value || Number(quantityInput.value) < 1)) {
                quantityInput.value = '1';
            }
        });

        renderPreview(getSelectedProduct());
        renderScanList();
    })();
</script>
@endpush

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('sales-register-modal');
        const openButton = document.getElementById('open-sales-register-modal');
        const closeButton = document.getElementById('close-sales-register-modal');

        if (!modal || !openButton) {
            return;
        }

        function openModal(options) {
            const settings = Object.assign({ reset: false }, options || {});
            if (settings.reset && typeof window.gymSalesResetRegisterModal === 'function') {
                window.gymSalesResetRegisterModal();
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            const scanInput = document.getElementById('sale-scan-input');
            scanInput?.focus();
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        openButton.addEventListener('click', function () {
            openModal({ reset: true });
        });

        closeButton?.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('flex')) {
                closeModal();
            }
        });

        if (modal.dataset.autoOpen === '1') {
            openModal();
        }
    })();
</script>
@endpush

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('sales-history-modal');
        const openButton = document.getElementById('open-sales-history-modal');
        const closeButton = document.getElementById('close-sales-history-modal');

        if (!modal || !openButton) {
            return;
        }

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        openButton.addEventListener('click', openModal);
        closeButton?.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('flex')) {
                closeModal();
            }
        });

        if (modal.dataset.autoOpen === '1') {
            openModal();
        }
    })();
</script>
@endpush
