@props([
    'variant' => 'default',
])

@php
    $palette = match ($variant) {
        'success' => 'ui-badge-success',
        'danger' => 'ui-badge-danger',
        'warning' => 'ui-badge-warning',
        'info' => 'ui-badge-info',
        'muted' => 'ui-badge-muted',
        default => 'ui-badge-muted',
    };

    $rawSlot = trim((string) $slot);
    $normalized = mb_strtolower(strip_tags($rawSlot));
    $translations = [
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'expired' => 'Vencido',
        'cancelled' => 'Cancelado',
        'open' => 'Abierto',
        'closed' => 'Cerrado',
        'income' => 'Ingreso',
        'expense' => 'Egreso',
        'cash' => 'Efectivo',
        'card' => 'Tarjeta',
        'transfer' => 'Transferencia',
        'sent' => 'Enviado',
        'skipped' => 'Omitido',
        'grace' => 'Gracia',
        'suspended' => 'Suspendido',
        'document' => 'Documento',
        'qr' => 'QR',
        'rfid' => 'RFID',
    ];

    $translatedText = str_contains($rawSlot, '<')
        ? $rawSlot
        : ($translations[$normalized] ?? $rawSlot);
@endphp

<span {{ $attributes->class("ui-badge {$palette}") }}>
    {{ $translatedText }}
</span>
