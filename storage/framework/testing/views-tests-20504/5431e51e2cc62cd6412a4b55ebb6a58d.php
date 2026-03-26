

<?php $__env->startSection('title', 'Aceptaciones legales'); ?>
<?php $__env->startSection('page-title', 'Aceptaciones legales'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $visibleAcceptanceCount = method_exists($acceptances, 'count') ? $acceptances->count() : collect($acceptances)->count();
        $acceptanceItems = method_exists($acceptances, 'getCollection') ? $acceptances->getCollection() : collect($acceptances);
        $withContractCount = $acceptanceItems->filter(fn ($acceptance) => filled($acceptance->contract_code ?? null))->count();
        $acceptanceTotal = method_exists($acceptances, 'total') ? $acceptances->total() : $visibleAcceptanceCount;
    ?>
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Respaldo legal</span>
                    <h2 class="sa-title">Aceptaciones legales con filtro, version y acceso rapido al contrato.</h2>
                    <p class="sa-subtitle">
                        Esta vista deja claro que version esta activa, cuantos registros tienes y cuantas evidencias estan listas para abrir en PDF.
                    </p>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Version vigente <?php echo e($currentVersion); ?></strong>
                            <span>La tabla muestra aceptaciones digitales del primer ingreso.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong><?php echo e($withContractCount); ?> contratos listos</strong>
                            <span>Los registros con codigo pueden abrirse directamente en PDF.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong><?php echo e(($dbNotReady ?? false) ? 'Migracion pendiente' : 'Base legal operativa'); ?></strong>
                            <span><?php echo e(($dbNotReady ?? false) ? 'Ejecuta migraciones para habilitar la vista.' : 'Puedes filtrar y consultar evidencias desde aqui.'); ?></span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total registros</p>
                <p class="sa-stat-value"><?php echo e($acceptanceTotal); ?></p>
                <p class="sa-stat-meta">Aceptaciones legales guardadas en el sistema.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Visibles</p>
                <p class="sa-stat-value"><?php echo e($visibleAcceptanceCount); ?></p>
                <p class="sa-stat-meta">Resultados del filtro actual.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Con contrato</p>
                <p class="sa-stat-value"><?php echo e($withContractCount); ?></p>
                <p class="sa-stat-meta">Registros con evidencia lista para PDF.</p>
            </article>
            <article class="sa-stat-card <?php echo e(($dbNotReady ?? false) ? 'is-warning' : 'is-success'); ?>">
                <p class="sa-stat-label">Estado modulo</p>
                <p class="sa-stat-value"><?php echo e(($dbNotReady ?? false) ? 'Pendiente' : 'Listo'); ?></p>
                <p class="sa-stat-meta">Migracion y consulta legal del modulo.</p>
            </article>
        </section>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Aceptaciones legales','subtitle' => 'Respaldo legal de aceptaciones digitales registradas en el primer ingreso.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Aceptaciones legales','subtitle' => 'Respaldo legal de aceptaciones digitales registradas en el primer ingreso.']); ?>
        <?php if(($dbNotReady ?? false) === true): ?>
            <div class="mb-4 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/40 dark:bg-amber-900/20 dark:text-amber-200">
                La base legal aún no está lista. Ejecuta <code>php artisan migrate</code> para habilitar esta sección.
            </div>
        <?php endif; ?>

        <form method="GET" action="<?php echo e(route('superadmin.legal-acceptances.index')); ?>" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-sm font-semibold ui-muted">
                Buscar
                <input type="text" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" class="ui-input mt-1 block min-w-[260px]" placeholder="Nombre, correo o código de contrato">
            </label>

            <label class="text-sm font-semibold ui-muted">
                Desde
                <input type="date" name="from" value="<?php echo e($filters['from'] ?? ''); ?>" class="ui-input mt-1 block min-w-[170px]">
            </label>

            <label class="text-sm font-semibold ui-muted">
                Hasta
                <input type="date" name="to" value="<?php echo e($filters['to'] ?? ''); ?>" class="ui-input mt-1 block min-w-[170px]">
            </label>

            <label class="text-sm font-semibold ui-muted">
                Versión
                <input type="text" name="versión" value="<?php echo e($filters['versión'] ?? ''); ?>" class="ui-input mt-1 block min-w-[140px]" placeholder="<?php echo e($currentVersion); ?>">
            </label>

            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary']); ?>Aplicar <?php echo $__env->renderComponent(); ?>
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

        <div class="mb-3 text-sm text-slate-600 dark:text-slate-300">
            Total registros: <strong><?php echo e($acceptances->total()); ?></strong> | Versión vigente: <strong><?php echo e($currentVersion); ?></strong>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1280px]">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Fecha de aceptación</th>
                        <th class="px-3 py-3">Usuario</th>
                        <th class="px-3 py-3">Correo</th>
                        <th class="px-3 py-3">Documento</th>
                        <th class="px-3 py-3">Versión</th>
                        <th class="px-3 py-3">IP</th>
                        <th class="px-3 py-3">Ubicación</th>
                        <th class="px-3 py-3">Contrato</th>
                        <th class="px-3 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $acceptances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acceptance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $hasCoords = $acceptance->latitude !== null && $acceptance->longitude !== null;
                            $coordsLabel = $hasCoords
                                ? number_format((float) $acceptance->latitude, 6).', '.number_format((float) $acceptance->longitude, 6)
                                : 'Sin coordenadas';
                        ?>
                        <tr class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                            <td class="px-3 py-3 whitespace-nowrap dark:text-slate-200">
                                <?php echo e($acceptance->accepted_at?->format('Y-m-d H:i:s') ?? '-'); ?>

                            </td>
                            <td class="px-3 py-3 dark:text-slate-100">
                                <p class="font-semibold"><?php echo e($acceptance->full_name); ?></p>
                                <p class="text-xs ui-muted">ID usuario: <?php echo e($acceptance->user_id ?? 'N/D'); ?></p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <?php echo e($acceptance->email); ?>

                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <?php echo e($acceptance->document_label); ?>

                            </td>
                            <td class="px-3 py-3 whitespace-nowrap dark:text-slate-200">
                                <?php echo e($acceptance->legal_version); ?>

                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <p><?php echo e($acceptance->ip_address ?? '-'); ?></p>
                                <p class="text-xs ui-muted">vía <?php echo e($acceptance->accepted_via ?? 'n/a'); ?></p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <p><?php echo e($coordsLabel); ?></p>
                                <p class="text-xs ui-muted">permiso: <?php echo e($acceptance->location_permission ?? 'skipped'); ?></p>
                            </td>
                            <td class="px-3 py-3 dark:text-slate-200">
                                <span class="inline-flex rounded bg-slate-100 px-2 py-1 font-mono text-xs text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <?php echo e($acceptance->contract_code ?: 'SIN-CÓDIGO'); ?>

                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <a href="<?php echo e(route('superadmin.legal-acceptances.contract.pdf', $acceptance->id)); ?>" target="_blank" rel="noreferrer">
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'secondary']); ?>Ver PDF contrato <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                No existen aceptaciones legales registradas con esos filtros.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($acceptances->links()); ?>

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

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/legal-acceptances/index.blade.php ENDPATH**/ ?>