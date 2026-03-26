<?php if (! $__env->hasRenderedOnce('bd963a90-7ca6-4139-9e9d-13ace1de585a')): $__env->markAsRenderedOnce('bd963a90-7ca6-4139-9e9d-13ace1de585a'); ?>
    <?php $__env->startPush('styles'); ?>
        <style>
            .branch-context-shell {
                border: 1px solid rgb(148 163 184 / 0.22);
                border-radius: 1.1rem;
                background: linear-gradient(162deg, rgb(255 255 255 / 0.985), rgb(248 250 252 / 0.95));
                box-shadow: 0 24px 40px -34px rgb(15 23 42 / 0.18);
            }

            .theme-dark .branch-context-shell,
            .dark .branch-context-shell {
                border-color: rgb(71 85 105 / 0.74);
                background: linear-gradient(165deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.84));
                box-shadow: 0 28px 44px -34px rgb(2 8 23 / 0.86);
            }

            .branch-context-summary {
                list-style: none;
            }

            .branch-context-summary::-webkit-details-marker {
                display: none;
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

            .branch-context-card {
                border: 1px solid color-mix(in srgb, var(--sidebar) 14%, rgb(203 213 225));
                border-radius: 0.95rem;
                background: linear-gradient(180deg, rgb(255 255 255 / 0.94), color-mix(in srgb, white 94%, var(--accent) 6%));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.88);
                transition: border-color 160ms ease, transform 160ms ease, box-shadow 180ms ease, background-color 180ms ease;
            }

            .branch-context-card:hover {
                transform: translateY(-1px);
                border-color: color-mix(in srgb, var(--accent) 24%, rgb(148 163 184));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.92), 0 18px 28px -24px color-mix(in srgb, var(--sidebar) 18%, transparent);
            }

            .branch-context-card.is-active {
                border-color: color-mix(in srgb, var(--accent) 34%, rgb(148 163 184));
                background: linear-gradient(135deg, color-mix(in srgb, var(--accent) 18%, white), color-mix(in srgb, var(--sidebar) 8%, white));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.92), 0 18px 28px -24px color-mix(in srgb, var(--accent) 18%, transparent);
            }

            .theme-dark .branch-context-card,
            .dark .branch-context-card {
                border-color: rgb(71 85 105 / 0.72);
                background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
                box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
            }

            .theme-dark .branch-context-card:hover,
            .dark .branch-context-card:hover {
                border-color: rgb(34 211 238 / 0.28);
                box-shadow: 0 20px 30px -24px rgb(2 8 23 / 0.84), inset 0 1px 0 rgb(255 255 255 / 0.05);
            }

            .theme-dark .branch-context-card.is-active,
            .dark .branch-context-card.is-active {
                border-color: rgb(34 211 238 / 0.34);
                background: linear-gradient(145deg, rgb(8 145 178 / 0.18), rgb(15 23 42 / 0.82));
                box-shadow: 0 22px 32px -26px rgb(2 8 23 / 0.9), inset 0 1px 0 rgb(255 255 255 / 0.06);
            }
        </style>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php if($showBranchContextSwitcher): ?>
    <section class="branch-context-shell p-3">
        <details class="group" open>
            <summary class="branch-context-summary flex cursor-pointer flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="branch-context-eyebrow">Sucursales</p>
                    <p class="branch-context-title text-sm font-semibold">
                        <?php echo e($activeBranchContextTitle); ?>

                    </p>
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
            <div class="mt-3 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                <?php
                    $isGlobalContextCardActive = ($showGlobalBranchOption ?? true) && ($isAdminGlobalContext || $isGlobalScope);
                ?>
                <?php if(($showGlobalBranchOption ?? true)): ?>
                    <a href="<?php echo e($globalContextUrl); ?>"
                       class="branch-context-card px-3 py-2 <?php echo e($isGlobalContextCardActive ? 'is-active' : ''); ?>">
                        <p class="branch-context-title text-sm font-bold leading-tight">Admin global</p>
                        <p class="branch-context-copy mt-0.5 text-xs">Consolidado de todas las sucursales (solo lectura)</p>
                    </a>
                <?php endif; ?>
                <?php $__currentLoopData = $branchContextOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branchOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isActiveBranchOption = (string) ($branchOption['slug'] ?? '') === $activeBranchContextSlug && ! $isGlobalContextCardActive;
                    ?>
                    <a href="<?php echo e((string) ($branchOption['url'] ?? '#')); ?>"
                       class="branch-context-card px-3 py-2 <?php echo e($isActiveBranchOption ? 'is-active' : ''); ?>">
                        <p class="branch-context-title text-sm font-bold leading-tight"><?php echo e((string) ($branchOption['name'] ?? 'Sucursal')); ?></p>
                        <p class="branch-context-copy mt-0.5 text-xs"><?php echo e((string) ($branchOption['address'] ?? '-')); ?></p>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </details>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/branch-context-switcher.blade.php ENDPATH**/ ?>