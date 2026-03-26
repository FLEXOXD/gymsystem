<?php
    $canManagePromotions = (bool) ($canManagePromotions ?? false);
?>

<div x-cloak
     x-show="membershipModalOpen"
     x-transition.opacity
     class="ui-modal-backdrop"
     x-on:click.self="closeMembershipModal()"
     x-on:keydown.escape.window="closeMembershipModal()">
    <div class="ui-modal-shell max-w-4xl" x-transition.scale.origin.top>
        <form method="POST" action="<?php echo e(route('memberships.store')); ?>" class="flex min-h-0 flex-1 flex-col space-y-0">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="client_id" value="<?php echo e($client->id); ?>">
            <input type="hidden" name="active_tab" value="membership">
            <input type="hidden" name="membership_form_mode" value="create">

            <header class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
                <div>
                    <h3 class="text-xl font-black text-slate-100">Cobrar / Renovar membresía</h3>
                    <p class="mt-1 text-sm text-slate-400">Crea la membresía y registra el ingreso en caja con la fecha real del pago.</p>
                </div>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','xOn:click' => 'closeMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','x-on:click' => 'closeMembershipModal()']); ?>Cerrar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </header>

            <div class="ui-modal-scroll-body space-y-5 px-6 py-5">
                <?php $__errorArgs = ['cash'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="rounded-xl border-2 border-rose-400/80 bg-rose-500/20 p-4 text-rose-100 shadow-lg">
                        <p class="text-sm font-black uppercase tracking-wide">Debe abrir caja para cobrar</p>
                        <p class="mt-1 text-sm font-semibold"><?php echo e($message); ?></p>
                        <p class="mt-2 text-xs text-rose-100/90">Abre un turno en caja y vuelve a intentar el cobro de membresía.</p>
                        <div class="mt-3">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'secondary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'secondary','size' => 'sm']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
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
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <div class="rounded-xl border border-cyan-500/30 bg-cyan-500/10 p-4 text-sm text-cyan-100">
                    <p class="font-semibold">Uso recomendado</p>
                    <p class="mt-1 text-cyan-50/90">Si el cliente pagó antes pero lo registras hoy, cambia "Fecha real de pago" para que caja quede alineada con el historial real.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Plan</span>
                        <select name="plan_id" required class="ui-input" x-ref="membershipPlanInput">
                            <?php if($plans->isEmpty()): ?>
                                <option value="">Sin planes activos</option>
                            <?php endif; ?>
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($plan->id); ?>" <?php if((string) old('plan_id') === (string) $plan->id): echo 'selected'; endif; ?>>
                                    <?php echo e($plan->name); ?> (<?php echo e(\App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months)); ?>, <?php echo e(\App\Support\Currency::format((float) $plan->price, $appCurrencyCode)); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Inicio de membresía</span>
                        <input type="date"
                               name="starts_at"
                               value="<?php echo e(old('starts_at', now()->toDateString())); ?>"
                               required
                               class="ui-input">
                        <?php $__errorArgs = ['starts_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Estado base</span>
                        <select name="status" class="ui-input">
                            <option value="active" <?php if(old('status', 'active') === 'active'): echo 'selected'; endif; ?>>Activo</option>
                            <option value="expired" <?php if(old('status') === 'expired'): echo 'selected'; endif; ?>>Vencido</option>
                            <option value="cancelled" <?php if(old('status') === 'cancelled'): echo 'selected'; endif; ?>>Cancelado</option>
                        </select>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Método de pago</span>
                        <select name="payment_method" required class="ui-input">
                            <option value="">Seleccione</option>
                            <option value="cash" <?php if(old('payment_method') === 'cash'): echo 'selected'; endif; ?>>Efectivo</option>
                            <option value="card" <?php if(old('payment_method') === 'card'): echo 'selected'; endif; ?>>Tarjeta</option>
                            <option value="transfer" <?php if(old('payment_method') === 'transfer'): echo 'selected'; endif; ?>>Transferencia</option>
                        </select>
                        <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                        <span>Fecha real de pago</span>
                        <input type="date"
                               name="payment_received_at"
                               value="<?php echo e(old('payment_received_at', now()->toDateString())); ?>"
                               max="<?php echo e(now()->toDateString()); ?>"
                               class="ui-input">
                        <p class="text-xs text-slate-400">Se guarda en caja como `occurred_at`. Déjalo en hoy si cobras y registras el mismo día.</p>
                        <?php $__errorArgs = ['payment_received_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <?php if($canManagePromotions): ?>
                        <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2 xl:col-span-4">
                            <span>Promoción (opcional)</span>
                            <select name="promotion_id" class="ui-input" x-ref="membershipPromotionInput">
                                <option value="">Sin promoción</option>
                                <?php $__currentLoopData = ($promotions ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $promoTypeLabel = match ($promotion->type) {
                                            'percentage' => '-'.$promotion->value.'%',
                                            'fixed' => '-'.\App\Support\Currency::format((float) $promotion->value, $appCurrencyCode),
                                            'final_price' => 'Precio final '.\App\Support\Currency::format((float) $promotion->value, $appCurrencyCode),
                                            'bonus_days' => '+'.(int) $promotion->value.' días',
                                            'two_for_one' => '2x1',
                                            'bring_friend' => 'Trae a un amigo',
                                            default => (string) $promotion->type,
                                        };
                                        $planScopeLabel = $promotion->plan_id
                                            ? ' - Plan '.($promotion->plan?->name ?? '#'.$promotion->plan_id)
                                            : ' - Todos los planes';
                                    ?>
                                    <option value="<?php echo e($promotion->id); ?>" <?php if((string) old('promotion_id') === (string) $promotion->id): echo 'selected'; endif; ?>>
                                        <?php echo e($promotion->name); ?> (<?php echo e($promoTypeLabel); ?><?php echo e($planScopeLabel); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="text-xs text-slate-400">La promoción válida precio final y días extra automáticamente.</p>
                            <?php $__errorArgs = ['promotion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                    <?php else: ?>
                        <div class="rounded-lg border border-amber-500/40 bg-amber-500/10 p-2 text-xs text-amber-200 md:col-span-2 xl:col-span-4">
                            Promociones no disponibles en tu plan actual.
                            <?php $__errorArgs = ['promotion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 font-semibold text-rose-200"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if($plans->isEmpty()): ?>
                    <div class="rounded-xl border border-amber-500/40 bg-amber-500/10 p-3 text-xs text-amber-200">
                        No hay planes activos disponibles. Crea un plan antes de cobrar membresías.
                    </div>
                <?php endif; ?>
            </div>

            <footer class="ui-modal-sticky-footer flex justify-end gap-3 px-6 py-4">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','xOn:click' => 'closeMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','x-on:click' => 'closeMembershipModal()']); ?>Cancelar <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'success','disabled' => $plans->isEmpty()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'success','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($plans->isEmpty())]); ?>Cobrar y guardar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </footer>
        </form>
    </div>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/partials/_modal_membership.blade.php ENDPATH**/ ?>