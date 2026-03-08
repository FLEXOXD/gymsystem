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
    @endphp

    <div class="space-y-5">
        <x-ui.card title="Listado de gimnasios" subtitle="Edita datos del usuario administrador por gimnasio o elimina el gimnasio completo.">
            <p class="mb-3 text-xs font-semibold text-rose-700 dark:text-rose-300">
                Eliminar gimnasio borrara todo: clientes, planes, membresias, caja, reportes y usuario del gimnasio.
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
                    <span class="ui-badge ui-badge-info">
                        Visibles: <strong id="gym-list-visible-count" class="ml-1">{{ $gymsWithAdmins->count() }}</strong>
                    </span>
                    <button id="gym-list-clear" type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold">
                        Limpiar
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto smart-list-wrap">
                <table class="ui-table min-w-[1040px]" data-smart-list-manual>
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
                                default => 'Operacion individual',
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
                                <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">{{ $adminPhone !== '' ? $adminPhone : 'Sin telefono registrado' }}</p>
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
                                <div class="grid gap-2 sm:grid-cols-2">
                                    @if ($adminUser)
                                        <button
                                            type="button"
                                            class="ui-button ui-button-primary w-full justify-center px-3 py-2 text-xs font-bold shadow-sm"
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
                                            class="ui-button ui-button-muted w-full justify-center px-3 py-2 text-xs font-bold"
                                            data-reset-admin-password
                                            data-gym-id="{{ (int) $gymRow->id }}"
                                            data-user-id="{{ (int) $adminUser->id }}"
                                            data-admin-name="{{ (string) $adminUser->name }}"
                                        >
                                            Restablecer clave
                                        </button>
                                    @endif

                                    <form method="POST"
                                          action="{{ route('superadmin.gyms.destroy', $gymRow->id) }}"
                                          onsubmit="return confirm('Se eliminara el gimnasio y todos sus datos. Deseas continuar?');"
                                          class="sm:col-span-2">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="danger" class="w-full justify-center">
                                            Eliminar gimnasio
                                        </x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-sm text-slate-500">No hay gimnasios registrados.</td>
                        </tr>
                    @endforelse
                    @if ($gymsWithAdmins->isNotEmpty())
                        <tr id="gym-list-empty" class="hidden">
                            <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                No se encontraron gimnasios con ese criterio.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            <div id="admin-edit-modal" class="fixed inset-0 z-[80] hidden items-center justify-center overflow-y-auto p-4">
                <div class="absolute inset-0 bg-black/60" data-admin-modal-close></div>
                <div class="relative z-[81] flex max-h-[calc(100dvh-1rem)] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4 shadow-2xl">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="ui-heading text-lg">Editar usuario del gimnasio</h3>
                        <button type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold" data-admin-modal-close>Cerrar</button>
                    </div>

                    <form id="admin-edit-form" method="POST" action="#" enctype="multipart/form-data" class="grid gap-3 overflow-y-auto pr-1 md:grid-cols-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="modal-admin-user-id" name="admin_user_id" value="{{ (int) old('admin_user_id', 0) }}">
                        <input type="hidden" id="modal-admin-gym-id" name="admin_gym_id" value="{{ $adminEditOldGymId > 0 ? $adminEditOldGymId : 0 }}">

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Nombre
                            <input id="modal-admin-name" type="text" name="admin_name" class="ui-input" value="{{ old('admin_name') }}" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-2">
                            Correo
                            <input id="modal-admin-email" type="email" name="admin_email" class="ui-input" value="{{ old('admin_email') }}" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Genero
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
                            Tipo de identificacion
                            <select id="modal-admin-identification-type" name="admin_identification_type" class="ui-input">
                                <option value="">No especificado</option>
                                <option value="cedula">Cedula</option>
                                <option value="dni">DNI</option>
                                <option value="passport">Pasaporte</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Numero identificacion
                            <input id="modal-admin-identification-number" type="text" name="admin_identification_number" class="ui-input" value="{{ old('admin_identification_number') }}" placeholder="Ej: 1726309071">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Pais
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
                            Direccion (linea)
                            <input id="modal-admin-address-line" type="text" name="admin_address_line" class="ui-input" value="{{ old('admin_address_line') }}" placeholder="Barrio, avenida, referencia">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Codigo de telefono
                            <input id="modal-admin-phone-country-dial" type="text" name="admin_phone_country_dial" class="ui-input" value="{{ old('admin_phone_country_dial') }}" placeholder="+593">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Telefono
                            <input id="modal-admin-phone-number" type="text" name="admin_phone_number" class="ui-input" value="{{ old('admin_phone_number') }}" placeholder="0991234567">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-3">
                            Foto de perfil (opcional)
                            <input type="file" name="admin_profile_photo" class="ui-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <p class="ui-muted text-[11px]">JPG/PNG/WEBP, maximo 15MB.</p>
                        </label>

                        @if ($adminEditHasErrors)
                            <div class="rounded-xl border border-rose-300/60 bg-rose-100/60 px-3 py-2 text-xs font-semibold text-rose-800 dark:border-rose-300/40 dark:bg-rose-300/10 dark:text-rose-200 md:col-span-3">
                                @foreach ($errors->getMessages() as $errorKey => $errorMessages)
                                    @if (in_array($errorKey, $adminEditErrorKeys, true))
                                        @foreach ($errorMessages as $errorMessage)
                                            <p>{{ $errorMessage }}</p>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="md:col-span-3 flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-admin-modal-close>Cancelar</button>
                            <x-ui.button type="submit">Guardar cambios</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="admin-password-modal" class="fixed inset-0 z-[82] hidden items-center justify-center overflow-y-auto p-4">
                <div class="absolute inset-0 bg-black/60" data-admin-password-modal-close></div>
                <div class="relative z-[83] w-full max-w-lg rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4 shadow-2xl">
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <h3 class="ui-heading text-lg">Restablecer contrasena</h3>
                        <button type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold" data-admin-password-modal-close>Cerrar</button>
                    </div>

                    <p id="admin-password-modal-label" class="ui-muted text-sm">
                        Define una nueva contrasena para el admin del gimnasio.
                    </p>

                    <form id="admin-password-form" method="POST" action="#" class="mt-3 space-y-3">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="modal-reset-password-gym-id" name="reset_password_gym_id" value="{{ (int) old('reset_password_gym_id', 0) }}">
                        <input type="hidden" id="modal-reset-password-user-id" name="reset_password_user_id" value="{{ (int) old('reset_password_user_id', 0) }}">

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Nueva contrasena
                            <input id="modal-reset-password" type="password" name="reset_password" class="ui-input" placeholder="Minimo 8 caracteres" required autocomplete="new-password">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Confirmar nueva contrasena
                            <input id="modal-reset-password-confirmation" type="password" name="reset_password_confirmation" class="ui-input" placeholder="Repite la contrasena" required autocomplete="new-password">
                        </label>

                        @if ($passwordResetHasErrors)
                            <div class="rounded-xl border border-rose-300/60 bg-rose-100/60 px-3 py-2 text-xs font-semibold text-rose-800 dark:border-rose-300/40 dark:bg-rose-300/10 dark:text-rose-200">
                                @foreach ($errors->getMessages() as $errorKey => $errorMessages)
                                    @if (in_array($errorKey, $passwordResetErrorKeys, true))
                                        @foreach ($errorMessages as $errorMessage)
                                            <p>{{ $errorMessage }}</p>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-admin-password-modal-close>Cancelar</button>
                            <x-ui.button type="submit">Guardar contrasena</x-ui.button>
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

            adminEditModal.classList.remove('hidden');
            adminEditModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeAdminModal() {
            if (!adminEditModal) return;
            adminEditModal.classList.add('hidden');
            adminEditModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function openPasswordModal(data) {
            if (!adminPasswordModal || !adminPasswordForm) return;

            const gymId = String(data.gymId || '').trim();
            const userId = String(data.userId || '').trim();
            if (gymId === '' || userId === '') return;

            adminPasswordForm.action = adminPasswordRouteTemplate.replace('__GYM__', gymId);
            setModalValue(passwordModalFields.gymId, gymId);
            setModalValue(passwordModalFields.userId, userId);
            setModalValue(passwordModalFields.password, '');
            setModalValue(passwordModalFields.passwordConfirmation, '');

            if (adminPasswordModalLabel) {
                const adminName = String(data.adminName || '').trim();
                adminPasswordModalLabel.textContent = adminName !== ''
                    ? 'Define una nueva contrasena para ' + adminName + '.'
                    : 'Define una nueva contrasena para el admin del gimnasio.';
            }

            adminPasswordModal.classList.remove('hidden');
            adminPasswordModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            passwordModalFields.password?.focus();
        }

        function closePasswordModal() {
            if (!adminPasswordModal) return;
            adminPasswordModal.classList.add('hidden');
            adminPasswordModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
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
