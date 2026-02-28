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
