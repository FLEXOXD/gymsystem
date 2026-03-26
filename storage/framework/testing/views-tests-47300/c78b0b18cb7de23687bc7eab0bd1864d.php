<?php if (! $__env->hasRenderedOnce('1ee4afd6-9afe-4d72-a017-8fe2db062661')): $__env->markAsRenderedOnce('1ee4afd6-9afe-4d72-a017-8fe2db062661'); ?>
    <?php $__env->startPush('styles'); ?>
        <style>
            .branch-context-shell {
                position: relative;
                overflow: hidden;
                border: 1px solid rgb(148 163 184 / 0.28);
                border-radius: 1.15rem;
                background:
                    radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 8%, transparent), transparent 42%),
                    linear-gradient(162deg, rgb(255 255 255 / 0.985), rgb(248 250 252 / 0.95));
                box-shadow: 0 26px 42px -34px rgb(15 23 42 / 0.22);
            }

            .theme-dark .branch-context-shell,
            .dark .branch-context-shell {
                border-color: rgb(71 85 105 / 0.82);
                background:
                    radial-gradient(circle at top right, color-mix(in srgb, var(--accent) 12%, transparent), transparent 42%),
                    linear-gradient(165deg, rgb(15 23 42 / 0.94), rgb(2 6 23 / 0.86));
                box-shadow: 0 30px 46px -34px rgb(2 8 23 / 0.9);
            }

            .branch-context-summary {
                list-style: none;
            }

            .branch-context-summary::-webkit-details-marker {
                display: none;
            }

            .branch-context-details[open] .branch-context-summary {
                padding-bottom: 0.2rem;
            }

            .branch-context-eyebrow {
                font-size: 0.68rem;
                font-weight: 900;
                letter-spacing: 0.15em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--accent) 34%, rgb(71 85 105));
            }

            .theme-dark .branch-context-eyebrow,
            .dark .branch-context-eyebrow {
                color: rgb(148 163 184 / 0.9);
            }

            .branch-context-title {
                color: color-mix(in srgb, var(--sidebar) 54%, rgb(15 23 42));
            }

            .theme-dark .branch-context-title,
            .dark .branch-context-title {
                color: rgb(248 250 252 / 0.98);
            }

            .branch-context-copy {
                color: rgb(71 85 105 / 0.9);
            }

            .theme-dark .branch-context-copy,
            .dark .branch-context-copy {
                color: rgb(148 163 184 / 0.9);
            }

            .branch-context-kind,
            .branch-context-status {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 1.6rem;
                border-radius: 999px;
                padding: 0.22rem 0.62rem;
                font-size: 0.63rem;
                font-weight: 900;
                letter-spacing: 0.14em;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .branch-context-kind {
                border: 1px solid color-mix(in srgb, var(--sidebar) 14%, rgb(203 213 225));
                background: color-mix(in srgb, white 92%, var(--accent) 8%);
                color: color-mix(in srgb, var(--sidebar) 46%, rgb(51 65 85));
            }

            .branch-context-status {
                border: 1px solid color-mix(in srgb, var(--accent) 28%, rgb(16 185 129 / 0.24));
                background: color-mix(in srgb, var(--accent) 14%, rgb(236 253 245));
                color: color-mix(in srgb, var(--sidebar) 44%, rgb(6 95 70));
            }

            .theme-dark .branch-context-kind {
                border-color: rgb(71 85 105 / 0.7);
                background: rgb(15 23 42 / 0.72);
                color: rgb(226 232 240 / 0.94);
            }

            .theme-dark .branch-context-status {
                border-color: rgb(34 211 238 / 0.3);
                background: rgb(8 145 178 / 0.16);
                color: rgb(207 250 254);
            }

            .branch-context-toggle {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                border-radius: 999px;
                border: 1px solid color-mix(in srgb, var(--sidebar) 14%, rgb(203 213 225));
                background: linear-gradient(180deg, rgb(255 255 255 / 0.94), color-mix(in srgb, white 93%, var(--accent) 7%));
                padding: 0.44rem 0.72rem;
                font-size: 0.7rem;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: color-mix(in srgb, var(--sidebar) 44%, rgb(71 85 105));
                transition: border-color 160ms ease, color 160ms ease, transform 160ms ease;
            }

            .theme-dark .branch-context-toggle,
            .dark .branch-context-toggle {
                border-color: rgb(71 85 105 / 0.72);
                background: rgb(15 23 42 / 0.72);
                color: rgb(226 232 240 / 0.94);
            }

            details[open] .branch-context-toggle {
                border-color: color-mix(in srgb, var(--accent) 26%, rgb(148 163 184));
                color: color-mix(in srgb, var(--sidebar) 58%, rgb(15 23 42));
            }

            .theme-dark details[open] .branch-context-toggle,
            .dark details[open] .branch-context-toggle {
                border-color: rgb(34 211 238 / 0.32);
                color: rgb(207 250 254);
            }

            .branch-context-grid {
                display: grid;
                gap: 0.8rem;
            }

            @media (min-width: 640px) {
                .branch-context-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (min-width: 1280px) {
                .branch-context-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            .branch-context-details[open] .branch-context-grid {
                margin-top: 0.9rem;
            }

            .branch-context-card {
                position: relative;
                display: flex;
                flex-direction: column;
                gap: 0.7rem;
                min-height: 7rem;
                border: 1px solid color-mix(in srgb, var(--sidebar) 16%, rgb(203 213 225));
                border-radius: 1rem;
                background: linear-gradient(180deg, rgb(255 255 255 / 0.96), color-mix(in srgb, white 92%, var(--accent) 8%));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.9), 0 16px 28px -26px rgb(15 23 42 / 0.18);
                text-decoration: none;
                transition: border-color 160ms ease, transform 160ms ease, box-shadow 180ms ease, background-color 180ms ease;
            }

            .branch-context-card::before {
                content: '';
                position: absolute;
                left: 1rem;
                right: 1rem;
                top: 0;
                height: 2px;
                border-radius: 999px;
                background: rgb(148 163 184 / 0.28);
                pointer-events: none;
            }

            .branch-context-card-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.65rem;
            }

            .branch-context-card:hover {
                transform: translateY(-1px);
                border-color: color-mix(in srgb, var(--accent) 24%, rgb(148 163 184));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.92), 0 22px 32px -24px color-mix(in srgb, var(--sidebar) 20%, transparent);
            }

            .branch-context-card.is-active {
                border-color: color-mix(in srgb, var(--accent) 34%, rgb(148 163 184));
                background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 18%, white), color-mix(in srgb, var(--sidebar) 8%, white));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.92), 0 18px 28px -24px color-mix(in srgb, var(--accent) 18%, transparent);
            }

            .branch-context-card.is-active::before {
                background: linear-gradient(90deg, color-mix(in srgb, var(--accent) 82%, white), color-mix(in srgb, var(--sidebar) 20%, transparent));
            }

            .branch-context-card.is-active .branch-context-kind {
                border-color: color-mix(in srgb, var(--accent) 28%, rgb(148 163 184));
                background: color-mix(in srgb, var(--accent) 14%, white);
                color: color-mix(in srgb, var(--sidebar) 52%, rgb(15 23 42));
            }

            .theme-dark .branch-context-card,
            .dark .branch-context-card {
                border-color: rgb(71 85 105 / 0.78);
                background: linear-gradient(160deg, rgb(15 23 42 / 0.8), rgb(15 23 42 / 0.6));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.05), 0 18px 28px -24px rgb(2 8 23 / 0.46);
            }

            .theme-dark .branch-context-card:hover,
            .dark .branch-context-card:hover {
                border-color: rgb(34 211 238 / 0.28);
                box-shadow: 0 20px 30px -24px rgb(2 8 23 / 0.84), inset 0 1px 0 rgb(255 255 255 / 0.05);
            }

            .theme-dark .branch-context-card.is-active,
            .dark .branch-context-card.is-active {
                border-color: rgb(34 211 238 / 0.34);
                background: linear-gradient(145deg, rgb(8 145 178 / 0.2), rgb(15 23 42 / 0.84));
                box-shadow: 0 22px 32px -26px rgb(2 8 23 / 0.9), inset 0 1px 0 rgb(255 255 255 / 0.06);
            }

            .theme-dark .branch-context-card::before,
            .dark .branch-context-card::before {
                background: rgb(71 85 105 / 0.48);
            }

            .theme-dark .branch-context-card.is-active::before,
            .dark .branch-context-card.is-active::before {
                background: linear-gradient(90deg, rgb(34 211 238 / 0.9), rgb(34 211 238 / 0.16));
            }

            .branch-context-card.is-global-card {
                border-color: color-mix(in srgb, var(--accent) 24%, rgb(203 213 225));
                background: linear-gradient(145deg, color-mix(in srgb, white 88%, var(--accent) 12%), rgb(255 255 255 / 0.94));
            }

            .theme-dark .branch-context-card.is-global-card,
            .dark .branch-context-card.is-global-card {
                border-color: color-mix(in srgb, var(--accent) 34%, rgb(71 85 105));
                background: linear-gradient(145deg, color-mix(in srgb, var(--accent) 16%, rgb(15 23 42)), rgb(15 23 42 / 0.82));
            }

            .branch-context-meta {
                color: rgb(100 116 139 / 0.88);
            }

            .theme-dark .branch-context-meta,
            .dark .branch-context-meta {
                color: rgb(148 163 184 / 0.82);
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if($showBranchContextSwitcher): ?>
    <section class="branch-context-shell p-3">
        <details class="group branch-context-details">
            <summary class="branch-context-summary flex cursor-pointer flex-wrap items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="branch-context-eyebrow">Sucursales</p>
                    <div class="mt-1 flex flex-wrap items-center gap-2">
                        <p class="branch-context-title text-sm font-semibold">
                            <?php echo e($activeBranchContextTitle); ?>

                        </p>
                        <span class="branch-context-kind"><?php echo e($activeBranchContextBadge ?? 'Activa'); ?></span>
                    </div>
                    <p class="branch-context-copy text-xs">
                        <?php echo e($activeBranchContextAddress); ?>

                    </p>
                </div>
                <span class="branch-context-toggle transition group-open:translate-y-[-1px]">
                    Cambiar sede
                    <svg class="h-4 w-4 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </summary>
            <div class="branch-context-grid sm:grid-cols-2 xl:grid-cols-3">
                <?php
                    $isGlobalContextCardActive = ($showGlobalBranchOption ?? true) && ($isAdminGlobalContext || $isGlobalScope);
                ?>
                <?php if(($showGlobalBranchOption ?? true)): ?>
                    <a href="<?php echo e($globalContextUrl); ?>"
                       class="branch-context-card is-global-card block px-4 py-3 <?php echo e($isGlobalContextCardActive ? 'is-active' : ''); ?>">
                        <div class="branch-context-card-head">
                            <p class="branch-context-title text-sm font-bold leading-tight">Admin global</p>
                            <span class="branch-context-kind">Solo lectura</span>
                        </div>
                        <p class="branch-context-copy text-xs">Compara toda la red antes de bajar a una sede puntual para operar.</p>
                        <p class="branch-context-meta text-xs">
                            <?php echo e(max(0, $branchContextOptions->count() - 1)); ?> sucursal(es) vinculadas + sede principal
                        </p>
                        <?php if($isGlobalContextCardActive): ?>
                            <span class="branch-context-status">Consolidado actual</span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php $__currentLoopData = $branchContextOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branchOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isActiveBranchOption = (string) ($branchOption['slug'] ?? '') === $activeBranchContextSlug && ! $isGlobalContextCardActive;
                        $branchKindLabel = (string) ($branchOption['kind_label'] ?? 'Sucursal');
                    ?>
                    <a href="<?php echo e((string) ($branchOption['url'] ?? '#')); ?>"
                       class="branch-context-card block px-4 py-3 <?php echo e($isActiveBranchOption ? 'is-active' : ''); ?>">
                        <div class="branch-context-card-head">
                            <p class="branch-context-title text-sm font-bold leading-tight"><?php echo e((string) ($branchOption['name'] ?? 'Sucursal')); ?></p>
                            <span class="branch-context-kind"><?php echo e($branchKindLabel); ?></span>
                        </div>
                        <p class="branch-context-copy text-xs"><?php echo e((string) ($branchOption['summary'] ?? 'Cambia el contexto operativo de esta sede.')); ?></p>
                        <p class="branch-context-meta text-xs"><?php echo e((string) ($branchOption['address'] ?? '-')); ?></p>
                        <?php if($isActiveBranchOption): ?>
                            <span class="branch-context-status">Sede actual</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </details>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/branch-context-switcher.blade.php ENDPATH**/ ?>