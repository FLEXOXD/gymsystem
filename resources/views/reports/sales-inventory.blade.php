@extends('layouts.panel')

@section('title', 'Reporte de ventas e inventario')
@section('page-title', 'Reporte de ventas e inventario')

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $contextGym = (string) request()->route('contextGym');
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $chartLabels = collect($salesByDay ?? [])->map(fn ($row) => \Carbon\Carbon::parse((string) $row->date)->format('Y-m-d'))->values();
        $chartRevenue = collect($salesByDay ?? [])->map(fn ($row) => round((float) $row->total_revenue, 2))->values();
        $chartProfit = collect($salesByDay ?? [])->map(fn ($row) => round((float) $row->total_profit, 2))->values();
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];
        $routeParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
    @endphp

    <div class="space-y-4">
        @if (! $schemaReady)
            <div class="ui-alert ui-alert-warning">Falta ejecutar <code>php artisan migrate</code> para habilitar el reporte de ventas e inventario.</div>
        @endif

        <x-ui.card title="Filtro del modulo" subtitle="Lee rendimiento comercial y rotacion de inventario por periodo.">
            <form method="GET" action="{{ route('reports.sales-inventory', ['contextGym' => $contextGym]) }}" class="grid gap-3 md:grid-cols-4 md:items-end">
                @if ($isGlobalScope)
                    <input type="hidden" name="scope" value="global">
                @endif
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Desde</span>
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Hasta</span>
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="ui-input">
                </label>

                <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>

                <div class="flex flex-wrap gap-2">
                    <x-ui.button :href="route('reports.index', ['contextGym' => $contextGym] + request()->query())" variant="ghost">Panel reportes</x-ui.button>
                    <x-ui.button :href="route('sales.index', $routeParams)" variant="ghost">Volver al modulo</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Ventas</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($salesSummary['total_sales'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">{{ (int) ($salesSummary['units_sold'] ?? 0) }} unidades vendidas</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Ingreso total</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) ($salesSummary['total_revenue'] ?? 0), $appCurrencyCode) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Ticket promedio {{ $currencyFormatter::format((float) ($salesSummary['average_ticket'] ?? 0), $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Costo total</p>
                <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-300">{{ $currencyFormatter::format((float) ($salesSummary['total_cost'] ?? 0), $appCurrencyCode) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Base para utilidad real</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Utilidad</p>
                <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-300">{{ $currencyFormatter::format((float) ($salesSummary['total_profit'] ?? 0), $appCurrencyCode) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Ingreso menos costo</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos de stock</p>
                <p class="mt-2 text-3xl font-black text-cyan-700 dark:text-cyan-300">{{ (int) ($inventorySummary['movement_count'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Rotacion del periodo</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Unidades que entraron</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ (int) ($inventorySummary['units_in'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Reposicion y carga inicial</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Unidades que salieron</p>
                <p class="mt-2 text-3xl font-black text-rose-700 dark:text-rose-300">{{ (int) ($inventorySummary['units_out'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Ventas y ajustes negativos</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Ajustes manuales</p>
                <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-300">{{ (int) ($inventorySummary['manual_adjustments'] ?? 0) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Correcciones de inventario</p>
            </x-ui.card>
        </section>

        <section class="grid gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)]">
            <x-ui.card title="Comportamiento diario" subtitle="Ingreso y utilidad generados por ventas de productos.">
                <canvas id="salesInventoryChart" height="120"></canvas>
            </x-ui.card>

            <x-ui.card title="Atajos del modulo" subtitle="Accesos directos para operacion rapida.">
                <div class="space-y-3">
                    <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Panel comercial</p>
                        <p class="mt-1 text-sm text-cyan-800 dark:text-cyan-100">Vuelve al centro operativo para registrar ventas nuevas.</p>
                        <div class="mt-3">
                            <x-ui.button :href="route('sales.index', $routeParams)" variant="secondary">Abrir ventas e inventario</x-ui.button>
                        </div>
                    </article>
                    <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Productos</p>
                        <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-100">Ajusta stock, precios y catalogo sin mezclarlo con clientes.</p>
                        <div class="mt-3">
                            <x-ui.button :href="route('products.index', $routeParams)" variant="ghost">Ir a productos</x-ui.button>
                        </div>
                    </article>
                    <article class="rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Alertas activas</p>
                        <p class="mt-2 text-2xl font-black text-violet-800 dark:text-violet-100">{{ $lowStockProducts->count() }}</p>
                        <p class="text-xs text-violet-700 dark:text-violet-200">Productos activos en stock bajo.</p>
                    </article>
                </div>
            </x-ui.card>
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            <x-ui.card title="Top productos" subtitle="Articulos con mejor salida y mejor ingreso del periodo.">
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
                                    <td colspan="{{ $showGymColumn ? 6 : 5 }}" class="py-8 text-center ui-muted">No hay ventas de productos en este rango.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <x-ui.card title="Stock bajo" subtitle="Productos activos que necesitan reposicion.">
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
                                <th>Minimo</th>
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
                                    <td colspan="{{ $showGymColumn ? 5 : 4 }}" class="py-8 text-center ui-muted">No hay alertas de stock bajo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </section>

        <x-ui.card title="Detalle de ventas del periodo">
            @if ($recentSales)
                <div class="smart-list-wrap">
                    <table class="ui-table w-full min-w-[1180px] text-sm">
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
                                <th>Costo</th>
                                <th>Utilidad</th>
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
                                    <td class="text-rose-700 dark:text-rose-300 font-bold">{{ $currencyFormatter::format((float) $sale->total_cost, $appCurrencyCode) }}</td>
                                    <td class="text-violet-700 dark:text-violet-300 font-bold">{{ $currencyFormatter::format((float) $sale->total_profit, $appCurrencyCode) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showGymColumn ? 10 : 9 }}" class="py-8 text-center ui-muted">No hay ventas dentro del rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $recentSales->links() }}
                </div>
            @else
                <p class="ui-muted">El detalle estara disponible despues de habilitar las tablas del modulo.</p>
            @endif
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const chartEl = document.getElementById('salesInventoryChart');
        if (!chartEl) return;

        const labels = @json($chartLabels);
        const revenue = @json($chartRevenue);
        const profit = @json($chartProfit);

        new Chart(chartEl, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ingreso',
                        data: revenue,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        fill: true,
                        tension: 0.28,
                    },
                    {
                        label: 'Utilidad',
                        data: profit,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.08)',
                        fill: true,
                        tension: 0.28,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    })();
</script>
@endpush
