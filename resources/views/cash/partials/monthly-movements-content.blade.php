@php
    $currencyFormatter = \App\Support\Currency::class;
    $methodLabels = [
        'cash' => 'Efectivo',
        'card' => 'Tarjeta',
        'transfer' => 'Transferencia',
    ];
    $monthLabel = ucfirst((string) $monthStart->translatedFormat('F Y'));
    $monthRangeLabel = $monthStart->format('Y-m-d').' al '.$monthEnd->format('Y-m-d');
    $emptyStateLabel = $isCashierScoped
        ? 'Aun no registras movimientos en este mes.'
        : 'Aun no hay movimientos registrados en este mes.';
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-2">
        <x-ui.badge variant="info">{{ $monthLabel }}</x-ui.badge>
        <x-ui.badge variant="muted">{{ (int) ($monthlySummary['movements_count'] ?? 0) }} movimientos</x-ui.badge>
    </div>

    <p class="text-sm ui-muted">
        Periodo consultado: <strong>{{ $monthRangeLabel }}</strong>. El valor vuelve a empezar desde cero al iniciar el siguiente mes, igual que en el panel.
    </p>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-900/75">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Movimientos</p>
            <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($monthlySummary['movements_count'] ?? 0) }}</p>
        </article>
        <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">{{ $isCashierScoped ? 'Tus ingresos' : 'Ingresos del mes' }}</p>
            <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100">{{ $currencyFormatter::format((float) ($monthlySummary['income_total'] ?? 0), $appCurrencyCode) }}</p>
        </article>
        <article class="rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
            <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">{{ $isCashierScoped ? 'Tus egresos' : 'Egresos del mes' }}</p>
            <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100">{{ $currencyFormatter::format((float) ($monthlySummary['expense_total'] ?? 0), $appCurrencyCode) }}</p>
        </article>
        <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
            <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Balance neto</p>
            <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100">{{ $currencyFormatter::format((float) ($monthlySummary['net_total'] ?? 0), $appCurrencyCode) }}</p>
        </article>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
        <table class="ui-table min-w-[1280px]">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Turno</th>
                <th>Tipo</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Cliente</th>
                <th>Alta cliente</th>
                <th>Usuario</th>
                <th>Descripcion</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($monthlyMovements as $movement)
                <tr>
                    <td>{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>#{{ $movement->cash_session_id ?? '-' }}</td>
                    <td>
                        <x-ui.badge :variant="$movement->type === 'income' ? 'success' : 'danger'">
                            {{ $movement->type === 'income' ? 'Ingreso' : 'Egreso' }}
                        </x-ui.badge>
                    </td>
                    <td>{{ $methodLabels[$movement->method] ?? $movement->method }}</td>
                    <td class="font-semibold {{ $movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }}">
                        {{ $movement->type === 'income' ? '+' : '-' }}{{ $currencyFormatter::format((float) $movement->amount, $appCurrencyCode, true) }}
                    </td>
                    <td>{{ $movement->membership?->client?->full_name ?? '-' }}</td>
                    <td>{{ \App\Support\ClientAudit::actorDisplay((string) ($movement->membership?->client?->created_by_name_snapshot ?? ''), (string) ($movement->membership?->client?->created_by_role_snapshot ?? '')) }}</td>
                    <td>{{ $movement->createdBy?->name ?? '-' }}</td>
                    <td>{{ $movement->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-sm text-slate-500 dark:text-slate-300">{{ $emptyStateLabel }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
