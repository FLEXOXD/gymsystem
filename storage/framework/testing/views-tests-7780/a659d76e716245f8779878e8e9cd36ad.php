<nav class="theme-mobile-nav fixed inset-x-0 bottom-0 z-30 border-t p-2 backdrop-blur lg:hidden">
    <div class="mx-auto flex max-w-full gap-2 overflow-x-auto px-1 pb-1">
        <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $activePatterns = explode('|', $item['active']);
                $isActive = collect($activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                $isHighlight = (bool) ($item['highlight'] ?? false);
                $mobileClass = $isActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link';
                if (! $isActive && $isHighlight) {
                    $mobileClass .= ' theme-nav-mobile-highlight';
                }
            ?>
            <a href="<?php echo e(route($item['route'], $item['params'] ?? [])); ?>"
               class="min-w-[84px] shrink-0 rounded-lg px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wide <?php echo e($mobileClass); ?>">
                <?php echo e($item['label']); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</nav>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/mobile-nav.blade.php ENDPATH**/ ?>