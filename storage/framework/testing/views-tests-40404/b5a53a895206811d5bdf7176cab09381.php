<?php if($isGlobalScope): ?>
    <section class="rounded-2xl border border-cyan-200 bg-cyan-50/90 px-4 py-3 text-sm text-cyan-900 shadow-sm dark:border-cyan-500/40 dark:bg-cyan-900/20 dark:text-cyan-100">
        <p class="font-bold uppercase tracking-wide">Modo Admin global</p>
        <p class="mt-1 text-xs">
            <?php if(($showBranchContextSwitcher ?? false)): ?>
                Vista consolidada y de solo lectura. Usa "Cambiar sede" para volver a la sede principal o entrar a una sucursal puntual.
            <?php else: ?>
                Vista consolidada y de solo lectura para recepcion, clientes, planes y caja.
                Selecciona una sede especifica para crear o editar datos.
            <?php endif; ?>
        </p>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/global-scope-banner.blade.php ENDPATH**/ ?>