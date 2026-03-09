<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'default',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'variant' => 'default',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<span <?php echo e($attributes->class("ui-badge {$palette}")); ?>>
    <?php echo e($translatedText); ?>

</span>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/components/ui/badge.blade.php ENDPATH**/ ?>