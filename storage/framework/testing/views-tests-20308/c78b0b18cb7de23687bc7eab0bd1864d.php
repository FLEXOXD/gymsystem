<?php if($showBranchContextSwitcher): ?>
                <section class="rounded-2xl border border-slate-200/80 bg-white/90 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                    <details class="group" open>
                        <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.16em] text-slate-500 dark:text-slate-300">Sucursales</p>
                                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                    <?php echo e($activeBranchContextTitle); ?>

                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-300">
                                    <?php echo e($activeBranchContextAddress); ?>

                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 transition group-open:text-cyan-700 dark:text-slate-300 dark:group-open:text-cyan-200">
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
                                   class="rounded-xl border px-3 py-2 transition <?php echo e($isGlobalContextCardActive ? 'border-cyan-400 bg-cyan-50 text-cyan-900 shadow-sm dark:border-cyan-500/60 dark:bg-cyan-900/25 dark:text-cyan-100' : 'border-slate-200 bg-white text-slate-800 hover:border-cyan-300 hover:bg-cyan-50/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-cyan-500/50 dark:hover:bg-cyan-900/20'); ?>">
                                    <p class="text-sm font-bold leading-tight">Admin global</p>
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300">Consolidado de todas las sucursales (solo lectura)</p>
                                </a>
                            <?php endif; ?>
                            <?php $__currentLoopData = $branchContextOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branchOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isActiveBranchOption = (string) ($branchOption['slug'] ?? '') === $activeBranchContextSlug && ! $isGlobalContextCardActive;
                                ?>
                                <a href="<?php echo e((string) ($branchOption['url'] ?? '#')); ?>"
                                   class="rounded-xl border px-3 py-2 transition <?php echo e($isActiveBranchOption ? 'border-cyan-400 bg-cyan-50 text-cyan-900 shadow-sm dark:border-cyan-500/60 dark:bg-cyan-900/25 dark:text-cyan-100' : 'border-slate-200 bg-white text-slate-800 hover:border-cyan-300 hover:bg-cyan-50/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-cyan-500/50 dark:hover:bg-cyan-900/20'); ?>">
                                    <p class="text-sm font-bold leading-tight"><?php echo e((string) ($branchOption['name'] ?? 'Sucursal')); ?></p>
                                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-300"><?php echo e((string) ($branchOption['address'] ?? '-')); ?></p>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </details>
                </section>
            <?php endif; ?>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/branch-context-switcher.blade.php ENDPATH**/ ?>