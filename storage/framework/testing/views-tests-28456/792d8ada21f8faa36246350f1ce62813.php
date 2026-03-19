

<?php $__env->startSection('title', 'Cajeros'); ?>
<?php $__env->startSection('page-title', 'Gestión de cajeros'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .staff-page .staff-table-filters {
        display: grid;
        gap: .75rem;
    }
    @media (min-width: 1280px) {
        .staff-page .staff-table-filters {
            grid-template-columns: minmax(0, 1fr) 14rem auto auto;
            align-items: end;
        }
    }
    .staff-page .staff-table-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .staff-page .staff-permissions-grid {
        display: grid;
        gap: .35rem;
    }
    .staff-page .staff-actions-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .5rem;
    }
    .staff-page .staff-password-trigger {
        min-width: 7.25rem;
    }
    .staff-page .modal-shell {
        position: fixed;
        inset: 0;
        z-index: 80;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgb(2 6 23 / .72);
        backdrop-filter: blur(3px);
    }
    .staff-page .modal-shell.is-open {
        display: flex;
    }
    .staff-page .modal-card {
        width: min(100%, 34rem);
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        overflow-y: auto;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / .35);
        background: rgb(2 6 23 / .96);
        color: rgb(226 232 240);
        box-shadow: 0 32px 48px -28px rgb(0 0 0 / .72);
    }
    .staff-page .mini-action {
        border: 1px solid rgb(148 163 184 / .35);
        border-radius: .65rem;
        background: rgb(15 23 42 / .75);
        color: rgb(226 232 240);
        padding: .38rem .68rem;
        font-size: .78rem;
        font-weight: 700;
        transition: all .18s ease;
    }
    .staff-page .mini-action:hover {
        border-color: rgb(34 211 238 / .45);
        background: rgb(8 47 73 / .55);
    }
    @media (max-width: 640px) {
        .staff-page .modal-shell {
            padding: .65rem;
        }
    }
    @media (max-width: 640px) {
        .staff-page .staff-actions-wrap form,
        .staff-page .staff-actions-wrap .staff-password-trigger,
        .staff-page .staff-actions-wrap .ui-button {
            width: 100%;
        }
    }
</style>
<?php $__env->stopPush(); ?>

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
            'basico' => 'Básico',
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
        $routeHasPasswordUpdate = \Illuminate\Support\Facades\Route::has('staff.cashiers.password.update');
        $passwordRouteTemplate = $routeHasPasswordUpdate
            ? route('staff.cashiers.password.update', $contextParams + ['cashier' => '__CASHIER__'])
            : '';
    ?>

    <div class="staff-page space-y-5" data-password-route-template="<?php echo e($passwordRouteTemplate); ?>">
        <?php if(! $roleSchemaReady): ?>
            <div class="ui-alert ui-alert-danger text-sm">
                <?php echo e($schemaErrorMessage !== '' ? $schemaErrorMessage : 'Falta la migración de roles de usuarios.'); ?>

            </div>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => $isGlobalStaffView ? 'Resumen global de cajeros' : 'Cupo de cajeros','subtitle' => $isGlobalStaffView
                ? 'Vista consolidada de cajeros en todas las sedes vinculadas (solo lectura).'
                : 'Límites aplicados según el plan activo de esta sede.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalStaffView ? 'Resumen global de cajeros' : 'Cupo de cajeros'),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalStaffView
                ? 'Vista consolidada de cajeros en todas las sedes vinculadas (solo lectura).'
                : 'Límites aplicados según el plan activo de esta sede.')]); ?>
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
                    Modo global activo: puedes consultar cajeros de todas las sedes, pero crear/editar/eliminar se realiza desde una sede específica.
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Crear cajero','subtitle' => 'Acceso a panel/recepción/clientes. Por defecto no abre ni cierra caja.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Crear cajero','subtitle' => 'Acceso a panel/recepción/clientes. Por defecto no abre ni cierra caja.']); ?>
                <form method="POST" action="<?php echo e(route('staff.cashiers.store', $contextParams)); ?>" class="grid gap-3 lg:grid-cols-6">
                    <?php echo csrf_field(); ?>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-3">
                        Nombre
                        <input type="text" name="name" class="ui-input" value="<?php echo e(old('name')); ?>" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-3">
                        Correo
                        <input type="email" name="email" class="ui-input" value="<?php echo e(old('email')); ?>" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        Contraseña
                        <input type="password" name="password" class="ui-input" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        Confirmar contraseña
                        <input type="password" name="password_confirmation" class="ui-input" required>
                    </label>
                    <div class="flex items-end lg:col-span-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','class' => 'w-full justify-center','disabled' => !$roleSchemaReady || (int) $remainingCashiers <= 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'w-full justify-center','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$roleSchemaReady || (int) $remainingCashiers <= 0)]); ?>Crear cajero <?php echo $__env->renderComponent(); ?>
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
                : 'Activa/desactiva usuarios, define permisos de caja y archiva cajeros sin perder su historial.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cajeros','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalStaffView
                ? 'Listado consolidado de cajeros por sede (solo lectura).'
                : 'Activa/desactiva usuarios, define permisos de caja y archiva cajeros sin perder su historial.')]); ?>
            <div class="staff-table-filters mb-3">
                <label class="space-y-1 text-sm font-semibold text-slate-500 dark:text-slate-300">
                    <span class="text-xs uppercase tracking-wide">Buscar cajero</span>
                    <input id="staff-search" type="search" class="ui-input" placeholder="Nombre o correo">
                </label>
                <?php if(! $isGlobalStaffView): ?>
                    <label class="space-y-1 text-sm font-semibold text-slate-500 dark:text-slate-300">
                        <span class="text-xs uppercase tracking-wide">Estado</span>
                        <select id="staff-status-filter" class="ui-input">
                            <option value="all">Todos</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                        </select>
                    </label>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                <div class="pt-1 xl:pt-0">
                    <span id="staff-count-badge" class="inline-flex rounded-full border border-slate-300/35 px-3 py-1 text-xs font-bold text-slate-400"><?php echo e($cashiers->count()); ?> cajeros</span>
                </div>
                <button id="staff-clear-filters" type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold">Limpiar filtros</button>
            </div>

            <div class="staff-table-wrap overflow-x-auto">
                <table class="ui-table <?php echo e($isGlobalStaffView ? 'min-w-[980px]' : 'min-w-[1140px]'); ?>">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Nombre</th>
                            <th class="px-3 py-3">Correo</th>
                            <?php if($isGlobalStaffView): ?>
                                <th class="px-3 py-3">Sede</th>
                            <?php endif; ?>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Creado</th>
                            <th class="px-3 py-3">Último acceso</th>
                            <th class="px-3 py-3">Permisos caja</th>
                            <?php if(! $isGlobalStaffView): ?>
                                <th class="px-3 py-3">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $cashiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cashier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr
                                data-staff-name="<?php echo e(mb_strtolower((string) $cashier->name)); ?>"
                                data-staff-email="<?php echo e(mb_strtolower((string) $cashier->email)); ?>"
                                data-staff-status="<?php echo e((bool) ($cashier->is_active ?? false) ? 'active' : 'inactive'); ?>"
                                class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
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
                                        <form method="POST" action="<?php echo e(route('staff.cashiers.permissions.update', $contextParams + ['cashier' => $cashier->id])); ?>" class="staff-permissions-grid">
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
                                        <div class="staff-actions-wrap">
                                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'staff-password-trigger js-open-password-modal','dataCashierId' => ''.e($cashier->id).'','dataCashierName' => ''.e($cashier->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','class' => 'staff-password-trigger js-open-password-modal','data-cashier-id' => ''.e($cashier->id).'','data-cashier-name' => ''.e($cashier->name).'']); ?>
                                                Actualizar contraseña
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

                                            <?php if((bool) ($cashier->is_active ?? false)): ?>
                                                <form method="POST" action="<?php echo e(route('staff.cashiers.disable', $contextParams + ['cashier' => $cashier->id])); ?>" onsubmit="return confirm('¿Desactivar este cajero y liberar cupo?');">
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
                                                <form method="POST" action="<?php echo e(route('staff.cashiers.activate', $contextParams + ['cashier' => $cashier->id])); ?>" onsubmit="return confirm('¿Reactivar este cajero?');">
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

                                            <form method="POST" action="<?php echo e(route('staff.cashiers.destroy', $contextParams + ['cashier' => $cashier->id])); ?>" onsubmit="return confirm('¿Archivar este cajero? Perderá acceso, pero se conservarán sus clientes, cobros e historial.');">
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
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'danger']); ?>Archivar <?php echo $__env->renderComponent(); ?>
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

        <?php if(! $isGlobalStaffView): ?>
            <div id="staff-password-modal" class="modal-shell" aria-hidden="true" aria-labelledby="staff-password-title">
                <div class="modal-card">
                    <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                        <h3 id="staff-password-title" class="text-base font-black text-slate-100">Actualizar contraseña</h3>
                        <button type="button" class="mini-action" data-close-staff-modal="staff-password-modal">Cerrar</button>
                    </div>
                    <form id="staff-password-form" method="POST" action="#" class="space-y-3 px-4 py-4">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <p id="staff-password-label" class="text-sm font-semibold text-slate-300">Cajero seleccionado</p>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Nueva contraseña
                            <input type="password" name="password" class="ui-input" placeholder="Mínimo 8 caracteres" required minlength="8">
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Confirmar contraseña
                            <input type="password" name="password_confirmation" class="ui-input" placeholder="Repite la contraseña" required minlength="8">
                        </label>
                        <div class="flex justify-end gap-2 pt-1">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'muted','size' => 'sm','dataCloseStaffModal' => 'staff-password-modal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'muted','size' => 'sm','data-close-staff-modal' => 'staff-password-modal']); ?>Cancelar <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary','size' => 'sm']); ?>Actualizar contraseña <?php echo $__env->renderComponent(); ?>
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
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(() => {
    const root = document.querySelector('.staff-page');
    if (!root) return;

    const searchInput = document.getElementById('staff-search');
    const statusFilter = document.getElementById('staff-status-filter');
    const clearFilters = document.getElementById('staff-clear-filters');
    const countBadge = document.getElementById('staff-count-badge');
    const rows = Array.from(document.querySelectorAll('tr[data-staff-name]'));

    const applyFilters = () => {
        const q = String(searchInput?.value || '').trim().toLowerCase();
        const status = String(statusFilter?.value || 'all');
        let visible = 0;

        rows.forEach((row) => {
            const name = String(row.getAttribute('data-staff-name') || '');
            const email = String(row.getAttribute('data-staff-email') || '');
            const rowStatus = String(row.getAttribute('data-staff-status') || '');
            const okText = q === '' || name.includes(q) || email.includes(q);
            const okStatus = status === 'all' || rowStatus === status;
            const show = okText && okStatus;
            row.classList.toggle('hidden', !show);
            if (show) visible += 1;
        });

        if (countBadge) {
            countBadge.textContent = `${visible} cajero${visible === 1 ? '' : 's'}`;
        }
    };

    searchInput?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
    clearFilters?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        applyFilters();
        searchInput?.focus();
    });
    applyFilters();

    const routeTemplate = String(root.getAttribute('data-password-route-template') || '');
    const passwordModal = document.getElementById('staff-password-modal');
    const passwordForm = document.getElementById('staff-password-form');
    const passwordLabel = document.getElementById('staff-password-label');
    const openButtons = Array.from(document.querySelectorAll('.js-open-password-modal'));

    const openModal = (modal) => {
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.modal-shell.is-open')) {
            document.body.classList.remove('overflow-hidden');
        }
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const cashierId = String(button.getAttribute('data-cashier-id') || '');
            const cashierName = String(button.getAttribute('data-cashier-name') || 'cajero');
            if (!cashierId || !passwordForm || routeTemplate === '') return;
            passwordForm.action = routeTemplate.replace('__CASHIER__', cashierId);
            if (passwordLabel) passwordLabel.textContent = `Actualizar contraseña de ${cashierName}`;
            passwordForm.reset();
            openModal(passwordModal);
        });
    });

    document.querySelectorAll('[data-close-staff-modal]').forEach((button) => {
        button.addEventListener('click', () => closeModal(passwordModal));
    });

    passwordModal?.addEventListener('click', (event) => {
        if (event.target === passwordModal) closeModal(passwordModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal(passwordModal);
    });
})();
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/staff/index.blade.php ENDPATH**/ ?>