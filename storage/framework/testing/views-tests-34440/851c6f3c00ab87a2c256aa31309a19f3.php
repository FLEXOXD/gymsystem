

<?php $__env->startSection('title', 'Historial de notificaciones'); ?>
<?php $__env->startSection('page-title', 'Historial de notificaciones'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $visibleNotificationCount = method_exists($notifications, 'count') ? $notifications->count() : collect($notifications)->count();
        $historyItems = method_exists($notifications, 'getCollection') ? $notifications->getCollection() : collect($notifications);
        $sentHistoryCount = $historyItems->where('status', 'sent')->count();
        $skippedHistoryCount = $historyItems->where('status', 'skipped')->count();
        $historyTotal = method_exists($notifications, 'total') ? $notifications->total() : $visibleNotificationCount;
    ?>
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Historial</span>
                    <h2 class="sa-title">Historial de notificaciones mas claro para seguir lo enviado y lo omitido.</h2>
                    <p class="sa-subtitle">
                        Filtras por fecha o gimnasio y ves rapido que se envio, que se omitio y quien lo gestiono.
                    </p>
                    <div class="sa-actions">
                        <a href="<?php echo e(route('superadmin.notifications.index')); ?>" class="ui-button ui-button-secondary">Volver a pendientes</a>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Envios y omisiones</strong>
                            <span>La tabla separa claramente lo enviado de lo omitido.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Filtro por rango</strong>
                            <span>Sirve para revisar semanas o cortes comerciales puntuales.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Gestionado por</strong>
                            <span>Queda visible quien movio cada notificacion.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total historial</p>
                <p class="sa-stat-value"><?php echo e($historyTotal); ?></p>
                <p class="sa-stat-meta">Registros historicos segun filtros aplicados.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Enviadas</p>
                <p class="sa-stat-value"><?php echo e($sentHistoryCount); ?></p>
                <p class="sa-stat-meta">Notificaciones marcadas como enviadas en la pagina actual.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Omitidas</p>
                <p class="sa-stat-value"><?php echo e($skippedHistoryCount); ?></p>
                <p class="sa-stat-meta">Casos omitidos dentro del filtro actual.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Visibles</p>
                <p class="sa-stat-value"><?php echo e($visibleNotificationCount); ?></p>
                <p class="sa-stat-meta">Resultados cargados en esta vista.</p>
            </article>
        </section>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial','subtitle' => 'Notificaciones enviadas/omitidas con filtros por fecha y gimnasio.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial','subtitle' => 'Notificaciones enviadas/omitidas con filtros por fecha y gimnasio.']); ?>
        <form method="GET" action="<?php echo e(route('superadmin.notifications.history')); ?>" class="mb-4 grid gap-3 md:grid-cols-4">
            <label class="text-sm font-semibold ui-muted">
                Desde
                <input type="date" name="date_from" value="<?php echo e($filters['date_from']); ?>" class="ui-input mt-1 block w-full">
            </label>
            <label class="text-sm font-semibold ui-muted">
                Hasta
                <input type="date" name="date_to" value="<?php echo e($filters['date_to']); ?>" class="ui-input mt-1 block w-full">
            </label>
            <label class="text-sm font-semibold ui-muted">
                Gimnasio
                <select name="gym_id" class="ui-input mt-1 block w-full">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $gyms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gym): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($gym->id); ?>" <?php if($filters['gym_id'] === (int) $gym->id): echo 'selected'; endif; ?>><?php echo e($gym->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
            <div class="flex items-end">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary']); ?>Filtrar <?php echo $__env->renderComponent(); ?>
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

        <div class="sa-table-shell overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Vence</th>
                    <th class="px-3 py-3">Gestionado por</th>
                    <th class="px-3 py-3">Enviado en</th>
                </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $typeLabel = match ($notification->type) {
                            'expires_7' => 'Vence en 7 días',
                            'expires_3' => 'Vence en 3 días',
                            'expires_1' => 'Vence en 1 día',
                            'grace_1' => 'Gracia día 1',
                            'grace_2' => 'Gracia día 2',
                            'grace_3' => 'Gracia día 3',
                            default => str_replace('_', ' ', $notification->type),
                        };
                    ?>
                    <tr>
                        <td class="dark:text-slate-200"><?php echo e($notification->scheduled_for?->toDateString()); ?></td>
                        <td class="font-semibold dark:text-slate-100"><?php echo e($notification->gym?->name ?? 'N/D'); ?></td>
                        <td class="dark:text-slate-200"><?php echo e($typeLabel); ?></td>
                        <td>
                            <?php
                                $statusClass = $notification->status === 'sent'
                                    ? 'sa-status-chip is-success'
                                    : 'sa-status-chip is-neutral';
                            ?>
                            <span class="<?php echo e($statusClass); ?>">
                                <?php echo e($notification->status === 'sent' ? 'Enviado' : 'Omitido'); ?>

                            </span>
                        </td>
                        <td class="dark:text-slate-200"><?php echo e($notification->subscription?->plan_name ?? '-'); ?></td>
                        <td class="dark:text-slate-200"><?php echo e($notification->subscription?->ends_at?->toDateString() ?? '-'); ?></td>
                        <td class="dark:text-slate-200"><?php echo e($notification->createdBy?->name ?? '-'); ?></td>
                        <td class="dark:text-slate-200"><?php echo e($notification->sent_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="sa-empty-row">
                            No hay resultados para el filtro actual.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($notifications->links()); ?>

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

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/notifications/history.blade.php ENDPATH**/ ?>