

<?php $__env->startSection('title', 'Detalle de clase'); ?>
<?php $__env->startSection('page-title', 'Detalle de clase'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .class-detail-page {
        display: grid;
        gap: 1rem;
    }

    .class-detail-summary {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .class-detail-stat {
        border: 1px solid rgb(148 163 184 / 0.24);
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        background: rgb(255 255 255 / 0.84);
    }

    .theme-dark .class-detail-stat,
    .dark .class-detail-stat {
        border-color: rgb(71 85 105 / 0.7);
        background: rgb(15 23 42 / 0.62);
    }

    .class-detail-stat-label {
        font-size: 0.7rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .class-detail-stat-label,
    .dark .class-detail-stat-label {
        color: rgb(148 163 184 / 0.9);
    }

    .class-detail-stat-value {
        margin-top: 0.45rem;
        font-size: 1.4rem;
        font-weight: 900;
        letter-spacing: -0.04em;
        color: rgb(15 23 42 / 0.98);
    }

    .theme-dark .class-detail-stat-value,
    .dark .class-detail-stat-value {
        color: rgb(248 250 252 / 0.98);
    }

    .class-detail-grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 1200px) {
        .class-detail-grid {
            grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
            align-items: start;
        }
    }

    .class-detail-table-wrap {
        overflow: auto;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / 0.24);
    }

    .theme-dark .class-detail-table-wrap,
    .dark .class-detail-table-wrap {
        border-color: rgb(71 85 105 / 0.62);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $routeParams = is_array($routeParams ?? null) ? $routeParams : [];
        $statusMeta = match ((string) $classModel->status) {
            'cancelled' => ['label' => 'Cancelada', 'variant' => 'danger'],
            default => ['label' => 'Programada', 'variant' => 'success'],
        };

        $reservationStatusMeta = static function (?string $status): array {
            return match (trim((string) $status)) {
                'attended' => ['label' => 'Asistio', 'variant' => 'info'],
                'waitlist' => ['label' => 'Espera', 'variant' => 'warning'],
                'cancelled' => ['label' => 'Cancelada', 'variant' => 'danger'],
                default => ['label' => 'Reservada', 'variant' => 'success'],
            };
        };
        $priceMeta = (float) ($classModel->price ?? 0) <= 0
            ? ['label' => 'Gratis', 'classes' => 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100']
            : ['label' => '$'.number_format((float) $classModel->price, 2, '.', ','), 'classes' => 'border-amber-300/45 bg-amber-400/15 text-amber-100'];
    ?>

    <div class="class-detail-page">
        <div class="space-y-2">
            <a href="<?php echo e(route('classes.index', $routeParams)); ?>" class="text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-300">Volver a clases</a>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-2xl font-black tracking-tight text-slate-950 dark:text-white"><?php echo e($classModel->name); ?></h2>
                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $statusMeta['variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusMeta['variant'])]); ?><?php echo e($statusMeta['label']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-black uppercase tracking-[0.14em] <?php echo e($priceMeta['classes']); ?>"><?php echo e($priceMeta['label']); ?></span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                <?php echo e($classModel->gym?->name ?? 'Sede actual'); ?>

                | <?php echo e(optional($classModel->starts_at)->format('d/m/Y H:i')); ?>

                | <?php echo e($classModel->instructor_name ?: 'Instructor por definir'); ?>

            </p>
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">
                La edicion de la clase ahora se hace desde la agenda con el boton Abrir.
            </p>
        </div>

        <?php if($isGlobalScope): ?>
            <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'warning','title' => 'Vista global']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning','title' => 'Vista global']); ?>
                Estas consultando la clase desde una vista multisede. Puedes revisar reservas, pero la edicion se habilita entrando a la sede de esta clase.
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
        <?php endif; ?>

        <section class="class-detail-summary">
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Reservas</p>
                <p class="class-detail-stat-value"><?php echo e((int) $classModel->reserved_count); ?>/<?php echo e((int) $classModel->capacity); ?></p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Lista de espera</p>
                <p class="class-detail-stat-value"><?php echo e((int) $classModel->waitlist_count); ?></p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Sala</p>
                <p class="class-detail-stat-value text-lg !tracking-normal"><?php echo e($classModel->room_name ?: '-'); ?></p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Categoria</p>
                <p class="class-detail-stat-value text-lg !tracking-normal"><?php echo e($classModel->category ?: 'General'); ?></p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Precio</p>
                <p class="class-detail-stat-value text-lg !tracking-normal"><?php echo e($priceMeta['label']); ?></p>
            </article>
        </section>

        <section class="class-detail-grid">
            <div class="space-y-4">
                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Informacion de la clase','subtitle' => 'Resumen operativo de la sesion.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Informacion de la clase','subtitle' => 'Resumen operativo de la sesion.']); ?>
                    <dl class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Inicio</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e(optional($classModel->starts_at)->format('d/m/Y H:i')); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Fin</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e(optional($classModel->ends_at)->format('d/m/Y H:i')); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Instructor</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($classModel->instructor_name ?: '-'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Nivel</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($classModel->level ?: '-'); ?></dd>
                        </div>
                    </dl>

                    <?php if($classModel->description): ?>
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm leading-6 text-slate-600 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-300">
                            <?php echo e($classModel->description); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Edicion desde agenda','subtitle' => 'Si quieres cambiar horario, cupos o estado, vuelve a la agenda y usa el boton Abrir de esa clase.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edicion desde agenda','subtitle' => 'Si quieres cambiar horario, cupos o estado, vuelve a la agenda y usa el boton Abrir de esa clase.']); ?>
                    <div class="flex flex-wrap gap-3">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('classes.index', $routeParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('classes.index', $routeParams)),'variant' => 'secondary']); ?>Ir a agenda <?php echo $__env->renderComponent(); ?>
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

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Reservas del cliente','subtitle' => 'Controla cupos confirmados, lista de espera y asistencia.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Reservas del cliente','subtitle' => 'Controla cupos confirmados, lista de espera y asistencia.']); ?>
                <?php if($classModel->reservations->isEmpty()): ?>
                    <div class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        Todavia no hay reservas registradas para esta clase.
                    </div>
                <?php else: ?>
                    <div class="class-detail-table-wrap">
                        <table class="ui-table min-w-[980px] text-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Reserva</th>
                                    <th>Contacto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $classModel->reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $reservationMeta = $reservationStatusMeta($reservation->status);
                                        $clientName = trim((string) ($reservation->client?->full_name ?? 'Cliente'));
                                        $reserveMoment = $reservation->promoted_at
                                            ? 'Promovido '.optional($reservation->promoted_at)->format('d/m H:i')
                                            : ($reservation->reserved_at
                                                ? 'Reservo '.optional($reservation->reserved_at)->format('d/m H:i')
                                                : ($reservation->waitlisted_at ? 'Espera '.optional($reservation->waitlisted_at)->format('d/m H:i') : '-'));
                                    ?>
                                    <tr>
                                        <td class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($clientName); ?></td>
                                        <td><?php echo e($reservation->client?->document_number ?? '-'); ?></td>
                                        <td>
                                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $reservationMeta['variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reservationMeta['variant'])]); ?><?php echo e($reservationMeta['label']); ?> <?php echo $__env->renderComponent(); ?>
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
                                        <td><?php echo e($reserveMoment); ?></td>
                                        <td><?php echo e($reservation->client?->phone ?: '-'); ?></td>
                                        <td>
                                            <?php if($canManageReservations): ?>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php if((string) $reservation->status === 'reserved'): ?>
                                                        <form method="POST" action="<?php echo e(route('classes.reservations.update', $routeParams + ['reservation' => $reservation->id])); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PATCH'); ?>
                                                            <input type="hidden" name="action" value="attended">
                                                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','size' => 'sm']); ?>Asistio <?php echo $__env->renderComponent(); ?>
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
                                                    <?php if(in_array((string) $reservation->status, ['reserved', 'waitlist'], true)): ?>
                                                        <form method="POST" action="<?php echo e(route('classes.reservations.update', $routeParams + ['reservation' => $reservation->id])); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PATCH'); ?>
                                                            <input type="hidden" name="action" value="cancel">
                                                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'ghost','size' => 'sm']); ?>Cancelar <?php echo $__env->renderComponent(); ?>
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
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-500 dark:text-slate-400">Solo lectura</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
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
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/classes/show.blade.php ENDPATH**/ ?>