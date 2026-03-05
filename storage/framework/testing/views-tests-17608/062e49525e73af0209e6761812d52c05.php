<?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'p-6 lg:p-7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'p-6 lg:p-7']); ?>
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex min-w-0 items-start gap-4 lg:gap-5">
            <div class="h-16 w-16 overflow-hidden rounded-2xl border border-slate-300 bg-slate-100 dark:border-white/10 dark:bg-slate-900/40">
                <?php if($photoUrl): ?>
                    <img src="<?php echo e($photoUrl); ?>" alt="<?php echo e($client->full_name); ?>" class="h-full w-full object-cover">
                <?php else: ?>
                    <div class="flex h-full w-full items-center justify-center text-lg font-black uppercase text-slate-700 dark:text-slate-200">
                        <?php echo e(mb_strtoupper(mb_substr($client->first_name, 0, 1).mb_substr($client->last_name, 0, 1))); ?>

                    </div>
                <?php endif; ?>
            </div>

            <div class="min-w-0 space-y-2.5">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="ui-heading truncate text-2xl"><?php echo e($client->full_name); ?></h2>
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
                </div>

                <p class="ui-muted text-sm">Documento: <?php echo e($client->document_number); ?></p>

                <div class="flex max-w-4xl flex-wrap items-center gap-x-2 gap-y-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    <span>Membresía: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipLabel); ?></span></span>
                    <span class="text-slate-500">|</span>
                    <span>Vence: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membershipEndsLabel); ?></span></span>
                    <span class="text-slate-500">|</span>
                    <span>Restan: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($remainingLabel); ?></span></span>
                    <span class="text-slate-500">|</span>
                    <span>Ultima asistencia: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($lastAttendanceLabel); ?></span></span>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 flex-wrap items-center gap-2.5">
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'success','class' => 'px-5 py-2.5','xOn:click' => 'openMembershipModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'success','class' => 'px-5 py-2.5','x-on:click' => 'openMembershipModal()']); ?>
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

            <div class="relative" x-on:keydown.escape.window="actionsOpen = false" x-on:click.outside="actionsOpen = false">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','class' => 'px-4 py-2 text-xs font-bold','xOn:click' => 'actionsOpen = !actionsOpen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','class' => 'px-4 py-2 text-xs font-bold','x-on:click' => 'actionsOpen = !actionsOpen']); ?>
                    Acciones v
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
                     class="absolute right-0 z-30 mt-2 w-64 rounded-xl border border-slate-700 bg-slate-950 p-2 shadow-xl">
                    <form method="POST" action="<?php echo e(route('client-credentials.generate-qr', $client->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm text-slate-200 transition hover:bg-slate-800"
                                x-on:click="actionsOpen = false">
                            <span>Generar QR</span>
                            <span class="text-xs text-slate-400">POST</span>
                        </button>
                    </form>

                    <button type="button"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm text-slate-200 transition hover:bg-slate-800"
                            x-on:click="openRfidModal()">
                        <span>Asignar RFID</span>
                        <span class="text-xs text-slate-400">Modal</span>
                    </button>

                    <a href="<?php echo e(route('clients.card', $client->id)); ?>"
                       target="_blank"
                       rel="noopener"
                       class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-slate-800"
                       x-on:click="actionsOpen = false">
                        <span>Imprimir tarjeta</span>
                        <span class="text-xs text-slate-400">Nueva pestaña</span>
                    </a>

                    <a href="<?php echo e(route('reception.index')); ?>"
                       class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-slate-800"
                       x-on:click="actionsOpen = false">
                        <span>Ir a recepción</span>
                        <span class="text-xs text-slate-400">Ir</span>
                    </a>
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