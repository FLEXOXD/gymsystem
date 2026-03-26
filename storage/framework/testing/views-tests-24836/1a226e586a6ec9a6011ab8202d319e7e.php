<?php
    $closureRows = $sessions ?? collect();
    $currencyFormatter = $currencyFormatter ?? \App\Support\Currency::class;
    $currencyCode = $currencyCode ?? ($appCurrencyCode ?? null);
?>

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
        <?php $__empty_1 = true; $__currentLoopData = $closureRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $difference = (float) ($session->difference ?? 0);
                $closedByLabel = $session->wasAutoClosedAtMidnight()
                    ? 'Sistema'
                    : ($session->closedBy?->name ?? '-');
            ?>
            <tr>
                <td><?php echo e($session->closed_at?->format('Y-m-d') ?? '-'); ?></td>
                <td><?php echo e($session->closed_at?->format('H:i') ?? '-'); ?></td>
                <td>
                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $session->wasAutoClosedAtMidnight() ? 'warning' : 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($session->wasAutoClosedAtMidnight() ? 'warning' : 'info')]); ?>
                        <?php echo e($session->closeSourceLabel()); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                </td>
                <td><?php echo e($session->closeMessage()); ?></td>
                <td class="font-semibold <?php echo e($difference > 0 ? 'text-emerald-700 dark:text-emerald-300' : ($difference < 0 ? 'text-rose-700 dark:text-rose-300' : 'text-slate-700 dark:text-slate-200')); ?>">
                    <?php echo e($currencyFormatter::format($difference, $currencyCode)); ?>

                </td>
                <td><?php echo e($session->difference_reason ?: 'Sin novedad'); ?></td>
                <td><?php echo e($session->closing_notes ?: '-'); ?></td>
                <td><?php echo e($closedByLabel); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="8" class="text-center text-sm text-slate-500 dark:text-slate-300">
                    Aun no hay cierres registrados.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/cash/partials/closure-history.blade.php ENDPATH**/ ?>