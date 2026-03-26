<?php if($showBranchContextSwitcher): ?>
    <?php
        $isGlobalContextCardActive = ($showGlobalBranchOption ?? true) && ($isAdminGlobalContext || $isGlobalScope);
        $branchContextExpandedByDefault = request()->routeIs('reception.*');
        $networkGymCount = (int) $branchContextOptions->count();
        $linkedBranchCount = max(0, $networkGymCount - 1);
        $activeContextSummary = $isGlobalContextCardActive
            ? 'Vista consolidada para revisar toda la red. Elige una sede puntual cuando necesites operar clientes, caja o planes.'
            : 'Cambia entre sede principal y sucursales desde una sola pieza, con el contexto activo siempre claro.';
        $activeContextStatus = $isGlobalContextCardActive ? 'Modo lectura' : 'Sede actual';
    ?>

    <section class="branch-context-shell">
        <details class="group branch-context-details" <?php if($branchContextExpandedByDefault): ?> open <?php endif; ?>>
            <summary class="branch-context-summary">
                <div class="branch-context-summary-button">
                    <div class="branch-context-summary-main">
                        <div class="branch-context-summary-copy">
                            <p class="branch-context-eyebrow">Selector de sucursales</p>
                            <div class="branch-context-summary-row">
                                <div class="branch-context-summary-copy">
                                    <h2 class="branch-context-title"><?php echo e($activeBranchContextTitle); ?></h2>
                                    <p class="branch-context-copy"><?php echo e($activeContextSummary); ?></p>
                                </div>
                                <div class="branch-context-pills">
                                    <span class="branch-context-kind"><?php echo e($activeBranchContextBadge ?? 'Activa'); ?></span>
                                    <span class="branch-context-status"><?php echo e($activeContextStatus); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="branch-context-summary-meta">
                            <div class="branch-context-summary-stats">
                                <span class="branch-context-stat">
                                    <span class="branch-context-stat-figure"><?php echo e($networkGymCount); ?></span>
                                    <span class="branch-context-stat-label">Sedes visibles</span>
                                </span>
                                <span class="branch-context-stat">
                                    <span class="branch-context-stat-figure"><?php echo e($linkedBranchCount); ?></span>
                                    <span class="branch-context-stat-label">Sucursales</span>
                                </span>
                            </div>
                            <p class="branch-context-location">
                                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M10 17s5-4.35 5-8.2A5 5 0 0 0 5 8.8C5 12.65 10 17 10 17Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="10" cy="8.8" r="1.8" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <span><?php echo e($activeBranchContextAddress); ?></span>
                            </p>
                        </div>
                    </div>

                    <span class="branch-context-toggle">
                        Cambiar sede
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </div>
            </summary>

            <div class="branch-context-panel">
                <div class="branch-context-panel-head">
                    <div>
                        <p class="branch-context-panel-kicker">Elige dónde vas a trabajar</p>
                        <p class="branch-context-panel-copy">
                            La vista global sirve para comparar toda la red. Una sede puntual habilita la operación diaria sin mezclar datos entre sucursales.
                        </p>
                    </div>
                </div>

                <div class="branch-context-grid">
                    <?php if(($showGlobalBranchOption ?? true)): ?>
                        <a href="<?php echo e($globalContextUrl); ?>"
                           class="branch-context-card block is-global-card <?php echo e($isGlobalContextCardActive ? 'is-active' : ''); ?>"
                           <?php if($isGlobalContextCardActive): ?> aria-current="page" <?php endif; ?>>
                            <div class="branch-context-card-head">
                                <div class="branch-context-card-head-main">
                                    <span class="branch-context-card-icon" aria-hidden="true">
                                        <svg viewBox="0 0 20 20" fill="none">
                                            <rect x="3.5" y="4" width="5.5" height="5.5" rx="1.2" stroke="currentColor" stroke-width="1.5"/>
                                            <rect x="11" y="4" width="5.5" height="5.5" rx="1.2" stroke="currentColor" stroke-width="1.5"/>
                                            <rect x="7.25" y="10.5" width="5.5" height="5.5" rx="1.2" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="branch-context-card-badges">
                                    <span class="branch-context-kind">Solo lectura</span>
                                    <?php if($isGlobalContextCardActive): ?>
                                        <span class="branch-context-status">Actual</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="branch-context-card-body">
                                <p class="branch-context-card-label">Vista consolidada</p>
                                <h3 class="branch-context-card-heading">Admin global</h3>
                                <p class="branch-context-card-summary">
                                    Compara toda la red antes de bajar a una sede puntual para operar caja, clientes, membresias o planes.
                                </p>
                            </div>

                            <div class="branch-context-card-footer">
                                <p class="branch-context-meta"><?php echo e($linkedBranchCount); ?> sucursal(es) vinculadas + sede principal</p>
                                <span class="branch-context-card-action">
                                    <?php echo e($isGlobalContextCardActive ? 'Viendo red' : 'Abrir vista'); ?>

                                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M7 5l6 5-6 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    <?php endif; ?>

                    <?php $__currentLoopData = $branchContextOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branchOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $branchSlug = (string) ($branchOption['slug'] ?? '');
                            $branchKind = (string) ($branchOption['kind'] ?? 'branch');
                            $isActiveBranchOption = $branchSlug === $activeBranchContextSlug && ! $isGlobalContextCardActive;
                            $branchKindLabel = (string) ($branchOption['kind_label'] ?? 'Sucursal');
                            $branchActionLabel = $isActiveBranchOption
                                ? 'Trabajando aqui'
                                : ($branchKind === 'hub' ? 'Abrir sede principal' : 'Abrir sede');
                            $branchCardLabel = $branchKind === 'hub' ? 'Nodo principal' : 'Operacion puntual';
                        ?>

                        <a href="<?php echo e((string) ($branchOption['url'] ?? '#')); ?>"
                           class="branch-context-card block <?php echo e($isActiveBranchOption ? 'is-active' : ''); ?>"
                           <?php if($isActiveBranchOption): ?> aria-current="page" <?php endif; ?>>
                            <div class="branch-context-card-head">
                                <div class="branch-context-card-head-main">
                                    <span class="branch-context-card-icon" aria-hidden="true">
                                        <?php if($branchKind === 'hub'): ?>
                                            <svg viewBox="0 0 20 20" fill="none">
                                                <path d="M4.5 16V8.75L10 4.5l5.5 4.25V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M7.5 16v-3.5h5V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg viewBox="0 0 20 20" fill="none">
                                                <path d="M5 16V6.5A1.5 1.5 0 0 1 6.5 5h7A1.5 1.5 0 0 1 15 6.5V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M8 8.25h4M8 11h4M9 16v-2.5h2V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="branch-context-card-badges">
                                    <span class="branch-context-kind"><?php echo e($branchKindLabel); ?></span>
                                    <?php if($isActiveBranchOption): ?>
                                        <span class="branch-context-status">Actual</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="branch-context-card-body">
                                <p class="branch-context-card-label"><?php echo e($branchCardLabel); ?></p>
                                <h3 class="branch-context-card-heading"><?php echo e((string) ($branchOption['name'] ?? 'Sucursal')); ?></h3>
                                <p class="branch-context-card-summary">
                                    <?php echo e((string) ($branchOption['summary'] ?? 'Cambia el contexto operativo de esta sede.')); ?>

                                </p>
                            </div>

                            <div class="branch-context-card-footer">
                                <p class="branch-context-meta"><?php echo e((string) ($branchOption['address'] ?? '-')); ?></p>
                                <span class="branch-context-card-action">
                                    <?php echo e($branchActionLabel); ?>

                                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M7 5l6 5-6 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </details>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/branch-context-switcher.blade.php ENDPATH**/ ?>