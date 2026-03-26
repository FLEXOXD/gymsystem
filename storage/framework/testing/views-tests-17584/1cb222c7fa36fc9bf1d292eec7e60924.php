<?php
    $progressStats = collect($progressOverview['stats'] ?? []);
    $alerts = collect($progressOverview['alerts'] ?? []);
    $prediction = (array) ($progressOverview['prediction'] ?? []);
    $weeklyGoal = (array) ($progressOverview['weekly_goal'] ?? []);
    $bodyState = (array) ($progressOverview['body_state'] ?? []);
    $timeline = (array) ($progressOverview['timeline'] ?? []);
    $timelineEntries = collect($timeline['entries'] ?? []);
    $training = (array) ($progressOverview['training'] ?? []);
    $profile = (array) ($progressOverview['profile'] ?? []);
    $profileReady = (bool) ($profile['ready'] ?? false);
    $canManageClientAccounts = (bool) ($canManageClientAccounts ?? false);
    $membership = (array) ($progressOverview['membership'] ?? []);
    $performance = (array) ($progressOverview['performance'] ?? []);
    $snapshotSourceLabel = trim((string) ($progressOverview['snapshot_source_label'] ?? ''));

    $toneClasses = [
        'success' => 'border-emerald-300/70 bg-emerald-500/10 text-emerald-900 dark:border-emerald-400/30 dark:bg-emerald-400/15 dark:text-emerald-100',
        'warning' => 'border-amber-300/70 bg-amber-500/10 text-amber-900 dark:border-amber-400/30 dark:bg-amber-400/15 dark:text-amber-100',
        'danger' => 'border-rose-300/70 bg-rose-500/10 text-rose-900 dark:border-rose-400/30 dark:bg-rose-400/15 dark:text-rose-100',
        'info' => 'border-cyan-300/70 bg-cyan-500/10 text-cyan-900 dark:border-cyan-400/30 dark:bg-cyan-400/15 dark:text-cyan-100',
        'muted' => 'border-slate-300/70 bg-slate-100 text-slate-900 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-100',
    ];
    $barClasses = [
        'force' => 'bg-sky-500',
        'resistance' => 'bg-cyan-500',
        'discipline' => 'bg-emerald-500',
        'recovery' => 'bg-amber-500',
    ];
?>

<?php if (! $__env->hasRenderedOnce('1f0f7006-6a32-42c4-a776-1ef611ae4924')): $__env->markAsRenderedOnce('1f0f7006-6a32-42c4-a776-1ef611ae4924'); ?>
    <?php $__env->startPush('styles'); ?>
        <style>
            .client-progress-calendar {
                border: 1px solid color-mix(in srgb, var(--accent) 22%, var(--border));
                background:
                    radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 20%, transparent), transparent 34%),
                    linear-gradient(180deg, color-mix(in srgb, var(--card) 96%, transparent), color-mix(in srgb, var(--card-2) 92%, transparent));
                border-radius: 22px;
                padding: 16px;
            }

            .client-progress-calendar-top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 14px;
                margin-bottom: 12px;
            }

            .client-progress-calendar-month {
                font-size: 13px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--text) 92%, #ffffff);
            }

            .client-progress-calendar-help {
                margin-top: 4px;
                font-size: 12px;
                line-height: 1.4;
                color: color-mix(in srgb, var(--muted) 90%, #ffffff);
            }

            .client-progress-calendar-legend {
                display: flex;
                flex-wrap: wrap;
                gap: 8px 10px;
                justify-content: flex-end;
            }

            .client-progress-calendar-legend-item {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                font-weight: 700;
                color: color-mix(in srgb, var(--muted) 90%, #ffffff);
            }

            .client-progress-calendar-dot {
                width: 9px;
                height: 9px;
                border-radius: 999px;
                display: inline-block;
            }

            .client-progress-calendar-dot-trained {
                background: #22c55e;
                box-shadow: 0 0 0 1px rgb(34 197 94 / 0.42);
            }

            .client-progress-calendar-dot-rest {
                background: #94a3b8;
                box-shadow: 0 0 0 1px rgb(148 163 184 / 0.45);
            }

            .client-progress-calendar-dot-missed {
                background: #f59e0b;
                box-shadow: 0 0 0 1px rgb(245 158 11 / 0.45);
            }

            .client-progress-calendar-dot-pending {
                background: rgb(148 163 184 / 0.45);
                box-shadow: 0 0 0 1px rgb(71 85 105 / 0.45);
            }

            .client-progress-calendar-weekdays,
            .client-progress-calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, minmax(0, 1fr));
                gap: 8px;
            }

            .client-progress-calendar-weekdays {
                margin-bottom: 8px;
            }

            .client-progress-calendar-weekday {
                text-align: center;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--muted) 92%, #ffffff);
            }

            .client-progress-calendar-cell {
                position: relative;
                min-height: 54px;
                border-radius: 16px;
                border: 1px solid color-mix(in srgb, var(--border) 72%, transparent);
                background: color-mix(in srgb, var(--card) 92%, transparent);
                padding: 9px 8px;
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.03);
                transition: transform .14s ease, border-color .14s ease, box-shadow .14s ease;
            }

            .client-progress-calendar-cell:hover {
                transform: translateY(-1px);
            }

            .client-progress-calendar-cell-placeholder {
                background: color-mix(in srgb, var(--card) 55%, transparent);
                border-style: dashed;
                opacity: .35;
                box-shadow: none;
                pointer-events: none;
            }

            .client-progress-calendar-cell-trained {
                border-color: color-mix(in srgb, #22c55e 68%, var(--border));
                background: linear-gradient(180deg, rgb(34 197 94 / 0.19), rgb(22 163 74 / 0.11));
            }

            .client-progress-calendar-cell-rest {
                border-color: color-mix(in srgb, var(--border) 86%, transparent);
                background: color-mix(in srgb, var(--card-2) 78%, transparent);
            }

            .client-progress-calendar-cell-missed {
                border-color: color-mix(in srgb, #f59e0b 62%, var(--border));
                background: linear-gradient(180deg, rgb(245 158 11 / 0.16), rgb(251 191 36 / 0.08));
            }

            .client-progress-calendar-cell-pending {
                border-style: dashed;
                border-color: color-mix(in srgb, var(--muted) 55%, transparent);
                background: color-mix(in srgb, var(--card) 72%, transparent);
                opacity: .82;
            }

            .client-progress-calendar-cell-today {
                box-shadow:
                    0 0 0 2px color-mix(in srgb, var(--accent) 34%, transparent),
                    0 10px 24px color-mix(in srgb, var(--accent) 16%, transparent);
            }

            .client-progress-calendar-day {
                font-size: 14px;
                font-weight: 900;
                line-height: 1;
                color: color-mix(in srgb, var(--text) 95%, #ffffff);
            }

            .client-progress-calendar-status {
                position: absolute;
                left: 8px;
                right: 8px;
                bottom: 8px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 6px;
            }

            .client-progress-calendar-chip {
                display: inline-flex;
                align-items: center;
                min-height: 18px;
                padding: 0 7px;
                border-radius: 999px;
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            .client-progress-calendar-chip-trained {
                background: rgb(34 197 94 / 0.18);
                color: #166534;
            }

            .client-progress-calendar-chip-rest {
                background: rgb(148 163 184 / 0.16);
                color: color-mix(in srgb, var(--text) 75%, #ffffff);
            }

            .client-progress-calendar-chip-missed {
                background: rgb(245 158 11 / 0.18);
                color: #92400e;
            }

            .client-progress-calendar-chip-pending {
                background: rgb(148 163 184 / 0.12);
                color: color-mix(in srgb, var(--muted) 88%, #ffffff);
            }

            .client-progress-calendar-marker {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                flex: 0 0 auto;
            }

            @media (prefers-color-scheme: dark) {
                .client-progress-calendar-chip-trained {
                    color: #dcfce7;
                }

                .client-progress-calendar-chip-missed {
                    color: #fef3c7;
                }
            }

            @media (max-width: 860px) {
                .client-progress-calendar-top {
                    flex-direction: column;
                }

                .client-progress-calendar-legend {
                    justify-content: flex-start;
                }
            }

            @media (max-width: 640px) {
                .client-progress-calendar {
                    padding: 12px;
                }

                .client-progress-calendar-weekdays,
                .client-progress-calendar-grid {
                    gap: 6px;
                }

                .client-progress-calendar-cell {
                    min-height: 46px;
                    padding: 7px 6px;
                    border-radius: 13px;
                }

                .client-progress-calendar-day {
                    font-size: 12px;
                }

                .client-progress-calendar-chip {
                    padding: 0 5px;
                    font-size: 9px;
                }
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<div class="space-y-6">
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = $progressStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $tone = (string) ($stat['tone'] ?? 'muted');
            ?>
            <article class="rounded-2xl border p-4 shadow-sm <?php echo e($toneClasses[$tone] ?? $toneClasses['muted']); ?>">
                <p class="text-xs font-semibold uppercase tracking-widest opacity-80"><?php echo e($stat['label'] ?? 'Dato'); ?></p>
                <p class="mt-2 text-2xl font-black"><?php echo e($stat['value'] ?? '-'); ?></p>
                <p class="mt-1 text-xs opacity-80"><?php echo e($stat['meta'] ?? ''); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <div class="client-layout-wide">
        <div class="space-y-6">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Analisis de rendimiento','subtitle' => 'Lectura operativa del progreso del cliente.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Analisis de rendimiento','subtitle' => 'Lectura operativa del progreso del cliente.']); ?>
                <div class="grid gap-4 lg:grid-cols-[1.35fr_.65fr]">
                    <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Estado actual</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => match ((string) ($performance['tone'] ?? 'muted')) { 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger', 'info' => 'info', default => 'muted' }]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(match ((string) ($performance['tone'] ?? 'muted')) { 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger', 'info' => 'info', default => 'muted' })]); ?>
                                <?php echo e($performance['label'] ?? 'Sin lectura'); ?>

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
                            <span class="text-sm text-slate-700 dark:text-slate-300">Membresia: <?php echo e($membership['status_label'] ?? 'Sin membresia'); ?></span>
                        </div>
                        <p class="mt-3 text-sm text-slate-700 dark:text-slate-300"><?php echo e($performance['summary'] ?? 'Sin resumen disponible.'); ?></p>
                        <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">Ultima asistencia: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($progressOverview['last_attendance_label'] ?? 'Sin asistencia'); ?></span></p>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">Ventana activa: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membership['period_window_label'] ?? 'Sin membresia activa'); ?></span></p>
                        <?php if($snapshotSourceLabel !== ''): ?>
                            <p class="mt-3 rounded-lg border border-cyan-300/60 bg-cyan-500/10 px-3 py-2 text-xs text-cyan-900 dark:border-cyan-400/30 dark:bg-cyan-400/10 dark:text-cyan-100"><?php echo e($snapshotSourceLabel); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="rounded-xl border border-slate-300 bg-slate-100 p-4 dark:border-white/10 dark:bg-slate-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actividad en vivo</p>
                        <p class="mt-2 text-4xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) ($progressOverview['live_clients_count'] ?? 0)); ?></p>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">clientes presentes ahora</p>
                        <div class="mt-4 space-y-2 text-sm text-slate-700 dark:text-slate-300">
                            <p>Vence: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membership['ends_at_label'] ?? 'N/A'); ?></span></p>
                            <p>Dias restantes: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($membership['days_remaining_label'] ?? 'N/A'); ?></span></p>
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

            <div class="grid gap-6 lg:grid-cols-2">
                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Prediccion','subtitle' => 'Proyeccion simple basada en constancia y objetivo.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Prediccion','subtitle' => 'Proyeccion simple basada en constancia y objetivo.']); ?>
                    <p class="text-xs font-semibold uppercase tracking-widest text-cyan-700 dark:text-cyan-300"><?php echo e($prediction['rhythm_label'] ?? 'Sin datos'); ?> | Constancia: <?php echo e((int) ($prediction['consistency_percent'] ?? 0)); ?>%</p>
                    <p class="mt-3 text-lg font-black text-slate-900 dark:text-slate-100"><?php echo e($prediction['primary_line'] ?? 'Sin prediccion disponible.'); ?></p>
                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-300"><?php echo e($prediction['secondary_line'] ?? ''); ?></p>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400"><?php echo e($prediction['context_line'] ?? ''); ?></p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Meta semanal','subtitle' => 'Seguimiento de frecuencia y adherencia.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Meta semanal','subtitle' => 'Seguimiento de frecuencia y adherencia.']); ?>
                    <p class="text-lg font-black text-slate-900 dark:text-slate-100"><?php echo e((int) ($weeklyGoal['visits'] ?? 0)); ?> de <?php echo e((int) ($weeklyGoal['target'] ?? 0)); ?> sesiones</p>
                    <div class="mt-3 h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                        <span class="block h-2 rounded-full bg-emerald-500" style="width: <?php echo e((int) ($weeklyGoal['completion_percent'] ?? 0)); ?>%;"></span>
                    </div>
                    <div class="mt-3 grid gap-2 text-sm text-slate-700 dark:text-slate-300 sm:grid-cols-3">
                        <p>Completado: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e((int) ($weeklyGoal['completion_percent'] ?? 0)); ?>%</span></p>
                        <p>Faltan: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e((int) ($weeklyGoal['remaining'] ?? 0)); ?></span></p>
                        <p>Dias: <span class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e((int) ($weeklyGoal['days_left_week'] ?? 0)); ?></span></p>
                    </div>
                    <p class="mt-3 text-sm text-slate-700 dark:text-slate-300"><?php echo e($weeklyGoal['commitment_line'] ?? ''); ?></p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?php echo e($weeklyGoal['rest_line'] ?? ''); ?></p>
                    <?php if(! empty($weeklyGoal['alerts'])): ?>
                        <div class="mt-3 space-y-2">
                            <?php $__currentLoopData = $weeklyGoal['alerts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $alertTone = match ((string) ($alert['type'] ?? 'info')) {
                                        'success' => 'success',
                                        'warning' => 'warning',
                                        'danger' => 'danger',
                                        default => 'info',
                                    };
                                ?>
                                <p class="rounded-lg border px-3 py-2 text-xs <?php echo e($toneClasses[$alertTone] ?? $toneClasses['info']); ?>"><?php echo e($alert['text'] ?? ''); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Historial del mes','subtitle' => ''.e($timeline['month_label'] ?? 'Mes actual').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial del mes','subtitle' => ''.e($timeline['month_label'] ?? 'Mes actual').'']); ?>
                <div class="client-progress-calendar">
                    <div class="client-progress-calendar-top">
                        <div>
                            <p class="client-progress-calendar-month"><?php echo e($timeline['month_label'] ?? 'Mes actual'); ?></p>
                            <p class="client-progress-calendar-help">Calendario visual de asistencias, descansos y dias pendientes del cliente.</p>
                        </div>
                        <div class="client-progress-calendar-legend" aria-label="Leyenda de estados">
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-trained" aria-hidden="true"></span>
                                Entreno
                            </span>
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-rest" aria-hidden="true"></span>
                                Descanso
                            </span>
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-missed" aria-hidden="true"></span>
                                Falta
                            </span>
                            <span class="client-progress-calendar-legend-item">
                                <span class="client-progress-calendar-dot client-progress-calendar-dot-pending" aria-hidden="true"></span>
                                Pendiente
                            </span>
                        </div>
                    </div>

                    <div class="client-progress-calendar-weekdays" aria-hidden="true">
                        <?php $__currentLoopData = ['L', 'M', 'X', 'J', 'V', 'S', 'D']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="client-progress-calendar-weekday"><?php echo e($dayLabel); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="client-progress-calendar-grid">
                        <?php $__currentLoopData = $timelineEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $status = (string) ($entry['status'] ?? 'pending');
                                $isPlaceholder = ! empty($entry['is_placeholder']);
                                $statusLabel = match ($status) {
                                    'trained' => 'Entreno',
                                    'missed' => 'Falta',
                                    'rest' => 'Descanso',
                                    default => 'Pendiente',
                                };
                                $cellClass = match ($status) {
                                    'trained' => 'client-progress-calendar-cell-trained',
                                    'missed' => 'client-progress-calendar-cell-missed',
                                    'rest' => 'client-progress-calendar-cell-rest',
                                    default => 'client-progress-calendar-cell-pending',
                                };
                                $chipClass = match ($status) {
                                    'trained' => 'client-progress-calendar-chip-trained',
                                    'missed' => 'client-progress-calendar-chip-missed',
                                    'rest' => 'client-progress-calendar-chip-rest',
                                    default => 'client-progress-calendar-chip-pending',
                                };
                                $markerClass = match ($status) {
                                    'trained' => 'client-progress-calendar-dot-trained',
                                    'missed' => 'client-progress-calendar-dot-missed',
                                    'rest' => 'client-progress-calendar-dot-rest',
                                    default => 'client-progress-calendar-dot-pending',
                                };
                                $title = trim((string) (($entry['weekday_short'] ?? '').' '.($entry['date'] ?? '').' - '.$statusLabel.(! empty($entry['is_today']) ? ' (hoy)' : '')));
                            ?>
                            <div
                                class="client-progress-calendar-cell <?php echo e($isPlaceholder ? 'client-progress-calendar-cell-placeholder' : $cellClass); ?> <?php echo e(! $isPlaceholder && ! empty($entry['is_today']) ? 'client-progress-calendar-cell-today' : ''); ?>"
                                <?php if(! $isPlaceholder): ?>
                                    title="<?php echo e($title); ?>"
                                    aria-label="<?php echo e($title); ?>"
                                <?php else: ?>
                                    aria-hidden="true"
                                <?php endif; ?>
                            >
                                <?php if(! $isPlaceholder): ?>
                                    <span class="client-progress-calendar-day"><?php echo e($entry['label'] ?? ''); ?></span>
                                    <div class="client-progress-calendar-status">
                                        <span class="client-progress-calendar-chip <?php echo e($chipClass); ?>"><?php echo e($statusLabel); ?></span>
                                        <span class="client-progress-calendar-dot <?php echo e($markerClass); ?> client-progress-calendar-marker" aria-hidden="true"></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

        <div class="space-y-6">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Alertas operativas','subtitle' => 'Senales para recepcion, renovacion y seguimiento.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alertas operativas','subtitle' => 'Senales para recepcion, renovacion y seguimiento.']); ?>
                <?php if($alerts->isNotEmpty()): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $alertTone = (string) ($alert['tone'] ?? 'info');
                            ?>
                            <div class="rounded-xl border px-3 py-3 <?php echo e($toneClasses[$alertTone] ?? $toneClasses['info']); ?>">
                                <p class="text-xs font-black uppercase tracking-widest"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                <p class="mt-2 text-sm"><?php echo e($alert['text'] ?? ''); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-slate-700 dark:text-slate-300">Sin alertas relevantes por ahora.</p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Estado del cuerpo','subtitle' => 'Lectura orientativa con base en constancia y carga.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Estado del cuerpo','subtitle' => 'Lectura orientativa con base en constancia y carga.']); ?>
                <p class="text-sm text-slate-700 dark:text-slate-300"><?php echo e($bodyState['summary_line'] ?? 'Sin resumen disponible.'); ?></p>
                <div class="mt-4 space-y-3">
                    <?php $__currentLoopData = ['force' => 'Fuerza', 'resistance' => 'Resistencia', 'discipline' => 'Disciplina', 'recovery' => 'Recuperacion']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $value = (int) ($bodyState[$key] ?? 0);
                        ?>
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm text-slate-700 dark:text-slate-300">
                                <span><?php echo e($label); ?></span>
                                <span class="font-bold text-slate-900 dark:text-slate-100"><?php echo e($value); ?></span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-800">
                                <span class="block h-2 rounded-full <?php echo e($barClasses[$key] ?? 'bg-cyan-500'); ?>" style="width: <?php echo e($value); ?>%;"></span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400"><?php echo e($bodyState['context_line'] ?? ''); ?></p>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Perfil fisico','subtitle' => 'Datos base usados para el analisis.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Perfil fisico','subtitle' => 'Datos base usados para el analisis.']); ?>
                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Objetivo</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['goal_label'] ?? 'Sin objetivo'); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Principal</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['primary_goal_label'] ?? 'Sin objetivo'); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Secundario</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['secondary_goal_label'] ?? 'Sin secundario'); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Nivel</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['experience_label'] ?? 'Sin nivel'); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Dias/semana</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['days_per_week'] ?? 'N/A'); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Sesion</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['session_minutes'] ?? 'N/A'); ?> min</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Peso</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['weight_label'] ?? 'N/A'); ?></dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-slate-600 dark:text-slate-400">Altura</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($profile['height_label'] ?? 'N/A'); ?></dd>
                    </div>
                </dl>
                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Limitaciones: <?php echo e($profile['limitations_label'] ?? 'Sin datos'); ?></p>
                <?php if(! empty($profile['updated_label'])): ?>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Ultima actualizacion: <?php echo e($profile['updated_label']); ?></p>
                <?php endif; ?>
                <?php if(! $profileReady): ?>
                    <div class="client-empty-state mt-4 rounded-xl border border-dashed border-slate-400 bg-slate-50 p-3">
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Completa datos fisicos para activar recomendaciones mas precisas.</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <?php if($canManageClientAccounts): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','xOn:click' => 'setTab(\'app_access\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','x-on:click' => 'setTab(\'app_access\')']); ?>Configurar usuario app <?php echo $__env->renderComponent(); ?>
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
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'secondary','xOn:click' => 'setTab(\'credentials\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'secondary','x-on:click' => 'setTab(\'credentials\')']); ?>Enviar acceso PWA <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => (string) ($training['title'] ?? 'Orientacion de entrenamiento'),'subtitle' => 'Resumen rapido del enfoque recomendado.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string) ($training['title'] ?? 'Orientacion de entrenamiento')),'subtitle' => 'Resumen rapido del enfoque recomendado.']); ?>
                <div class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                    <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($training['objective_line'] ?? 'Sin objetivo'); ?></p>
                    <p><?php echo e($training['focus_line'] ?? ''); ?></p>
                    <p><?php echo e($training['rhythm_line'] ?? ''); ?></p>
                    <p><?php echo e($training['adaptation_line'] ?? ''); ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($training['context_line'] ?? ''); ?></p>
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
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/partials/_tab_progress.blade.php ENDPATH**/ ?>