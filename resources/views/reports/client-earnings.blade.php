@extends('layouts.panel')

@section('title', 'Ganancias de clientes')
@section('page-title', 'Ganancias de clientes')

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $contextGym = (string) request()->route('contextGym');
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $panelParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $baseRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $sourceFilter = (string) ($filters['source'] ?? 'all');
        $orderFilter = (string) ($filters['order'] ?? 'amount_desc');
        $userFilter = isset($filters['user_id']) ? (int) $filters['user_id'] : null;
    @endphp

    <div class="space-y-4">
        <x-ui.card title="Filtro de facturacion por cliente" subtitle="Analiza cuanto se ha facturado por cliente en el rango seleccionado.">
            <form method="GET" action="{{ route('reports.client-earnings', ['contextGym' => $contextGym]) }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
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

                <label class="space-y-1 text-sm font-semibold ui-muted xl:col-span-2">
                    <span>Cliente o documento</span>
                    <input type="text"
                           name="search"
                           value="{{ (string) ($filters['search'] ?? '') }}"
                           class="ui-input"
                           placeholder="Nombre, apellido o documento">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Usuario</span>
                    <select name="user_id" class="ui-input">
                        <option value="">Todos</option>
                        @foreach ($users as $user)
                            <option value="{{ (int) $user->id }}" @selected($userFilter === (int) $user->id)>{{ (string) ($user->name ?? 'Sin nombre') }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Origen</span>
                    <select name="source" class="ui-input">
                        <option value="all" @selected($sourceFilter === 'all')>Todo</option>
                        <option value="membership" @selected($sourceFilter === 'membership')>Solo membresias</option>
                        <option value="sale" @selected($sourceFilter === 'sale')>Solo ventas de productos</option>
                        <option value="mixed" @selected($sourceFilter === 'mixed')>Clientes mixtos</option>
                    </select>
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Orden</span>
                    <select name="order" class="ui-input">
                        <option value="amount_desc" @selected($orderFilter === 'amount_desc')>Mayor facturacion</option>
                        <option value="amount_asc" @selected($orderFilter === 'amount_asc')>Menor facturacion</option>
                        <option value="last_desc" @selected($orderFilter === 'last_desc')>Ultima facturacion reciente</option>
                        <option value="last_asc" @selected($orderFilter === 'last_asc')>Ultima facturacion antigua</option>
                        <option value="name_asc" @selected($orderFilter === 'name_asc')>Nombre A-Z</option>
                        <option value="name_desc" @selected($orderFilter === 'name_desc')>Nombre Z-A</option>
                    </select>
                </label>

                <div class="xl:col-span-5 flex flex-wrap gap-2 items-end">
                    <x-ui.button type="submit" variant="secondary">Aplicar</x-ui.button>
                    <x-ui.button :href="route('reports.index', $panelParams)" variant="ghost">Panel reportes</x-ui.button>
                    <x-ui.button :href="route('panel.index', $baseRouteParams)" variant="ghost">Volver al panel</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Clientes facturados</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($summary['billed_clients'] ?? 0) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Total facturado</p>
                <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) ($summary['total_billed'] ?? 0), $appCurrencyCode) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Operaciones</p>
                <p class="mt-2 text-3xl font-black text-cyan-700 dark:text-cyan-300">{{ (int) ($summary['operations_count'] ?? 0) }}</p>
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Promedio por cliente</p>
                <p class="mt-2 text-3xl font-black text-violet-700 dark:text-violet-300">{{ $currencyFormatter::format((float) ($summary['average_per_client'] ?? 0), $appCurrencyCode) }}</p>
            </x-ui.card>
        </section>

        <x-ui.card title="Cliente con mayor facturacion">
            @if ($topClient)
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                    <div>
                        <p class="text-lg font-black">{{ (string) ($topClient->client_name ?? 'Cliente') }}</p>
                        <p class="text-sm ui-muted">Documento: {{ (string) ($topClient->document_number ?? '-') }}</p>
                        @if ($showGymColumn)
                            <p class="text-sm ui-muted">Sede: {{ (string) ($topClient->gym_name ?? '-') }}</p>
                        @endif
                        <p class="text-sm ui-muted">Ultima facturacion: {{ $topClient->last_billed_at ? \Carbon\Carbon::parse((string) $topClient->last_billed_at)->format('Y-m-d H:i') : '-' }}</p>
                    </div>
                    <x-ui.badge variant="success">{{ $currencyFormatter::format((float) ($topClient->total_billed ?? 0), $appCurrencyCode) }}</x-ui.badge>
                </div>
            @else
                <p class="ui-muted">No hay clientes facturados con los filtros actuales.</p>
            @endif
        </x-ui.card>

        <x-ui.card title="Detalle por cliente">
            <p class="mb-3 text-sm ui-muted">
                Mostrando
                <strong>{{ $clients->firstItem() ?? 0 }}</strong>
                a
                <strong>{{ $clients->lastItem() ?? 0 }}</strong>
                de
                <strong>{{ $clients->total() }}</strong>
                registros (50 por pagina).
            </p>

            <div class="smart-list-wrap">
                <table class="ui-table w-full min-w-[1080px] text-sm">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Documento</th>
                            @if ($showGymColumn)
                                <th>Sede</th>
                            @endif
                            <th>Total facturado</th>
                            <th>Membresias</th>
                            <th>Ventas de productos</th>
                            <th>Operaciones</th>
                            <th>Ultima facturacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td class="font-semibold">{{ (string) ($client->client_name ?? '-') }}</td>
                                <td>{{ (string) ($client->document_number ?? '-') }}</td>
                                @if ($showGymColumn)
                                    <td>{{ (string) ($client->gym_name ?? '-') }}</td>
                                @endif
                                <td class="font-bold text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) ($client->total_billed ?? 0), $appCurrencyCode) }}</td>
                                <td>{{ $currencyFormatter::format((float) ($client->memberships_billed ?? 0), $appCurrencyCode) }}</td>
                                <td>{{ $currencyFormatter::format((float) ($client->sales_billed ?? 0), $appCurrencyCode) }}</td>
                                <td>{{ (int) ($client->operations_count ?? 0) }}</td>
                                <td>{{ $client->last_billed_at ? \Carbon\Carbon::parse((string) $client->last_billed_at)->format('Y-m-d H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showGymColumn ? 8 : 7 }}" class="py-8 text-center ui-muted">No hay datos para el rango y filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $clients->links() }}
            </div>
        </x-ui.card>
    </div>
@endsection
