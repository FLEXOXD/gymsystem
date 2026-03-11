
@extends('layouts.panel')

@section('title', 'Panel de control')
@section('page-title', 'Panel de control')

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $monthCurrentLabel = now()->format('M Y');
        $monthPreviousLabel = now()->subMonthNoOverflow()->format('M Y');
        $monthlyBarsMax = max(1, (float) collect($incomeLast6Months)->max('income'));
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isCashierScoped = (bool) ($isCashierScoped ?? false);
        $panelSessionSummary = $openSessionScopedSummary ?? [
            'opening_balance' => 0,
            'income_total' => 0,
            'expense_total' => 0,
            'net_total' => 0,
            'visible_total' => 0,
            'movements_count' => 0,
        ];
        $clientShowUrl = static fn (int $clientId): string => route('clients.show', ['client' => $clientId] + ($isGlobalScope ? ['scope' => 'global'] : []));
    @endphp

    <section class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px] xl:items-start">
    <div class="space-y-4">
    <x-ui.card id="tour-panel-summary" title="Resumen del día" subtitle="Indicadores clave para tomar decisiones rápidas.">
        @if ($isCashierScoped)
            <p class="mb-4 ui-alert ui-alert-info">Vista privada: aqui solo ves tus cobros, movimientos y acumulados del mes actual.</p>
        @endif
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-slate-500 dark:text-slate-300">Clientes</p>
                <p class="mt-1 text-2xl font-black leading-none text-slate-900 dark:text-slate-100">{{ $totalClients }}</p>
                <p class="min-h-[16px] text-xs text-slate-500 dark:text-slate-300">Base registrada</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-emerald-700 dark:text-emerald-200">Membresías activas</p>
                <p class="mt-1 text-2xl font-black leading-none text-emerald-800 dark:text-emerald-100">{{ $activeMemberships }}</p>
                <p class="min-h-[16px] text-xs text-emerald-700 dark:text-emerald-200">Vigentes hoy</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-amber-700 dark:text-amber-200">Por vencer</p>
                <p class="mt-1 text-2xl font-black leading-none text-amber-800 dark:text-amber-100">{{ $expiringSoonMemberships }}</p>
                <p class="min-h-[16px] text-xs text-amber-700 dark:text-amber-200">Próximas 48 horas</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-rose-700 dark:text-rose-200">Vencid@s</p>
                <p class="mt-1 text-2xl font-black leading-none text-rose-800 dark:text-rose-100">{{ $expiredMemberships }}</p>
                <p class="min-h-[16px] text-xs text-rose-700 dark:text-rose-200">Requieren renovación</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-cyan-700 dark:text-cyan-200">Check-ins hoy</p>
                <p class="mt-1 text-2xl font-black leading-none text-cyan-800 dark:text-cyan-100">{{ (int) $checkinsToday }}</p>
                <p class="min-h-[16px] text-xs text-cyan-700 dark:text-cyan-200">Se reinicia 12:00 AM</p>
            </article>
            <article class="flex min-h-[120px] flex-col justify-between rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                <p class="min-h-[28px] text-xs font-bold uppercase leading-tight tracking-wider text-violet-700 dark:text-violet-200">Planes activos</p>
                <p class="mt-1 text-2xl font-black leading-none text-violet-800 dark:text-violet-100">{{ $activePlans }}</p>
                <p class="min-h-[16px] text-xs text-violet-700 dark:text-violet-200">Oferta vigente</p>
            </article>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-ui.button :href="route('reception.index')" variant="primary">Ir a recepción</x-ui.button>
            <x-ui.button id="tour-panel-go-clients" :href="route('clients.index')" variant="secondary">Gestionar clientes</x-ui.button>
            <x-ui.button :href="route('plans.index')" variant="ghost">Ver planes</x-ui.button>
            <x-ui.button :href="route('cash.index')" variant="ghost">Ir a caja</x-ui.button>
        </div>
    </x-ui.card>

    <section class="grid gap-4 xl:grid-cols-3">
        <x-ui.card title="{{ $isCashierScoped ? 'Comparativo mensual personal' : 'Comparativo mensual' }}" subtitle="{{ $isCashierScoped ? 'Compara tus cobros del mes actual contra tu mes anterior.' : 'Si las ventas van mejor o peor vs el mes anterior.' }}" class="xl:col-span-2">
            <div class="grid gap-3 md:grid-cols-3">
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">{{ $monthCurrentLabel }}</p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ $currencyFormatter::format((float) $incomeCurrentMonth, $appCurrencyCode) }}</p>
                    <p class="text-xs text-cyan-700 dark:text-cyan-200">{{ $isCashierScoped ? 'Tus ingresos del mes' : 'Ingresos del mes' }}</p>
                </article>
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">{{ $monthPreviousLabel }}</p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ $currencyFormatter::format((float) $incomePreviousMonth, $appCurrencyCode) }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-300">{{ $isCashierScoped ? 'Tu mes anterior' : 'Mes anterior' }}</p>
                </article>
                <article class="rounded-xl border p-3 {{ $monthlyIncomeDiff >= 0 ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15' }}">
                    <p class="text-xs font-bold uppercase tracking-wider {{ $monthlyIncomeDiff >= 0 ? 'text-emerald-700 dark:text-emerald-200' : 'text-rose-700 dark:text-rose-200' }}">{{ $isCashierScoped ? 'Variación personal' : 'Variación' }}</p>
                    <p class="mt-1 text-2xl font-black {{ $monthlyIncomeDiff >= 0 ? 'text-emerald-800 dark:text-emerald-100' : 'text-rose-800 dark:text-rose-100' }}">
                        {{ $monthlyIncomeDiff >= 0 ? '+' : '' }}{{ $currencyFormatter::format((float) $monthlyIncomeDiff, $appCurrencyCode, true) }}
                    </p>
                    <p class="text-xs {{ $monthlyIncomeDiff >= 0 ? 'text-emerald-700 dark:text-emerald-200' : 'text-rose-700 dark:text-rose-200' }}">
                        @if ($monthlyIncomePct !== null)
                            {{ $monthlyIncomePct >= 0 ? '+' : '' }}{{ number_format((float) $monthlyIncomePct, 1) }}%
                        @else
                            Sin base de comparación
                        @endif
                    </p>
                </article>
            </div>

            <div class="mt-4 space-y-2">
                @foreach ($incomeLast6Months as $row)
                    @php
                        $barWidth = min(100, max(6, ($row['income'] / $monthlyBarsMax) * 100));
                    @endphp
                    <div class="grid grid-cols-[68px_1fr_88px] items-center gap-2 text-xs sm:grid-cols-[84px_1fr_120px]">
                        <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $row['label'] }}</span>
                        <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-2 rounded-full bg-cyan-500 dark:bg-cyan-400" style="width: {{ number_format($barWidth, 2, '.', '') }}%;"></div>
                        </div>
                        <span class="text-right font-semibold text-slate-700 dark:text-slate-200">{{ $currencyFormatter::format((float) $row['income'], $appCurrencyCode, true) }}</span>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card title="{{ $isCashierScoped ? 'Tu producción de hoy' : 'Caja y ventas hoy' }}" class="xl:col-span-1">
            <div class="space-y-3">
                <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">{{ $isCashierScoped ? 'Tus ingresos hoy' : 'Ingresos hoy' }}</p>
                    <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100">{{ $currencyFormatter::format((float) $incomeToday, $appCurrencyCode) }}</p>
                </article>
                <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">{{ $isCashierScoped ? 'Tus egresos hoy' : 'Egresos hoy' }}</p>
                    <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100">{{ $currencyFormatter::format((float) $expenseToday, $appCurrencyCode) }}</p>
                </article>
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">{{ $isCashierScoped ? 'Tu balance hoy' : 'Balance hoy' }}</p>
                    <p class="mt-1 text-2xl font-black {{ (float) $todayBalance >= 0 ? 'text-cyan-800 dark:text-cyan-100' : 'text-rose-800 dark:text-rose-100' }}">{{ $currencyFormatter::format((float) $todayBalance, $appCurrencyCode) }}</p>
                </article>
                <article class="rounded-xl border p-3 {{ (float) $netYearToDate >= 0 ? 'border-violet-200 bg-violet-50 dark:border-violet-400/40 dark:bg-violet-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15' }}">
                    <p class="text-xs font-bold uppercase tracking-wider {{ (float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200' }}">{{ $isCashierScoped ? 'Tu acumulado del año' : 'Ganancia del año' }}</p>
                    <p class="mt-1 text-2xl font-black {{ (float) $netYearToDate >= 0 ? 'text-violet-800 dark:text-violet-100' : 'text-rose-800 dark:text-rose-100' }}">{{ $currencyFormatter::format((float) $netYearToDate, $appCurrencyCode) }}</p>
                    <p class="text-xs {{ (float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200' }}">{{ $isCashierScoped ? 'Tus ingresos menos egresos en el año' : 'Ingresos - egresos acumulados del año' }}</p>
                </article>
            </div>
        </x-ui.card>
    </section>

    <x-ui.card title="{{ $isCashierScoped ? 'Tu actividad de caja actual' : 'Estado de caja actual' }}" subtitle="{{ $isCashierScoped ? 'Resumen privado de tus movimientos dentro del turno activo.' : 'Control rápido del turno activo.' }}">
        @if ($isGlobalScope)
            <p class="ui-alert ui-alert-info">Modo global activo: esta vista consolida sedes y no permite abrir o cerrar turnos desde el panel.</p>
            <div class="mt-3">
                <x-ui.button :href="route('cash.index')" variant="secondary">Ver consolidado de caja</x-ui.button>
            </div>
        @elseif ($openSession)
            @if ($isCashierScoped)
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Turno</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">#{{ $openSession->id }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Tus registros dentro del turno</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">{{ $currencyFormatter::format((float) ($panelSessionSummary['opening_balance'] ?? 0), $appCurrencyCode) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Monto inicial del turno</p>
                    </article>
                    <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Tus ingresos</p>
                        <p class="mt-1 text-xl font-black text-emerald-800 dark:text-emerald-100">{{ $currencyFormatter::format((float) ($panelSessionSummary['income_total'] ?? 0), $appCurrencyCode) }}</p>
                        <p class="text-xs text-emerald-700 dark:text-emerald-200">Cobros del turno activo</p>
                    </article>
                    <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Tus egresos</p>
                        <p class="mt-1 text-xl font-black text-rose-800 dark:text-rose-100">{{ $currencyFormatter::format((float) ($panelSessionSummary['expense_total'] ?? 0), $appCurrencyCode) }}</p>
                        <p class="text-xs text-rose-700 dark:text-rose-200">Salidas registradas por ti</p>
                    </article>
                    <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Saldo visible</p>
                        <p class="mt-1 text-xl font-black text-cyan-800 dark:text-cyan-100">{{ $currencyFormatter::format((float) ($panelSessionSummary['visible_total'] ?? 0), $appCurrencyCode) }}</p>
                        <p class="text-xs text-cyan-700 dark:text-cyan-200">Apertura + tus movimientos</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Tus movimientos</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($panelSessionSummary['movements_count'] ?? 0) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Registrados por tu usuario</p>
                    </article>
                </div>
            @else
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Turno</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">#{{ $openSession->id }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Abierto</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">{{ $currencyFormatter::format((float) $openSession->opening_balance, $appCurrencyCode) }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">{{ $openSession->opened_at?->format('Y-m-d H:i') }}</p>
                    </article>
                    <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Esperado actual</p>
                        <p class="mt-1 text-xl font-black text-cyan-800 dark:text-cyan-100">{{ $currencyFormatter::format((float) ($openSessionExpected ?? 0), $appCurrencyCode) }}</p>
                        <p class="text-xs text-cyan-700 dark:text-cyan-200">Caja operando</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Abierta por</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">{{ $openSession->openedBy?->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Usuario responsable</p>
                    </article>
                </div>
            @endif
            <div class="mt-3">
                <x-ui.button :href="route('cash.index')" variant="secondary">{{ $isCashierScoped ? 'Ir a tu caja' : 'Ir a caja por turno' }}</x-ui.button>
            </div>
        @else
            <p class="ui-alert ui-alert-warning">No hay turno de caja abierto ahora mismo.</p>
            <div class="mt-3">
                <x-ui.button :href="route('cash.index')" variant="primary">Abrir caja</x-ui.button>
            </div>
        @endif
    </x-ui.card>
    </div>

    <div class="space-y-4">
        <x-ui.card id="tour-panel-tracking" title="Centro de seguimiento" subtitle="Abre detalle en modal para evitar saturar la pantalla.">
            <div class="grid gap-3">
                <article class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200">Renovaciones 48h</p>
                    <p class="mt-1 text-2xl font-black text-amber-800 dark:text-amber-100">{{ $upcomingRenewals->count() }}</p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-renewals">Ver detalle</button>
                </article>
                <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Check-ins de hoy</p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ (int) $checkinsToday }}</p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-checkins">Ver detalle</button>
                </article>
                <article class="rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">{{ $isCashierScoped ? 'Tus movimientos hoy' : 'Movimientos de hoy' }}</p>
                    <p class="mt-1 text-2xl font-black text-violet-800 dark:text-violet-100">{{ $movementsTodayCount }}</p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-movements">Ver detalle</button>
                </article>
            </div>
        </x-ui.card>

        <x-ui.card title="Renovar vencid@s" subtitle="Acciones rápidas para clientes con membresía vencida.">
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Total vencid@s</p>
                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100">{{ $expiredMemberships }}</p>
            </div>

            @if ($expiredRenewalCandidates->isNotEmpty())
                <div class="mt-3 space-y-2">
                    @foreach ($expiredRenewalCandidates->take(4) as $expiredClient)
                        @php
                            $expiredLabel = $expiredClient->days_expired === null
                                ? 'Sin fecha'
                                : ($expiredClient->days_expired <= 0 ? 'Hoy' : ($expiredClient->days_expired === 1 ? 'Hace 1 día' : 'Hace '.$expiredClient->days_expired.' días'));
                        @endphp
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200/80 bg-slate-50/80 p-2 dark:border-slate-700 dark:bg-slate-900/70">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $expiredClient->client_name }}</p>
                                @if ($isGlobalScope)
                                    <p class="mt-0.5 text-[11px] font-semibold text-cyan-700 dark:text-cyan-300">{{ $expiredClient->gym_name ?? '-' }}</p>
                                @endif
                                <p class="truncate text-xs text-slate-600 dark:text-slate-300">{{ $expiredClient->plan_name }} · {{ $expiredLabel }}</p>
                            </div>
                            <x-ui.button :href="$clientShowUrl((int) $expiredClient->client_id)" size="sm" variant="{{ $isGlobalScope ? 'ghost' : 'secondary' }}">{{ $isGlobalScope ? 'Ver' : 'Renovar' }}</x-ui.button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-800 dark:border-emerald-400/40 dark:bg-emerald-500/15 dark:text-emerald-200">
                    No hay vencid@s por renovar.
                </p>
            @endif

            <div class="mt-3 flex flex-wrap gap-2">
                <x-ui.button :href="route('clients.index', ['filter' => 'expired'])" variant="ghost" size="sm">Ver listado vencid@s</x-ui.button>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-expired-renewals">Ver detalle</button>
            </div>
        </x-ui.card>
    </div>
    </section>

    <div id="modal-renewals" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalRenewalsTitle">
        <div class="ui-modal-panel max-w-5xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalRenewalsTitle" class="ui-heading text-lg">Próximas renovaciones (48 horas)</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[760px]">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        @if ($isGlobalScope)
                            <th>Sede</th>
                        @endif
                        <th>Plan</th>
                        <th>Vence</th>
                        <th>Días</th>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($upcomingRenewals as $membership)
                        @php
                            $daysLeft = (int) ($membership->days_left ?? 0);
                            $daysLabel = $daysLeft <= 0 ? 'Hoy' : ($daysLeft === 1 ? '1 día' : $daysLeft.' días');
                        @endphp
                        <tr>
                            <td>{{ $membership->client_name }}</td>
                            @if ($isGlobalScope)
                                <td>{{ $membership->gym_name ?? '-' }}</td>
                            @endif
                            <td>{{ $membership->plan_name }}</td>
                            <td>{{ $membership->ends_at?->format('Y-m-d') ?? '-' }}</td>
                            <td><x-ui.badge :variant="$daysLeft <= 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info')">{{ $daysLabel }}</x-ui.badge></td>
                            <td class="text-right"><x-ui.button :href="$clientShowUrl((int) $membership->client_id)" size="sm" variant="ghost">Ver cliente</x-ui.button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGlobalScope ? 6 : 5 }}" class="text-center text-sm text-slate-500 dark:text-slate-300">Sin renovaciones en las próximas 48 horas.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-expired-renewals" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalExpiredRenewalsTitle">
        <div class="ui-modal-panel max-w-5xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalExpiredRenewalsTitle" class="ui-heading text-lg">Renovar vencid@s</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[760px]">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        @if ($isGlobalScope)
                            <th>Sede</th>
                        @endif
                        <th>Plan</th>
                        <th>Vencio</th>
                        <th>Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($expiredRenewalCandidates as $expiredClient)
                        @php
                            $expiredStatusLabel = $expiredClient->membership_status === 'cancelled'
                                ? 'Cancelada'
                                : ($expiredClient->days_expired === null ? 'Sin fecha' : ($expiredClient->days_expired <= 0 ? 'Hoy' : ($expiredClient->days_expired === 1 ? 'Hace 1 día' : 'Hace '.$expiredClient->days_expired.' días')));
                        @endphp
                        <tr>
                            <td>{{ $expiredClient->client_name }}</td>
                            @if ($isGlobalScope)
                                <td>{{ $expiredClient->gym_name ?? '-' }}</td>
                            @endif
                            <td>{{ $expiredClient->plan_name }}</td>
                            <td>{{ $expiredClient->ends_at?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                <x-ui.badge :variant="$expiredClient->membership_status === 'cancelled' ? 'warning' : 'danger'">
                                    {{ $expiredStatusLabel }}
                                </x-ui.badge>
                            </td>
                            <td class="text-right">
                                <x-ui.button :href="$clientShowUrl((int) $expiredClient->client_id)" size="sm" variant="{{ $isGlobalScope ? 'ghost' : 'secondary' }}">{{ $isGlobalScope ? 'Ver' : 'Renovar' }}</x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGlobalScope ? 6 : 5 }}" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay clientes vencid@s para renovar.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 flex justify-end">
                <x-ui.button :href="route('clients.index', ['filter' => 'expired'])" variant="ghost" size="sm">Ir a clientes vencid@s</x-ui.button>
            </div>
        </div>
    </div>

    <div id="modal-checkins" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalCheckinsTitle">
        <div class="ui-modal-panel max-w-4xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalCheckinsTitle" class="ui-heading text-lg">Check-ins de hoy</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[640px]">
                    <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Cliente</th>
                        @if ($isGlobalScope)
                            <th>Sede</th>
                        @endif
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($todayAttendances as $attendance)
                        <tr>
                            <td>{{ $attendance->time }}</td>
                            <td>{{ $attendance->client?->full_name ?? '-' }}</td>
                            @if ($isGlobalScope)
                                <td>{{ $attendance->gym?->name ?? '-' }}</td>
                            @endif
                            <td class="text-right"><x-ui.button :href="$clientShowUrl((int) $attendance->client_id)" size="sm" variant="ghost">Perfil</x-ui.button></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGlobalScope ? 4 : 3 }}" class="text-center text-sm text-slate-500 dark:text-slate-300">Aún no hay check-ins hoy.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-movements" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalMovementsTitle">
        <div class="ui-modal-panel max-w-6xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalMovementsTitle" class="ui-heading text-lg">{{ $isCashierScoped ? 'Tus últimos movimientos de caja' : 'Últimos movimientos de caja' }}</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[940px]">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Método</th>
                        <th>Monto</th>
                        @if ($isGlobalScope)
                            <th>Sede</th>
                        @endif
                        <th>Usuario</th>
                        <th>Descripción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($recentCashMovements as $movement)
                        <tr>
                            <td>{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>
                                <x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">
                                    {{ $movement->type === 'income' ? 'Ingreso' : 'Egreso' }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                            <td class="{{ $movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }} font-semibold">
                                {{ $movement->type === 'income' ? '+' : '-' }}{{ $currencyFormatter::format((float) $movement->amount, $appCurrencyCode, true) }}
                            </td>
                            @if ($isGlobalScope)
                                <td>{{ $movement->gym?->name ?? '-' }}</td>
                            @endif
                            <td>{{ $movement->createdBy?->name ?? '-' }}</td>
                            <td class="max-w-[340px] truncate" title="{{ $movement->description ?: '-' }}">{{ $movement->description ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGlobalScope ? 7 : 6 }}" class="text-center text-sm text-slate-500 dark:text-slate-300">{{ $isCashierScoped ? 'Aún no tienes movimientos registrados.' : 'No hay movimientos registrados aún.' }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        function closeAllPanelModals() {
            document.querySelectorAll('.panel-modal').forEach(function (modal) {
                modal.classList.add('hidden');
            });
            document.body.classList.remove('overflow-hidden');
        }

        document.querySelectorAll('[data-open-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                const modalId = button.getAttribute('data-open-modal');
                const modal = modalId ? document.getElementById(modalId) : null;
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', closeAllPanelModals);
        });

        document.querySelectorAll('.panel-modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeAllPanelModals();
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllPanelModals();
            }
        });

    })();
</script>
@endpush
