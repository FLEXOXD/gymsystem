<div x-cloak
     x-show="membershipModalOpen"
     x-transition.opacity
     class="ui-modal-backdrop"
     x-on:click.self="closeMembershipModal()"
     x-on:keydown.escape.window="closeMembershipModal()">
    <div class="w-full max-w-4xl rounded-2xl border border-slate-700 bg-slate-950 shadow-2xl" x-transition.scale.origin.top>
        <form method="POST" action="{{ route('memberships.store') }}" class="space-y-0">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <header class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
                <div>
                    <h3 class="text-xl font-black text-slate-100">Cobrar / Renovar membresia</h3>
                    <p class="mt-1 text-sm text-slate-400">Crea membresia y registra cobro en caja automaticamente.</p>
                </div>
                <x-ui.button type="button" variant="ghost" size="sm" x-on:click="closeMembershipModal()">Cerrar</x-ui.button>
            </header>

            <div class="space-y-5 px-6 py-5">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Plan</span>
                        <select name="plan_id" required class="ui-input" x-ref="membershipPlanInput">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected((string) old('plan_id') === (string) $plan->id)>
                                    {{ $plan->name }} ({{ $plan->duration_days }} dias, {{ \App\Support\Currency::format((float) $plan->price, $appCurrencyCode) }})
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Inicio</span>
                        <input type="date"
                               name="starts_at"
                               value="{{ old('starts_at', now()->toDateString()) }}"
                               required
                               class="ui-input">
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Estado</span>
                        <select name="status" class="ui-input">
                            <option value="active" @selected(old('status', 'active') === 'active')>Activo</option>
                            <option value="expired" @selected(old('status') === 'expired')>Vencido</option>
                            <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelado</option>
                        </select>
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Metodo de pago</span>
                        <select name="payment_method" required class="ui-input">
                            <option value="">Seleccione</option>
                            <option value="cash" @selected(old('payment_method') === 'cash')>Efectivo</option>
                            <option value="card" @selected(old('payment_method') === 'card')>Tarjeta</option>
                            <option value="transfer" @selected(old('payment_method') === 'transfer')>Transferencia</option>
                        </select>
                    </label>
                </div>

                @if ($plans->isEmpty())
                    <div class="rounded-xl border border-amber-500/40 bg-amber-500/10 p-3 text-xs text-amber-200">
                        No hay planes activos disponibles. Crea un plan antes de cobrar membresias.
                    </div>
                @endif
            </div>

            <footer class="flex justify-end gap-3 border-t border-slate-800 px-6 py-4">
                <x-ui.button type="button" variant="ghost" x-on:click="closeMembershipModal()">Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="success" :disabled="$plans->isEmpty()">Cobrar y guardar</x-ui.button>
            </footer>
        </form>
    </div>
</div>
