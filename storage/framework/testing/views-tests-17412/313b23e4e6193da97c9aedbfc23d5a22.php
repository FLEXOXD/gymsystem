

<?php $__env->startSection('title', 'Bandeja de notificaciones'); ?>
<?php $__env->startSection('page-title', 'Notificaciones pendientes'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $pushCampaigns = $pushCampaigns ?? collect();
        $pushSentCount = $pushCampaigns->where('status', 'sent')->count();
        $pushProblemCount = $pushCampaigns->filter(fn ($campaign) => in_array((string) ($campaign->status ?? ''), ['partial', 'failed'], true))->count();
        $pendingNotificationCount = $notifications->count();
    ?>
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Notificaciones</span>
                    <h2 class="sa-title">Campanas push y recordatorios en una lectura mas ordenada.</h2>
                    <p class="sa-subtitle">
                        Tienes campañas manuales arriba y pendientes automaticos abajo, con conteo rapido para actuar sin revisar toda la tabla.
                    </p>
                    <div class="sa-actions">
                        <a href="#pending-notifications" class="ui-button ui-button-secondary">Ver pendientes</a>
                        <a href="<?php echo e(route('superadmin.notifications.history')); ?>" class="ui-button ui-button-ghost">Ver historial</a>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Push manual</strong>
                            <span>Lanza campañas segmentadas por gimnasio y audiencia.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Pendientes del dia</strong>
                            <span>Los avisos automáticos quedan listos para marcar enviados u omitidos.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Historial separado</strong>
                            <span>Los registros enviados u omitidos ya tienen su vista propia.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Campanas push</p>
                <p class="sa-stat-value"><?php echo e($pushCampaigns->count()); ?></p>
                <p class="sa-stat-meta">Ultimas campanas registradas en el panel.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Enviadas</p>
                <p class="sa-stat-value"><?php echo e($pushSentCount); ?></p>
                <p class="sa-stat-meta">Campanas completadas sin accion adicional.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Con problema</p>
                <p class="sa-stat-value"><?php echo e($pushProblemCount); ?></p>
                <p class="sa-stat-meta">Campanas parciales o fallidas para revisar.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Pendientes</p>
                <p class="sa-stat-value"><?php echo e($pendingNotificationCount); ?></p>
                <p class="sa-stat-meta">Avisos automaticos pendientes para la fecha elegida.</p>
            </article>
        </section>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Campañas push','subtitle' => 'Envio segmentado de notificaciones push a gimnasios y roles operativos.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Campañas push','subtitle' => 'Envio segmentado de notificaciones push a gimnasios y roles operativos.']); ?>
        <form method="POST"
              action="<?php echo e(route('superadmin.notifications.push-campaigns.send')); ?>"
              class="grid gap-3 md:grid-cols-2 xl:grid-cols-4"
              data-ui-loading-overlay="1"
              data-ui-loading-message="Enviando campaña push...">
            <?php echo csrf_field(); ?>
            <label class="text-sm font-semibold ui-muted">
                Gimnasio
                <select name="gym_id" class="ui-input mt-1 block w-full">
                    <option value="">Todos los gimnasios</option>
                    <?php $__currentLoopData = $gyms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gym): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($gym->id); ?>" <?php if((int) old('gym_id') === (int) $gym->id): echo 'selected'; endif; ?>><?php echo e($gym->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Audiencia
                <select name="audience" class="ui-input mt-1 block w-full" required>
                    <option value="owners" <?php if(old('audience', 'owners') === 'owners'): echo 'selected'; endif; ?>>Solo duenos</option>
                    <option value="staff" <?php if(old('audience') === 'staff'): echo 'selected'; endif; ?>>Duenos y cajeros</option>
                    <option value="all_users" <?php if(old('audience') === 'all_users'): echo 'selected'; endif; ?>>Todos los usuarios del gym</option>
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-2">
                Titulo
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" maxlength="120" class="ui-input mt-1 block w-full" placeholder="Ej: Recordatorio de renovación" required>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-2">
                Mensaje
                <input type="text" name="body" value="<?php echo e(old('body')); ?>" maxlength="255" class="ui-input mt-1 block w-full" placeholder="Mensaje corto para push en celular" required>
            </label>

            <label class="text-sm font-semibold ui-muted xl:col-span-3">
                Detalle (opcional)
                <textarea name="detail_text" rows="3" maxlength="1500" class="ui-input mt-1 block w-full" placeholder="Texto adicional para abrir en detalle"><?php echo e(old('detail_text')); ?></textarea>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Tag (opcional)
                <input type="text" name="tag" value="<?php echo e(old('tag')); ?>" maxlength="120" class="ui-input mt-1 block w-full" placeholder="promo-marzo-2026">
            </label>

            <div class="flex items-end xl:col-span-1">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary']); ?>Enviar campaña push <?php echo $__env->renderComponent(); ?>
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

        <div class="mt-4 overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Audiencia</th>
                    <th class="px-3 py-3">Titulo</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Enviadas</th>
                    <th class="px-3 py-3">Fallidas</th>
                    <th class="px-3 py-3">Saltadas</th>
                </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = ($pushCampaigns ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $statusVariant = match ((string) ($campaign->status ?? 'queued')) {
                            'sent' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                            'partial' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                            'failed' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                            'skipped' => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-100',
                            'sending' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-200',
                            default => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
                        };
                        $audienceLabel = match ((string) ($campaign->audience ?? 'owners')) {
                            'staff' => 'Duenos y cajeros',
                            'all_users' => 'Todos usuarios',
                            default => 'Solo duenos',
                        };
                    ?>
                    <tr data-push-campaign-status="<?php echo e((string) ($campaign->status ?? 'queued')); ?>" class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e($campaign->created_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100"><?php echo e($campaign->gym?->name ?? 'Todos'); ?></td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e($audienceLabel); ?></td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e($campaign->title); ?></td>
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide <?php echo e($statusVariant); ?>">
                                <?php echo e($campaign->status); ?>

                            </span>
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e((int) ($campaign->sent_count ?? 0)); ?></td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e((int) ($campaign->failed_count ?? 0)); ?></td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e((int) ($campaign->skipped_count ?? 0)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            Aún no se han enviado campañas push.
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

    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['id' => 'pending-notifications','title' => 'Bandeja de notificaciones','subtitle' => 'Avisos automaticos por vencimiento y días de gracia.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'pending-notifications','title' => 'Bandeja de notificaciones','subtitle' => 'Avisos automaticos por vencimiento y días de gracia.']); ?>
        <form method="GET" action="<?php echo e(route('superadmin.notifications.index')); ?>" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-sm font-semibold ui-muted">
                Fecha
                <input type="date" name="date" value="<?php echo e($selectedDate); ?>" class="ui-input mt-1 block">
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

        <div class="mb-3 text-sm text-slate-600 dark:text-slate-300">Pendientes para <?php echo e($selectedDate); ?>: <strong><?php echo e($notifications->count()); ?></strong></div>
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1100px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Vence</th>
                    <th class="px-3 py-3">Canal</th>
                    <th class="px-3 py-3">Mensaje</th>
                    <th class="px-3 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $typeClass = str_starts_with($notification->type, 'grace_')
                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'
                            : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200';
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
                    <tr class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide <?php echo e($typeClass); ?>">
                                <?php echo e($typeLabel); ?>

                            </span>
                        </td>
                        <td class="px-3 py-3 font-semibold dark:text-slate-100"><?php echo e($notification->gym?->name ?? 'N/D'); ?></td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e($notification->subscription?->plan_name ?? '-'); ?></td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e($notification->subscription?->ends_at?->toDateString() ?? '-'); ?></td>
                        <td class="px-3 py-3 dark:text-slate-200"><?php echo e($notification->channel); ?></td>
                        <td class="px-3 py-3">
                            <p id="msg-<?php echo e($notification->id); ?>" class="max-w-md whitespace-pre-wrap break-words text-xs text-slate-700 dark:text-slate-200"><?php echo e($notification->message_snapshot); ?></p>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                        class="rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-600"
                                        data-copy-target="msg-<?php echo e($notification->id); ?>">
                                    Copiar mensaje
                                </button>
                                <form method="POST" action="<?php echo e(route('superadmin.notifications.sent', $notification->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'success']); ?>Marcar enviado <?php echo $__env->renderComponent(); ?>
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
                                <form method="POST" action="<?php echo e(route('superadmin.notifications.skipped', $notification->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'danger']); ?>Omitir <?php echo $__env->renderComponent(); ?>
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
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            No hay notificaciones pendientes para esta fecha.
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

<?php $__env->startPush('scripts'); ?>
<script>
    document.querySelectorAll('[data-copy-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.getAttribute('data-copy-target');
            const element = document.getElementById(id);
            if (!element) {
                return;
            }

            navigator.clipboard.writeText(element.innerText).then(function () {
                const original = button.innerText;
                button.innerText = 'Copiado';
                setTimeout(function () {
                    button.innerText = original;
                }, 1000);
            });
        });
    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/notifications/index.blade.php ENDPATH**/ ?>