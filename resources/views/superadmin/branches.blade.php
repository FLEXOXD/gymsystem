@extends('layouts.panel')

@section('title', 'SuperAdmin Sucursales')
@section('page-title', 'Gestión multisucursal')
@push('styles')
<style>
    .sa-branch-pill {
        display: inline-flex;
        border-radius: 9999px;
        padding: 0.25rem 0.625rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        line-height: 1.2;
        border: 1px solid transparent;
    }

    .sa-branch-pill-plan { background: #e0f2fe; color: #0c4a6e; border-color: #bae6fd; }
    .sa-branch-pill-cash-hub { background: #fef3c7; color: #78350f; border-color: #fde68a; }
    .sa-branch-pill-cash-local { background: #dcfce7; color: #14532d; border-color: #bbf7d0; }
    .sa-branch-pill-status-active { background: #dcfce7; color: #14532d; border-color: #86efac; }
    .sa-branch-pill-status-other { background: #e2e8f0; color: #0f172a; border-color: #cbd5e1; }

    .theme-dark .sa-branch-pill-plan { background: #0c4a6e; color: #e0f2fe; border-color: #0369a1; }
    .theme-dark .sa-branch-pill-cash-hub { background: #78350f; color: #fef3c7; border-color: #92400e; }
    .theme-dark .sa-branch-pill-cash-local { background: #14532d; color: #dcfce7; border-color: #166534; }
    .theme-dark .sa-branch-pill-status-active { background: #166534; color: #dcfce7; border-color: #15803d; }
    .theme-dark .sa-branch-pill-status-other { background: #334155; color: #f8fafc; border-color: #475569; }
</style>
@endpush

@section('content')
    @php
        $links = $links ?? collect();
        $hubGyms = $hubGyms ?? collect();
        $kpis = $kpis ?? [];
        $locationCatalog = $locationCatalog ?? [];
        $branchPlanOptions = $branchPlanOptions ?? [];
        $hubGymAdminDomains = is_array($hubGymAdminDomains ?? null) ? $hubGymAdminDomains : [];
        $defaultBranchCountry = old('branch_country', $defaultBranchCountry ?? 'ec');
        $defaultBranchState = old('branch_state', '');
        $defaultBranchCity = old('branch_city', '');
        $statesForCountry = $locationCatalog[$defaultBranchCountry]['states'] ?? [];
        $citiesForState = $statesForCountry[$defaultBranchState] ?? [];

        $formatAddress = static function ($gym): string {
            if (! $gym) {
                return '-';
            }

            $parts = collect([
                trim((string) ($gym->address_line ?? '')),
                trim((string) ($gym->address_city ?? '')),
                trim((string) ($gym->address_state ?? '')),
            ])->filter(static fn (string $value): bool => $value !== '');

            if ($parts->isNotEmpty()) {
                return (string) $parts->implode(', ');
            }

            $fallback = trim((string) ($gym->address ?? ''));

            return $fallback !== '' ? $fallback : '-';
        };
    @endphp

    <div class="space-y-4">
        <x-ui.card title="Resumen de enlaces" subtitle="Gestión centralizada de sedes para clientes con plan sucursales.">
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900/60">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Enlaces activos</p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ (int) ($kpis['total_links'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 dark:border-cyan-700/60 dark:bg-cyan-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-cyan-700 dark:text-cyan-300">Sedes principales</p>
                    <p class="mt-1 text-2xl font-black text-cyan-700 dark:text-cyan-200">{{ (int) ($kpis['total_hubs'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-700/60 dark:bg-indigo-900/20">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">Sucursales vinculadas</p>
                    <p class="mt-1 text-2xl font-black text-indigo-700 dark:text-indigo-200">{{ (int) ($kpis['total_branches'] ?? 0) }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Crear nueva sucursal" subtitle="Crea sede aislada con usuario propio y vínculo directo a una sede principal multisucursal.">
            <form method="POST" action="{{ route('superadmin.branches.store') }}" class="space-y-4">
                @csrf

                <div class="grid gap-3 lg:grid-cols-2">
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Sede principal (plan sucursales)
                        <select name="hub_gym_id" class="ui-input" required>
                            <option value="">Selecciona sede principal</option>
                            @foreach ($hubGyms as $hubGym)
                                <option value="{{ (int) $hubGym->id }}" @selected((int) old('hub_gym_id') === (int) $hubGym->id)>
                                    {{ (string) $hubGym->name }} | {{ (string) $hubGym->slug }}
                                </option>
                            @endforeach
                        </select>
                        @error('hub_gym_id')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Plan operativo de sucursal
                        <select name="branch_plan_key" class="ui-input" required>
                            <option value="">Selecciona plan</option>
                            @foreach ($branchPlanOptions as $planKey => $planLabel)
                                <option value="{{ $planKey }}" @selected((string) old('branch_plan_key') === (string) $planKey)>
                                    {{ $planLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_plan_key')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="grid gap-3 lg:grid-cols-2">
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Nombre de la sucursal
                        <input type="text" id="branch-name" name="branch_name" value="{{ old('branch_name') }}" class="ui-input" placeholder="Ej: Sucursal Norte" required>
                        @error('branch_name')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Teléfono de sucursal
                        <input type="text" name="branch_phone" value="{{ old('branch_phone') }}" class="ui-input" placeholder="Opcional">
                        @error('branch_phone')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="grid gap-3 lg:grid-cols-4">
                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        País
                        <select id="branch-country" name="branch_country" class="ui-input" required>
                            @foreach ($locationCatalog as $countryCode => $countryMeta)
                                <option value="{{ $countryCode }}" @selected($defaultBranchCountry === $countryCode)>
                                    {{ $countryMeta['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_country')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Provincia / estado
                        <select id="branch-state" name="branch_state" class="ui-input" required>
                            <option value="">Selecciona provincia/estado</option>
                            @foreach (array_keys($statesForCountry) as $stateName)
                                <option value="{{ $stateName }}" @selected($defaultBranchState === $stateName)>{{ $stateName }}</option>
                            @endforeach
                        </select>
                        @error('branch_state')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Ciudad
                        <select id="branch-city" name="branch_city" class="ui-input" required>
                            <option value="">Selecciona ciudad</option>
                            @foreach ($citiesForState as $cityName)
                                <option value="{{ $cityName }}" @selected($defaultBranchCity === $cityName)>{{ $cityName }}</option>
                            @endforeach
                        </select>
                        @error('branch_city')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        Dirección (línea)
                        <input type="text" name="branch_address_line" value="{{ old('branch_address_line') }}" class="ui-input" placeholder="Barrio, avenida, referencia">
                        @error('branch_address_line')
                            <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-900/50">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300">Usuario administrador de la sucursal</p>
                    <div class="mt-3 grid gap-3 lg:grid-cols-2">
                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Nombre de usuario
                            <input type="text" name="branch_admin_name" value="{{ old('branch_admin_name') }}" class="ui-input" placeholder="Ej: Admin Norte" required>
                            @error('branch_admin_name')
                                <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Correo login (autogenerado)
                            <input type="text" id="branch-admin-email-preview" value="{{ old('branch_admin_email') }}" class="ui-input" readonly>
                            <input type="hidden" id="branch-admin-email" name="branch_admin_email" value="{{ old('branch_admin_email') }}">
                            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Se genera con nombre del gimnasio + nombre de sucursal.</span>
                            @error('branch_admin_email')
                                <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Contraseña
                            <input type="password" name="branch_admin_password" class="ui-input" placeholder="Mínimo 8 caracteres" required>
                            @error('branch_admin_password')
                                <span class="text-[11px] font-semibold text-rose-500">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                            Confirmar contraseña
                            <input type="password" name="branch_admin_password_confirmation" class="ui-input" placeholder="Repite la contraseña" required>
                        </label>
                    </div>
                </div>

                <p class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-900 dark:border-amber-500/40 dark:bg-amber-900/20 dark:text-amber-100">
                    La caja de cada sucursal secundaria queda gestionada por el Admin global (sede principal).
                </p>

                <div class="flex justify-end">
                    <x-ui.button type="submit" class="min-w-[220px]">Crear sucursal y vincular</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="Vínculos actuales" subtitle="Sucursales aisladas por sede. La gestión se define desde Admin global y SuperAdmin.">
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[1160px]">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            <th class="px-3 py-3">Sede principal</th>
                            <th class="px-3 py-3">Sucursal</th>
                            <th class="px-3 py-3">Plan sucursal</th>
                            <th class="px-3 py-3">Caja</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Creado por</th>
                            <th class="px-3 py-3">Fecha</th>
                            <th class="px-3 py-3">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($links as $link)
                            <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ (string) ($link->hubGym?->name ?? '-') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300">{{ (string) ($link->hubGym?->slug ?? '-') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300">{{ $formatAddress($link->hubGym) }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-slate-100">{{ (string) ($link->branchGym?->name ?? '-') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300">{{ (string) ($link->branchGym?->slug ?? '-') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300">{{ $formatAddress($link->branchGym) }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    @php
                                        $branchPlanKey = (string) ($link->branch_plan_key ?? 'basico');
                                        $branchPlanLabel = match (strtolower($branchPlanKey)) {
                                            'basico' => 'Basico',
                                            'profesional' => 'Profesional',
                                            'premium' => 'Premium',
                                            default => ucfirst($branchPlanKey),
                                        };
                                    @endphp
                                    <span class="sa-branch-pill sa-branch-pill-plan">
                                        {{ $branchPlanLabel }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="sa-branch-pill {{ (bool) ($link->cash_managed_by_hub ?? true) ? 'sa-branch-pill-cash-hub' : 'sa-branch-pill-cash-local' }}">
                                        {{ (bool) ($link->cash_managed_by_hub ?? true) ? 'Control hub' : 'Caja local' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="sa-branch-pill {{ (string) ($link->status ?? 'active') === 'active' ? 'sa-branch-pill-status-active' : 'sa-branch-pill-status-other' }}">
                                        {{ (string) ($link->status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-slate-800 dark:text-slate-100">{{ (string) ($link->createdBy?->name ?? 'Sin usuario') }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300">{{ (string) ($link->createdBy?->email ?? '-') }}</p>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-200">
                                    {{ optional($link->created_at)->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-3 py-3">
                                    <form method="POST" action="{{ route('superadmin.branches.destroy', (int) $link->id) }}" onsubmit="return confirm('¿Desvincular esta sucursal?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="danger">Desvincular</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                                    Aún no hay vínculos configurados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (method_exists($links, 'links'))
                <div class="mt-4">
                    {{ $links->onEachSide(1)->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const locationCatalog = @json($locationCatalog);
        const hubDomains = @json($hubGymAdminDomains);
        const countrySelect = document.getElementById('branch-country');
        const stateSelect = document.getElementById('branch-state');
        const citySelect = document.getElementById('branch-city');
        const hubSelect = document.querySelector('select[name="hub_gym_id"]');
        const branchNameInput = document.getElementById('branch-name');
        const branchEmailInput = document.getElementById('branch-admin-email');
        const branchEmailPreview = document.getElementById('branch-admin-email-preview');

        const selectedState = @json($defaultBranchState);
        const selectedCity = @json($defaultBranchCity);

        function sanitizeToken(value, fallback) {
            const raw = String(value || '').trim();
            const normalized = raw.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            const slug = normalized.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            const compact = slug.replace(/-/g, '');
            return compact !== '' ? compact : fallback;
        }

        function resolveHubName() {
            if (!hubSelect) return '';
            const option = hubSelect.options[hubSelect.selectedIndex];
            if (!option) return '';
            const label = String(option.textContent || '');
            return label.split('|')[0]?.trim() || '';
        }

        function resolveDomain() {
            if (!hubSelect) return 'gymsystem.app';
            const gymId = String(hubSelect.value || '').trim();
            if (gymId === '') return 'gymsystem.app';
            const fromMap = String(hubDomains[gymId] || '').trim().toLowerCase();
            return fromMap !== '' ? fromMap : 'gymsystem.app';
        }

        function syncBranchLoginEmail() {
            if (!branchEmailInput || !branchEmailPreview) return;
            const gymToken = sanitizeToken(resolveHubName(), 'gym');
            const branchToken = sanitizeToken(branchNameInput?.value || '', 'sucursal');
            const domain = resolveDomain();
            const email = `${gymToken}.${branchToken}@${domain}`;
            branchEmailInput.value = email;
            branchEmailPreview.value = email;
        }

        function replaceOptions(select, values, placeholder) {
            if (!select) return;
            select.innerHTML = '';

            const first = document.createElement('option');
            first.value = '';
            first.textContent = placeholder;
            select.appendChild(first);

            values.forEach(function (value) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });
        }

        function statesForCountry(countryCode) {
            const country = locationCatalog[String(countryCode || '').toLowerCase()] || null;
            if (!country || !country.states) return [];
            return Object.keys(country.states);
        }

        function citiesForState(countryCode, stateName) {
            const country = locationCatalog[String(countryCode || '').toLowerCase()] || null;
            if (!country || !country.states || !country.states[stateName]) return [];
            return country.states[stateName];
        }

        function syncStates(preferredState, preferredCity) {
            if (!countrySelect || !stateSelect) return;
            const states = statesForCountry(countrySelect.value);
            replaceOptions(stateSelect, states, 'Selecciona provincia/estado');

            if (preferredState && states.includes(preferredState)) {
                stateSelect.value = preferredState;
            }

            syncCities(preferredCity);
        }

        function syncCities(preferredCity) {
            if (!countrySelect || !stateSelect || !citySelect) return;
            const cities = citiesForState(countrySelect.value, stateSelect.value);
            replaceOptions(citySelect, cities, 'Selecciona ciudad');

            if (preferredCity && cities.includes(preferredCity)) {
                citySelect.value = preferredCity;
            }
        }

        countrySelect?.addEventListener('change', function () {
            syncStates('', '');
        });

        stateSelect?.addEventListener('change', function () {
            syncCities('');
        });

        hubSelect?.addEventListener('change', syncBranchLoginEmail);
        branchNameInput?.addEventListener('input', syncBranchLoginEmail);

        syncStates(selectedState, selectedCity);
        syncBranchLoginEmail();
    })();
</script>
@endpush
