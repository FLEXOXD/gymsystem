<div x-cloak
     x-show="rfidModalOpen"
     x-transition.opacity
     class="ui-modal-backdrop"
     x-on:click.self="closeRfidModal()"
     x-on:keydown.escape.window="closeRfidModal()">
    <div class="ui-modal-panel">
        <h3 class="ui-heading text-lg">Asignar RFID</h3>
        <p class="ui-muted mt-1 text-sm">Pasa la pulsera por el lector o pega el UID manualmente.</p>

        <form method="POST" action="{{ route('client-credentials.store-rfid', $client->id) }}" class="mt-4 space-y-4">
            @csrf

            <label class="space-y-1 text-sm font-semibold ui-muted">
                <span>UID RFID</span>
                <input type="text"
                       name="value"
                       x-ref="rfidValueInput"
                       value="{{ old('value') }}"
                       required
                       class="ui-input font-mono">
            </label>

            <div class="flex justify-end gap-2">
                <x-ui.button type="button" variant="muted" x-on:click="closeRfidModal()">Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="success">Guardar RFID</x-ui.button>
            </div>
        </form>
    </div>
</div>
