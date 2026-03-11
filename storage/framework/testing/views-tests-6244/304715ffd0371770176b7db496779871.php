<div class="grid gap-6 xl:grid-cols-12">
    <div class="space-y-6 xl:col-span-8">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Estado de membresía','subtitle' => 'Vista rápida para recepción, cobro y ajustes.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Estado de membresía','subtitle' => 'Vista rápida para recepción, cobro y ajustes.']); ?>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Estado actual</p>
                    <div class="mt-2 flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $membershipBadgeVariant]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($membershipBadgeVariant)]); ?><?php echo e($membershipBadgeText); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                        <?php if($latestMembership): ?>
                            <span class="text-sm text-slate-700 dark:text-slate-300">Plan: <?php echo e($latestMembership->plan?->name ?? 'Sin plan'); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="mt-3 text-sm text-slate-700 dark:text-slate-300"><?php echo e($membershipDateLabel); ?>: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipDateValue); ?></span></p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300"><?php echo e($membershipCountdownLabel); ?>: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipCountdownValue); ?></span></p>
                    <?php if($latestMembership): ?>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Inicio: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipStartsLabel); ?></span></p>
                    <?php endif; ?>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'success','class' => 'flex-1 min-w-[170px]','xOn:click' => 'openMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'success','class' => 'flex-1 min-w-[170px]','x-on:click' => 'openMembershipModal()']); ?>
                            Cobrar / Renovar
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>

                        <?php if(! empty($canAdjustMemberships) && $latestMembership): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','class' => 'flex-1 min-w-[170px]','xOn:click' => 'openMembershipAdjustmentModal('.e((int) $latestMembership->id).')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','class' => 'flex-1 min-w-[170px]','x-on:click' => 'openMembershipAdjustmentModal('.e((int) $latestMembership->id).')']); ?>
                                Ajustar membresía
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Cliente</p>
                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">Estado general: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($statusLabels[$client->status] ?? $client->status); ?></span></p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Teléfono: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($client->phone ?: '-'); ?></span></p>
                    <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Última asistencia: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($lastAttendanceLabel); ?></span></p>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-2">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Últimas asistencias','subtitle' => 'Últimos ingresos registrados.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Últimas asistencias','subtitle' => 'Últimos ingresos registrados.']); ?>
                <?php if($attendancePreview->isNotEmpty()): ?>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $attendancePreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm dark:border-white/10 dark:bg-slate-900/40">
                                <span class="text-slate-700 dark:text-slate-200"><?php echo e($attendance->date?->translatedFormat('d M Y') ?? '-'); ?></span>
                                <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($attendance->time ? mb_substr((string) $attendance->time, 0, 5) : '--:--'); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                        <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12h18"/>
                                <path d="M12 3v18"/>
                            </svg>
                        </div>
                        <p class="font-semibold">Aún no hay asistencias registradas.</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cuando el cliente haga check-in aparecerá aquí.</p>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reception.index'),'variant' => 'ghost','size' => 'sm','class' => 'mt-3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reception.index')),'variant' => 'ghost','size' => 'sm','class' => 'mt-3']); ?>Ir a recepción <?php echo $__env->renderComponent(); ?>
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
                <?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Últimos pagos','subtitle' => 'Movimientos de caja vinculados al cliente.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Últimos pagos','subtitle' => 'Movimientos de caja vinculados al cliente.']); ?>
                <?php if($paymentsPreview->isNotEmpty()): ?>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $paymentsPreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm dark:border-white/10 dark:bg-slate-900/40">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900 dark:text-slate-100"><?php echo e($movement->membership?->plan?->name ?? 'Pago membresía'); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($movement->occurred_at?->format('Y-m-d H:i') ?? '-'); ?></p>
                                </div>
                                <span class="font-bold text-emerald-700 dark:text-emerald-300"><?php echo e(\App\Support\Currency::format((float) $movement->amount, $appCurrencyCode)); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
                        <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 7h16"/>
                                <path d="M4 12h16"/>
                                <path d="M4 17h16"/>
                            </svg>
                        </div>
                        <p class="font-semibold">Sin pagos vinculados todavía.</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Cobra una membresía para registrar el primer pago.</p>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','class' => 'mt-3','xOn:click' => 'openMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','class' => 'mt-3','x-on:click' => 'openMembershipModal()']); ?>Cobrar / Renovar <?php echo $__env->renderComponent(); ?>
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
                <?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
        </div>
    </div>

    <div class="space-y-6 xl:col-span-4">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Perfil del cliente','subtitle' => 'Datos base de contacto.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Perfil del cliente','subtitle' => 'Datos base de contacto.']); ?>
            <div class="space-y-4">
                <div class="h-40 overflow-hidden rounded-xl border border-slate-300 bg-slate-100 dark:border-white/10 dark:bg-slate-900/50">
                    <?php if($photoUrl): ?>
                        <img src="<?php echo e($photoUrl); ?>" alt="Foto cliente" class="h-full w-full object-cover">
                    <?php else: ?>
                        <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-slate-600 dark:text-slate-400">Sin foto</div>
                    <?php endif; ?>
                </div>

                <form method="POST"
                      action="<?php echo e(route('clients.photo.update', $client->id)); ?>"
                      enctype="multipart/form-data"
                      class="space-y-2">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="file"
                           name="photo"
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           class="ui-input">
                    <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs font-semibold text-rose-500 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'ghost','size' => 'sm','class' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'ghost','size' => 'sm','class' => 'w-full']); ?>Guardar foto <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </form>

                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Documento</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($client->document_number); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Teléfono</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($client->phone ?: '-'); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Estado</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($statusLabels[$client->status] ?? $client->status); ?></dd>
                    </div>
                </dl>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Trazabilidad','subtitle' => 'Quien dio de alta al cliente y quien lo gestiono por ultima vez.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Trazabilidad','subtitle' => 'Quien dio de alta al cliente y quien lo gestiono por ultima vez.']); ?>
            <div class="space-y-4 text-sm">
                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Creado por</p>
                    <p class="mt-2 font-semibold text-slate-900 dark:text-slate-100"><?php echo e($clientCreationAudit['display'] ?? 'Sin registro'); ?></p>
                    <?php if(! empty($clientCreationAudit['state'])): ?>
                        <div class="mt-2">
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $clientCreationAudit['state_variant'] ?? 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientCreationAudit['state_variant'] ?? 'muted')]); ?><?php echo e($clientCreationAudit['state']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Fecha alta: <?php echo e($clientCreationAudit['timestamp_label'] ?? 'Sin fecha'); ?></p>
                </div>

                <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">Ultima gestion</p>
                    <p class="mt-2 font-semibold text-slate-900 dark:text-slate-100"><?php echo e($clientLastManagementAudit['display'] ?? 'Sin registro'); ?></p>
                    <?php if(! empty($clientLastManagementAudit['state'])): ?>
                        <div class="mt-2">
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $clientLastManagementAudit['state_variant'] ?? 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientLastManagementAudit['state_variant'] ?? 'muted')]); ?><?php echo e($clientLastManagementAudit['state']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Fecha gestion: <?php echo e($clientLastManagementAudit['timestamp_label'] ?? 'Sin fecha'); ?></p>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Accesos rápidos','subtitle' => 'Atajos operativos compactos.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Accesos rápidos','subtitle' => 'Atajos operativos compactos.']); ?>
            <div class="space-y-2">
                <a href="<?php echo e(route('reception.index')); ?>" class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800">
                    <span>Ir a recepción</span>
                    <span class="text-slate-500 dark:text-slate-400">-></span>
                </a>

                <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('attendance')">
                    <span>Ver asistencias</span>
                    <span class="text-slate-500 dark:text-slate-400">Tab</span>
                </button>

                <button type="button" class="text-xs font-semibold text-cyan-700 underline dark:text-cyan-300" x-on:click="quickMoreOpen = !quickMoreOpen">
                    Ver más
                </button>

                <div x-cloak x-show="quickMoreOpen" class="space-y-2">
                    <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('membership')">
                        <span>Ver membresías y pagos</span>
                        <span class="text-slate-500 dark:text-slate-400">Tab</span>
                    </button>

                    <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('credentials')">
                        <span>Ver credenciales</span>
                        <span class="text-slate-500 dark:text-slate-400">Tab</span>
                    </button>

                    <?php if(! empty($canManageClientAccounts)): ?>
                        <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setTab('app_access')">
                            <span>Usuario app cliente</span>
                            <span class="text-slate-500 dark:text-slate-400">Tab</span>
                        </button>
                    <?php endif; ?>

                    <?php if(! empty($canAdjustMemberships) && $latestMembership): ?>
                        <button type="button" class="flex w-full items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-left text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="openMembershipAdjustmentModal(<?php echo e((int) $latestMembership->id); ?>)">
                            <span>Ajustar membresía</span>
                            <span class="text-slate-500 dark:text-slate-400">Modal</span>
                        </button>
                    <?php endif; ?>

                    <a href="<?php echo e(route('cash.index')); ?>" class="flex items-center justify-between rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-800 transition hover:bg-slate-200 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-800">
                        <span>Ir a caja</span>
                        <span class="text-slate-500 dark:text-slate-400">-></span>
                    </a>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/partials/_tab_summary.blade.php ENDPATH**/ ?>