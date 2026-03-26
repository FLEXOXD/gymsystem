<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
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
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $variants = [
        'primary' => 'ui-button-primary',
        'secondary' => 'ui-button-secondary',
        'success' => 'ui-button-success',
        'danger' => 'ui-button-danger',
        'muted' => 'ui-button-muted',
        'ghost' => 'ui-button-ghost',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs font-bold',
        'md' => 'px-4 py-2 text-sm font-semibold',
        'lg' => 'px-5 py-3 text-base font-bold',
    ];
    $buttonClass = 'ui-button '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->class($buttonClass)); ?>>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button type="<?php echo e($type); ?>" <?php echo e($attributes->class($buttonClass)); ?>>
        <?php echo e($slot); ?>

    </button>
<?php endif; ?>

<?php /**PATH C:\laragon\www\gymsystem\resources\views/components/ui/button.blade.php ENDPATH**/ ?>