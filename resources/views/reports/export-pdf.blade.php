<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Profesional</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111; font-size: 12px; }
        h1, h2, h3 { margin: 0 0 8px; }
        h1 { font-size: 18px; }
        h2 { font-size: 14px; margin-top: 14px; }
        .muted { color: #555; margin-bottom: 8px; }
        .kpis { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .kpis td { border: 1px solid #aaa; padding: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #aaa; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #efefef; }
    </style>
</head>
<body>
    @php
        $currencyFormatter = \App\Support\Currency::class;
    @endphp
    <h1>Reportes Profesionales - Gimnasio</h1>
    <p class="muted">Período: {{ $from->toDateString() }} a {{ $to->toDateString() }}</p>

    <h2>Resumen Financiero</h2>
    <table class="kpis">
        <tr>
            <td>Total ingresos: {{ $currencyFormatter::format((float) $incomeSummary['total_income'], $appCurrencyCode) }}</td>
            <td>Total egresos: {{ $currencyFormatter::format((float) $incomeSummary['total_expense'], $appCurrencyCode) }}</td>
            <td>Balance: {{ $currencyFormatter::format((float) $incomeSummary['balance'], $appCurrencyCode) }}</td>
            <td>Movimientos: {{ (int) $incomeSummary['total_movements'] }}</td>
        </tr>
    </table>

    <h2>Ingresos/Egresos por Método</h2>
    <table>
        <thead>
        <tr>
            <th>Método</th>
            <th>Ingresos</th>
            <th>Egresos</th>
            <th>Balance</th>
            <th>Movimientos</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($incomeByMethod as $row)
            <tr>
                <td>{{ match ($row->method) { 'cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia', default => $row->method } }}</td>
                <td>{{ $currencyFormatter::format((float) $row->income_total, $appCurrencyCode) }}</td>
                <td>{{ $currencyFormatter::format((float) $row->expense_total, $appCurrencyCode) }}</td>
                <td>{{ $currencyFormatter::format((float) $row->balance, $appCurrencyCode) }}</td>
                <td>{{ (int) $row->movements_count }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Sin datos en este rango.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Asistencias</h2>
    <table class="kpis">
        <tr>
            <td>Total asistencias: {{ (int) $attendanceSummary['total_attendances'] }}</td>
            <td>Activos: {{ (int) $membershipSummary['active'] }}</td>
            <td>Vencidos: {{ (int) $membershipSummary['expired'] }}</td>
            <td>Total clientes: {{ (int) $membershipSummary['total_clients'] }}</td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Asistencias</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($attendanceByDay as $row)
            <tr>
                <td>{{ $row->date }}</td>
                <td>{{ (int) $row->attendances_count }}</td>
            </tr>
        @empty
            <tr><td colspan="2">Sin asistencias en este rango.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Detalle de Movimientos</h2>
    @if (!empty($isPdfDetailTruncated))
        <p class="muted">
            El detalle fue limitado a {{ (int) ($pdfMaxDetailRows ?? 0) }} de {{ (int) ($movementsCount ?? 0) }} filas para proteger memoria.
            Use exportación CSV para obtener el dataset completo.
        </p>
    @endif
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Método</th>
            <th>Monto</th>
            <th>Cliente</th>
            <th>Usuario</th>
            <th>Descripción</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($movements as $movement)
            <tr>
                <td>{{ $movement->id }}</td>
                <td>{{ $movement->occurred_at?->format('Y-m-d H:i') }}</td>
                <td>{{ match ($movement->type) { 'income' => 'Ingreso', 'expense' => 'Egreso', default => $movement->type } }}</td>
                <td>{{ match ($movement->method) { 'cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia', default => $movement->method } }}</td>
                <td>{{ $currencyFormatter::format((float) $movement->amount, $appCurrencyCode) }}</td>
                <td>{{ $movement->membership?->client?->full_name ?? '-' }}</td>
                <td>{{ $movement->createdBy?->name ?? '-' }}</td>
                <td>{{ $movement->description ?: '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="8">Sin movimientos en este rango.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
