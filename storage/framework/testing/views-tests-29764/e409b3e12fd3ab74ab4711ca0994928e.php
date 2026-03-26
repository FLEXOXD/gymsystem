<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'subtitle' => null,
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
    'title' => null,
    'subtitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section <?php echo e($attributes->class('ui-card')); ?>>
    <?php if($title || $subtitle): ?>
        <header class="mb-4">
            <?php if($title): ?>
                <h2 class="ui-heading text-lg md:text-xl"><?php echo e($title); ?></h2>
            <?php endif; ?>
            <?php if($subtitle): ?>
                <p class="ui-muted mt-1 text-sm"><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </header>
    <?php endif; ?>

    <?php echo e($slot); ?>

</section>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/components/ui/card.blade.php ENDPATH**/ ?>