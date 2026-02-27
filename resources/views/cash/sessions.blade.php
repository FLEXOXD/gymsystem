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
        $isCashAdmin = (bool) ($isCashAdmin ?? ($user && ($user->gym_id === null || str_contains(strtolower((string) $user->name), 'admin'))));
        $canApproveDifference = (bool) ($canApproveCashDifference ?? $isCashAdmin);

        $routeHasVoidMovement = \Illuminate\Support\Facades\Route::has('cash.movements.void');
        $voidRouteTemplate = $routeHasVoidMovement ? route('cash.movements.void', ['movement' => '__MOVEMENT__']) : '';

        $isCurrentCashView = array_key_exists('openSession', get_defined_vars());
        $openSession = $openSession ?? null;
    @endphp

    <div class="cash-page space-y-4">
        @if ($isCurrentCashView)
            @if (! $openSession)
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
            @else
                @php
                    $activeSummary = $summary ?? ['income_total' => 0, 'expense_total' => 0, 'expected_balance' => 0, 'movements_count' => 0];
                    $activeMethodTotals = $methodTotals ?? collect();
                    $activeMovements = $latestMovements ?? collect();

                    $methodMap = collect(['cash', 'card', 'transfer'])->map(function (string $method) use ($activeMethodTotals) {
                        $row = $activeMethodTotals->firstWhere('method', $method);
                        return (object) [
                            'method' => $method,
                            'movements_count' => (int) ($row->movements_count ?? 0),
                            'income_total' => (float) ($row->income_total ?? 0),
                            'expense_total' => (float) ($row->expense_total ?? 0),
                        ];
                    })->keyBy('method');

                    $expectedCash = (float) $openSession->opening_balance + (float) ($methodMap->get('cash')->income_total ?? 0) - (float) ($methodMap->get('cash')->expense_total ?? 0);
                    $expectedCard = (float) ($methodMap->get('card')->income_total ?? 0) - (float) ($methodMap->get('card')->expense_total ?? 0);
                    $expectedTransfer = (float) ($methodMap->get('transfer')->income_total ?? 0) - (float) ($methodMap->get('transfer')->expense_total ?? 0);
                    $expectedTotal = round($expectedCash + $expectedCard + $expectedTransfer, 2);
                @endphp

                <x-ui.card title="Turno activo #{{ $openSession->id }}" subtitle="Apertura {{ $openSession->opened_at?->format('Y-m-d H:i') }} por {{ $openSession->openedBy?->name ?? 'N/D' }}">
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
                    <x-ui.card title="Registrar movimiento" class="xl:col-span-2">
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
                    </x-ui.card>

                    <x-ui.card title="Cerrar turno" subtitle="Conteo por método y control de diferencias.">
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
                                <textarea id="difference-reason" name="difference_reason" rows="2" class="ui-input" placeholder="Explica por que hay diferencia.">{{ old('difference_reason') }}</textarea>
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
                    </x-ui.card>
                </section>

                <x-ui.card title="Últimos 10 movimientos">
                    <div class="overflow-x-auto">
                        <table class="ui-table min-w-[1180px]">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Cliente</th>
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
                                    <td colspan="9" class="text-center text-sm text-slate-500 dark:text-slate-300">Aún no hay movimientos en este turno.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <x-ui.button :href="route('clients.index')" variant="primary">Cobrar membresía</x-ui.button>
                        <x-ui.button :href="route('cash.sessions.index')" variant="secondary">Ver historial de caja</x-ui.button>
                        <x-ui.button :href="route('reports.income')" variant="ghost">Ver reporte de ingresos</x-ui.button>
                    </div>
                </x-ui.card>

                <div id="high-amount-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="highAmountTitle">
                    <div class="ui-modal-panel max-w-md">
                        <h3 id="highAmountTitle" class="ui-heading text-lg">Confirmar monto alto</h3>
                        <p class="mt-2 text-sm ui-muted">Estas registrando un movimiento alto: <strong id="high-amount-value">{{ $currencySymbol }}0.00</strong></p>
                        <p class="mt-1 text-xs ui-muted">Verifica tipo y método antes de continuar.</p>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-close-high-modal>Cancelar</button>
                            <button type="button" class="ui-button ui-button-primary" id="confirm-high-amount">Confirmar y guardar</button>
                        </div>
                    </div>
                </div>

                <div id="difference-approval-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="differenceApprovalTitle">
                    <div class="ui-modal-panel max-w-md">
                        <h3 id="differenceApprovalTitle" class="ui-heading text-lg">Aprobación supervisor</h3>
                        <p class="mt-2 text-sm ui-muted">El cierre tiene diferencia. Solo Admin puede aprobarlo.</p>
                        <label class="mt-3 block space-y-1 text-sm font-semibold ui-muted">
                            <span>Password/PIN admin</span>
                            <input id="difference-approval-password" type="password" class="ui-input" autocomplete="new-password">
                        </label>
                        <p id="difference-approval-error" class="mt-2 hidden text-xs font-semibold text-rose-600 dark:text-rose-300">Ingresa password/PIN para continuar.</p>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-close-difference-modal>Cancelar</button>
                            <button type="button" class="ui-button ui-button-danger" id="confirm-close-with-diff">Aprobar cierre</button>
                        </div>
                    </div>
                </div>

                <div id="void-movement-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="voidMovementTitle">
                    <div class="ui-modal-panel max-w-md">
                        <h3 id="voidMovementTitle" class="ui-heading text-lg">Anular movimiento</h3>
                        <p class="mt-2 text-sm ui-muted">Movimiento: <strong id="void-movement-label">-</strong></p>

                        <form id="void-movement-form" method="POST" action="{{ $voidRouteTemplate }}" class="mt-3 space-y-3">
                            @csrf
                            @method('PATCH')
                            <label class="space-y-1 text-sm font-semibold ui-muted">
                                <span>Motivo de anulación (obligatorio)</span>
                                <textarea name="void_reason" id="void-reason" rows="3" required class="ui-input" placeholder="Ej: ingreso duplicado o error de caja."></textarea>
                            </label>

                            @if (! $routeHasVoidMovement)
                                <p class="ui-alert ui-alert-danger text-xs">Falta route `cash.movements.void` en backend.</p>
                            @endif

                            <div class="flex justify-end gap-2">
                                <button type="button" class="ui-button ui-button-ghost" data-close-void-modal>Cancelar</button>
                                <button type="submit" class="ui-button ui-button-danger" @disabled(! $routeHasVoidMovement)>Anular movimiento</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @else
            @php
                $historyRows = $sessions ?? collect();
            @endphp

            <x-ui.card title="Historial de caja" subtitle="Revision de cierres, diferencias y responsables.">
                <div class="overflow-x-auto">
                    <table class="ui-table min-w-[1040px]">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Apertura por</th>
                            <th>Cierre por</th>
                            <th>Esperado</th>
                            <th>Cierre</th>
                            <th>Diferencia</th>
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
                                <td>{{ $session->opened_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $session->closed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $session->openedBy?->name ?? '-' }}</td>
                                <td>{{ $session->closedBy?->name ?? '-' }}</td>
                                <td>{{ $currencyFormatter::format((float) ($session->expected_balance ?? 0), $currencyCode) }}</td>
                                <td>{{ $session->closing_balance !== null ? $currencyFormatter::format((float) $session->closing_balance, $currencyCode) : '-' }}</td>
                                <td class="font-bold {{ $difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200') }}">{{ $currencyFormatter::format($difference, $currencyCode) }}</td>
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
                                <td colspan="10" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay sesiones registradas.</td>
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

@push('scripts')
<script>
    (function () {
        const currencySymbol = @json($currencySymbol);

        function formatMoney(value) {
            const num = Number(value || 0);
            return (num < 0 ? '-' : '') + currencySymbol + Math.abs(num).toFixed(2);
        }

        function openModal(el) {
            if (!el) return;
            el.classList.remove('hidden');
        }

        function closeModal(el) {
            if (!el) return;
            el.classList.add('hidden');
        }

        const movementForm = document.getElementById('cash-movement-form');
        const movementType = document.getElementById('movement-type');
        const movementDescription = document.getElementById('movement-description');
        const movementDescriptionLabel = document.getElementById('movement-description-label');
        const movementCategoryWrap = document.getElementById('movement-expense-category-wrap');
        const movementSubmit = document.getElementById('movement-submit');
        const movementAmount = document.getElementById('movement-amount');
        const highConfirmed = document.getElementById('movement-high-confirmed');
        const guardAlert = document.getElementById('movement-guard-alert');

        const highAmountModal = document.getElementById('high-amount-modal');
        const highAmountValue = document.getElementById('high-amount-value');
        const highAmountConfirm = document.getElementById('confirm-high-amount');

        const closeForm = document.getElementById('cash-close-form');
        const closeStatusText = document.getElementById('close-status-text');
        const differenceCash = document.getElementById('difference-cash');
        const differenceCard = document.getElementById('difference-card');
        const differenceTransfer = document.getElementById('difference-transfer');
        const differenceTotal = document.getElementById('difference-total');
        const differenceReason = document.getElementById('difference-reason');
        const closeBalanceInput = document.getElementById('close-closing-balance');
        const closeAlert = document.getElementById('close-form-alert');
        const differenceApproved = document.getElementById('close-difference-approved');

        const differenceApprovalModal = document.getElementById('difference-approval-modal');
        const differenceApprovalPassword = document.getElementById('difference-approval-password');
        const differenceApprovalError = document.getElementById('difference-approval-error');
        const confirmCloseWithDiff = document.getElementById('confirm-close-with-diff');

        const voidModal = document.getElementById('void-movement-modal');
        const voidLabel = document.getElementById('void-movement-label');
        const voidForm = document.getElementById('void-movement-form');
        const voidRouteTemplate = @json($voidRouteTemplate);

        function setTone(el, value) {
            if (!el) return;
            const tone = value > 0 ? 'warn' : (value < 0 ? 'bad' : 'ok');
            el.setAttribute('data-tone', tone);
        }

        function updateMovementMode() {
            if (!movementType) return;
            const isExpense = movementType.value === 'expense';

            if (movementDescription) {
                movementDescription.required = true;
                movementDescription.placeholder = isExpense ? 'Motivo obligatorio del egreso.' : 'Ingresa descripción obligatoria.';
            }

            if (movementDescriptionLabel) {
                movementDescriptionLabel.textContent = 'Descripción (obligatoria)';
            }

            if (movementCategoryWrap) {
                movementCategoryWrap.classList.toggle('hidden', !isExpense);
            }

            if (movementSubmit) {
                movementSubmit.textContent = isExpense ? 'Registrar egreso' : 'Registrar ingreso';
                movementSubmit.classList.toggle('ui-button-danger', isExpense);
                movementSubmit.classList.toggle('ui-button-success', !isExpense);
            }

            if (guardAlert) {
                if (isExpense) {
                    guardAlert.classList.remove('hidden');
                    guardAlert.textContent = 'Egreso requiere descripción obligatoria para auditoría.';
                } else {
                    guardAlert.classList.add('hidden');
                    guardAlert.textContent = '';
                }
            }
        }

        movementType?.addEventListener('change', updateMovementMode);
        updateMovementMode();

        movementForm?.addEventListener('submit', function (event) {
            const amount = Number(movementAmount?.value || 0);
            const threshold = Number(movementForm.dataset.highThreshold || 100);
            const alreadyConfirmed = highConfirmed?.value === '1';
            const descriptionValue = (movementDescription?.value || '').trim();

            if (descriptionValue === '') {
                event.preventDefault();
                movementDescription?.focus();
                if (guardAlert) {
                    guardAlert.classList.remove('hidden');
                    guardAlert.textContent = 'Ingresa descripción obligatoria.';
                }
                return;
            }

            if (amount <= 0) {
                event.preventDefault();
                if (guardAlert) {
                    guardAlert.classList.remove('hidden');
                    guardAlert.textContent = 'Monto debe ser mayor a 0.';
                }
                return;
            }

            if (amount > threshold && !alreadyConfirmed) {
                event.preventDefault();
                if (highAmountValue) highAmountValue.textContent = formatMoney(amount);
                openModal(highAmountModal);
            }
        });

        highAmountConfirm?.addEventListener('click', function () {
            if (highConfirmed) highConfirmed.value = '1';
            closeModal(highAmountModal);
            movementForm?.submit();
        });

        document.querySelectorAll('[data-close-high-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(highAmountModal);
            });
        });

        function updateCloseMath() {
            if (!closeForm) return { totalDiff: 0, totalCounted: 0 };

            const expectedCash = Number(closeForm.dataset.expectedCash || 0);
            const expectedCard = Number(closeForm.dataset.expectedCard || 0);
            const expectedTransfer = Number(closeForm.dataset.expectedTransfer || 0);

            const countedCash = Number((document.getElementById('counted-cash') || {}).value || 0);
            const countedCard = Number((document.getElementById('counted-card') || {}).value || 0);
            const countedTransfer = Number((document.getElementById('counted-transfer') || {}).value || 0);

            const diffCash = Math.round((countedCash - expectedCash) * 100) / 100;
            const diffCard = Math.round((countedCard - expectedCard) * 100) / 100;
            const diffTransfer = Math.round((countedTransfer - expectedTransfer) * 100) / 100;
            const totalDiff = Math.round((diffCash + diffCard + diffTransfer) * 100) / 100;
            const totalCounted = Math.round((countedCash + countedCard + countedTransfer) * 100) / 100;

            if (differenceCash) {
                differenceCash.textContent = formatMoney(diffCash);
                setTone(differenceCash, diffCash);
            }
            if (differenceCard) {
                differenceCard.textContent = formatMoney(diffCard);
                setTone(differenceCard, diffCard);
            }
            if (differenceTransfer) {
                differenceTransfer.textContent = formatMoney(diffTransfer);
                setTone(differenceTransfer, diffTransfer);
            }
            if (differenceTotal) {
                differenceTotal.textContent = formatMoney(totalDiff);
                setTone(differenceTotal, totalDiff);
            }

            if (closeStatusText) {
                if (totalDiff === 0) {
                    closeStatusText.textContent = 'CUADRA';
                    closeStatusText.setAttribute('data-tone', 'ok');
                } else if (totalDiff > 0) {
                    closeStatusText.textContent = 'SOBRANTE ' + formatMoney(totalDiff);
                    closeStatusText.setAttribute('data-tone', 'warn');
                } else {
                    closeStatusText.textContent = 'FALTANTE ' + formatMoney(totalDiff);
                    closeStatusText.setAttribute('data-tone', 'bad');
                }
            }

            if (differenceReason) {
                differenceReason.required = totalDiff !== 0;
            }

            if (closeBalanceInput) {
                closeBalanceInput.value = totalCounted.toFixed(2);
            }

            return { totalDiff, totalCounted };
        }

        ['counted-cash', 'counted-card', 'counted-transfer'].forEach(function (id) {
            document.getElementById(id)?.addEventListener('input', updateCloseMath);
        });
        updateCloseMath();

        closeForm?.addEventListener('submit', function (event) {
            const calc = updateCloseMath();
            const canApprove = (closeForm.dataset.canApproveDifference || '0') === '1';
            const hasDifference = Math.abs(calc.totalDiff) > 0.0001;

            if (closeAlert) {
                closeAlert.classList.add('hidden');
                closeAlert.textContent = '';
            }

            if (!hasDifference) {
                if (differenceApproved) differenceApproved.value = '0';
                return;
            }

            if (!differenceReason || differenceReason.value.trim() === '') {
                event.preventDefault();
                if (closeAlert) {
                    closeAlert.classList.remove('hidden');
                    closeAlert.textContent = 'Debes ingresar un motivo porque el cierre no cuadra.';
                }
                return;
            }

            if (!canApprove) {
                event.preventDefault();
                if (closeAlert) {
                    closeAlert.classList.remove('hidden');
                    closeAlert.textContent = 'Solo Admin puede confirmar cierre con diferencia.';
                }
                return;
            }

            if (differenceApproved && differenceApproved.value !== '1') {
                event.preventDefault();
                if (differenceApprovalError) differenceApprovalError.classList.add('hidden');
                if (differenceApprovalPassword) differenceApprovalPassword.value = '';
                openModal(differenceApprovalModal);
            }
        });

        confirmCloseWithDiff?.addEventListener('click', function () {
            if (!differenceApprovalPassword || differenceApprovalPassword.value.trim() === '') {
                if (differenceApprovalError) differenceApprovalError.classList.remove('hidden');
                return;
            }

            if (differenceApproved) differenceApproved.value = '1';

            const oldHidden = closeForm?.querySelector('input[name="supervisor_password"]');
            if (oldHidden) {
                oldHidden.value = differenceApprovalPassword.value;
            } else if (closeForm) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'supervisor_password';
                hidden.value = differenceApprovalPassword.value;
                closeForm.appendChild(hidden);
            }

            closeModal(differenceApprovalModal);
            closeForm?.submit();
        });

        document.querySelectorAll('[data-close-difference-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(differenceApprovalModal);
            });
        });

        document.querySelectorAll('.js-open-void-modal').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!voidForm || !voidRouteTemplate) return;

                const movementId = button.getAttribute('data-movement-id') || '';
                const movementLabel = button.getAttribute('data-movement-label') || '-';
                voidForm.action = voidRouteTemplate.replace('__MOVEMENT__', movementId);
                if (voidLabel) voidLabel.textContent = movementLabel;

                const reason = document.getElementById('void-reason');
                if (reason) reason.value = '';

                openModal(voidModal);
            });
        });

        document.querySelectorAll('[data-close-void-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(voidModal);
            });
        });

    })();
</script>
@endpush

{{--
TODO backend minimo:
1) Route sugerida: PATCH /cash/movements/{movement}/void -> cash.movements.void
2) CashMovement: status, void_reason, voided_at, voided_by (solo Admin puede anular).
3) Cierre con diferencia: validar difference_reason + supervisor_password + permiso admin.
4) Registrar auditoría de: movimiento creado, anulado, cierre, cierre con diferencia, aprobación supervisor.
--}}
