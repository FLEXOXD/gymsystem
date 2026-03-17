@extends('layouts.panel')

@section('title', 'Cajeros')
@section('page-title', 'GestiÃ³n de cajeros')

@push('styles')
<style>
    .staff-page .staff-table-filters {
        display: grid;
        gap: .75rem;
    }
    @media (min-width: 1280px) {
        .staff-page .staff-table-filters {
            grid-template-columns: minmax(0, 1fr) 14rem auto auto;
            align-items: end;
        }
    }
    .staff-page .staff-table-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .staff-page .staff-permissions-grid {
        display: grid;
        gap: .35rem;
    }
    .staff-page .staff-actions-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .5rem;
    }
    .staff-page .staff-password-trigger {
        min-width: 7.25rem;
    }
    @media (max-width: 640px) {
        .staff-page .staff-actions-wrap form,
        .staff-page .staff-actions-wrap .staff-password-trigger,
        .staff-page .staff-actions-wrap .ui-button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    @php
        $roleSchemaReady = (bool) ($roleSchemaReady ?? true);
        $schemaErrorMessage = trim((string) ($schemaErrorMessage ?? ''));
        $isGlobalStaffView = (bool) ($isGlobalStaffView ?? false);
        $inactiveCashiers = (int) ($inactiveCashiers ?? 0);
        $scopeGymCount = (int) ($scopeGymCount ?? 1);
        $totalCashiers = (int) ($totalCashiers ?? ($cashiers instanceof \Illuminate\Support\Collection ? $cashiers->count() : 0));
        $activeCashiers = (int) ($activeCashiers ?? $currentCashiers ?? 0);
        $currentPlanLabel = match ($currentPlanKey ?? '') {
            'basico' => 'BÃ¡sico',
            'profesional' => 'Profesional',
            'premium' => 'Premium',
            'sucursales' => 'Sucursales',
            default => strtoupper((string) ($currentPlanKey ?? '-')),
        };
        $contextGym = (string) (request()->route('contextGym') ?? '');
        $contextParams = $contextGym !== '' ? ['contextGym' => $contextGym] : [];
        if ($isGlobalStaffView) {
            $contextParams['scope'] = 'global';
        }
        $routeHasPasswordUpdate = \Illuminate\Support\Facades\Route::has('staff.cashiers.password.update');
        $passwordRouteTemplate = $routeHasPasswordUpdate
            ? route('staff.cashiers.password.update', $contextParams + ['cashier' => '__CASHIER__'])
            : '';
    @endphp

    <div class="staff-page space-y-5" data-password-route-template="{{ $passwordRouteTemplate }}">
        @if (! $roleSchemaReady)
            <div class="ui-alert ui-alert-danger text-sm">
                {{ $schemaErrorMessage !== '' ? $schemaErrorMessage : 'Falta la migraciÃ³n de roles de usuarios.' }}
            </div>
        @endif

        <x-ui.card
            :title="$isGlobalStaffView ? 'Resumen global de cajeros' : 'Cupo de cajeros'"
            :subtitle="$isGlobalStaffView
                ? 'Vista consolidada de cajeros en todas las sedes vinculadas (solo lectura).'
                : 'LÃ­mites aplicados segÃºn el plan activo de esta sede.'">
            <div class="grid gap-3 sm:grid-cols-4">
                <article class="rounded-xl border border-slate-300/60 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/60">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">Plan actual</p>
                    <p class="mt-1 text-lg font-black text-slate-900 dark:text-slate-100">{{ $currentPlanLabel }}</p>
                </article>

                @if ($isGlobalStaffView)
                    <article class="rounded-xl border border-indigo-300/50 bg-indigo-50 p-3 dark:border-indigo-500/40 dark:bg-indigo-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-200">Sedes en alcance</p>
                        <p class="mt-1 text-lg font-black text-indigo-900 dark:text-indigo-100">{{ $scopeGymCount }}</p>
                    </article>
                    <article class="rounded-xl border border-cyan-300/50 bg-cyan-50 p-3 dark:border-cyan-500/40 dark:bg-cyan-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-200">Cajeros activos</p>
                        <p class="mt-1 text-lg font-black text-cyan-900 dark:text-cyan-100">{{ $activeCashiers }}</p>
                    </article>
                    <article class="rounded-xl border border-amber-300/50 bg-amber-50 p-3 dark:border-amber-500/40 dark:bg-amber-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-700 dark:text-amber-200">Cajeros inactivos</p>
                        <p class="mt-1 text-lg font-black text-amber-900 dark:text-amber-100">{{ $inactiveCashiers }}</p>
                    </article>
                @else
                    <article class="rounded-xl border border-cyan-300/50 bg-cyan-50 p-3 dark:border-cyan-500/40 dark:bg-cyan-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-200">Cajeros activos</p>
                        <p class="mt-1 text-lg font-black text-cyan-900 dark:text-cyan-100">{{ (int) $currentCashiers }}</p>
                    </article>
                    <article class="rounded-xl border border-emerald-300/50 bg-emerald-50 p-3 dark:border-emerald-500/40 dark:bg-emerald-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-200">Cupo restante</p>
                        <p class="mt-1 text-lg font-black text-emerald-900 dark:text-emerald-100">{{ (int) $remainingCashiers }} / {{ (int) $maxCashiers }}</p>
                    </article>
                    <article class="rounded-xl border border-amber-300/50 bg-amber-50 p-3 dark:border-amber-500/40 dark:bg-amber-900/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-700 dark:text-amber-200">Cajeros inactivos</p>
                        <p class="mt-1 text-lg font-black text-amber-900 dark:text-amber-100">{{ $inactiveCashiers }}</p>
                    </article>
                @endif
            </div>

            @if ($isGlobalStaffView)
                <div class="ui-alert ui-alert-info mt-4 text-sm">
                    Modo global activo: puedes consultar cajeros de todas las sedes, pero crear/editar/eliminar se realiza desde una sede especÃ­fica.
                </div>
            @elseif ((int) $maxCashiers <= 0)
                <div class="ui-alert ui-alert-warning mt-4 text-sm">
                    Tu plan actual no permite crear cajeros. Sube a Profesional, Premium o Sucursales.
                </div>
            @endif
        </x-ui.card>

        @if (! $isGlobalStaffView)
            <x-ui.card title="Crear cajero" subtitle="Acceso a panel/recepciÃ³n/clientes. Por defecto no abre ni cierra caja.">
                <form method="POST" action="{{ route('staff.cashiers.store', $contextParams) }}" class="grid gap-3 lg:grid-cols-6">
                    @csrf
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-3">
                        Nombre
                        <input type="text" name="name" class="ui-input" value="{{ old('name') }}" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-3">
                        Correo
                        <input type="email" name="email" class="ui-input" value="{{ old('email') }}" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        ContraseÃ±a
                        <input type="password" name="password" class="ui-input" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        Confirmar contraseÃ±a
                        <input type="password" name="password_confirmation" class="ui-input" required>
                    </label>
                    <div class="flex items-end lg:col-span-2">
                        <x-ui.button type="submit" class="w-full justify-center" :disabled="!$roleSchemaReady || (int) $remainingCashiers <= 0">Crear cajero</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        @endif

        <x-ui.card
            title="Cajeros"
            :subtitle="$isGlobalStaffView
                ? 'Listado consolidado de cajeros por sede (solo lectura).'
                : 'Activa/desactiva usuarios, define permisos de caja y archiva cajeros sin perder su historial.'">
            <div class="staff-table-filters mb-3">
                <label class="space-y-1 text-sm font-semibold text-slate-500 dark:text-slate-300">
                    <span class="text-xs uppercase tracking-wide">Buscar cajero</span>
                    <input id="staff-search" type="search" class="ui-input" placeholder="Nombre o correo">
                </label>
                @if (! $isGlobalStaffView)
                    <label class="space-y-1 text-sm font-semibold text-slate-500 dark:text-slate-300">
                        <span class="text-xs uppercase tracking-wide">Estado</span>
                        <select id="staff-status-filter" class="ui-input">
                            <option value="all">Todos</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                        </select>
                    </label>
                @else
                    <div></div>
                @endif
                <div class="pt-1 xl:pt-0">
                    <span id="staff-count-badge" class="inline-flex rounded-full border border-slate-300/35 px-3 py-1 text-xs font-bold text-slate-400">{{ $cashiers->count() }} cajeros</span>
                </div>
                <button id="staff-clear-filters" type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold">Limpiar filtros</button>
            </div>

            <div class="staff-table-wrap overflow-x-auto">
                <table class="ui-table {{ $isGlobalStaffView ? 'min-w-[980px]' : 'min-w-[1140px]' }}">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Nombre</th>
                            <th class="px-3 py-3">Correo</th>
                            @if ($isGlobalStaffView)
                                <th class="px-3 py-3">Sede</th>
                            @endif
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Creado</th>
                            <th class="px-3 py-3">Ãºltimo acceso</th>
                            <th class="px-3 py-3">Permisos caja</th>
                            @if (! $isGlobalStaffView)
                                <th class="px-3 py-3">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cashiers as $cashier)
                            <tr
                                data-staff-name="{{ mb_strtolower((string) $cashier->name) }}"
                                data-staff-email="{{ mb_strtolower((string) $cashier->email) }}"
                                data-staff-status="{{ (bool) ($cashier->is_active ?? false) ? 'active' : 'inactive' }}"
                                class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                                <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-100">{{ $cashier->name }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $cashier->email }}</td>
                                @if ($isGlobalStaffView)
                                    <td class="px-3 py-3 dark:text-slate-200">
                                        {{ (string) ($cashier->gym?->name ?? 'Sede sin nombre') }}
                                    </td>
                                @endif
                                <td class="px-3 py-3">
                                    @if ((bool) ($cashier->is_active ?? false))
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">Activo</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-200">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $cashier->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-3 py-3 dark:text-slate-200">{{ $cashier->last_login_at?->format('Y-m-d H:i') ?? 'Sin acceso' }}</td>
                                <td class="px-3 py-3">
                                    @if ($isGlobalStaffView)
                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ (bool) ($cashier->can_manage_cash_movements ?? false) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                                {{ (bool) ($cashier->can_manage_cash_movements ?? false) ? 'Cobros: si' : 'Cobros: no' }}
                                            </span>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ (bool) ($cashier->can_open_cash ?? false) ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                                {{ (bool) ($cashier->can_open_cash ?? false) ? 'Abrir caja: si' : 'Abrir caja: no' }}
                                            </span>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ (bool) ($cashier->can_close_cash ?? false) ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                                {{ (bool) ($cashier->can_close_cash ?? false) ? 'Cerrar caja: si' : 'Cerrar caja: no' }}
                                            </span>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('staff.cashiers.permissions.update', $contextParams + ['cashier' => $cashier->id]) }}" class="staff-permissions-grid">
                                            @csrf
                                            @method('PATCH')
                                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <input type="checkbox" name="can_manage_cash_movements" value="1" @checked((bool) ($cashier->can_manage_cash_movements ?? false))>
                                                Puede registrar cobros/movimientos
                                            </label>
                                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <input type="checkbox" name="can_open_cash" value="1" @checked((bool) ($cashier->can_open_cash ?? false))>
                                                Puede abrir caja
                                            </label>
                                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <input type="checkbox" name="can_close_cash" value="1" @checked((bool) ($cashier->can_close_cash ?? false))>
                                                Puede cerrar caja
                                            </label>
                                            <div>
                                                <x-ui.button type="submit" size="sm" variant="ghost">Guardar permisos</x-ui.button>
                                            </div>
                                        </form>
                                    @endif
                                </td>
                                @if (! $isGlobalStaffView)
                                    <td class="px-3 py-3">
                                        <div class="staff-actions-wrap">
                                            <x-ui.button
                                                type="button"
                                                size="sm"
                                                variant="ghost"
                                                class="staff-password-trigger js-open-password-modal"
                                                data-cashier-id="{{ $cashier->id }}"
                                                data-cashier-name="{{ $cashier->name }}">
                                                Actualizar clave
                                            </x-ui.button>

                                            @if ((bool) ($cashier->is_active ?? false))
                                                <form method="POST" action="{{ route('staff.cashiers.disable', $contextParams + ['cashier' => $cashier->id]) }}" onsubmit="return confirm('Desactivar este cajero y liberar cupo?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button type="submit" size="sm" variant="danger">Desactivar</x-ui.button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('staff.cashiers.activate', $contextParams + ['cashier' => $cashier->id]) }}" onsubmit="return confirm('Reactivar este cajero?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-ui.button type="submit" size="sm" variant="success">Activar</x-ui.button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('staff.cashiers.destroy', $contextParams + ['cashier' => $cashier->id]) }}" onsubmit="return confirm('Archivar este cajero? Perdera acceso, pero se conservaran sus clientes, cobros e historial.');">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" size="sm" variant="danger">Archivar</x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isGlobalStaffView ? 7 : 8 }}" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                    {{ $isGlobalStaffView ? 'No hay cajeros registrados en el alcance global.' : 'No hay cajeros registrados en esta sede.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        @if (! $isGlobalStaffView)
            <div id="staff-password-modal" class="modal-shell" aria-hidden="true" aria-labelledby="staff-password-title">
                <div class="modal-card">
                    <div class="flex items-center justify-between border-b border-slate-300/25 px-4 py-3">
                        <h3 id="staff-password-title" class="text-base font-black text-slate-100">Actualizar contraseña</h3>
                        <button type="button" class="mini-action" data-close-staff-modal="staff-password-modal">Cerrar</button>
                    </div>
                    <form id="staff-password-form" method="POST" action="#" class="space-y-3 px-4 py-4">
                        @csrf
                        @method('PATCH')
                        <p id="staff-password-label" class="text-sm font-semibold text-slate-300">Cajero seleccionado</p>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Nueva contraseña
                            <input type="password" name="password" class="ui-input" placeholder="Minimo 8 caracteres" required minlength="8">
                        </label>
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Confirmar contraseña
                            <input type="password" name="password_confirmation" class="ui-input" placeholder="Repite la contraseña" required minlength="8">
                        </label>
                        <div class="flex justify-end gap-2 pt-1">
                            <x-ui.button type="button" variant="muted" size="sm" data-close-staff-modal="staff-password-modal">Cancelar</x-ui.button>
                            <x-ui.button type="submit" variant="secondary" size="sm">Actualizar clave</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.querySelector('.staff-page');
    if (!root) return;

    const searchInput = document.getElementById('staff-search');
    const statusFilter = document.getElementById('staff-status-filter');
    const clearFilters = document.getElementById('staff-clear-filters');
    const countBadge = document.getElementById('staff-count-badge');
    const rows = Array.from(document.querySelectorAll('tr[data-staff-name]'));

    const applyFilters = () => {
        const q = String(searchInput?.value || '').trim().toLowerCase();
        const status = String(statusFilter?.value || 'all');
        let visible = 0;

        rows.forEach((row) => {
            const name = String(row.getAttribute('data-staff-name') || '');
            const email = String(row.getAttribute('data-staff-email') || '');
            const rowStatus = String(row.getAttribute('data-staff-status') || '');
            const okText = q === '' || name.includes(q) || email.includes(q);
            const okStatus = status === 'all' || rowStatus === status;
            const show = okText && okStatus;
            row.classList.toggle('hidden', !show);
            if (show) visible += 1;
        });

        if (countBadge) {
            countBadge.textContent = `${visible} cajero${visible === 1 ? '' : 's'}`;
        }
    };

    searchInput?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
    clearFilters?.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = 'all';
        applyFilters();
        searchInput?.focus();
    });
    applyFilters();

    const routeTemplate = String(root.getAttribute('data-password-route-template') || '');
    const passwordModal = document.getElementById('staff-password-modal');
    const passwordForm = document.getElementById('staff-password-form');
    const passwordLabel = document.getElementById('staff-password-label');
    const openButtons = Array.from(document.querySelectorAll('.js-open-password-modal'));

    const openModal = (modal) => {
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.modal-shell.is-open')) {
            document.body.classList.remove('overflow-hidden');
        }
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const cashierId = String(button.getAttribute('data-cashier-id') || '');
            const cashierName = String(button.getAttribute('data-cashier-name') || 'cajero');
            if (!cashierId || !passwordForm || routeTemplate === '') return;
            passwordForm.action = routeTemplate.replace('__CASHIER__', cashierId);
            if (passwordLabel) passwordLabel.textContent = `Actualizar clave de ${cashierName}`;
            passwordForm.reset();
            openModal(passwordModal);
        });
    });

    document.querySelectorAll('[data-close-staff-modal]').forEach((button) => {
        button.addEventListener('click', () => closeModal(passwordModal));
    });

    passwordModal?.addEventListener('click', (event) => {
        if (event.target === passwordModal) closeModal(passwordModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal(passwordModal);
    });
})();
</script>
@endpush

