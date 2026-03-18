<div id="high-amount-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="highAmountTitle">
    <div class="ui-modal-panel max-w-md">
        <h3 id="highAmountTitle" class="ui-heading text-lg">Confirmar monto alto</h3>
        <p class="mt-2 text-sm ui-muted">Estas registrando un movimiento alto: <strong id="high-amount-value"><?php echo e($currencySymbol); ?>0.00</strong></p>
        <p class="mt-1 text-xs ui-muted">Verifica tipo y método antes de continuar.</p>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" class="ui-button ui-button-ghost" data-close-high-modal>Cancelar</button>
            <button type="button" class="ui-button ui-button-primary" id="confirm-high-amount">Confirmar y guardar</button>
        </div>
    </div>
</div>

<div id="difference-approval-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="differenceApprovalTitle">
    <div class="ui-modal-panel max-w-md">
        <h3 id="differenceApprovalTitle" class="ui-heading text-lg">Aprobación supervisor</h3>
        <p class="mt-2 text-sm ui-muted">El cierre tiene diferencia. Solo Admin puede aprobarlo.</p>
        <label class="mt-3 block space-y-1 text-sm font-semibold ui-muted">
            <span>Password/PIN admin</span>
            <input id="difference-approval-password" type="password" class="ui-input" autocomplete="new-password">
        </label>
        <p id="difference-approval-error" class="mt-2 hidden text-xs font-semibold text-rose-600 dark:text-rose-300">Ingresa password/PIN para continuar.</p>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" class="ui-button ui-button-ghost" data-close-difference-modal>Cancelar</button>
            <button type="button" class="ui-button ui-button-danger" id="confirm-close-with-diff">Aprobar cierre</button>
        </div>
    </div>
</div>

<div id="void-movement-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="voidMovementTitle">
    <div class="ui-modal-panel max-w-md">
        <h3 id="voidMovementTitle" class="ui-heading text-lg">Anular movimiento</h3>
        <p class="mt-2 text-sm ui-muted">Movimiento: <strong id="void-movement-label">-</strong></p>

        <form id="void-movement-form" method="POST" action="<?php echo e($voidRouteTemplate); ?>" class="mt-3 space-y-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Motivo de anulación (obligatorio)</span>
                <textarea name="void_reason" id="void-reason" rows="3" required class="ui-input" placeholder="Ej: ingreso duplicado o error de caja."></textarea>
            </label>

            <?php if(! $routeHasVoidMovement): ?>
                <p class="ui-alert ui-alert-danger text-xs">Falta route `cash.movements.void` en backend.</p>
            <?php endif; ?>

            <div class="flex justify-end gap-2">
                <button type="button" class="ui-button ui-button-ghost" data-close-void-modal>Cancelar</button>
                <button type="submit" class="ui-button ui-button-danger" <?php if(! $routeHasVoidMovement): echo 'disabled'; endif; ?>>Anular movimiento</button>
            </div>
        </form>
    </div>
</div>

<div id="monthly-movements-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="monthlyMovementsTitle">
    <div class="ui-modal-panel max-w-7xl p-0">
        <div class="ui-modal-shell">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
                <div>
                    <h3 id="monthlyMovementsTitle" class="ui-heading text-lg text-slate-100">
                        <?php echo e($isCashierScoped ? 'Tus movimientos del mes' : 'Movimientos del mes'); ?>

                    </h3>
                    <p class="mt-1 text-sm text-slate-300">
                        Revisión completa del mes actual sin salir de la caja.
                    </p>
                </div>
                <button type="button" class="ui-button ui-button-ghost" data-close-monthly-modal>Cerrar</button>
            </div>

            <div class="ui-modal-scroll-body px-5 py-4">
                <?php echo $__env->make('cash.partials.monthly-movements-content', [
                    'monthlyMovements' => $monthlyMovements,
                    'monthlySummary' => $monthlySummary,
                    'monthStart' => $monthStart,
                    'monthEnd' => $monthEnd,
                    'isCashierScoped' => $isCashierScoped,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <div class="ui-modal-sticky-footer flex justify-end gap-2 px-5 pt-4">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','dataCloseMonthlyModal' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','data-close-monthly-modal' => true]); ?>Cerrar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index'),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index')),'variant' => 'primary']); ?>Cobrar membresía <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/cash/partials/session-modals.blade.php ENDPATH**/ ?>