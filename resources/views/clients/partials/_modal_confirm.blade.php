<div x-cloak
     x-show="confirmOpen"
     x-transition.opacity
     class="ui-modal-backdrop"
     x-on:click.self="closeConfirm()"
     x-on:keydown.escape.window="closeConfirm()">
    <div class="ui-modal-panel max-w-md">
        <h3 class="ui-heading text-lg">Confirmar accion</h3>
        <p class="ui-text mt-2 text-sm" x-text="confirmMessage">Desea continuar?</p>

        <div class="mt-4 flex justify-end gap-2">
            <x-ui.button type="button" variant="muted" x-on:click="closeConfirm()">Cancelar</x-ui.button>
            <x-ui.button type="button" variant="danger" x-on:click="confirmDeactivate()">Desactivar</x-ui.button>
        </div>
    </div>
</div>
