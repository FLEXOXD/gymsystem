<div id="high-amount-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="highAmountTitle">
    <div class="ui-modal-panel max-w-md">
        <h3 id="highAmountTitle" class="ui-heading text-lg">Confirmar monto alto</h3>
        <p class="mt-2 text-sm ui-muted">Estas registrando un movimiento alto: <strong id="high-amount-value">{{ $currencySymbol }}0.00</strong></p>
        <p class="mt-1 text-xs ui-muted">Verifica tipo y método antes de continuar.</p>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" class="ui-button ui-button-ghost" data-close-high-modal>Cancelar</button>
            <button type="button" class="ui-button ui-button-primary" id="confirm-high-amount">Confirmar y guardar</button>
        </div>
    </div>
</div>

<div id="difference-approval-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="differenceApprovalTitle">
    <div class="ui-modal-panel max-w-md">
        <h3 id="differenceApprovalTitle" class="ui-heading text-lg">Aprobación supervisor</h3>
        <p class="mt-2 text-sm ui-muted">El cierre tiene diferencia. Solo Admin puede aprobarlo.</p>
        <label class="mt-3 block space-y-1 text-sm font-semibold ui-muted">
            <span>Password/PIN admin</span>
            <input id="difference-approval-password" type="password" class="ui-input" autocomplete="new-password">
        </label>
        <p id="difference-approval-error" class="mt-2 hidden text-xs font-semibold text-rose-600 dark:text-rose-300">Ingresa password/PIN para continuar.</p>
        <div class="mt-4 flex justify-end gap-2">
            <button type="button" class="ui-button ui-button-ghost" data-close-difference-modal>Cancelar</button>
            <button type="button" class="ui-button ui-button-danger" id="confirm-close-with-diff">Aprobar cierre</button>
        </div>
    </div>
</div>

<div id="void-movement-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="voidMovementTitle">
    <div class="ui-modal-panel max-w-md">
        <h3 id="voidMovementTitle" class="ui-heading text-lg">Anular movimiento</h3>
        <p class="mt-2 text-sm ui-muted">Movimiento: <strong id="void-movement-label">-</strong></p>

        <form id="void-movement-form" method="POST" action="{{ $voidRouteTemplate }}" class="mt-3 space-y-3">
            @csrf
            @method('PATCH')
            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>Motivo de anulación (obligatorio)</span>
                <textarea name="void_reason" id="void-reason" rows="3" required class="ui-input" placeholder="Ej: ingreso duplicado o error de caja."></textarea>
            </label>

            @if (! $routeHasVoidMovement)
                <p class="ui-alert ui-alert-danger text-xs">Falta route `cash.movements.void` en backend.</p>
            @endif

            <div class="flex justify-end gap-2">
                <button type="button" class="ui-button ui-button-ghost" data-close-void-modal>Cancelar</button>
                <button type="submit" class="ui-button ui-button-danger" @disabled(! $routeHasVoidMovement)>Anular movimiento</button>
            </div>
        </form>
    </div>
</div>

<div id="monthly-movements-modal" class="ui-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="monthlyMovementsTitle">
    <div class="ui-modal-panel max-w-7xl p-0">
        <div class="ui-modal-shell">
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-5 py-4">
                <div>
                    <h3 id="monthlyMovementsTitle" class="ui-heading text-lg text-slate-100">
                        {{ $isCashierScoped ? 'Tus movimientos del mes' : 'Movimientos del mes' }}
                    </h3>
                    <p class="mt-1 text-sm text-slate-300">
                        Revisión completa del mes actual sin salir de la caja.
                    </p>
                </div>
                <button type="button" class="ui-button ui-button-ghost" data-close-monthly-modal>Cerrar</button>
            </div>

            <div class="ui-modal-scroll-body px-5 py-4">
                @include('cash.partials.monthly-movements-content', [
                    'monthlyMovements' => $monthlyMovements,
                    'monthlySummary' => $monthlySummary,
                    'monthStart' => $monthStart,
                    'monthEnd' => $monthEnd,
                    'isCashierScoped' => $isCashierScoped,
                ])
            </div>

            <div class="ui-modal-sticky-footer flex justify-end gap-2 px-5 pt-4">
                <x-ui.button type="button" variant="ghost" data-close-monthly-modal>Cerrar</x-ui.button>
                <x-ui.button :href="route('clients.index')" variant="primary">Cobrar membresía</x-ui.button>
            </div>
        </div>
    </div>
</div>
