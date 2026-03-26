

<?php $__env->startSection('title', 'Sugerencias'); ?>
<?php $__env->startSection('page-title', 'Sugerencias de gimnasios'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $suggestionItems = method_exists($suggestions, 'getCollection') ? $suggestions->getCollection() : collect($suggestions);
        $suggestionTotal = method_exists($suggestions, 'total') ? $suggestions->total() : $suggestionItems->count();
        $pendingSuggestionCount = $suggestionItems->where('status', 'pending')->count();
        $reviewedSuggestionCount = $suggestionItems->where('status', 'reviewed')->count();
        $reactivationSuggestionCount = $suggestionItems
            ->filter(fn ($suggestion) => mb_strtolower(trim((string) ($suggestion->subject ?? ''))) === 'solicitud de reactivacion de suscripcion')
            ->count();
    ?>
    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Feedback de gimnasios</span>
                    <h2 class="sa-title">Sugerencias y reactivaciones en una bandeja mas util para decidir rapido.</h2>
                    <p class="sa-subtitle">
                        Filtras por estado, gimnasio o texto y ves de inmediato que requiere respuesta, comprobante o revision interna.
                    </p>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Pendientes primero</strong>
                            <span>Las solicitudes por atender quedan visibles con su estado.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Reactivaciones detectadas</strong>
                            <span>Las peticiones de reactivacion se distinguen rapido dentro del listado.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Comprobante cuando exista</strong>
                            <span>Si llega evidencia, la tabla deja el acceso directo al archivo.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total sugerencias</p>
                <p class="sa-stat-value"><?php echo e($suggestionTotal); ?></p>
                <p class="sa-stat-meta">Registros visibles bajo el filtro actual.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Pendientes</p>
                <p class="sa-stat-value"><?php echo e($pendingSuggestionCount); ?></p>
                <p class="sa-stat-meta">Casos por revisar en esta pagina.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Revisadas</p>
                <p class="sa-stat-value"><?php echo e($reviewedSuggestionCount); ?></p>
                <p class="sa-stat-meta">Registros ya atendidos en la vista actual.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Reactivaciones</p>
                <p class="sa-stat-value"><?php echo e($reactivationSuggestionCount); ?></p>
                <p class="sa-stat-meta">Solicitudes de reactivacion detectadas en esta pagina.</p>
            </article>
        </section>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Sugerencias de gimnasios','subtitle' => 'Bandeja de mejoras y solicitudes de reactivacion enviadas por gimnasios.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Sugerencias de gimnasios','subtitle' => 'Bandeja de mejoras y solicitudes de reactivacion enviadas por gimnasios.']); ?>
        <form method="GET" action="<?php echo e(route('superadmin.suggestions.index')); ?>" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-sm font-semibold ui-muted">
                Estado
                <select name="status" class="ui-input mt-1 block min-w-[170px]">
                    <option value="pending" <?php if(($filters['status'] ?? 'pending') === 'pending'): echo 'selected'; endif; ?>>Pendientes</option>
                    <option value="reviewed" <?php if(($filters['status'] ?? '') === 'reviewed'): echo 'selected'; endif; ?>>Revisadas</option>
                    <option value="all" <?php if(($filters['status'] ?? '') === 'all'): echo 'selected'; endif; ?>>Todas</option>
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Gimnasio
                <select name="gym_id" class="ui-input mt-1 block min-w-[220px]">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $gyms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gym): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($gym->id); ?>" <?php if((int) ($filters['gym_id'] ?? 0) === (int) $gym->id): echo 'selected'; endif; ?>><?php echo e($gym->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>

            <label class="text-sm font-semibold ui-muted">
                Buscar
                <input type="text" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" class="ui-input mt-1 block min-w-[260px]" placeholder="Asunto, mensaje, gym o usuario">
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
            Total: <strong><?php echo e($suggestions->total()); ?></strong>
        </div>

        <div class="sa-table-shell overflow-x-auto">
            <table class="ui-table min-w-[1180px]">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Gimnasio</th>
                        <th class="px-3 py-3">Enviado por</th>
                        <th class="px-3 py-3">Asunto</th>
                        <th class="px-3 py-3">Sugerencia</th>
                        <th class="px-3 py-3">Estado</th>
                        <th class="px-3 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isPending = $suggestion->status === 'pending';
                            $isReactivationRequest = mb_strtolower(trim((string) $suggestion->subject)) === 'solicitud de reactivacion de suscripcion';
                            $message = (string) $suggestion->message;
                            $displayMessage = $message;
                            $receiptUrl = null;
                            $receiptPath = null;
                            if (preg_match('/Ruta interna:\s*([^\r\n]+)/u', $message, $pathMatch)) {
                                $rawPath = trim((string) ($pathMatch[1] ?? ''));
                                if ($rawPath !== '') {
                                    $normalizedPath = ltrim($rawPath, '/');
                                    if (\Illuminate\Support\Str::startsWith($normalizedPath, 'storage/')) {
                                        $normalizedPath = ltrim(\Illuminate\Support\Str::after($normalizedPath, 'storage/'), '/');
                                    }
                                    if ($normalizedPath !== '') {
                                        $receiptPath = $normalizedPath;
                                    }
                                }
                            }
                            if ($receiptPath === null && preg_match('/Comprobante:\s*(https?:\/\/\S+|\/storage\/\S+)/u', $message, $receiptMatch)) {
                                $rawReceiptUrl = trim((string) ($receiptMatch[1] ?? ''));
                                if (\Illuminate\Support\Str::startsWith($rawReceiptUrl, '/storage/')) {
                                    $receiptPath = ltrim(\Illuminate\Support\Str::after($rawReceiptUrl, '/storage/'), '/');
                                } elseif (filter_var($rawReceiptUrl, FILTER_VALIDATE_URL)) {
                                    $parsedPath = parse_url($rawReceiptUrl, PHP_URL_PATH);
                                    $parsedPath = is_string($parsedPath) ? trim($parsedPath) : '';
                                    if ($parsedPath !== '' && \Illuminate\Support\Str::startsWith($parsedPath, '/storage/')) {
                                        $receiptPath = ltrim(\Illuminate\Support\Str::after($parsedPath, '/storage/'), '/');
                                    }
                                }
                            }
                            if ($receiptPath !== null && $receiptPath !== '') {
                                $receiptUrl = url('/storage/'.$receiptPath);
                            } elseif (preg_match('/Comprobante:\s*(https?:\/\/\S+|\/storage\/\S+)/u', $message, $receiptMatch)) {
                                $rawReceiptUrl = trim((string) ($receiptMatch[1] ?? ''));
                                if (\Illuminate\Support\Str::startsWith($rawReceiptUrl, '/storage/')) {
                                    $receiptUrl = url($rawReceiptUrl);
                                } else {
                                    $receiptUrl = $rawReceiptUrl !== '' ? $rawReceiptUrl : null;
                                }
                            }
                            if (is_string($receiptUrl) && $receiptUrl !== '') {
                                $normalizedLine = 'Comprobante: '.$receiptUrl;
                                $displayMessage = preg_replace('/Comprobante:\s*(https?:\/\/\S+|\/storage\/\S+)/u', $normalizedLine, $message, 1) ?? $message;
                            }
                            $statusClass = $isPending
                                ? 'sa-status-chip is-warning'
                                : 'sa-status-chip is-success';
                        ?>
                        <tr>
                            <td class="whitespace-nowrap dark:text-slate-200">
                                <?php echo e($suggestion->created_at?->format('Y-m-d H:i') ?? '-'); ?>

                            </td>
                            <td class="font-semibold dark:text-slate-100">
                                <?php echo e($suggestion->gym?->name ?? 'N/D'); ?>

                            </td>
                            <td class="dark:text-slate-200">
                                <p class="font-semibold"><?php echo e($suggestion->sender?->name ?? 'Usuario eliminado'); ?></p>
                                <p class="text-xs ui-muted"><?php echo e($suggestion->sender?->email ?? '-'); ?></p>
                            </td>
                            <td class="dark:text-slate-100">
                                <?php if($isReactivationRequest): ?>
                                    <span class="sa-status-chip is-info mb-1">Reactivacion</span>
                                <?php endif; ?>
                                <p class="font-semibold"><?php echo e($suggestion->subject); ?></p>
                            </td>
                            <td class="dark:text-slate-200">
                                <p class="max-w-lg whitespace-pre-wrap break-words text-sm"><?php echo e($displayMessage); ?></p>
                                <?php if(is_string($receiptUrl) && $receiptUrl !== ''): ?>
                                    <a href="<?php echo e($receiptUrl); ?>" target="_blank" rel="noopener" class="sa-status-chip is-info mt-2">
                                        Ver comprobante
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?php echo e($statusClass); ?>">
                                    <?php echo e($isPending ? 'Pendiente' : 'Revisada'); ?>

                                </span>
                                <?php if(! $isPending && $suggestion->reviewedBy): ?>
                                    <p class="sa-inline-note mt-1">Por <?php echo e($suggestion->reviewedBy->name); ?></p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($isPending): ?>
                                    <div class="sa-action-row">
                                        <form method="POST" action="<?php echo e(route('superadmin.suggestions.reviewed', $suggestion->id)); ?>">
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
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'success']); ?>Marcar revisada <?php echo $__env->renderComponent(); ?>
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
                                    <span class="sa-inline-note">Sin acciones</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="sa-empty-row">
                                No hay sugerencias para los filtros seleccionados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($suggestions->links()); ?>

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

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/suggestions/index.blade.php ENDPATH**/ ?>