@php
    $canManagePromotions = (bool) ($canManagePromotions ?? false);
@endphp

<div x-cloak
     x-show="membershipModalOpen"
     x-transition.opacity
     class="ui-modal-backdrop"
     x-on:click.self="closeMembershipModal()"
     x-on:keydown.escape.window="closeMembershipModal()">
    <div class="ui-modal-shell max-w-4xl" x-transition.scale.origin.top>
        <form method="POST" action="{{ route('memberships.store') }}" class="flex min-h-0 flex-1 flex-col space-y-0">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="active_tab" value="membership">
            <input type="hidden" name="membership_form_mode" value="create">

            <header class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
                <div>
                    <h3 class="text-xl font-black text-slate-100">Cobrar / Renovar membresía</h3>
                    <p class="mt-1 text-sm text-slate-400">Crea la membresía y registra el ingreso en caja con la fecha real del pago.</p>
                </div>
                <x-ui.button type="button" variant="ghost" size="sm" x-on:click="closeMembershipModal()">Cerrar</x-ui.button>
            </header>

            <div class="ui-modal-scroll-body space-y-5 px-6 py-5">
                @error('cash')
                    <div class="rounded-xl border-2 border-rose-400/80 bg-rose-500/20 p-4 text-rose-100 shadow-lg">
                        <p class="text-sm font-black uppercase tracking-wide">Debe abrir caja para cobrar</p>
                        <p class="mt-1 text-sm font-semibold">{{ $message }}</p>
                        <p class="mt-2 text-xs text-rose-100/90">Abre un turno en caja y vuelve a intentar el cobro de membresía.</p>
                        <div class="mt-3">
                            <x-ui.button :href="route('cash.index')" variant="secondary" size="sm">Ir a caja</x-ui.button>
                        </div>
                    </div>
                @enderror

                <div class="rounded-xl border border-cyan-500/30 bg-cyan-500/10 p-4 text-sm text-cyan-100">
                    <p class="font-semibold">Uso recomendado</p>
                    <p class="mt-1 text-cyan-50/90">Si el cliente pagó antes pero lo registras hoy, cambia "Fecha real de pago" para que caja quede alineada con el historial real.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Plan</span>
                        <select name="plan_id" required class="ui-input" x-ref="membershipPlanInput">
                            @if ($plans->isEmpty())
                                <option value="">Sin planes activos</option>
                            @endif
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected((string) old('plan_id') === (string) $plan->id)>
                                    {{ $plan->name }} ({{ \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months) }}, {{ \App\Support\Currency::format((float) $plan->price, $appCurrencyCode) }})
                                </option>
                            @endforeach
                        </select>
                        @error('plan_id')
                            <p class="text-xs font-semibold text-rose-300">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Inicio de membresía</span>
                        <input type="date"
                               name="starts_at"
                               value="{{ old('starts_at', now()->toDateString()) }}"
                               required
                               class="ui-input">
                        @error('starts_at')
                            <p class="text-xs font-semibold text-rose-300">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Estado base</span>
                        <select name="status" class="ui-input">
                            <option value="active" @selected(old('status', 'active') === 'active')>Activo</option>
                            <option value="expired" @selected(old('status') === 'expired')>Vencido</option>
                            <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelado</option>
                        </select>
                        @error('status')
                            <p class="text-xs font-semibold text-rose-300">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                        <span>Método de pago</span>
                        <select name="payment_method" required class="ui-input">
                            <option value="">Seleccione</option>
                            <option value="cash" @selected(old('payment_method') === 'cash')>Efectivo</option>
                            <option value="card" @selected(old('payment_method') === 'card')>Tarjeta</option>
                            <option value="transfer" @selected(old('payment_method') === 'transfer')>Transferencia</option>
                        </select>
                        @error('payment_method')
                            <p class="text-xs font-semibold text-rose-300">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                        <span>Fecha real de pago</span>
                        <input type="date"
                               name="payment_received_at"
                               value="{{ old('payment_received_at', now()->toDateString()) }}"
                               max="{{ now()->toDateString() }}"
                               class="ui-input">
                        <p class="text-xs text-slate-400">Se guarda en caja como `occurred_at`. Déjalo en hoy si cobras y registras el mismo día.</p>
                        @error('payment_received_at')
                            <p class="text-xs font-semibold text-rose-300">{{ $message }}</p>
                        @enderror
                    </label>

                    @if ($canManagePromotions)
                        <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2 xl:col-span-4">
                            <span>Promoción (opcional)</span>
                            <select name="promotion_id" class="ui-input" x-ref="membershipPromotionInput">
                                <option value="">Sin promoción</option>
                                @foreach (($promotions ?? collect()) as $promotion)
                                    @php
                                        $promoTypeLabel = match ($promotion->type) {
                                            'percentage' => '-'.$promotion->value.'%',
                                            'fixed' => '-'.\App\Support\Currency::format((float) $promotion->value, $appCurrencyCode),
                                            'final_price' => 'Precio final '.\App\Support\Currency::format((float) $promotion->value, $appCurrencyCode),
                                            'bonus_days' => '+'.(int) $promotion->value.' días',
                                            'two_for_one' => '2x1',
                                            'bring_friend' => 'Trae a un amigo',
                                            default => (string) $promotion->type,
                                        };
                                        $planScopeLabel = $promotion->plan_id
                                            ? ' - Plan '.($promotion->plan?->name ?? '#'.$promotion->plan_id)
                                            : ' - Todos los planes';
                                    @endphp
                                    <option value="{{ $promotion->id }}" @selected((string) old('promotion_id') === (string) $promotion->id)>
                                        {{ $promotion->name }} ({{ $promoTypeLabel }}{{ $planScopeLabel }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-400">La promoción válida precio final y días extra automáticamente.</p>
                            @error('promotion_id')
                                <p class="text-xs font-semibold text-rose-300">{{ $message }}</p>
                            @enderror
                        </label>
                    @else
                        <div class="rounded-lg border border-amber-500/40 bg-amber-500/10 p-2 text-xs text-amber-200 md:col-span-2 xl:col-span-4">
                            Promociones no disponibles en tu plan actual.
                            @error('promotion_id')
                                <p class="mt-1 font-semibold text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                @if ($plans->isEmpty())
                    <div class="rounded-xl border border-amber-500/40 bg-amber-500/10 p-3 text-xs text-amber-200">
                        No hay planes activos disponibles. Crea un plan antes de cobrar membresías.
                    </div>
                @endif
            </div>

            <footer class="ui-modal-sticky-footer flex justify-end gap-3 px-6 py-4">
                <x-ui.button type="button" variant="ghost" x-on:click="closeMembershipModal()">Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="success" :disabled="$plans->isEmpty()">Cobrar y guardar</x-ui.button>
            </footer>
        </form>
    </div>
</div>
