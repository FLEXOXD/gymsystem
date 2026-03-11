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
        $canManagePromotions = (bool) ($canManagePromotions ?? false);
        $canManageClientAccounts = (bool) ($canManageClientAccounts ?? false);
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
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
            'promotion_id' => $canManagePromotions && old('promotion_id') !== null ? (string) old('promotion_id') : '',
            'payment_method' => (string) old('payment_method', 'cash'),
            'amount_paid' => old('amount_paid') !== null ? (string) old('amount_paid') : '',
            'create_app_account' => $canManageClientAccounts && old('create_app_account') ? true : false,
            'app_username' => $canManageClientAccounts ? (string) old('app_username', '') : '',
        ];

        $createErrorKeys = [
            'first_name',
            'last_name',
            'document_number',
            'phone',
            'gender',
            'photo',
            'start_membership',
            'plan_id',
            'membership_starts_at',
            'membership_price',
            'promotion_id',
            'payment_method',
            'amount_paid',
            'create_app_account',
            'app_username',
            'app_password',
            'app_password_confirmation',
            'cash',
        ];
        $editErrorKeys = ['edit_client_id', 'edit_first_name', 'edit_last_name', 'edit_phone', 'clients'];
        $deleteErrorKeys = ['delete_client_id', 'owner_password', 'clients'];
        $clientRowsById = collect($clients->items())->keyBy('id');
        $oldEditClientId = (int) old('edit_client_id', 0);
        $oldDeleteClientId = (int) old('delete_client_id', 0);
        $oldEditClient = $oldEditClientId > 0 ? $clientRowsById->get($oldEditClientId) : null;
        $oldDeleteClient = $oldDeleteClientId > 0 ? $clientRowsById->get($oldDeleteClientId) : null;
        $showCreateErrorSummary = collect($createErrorKeys)->contains(fn (string $key): bool => $errors->has($key));
        $showEditErrorSummary = collect($editErrorKeys)->contains(fn (string $key): bool => $errors->has($key));
        $showDeleteErrorSummary = collect($deleteErrorKeys)->contains(fn (string $key): bool => $errors->has($key));
        $createErrorMessages = collect($createErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->unique()
            ->values();
        $editErrorMessages = collect($editErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->unique()
            ->values();
        $deleteErrorMessages = collect($deleteErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->unique()
            ->values();

        $editModalDefaults = [
            'open' => old('_open_edit_modal') ? true : false,
            'action' => (string) ($oldEditClient['edit_url'] ?? ''),
            'id' => $oldEditClientId > 0 ? $oldEditClientId : null,
            'first_name' => (string) old('edit_first_name', (string) ($oldEditClient['first_name'] ?? '')),
            'last_name' => (string) old('edit_last_name', (string) ($oldEditClient['last_name'] ?? '')),
            'phone' => (string) old('edit_phone', (string) ($oldEditClient['phone'] ?? '')),
            'full_name' => (string) ($oldEditClient['full_name'] ?? ''),
        ];

        $deleteModalDefaults = [
            'open' => old('_open_delete_modal') ? true : false,
            'action' => (string) ($oldDeleteClient['delete_url'] ?? ''),
            'id' => $oldDeleteClientId > 0 ? $oldDeleteClientId : null,
            'full_name' => (string) ($oldDeleteClient['full_name'] ?? ''),
            'owner_scope_label' => (string) ($oldDeleteClient['owner_scope_label'] ?? 'dueño del gimnasio'),
            'owner_modal_hint' => (string) ($oldDeleteClient['owner_modal_hint'] ?? 'Confirma con la contraseña del dueño del gimnasio.'),
        ];
    @endphp

    <div x-data="clientsIndexPage({
            openCreateModal: @js($openCreateModal),
             plans: @js($planCatalog),
             promotions: @js($promotionCatalog),
             defaults: @js($formDefaults),
             editModal: @js($editModalDefaults),
             deleteModal: @js($deleteModalDefaults),
             documentCheckUrl: @js(route('clients.check-document')),
             allowCreate: @js(! $isGlobalScope),
             canManageClientAccounts: @js($canManageClientAccounts),
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

        @if ($isGlobalScope)
            <div class="ui-alert ui-alert-warning">
                Modo global activo: listado consolidado por sede. Para crear o editar clientes selecciona una sucursal específica.
            </div>
        @endif

        <x-ui.card title="Clientes del gimnasio" subtitle="Vista operacional para recepción, renovaciones y retención.">
            <div class="space-y-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('clients.index') }}" class="grid gap-3 lg:grid-cols-[1fr_auto_auto] lg:w-full lg:max-w-3xl">
                        <input type="hidden" name="filter" value="{{ $quickFilter }}">
                        <input id="clients-search"
                               type="text"
                               name="q"
                               value="{{ $search }}"
                               placeholder="Buscar por nombre, apellido o documento..."
                               class="ui-input">
                        <x-ui.button type="submit" variant="secondary">Buscar</x-ui.button>
                        <x-ui.button :href="route('clients.index')" variant="ghost">Limpiar</x-ui.button>
                    </form>

                    <x-ui.button id="clients-open-create"
                                 type="button"
                                 :variant="$isGlobalScope ? 'ghost' : 'primary'"
                                 x-on:click="openCreateClient()"
                                 class="whitespace-nowrap"
                                 :disabled="$isGlobalScope"
                                 title="{{ $isGlobalScope ? 'Selecciona una sede para crear clientes' : 'Crear cliente' }}">
                        {{ $isGlobalScope ? 'Solo lectura global' : '+ Nuevo cliente' }}
                    </x-ui.button>
                </div>

                <div id="clients-filter-chips" class="flex flex-wrap gap-2">
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

            <div id="clients-table" class="mt-4 overflow-hidden rounded-2xl border border-slate-300/70 dark:border-white/10">
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
                            <th class="px-3 py-3">Última asistencia</th>
                            <th class="px-3 py-3">Estado</th>
                            @if ($isGlobalScope)
                                <th class="px-3 py-3">Sede</th>
                            @endif
                            <th class="px-3 py-3">Acciones</th>
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
                                            <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">Alta: {{ $client['created_by_display'] }}</p>
                                            <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">Ultima gestion: {{ $client['last_managed_by_display'] }}</p>
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
                                @if ($isGlobalScope)
                                    <td class="px-3 py-3">
                                        <x-ui.badge variant="info">
                                            {{ $client['gym_name'] ?? '-' }}
                                        </x-ui.badge>
                                    </td>
                                @endif
                                <td class="px-3 py-3">
                                    <div class="flex min-w-[180px] flex-wrap gap-2">
                                        <a href="{{ (string) ($client['show_url'] ?? route('clients.show', ['client' => $client['id']] + ($isGlobalScope ? ['scope' => 'global'] : []))) }}"
                                           class="ui-button ui-button-secondary inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Ver
                                        </a>

                                        @if (! empty($client['can_show_progress']))
                                            <a href="{{ (string) ($client['progress_url'] ?? ($client['show_url'] ?? route('clients.show', ['client' => $client['id']] + ($isGlobalScope ? ['scope' => 'global'] : [])))) }}"
                                               class="ui-button ui-button-ghost inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold">
                                                Rendimiento
                                            </a>
                                        @endif

                                        @if (! empty($client['can_manage']))
                                            <button type="button"
                                                    class="ui-button ui-button-primary inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold"
                                                    x-on:click="openEditClient({
                                                        action: @js((string) ($client['edit_url'] ?? '')),
                                                        id: {{ (int) $client['id'] }},
                                                        first_name: @js((string) ($client['first_name'] ?? '')),
                                                        last_name: @js((string) ($client['last_name'] ?? '')),
                                                        phone: @js((string) ($client['phone'] ?? '')),
                                                        full_name: @js((string) ($client['full_name'] ?? ''))
                                                    })">
                                                Editar
                                            </button>

                                            <button type="button"
                                                    class="inline-flex items-center gap-2 rounded-xl border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:border-rose-400 hover:bg-rose-100 dark:border-rose-400/35 dark:bg-rose-500/15 dark:text-rose-100 dark:hover:border-rose-300/60 dark:hover:bg-rose-500/25"
                                                    x-on:click="openDeleteClient({
                                                        action: @js((string) ($client['delete_url'] ?? '')),
                                                        id: {{ (int) $client['id'] }},
                                                        full_name: @js((string) ($client['full_name'] ?? '')),
                                                        owner_scope_label: @js((string) ($client['owner_scope_label'] ?? 'dueño del gimnasio')),
                                                        owner_modal_hint: @js((string) ($client['owner_modal_hint'] ?? 'Confirma con la contraseña del dueño del gimnasio.'))
                                                    })">
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isGlobalScope ? 10 : 9 }}" class="px-3 py-8 text-center text-sm text-slate-600 dark:text-slate-300">
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
             class="ui-modal-backdrop items-start"
             x-on:click.self="closeCreateClient()"
             x-on:keydown.escape.window="closeCreateClient()">
            <div class="ui-modal-shell w-full max-w-3xl"
                 x-transition.scale.origin.top
                 role="dialog"
                 aria-modal="true">
                <form method="POST"
                      action="{{ route('clients.store') }}"
                      enctype="multipart/form-data"
                      novalidate
                      class="flex h-full min-h-0 flex-1 flex-col space-y-0"
                      x-on:submit="submitCreateClient($event)">
                    @csrf
                    <input type="hidden" name="_open_create_modal" value="1">

                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-100">Crear cliente</h3>
                            <p class="mt-1 text-sm text-slate-400">Alta rápida de cliente con membresía opcional.</p>
                        </div>
                        <button type="button"
                                class="ui-button ui-button-ghost px-2 py-1 text-sm"
                                x-on:click="closeCreateClient()"
                                aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                        @if ($showCreateErrorSummary)
                            <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                                <p class="font-semibold">Corrige los siguientes campos antes de guardar:</p>
                                <ul class="mt-1 list-disc space-y-1 pl-5 text-xs">
                                    @foreach ($createErrorMessages as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Nombre</span>
                                <input type="text"
                                       name="first_name"
                                       x-model="form.first_name"
                                       x-on:blur="normalizeNameField('first_name')"
                                       x-on:input="clearClientFieldError('first_name')"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.first_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       x-ref="firstNameInput">
                                <p x-cloak x-show="clientValidationErrors.first_name" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.first_name"></p>
                                @error('first_name')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Apellido</span>
                                <input type="text"
                                       name="last_name"
                                       x-model="form.last_name"
                                       x-on:blur="normalizeNameField('last_name')"
                                       x-on:input="clearClientFieldError('last_name')"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.last_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''">
                                <p x-cloak x-show="clientValidationErrors.last_name" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.last_name"></p>
                                @error('last_name')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                <span>Documento</span>
                                <input type="text"
                                       name="document_number"
                                       x-model.trim="form.document_number"
                                       x-on:input="clearClientFieldError('document_number')"
                                       x-on:input.debounce.350ms="checkDocument()"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.document_number ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       placeholder="Cédula, DNI o pasaporte">
                                <p x-cloak x-show="clientValidationErrors.document_number" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.document_number"></p>
                                @error('document_number')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
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
                                <input type="text"
                                       name="phone"
                                       x-model="form.phone"
                                       x-on:input="clearClientFieldError('phone')"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.phone ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       placeholder="Ej: 0991234567">
                                <p x-cloak x-show="clientValidationErrors.phone" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.phone"></p>
                                @error('phone')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Género</span>
                                <select name="gender" x-model="form.gender" class="ui-input">
                                    <option value="male">Hombre</option>
                                    <option value="female">Mujer</option>
                                    <option value="neutral">Neutral</option>
                                </select>
                                @error('gender')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
                            </label>

                            @if ($canManageClientAccounts)
                                <div class="space-y-3 rounded-xl border border-cyan-500/30 bg-cyan-500/5 p-3 md:col-span-2">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-100">
                                            <input type="checkbox"
                                                   name="create_app_account"
                                                   value="1"
                                                   x-model="form.create_app_account"
                                                   class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-cyan-500 focus:ring-cyan-400/40">
                                            Crear usuario y contraseña para app cliente
                                        </label>
                                        <span class="inline-flex rounded-full border border-cyan-400/30 bg-cyan-500/15 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-cyan-100">
                                            Premium / Sucursales
                                        </span>
                                    </div>

                                    <div class="grid gap-3 md:grid-cols-2" x-cloak x-show="form.create_app_account">
                                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                                            <span>Usuario app</span>
                                            <input type="text"
                                                   name="app_username"
                                                   x-model.trim="form.app_username"
                                                   x-on:input="clearClientFieldError('app_username')"
                                                   autocomplete="off"
                                                   class="ui-input"
                                                   x-bind:class="clientValidationErrors.app_username ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                                   placeholder="ej: maria.perez">
                                            <p x-cloak x-show="clientValidationErrors.app_username" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.app_username"></p>
                                            @error('app_username')
                                                <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                            @enderror
                                        </label>

                                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                                            <span>Contraseña app</span>
                                            <input type="password"
                                                   name="app_password"
                                                   x-model="form.app_password"
                                                   x-on:input="clearClientFieldError('app_password')"
                                                   autocomplete="new-password"
                                                   class="ui-input"
                                                   x-bind:class="clientValidationErrors.app_password ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                                   placeholder="Mínimo 8 caracteres">
                                            <p x-cloak x-show="clientValidationErrors.app_password" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.app_password"></p>
                                            @error('app_password')
                                                <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                            @enderror
                                        </label>

                                        <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                            <span>Confirmar contraseña app</span>
                                            <input type="password"
                                                   name="app_password_confirmation"
                                                   x-model="form.app_password_confirmation"
                                                   x-on:input="clearClientFieldError('app_password_confirmation')"
                                                   autocomplete="new-password"
                                                   class="ui-input"
                                                   x-bind:class="clientValidationErrors.app_password_confirmation ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                                   placeholder="Repite la contraseña">
                                            <p x-cloak x-show="clientValidationErrors.app_password_confirmation" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.app_password_confirmation"></p>
                                            @error('app_password_confirmation')
                                                <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                            @enderror
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-2 md:col-span-2">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Foto del cliente</span>
                                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="ui-input"
                                           x-on:change="onPhotoSelected($event)">
                                </label>
                                @error('photo')
                                    <span class="block text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror

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
                                    @error('plan_id')
                                        <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Fecha inicio</span>
                                    <input type="date" name="membership_starts_at" x-model="form.membership_starts_at" x-on:input="recalculateMembershipEnd()" x-bind:disabled="!form.start_membership" class="ui-input">
                                    @error('membership_starts_at')
                                        <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Precio</span>
                                    <input type="number" name="membership_price" x-model="form.membership_price" min="0" step="0.01" x-bind:disabled="!form.start_membership" class="ui-input">
                                    @error('membership_price')
                                        <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                    @enderror
                                </label>

                                @if ($canManagePromotions)
                                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                                        <span>Promoción (opcional)</span>
                                        <select name="promotion_id" x-model="form.promotion_id" x-on:change="onPromotionChange()" x-bind:disabled="!form.start_membership" class="ui-input">
                                            <option value="">Sin promoción</option>
                                            <template x-for="promo in availablePromotions()" :key="promo.id">
                                                <option :value="String(promo.id)" x-text="promotionOptionLabel(promo)"></option>
                                            </template>
                                        </select>
                                        @error('promotion_id')
                                            <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                        @enderror
                                    </label>
                                @else
                                    <div class="rounded-lg border border-amber-500/40 bg-amber-500/10 p-2 text-xs text-amber-200 md:col-span-2">
                                        Promociones no disponibles en tu plan actual.
                                        @error('promotion_id')
                                            <p class="mt-1 font-semibold text-rose-200">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Método de pago</span>
                                    <select name="payment_method" x-model="form.payment_method" x-bind:disabled="!form.start_membership" class="ui-input">
                                        <option value="cash">Efectivo</option>
                                        <option value="transfer">Transferencia</option>
                                        <option value="card">Tarjeta</option>
                                    </select>
                                    @error('payment_method')
                                        <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Monto pagado</span>
                                    <input type="number" name="amount_paid" x-model="form.amount_paid" min="0" step="0.01" x-bind:disabled="!form.start_membership" class="ui-input">
                                    @error('amount_paid')
                                        <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                    @enderror
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
                            @error('cash')
                                <div class="mt-3 rounded-xl border-2 border-rose-400/80 bg-rose-500/20 p-3 text-rose-100 shadow-lg">
                                    <p class="text-xs font-black uppercase tracking-wide">Debe abrir caja para cobrar</p>
                                    <p class="mt-1 text-sm font-semibold">{{ $message }}</p>
                                    <div class="mt-2">
                                        <x-ui.button :href="route('cash.index')" variant="secondary" size="sm">Ir a caja</x-ui.button>
                                    </div>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <footer class="ui-modal-sticky-footer flex items-center justify-end gap-3 px-5 py-4">
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

        <div x-cloak
             x-show="editModalOpen"
             x-transition.opacity
             class="ui-modal-backdrop items-start"
             x-on:click.self="closeEditClient()"
             x-on:keydown.escape.window="closeEditClient()">
            <div class="ui-modal-shell w-full max-w-2xl"
                 x-transition.scale.origin.top
                 role="dialog"
                 aria-modal="true">
                <form method="POST"
                      x-bind:action="editForm.action || '#'"
                      novalidate
                      class="flex h-full min-h-0 flex-1 flex-col space-y-0"
                      x-on:submit="submitEditClient($event)">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="_open_edit_modal" value="1">
                    <input type="hidden" name="edit_client_id" x-bind:value="editForm.id || ''">

                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-100">Editar cliente</h3>
                            <p class="mt-1 text-sm text-slate-400">Solo puedes actualizar nombre, apellido y teléfono.</p>
                        </div>
                        <button type="button"
                                class="ui-button ui-button-ghost px-2 py-1 text-sm"
                                x-on:click="closeEditClient()"
                                aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                        @if ($showEditErrorSummary)
                            <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                                <p class="font-semibold">No se pudo actualizar este cliente.</p>
                                <ul class="mt-1 list-disc space-y-1 pl-5 text-xs">
                                    @foreach ($editErrorMessages as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="rounded-2xl border border-cyan-500/20 bg-cyan-500/5 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200/80">Cliente seleccionado</p>
                            <p class="mt-2 text-lg font-black text-slate-100" x-text="editClientLabel()"></p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Nombre</span>
                                <input type="text"
                                       name="edit_first_name"
                                       x-model="editForm.first_name"
                                       x-on:blur="normalizeEditNameField('first_name')"
                                       x-on:input="clearEditFieldError('first_name')"
                                       class="ui-input"
                                       x-bind:class="editValidationErrors.first_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       x-ref="editFirstNameInput">
                                <p x-cloak x-show="editValidationErrors.first_name" class="text-xs font-semibold text-rose-300" x-text="editValidationErrors.first_name"></p>
                                @error('edit_first_name')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Apellido</span>
                                <input type="text"
                                       name="edit_last_name"
                                       x-model="editForm.last_name"
                                       x-on:blur="normalizeEditNameField('last_name')"
                                       x-on:input="clearEditFieldError('last_name')"
                                       class="ui-input"
                                       x-bind:class="editValidationErrors.last_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''">
                                <p x-cloak x-show="editValidationErrors.last_name" class="text-xs font-semibold text-rose-300" x-text="editValidationErrors.last_name"></p>
                                @error('edit_last_name')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                <span>Teléfono</span>
                                <input type="text"
                                       name="edit_phone"
                                       x-model="editForm.phone"
                                       x-on:input="clearEditFieldError('phone')"
                                       class="ui-input"
                                       x-bind:class="editValidationErrors.phone ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       placeholder="Ej: 0991234567">
                                <p x-cloak x-show="editValidationErrors.phone" class="text-xs font-semibold text-rose-300" x-text="editValidationErrors.phone"></p>
                                @error('edit_phone')
                                    <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </div>

                    <footer class="ui-modal-sticky-footer flex items-center justify-end gap-3 px-5 py-4">
                        <x-ui.button type="button" variant="ghost" x-on:click="closeEditClient()">Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary" x-bind:disabled="editSubmitting">Guardar cambios</x-ui.button>
                    </footer>
                </form>
            </div>
        </div>

        <div x-cloak
             x-show="deleteModalOpen"
             x-transition.opacity
             class="ui-modal-backdrop items-start"
             x-on:click.self="closeDeleteClient()"
             x-on:keydown.escape.window="closeDeleteClient()">
            <div class="ui-modal-shell w-full max-w-xl"
                 x-transition.scale.origin.top
                 role="dialog"
                 aria-modal="true">
                <form method="POST"
                      x-bind:action="deleteForm.action || '#'"
                      novalidate
                      class="flex h-full min-h-0 flex-1 flex-col space-y-0"
                      x-on:submit="submitDeleteClient($event)">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="_open_delete_modal" value="1">
                    <input type="hidden" name="delete_client_id" x-bind:value="deleteForm.id || ''">

                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-100">Eliminar cliente</h3>
                            <p class="mt-1 text-sm text-slate-400">Esta acción borrará también sus membresías y datos vinculados.</p>
                        </div>
                        <button type="button"
                                class="ui-button ui-button-ghost px-2 py-1 text-sm"
                                x-on:click="closeDeleteClient()"
                                aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                        @if ($showDeleteErrorSummary)
                            <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                                <p class="font-semibold">No se pudo eliminar este cliente.</p>
                                <ul class="mt-1 list-disc space-y-1 pl-5 text-xs">
                                    @foreach ($deleteErrorMessages as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-rose-200/80">Cliente a eliminar</p>
                            <p class="mt-2 text-lg font-black text-white" x-text="deleteForm.full_name || 'Cliente sin nombre'"></p>
                            <p class="mt-3 text-sm text-rose-100/90" x-text="deleteForm.owner_modal_hint || 'Confirma con la contraseña del dueño autorizado.'"></p>
                        </div>

                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                            <span>Contraseña del <span class="lowercase" x-text="deleteForm.owner_scope_label || 'dueño del gimnasio'"></span></span>
                            <input type="password"
                                   name="owner_password"
                                   x-model="deleteForm.owner_password"
                                   x-on:input="clearDeleteFieldError('owner_password')"
                                   class="ui-input"
                                   x-bind:class="deleteValidationErrors.owner_password ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                   autocomplete="current-password"
                                   x-ref="deletePasswordInput">
                            <p x-cloak x-show="deleteValidationErrors.owner_password" class="text-xs font-semibold text-rose-300" x-text="deleteValidationErrors.owner_password"></p>
                            @error('owner_password')
                                <span class="text-xs font-semibold text-rose-300">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <footer class="ui-modal-sticky-footer flex items-center justify-end gap-3 px-5 py-4">
                        <x-ui.button type="button" variant="ghost" x-on:click="closeDeleteClient()">Cancelar</x-ui.button>
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-rose-400/35 bg-rose-500/90 px-4 py-2 text-sm font-bold text-white transition hover:bg-rose-500 disabled:cursor-not-allowed disabled:opacity-60"
                                x-bind:disabled="deleteSubmitting">
                            Eliminar cliente
                        </button>
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
            const allowCreate = Boolean(config.allowCreate);
            return {
                allowCreate: allowCreate,
                canManageClientAccounts: Boolean(config.canManageClientAccounts),
                modalOpen: allowCreate && Boolean(config.openCreateModal),
                editModalOpen: Boolean(config.editModal?.open),
                deleteModalOpen: Boolean(config.deleteModal?.open),
                submitting: false,
                editSubmitting: false,
                deleteSubmitting: false,
                documentState: 'idle',
                documentMatchUrl: null,
                plans: Array.isArray(config.plans) ? config.plans : [],
                promotions: Array.isArray(config.promotions) ? config.promotions : [],
                documentCheckUrl: config.documentCheckUrl,
                clientValidationErrors: {},
                editValidationErrors: {},
                deleteValidationErrors: {},
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
                    create_app_account: Boolean(config.defaults?.create_app_account),
                    app_username: config.defaults?.app_username ?? '',
                    app_password: '',
                    app_password_confirmation: '',
                },
                editForm: {
                    action: config.editModal?.action ?? '',
                    id: config.editModal?.id ?? '',
                    first_name: config.editModal?.first_name ?? '',
                    last_name: config.editModal?.last_name ?? '',
                    phone: config.editModal?.phone ?? '',
                    full_name: config.editModal?.full_name ?? '',
                },
                deleteForm: {
                    action: config.deleteModal?.action ?? '',
                    id: config.deleteModal?.id ?? '',
                    full_name: config.deleteModal?.full_name ?? '',
                    owner_scope_label: config.deleteModal?.owner_scope_label ?? 'dueño del gimnasio',
                    owner_modal_hint: config.deleteModal?.owner_modal_hint ?? 'Confirma con la contraseña del dueño del gimnasio.',
                    owner_password: '',
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

                    if (this.editModalOpen) {
                        this.$nextTick(() => this.$refs.editFirstNameInput?.focus());
                    }

                    if (this.deleteModalOpen) {
                        this.$nextTick(() => this.$refs.deletePasswordInput?.focus());
                    }
                },

                openCreateClient() {
                    if (!this.allowCreate) {
                        return;
                    }
                    this.editModalOpen = false;
                    this.deleteModalOpen = false;
                    this.modalOpen = true;
                    this.$nextTick(() => this.$refs.firstNameInput?.focus());
                },

                closeCreateClient() {
                    if (this.submitting) {
                        return;
                    }
                    this.modalOpen = false;
                    this.clearClientValidationErrors();
                },

                editClientLabel() {
                    const fullName = String(this.editForm.full_name || '').trim();
                    if (fullName !== '') {
                        return fullName;
                    }

                    return `${this.editForm.first_name || ''} ${this.editForm.last_name || ''}`.trim() || 'Cliente sin nombre';
                },

                openEditClient(payload) {
                    this.modalOpen = false;
                    this.deleteModalOpen = false;
                    this.editSubmitting = false;
                    this.editValidationErrors = {};
                    this.editForm = {
                        action: payload?.action ?? '',
                        id: payload?.id ?? '',
                        first_name: payload?.first_name ?? '',
                        last_name: payload?.last_name ?? '',
                        phone: payload?.phone ?? '',
                        full_name: payload?.full_name ?? '',
                    };
                    this.editModalOpen = true;
                    this.$nextTick(() => this.$refs.editFirstNameInput?.focus());
                },

                closeEditClient() {
                    if (this.editSubmitting) {
                        return;
                    }

                    this.editModalOpen = false;
                    this.clearEditValidationErrors();
                },

                openDeleteClient(payload) {
                    this.modalOpen = false;
                    this.editModalOpen = false;
                    this.deleteSubmitting = false;
                    this.deleteValidationErrors = {};
                    this.deleteForm = {
                        action: payload?.action ?? '',
                        id: payload?.id ?? '',
                        full_name: payload?.full_name ?? '',
                        owner_scope_label: payload?.owner_scope_label ?? 'dueño del gimnasio',
                        owner_modal_hint: payload?.owner_modal_hint ?? 'Confirma con la contraseña del dueño del gimnasio.',
                        owner_password: '',
                    };
                    this.deleteModalOpen = true;
                    this.$nextTick(() => this.$refs.deletePasswordInput?.focus());
                },

                closeDeleteClient() {
                    if (this.deleteSubmitting) {
                        return;
                    }

                    this.deleteModalOpen = false;
                    this.clearDeleteValidationErrors();
                    this.deleteForm.owner_password = '';
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

                clearClientFieldError(field) {
                    if (!field) {
                        return;
                    }

                    delete this.clientValidationErrors[field];
                },

                clearClientValidationErrors() {
                    this.clientValidationErrors = {};
                },

                clearEditFieldError(field) {
                    if (!field) {
                        return;
                    }

                    delete this.editValidationErrors[field];
                },

                clearEditValidationErrors() {
                    this.editValidationErrors = {};
                },

                setEditFieldError(field, message) {
                    if (!field || !message) {
                        return;
                    }

                    this.editValidationErrors[field] = message;
                },

                clearDeleteFieldError(field) {
                    if (!field) {
                        return;
                    }

                    delete this.deleteValidationErrors[field];
                },

                clearDeleteValidationErrors() {
                    this.deleteValidationErrors = {};
                },

                setDeleteFieldError(field, message) {
                    if (!field || !message) {
                        return;
                    }

                    this.deleteValidationErrors[field] = message;
                },

                setClientFieldError(field, message) {
                    if (!field || !message) {
                        return;
                    }

                    this.clientValidationErrors[field] = message;
                },

                isSequentialDigits(value) {
                    const text = String(value || '');
                    if (text.length < 6 || text.length > 10) {
                        return false;
                    }

                    return '0123456789'.includes(text) || '9876543210'.includes(text);
                },

                validateDocumentField() {
                    const raw = String(this.form.document_number || '').trim();
                    const canonical = raw.toUpperCase().replace(/[-\s]/g, '');

                    if (raw === '') {
                        return 'Ingresa el documento del cliente.';
                    }

                    if (!/^[A-Za-z0-9\- ]+$/.test(raw)) {
                        return 'El documento solo puede usar letras, números, espacios y guion.';
                    }

                    if (canonical.length < 6 || canonical.length > 20) {
                        return 'El documento debe tener entre 6 y 20 caracteres utiles.';
                    }

                    if (!/\d/.test(canonical)) {
                        return 'El documento debe incluir al menos un número.';
                    }

                    if (/^(.)\1+$/.test(canonical)) {
                        return 'El documento ingresado no parece válido.';
                    }

                    if (/^\d+$/.test(canonical) && this.isSequentialDigits(canonical)) {
                        return 'El documento ingresado no parece válido.';
                    }

                    return '';
                },

                validatePhoneField() {
                    return this.validatePhoneValue(this.form.phone);
                },

                validatePhoneValue(value) {
                    const raw = String(value || '').trim();
                    const digits = raw.replace(/\D/g, '');

                    if (raw === '') {
                        return 'Ingresa el teléfono del cliente.';
                    }

                    if (!/^[0-9+\-\s()]+$/.test(raw)) {
                        return 'El teléfono solo puede contener números y los símbolos + - ( ).';
                    }

                    if (digits.length < 7 || digits.length > 15) {
                        return 'El teléfono debe tener entre 7 y 15 dígitos.';
                    }

                    if (/^(\d)\1+$/.test(digits)) {
                        return 'El teléfono ingresado no parece válido.';
                    }

                    if (this.isSequentialDigits(digits)) {
                        return 'El teléfono ingresado no parece válido.';
                    }

                    return '';
                },

                validateAppUsernameField() {
                    const username = String(this.form.app_username || '').trim().toLowerCase();
                    if (username === '') {
                        return 'Ingresa el usuario para la app cliente.';
                    }

                    if (username.length < 4 || username.length > 80) {
                        return 'El usuario debe tener entre 4 y 80 caracteres.';
                    }

                    if (!/^[a-z0-9._-]+$/.test(username)) {
                        return 'El usuario solo puede usar letras minúsculas, números, punto, guion y guion bajo.';
                    }

                    return '';
                },

                validateCreateClientForm() {
                    this.clearClientValidationErrors();

                    const firstName = String(this.form.first_name || '').trim();
                    const lastName = String(this.form.last_name || '').trim();
                    const documentError = this.validateDocumentField();
                    const phoneError = this.validatePhoneField();

                    if (firstName === '') {
                        this.setClientFieldError('first_name', 'Ingresa el nombre del cliente.');
                    }

                    if (lastName === '') {
                        this.setClientFieldError('last_name', 'Ingresa el apellido del cliente.');
                    }

                    if (documentError !== '') {
                        this.setClientFieldError('document_number', documentError);
                    } else if (this.documentState === 'exists') {
                        this.setClientFieldError('document_number', 'Este documento ya está registrado en este gimnasio.');
                    }

                    if (phoneError !== '') {
                        this.setClientFieldError('phone', phoneError);
                    }

                    if (this.canManageClientAccounts && this.form.create_app_account) {
                        const appUsernameError = this.validateAppUsernameField();
                        const appPassword = String(this.form.app_password || '');
                        const appPasswordConfirmation = String(this.form.app_password_confirmation || '');

                        if (appUsernameError !== '') {
                            this.setClientFieldError('app_username', appUsernameError);
                        }

                        if (appPassword.length < 8) {
                            this.setClientFieldError('app_password', 'La contraseña debe tener al menos 8 caracteres.');
                        }

                        if (appPasswordConfirmation === '') {
                            this.setClientFieldError('app_password_confirmation', 'Confirma la contraseña de la app cliente.');
                        } else if (appPasswordConfirmation !== appPassword) {
                            this.setClientFieldError('app_password_confirmation', 'La confirmación de contraseña no coincide.');
                        }
                    }

                    return Object.keys(this.clientValidationErrors).length === 0;
                },

                normalizeEditNameField(field) {
                    if (field !== 'first_name' && field !== 'last_name') {
                        return;
                    }

                    this.editForm[field] = this.formatPersonName(this.editForm[field]);
                },

                validateEditClientForm() {
                    this.clearEditValidationErrors();

                    const firstName = String(this.editForm.first_name || '').trim();
                    const lastName = String(this.editForm.last_name || '').trim();
                    const phoneError = this.validatePhoneValue(this.editForm.phone);

                    if (firstName === '') {
                        this.setEditFieldError('first_name', 'Ingresa el nombre del cliente.');
                    }

                    if (lastName === '') {
                        this.setEditFieldError('last_name', 'Ingresa el apellido del cliente.');
                    }

                    if (phoneError !== '') {
                        this.setEditFieldError('phone', phoneError);
                    }

                    return Object.keys(this.editValidationErrors).length === 0;
                },

                focusFirstEditValidationError() {
                    if (this.editValidationErrors.first_name) {
                        this.$refs.editFirstNameInput?.focus();
                        return;
                    }

                    const fieldOrder = ['last_name', 'phone'];
                    for (const fieldName of fieldOrder) {
                        if (!this.editValidationErrors[fieldName]) {
                            continue;
                        }

                        const input = this.$el.querySelector(`[name="edit_${fieldName}"]`);
                        if (input) {
                            input.focus();
                        }
                        return;
                    }
                },

                submitEditClient(event) {
                    this.normalizeEditNameField('first_name');
                    this.normalizeEditNameField('last_name');
                    this.editSubmitting = false;

                    if (!this.validateEditClientForm()) {
                        event.preventDefault();
                        this.focusFirstEditValidationError();
                        return;
                    }

                    this.editSubmitting = true;
                },

                validateDeleteClientForm() {
                    this.clearDeleteValidationErrors();

                    if (String(this.deleteForm.owner_password || '').trim() === '') {
                        this.setDeleteFieldError('owner_password', 'Ingresa la contraseña del dueño autorizado.');
                    }

                    return Object.keys(this.deleteValidationErrors).length === 0;
                },

                submitDeleteClient(event) {
                    this.deleteSubmitting = false;

                    if (!this.validateDeleteClientForm()) {
                        event.preventDefault();
                        this.$refs.deletePasswordInput?.focus();
                        return;
                    }

                    this.deleteSubmitting = true;
                },

                focusFirstClientValidationError() {
                    if (this.clientValidationErrors.first_name) {
                        this.$refs.firstNameInput?.focus();
                        return;
                    }

                    const fieldOrder = ['last_name', 'document_number', 'phone', 'app_username', 'app_password', 'app_password_confirmation'];
                    for (const fieldName of fieldOrder) {
                        if (!this.clientValidationErrors[fieldName]) {
                            continue;
                        }

                        const input = this.$el.querySelector(`[name="${fieldName}"]`);
                        if (input) {
                            input.focus();
                        }
                        return;
                    }
                },

                submitCreateClient(event) {
                    this.normalizeNameField('first_name');
                    this.normalizeNameField('last_name');
                    if (this.canManageClientAccounts) {
                        this.form.app_username = String(this.form.app_username || '').trim().toLowerCase();
                        if (!this.form.create_app_account) {
                            this.form.app_username = '';
                            this.form.app_password = '';
                            this.form.app_password_confirmation = '';
                        }
                    }
                    this.submitting = false;

                    if (!this.validateCreateClientForm()) {
                        event.preventDefault();
                        this.focusFirstClientValidationError();
                        return;
                    }

                    this.submitting = true;
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

                    return 'Activa (automática)';
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
