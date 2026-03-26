<?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'client-hero-card p-4 sm:p-6 lg:p-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'client-hero-card p-4 sm:p-6 lg:p-7']); ?>
    <div class="client-hero-layout">
        <div class="min-w-0 space-y-4">
            <div class="flex min-w-0 items-start gap-3 sm:gap-5">
                <div class="h-14 w-14 overflow-hidden rounded-2xl border border-slate-300 bg-slate-100 shadow-sm dark:border-white/10 dark:bg-slate-900/40 sm:h-16 sm:w-16">
                    <?php if($photoUrl): ?>
                        <img src="<?php echo e($photoUrl); ?>" alt="<?php echo e($client->full_name); ?>" class="h-full w-full object-cover">
                    <?php else: ?>
                        <div class="flex h-full w-full items-center justify-center text-lg font-black uppercase text-slate-700 dark:text-slate-200">
                            <?php echo e(mb_strtoupper(mb_substr($client->first_name, 0, 1).mb_substr($client->last_name, 0, 1))); ?>

                        </div>
                    <?php endif; ?>
                </div>

                <div class="min-w-0 flex-1 space-y-2">
                    <div class="min-w-0 max-w-3xl">
                        <h2 class="ui-heading text-xl leading-tight sm:text-2xl"><?php echo e($client->full_name); ?></h2>
                        <p class="ui-muted mt-1 text-sm">Documento: <?php echo e($client->document_number); ?></p>
                    </div>
                </div>
            </div>

            <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label">Membresía</span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipLabel); ?></p>
                </div>
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label"><?php echo e($membershipDateLabel); ?></span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipDateValue); ?></p>
                </div>
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label"><?php echo e($membershipCountdownLabel); ?></span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipCountdownValue); ?></p>
                </div>
                <div class="client-hero-stat">
                    <span class="client-hero-stat-label">Última asistencia</span>
                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($lastAttendanceLabel); ?></p>
                </div>
            </div>
        </div>

        <div class="client-hero-actions">
            <div class="client-hero-status">
                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $membershipBadgeVariant,'class' => 'px-4 py-2 text-[11px] font-extrabold tracking-[0.12em]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($membershipBadgeVariant),'class' => 'px-4 py-2 text-[11px] font-extrabold tracking-[0.12em]']); ?>
                    <?php echo e($membershipBadgeText); ?>

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
            </div>

            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'success','class' => 'w-full justify-center px-4 py-3 text-sm font-bold','xOn:click' => 'openMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'success','class' => 'w-full justify-center px-4 py-3 text-sm font-bold','x-on:click' => 'openMembershipModal()']); ?>
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

            <?php if(! empty($canShowProgress) && ! empty($progressTabUrl)): ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $progressTabUrl,'variant' => 'secondary','class' => 'w-full justify-center px-4 py-3 text-sm font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($progressTabUrl),'variant' => 'secondary','class' => 'w-full justify-center px-4 py-3 text-sm font-bold']); ?>
                    Ver rendimiento
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

            <div class="relative client-hero-actions-full" x-on:keydown.escape.window="actionsOpen = false" x-on:click.outside="actionsOpen = false">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','class' => 'w-full justify-between px-4 py-3 text-sm font-bold','xBind:ariaExpanded' => 'actionsOpen.toString()','xOn:click' => 'actionsOpen = !actionsOpen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','class' => 'w-full justify-between px-4 py-3 text-sm font-bold','x-bind:aria-expanded' => 'actionsOpen.toString()','x-on:click' => 'actionsOpen = !actionsOpen']); ?>
                    <span>Más acciones</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 transition"
                         x-bind:class="actionsOpen ? 'rotate-180' : ''"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
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

                <div x-cloak
                     x-show="actionsOpen"
                     x-transition.origin.top.right
                     class="client-action-popover absolute right-0 z-30 mt-2 rounded-2xl p-2">
                    <div class="space-y-1.5">
                    <form method="POST" action="<?php echo e(route('client-credentials.generate-qr', $client->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="client-action-item"
                                x-on:click="actionsOpen = false">
                            <span>Generar QR</span>
                        </button>
                    </form>

                    <button type="button"
                            class="client-action-item"
                            x-on:click="actionsOpen = false; openRfidModal()">
                        <span>Asignar RFID</span>
                    </button>

                    <?php if(! empty($canAdjustMemberships) && $latestMembership): ?>
                        <button type="button"
                                class="client-action-item"
                                x-on:click="actionsOpen = false; openMembershipAdjustmentModal(<?php echo e((int) $latestMembership->id); ?>)">
                            <span>Ajustar membresía</span>
                        </button>
                    <?php endif; ?>

                    <a href="<?php echo e(route('clients.card', $client->id)); ?>"
                       target="_blank"
                       rel="noopener"
                       class="client-action-item"
                       x-on:click="actionsOpen = false">
                        <span>Imprimir tarjeta</span>
                    </a>

                    <a href="<?php echo e(route('reception.index')); ?>"
                       class="client-action-item"
                       x-on:click="actionsOpen = false">
                        <span>Ir a recepción</span>
                    </a>
                    </div>
                </div>
            </div>
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
<?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/partials/_header.blade.php ENDPATH**/ ?>