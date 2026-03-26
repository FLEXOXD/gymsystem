@if ($isGlobalScope)
    <section class="rounded-2xl border border-cyan-200 bg-cyan-50/90 px-4 py-3 text-sm text-cyan-900 shadow-sm dark:border-cyan-500/40 dark:bg-cyan-900/20 dark:text-cyan-100">
        <p class="font-bold uppercase tracking-wide">Modo Admin global</p>
        <p class="mt-1 text-xs">
            @if (($showBranchContextSwitcher ?? false))
                Vista consolidada y de solo lectura. Usa "Cambiar sede" para volver a la sede principal o entrar a una sucursal puntual.
            @else
                Vista consolidada y de solo lectura para recepcion, clientes, planes y caja.
                Selecciona una sede especifica para crear o editar datos.
            @endif
        </p>
    </section>
@endif
