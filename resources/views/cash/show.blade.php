@extends('layouts.panel')

@section('title', 'Caja #'.$session->id)
@section('page-title', 'Detalle de caja #'.$session->id)

@section('content')
    @php
        $currencyFormatter = \App\Support\Currency::class;
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];
    @endphp
    <x-ui.card title="Sesión #{{ $session->id }}" subtitle="Apertura {{ $session->opened_at?->format('Y-m-d H:i') }} por {{ $session->openedBy?->name ?? 'N/D' }}">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge :variant="$session->status === 'open' ? 'success' : 'info'">{{ $session->status }}</x-ui.badge>
            @if ($session->status === 'closed')
                <x-ui.badge :variant="$session->wasAutoClosedAtMidnight() ? 'warning' : 'info'">{{ $session->closeSourceLabel() }}</x-ui.badge>
            @endif
            <x-ui.button :href="route('cash.index')" size="sm" variant="secondary">Caja actual</x-ui.button>
            <x-ui.button :href="route('cash.sessions.index')" size="sm" variant="ghost">Historial</x-ui.button>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ $currencyFormatter::format((float) $session->opening_balance, $appCurrencyCode) }}</p>
            </article>
            <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Ingresos</p>
                <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100">{{ $currencyFormatter::format((float) $summary['income_total'], $appCurrencyCode) }}</p>
            </article>
            <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Egresos</p>
                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100">{{ $currencyFormatter::format((float) $summary['expense_total'], $appCurrencyCode) }}</p>
            </article>
            <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Esperado</p>
                <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ $currencyFormatter::format((float) $summary['expected_balance'], $appCurrencyCode) }}</p>
            </article>
        </div>

        <div class="mt-4 grid gap-2 text-sm text-slate-700 dark:text-slate-200 md:grid-cols-2">
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">Cierre:</span> {{ $session->closed_at?->format('Y-m-d H:i') ?? 'Sin cerrar' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">Cerrada por:</span> {{ $session->wasAutoClosedAtMidnight() ? 'Sistema' : ($session->closedBy?->name ?? '-') }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">Notas de apertura:</span> {{ $session->notes ?: '-' }}</p>
            @if ($session->status === 'closed')
                <p><span class="font-semibold text-slate-900 dark:text-slate-100">Tipo de cierre:</span> {{ $session->closeSourceLabel() }}</p>
                <p><span class="font-semibold text-slate-900 dark:text-slate-100">Mensaje:</span> {{ $session->closeMessage() }}</p>
                <p><span class="font-semibold text-slate-900 dark:text-slate-100">Motivo de diferencia:</span> {{ $session->difference_reason ?: '-' }}</p>
                <p><span class="font-semibold text-slate-900 dark:text-slate-100">Notas de cierre:</span> {{ $session->closing_notes ?: '-' }}</p>
                <p>
                    <span class="font-semibold text-slate-900 dark:text-slate-100">Diferencia:</span>
                    <span class="{{ (float) $session->difference === 0.0 ? 'text-slate-800 dark:text-slate-200' : ((float) $session->difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300') }} font-bold">
                        {{ $currencyFormatter::format((float) $session->difference, $appCurrencyCode) }}
                    </span>
                </p>
            @endif
        </div>
    </x-ui.card>

    <x-ui.card title="Totales por método">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[640px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:text-slate-300">
                    <th class="px-3 py-3">Método</th>
                    <th class="px-3 py-3">Movimientos</th>
                    <th class="px-3 py-3">Ingresos</th>
                    <th class="px-3 py-3">Egresos</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($methodTotals as $methodTotal)
                    <tr class="border-b border-slate-100 text-sm dark:border-slate-800">
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $methodLabels[$methodTotal->method] ?? $methodTotal->method }}</td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ (int) $methodTotal->movements_count }}</td>
                        <td class="px-3 py-3 text-emerald-700 dark:text-emerald-300">{{ $currencyFormatter::format((float) $methodTotal->income_total, $appCurrencyCode) }}</td>
                        <td class="px-3 py-3 text-rose-700 dark:text-rose-300">{{ $currencyFormatter::format((float) $methodTotal->expense_total, $appCurrencyCode) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">Sin movimientos.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card title="Movimientos del turno">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:text-slate-300">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Método</th>
                    <th class="px-3 py-3">Monto</th>
                    <th class="px-3 py-3">Membresía</th>
                    <th class="px-3 py-3">Creado por</th>
                    <th class="px-3 py-3">Descripción</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($session->movements as $movement)
                    <tr class="border-b border-slate-100 text-sm dark:border-slate-800">
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $movement->id }}</td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3">
                            <x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">{{ $movement->type }}</x-ui.badge>
                        </td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                        <td class="px-3 py-3 {{ $movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }} font-semibold">
                            {{ $movement->type === 'income' ? '+' : '-' }}{{ $currencyFormatter::format((float) $movement->amount, $appCurrencyCode, true) }}
                        </td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $movement->membership_id ?: '-' }}</td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $movement->createdBy?->name ?? '-' }}</td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $movement->description ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">Sin movimientos en este turno.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @php
        $auditLogs = $auditLogs ?? collect();
    @endphp
    <x-ui.card title="Auditoría del turno" subtitle="Eventos clave: movimientos, anulaciones, cierre y aprobaciones.">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[980px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Evento</th>
                    <th class="px-3 py-3">Usuario</th>
                    <th class="px-3 py-3">Detalle</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($auditLogs as $log)
                    @php
                        $eventType = (string) ($log->event_type ?? $log['event_type'] ?? 'evento');
                        $eventBadge = match ($eventType) {
                            'movimiento_creado' => 'success',
                            'movimiento_anulado' => 'warning',
                            'cierre_con_diferencia' => 'danger',
                            'aprobacion_supervisor' => 'info',
                            default => 'muted',
                        };
                        $eventLabel = str_replace('_', ' ', $eventType);
                        $eventDate = $log->created_at ?? $log['created_at'] ?? null;
                        $eventUser = $log->user?->name ?? $log['user_name'] ?? ($log->user_name ?? '-');
                        $eventDetail = $log->detail ?? $log['detail'] ?? ($log->description ?? '-');
                    @endphp
                    <tr class="border-b border-slate-100 text-sm dark:border-slate-800">
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $eventDate ? \Illuminate\Support\Carbon::parse($eventDate)->format('Y-m-d H:i:s') : '-' }}</td>
                        <td class="px-3 py-3"><x-ui.badge :variant="$eventBadge">{{ $eventLabel }}</x-ui.badge></td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $eventUser }}</td>
                        <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $eventDetail }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            Aún no hay eventos de auditoría para este turno.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    {{--
    TODO backend mínimo:
    1) Pasar variable $auditLogs en CashController@show.
       Estructura sugerida por item: event_type, detail, created_at, user_name (o relación user).
    2) Registrar eventos al crear/anular movimiento y al cerrar turno (normal o con diferencia).
    3) Registrar evento de aprobación cuando cierre con diferencia sea confirmado por Admin.
    --}}
@endsection
