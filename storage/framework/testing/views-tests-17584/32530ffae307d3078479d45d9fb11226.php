<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'info',
    'title' => null,
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
    'type' => 'info',
    'title' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $palette = match ($type) {
        'success' => 'ui-alert-success',
        'danger' => 'ui-alert-danger',
        'warning' => 'ui-alert-warning',
        default => 'ui-alert-info',
    };
?>

<div <?php echo e($attributes->class("ui-alert {$palette}")); ?>>
    <?php if($title): ?>
        <p class="mb-1 text-xs font-bold uppercase tracking-wider"><?php echo e($title); ?></p>
    <?php endif; ?>
    <div><?php echo e($slot); ?></div>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/components/ui/alert.blade.php ENDPATH**/ ?>