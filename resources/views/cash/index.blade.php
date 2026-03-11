@extends('layouts.panel')

@section('title', 'Caja profesional')
@section('page-title', 'Caja por turno')

@push('styles')
<style>
    .cash-page [data-tone='ok'] { color: rgb(5 150 105); }
    .cash-page [data-tone='warn'] { color: rgb(217 119 6); }
    .cash-page [data-tone='bad'] { color: rgb(225 29 72); }
    .theme-dark .cash-page [data-tone='ok'] { color: rgb(110 231 183); }
    .theme-dark .cash-page [data-tone='warn'] { color: rgb(252 211 77); }
    .theme-dark .cash-page [data-tone='bad'] { color: rgb(251 113 133); }
</style>
@endpush

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $currencyCode = $appCurrencyCode ?? null;
        $currencySymbol = trim((string) ($appCurrencySymbol ?? '$'));

        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $user = auth()->user();
        $isOwnerUser = (bool) ($user?->isOwner());
        $isCashierUser = (bool) ($user?->isCashier());
        $isCashAdmin = (bool) ($isCashAdmin ?? ($user && ($user->gym_id === null || $isOwnerUser)));
        $canApproveDifference = (bool) ($canApproveCashDifference ?? $isCashAdmin);

        $routeHasVoidMovement = \Illuminate\Support\Facades\Route::has('cash.movements.void');
        $voidRouteTemplate = $routeHasVoidMovement ? route('cash.movements.void', ['movement' => '__MOVEMENT__']) : '';

        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isCurrentCashView = ! $isGlobalScope && array_key_exists('openSession', get_defined_vars());
        $openSession = $openSession ?? null;
        $isCashierScoped = (bool) ($isCashierScoped ?? false);
        $cashWriteBlocked = (bool) ($cashWriteBlocked ?? false);
        $cashWriteBlockedReason = trim((string) ($cashWriteBlockedReason ?? ''));
        $canOpenCash = (bool) ($canOpenCash ?? $isOwnerUser);
        $canCloseCash = (bool) ($canCloseCash ?? $isOwnerUser);
        $canManageMovements = (bool) ($canManageMovements ?? true);
        $recentClosedSessions = $recentClosedSessions ?? collect();
    @endphp

    <div class="cash-page space-y-4"
         data-module="cash-index"
         data-currency-symbol="{{ $currencySymbol }}"
         data-void-route-template="{{ $voidRouteTemplate }}">
        @if ($isCurrentCashView && $isCashierScoped)
            <x-ui.card title="Vista privada" subtitle="Solo ves tus cobros, egresos y movimientos personales dentro del turno.">
                <p class="ui-alert ui-alert-info">Los acumulados del gimnasio quedan ocultos para tu perfil.</p>
            </x-ui.card>
        @endif
        @if ($isCurrentCashView)
            @if (! $openSession)
                @if ($cashWriteBlocked)
                    <x-ui.card title="Caja en solo lectura" subtitle="Operación administrada desde sede principal.">
                        <p class="ui-alert ui-alert-warning">
                            {{ $cashWriteBlockedReason !== '' ? $cashWriteBlockedReason : 'No tienes permisos para abrir o cerrar caja en esta sucursal.' }}
                        </p>
                    </x-ui.card>
                @elseif (! $canOpenCash)
                    <x-ui.card title="Apertura restringida" subtitle="Solo usuarios autorizados pueden abrir caja.">
                        <p class="ui-alert ui-alert-warning">
                            Tu perfil no tiene permiso para abrir caja. Solicita al dueño del gimnasio que abra el turno o te habilite este permiso.
                        </p>
                    </x-ui.card>
                @else
                    <x-ui.card title="Abrir turno" subtitle="Debes abrir caja para cobrar membresías o registrar movimientos.">
                        <form method="POST" action="{{ route('cash.open') }}" class="space-y-4">
                            @csrf
                            <div class="grid gap-4 md:grid-cols-2">
                                <label class="space-y-1 text-sm font-semibold ui-muted">
                                    <span>Monto inicial (obligatorio)</span>
                                    <input type="number" name="opening_balance" step="0.01" min="0" value="{{ old('opening_balance') }}" required class="ui-input">
                                </label>

                                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                                    <span>Notas</span>
                                    <textarea name="notes" rows="3" class="ui-input">{{ old('notes') }}</textarea>
                                </label>
                            </div>

                            <x-ui.button type="submit" variant="success" size="lg">Abrir turno</x-ui.button>
                        </form>
                    </x-ui.card>
                @endif

                <x-ui.card title="Historial reciente de cierres" subtitle="Fecha, hora, motivo y notas del cierre.">
                    @include('cash.partials.closure-history', [
                        'sessions' => $recentClosedSessions,
                        'currencyFormatter' => $currencyFormatter,
                        'currencyCode' => $currencyCode,
                    ])
                </x-ui.card>
            @else
                @php
                    $activeSummary = $summary ?? ['income_total' => 0, 'expense_total' => 0, 'expected_balance' => 0, 'movements_count' => 0];
                    $activeMethodTotals = $methodTotals ?? collect();
                    $closingSummary = $closeSummary ?? $activeSummary;
                    $closingMethodTotals = $closeMethodTotals ?? $activeMethodTotals;
                    $activeMovements = $latestMovements ?? collect();
                    $scopedNetTotal = round((float) ($activeSummary['income_total'] ?? 0) - (float) ($activeSummary['expense_total'] ?? 0), 2);
                    $scopedVisibleTotal = round((float) $openSession->opening_balance + $scopedNetTotal, 2);

                    $methodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($activeMethodTotals) {
                        $row = $activeMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $closeMethodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($closingMethodTotals) {
                        $row = $closingMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $expectedCash = (float) $openSession->opening_balance + (float) ($closeMethodMap->get('cash')->income_total ?? 0) - (float) ($closeMethodMap->get('cash')->expense_total ?? 0);
                    $expectedCard = (float) ($closeMethodMap->get('card')->income_total ?? 0) - (float) ($closeMethodMap->get('card')->expense_total ?? 0);
                    $expectedTransfer = (float) ($closeMethodMap->get('transfer')->income_total ?? 0) - (float) ($closeMethodMap->get('transfer')->expense_total ?? 0);
                    $expectedTotal = round($expectedCash + $expectedCard + $expectedTransfer, 2);
                @endphp

                <x-ui.card title="{{ $isCashierScoped ? 'Tu producciÃ³n en el turno #'.$openSession->id : 'Turno activo #'.$openSession->id }}" subtitle="Apertura {{ $openSession->opened_at?->format('Y-m-d H:i') }} por {{ $openSession->openedBy?->name ?? 'N/D' }}">
                    @if ($isCashierScoped)
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                            <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ $currencyFormatter::format((float) $openSession->opening_balance, $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Tus ingresos</p>
                                <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100">{{ $currencyFormatter::format((float) $activeSummary['income_total'], $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Tus egresos</p>
                                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100">{{ $currencyFormatter::format((float) $activeSummary['expense_total'], $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Saldo visible</p>
                                <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ $currencyFormatter::format($scopedVisibleTotal, $currencyCode) }}</p>
                                <p class="text-xs text-cyan-700 dark:text-cyan-200">Apertura + tus movimientos</p>
                            </article>
                            <article class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Tus movimientos</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ (int) $activeSummary['movements_count'] }}</p>
                            </article>
                        </div>
                    @else
                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                            <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ $currencyFormatter::format((float) $openSession->opening_balance, $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Ingresos</p>
                                <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100">{{ $currencyFormatter::format((float) $activeSummary['income_total'], $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Egresos</p>
                                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100">{{ $currencyFormatter::format((float) $activeSummary['expense_total'], $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Esperados</p>
                                <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ $currencyFormatter::format($expectedTotal, $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Saldo actual</p>
                                <p class="mt-1 text-2xl font-black text-violet-800 dark:text-violet-100">{{ $currencyFormatter::format($expectedTotal, $currencyCode) }}</p>
                            </article>
                            <article class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos</p>
                                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ (int) $activeSummary['movements_count'] }}</p>
                            </article>
                        </div>
                    @endif

                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        @foreach ($methodMap as $methodTotal)
                            <article class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">{{ $methodLabels[$methodTotal->method] ?? $methodTotal->method }}</p>
                                <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">Movimientos: <strong>{{ $methodTotal->movements_count }}</strong></p>
                                <p class="text-sm text-emerald-700 dark:text-emerald-300">+ {{ $currencyFormatter::format((float) $methodTotal->income_total, $currencyCode, true) }}</p>
                                <p class="text-sm text-rose-700 dark:text-rose-300">- {{ $currencyFormatter::format((float) $methodTotal->expense_total, $currencyCode, true) }}</p>
                            </article>
                        @endforeach
                    </div>
                </x-ui.card>

                <section class="grid gap-4 xl:grid-cols-3">
                    @if ($cashWriteBlocked)
                        <x-ui.card title="Operaciones de caja bloqueadas" class="xl:col-span-3">
                            <p class="ui-alert ui-alert-warning">
                                {{ $cashWriteBlockedReason !== '' ? $cashWriteBlockedReason : 'Esta sucursal opera con caja controlada por sede principal.' }}
                            </p>
                        </x-ui.card>
                    @else
                        <x-ui.card title="Registrar movimiento" class="xl:col-span-2">
                            @if (! $canManageMovements)
                                <p class="ui-alert ui-alert-warning">
                                    Tu perfil no tiene permiso para registrar cobros o movimientos de caja.
                                </p>
                            @else
                                <form id="cash-movement-form" method="POST" action="{{ route('cash.movements.store') }}" class="space-y-4" data-high-threshold="100">
                                @csrf
                                <input type="hidden" id="movement-high-confirmed" name="high_amount_confirmed" value="0">

                                <div id="movement-guard-alert" class="hidden ui-alert ui-alert-warning"></div>

                                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Tipo</span>
                                        <select id="movement-type" name="type" required class="ui-input" aria-label="Tipo de movimiento">
                                            <option value="">Seleccione</option>
                                            <option value="income" @selected(old('type') === 'income')>Ingreso</option>
                                            <option value="expense" @selected(old('type') === 'expense')>Egreso</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Método</span>
                                        <select name="method" required class="ui-input" aria-label="Método de pago">
                                            <option value="">Seleccione</option>
                                            <option value="cash" @selected(old('method') === 'cash')>Efectivo</option>
                                            <option value="card" @selected(old('method') === 'card')>Tarjeta</option>
                                            <option value="transfer" @selected(old('method') === 'transfer')>Transferencia</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted">
                                        <span>Monto</span>
                                        <input id="movement-amount" type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="ui-input" aria-label="Monto">
                                    </label>

                                    <label id="movement-expense-category-wrap" class="hidden space-y-1 text-sm font-semibold ui-muted">
                                        <span>Categoria egreso (opcional)</span>
                                        <select id="movement-expense-category" name="expense_category" class="ui-input" aria-label="Categoria de egreso">
                                            <option value="">Sin categoria</option>
                                            <option value="insumos" @selected(old('expense_category') === 'insumos')>Insumos</option>
                                            <option value="servicios" @selected(old('expense_category') === 'servicios')>Servicios</option>
                                            <option value="mantenimiento" @selected(old('expense_category') === 'mantenimiento')>Mantenimiento</option>
                                            <option value="nomina" @selected(old('expense_category') === 'nomina')>Nomina</option>
                                            <option value="otros" @selected(old('expense_category') === 'otros')>Otros</option>
                                        </select>
                                    </label>

                                    <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2 xl:col-span-4">
                                        <span id="movement-description-label">Descripción (obligatoria)</span>
                                        <textarea id="movement-description" name="description" rows="2" required class="ui-input" aria-label="Descripción" placeholder="Ingresa descripción obligatoria.">{{ old('description') }}</textarea>
                                    </label>
                                </div>

                                    <x-ui.button id="movement-submit" type="submit" variant="success">Registrar ingreso</x-ui.button>
                                </form>
                            @endif

                            <div class="mt-6 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <div class="mb-3">
                                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">Historial reciente de cierres</h3>
                                    <p class="text-sm ui-muted">Aqui puedes leer la fecha, hora, motivo y notas del ultimo cierre de caja.</p>
                                </div>

                                @include('cash.partials.closure-history', [
                                    'sessions' => $recentClosedSessions,
                                    'currencyFormatter' => $currencyFormatter,
                                    'currencyCode' => $currencyCode,
                                ])
                            </div>
                        </x-ui.card>

                        <x-ui.card title="{{ $canCloseCash ? 'Cerrar turno' : 'Cierre restringido' }}" subtitle="{{ $canCloseCash ? 'Conteo por método y control de diferencias.' : 'Solo usuarios autorizados pueden ver y ejecutar el cierre completo.' }}">
                            @if (! $canCloseCash)
                                <p class="ui-alert ui-alert-warning mb-3">
                                    Tu perfil no tiene permiso para cerrar caja. Esta acción la realiza el dueño o un usuario autorizado.
                                </p>
                            @else

                            <div id="close-form-alert" class="hidden ui-alert ui-alert-warning"></div>

                            <div class="space-y-2 text-sm">
                                <p class="ui-muted">Esperado total: <strong>{{ $currencyFormatter::format($expectedTotal, $currencyCode) }}</strong></p>
                                <p class="ui-muted">Estado de cierre: <strong id="close-status-text" data-tone="ok">CUADRA</strong></p>
                            </div>

                            <form id="cash-close-form" method="POST" action="{{ route('cash.close') }}" class="mt-4 space-y-4"
                                  data-expected-cash="{{ number_format($expectedCash, 2, '.', '') }}"
                                  data-expected-card="{{ number_format($expectedCard, 2, '.', '') }}"
                                  data-expected-transfer="{{ number_format($expectedTransfer, 2, '.', '') }}"
                                  data-can-approve-difference="{{ $canApproveDifference ? '1' : '0' }}">
                                @csrf
                                <input id="close-closing-balance" type="hidden" name="closing_balance" value="{{ old('closing_balance') }}">
                                <input id="close-difference-approved" type="hidden" name="difference_approved" value="0">

                            <div class="grid gap-3 rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                <div class="grid grid-cols-4 gap-2 text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">
                                    <span>Método</span>
                                    <span class="text-right">Esperado</span>
                                    <span class="text-right">Contado</span>
                                    <span class="text-right">Diferencia</span>
                                </div>

                                @foreach (['cash' => 'efectivo_contado', 'card' => 'tarjeta_contado', 'transfer' => 'transferencia_contado'] as $methodKey => $fieldName)
                                    @php
                                        $expectedByMethod = $methodKey === 'cash' ? $expectedCash : ($methodKey === 'card' ? $expectedCard : $expectedTransfer);
                                    @endphp
                                    <div class="grid grid-cols-4 items-center gap-2 text-sm">
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $methodLabels[$methodKey] }}</span>
                                        <span class="text-right text-slate-700 dark:text-slate-200">{{ $currencyFormatter::format($expectedByMethod, $currencyCode) }}</span>
                                        <input id="counted-{{ $methodKey }}" class="ui-input text-right" type="number" min="0" step="0.01" name="{{ $fieldName }}" value="{{ old($fieldName, number_format($expectedByMethod, 2, '.', '')) }}" aria-label="Contado {{ $methodLabels[$methodKey] }}">
                                        <span id="difference-{{ $methodKey }}" class="text-right font-bold" data-tone="ok">{{ $currencyFormatter::format(0, $currencyCode) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Diferencia total</span>
                                    <span id="difference-total" class="text-lg font-black" data-tone="ok">{{ $currencyFormatter::format(0, $currencyCode) }}</span>
                                </div>
                            </div>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Motivo de diferencia (obligatorio si no cuadra)</span>
                                <textarea id="difference-reason" name="difference_reason" rows="2" class="ui-input" placeholder="Explica por qué hay diferencia.">{{ old('difference_reason') }}</textarea>
                            </label>

                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Notas de cierre</span>
                                <textarea name="notes" rows="3" class="ui-input">{{ old('notes') }}</textarea>
                            </label>

                            @if (! $canApproveDifference)
                                <p class="ui-alert ui-alert-warning text-xs">Solo Admin puede confirmar cierre con diferencia.</p>
                            @endif

                                <x-ui.button id="close-submit" type="submit" variant="danger" size="lg" class="w-full justify-center">Cerrar turno</x-ui.button>
                            </form>
                            @endif
                        </x-ui.card>
                    @endif
                </section>

                <x-ui.card title="{{ $isCashierScoped ? 'Tus últimos 10 movimientos' : 'últimos 10 movimientos' }}">
                    <div class="overflow-x-auto">
                        <table class="ui-table min-w-[1180px]">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Cliente</th>
                                <th>Alta cliente</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($activeMovements as $movement)
                                @php
                                    $movementIsVoided = (string) ($movement->status ?? '') === 'voided' || !empty($movement->voided_at) || !empty($movement->void_reason);
                                @endphp
                                <tr class="{{ $movementIsVoided ? 'opacity-70' : '' }}">
                                    <td>{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td><x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">{{ $movement->type }}</x-ui.badge></td>
                                    <td>{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                                    <td class="font-semibold {{ $movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                                        {{ $movement->type === 'income' ? '+' : '-' }}{{ $currencyFormatter::format((float) $movement->amount, $currencyCode, true) }}
                                    </td>
                                    <td>{{ $movement->membership?->client?->full_name ?? '-' }}</td>
                                    <td>{{ \App\Support\ClientAudit::actorDisplay((string) ($movement->membership?->client?->created_by_name_snapshot ?? ''), (string) ($movement->membership?->client?->created_by_role_snapshot ?? '')) }}</td>
                                    <td>{{ $movement->createdBy?->name ?? '-' }}</td>
                                    <td>{{ $movement->description ?: '-' }}</td>
                                    <td>
                                        @if ($movementIsVoided)
                                            <x-ui.badge variant="warning">Anulado</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="success">Activo</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($movementIsVoided)
                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">Sin acción</span>
                                        @elseif (! $isCashAdmin)
                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-300">Solo Admin</span>
                                        @elseif (! $routeHasVoidMovement)
                                            <button type="button" class="ui-button ui-button-muted px-2 py-1 text-xs" disabled title="Falta route cash.movements.void">Anular</button>
                                        @else
                                            <button type="button" class="ui-button ui-button-danger px-2 py-1 text-xs js-open-void-modal" data-movement-id="{{ $movement->id }}" data-movement-label="#{{ $movement->id }} {{ $movement->type }} {{ $currencyFormatter::format((float) $movement->amount, $currencyCode) }}" aria-label="Anular movimiento {{ $movement->id }}" title="Anular movimiento">Anular</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-sm text-slate-500 dark:text-slate-300">{{ $isCashierScoped ? 'Aún no tienes movimientos en este turno.' : 'Aún no hay movimientos en este turno.' }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <x-ui.button :href="route('clients.index')" variant="primary">Cobrar membresía</x-ui.button>
                        @if (! $isCashierUser || $canCloseCash)
                            <x-ui.button id="cash-go-history" :href="route('cash.sessions.index')" variant="secondary">Ver historial de caja</x-ui.button>
                        @endif
                        @if (! $isCashierUser)
                            <x-ui.button :href="route('reports.income')" variant="ghost">Ver reporte de ingresos</x-ui.button>
                        @endif
                    </div>
                </x-ui.card>

                @include('cash.partials.session-modals')
            @endif
        @else
            @php
                $historyRows = $sessions ?? collect();
            @endphp

            <x-ui.card title="Historial de caja" subtitle="Revisión de cierres, diferencias y responsables.">
                @if ($isGlobalScope)
                    <p class="mb-4 ui-alert ui-alert-info">
                        Modo global activo: historial consolidado de todas tus sedes en solo lectura.
                    </p>
                @endif
                <div class="overflow-x-auto">
                    <table class="ui-table min-w-[1480px]">
                        <thead>
                        <tr>
                            <th>ID</th>
                            @if ($isGlobalScope)
                                <th>Sede</th>
                            @endif
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Apertura por</th>
                            <th>Cierre por</th>
                            <th>Tipo</th>
                            <th>Mensaje</th>
                            <th>Esperado</th>
                            <th>Cierre</th>
                            <th>Diferencia</th>
                            <th>Motivo</th>
                            <th>Notas</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($historyRows as $session)
                            @php
                                $difference = (float) ($session->difference ?? 0);
                                $closedWithDifference = (string) $session->status === 'closed' && abs($difference) > 0.00001;
                            @endphp
                            <tr>
                                <td>{{ $session->id }}</td>
                                @if ($isGlobalScope)
                                    <td>{{ $session->gym?->name ?? '-' }}</td>
                                @endif
                                <td>{{ $session->opened_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $session->closed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $session->openedBy?->name ?? '-' }}</td>
                                <td>{{ $session->wasAutoClosedAtMidnight() ? 'Sistema' : ($session->closedBy?->name ?? '-') }}</td>
                                <td>{{ $session->closeSourceLabel() }}</td>
                                <td>{{ $session->closeMessage() }}</td>
                                <td>{{ $currencyFormatter::format((float) ($session->expected_balance ?? 0), $currencyCode) }}</td>
                                <td>{{ $session->closing_balance !== null ? $currencyFormatter::format((float) $session->closing_balance, $currencyCode) : '-' }}</td>
                                <td class="font-bold {{ $difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200') }}">{{ $currencyFormatter::format($difference, $currencyCode) }}</td>
                                <td>{{ $session->difference_reason ?: '-' }}</td>
                                <td>{{ $session->closing_notes ?: '-' }}</td>
                                <td>
                                    <x-ui.badge :variant="(string) $session->status === 'open' ? 'info' : 'success'">{{ $session->status }}</x-ui.badge>
                                    @if ($closedWithDifference)
                                        <x-ui.badge variant="warning">Cerro con diferencia</x-ui.badge>
                                    @endif
                                </td>
                                <td class="text-right"><x-ui.button :href="route('cash.sessions.show', $session->id)" variant="ghost" size="sm">Detalle</x-ui.button></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isGlobalScope ? 15 : 14 }}" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay sesiones registradas.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($historyRows, 'links'))
                    <div class="mt-4">{{ $historyRows->links() }}</div>
                @endif
            </x-ui.card>
        @endif
    </div>
@endsection

{{-- 
TODO backend mínimo:
1) Route sugerida: PATCH /cash/movements/{movement}/void -> cash.movements.void
2) CashMovement: status, void_reason, voided_at, voided_by (solo Admin puede anular).
3) Cierre con diferencia: validar difference_reason + supervisor_password + permiso admin.
4) Registrar auditoría de: movimiento creado, anulado, cierre, cierre con diferencia, aprobación supervisor.
--}}
