@extends('layouts.panel')

@section('title', 'Cajeros')
@section('page-title', 'Gestión de cajeros')

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
            'basico' => 'Básico',
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
    @endphp

    <div class="space-y-5">
        @if (! $roleSchemaReady)
            <div class="ui-alert ui-alert-danger text-sm">
                {{ $schemaErrorMessage !== '' ? $schemaErrorMessage : 'Falta la migración de roles de usuarios.' }}
            </div>
        @endif

        <x-ui.card
            :title="$isGlobalStaffView ? 'Resumen global de cajeros' : 'Cupo de cajeros'"
            :subtitle="$isGlobalStaffView
                ? 'Vista consolidada de cajeros en todas las sedes vinculadas (solo lectura).'
                : 'Límites aplicados según el plan activo de esta sede.'">
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
                    Modo global activo: puedes consultar cajeros de todas las sedes, pero crear/editar/eliminar se realiza desde una sede específica.
                </div>
            @elseif ((int) $maxCashiers <= 0)
                <div class="ui-alert ui-alert-warning mt-4 text-sm">
                    Tu plan actual no permite crear cajeros. Sube a Profesional, Premium o Sucursales.
                </div>
            @endif
        </x-ui.card>

        @if (! $isGlobalStaffView)
            <x-ui.card title="Crear cajero" subtitle="Acceso a panel/recepción/clientes. Por defecto no abre ni cierra caja.">
                <form method="POST" action="{{ route('staff.cashiers.store', $contextParams) }}" class="grid gap-3 lg:grid-cols-4">
                    @csrf
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        Nombre
                        <input type="text" name="name" class="ui-input" value="{{ old('name') }}" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300 lg:col-span-2">
                        Correo
                        <input type="email" name="email" class="ui-input" value="{{ old('email') }}" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Contraseña
                        <input type="password" name="password" class="ui-input" required>
                    </label>
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Confirmar contraseña
                        <input type="password" name="password_confirmation" class="ui-input" required>
                    </label>
                    <div class="flex items-end lg:col-span-2">
                        <x-ui.button type="submit" :disabled="!$roleSchemaReady || (int) $remainingCashiers <= 0">Crear cajero</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        @endif

        <x-ui.card
            title="Cajeros"
            :subtitle="$isGlobalStaffView
                ? 'Listado consolidado de cajeros por sede (solo lectura).'
                : 'Activa/desactiva usuarios, define permisos de caja y archiva cajeros sin perder su historial.'">
            <div class="overflow-x-auto">
                <table class="ui-table {{ $isGlobalStaffView ? 'min-w-[980px]' : 'min-w-[1320px]' }}">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Nombre</th>
                            <th class="px-3 py-3">Correo</th>
                            @if ($isGlobalStaffView)
                                <th class="px-3 py-3">Sede</th>
                            @endif
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Creado</th>
                            <th class="px-3 py-3">último acceso</th>
                            <th class="px-3 py-3">Permisos caja</th>
                            @if (! $isGlobalStaffView)
                                <th class="px-3 py-3">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cashiers as $cashier)
                            <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
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
                                        <form method="POST" action="{{ route('staff.cashiers.permissions.update', $contextParams + ['cashier' => $cashier->id]) }}" class="grid gap-2">
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
                                        <div class="flex flex-wrap items-center gap-2">
                                            <form method="POST" action="{{ route('staff.cashiers.password.update', $contextParams + ['cashier' => $cashier->id]) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="password" name="password" class="ui-input !w-40" placeholder="Nueva contraseña" required>
                                                <input type="password" name="password_confirmation" class="ui-input !w-40" placeholder="Confirmar" required>
                                                <x-ui.button type="submit" size="sm" variant="ghost">Actualizar clave</x-ui.button>
                                            </form>

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
    </div>
@endsection
