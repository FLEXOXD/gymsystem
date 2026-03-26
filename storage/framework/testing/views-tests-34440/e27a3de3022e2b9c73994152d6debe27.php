

<?php $__env->startSection('title', 'Solicitudes de cotización'); ?>
<?php $__env->startSection('page-title', 'Solicitudes de cotización'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $panelTimezone = trim((string) (auth()->user()?->timezone ?? ''));
        if (
            $panelTimezone === ''
            || $panelTimezone === 'UTC'
            || ! in_array($panelTimezone, timezone_identifiers_list(), true)
        ) {
            $panelTimezone = 'America/Guayaquil';
        }
        $reviewedCount = max(0, (int) $totalCount - (int) $unreadCount);
    ?>
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Leads comerciales</span>
                    <h2 class="sa-title">Cotizaciones claras para leer, priorizar y responder sin perder contexto.</h2>
                    <p class="sa-subtitle">
                        El listado queda a la izquierda y el detalle a la derecha para trabajar rapido sin abrir pantallas extra.
                    </p>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Uso recomendado</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Lee primero las pendientes</strong>
                            <span>Las solicitudes sin revisar quedan arriba.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Filtra por plan o pais</strong>
                            <span>Sirve para separar leads de sucursales, premium o general.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Marca al revisar</strong>
                            <span>El estado no cambia solo por abrir el detalle.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total leads</p>
                <p class="sa-stat-value"><?php echo e($totalCount); ?></p>
                <p class="sa-stat-meta">Solicitudes registradas en la bandeja.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Pendientes</p>
                <p class="sa-stat-value"><?php echo e($unreadCount); ?></p>
                <p class="sa-stat-meta">Solicitudes aun sin revisar.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Revisadas</p>
                <p class="sa-stat-value"><?php echo e($reviewedCount); ?></p>
                <p class="sa-stat-meta">Leads ya abiertos y procesados.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Visibles</p>
                <p class="sa-stat-value"><?php echo e($quotes->count()); ?></p>
                <p class="sa-stat-meta">Resultados del filtro actual.</p>
            </article>
        </section>

    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Solicitudes de cotización','subtitle' => 'Leads enviados desde el modal comercial de la landing principal.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Solicitudes de cotización','subtitle' => 'Leads enviados desde el modal comercial de la landing principal.']); ?>
        <?php if($unreadCount > 0): ?>
            <div class="mb-4 rounded-xl border border-cyan-300 bg-cyan-50 px-4 py-3 text-sm text-cyan-900 dark:border-cyan-500/30 dark:bg-cyan-900/20 dark:text-cyan-100">
                <p class="font-bold">Tienes <?php echo e($unreadCount); ?> solicitud(es) pendientes de revisar.</p>
                <p class="mt-1">Abre cada solicitud para ver telefono, pais, cantidad de personal y observaciones.</p>
            </div>
        <?php endif; ?>

        <div class="sa-toolbar mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                    <span class="sa-pill is-neutral">
                        Total: <?php echo e($totalCount); ?>

                    </span>
                    <span class="sa-pill is-info">
                        Sin revisar: <?php echo e($unreadCount); ?>

                    </span>
                </div>

                <form method="GET" action="<?php echo e(route('superadmin.quotations.index')); ?>" class="grid w-full gap-2 sm:w-auto sm:grid-cols-[150px_260px_auto]">
                    <select name="status" class="ui-input">
                        <option value="all" <?php if($filters['status'] === 'all'): echo 'selected'; endif; ?>>Todos</option>
                        <option value="unread" <?php if($filters['status'] === 'unread'): echo 'selected'; endif; ?>>Sin revisar</option>
                        <option value="read" <?php if($filters['status'] === 'read'): echo 'selected'; endif; ?>>Revisados</option>
                    </select>
                    <input type="text" name="q" value="<?php echo e($filters['q']); ?>" class="ui-input" placeholder="Buscar por nombre, correo, pais o plan">
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
                </form>
            </div>
        </div>

        <div class="sa-split-shell">
            <aside class="sa-split-pane">
                <div class="sa-split-scroll max-h-[68vh] overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $quotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $fullName = trim($quote->first_name.' '.$quote->last_name);
                            $isActive = $selectedQuote && (int) $selectedQuote->id === (int) $quote->id;
                            $isUnread = $quote->read_at === null;
                            $phoneDisplay = trim($quote->phone_country_code.' '.$quote->phone_number);
                            $planLabel = trim((string) ($quote->requested_plan ?? ''));
                            $planLabel = $planLabel !== '' ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $planLabel)) : '';
                            $receivedAt = $quote->created_at?->copy()->timezone($panelTimezone);
                        ?>
                        <a href="<?php echo e(route('superadmin.quotations.show', array_merge(['quote' => $quote->id], request()->only(['status', 'q', 'page'])))); ?>"
                           class="sa-split-item <?php echo e($isActive ? 'is-active' : ''); ?>">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100"><?php echo e($fullName !== '' ? $fullName : 'Sin nombre'); ?></p>
                                <?php if($isUnread): ?>
                                    <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-cyan-500"></span>
                                <?php endif; ?>
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300"><?php echo e($quote->email); ?></p>
                            <p class="mt-1 text-xs text-slate-600 dark:text-slate-300"><?php echo e($phoneDisplay !== '' ? $phoneDisplay : 'Sin telefono'); ?></p>
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                <span class="sa-status-chip is-neutral">
                                    <?php echo e($quote->country); ?>

                                </span>
                                <span class="sa-status-chip is-success">
                                    <?php echo e($quote->professionals_count); ?> profesionales
                                </span>
                                <?php if($planLabel !== ''): ?>
                                    <span class="sa-status-chip is-info">
                                        <?php echo e($planLabel); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="mt-2 text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                <?php echo e($receivedAt?->format('d/m/Y H:i')); ?>

                            </p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="sa-empty-row">
                            No hay solicitudes con este filtro.
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="sa-split-detail">
                <?php if($selectedQuote): ?>
                    <?php
                        $selectedFullName = trim($selectedQuote->first_name.' '.$selectedQuote->last_name);
                        $selectedPlanLabel = trim((string) ($selectedQuote->requested_plan ?? ''));
                        $selectedPlanLabel = $selectedPlanLabel !== '' ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $selectedPlanLabel)) : '';
                        $requestTypeLabel = $selectedPlanLabel !== '' ? 'Plan '.$selectedPlanLabel : 'Cotizacion general';
                        $selectedReceivedAt = $selectedQuote->created_at?->copy()->timezone($panelTimezone);
                    ?>
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 pb-3 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100"><?php echo e($selectedFullName !== '' ? $selectedFullName : 'Sin nombre'); ?></h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300"><?php echo e($selectedQuote->email); ?></p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                Recibido: <?php echo e($selectedReceivedAt?->format('d/m/Y H:i')); ?>

                            </p>
                        </div>
                        <?php if($selectedQuote->read_at === null): ?>
                            <div class="sa-action-row">
                                <form method="POST" action="<?php echo e(route('superadmin.quotations.read', $selectedQuote->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'secondary']); ?>Marcar como revisada <?php echo $__env->renderComponent(); ?>
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
                        <?php else: ?>
                            <span class="sa-status-chip is-neutral">
                                Revisada
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Telefono</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e(trim($selectedQuote->phone_country_code.' '.$selectedQuote->phone_number)); ?></p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Pais</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($selectedQuote->country); ?></p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Profesionales</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($selectedQuote->professionals_count); ?></p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Tipo de solicitud</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($requestTypeLabel); ?></p>
                        </article>
                        <article class="sa-mini-card">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Estado</p>
                            <p class="mt-2">
                                <span class="<?php echo e($selectedQuote->read_at === null ? 'sa-status-chip is-warning' : 'sa-status-chip is-success'); ?>">
                                    <?php echo e($selectedQuote->read_at === null ? 'Pendiente' : 'Revisada'); ?>

                                </span>
                            </p>
                        </article>
                    </div>

                    <article class="sa-message-surface mt-4">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Comentarios</p>
                        <div class="mt-2 whitespace-pre-wrap break-words">
                            <?php echo e($selectedQuote->notes !== null && trim($selectedQuote->notes) !== '' ? $selectedQuote->notes : 'Sin comentarios adicionales.'); ?>

                        </div>
                    </article>
                <?php else: ?>
                    <div class="py-14 text-center text-sm text-slate-500 dark:text-slate-300">
                        Selecciona una solicitud para ver el detalle.
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <div class="mt-4">
            <?php echo e($quotes->links()); ?>

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

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/quotations/index.blade.php ENDPATH**/ ?>