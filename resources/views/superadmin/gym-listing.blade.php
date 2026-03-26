@extends('layouts.panel')

@section('title', 'Listado de gimnasios')
@section('page-title', 'Listado de gimnasios')

@section('content')
    @php
        $gymsWithAdmins = $gymsWithAdmins ?? collect();
        $adminEditErrorKeys = [
            'admin_user_id',
            'admin_name',
            'admin_email',
            'admin_gender',
            'admin_birth_date',
            'admin_identification_type',
            'admin_identification_number',
            'admin_country_iso',
            'admin_address_state',
            'admin_address_city',
            'admin_address_line',
            'admin_phone_country_dial',
            'admin_phone_number',
            'admin_profile_photo',
        ];
        $adminEditHasErrors = $errors->hasAny($adminEditErrorKeys);
        $adminEditOldGymId = (int) old('admin_gym_id', 0);
        $adminEditRouteTemplate = route('superadmin.gyms.admin-user.update', ['gym' => '__GYM__']);
        $adminEditOldData = [
            'hasErrors' => $adminEditHasErrors,
            'gymId' => $adminEditOldGymId,
            'userId' => (int) old('admin_user_id', 0),
            'name' => (string) old('admin_name', ''),
            'email' => (string) old('admin_email', ''),
            'gender' => (string) old('admin_gender', ''),
            'birthDate' => (string) old('admin_birth_date', ''),
            'identificationType' => (string) old('admin_identification_type', ''),
            'identificationNumber' => (string) old('admin_identification_number', ''),
            'countryIso' => strtolower((string) old('admin_country_iso', '')),
            'addressState' => (string) old('admin_address_state', ''),
            'addressCity' => (string) old('admin_address_city', ''),
            'addressLine' => (string) old('admin_address_line', ''),
            'phoneCountryDial' => (string) old('admin_phone_country_dial', ''),
            'phoneNumber' => (string) old('admin_phone_number', ''),
        ];
        $passwordResetErrorKeys = [
            'reset_password_gym_id',
            'reset_password_user_id',
            'reset_password',
            'reset_password_confirmation',
        ];
        $passwordResetHasErrors = $errors->hasAny($passwordResetErrorKeys);
        $passwordResetRouteTemplate = route('superadmin.gyms.admin-user.password.update', ['gym' => '__GYM__']);
        $passwordResetOldData = [
            'hasErrors' => $passwordResetHasErrors,
            'gymId' => (int) old('reset_password_gym_id', 0),
            'userId' => (int) old('reset_password_user_id', 0),
        ];
        $branchCount = $gymsWithAdmins->filter(fn ($gym) => $gym->parentHubLinks->isNotEmpty())->count();
        $hubCount = $gymsWithAdmins->filter(fn ($gym) => $gym->parentHubLinks->isEmpty() && $gym->branchLinks->isNotEmpty())->count();
        $independentCount = $gymsWithAdmins->filter(fn ($gym) => $gym->parentHubLinks->isEmpty() && $gym->branchLinks->isEmpty())->count();
        $withAdminCount = $gymsWithAdmins->filter(fn ($gym) => $gym->users->isNotEmpty())->count();
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Gestión de admins</span>
                    <h2 class="sa-title">Accesos y estructura en una vista mas clara y menos riesgosa.</h2>
                    <p class="sa-subtitle">
                        Primero ves el tipo de gimnasio y luego editas usuarios, para reducir errores al tocar accesos o borrar.
                    </p>

                    <div class="sa-actions">
                        <x-ui.button :href="route('superadmin.gym.index')">Crear gimnasio</x-ui.button>
                        <x-ui.button :href="route('superadmin.gyms.index')" variant="secondary">Ver suscripciones</x-ui.button>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Desde aqui puedes</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>Editar admin</strong>
                            <span>Actualizar datos del admin principal sin entrar al panel.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Reset de acceso</strong>
                            <span>Restablecer contrasenas desde un modal separado.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>Borrado aislado</strong>
                            <span>La accion destructiva queda separada del mantenimiento normal.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total gimnasios</p>
                <p class="sa-stat-value">{{ $gymsWithAdmins->count() }}</p>
                <p class="sa-stat-meta">Base visible para mantenimiento administrativo.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Sucursales</p>
                <p class="sa-stat-value">{{ $branchCount }}</p>
                <p class="sa-stat-meta">Cuentas que dependen de una casa matriz.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Casas matriz</p>
                <p class="sa-stat-value">{{ $hubCount }}</p>
                <p class="sa-stat-meta">Gimnasios que administran sucursales.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Independientes</p>
                <p class="sa-stat-value">{{ $independentCount }}</p>
                <p class="sa-stat-meta">Operaciones sin estructura multisede.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Con admin asignado</p>
                <p class="sa-stat-value">{{ $withAdminCount }}</p>
                <p class="sa-stat-meta">Cuentas con usuario principal listo.</p>
            </article>
        </section>

        <x-ui.card title="Listado de gimnasios" subtitle="Edita admins por gimnasio y filtra rapido por estructura.">
            <p class="mb-3 text-xs font-semibold text-rose-700 dark:text-rose-300">
                Eliminar gimnasio borrará todo: clientes, planes, membresías, caja, reportes y usuario del gimnasio.
            </p>
            @if ($adminEditHasErrors)
                <div class="mb-3 rounded-xl border border-rose-300/60 bg-rose-100/60 px-3 py-2 text-sm font-semibold text-rose-800 dark:border-rose-300/40 dark:bg-rose-300/10 dark:text-rose-200">
                    Revisa los datos del usuario. Hay campos con errores.
                </div>
            @endif

            <div class="mb-4 grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto] lg:items-end">
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Buscar gimnasio</span>
                    <input
                        id="gym-list-search"
                        type="text"
                        class="ui-input"
                        placeholder="Buscar por gimnasio, responsable, correo, slug o sede principal"
                        autocomplete="off"
                    >
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Tipo</span>
                    <select id="gym-list-type" class="ui-input">
                        <option value="all">Todos</option>
                        <option value="branch">Solo sucursales</option>
                        <option value="hub">Solo casa matriz</option>
                        <option value="independent">Solo independientes</option>
                    </select>
                </label>

                <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                    <span class="ui-badge ui-badge-muted">
                        Total: {{ $gymsWithAdmins->count() }}
                    </span>
                    <span class="ui-badge ui-badge-info" role="status" aria-live="polite">
                        Visibles: <strong id="gym-list-visible-count" class="ml-1">{{ $gymsWithAdmins->count() }}</strong>
                    </span>
                    <button id="gym-list-clear" type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold">
                        Limpiar filtros
                    </button>
                </div>
            </div>
            <p id="gym-list-filter-help" class="sa-filter-note mb-4">
                Busca por gimnasio, responsable o estructura. El filtro deja visibles solo los casos sobre los que puedes actuar.
            </p>

            <div class="sa-table-shell overflow-x-auto smart-list-wrap">
                <table class="ui-table min-w-[1040px]" data-smart-list-manual aria-describedby="gym-list-filter-help gym-list-table-help">
                    <caption id="gym-list-table-help" class="sr-only">
                        Tabla de gimnasios con filtros por texto y tipo de estructura.
                    </caption>
                    <thead>
                    <tr>
                        <th>Gimnasio</th>
                        <th>Responsable</th>
                        <th>Perfil</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($gymsWithAdmins as $gymRow)
                        @php
                            $adminUser = $gymRow->users->first();
                            $parentLink = $gymRow->parentHubLinks->first();
                            $hubGym = $parentLink?->hubGym;
                            $linkedBranchCount = $gymRow->branchLinks->count();
                            $gymType = $parentLink
                                ? 'branch'
                                : ($linkedBranchCount > 0 ? 'hub' : 'independent');
                            $gymTypeLabel = match ($gymType) {
                                'branch' => 'Sucursal',
                                'hub' => 'Casa matriz',
                                default => 'Independiente',
                            };
                            $gymTypeBadgeClass = match ($gymType) {
                                'branch' => 'ui-badge-info',
                                'hub' => 'ui-badge-success',
                                default => 'ui-badge-muted',
                            };
                            $gymRelationLabel = match ($gymType) {
                                'branch' => $hubGym ? 'Sucursal de '.$hubGym->name : 'Sucursal vinculada',
                                'hub' => $linkedBranchCount === 1 ? 'Administra 1 sucursal' : 'Administra '.$linkedBranchCount.' sucursales',
                                default => 'Operación individual',
                            };
                            $adminCountryIso = strtolower((string) ($adminUser?->country_iso ?? $gymRow->address_country_code ?? 'ec'));
                            $adminAddressState = (string) ($adminUser?->address_state ?? $gymRow->address_state ?? '');
                            $adminAddressCity = (string) ($adminUser?->address_city ?? $gymRow->address_city ?? '');
                            $adminAddressLine = (string) ($adminUser?->address_line ?? $gymRow->address_line ?? '');
                            $adminPhone = trim((string) (($adminUser?->phone_country_dial ?? '').' '.($adminUser?->phone_number ?? '')));
                            $locationLabel = collect([$gymRow->address_city, $gymRow->address_state])->filter()->implode(', ');
                            $genderLabel = match ((string) ($adminUser?->gender ?? '')) {
                                'male' => 'Hombre',
                                'female' => 'Mujer',
                                'other' => 'Otro',
                                'prefer_not_say' => 'Prefiere no decir',
                                default => 'No especificado',
                            };
                            $profilePieces = [
                                $gymRow->name,
                                $gymRow->slug,
                                $locationLabel,
                                $gymTypeLabel,
                                $gymRelationLabel,
                                $hubGym?->name,
                                $adminUser?->name,
                                $adminUser?->email,
                                $adminPhone,
                                $genderLabel,
                                $adminUser?->identification_number,
                            ];
                        @endphp
                        <tr
                            class="align-top"
                            data-gym-list-row
                            data-gym-type="{{ $gymType }}"
                            data-gym-search="{{ strtolower(trim(implode(' ', array_filter($profilePieces, fn ($value) => filled($value))))) }}"
                        >
                            <td>
                                <p class="font-semibold">{{ $gymRow->name }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <span class="ui-badge {{ $gymTypeBadgeClass }} normal-case tracking-normal">
                                        {{ $gymTypeLabel }}
                                    </span>
                                    <span class="ui-badge ui-badge-muted normal-case tracking-normal">
                                        /{{ $gymRow->slug }}/panel
                                    </span>
                                    @if ($locationLabel !== '')
                                        <span class="ui-badge ui-badge-info normal-case tracking-normal">
                                            {{ $locationLabel }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    {{ $gymRelationLabel }}
                                </p>
                            </td>
                            <td>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $adminUser?->name ?? 'Sin usuario asignado' }}</p>
                                <p class="mt-1 text-xs ui-muted">{{ $adminUser?->email ?? 'Sin correo' }}</p>
                                <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">{{ $adminPhone !== '' ? $adminPhone : 'Sin teléfono registrado' }}</p>
                            </td>
                            <td>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="ui-badge ui-badge-muted normal-case tracking-normal">
                                        {{ $genderLabel }}
                                    </span>
                                    <span class="ui-badge {{ filled($adminUser?->identification_number) ? 'ui-badge-info' : 'ui-badge-muted' }} normal-case tracking-normal">
                                        {{ filled($adminUser?->identification_number) ? $adminUser->identification_number : 'Sin ID' }}
                                    </span>
                                </div>
                            </td>
                            <td class="w-[280px]">
                                <div class="space-y-3">
                                    <div class="sa-action-row">
                                    @if ($adminUser)
                                        <button
                                            type="button"
                                            class="ui-button ui-button-primary justify-center shadow-sm"
                                            data-edit-admin
                                            data-gym-id="{{ (int) $gymRow->id }}"
                                            data-user-id="{{ (int) $adminUser->id }}"
                                            data-admin-name="{{ (string) $adminUser->name }}"
                                            data-admin-email="{{ (string) $adminUser->email }}"
                                            data-admin-gender="{{ (string) ($adminUser->gender ?? '') }}"
                                            data-admin-birth-date="{{ optional($adminUser->birth_date)->format('Y-m-d') }}"
                                            data-admin-identification-type="{{ (string) ($adminUser->identification_type ?? '') }}"
                                            data-admin-identification-number="{{ (string) ($adminUser->identification_number ?? '') }}"
                                            data-admin-country-iso="{{ $adminCountryIso }}"
                                            data-admin-address-state="{{ $adminAddressState }}"
                                            data-admin-address-city="{{ $adminAddressCity }}"
                                            data-admin-address-line="{{ $adminAddressLine }}"
                                            data-admin-phone-country-dial="{{ (string) ($adminUser->phone_country_dial ?? '+593') }}"
                                            data-admin-phone-number="{{ (string) ($adminUser->phone_number ?? '') }}"
                                        >
                                            Editar usuario
                                        </button>
                                        <button
                                            type="button"
                                            class="ui-button ui-button-muted justify-center"
                                            data-reset-admin-password
                                            data-gym-id="{{ (int) $gymRow->id }}"
                                            data-user-id="{{ (int) $adminUser->id }}"
                                            data-admin-name="{{ (string) $adminUser->name }}"
                                        >
                                            Restablecer contraseña
                                        </button>
                                    @endif
                                    </div>

                                    <details class="sa-disclosure">
                                        <summary>
                                            <span>Zona peligrosa</span>
                                            <svg class="h-4 w-4 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                            </svg>
                                        </summary>
                                        <div class="space-y-3 p-4">
                                            <p class="sa-inline-note">
                                                Elimina el gimnasio solo si confirmaste que no necesitas conservar clientes, caja, reportes ni historial.
                                            </p>
                                            <form method="POST"
                                                  action="{{ route('superadmin.gyms.destroy', $gymRow->id) }}"
                                                  onsubmit="return confirm('Esta acción eliminara el gimnasio y todos sus datos. Deseas continuar?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" size="sm" variant="danger" class="w-full justify-center">
                                                    Eliminar gimnasio
                                                </x-ui.button>
                                            </form>
                                        </div>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="sa-empty-row">No hay gimnasios registrados.</td>
                        </tr>
                    @endforelse
                    @if ($gymsWithAdmins->isNotEmpty())
                        <tr id="gym-list-empty" class="hidden">
                            <td colspan="4" class="sa-empty-row">
                                No se encontraron gimnasios con ese criterio.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            <div id="admin-edit-modal" class="sa-modal-shell" aria-hidden="true">
                <div class="sa-modal-backdrop" data-admin-modal-close></div>
                <div class="sa-modal-panel max-h-[calc(100dvh-1rem)]" role="dialog" aria-modal="true" aria-labelledby="admin-edit-modal-title" aria-describedby="admin-edit-modal-description" tabindex="-1">
                    <div class="sa-modal-header">
                        <div>
                            <h3 id="admin-edit-modal-title">Editar usuario del gimnasio</h3>
                            <p id="admin-edit-modal-description" class="sa-modal-copy">
                                Actualiza perfil, ubicación y contacto del admin principal sin salir del listado.
                            </p>
                        </div>
                        <button type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold" data-admin-modal-close>Cerrar</button>
                    </div>

                    <form id="admin-edit-form" method="POST" action="#" enctype="multipart/form-data" class="sa-modal-body">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="modal-admin-user-id" name="admin_user_id" value="{{ (int) old('admin_user_id', 0) }}">
                        <input type="hidden" id="modal-admin-gym-id" name="admin_gym_id" value="{{ $adminEditOldGymId > 0 ? $adminEditOldGymId : 0 }}">

                        <section class="sa-modal-section">
                            <div class="sa-modal-section-header">
                                <p class="sa-modal-section-title">Perfil base</p>
                                <p class="sa-modal-section-copy">Lo esencial para identificar al admin y validar sus datos personales.</p>
                            </div>
                            <div class="sa-modal-grid md:grid-cols-3">
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Nombre
                                    <input id="modal-admin-name" type="text" name="admin_name" class="ui-input" value="{{ old('admin_name') }}" required>
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-2">
                                    Correo
                                    <input id="modal-admin-email" type="email" name="admin_email" class="ui-input" value="{{ old('admin_email') }}" required>
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Género
                                    <select id="modal-admin-gender" name="admin_gender" class="ui-input">
                                        <option value="">No especificado</option>
                                        <option value="male">Hombre</option>
                                        <option value="female">Mujer</option>
                                        <option value="other">Otro</option>
                                        <option value="prefer_not_say">Prefiero no decir</option>
                                    </select>
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Nacimiento
                                    <input id="modal-admin-birth-date" type="date" name="admin_birth_date" class="ui-input" value="{{ old('admin_birth_date') }}">
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Tipo de identificación
                                    <select id="modal-admin-identification-type" name="admin_identification_type" class="ui-input">
                                        <option value="">No especificado</option>
                                        <option value="cédula">Cédula</option>
                                        <option value="dni">DNI</option>
                                        <option value="passport">Pasaporte</option>
                                    </select>
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-3">
                                    Número identificación
                                    <input id="modal-admin-identification-number" type="text" name="admin_identification_number" class="ui-input" value="{{ old('admin_identification_number') }}" placeholder="Ej: 1726309071">
                                </label>
                            </div>
                        </section>

                        <section class="sa-modal-section">
                            <div class="sa-modal-section-header">
                                <p class="sa-modal-section-title">Ubicación</p>
                                <p class="sa-modal-section-copy">Asegura país, provincia y ciudad para no dejar el perfil incompleto.</p>
                            </div>
                            <div class="sa-modal-grid md:grid-cols-3">
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    País
                                    <select id="modal-admin-country-iso" name="admin_country_iso" class="ui-input">
                                        <option value="">No especificado</option>
                                        @foreach ($locationCatalog as $countryCode => $countryMeta)
                                            <option value="{{ $countryCode }}">{{ $countryMeta['label'] }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Provincia / estado
                                    <select id="modal-admin-address-state" name="admin_address_state" class="ui-input">
                                        <option value="">Selecciona provincia/estado</option>
                                    </select>
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Ciudad
                                    <select id="modal-admin-address-city" name="admin_address_city" class="ui-input">
                                        <option value="">Selecciona ciudad</option>
                                    </select>
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-3">
                                    Dirección (línea)
                                    <input id="modal-admin-address-line" type="text" name="admin_address_line" class="ui-input" value="{{ old('admin_address_line') }}" placeholder="Barrio, avenida, referencia">
                                </label>
                            </div>
                        </section>

                        <section class="sa-modal-section">
                            <div class="sa-modal-section-header">
                                <p class="sa-modal-section-title">Contacto y foto</p>
                                <p class="sa-modal-section-copy">Completa el teléfono y sube imagen solo si realmente vas a reemplazar la actual.</p>
                            </div>
                            <div class="sa-modal-grid md:grid-cols-3">
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Código de teléfono
                                    <input id="modal-admin-phone-country-dial" type="text" name="admin_phone_country_dial" class="ui-input" value="{{ old('admin_phone_country_dial') }}" placeholder="+593">
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-2">
                                    Teléfono
                                    <input id="modal-admin-phone-number" type="text" name="admin_phone_number" class="ui-input" value="{{ old('admin_phone_number') }}" placeholder="0991234567">
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-3">
                                    Foto de perfil (opcional)
                                    <input type="file" name="admin_profile_photo" class="ui-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    <p class="sa-form-note">JPG/PNG/WEBP, máximo 15MB.</p>
                                </label>
                            </div>
                        </section>

                        @if ($adminEditHasErrors)
                            <div class="sa-modal-error">
                                @foreach ($errors->getMessages() as $errorKey => $errorMessages)
                                    @if (in_array($errorKey, $adminEditErrorKeys, true))
                                        @foreach ($errorMessages as $errorMessage)
                                            <p>{{ $errorMessage }}</p>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="sa-modal-actions">
                            <button type="button" class="ui-button ui-button-ghost" data-admin-modal-close>Cancelar</button>
                            <x-ui.button type="submit">Guardar cambios</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="admin-password-modal" class="sa-modal-shell" aria-hidden="true">
                <div class="sa-modal-backdrop" data-admin-password-modal-close></div>
                <div class="sa-modal-panel max-w-lg" role="dialog" aria-modal="true" aria-labelledby="admin-password-modal-title" aria-describedby="admin-password-modal-label" tabindex="-1">
                    <div class="sa-modal-header">
                        <div>
                            <h3 id="admin-password-modal-title">Restablecer contraseña</h3>
                            <p id="admin-password-modal-label" class="sa-modal-copy">
                                Define una nueva contraseña para el admin del gimnasio y entrégala solo cuando ya esté validada.
                            </p>
                        </div>
                        <button type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold" data-admin-password-modal-close>Cerrar</button>
                    </div>

                    <form id="admin-password-form" method="POST" action="#" class="sa-modal-body">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="modal-reset-password-gym-id" name="reset_password_gym_id" value="{{ (int) old('reset_password_gym_id', 0) }}">
                        <input type="hidden" id="modal-reset-password-user-id" name="reset_password_user_id" value="{{ (int) old('reset_password_user_id', 0) }}">

                        <section class="sa-modal-section">
                            <div class="sa-modal-section-header">
                                <p class="sa-modal-section-title">Nueva credencial</p>
                                <p class="sa-modal-section-copy">Usa una contraseña temporal fuerte y compártela por un canal seguro.</p>
                            </div>
                            <div class="sa-modal-grid">
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Nueva contraseña
                                    <input id="modal-reset-password" type="password" name="reset_password" class="ui-input" placeholder="Mínimo 8 caracteres" required autocomplete="new-password">
                                </label>

                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Confirmar nueva contraseña
                                    <input id="modal-reset-password-confirmation" type="password" name="reset_password_confirmation" class="ui-input" placeholder="Repite la contraseña" required autocomplete="new-password">
                                </label>
                            </div>
                        </section>

                        @if ($passwordResetHasErrors)
                            <div class="sa-modal-error">
                                @foreach ($errors->getMessages() as $errorKey => $errorMessages)
                                    @if (in_array($errorKey, $passwordResetErrorKeys, true))
                                        @foreach ($errorMessages as $errorMessage)
                                            <p>{{ $errorMessage }}</p>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="sa-modal-actions">
                            <button type="button" class="ui-button ui-button-ghost" data-admin-password-modal-close>Cancelar</button>
                            <x-ui.button type="submit">Guardar contraseña</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const locationCatalog = @json($locationCatalog);
        const gymListSearch = document.getElementById('gym-list-search');
        const gymListType = document.getElementById('gym-list-type');
        const gymListClear = document.getElementById('gym-list-clear');
        const gymListVisibleCount = document.getElementById('gym-list-visible-count');
        const gymListEmpty = document.getElementById('gym-list-empty');
        const gymListRows = Array.from(document.querySelectorAll('[data-gym-list-row]'));
        const adminEditModal = document.getElementById('admin-edit-modal');
        const adminEditForm = document.getElementById('admin-edit-form');
        const adminEditButtons = Array.from(document.querySelectorAll('[data-edit-admin]'));
        const adminModalCloseButtons = Array.from(document.querySelectorAll('[data-admin-modal-close]'));
        const adminUpdateRouteTemplate = @json($adminEditRouteTemplate);
        const adminEditOldData = @json($adminEditOldData);
        const adminPasswordModal = document.getElementById('admin-password-modal');
        const adminPasswordForm = document.getElementById('admin-password-form');
        const adminPasswordButtons = Array.from(document.querySelectorAll('[data-reset-admin-password]'));
        const adminPasswordModalCloseButtons = Array.from(document.querySelectorAll('[data-admin-password-modal-close]'));
        const adminPasswordRouteTemplate = @json($passwordResetRouteTemplate);
        const adminPasswordOldData = @json($passwordResetOldData);
        const adminPasswordModalLabel = document.getElementById('admin-password-modal-label');
        const adminEditDialog = adminEditModal ? adminEditModal.querySelector('[role="dialog"]') : null;
        const adminPasswordDialog = adminPasswordModal ? adminPasswordModal.querySelector('[role="dialog"]') : null;
        let lastFocusedAdminTrigger = null;
        let lastFocusedPasswordTrigger = null;

        const modalFields = {
            userId: document.getElementById('modal-admin-user-id'),
            gymId: document.getElementById('modal-admin-gym-id'),
            name: document.getElementById('modal-admin-name'),
            email: document.getElementById('modal-admin-email'),
            gender: document.getElementById('modal-admin-gender'),
            birthDate: document.getElementById('modal-admin-birth-date'),
            identificationType: document.getElementById('modal-admin-identification-type'),
            identificationNumber: document.getElementById('modal-admin-identification-number'),
            countryIso: document.getElementById('modal-admin-country-iso'),
            addressState: document.getElementById('modal-admin-address-state'),
            addressCity: document.getElementById('modal-admin-address-city'),
            addressLine: document.getElementById('modal-admin-address-line'),
            phoneCountryDial: document.getElementById('modal-admin-phone-country-dial'),
            phoneNumber: document.getElementById('modal-admin-phone-number'),
        };
        const passwordModalFields = {
            gymId: document.getElementById('modal-reset-password-gym-id'),
            userId: document.getElementById('modal-reset-password-user-id'),
            password: document.getElementById('modal-reset-password'),
            passwordConfirmation: document.getElementById('modal-reset-password-confirmation'),
        };

        function replaceOptions(select, items, placeholder) {
            if (!select) return;
            select.innerHTML = '';

            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = placeholder;
            select.appendChild(placeholderOption);

            items.forEach(function (item) {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                select.appendChild(option);
            });
        }

        function normalizeSearch(value) {
            return (value || '')
                .toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function applyGymListFilter() {
            if (gymListRows.length === 0) return;

            const term = normalizeSearch(gymListSearch?.value || '');
            const selectedType = String(gymListType?.value || 'all').trim().toLowerCase();
            let visible = 0;

            gymListRows.forEach(function (row) {
                const haystack = normalizeSearch(row.dataset.gymSearch || row.textContent || '');
                const rowType = String(row.dataset.gymType || 'independent').trim().toLowerCase();
                const matchesSearch = term === '' || haystack.includes(term);
                const matchesType = selectedType === 'all' || rowType === selectedType;
                const matches = matchesSearch && matchesType;
                row.classList.toggle('hidden', !matches);

                if (matches) {
                    visible += 1;
                }
            });

            if (gymListVisibleCount) {
                gymListVisibleCount.textContent = String(visible);
            }

            gymListEmpty?.classList.toggle('hidden', visible !== 0);
        }

        function setModalValue(field, value) {
            if (!field) return;
            field.value = value || '';
        }

        function statesForCountry(countryCode) {
            const code = String(countryCode || '').toLowerCase();
            const country = locationCatalog[code] || null;
            if (!country || !country.states) return [];

            return Object.keys(country.states);
        }

        function citiesForState(countryCode, stateName) {
            const code = String(countryCode || '').toLowerCase();
            const country = locationCatalog[code] || null;
            if (!country || !country.states) return [];

            return country.states[stateName] || [];
        }

        function syncAdminModalCities(countryCode, stateName, preferredCity) {
            if (!modalFields.addressCity) return;

            const cities = citiesForState(countryCode, stateName);
            replaceOptions(modalFields.addressCity, cities, 'Selecciona ciudad');

            if (preferredCity && cities.includes(preferredCity)) {
                modalFields.addressCity.value = preferredCity;
            }
        }

        function syncAdminModalStates(countryCode, preferredState, preferredCity) {
            if (!modalFields.addressState) return;

            const states = statesForCountry(countryCode);
            replaceOptions(modalFields.addressState, states, 'Selecciona provincia/estado');

            if (preferredState && states.includes(preferredState)) {
                modalFields.addressState.value = preferredState;
            }

            syncAdminModalCities(countryCode, modalFields.addressState.value, preferredCity);
        }

        function openAdminModal(data) {
            if (!adminEditModal || !adminEditForm) return;

            const gymId = String(data.gymId || '').trim();
            if (gymId === '') return;
            lastFocusedAdminTrigger = document.activeElement instanceof HTMLElement ? document.activeElement : null;

            adminEditForm.action = adminUpdateRouteTemplate.replace('__GYM__', gymId);
            setModalValue(modalFields.userId, String(data.userId || ''));
            setModalValue(modalFields.gymId, gymId);
            setModalValue(modalFields.name, data.name || '');
            setModalValue(modalFields.email, data.email || '');
            setModalValue(modalFields.gender, data.gender || '');
            setModalValue(modalFields.birthDate, data.birthDate || '');
            setModalValue(modalFields.identificationType, data.identificationType || '');
            setModalValue(modalFields.identificationNumber, data.identificationNumber || '');
            setModalValue(modalFields.countryIso, (data.countryIso || '').toLowerCase());
            syncAdminModalStates((data.countryIso || '').toLowerCase(), data.addressState || '', data.addressCity || '');
            setModalValue(modalFields.addressLine, data.addressLine || '');
            setModalValue(modalFields.phoneCountryDial, data.phoneCountryDial || '');
            setModalValue(modalFields.phoneNumber, data.phoneNumber || '');

            adminEditModal.classList.add('is-open');
            adminEditModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            window.setTimeout(function () {
                modalFields.name?.focus();
            }, 0);
        }

        function closeAdminModal() {
            if (!adminEditModal) return;
            adminEditModal.classList.remove('is-open');
            adminEditModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            if (lastFocusedAdminTrigger && typeof lastFocusedAdminTrigger.focus === 'function') {
                lastFocusedAdminTrigger.focus();
            }
        }

        function openPasswordModal(data) {
            if (!adminPasswordModal || !adminPasswordForm) return;

            const gymId = String(data.gymId || '').trim();
            const userId = String(data.userId || '').trim();
            if (gymId === '' || userId === '') return;
            lastFocusedPasswordTrigger = document.activeElement instanceof HTMLElement ? document.activeElement : null;

            adminPasswordForm.action = adminPasswordRouteTemplate.replace('__GYM__', gymId);
            setModalValue(passwordModalFields.gymId, gymId);
            setModalValue(passwordModalFields.userId, userId);
            setModalValue(passwordModalFields.password, '');
            setModalValue(passwordModalFields.passwordConfirmation, '');

            if (adminPasswordModalLabel) {
                const adminName = String(data.adminName || '').trim();
                adminPasswordModalLabel.textContent = adminName !== ''
                    ? 'Define una nueva contraseña para ' + adminName + '.'
                    : 'Define una nueva contraseña para el admin del gimnasio.';
            }

            adminPasswordModal.classList.add('is-open');
            adminPasswordModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            passwordModalFields.password?.focus();
        }

        function closePasswordModal() {
            if (!adminPasswordModal) return;
            adminPasswordModal.classList.remove('is-open');
            adminPasswordModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            if (lastFocusedPasswordTrigger && typeof lastFocusedPasswordTrigger.focus === 'function') {
                lastFocusedPasswordTrigger.focus();
            }
        }

        function trapFocus(modalRoot, dialog, event) {
            if (!modalRoot || !dialog || !modalRoot.classList.contains('is-open')) {
                return false;
            }

            const focusable = Array.from(dialog.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'))
                .filter(function (element) {
                    return element instanceof HTMLElement && !element.hasAttribute('hidden');
                });

            if (focusable.length === 0) {
                return false;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
                return true;
            }

            if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
                return true;
            }

            return false;
        }

        adminEditButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openAdminModal({
                    gymId: button.dataset.gymId || '',
                    userId: button.dataset.userId || '',
                    name: button.dataset.adminName || '',
                    email: button.dataset.adminEmail || '',
                    gender: button.dataset.adminGender || '',
                    birthDate: button.dataset.adminBirthDate || '',
                    identificationType: button.dataset.adminIdentificationType || '',
                    identificationNumber: button.dataset.adminIdentificationNumber || '',
                    countryIso: button.dataset.adminCountryIso || '',
                    addressState: button.dataset.adminAddressState || '',
                    addressCity: button.dataset.adminAddressCity || '',
                    addressLine: button.dataset.adminAddressLine || '',
                    phoneCountryDial: button.dataset.adminPhoneCountryDial || '',
                    phoneNumber: button.dataset.adminPhoneNumber || '',
                });
            });
        });

        adminModalCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeAdminModal);
        });

        adminPasswordButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openPasswordModal({
                    gymId: button.dataset.gymId || '',
                    userId: button.dataset.userId || '',
                    adminName: button.dataset.adminName || '',
                });
            });
        });

        adminPasswordModalCloseButtons.forEach(function (button) {
            button.addEventListener('click', closePasswordModal);
        });

        gymListSearch?.addEventListener('input', applyGymListFilter);
        gymListType?.addEventListener('change', applyGymListFilter);
        gymListClear?.addEventListener('click', function () {
            if (!gymListSearch) return;
            gymListSearch.value = '';
            if (gymListType) {
                gymListType.value = 'all';
            }
            applyGymListFilter();
            gymListSearch.focus();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Tab') {
                if (trapFocus(adminPasswordModal, adminPasswordDialog, event)) {
                    return;
                }
                if (trapFocus(adminEditModal, adminEditDialog, event)) {
                    return;
                }
            }

            if (event.key === 'Escape') {
                closeAdminModal();
                closePasswordModal();
            }
        });

        modalFields.countryIso?.addEventListener('change', function () {
            syncAdminModalStates(modalFields.countryIso.value, '', '');
        });
        modalFields.addressState?.addEventListener('change', function () {
            syncAdminModalCities(modalFields.countryIso?.value || '', modalFields.addressState?.value || '', '');
        });

        if (adminEditOldData.hasErrors && Number(adminEditOldData.gymId) > 0) {
            openAdminModal(adminEditOldData);
        }
        if (adminPasswordOldData.hasErrors && Number(adminPasswordOldData.gymId) > 0 && Number(adminPasswordOldData.userId) > 0) {
            openPasswordModal(adminPasswordOldData);
        }

        applyGymListFilter();
    })();
</script>
@endpush
