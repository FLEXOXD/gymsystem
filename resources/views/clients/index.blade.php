@extends('layouts.panel')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    @php
        $filters = [
            'all' => 'Todos',
            'active' => 'Activos',
            'expiring' => 'Por vencer',
            'expired' => 'Vencid@s',
            'attended_today' => 'Asistieron hoy',
        ];
        $baseFilterQuery = request()->query();
        unset($baseFilterQuery['page']);

        $planCatalog = $plans
            ->map(fn ($plan) => [
                'id' => (int) $plan->id,
                'name' => (string) $plan->name,
                'duration_days' => (int) $plan->duration_days,
                'duration_unit' => \App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days')),
                'duration_months' => $plan->duration_months !== null ? (int) $plan->duration_months : null,
                'price' => (float) $plan->price,
            ])
            ->values();

        $promotionCatalog = ($promotions ?? collect())
            ->map(fn ($promotion) => [
                'id' => (int) $promotion->id,
                'plan_id' => $promotion->plan_id !== null ? (int) $promotion->plan_id : null,
                'name' => (string) $promotion->name,
                'type' => (string) $promotion->type,
                'value' => (float) ($promotion->value ?? 0),
                'starts_at' => optional($promotion->starts_at)?->toDateString(),
                'ends_at' => optional($promotion->ends_at)?->toDateString(),
                'max_uses' => $promotion->max_uses !== null ? (int) $promotion->max_uses : null,
                'times_used' => (int) ($promotion->times_used ?? 0),
            ])
            ->values();

        $formDefaults = [
            'first_name' => (string) old('first_name', ''),
            'last_name' => (string) old('last_name', ''),
            'document_number' => (string) old('document_number', ''),
            'phone' => (string) old('phone', ''),
            'gender' => (string) old('gender', 'neutral'),
            'start_membership' => old('start_membership') ? true : false,
            'plan_id' => old('plan_id') !== null ? (string) old('plan_id') : '',
            'membership_starts_at' => (string) old('membership_starts_at', now()->toDateString()),
            'membership_price' => old('membership_price') !== null ? (string) old('membership_price') : '',
            'promotion_id' => old('promotion_id') !== null ? (string) old('promotion_id') : '',
            'payment_method' => (string) old('payment_method', 'cash'),
            'amount_paid' => old('amount_paid') !== null ? (string) old('amount_paid') : '',
        ];
    @endphp

    <div x-data="clientsIndexPage({
            openCreateModal: @js($openCreateModal),
            plans: @js($planCatalog),
            promotions: @js($promotionCatalog),
            defaults: @js($formDefaults),
            documentCheckUrl: @js(route('clients.check-document')),
        })"
         x-init="init()"
         class="space-y-4">

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-300/70 bg-slate-100 p-4 shadow-lg dark:border-white/10 dark:bg-slate-900/40">
                <p class="ui-muted text-xs font-semibold uppercase tracking-widest">Total clientes</p>
                <p class="mt-2 text-3xl font-black ui-heading">{{ $stats['total'] }}</p>
                <p class="ui-muted mt-1 text-xs">Base actual del listado</p>
            </article>
            <article class="rounded-2xl border border-emerald-400/35 bg-emerald-500/10 p-4 shadow-lg dark:border-emerald-300/30 dark:bg-emerald-400/15">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-200">Activos</p>
                <p class="mt-2 text-3xl font-black text-emerald-900 dark:text-emerald-100">{{ $stats['active'] }}</p>
                <p class="mt-1 text-xs text-emerald-800 dark:text-emerald-200">Membresía vigente</p>
            </article>
            <article class="rounded-2xl border border-amber-400/35 bg-amber-500/10 p-4 shadow-lg dark:border-amber-300/30 dark:bg-amber-400/15">
                <p class="text-xs font-semibold uppercase tracking-widest text-amber-800 dark:text-amber-200">Por vencer</p>
                <p class="mt-2 text-3xl font-black text-amber-900 dark:text-amber-100">{{ $stats['expiring'] }}</p>
                <p class="mt-1 text-xs text-amber-800 dark:text-amber-200">En los próximos 7 días</p>
            </article>
            <article class="rounded-2xl border border-rose-400/35 bg-rose-500/10 p-4 shadow-lg dark:border-rose-300/30 dark:bg-rose-400/15">
                <p class="text-xs font-semibold uppercase tracking-widest text-rose-800 dark:text-rose-200">Vencid@s</p>
                <p class="mt-2 text-3xl font-black text-rose-900 dark:text-rose-100">{{ $stats['expired'] }}</p>
                <p class="mt-1 text-xs text-rose-800 dark:text-rose-200">Requieren renovación</p>
            </article>
        </section>

        <x-ui.card title="Clientes del gimnasio" subtitle="Vista operacional para recepción, renovaciones y retención.">
            <div class="space-y-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('clients.index') }}" class="grid gap-3 lg:grid-cols-[1fr_auto_auto] lg:w-full lg:max-w-3xl">
                        <input type="hidden" name="filter" value="{{ $quickFilter }}">
                        <input type="text"
                               name="q"
                               value="{{ $search }}"
                               placeholder="Buscar por nombre, apellido o documento..."
                               class="ui-input">
                        <x-ui.button type="submit" variant="secondary">Buscar</x-ui.button>
                        <x-ui.button :href="route('clients.index')" variant="ghost">Limpiar</x-ui.button>
                    </form>

                    <x-ui.button type="button" variant="primary" x-on:click="openCreateClient()" class="whitespace-nowrap">
                        + Nuevo cliente
                    </x-ui.button>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach ($filters as $filterKey => $filterLabel)
                        @php
                            $isActiveFilter = $quickFilter === $filterKey;
                            $chipQuery = array_merge($baseFilterQuery, ['filter' => $filterKey]);
                            $chipClass = $isActiveFilter
                                ? 'ui-button ui-button-primary px-3 py-1.5 text-xs font-bold uppercase tracking-wide'
                                : 'ui-button ui-button-ghost px-3 py-1.5 text-xs font-bold uppercase tracking-wide';
                        @endphp
                        <a href="{{ route('clients.index', $chipQuery) }}" class="{{ $chipClass }}">{{ $filterLabel }}</a>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-300/70 dark:border-white/10">
                <div class="max-h-[560px] overflow-auto">
                    <table class="ui-table min-w-[1200px]">
                        <thead>
                        <tr class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50/95 text-left text-xs uppercase tracking-wider text-slate-500 backdrop-blur dark:border-slate-700 dark:bg-slate-800/95 dark:text-slate-300">
                            <th class="px-3 py-3">ID</th>
                            <th class="px-3 py-3">Cliente</th>
                            <th class="px-3 py-3">Plan</th>
                            <th class="px-3 py-3">Vence</th>
                            <th class="px-3 py-3">Días restantes</th>
                            <th class="px-3 py-3">Pago</th>
                            <th class="px-3 py-3">Ultima asistencia</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($clients as $client)
                            @php
                                $daysToneClass = match ($client['days_badge']['tone']) {
                                    'success' => 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-500/20 dark:text-emerald-200 dark:border-emerald-400/30',
                                    'warning' => 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-500/20 dark:text-amber-200 dark:border-amber-400/30',
                                    'danger' => 'bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-500/20 dark:text-rose-200 dark:border-rose-400/30',
                                    'danger-strong' => 'bg-rose-600/80 text-rose-50 border border-rose-300/60',
                                    default => 'bg-slate-100 text-slate-700 border border-slate-300 dark:bg-slate-500/20 dark:text-slate-200 dark:border-slate-400/30',
                                };
                            @endphp
                            <tr class="border-b border-slate-200 text-sm text-slate-800 odd:bg-white even:bg-slate-100 hover:bg-sky-100/70 dark:border-slate-800 dark:text-slate-200 dark:odd:bg-slate-900 dark:even:bg-slate-950/50 dark:hover:bg-cyan-500/10">
                                <td class="px-3 py-3 font-bold text-slate-800 dark:text-slate-200">#{{ $client['id'] }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($client['photo_url'])
                                            <img src="{{ $client['photo_url'] }}"
                                                 alt="{{ $client['full_name'] }}"
                                                 class="h-10 w-10 rounded-full border border-white/20 object-cover">
                                        @else
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-slate-700 text-xs font-black uppercase tracking-wider text-white">
                                                {{ $client['initials'] }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ $client['full_name'] }}</p>
                                            <p class="truncate text-xs font-medium text-slate-600 dark:text-slate-400">{{ $client['document_number'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-100">{{ $client['plan_name'] }}</td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $client['membership_ends_at_human'] }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $daysToneClass }}">
                                        {{ $client['days_badge']['label'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <x-ui.badge :variant="$client['payment_badge']['variant']">
                                        {{ $client['payment_badge']['label'] }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">{{ $client['last_checkin_label'] }}</td>
                                <td class="px-3 py-3">
                                    <x-ui.badge :variant="$client['status_badge']['variant']">
                                        {{ $client['status_badge']['label'] }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('clients.show', $client['id']) }}"
                                       class="ui-button ui-button-secondary inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-8 text-center text-sm text-slate-600 dark:text-slate-300">
                                    No hay clientes para los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <p class="text-sm ui-muted">
                    Mostrando {{ $clients->firstItem() ?? 0 }} - {{ $clients->lastItem() ?? 0 }} de {{ $clients->total() }} clientes
                </p>
                <div>
                    {{ $clients->onEachSide(1)->links() }}
                </div>
            </div>
        </x-ui.card>

        <div x-cloak
             x-show="modalOpen"
             x-transition.opacity
             class="ui-modal-backdrop"
             x-on:click.self="closeCreateClient()"
             x-on:keydown.escape.window="closeCreateClient()">
            <div class="w-full max-w-3xl rounded-2xl border border-slate-700 bg-slate-950 shadow-2xl"
                 x-transition.scale.origin.top
                 role="dialog"
                 aria-modal="true">
                <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" class="space-y-0" x-on:submit="normalizeNameField('first_name'); normalizeNameField('last_name'); submitting = true">
                    @csrf
                    <input type="hidden" name="_open_create_modal" value="1">

                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-100">Crear cliente</h3>
                            <p class="mt-1 text-sm text-slate-400">Alta rapida de cliente con membresía opcional.</p>
                        </div>
                        <button type="button"
                                class="ui-button ui-button-ghost px-2 py-1 text-sm"
                                x-on:click="closeCreateClient()"
                                aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="space-y-5 px-5 py-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Nombre</span>
                                <input type="text" name="first_name" x-model="form.first_name" x-on:blur="normalizeNameField('first_name')" required class="ui-input" x-ref="firstNameInput">
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Apellido</span>
                                <input type="text" name="last_name" x-model="form.last_name" x-on:blur="normalizeNameField('last_name')" required class="ui-input">
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                <span>Documento</span>
                                <input type="text"
                                       name="document_number"
                                       x-model.trim="form.document_number"
                                       x-on:input.debounce.350ms="checkDocument()"
                                       required
                                       class="ui-input"
                                       placeholder="Cedula, DNI o pasaporte">
                                <div x-cloak x-show="documentState === 'checking'" class="text-xs text-slate-400">Validando documento...</div>
                                <div x-cloak x-show="documentState === 'exists'" class="rounded-lg border border-rose-500/40 bg-rose-500/10 p-2 text-xs text-rose-200">
                                    <p>Este documento ya existe en este gimnasio.</p>
                                    <a class="mt-2 inline-flex items-center gap-1 font-semibold text-cyan-300 underline"
                                       x-bind:href="documentMatchUrl"
                                       x-show="documentMatchUrl">
                                        Abrir cliente
                                    </a>
                                </div>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Teléfono</span>
                                <input type="text" name="phone" x-model="form.phone" class="ui-input" placeholder="Opcional">
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Genero</span>
                                <select name="gender" x-model="form.gender" class="ui-input">
                                    <option value="male">Hombre</option>
                                    <option value="female">Mujer</option>
                                    <option value="neutral">Neutral</option>
                                </select>
                            </label>

                            <div class="space-y-2 md:col-span-2">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Foto del cliente</span>
                                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="ui-input"
                                           x-on:change="onPhotoSelected($event)">
                                </label>

                                <div class="flex items-center gap-3 rounded-xl border border-slate-700 bg-slate-900/60 p-3">
                                    <template x-if="photoPreview">
                                        <img x-bind:src="photoPreview" alt="Preview" class="h-16 w-16 rounded-full border border-slate-600 object-cover">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full border border-slate-600 bg-slate-800 text-lg font-black uppercase text-slate-200"
                                             x-text="avatarInitials()"></div>
                                    </template>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-200">Vista previa</p>
                                        <p class="text-xs text-slate-400">Si no subes imagen se mostraran iniciales.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-200">
                                    <input type="checkbox" name="start_membership" value="1" x-model="form.start_membership" x-on:change="onMembershipToggle()" class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-cyan-500 focus:ring-cyan-400/40">
                                    Iniciar membresía ahora
                                </label>

                                <span class="inline-flex rounded-full border border-slate-600 bg-slate-800 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-200"
                                      x-text="membershipBadgeLabel"></span>
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-2" x-cloak x-show="form.start_membership">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Plan</span>
                                    <select name="plan_id" x-model="form.plan_id" x-on:change="onPlanChange()" x-bind:disabled="!form.start_membership" class="ui-input">
                                        <option value="">Selecciona un plan</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ \App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months) }})</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Fecha inicio</span>
                                    <input type="date" name="membership_starts_at" x-model="form.membership_starts_at" x-on:input="recalculateMembershipEnd()" x-bind:disabled="!form.start_membership" class="ui-input">
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Precio</span>
                                    <input type="number" name="membership_price" x-model="form.membership_price" min="0" step="0.01" x-bind:disabled="!form.start_membership" class="ui-input">
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Promoción (opcional)</span>
                                    <select name="promotion_id" x-model="form.promotion_id" x-on:change="onPromotionChange()" x-bind:disabled="!form.start_membership" class="ui-input">
                                        <option value="">Sin promoción</option>
                                        <template x-for="promo in availablePromotions()" :key="promo.id">
                                            <option :value="String(promo.id)" x-text="promotionOptionLabel(promo)"></option>
                                        </template>
                                    </select>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Método de pago</span>
                                    <select name="payment_method" x-model="form.payment_method" x-bind:disabled="!form.start_membership" class="ui-input">
                                        <option value="cash">Efectivo</option>
                                        <option value="transfer">Transferencia</option>
                                        <option value="card">Tarjeta</option>
                                    </select>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Monto pagado</span>
                                    <input type="number" name="amount_paid" x-model="form.amount_paid" min="0" step="0.01" x-bind:disabled="!form.start_membership" class="ui-input">
                                </label>

                                <div class="space-y-2 rounded-lg border border-slate-700 bg-slate-900/70 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Fecha fin estimada</p>
                                    <p class="text-sm font-bold text-slate-100" x-text="membershipEndLabel"></p>
                                    <p class="text-xs text-slate-400" x-show="promotionSummaryLabel" x-text="promotionSummaryLabel"></p>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
                                          x-bind:class="paymentBadgeClass"
                                          x-text="paymentStatusLabel"></span>
                                </div>
                            </div>

                            <p x-cloak x-show="form.start_membership && plans.length === 0" class="mt-3 rounded-lg border border-amber-500/40 bg-amber-500/10 p-2 text-xs text-amber-200">
                                No hay planes activos. Crea un plan antes de iniciar membresías desde este modal.
                            </p>
                        </div>
                    </div>

                    <footer class="flex items-center justify-end gap-3 border-t border-slate-800 px-5 py-4">
                        <x-ui.button type="button" variant="ghost" x-on:click="closeCreateClient()">Cancelar</x-ui.button>
                        <x-ui.button type="submit"
                                     variant="success"
                                     x-bind:disabled="submitting || documentState === 'exists' || (form.start_membership && plans.length === 0)">
                            Guardar
                        </x-ui.button>
                    </footer>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        window.clientsIndexPage = function clientsIndexPage(config) {
            return {
                modalOpen: Boolean(config.openCreateModal),
                submitting: false,
                documentState: 'idle',
                documentMatchUrl: null,
                plans: Array.isArray(config.plans) ? config.plans : [],
                promotions: Array.isArray(config.promotions) ? config.promotions : [],
                documentCheckUrl: config.documentCheckUrl,
                form: {
                    first_name: config.defaults?.first_name ?? '',
                    last_name: config.defaults?.last_name ?? '',
                    document_number: config.defaults?.document_number ?? '',
                    phone: config.defaults?.phone ?? '',
                    gender: config.defaults?.gender ?? 'neutral',
                    start_membership: Boolean(config.defaults?.start_membership),
                    plan_id: config.defaults?.plan_id ?? '',
                    membership_starts_at: config.defaults?.membership_starts_at ?? new Date().toISOString().slice(0, 10),
                    membership_price: config.defaults?.membership_price ?? '',
                    promotion_id: config.defaults?.promotion_id ?? '',
                    payment_method: config.defaults?.payment_method ?? 'cash',
                    amount_paid: config.defaults?.amount_paid ?? '',
                },
                photoPreview: null,
                membershipEndLabel: 'N/A',
                promotionSummaryLabel: '',

                init() {
                    this.recalculateMembershipEnd();
                    if (this.form.document_number !== '') {
                        this.checkDocument();
                    }

                    if (this.form.start_membership) {
                        this.onPlanChange();
                    }

                    if (this.modalOpen) {
                        this.$nextTick(() => this.$refs.firstNameInput?.focus());
                    }
                },

                openCreateClient() {
                    this.modalOpen = true;
                    this.$nextTick(() => this.$refs.firstNameInput?.focus());
                },

                closeCreateClient() {
                    if (this.submitting) {
                        return;
                    }
                    this.modalOpen = false;
                },

                avatarInitials() {
                    const first = (this.form.first_name || '').trim().charAt(0);
                    const last = (this.form.last_name || '').trim().charAt(0);
                    const initials = `${first}${last}`.trim().toUpperCase();
                    return initials !== '' ? initials : '--';
                },

                onPhotoSelected(event) {
                    const [file] = event.target.files || [];
                    if (!file) {
                        this.photoPreview = null;
                        return;
                    }

                    this.photoPreview = URL.createObjectURL(file);
                },

                formatPersonName(value) {
                    const raw = String(value ?? '').trim().replace(/\s+/g, ' ');
                    if (raw === '') {
                        return '';
                    }

                    return raw
                        .split(/(\s+|-|')/u)
                        .map((segment) => {
                            if (segment === '' || /^(\s+|-|')$/u.test(segment)) {
                                return segment;
                            }

                            const chars = Array.from(segment);
                            const [first, ...rest] = chars;
                            return first.toLocaleUpperCase('es-ES') + rest.join('').toLocaleLowerCase('es-ES');
                        })
                        .join('');
                },

                normalizeNameField(field) {
                    if (field !== 'first_name' && field !== 'last_name') {
                        return;
                    }

                    this.form[field] = this.formatPersonName(this.form[field]);
                },

                normalizePlanDurationUnit(unit) {
                    return String(unit || '').toLowerCase() === 'months' ? 'months' : 'days';
                },

                addMonthsNoOverflow(baseDate, monthsToAdd) {
                    const months = Math.max(1, Number(monthsToAdd || 1));
                    const baseDay = baseDate.getDate();
                    const baseMonthIndex = baseDate.getMonth() + months;
                    const targetYear = baseDate.getFullYear() + Math.floor(baseMonthIndex / 12);
                    const targetMonth = ((baseMonthIndex % 12) + 12) % 12;
                    const targetLastDay = new Date(targetYear, targetMonth + 1, 0).getDate();
                    const targetDay = Math.min(baseDay, targetLastDay);

                    return new Date(targetYear, targetMonth, targetDay);
                },

                computeMembershipEndDate(startDate, plan, bonusDays) {
                    const unit = this.normalizePlanDurationUnit(plan.duration_unit);
                    const safeBonusDays = Math.max(0, Math.round(Number(bonusDays || 0)));
                    let endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());

                    if (unit === 'months') {
                        const months = Math.max(1, Math.round(Number(plan.duration_months || 1)));
                        endDate = this.addMonthsNoOverflow(startDate, months);
                    } else {
                        const days = Math.max(1, Math.round(Number(plan.duration_days || 1)));
                        endDate.setDate(endDate.getDate() + days - 1);
                    }

                    if (safeBonusDays > 0) {
                        endDate.setDate(endDate.getDate() + safeBonusDays);
                    }

                    return endDate;
                },

                availablePromotions() {
                    const planId = String(this.form.plan_id || '');
                    const startDate = this.form.membership_starts_at || new Date().toISOString().slice(0, 10);

                    return this.promotions.filter((promo) => {
                        const promoPlanId = promo.plan_id !== null ? String(promo.plan_id) : '';
                        const isPlanMatch = promoPlanId === '' || promoPlanId === planId;
                        const fromOk = !promo.starts_at || promo.starts_at <= startDate;
                        const toOk = !promo.ends_at || promo.ends_at >= startDate;
                        const usesOk = promo.max_uses === null || Number(promo.times_used) < Number(promo.max_uses);

                        return isPlanMatch && fromOk && toOk && usesOk;
                    });
                },

                selectedPromotion() {
                    const promotionId = String(this.form.promotion_id || '');
                    if (promotionId === '') {
                        return null;
                    }

                    return this.availablePromotions().find((promo) => String(promo.id) === promotionId) || null;
                },

                promotionOptionLabel(promo) {
                    const value = Number(promo.value || 0);
                    const byType = {
                        percentage: `-${value}%`,
                        fixed: `-${value.toFixed(2)}`,
                        final_price: `Precio final ${value.toFixed(2)}`,
                        bonus_days: `+${Math.max(0, Math.round(value))} días`,
                        two_for_one: '2x1',
                        bring_friend: 'Trae a un amigo',
                    };
                    return `${promo.name} (${byType[promo.type] || promo.type})`;
                },

                computePromotionalPrice(planPrice, promo) {
                    const base = Math.max(0, Number(planPrice || 0));
                    if (!promo) {
                        return { finalPrice: base, bonusDays: 0, summary: '' };
                    }

                    const value = Number(promo.value || 0);
                    let finalPrice = base;
                    let bonusDays = 0;
                    let summary = promo.name;

                    if (promo.type === 'percentage') {
                        const percent = Math.min(Math.max(value, 0), 100);
                        finalPrice = Math.max(0, base - (base * percent / 100));
                        summary += `: -${percent}%`;
                    } else if (promo.type === 'fixed') {
                        finalPrice = Math.max(0, base - Math.max(0, value));
                        summary += `: -${value.toFixed(2)}`;
                    } else if (promo.type === 'final_price') {
                        finalPrice = Math.max(0, value);
                        summary += `: precio final ${finalPrice.toFixed(2)}`;
                    } else if (promo.type === 'bonus_days') {
                        bonusDays = Math.max(0, Math.round(value));
                        summary += `: +${bonusDays} días`;
                    } else if (promo.type === 'two_for_one' || promo.type === 'bring_friend') {
                        const percent = value > 0 ? Math.min(Math.max(value, 0), 100) : 50;
                        finalPrice = Math.max(0, base - (base * percent / 100));
                        summary += `: -${percent}%`;
                    }

                    return {
                        finalPrice: Number(finalPrice.toFixed(2)),
                        bonusDays,
                        summary,
                    };
                },

                onMembershipToggle() {
                    if (!this.form.start_membership) {
                        this.form.promotion_id = '';
                        this.promotionSummaryLabel = '';
                        return;
                    }

                    this.onPlanChange();
                    this.recalculateMembershipEnd();
                },

                onPromotionChange() {
                    this.onPlanChange();
                },

                onPlanChange() {
                    const plan = this.plans.find((item) => String(item.id) === String(this.form.plan_id));
                    if (!plan) {
                        this.form.promotion_id = '';
                        this.promotionSummaryLabel = '';
                        this.recalculateMembershipEnd();
                        return;
                    }

                    const promo = this.selectedPromotion();
                    const pricing = this.computePromotionalPrice(plan.price, promo);

                    this.form.membership_price = Number(pricing.finalPrice).toFixed(2);
                    this.form.amount_paid = Number(pricing.finalPrice).toFixed(2);
                    this.promotionSummaryLabel = pricing.summary;

                    if (this.form.promotion_id !== '' && !promo) {
                        this.form.promotion_id = '';
                        this.promotionSummaryLabel = '';
                    }

                    this.recalculateMembershipEnd();
                },

                recalculateMembershipEnd() {
                    const plan = this.plans.find((item) => String(item.id) === String(this.form.plan_id));
                    const start = this.form.membership_starts_at;
                    const promo = this.selectedPromotion();
                    const promoPricing = plan ? this.computePromotionalPrice(plan.price, promo) : { bonusDays: 0 };

                    if (!plan || !start) {
                        this.membershipEndLabel = 'N/A';
                        return;
                    }

                    const startDate = new Date(`${start}T00:00:00`);
                    if (Number.isNaN(startDate.getTime())) {
                        this.membershipEndLabel = 'N/A';
                        return;
                    }

                    const endDate = this.computeMembershipEndDate(startDate, plan, promoPricing.bonusDays);
                    this.membershipEndLabel = endDate.toLocaleDateString('es-EC', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    });
                },

                async checkDocument() {
                    const value = (this.form.document_number || '').trim();
                    this.documentMatchUrl = null;

                    if (value === '') {
                        this.documentState = 'idle';
                        return;
                    }

                    this.documentState = 'checking';

                    try {
                        const url = new URL(this.documentCheckUrl, window.location.origin);
                        url.searchParams.set('document_number', value);

                        const response = await fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            this.documentState = 'idle';
                            return;
                        }

                        const payload = await response.json();
                        if (payload.exists) {
                            this.documentState = 'exists';
                            this.documentMatchUrl = payload.show_url || null;
                            return;
                        }

                        this.documentState = 'available';
                    } catch (error) {
                        this.documentState = 'idle';
                    }
                },

                get membershipBadgeLabel() {
                    if (!this.form.start_membership) {
                        return 'Sin membresía';
                    }

                    if (this.membershipEndLabel === 'N/A') {
                        return 'Pendiente de datos';
                    }

                    return 'Activa (automatica)';
                },

                get paymentStatusLabel() {
                    const price = Number(this.form.membership_price || 0);
                    const paid = Number(this.form.amount_paid || 0);

                    if (price <= 0) {
                        return 'Pendiente';
                    }

                    return paid >= price ? 'AL DÍA' : 'PENDIENTE';
                },

                get paymentBadgeClass() {
                    return this.paymentStatusLabel === 'AL DÍA'
                        ? 'border border-emerald-400/40 bg-emerald-500/20 text-emerald-200'
                        : 'border border-amber-400/40 bg-amber-500/20 text-amber-200';
                },
            };
        };
    </script>
@endpush
