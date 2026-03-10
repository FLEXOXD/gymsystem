@php
    $closureRows = $sessions ?? collect();
    $currencyFormatter = $currencyFormatter ?? \App\Support\Currency::class;
    $currencyCode = $currencyCode ?? ($appCurrencyCode ?? null);
@endphp

<div class="overflow-x-auto">
    <table class="ui-table min-w-[980px]">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Tipo</th>
            <th>Mensaje</th>
            <th>Diferencia</th>
            <th>Motivo</th>
            <th>Notas de cierre</th>
            <th>Cerrado por</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($closureRows as $session)
            @php
                $difference = (float) ($session->difference ?? 0);
                $closedByLabel = $session->wasAutoClosedAtMidnight()
                    ? 'Sistema'
                    : ($session->closedBy?->name ?? '-');
            @endphp
            <tr>
                <td>{{ $session->closed_at?->format('Y-m-d') ?? '-' }}</td>
                <td>{{ $session->closed_at?->format('H:i') ?? '-' }}</td>
                <td>
                    <x-ui.badge :variant="$session->wasAutoClosedAtMidnight() ? 'warning' : 'info'">
                        {{ $session->closeSourceLabel() }}
                    </x-ui.badge>
                </td>
                <td>{{ $session->closeMessage() }}</td>
                <td class="font-semibold {{ $difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200') }}">
                    {{ $currencyFormatter::format($difference, $currencyCode) }}
                </td>
                <td>{{ $session->difference_reason ?: 'Sin novedad' }}</td>
                <td>{{ $session->closing_notes ?: '-' }}</td>
                <td>{{ $closedByLabel }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-sm text-slate-500 dark:text-slate-300">
                    Aun no hay cierres registrados.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
