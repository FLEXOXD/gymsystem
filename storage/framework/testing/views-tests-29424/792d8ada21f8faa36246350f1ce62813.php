<?php $__env->startSection('title', 'Cajeros'); ?>
<?php $__env->startSection('page-title', 'Gestion de cajeros'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $roleSchemaReady = (bool) ($roleSchemaReady ?? true);
        $schemaErrorMessage = trim((string) ($schemaErrorMessage ?? ''));
        $isGlobalStaffView = (bool) ($isGlobalStaffView ?? false);
        $inactiveCashiers = (int) ($inactiveCashiers ?? 0);
        $scopeGymCount = (int) ($scopeGymCount ?? 1);
        $totalCashiers = (int) ($totalCashiers ?? ($cashiers instanceof \Illuminate\Support\Collection ? $cashiers->count() : 0));
        $activeCashiers = (int) ($activeCashiers ?? $currentCashiers ?? 0);
        $currentPlanLabel = match ($currentPlanKey ?? '') {
            'basico' => 'Basico',
            'profesional' => 'Profesional',
            'premium' => 'Premium',
            'sucursales' => 'Sucursales',
            default => strtoupper((string) ($currentPlanKey ?? '-')),
        };
        $contextGym = (string) (request()->route('contextGym') ?? '');
        $contextParams = $contextGym !== '' ? ['contextGym' => $contextGym] : [];
        if ($isGlobalStaffView) {
            $contextParams['scope'] = 'global';
        }
    ?>

    <div class="space-y-5">
        <?php if(! $roleSchemaReady): ?>
            <div class="ui-alert ui-alert-danger text-sm">
                <?php echo e($schemaErrorMessage !== '' ? $schemaErrorMessage : 'Falta la migracion de roles de usuarios.'); ?>

            </div>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => $isGlobalStaffView ? 'Resumen global de cajeros' : 'Cupo de cajeros','subtitle' => $isGlobalStaffView
                ? 'Vista consolidada de cajeros en todas las sedes vinculadas (solo lectura).'
                : 'Limites aplicados segun el plan activo de esta sede.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalStaffView ? 'Resumen global de cajeros' : 'Cupo de cajeros'),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalStaffView
                ? 'Vista consolidada de cajeros en todas las sedes vinculadas (solo lectura).'
                : 'Limites aplicados segun el plan activo de esta sede.')]); ?>
            <div class="grid gap-3 sm:grid-cols-4">
                <article class="rounded-xl border border-slate-300/60 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/60">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Plan actual</p>
                    <p class="mt-1 text-lg font-black text-slate-900 dark:text-slate-100"><?php echo e($currentPlanLabel); ?></p>
                </article>

                <?php if($isGlobalStaffView): ?>
                    <article class="rounded-xl border border-indigo-300/50 bg-indigo-50 p-3 dark:border-indigo-500/40 dark:bg-indigo-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-200">Sedes en alcance</p>
                        <p class="mt-1 text-lg font-black text-indigo-900 dark:text-indigo-100"><?php echo e($scopeGymCount); ?></p>
                    </article>
                    <article class="rounded-xl border border-cyan-300/50 bg-cyan-50 p-3 dark:border-cyan-500/40 dark:bg-cyan-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-200">Cajeros activos</p>
                        <p class="mt-1 text-lg font-black text-cyan-900 dark:text-cyan-100"><?php echo e($activeCashiers); ?></p>
                    </article>
                    <article class="rounded-xl border border-amber-300/50 bg-amber-50 p-3 dark:border-amber-500/40 dark:bg-amber-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-700 dark:text-amber-200">Cajeros inactivos</p>
                        <p class="mt-1 text-lg font-black text-amber-900 dark:text-amber-100"><?php echo e($inactiveCashiers); ?></p>
                    </article>
                <?php else: ?>
                    <article class="rounded-xl border border-cyan-300/50 bg-cyan-50 p-3 dark:border-cyan-500/40 dark:bg-cyan-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-200">Cajeros activos</p>
                        <p class="mt-1 text-lg font-black text-cyan-900 dark:text-cyan-100"><?php echo e((int) $currentCashiers); ?></p>
                    </article>
                    <article class="rounded-xl border border-emerald-300/50 bg-emerald-50 p-3 dark:border-emerald-500/40 dark:bg-emerald-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-200">Cupo restante</p>
                        <p class="mt-1 text-lg font-black text-emerald-900 dark:text-emerald-100"><?php echo e((int) $remainingCashiers); ?> / <?php echo e((int) $maxCashiers); ?></p>
                    </article>
                    <article class="rounded-xl border border-amber-300/50 bg-amber-50 p-3 dark:border-amber-500/40 dark:bg-amber-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-700 dark:text-amber-200">Cajeros inactivos</p>
                        <p class="mt-1 text-lg font-black text-amber-900 dark:text-amber-100"><?php echo e($inactiveCashiers); ?></p>
                    </article>
                <?php endif; ?>
            </div>

            <?php if($isGlobalStaffView): ?>
                <div class="ui-alert ui-alert-info mt-4 text-sm">
                    Modo global activo: puedes consultar cajeros de todas las sedes, pero crear/editar/eliminar se realiza desde una sede especifica.
                </div>
            <?php elseif((int) $maxCashiers <= 0): ?>
                <div class="ui-alert ui-alert-warning mt-4 text-sm">
                    Tu plan actual no permite crear cajeros. Sube a Profesional, Premium o Sucursales.
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

        <?php if(! $isGlobalStaffView): ?>
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Crear cajero','subtitle' => 'Acceso a panel/recepcion/clientes. Por defecto no abre ni cierra caja.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Crear cajero','subtitle' => 'Acceso a panel/recepcion/clientes. Por defecto no abre ni cierra caja.']); ?>
                <form method="POST" action="<?php echo e(route('staff.cashiers.store', $contextParams)); ?>" class="grid gap-3 lg:grid-cols-4">
                    <?php echo csrf_field(); ?>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        Nombre
                        <input type="text" name="name" class="ui-input" value="<?php echo e(old('name')); ?>" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        Correo
                        <input type="email" name="email" class="ui-input" value="<?php echo e(old('email')); ?>" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Contrasena
                        <input type="password" name="password" class="ui-input" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Confirmar contrasena
                        <input type="password" name="password_confirmation" class="ui-input" required>
                    </label>
                    <div class="flex items-end lg:col-span-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','disabled' => !$roleSchemaReady || (int) $remainingCashiers <= 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$roleSchemaReady || (int) $remainingCashiers <= 0)]); ?>Crear cajero <?php echo $__env->renderComponent(); ?>
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
                </form>
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
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Cajeros','subtitle' => $isGlobalStaffView
                ? 'Listado consolidado de cajeros por sede (solo lectura).'
                : 'Activa/desactiva usuarios, define permisos de caja y elimina permanentemente si hace falta.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cajeros','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalStaffView
                ? 'Listado consolidado de cajeros por sede (solo lectura).'
                : 'Activa/desactiva usuarios, define permisos de caja y elimina permanentemente si hace falta.')]); ?>
            <div class="overflow-x-auto">
                <table class="ui-table <?php echo e($isGlobalStaffView ? 'min-w-[980px]' : 'min-w-[1320px]'); ?>">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Nombre</th>
                            <th class="px-3 py-3">Correo</th>
                            <?php if($isGlobalStaffView): ?>
                                <th class="px-3 py-3">Sede</th>
                            <?php endif; ?>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Creado</th>
                            <th class="px-3 py-3">Ultimo acceso</th>
                            <th class="px-3 py-3">Permisos caja</th>
                            <?php if(! $isGlobalStaffView): ?>
                                <th class="px-3 py-3">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $cashiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                                <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-100"><?php echo e($cashier->name); ?></td>
                                <td class="px-3 py-3 dark:text-slate-200"><?php echo e($cashier->email); ?></td>
                                <?php if($isGlobalStaffView): ?>
                                    <td class="px-3 py-3 dark:text-slate-200">
                                        <?php echo e((string) ($cashier->gym?->name ?? 'Sede sin nombre')); ?>

                                    </td>
                                <?php endif; ?>
                                <td class="px-3 py-3">
                                    <?php if((bool) ($cashier->is_active ?? false)): ?>
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">Activo</span>
                                    <?php else: ?>
                                        <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-200">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-3 dark:text-slate-200"><?php echo e($cashier->created_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                                <td class="px-3 py-3 dark:text-slate-200"><?php echo e($cashier->last_login_at?->format('Y-m-d H:i') ?? 'Sin acceso'); ?></td>
                                <td class="px-3 py-3">
                                    <?php if($isGlobalStaffView): ?>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide <?php echo e((bool) ($cashier->can_manage_cash_movements ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200'); ?>">
                                                <?php echo e((bool) ($cashier->can_manage_cash_movements ?? false) ? 'Cobros: si' : 'Cobros: no'); ?>

                                            </span>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide <?php echo e((bool) ($cashier->can_open_cash ?? false) ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200'); ?>">
                                                <?php echo e((bool) ($cashier->can_open_cash ?? false) ? 'Abrir caja: si' : 'Abrir caja: no'); ?>

                                            </span>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide <?php echo e((bool) ($cashier->can_close_cash ?? false) ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200'); ?>">
                                                <?php echo e((bool) ($cashier->can_close_cash ?? false) ? 'Cerrar caja: si' : 'Cerrar caja: no'); ?>

                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" action="<?php echo e(route('staff.cashiers.permissions.update', $contextParams + ['cashier' => $cashier->id])); ?>" class="grid gap-2">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <input type="checkbox" name="can_manage_cash_movements" value="1" <?php if((bool) ($cashier->can_manage_cash_movements ?? false)): echo 'checked'; endif; ?>>
                                                Puede registrar cobros/movimientos
                                            </label>
                                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <input type="checkbox" name="can_open_cash" value="1" <?php if((bool) ($cashier->can_open_cash ?? false)): echo 'checked'; endif; ?>>
                                                Puede abrir caja
                                            </label>
                                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <input type="checkbox" name="can_close_cash" value="1" <?php if((bool) ($cashier->can_close_cash ?? false)): echo 'checked'; endif; ?>>
                                                Puede cerrar caja
                                            </label>
                                            <div>
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'ghost']); ?>Guardar permisos <?php echo $__env->renderComponent(); ?>
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
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <?php if(! $isGlobalStaffView): ?>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <form method="POST" action="<?php echo e(route('staff.cashiers.password.update', $contextParams + ['cashier' => $cashier->id])); ?>" class="flex items-center gap-2">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="password" name="password" class="ui-input !w-40" placeholder="Nueva contrasena" required>
                                                <input type="password" name="password_confirmation" class="ui-input !w-40" placeholder="Confirmar" required>
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'ghost']); ?>Actualizar clave <?php echo $__env->renderComponent(); ?>
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

                                            <?php if((bool) ($cashier->is_active ?? false)): ?>
                                                <form method="POST" action="<?php echo e(route('staff.cashiers.disable', $contextParams + ['cashier' => $cashier->id])); ?>" onsubmit="return confirm('Desactivar este cajero y liberar cupo?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'danger']); ?>Desactivar <?php echo $__env->renderComponent(); ?>
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
                                            <?php else: ?>
                                                <form method="POST" action="<?php echo e(route('staff.cashiers.activate', $contextParams + ['cashier' => $cashier->id])); ?>" onsubmit="return confirm('Reactivar este cajero?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'success']); ?>Activar <?php echo $__env->renderComponent(); ?>
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
                                            <?php endif; ?>

                                            <form method="POST" action="<?php echo e(route('staff.cashiers.destroy', $contextParams + ['cashier' => $cashier->id])); ?>" onsubmit="return confirm('Eliminar permanentemente este cajero? Se conservara el historial reasignado al sistema del gimnasio.');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'danger']); ?>Eliminar permanente <?php echo $__env->renderComponent(); ?>
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
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($isGlobalStaffView ? 7 : 8); ?>" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                    <?php echo e($isGlobalStaffView ? 'No hay cajeros registrados en el alcance global.' : 'No hay cajeros registrados en esta sede.'); ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/staff/index.blade.php ENDPATH**/ ?>