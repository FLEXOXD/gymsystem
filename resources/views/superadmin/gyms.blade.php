@extends('layouts.panel')

@section('title', 'SuperAdmin Gimnasios')
@section('page-title', 'Gimnasios y Suscripciones')

@push('styles')
<style>
    .theme-dark [data-branches-toggle] {
        background-color: rgba(15, 23, 42, 0.92);
        border-color: rgba(148, 163, 184, 0.45);
        color: rgb(226 232 240);
    }

    .theme-dark [data-branches-toggle]:hover {
        background-color: rgba(30, 41, 59, 0.98);
        border-color: rgba(56, 189, 248, 0.55);
        color: rgb(248 250 252);
    }

    .theme-dark [data-branches-toggle][aria-expanded='true'] {
        background-color: rgba(8, 47, 73, 0.65);
        border-color: rgba(34, 211, 238, 0.55);
        color: rgb(165 243 252);
    }

    .theme-dark [data-branches-toggle][aria-expanded='true']:hover {
        background-color: rgba(12, 74, 110, 0.75);
    }
</style>
@endpush

@section('content')
    @php
        $today = \Carbon\Carbon::today();
        $expiringLimit = $today->copy()->addDays(7);
        $totalGyms = $gyms->count();
        $activeGyms = $gyms->where('status', 'active')->count();
        $graceGyms = $gyms->where('status', 'grace')->count();
        $suspendedGyms = $gyms->where('status', 'suspended')->count();
        $branchManagedGyms = $gyms->filter(fn ($row) => (bool) ($row->is_branch_managed ?? false))->count();
        $expiringSoon = $gyms->filter(function ($row) use ($today, $expiringLimit) {
            if (($row->status ?? null) === 'suspended' || empty($row->ends_at)) {
                return false;
            }

            $endDate = \Carbon\Carbon::parse($row->ends_at);

            return $endDate->betweenIncluded($today, $expiringLimit);
        })->count();
        $estimatedMrr = $gyms->filter(function ($row) {
            return in_array((string) ($row->status ?? ''), ['active', 'grace'], true)
                && ! (bool) ($row->is_branch_managed ?? false);
        })->sum(function ($row) {
            return (float) ($row->price ?? 0);
        });
    @endphp

    <x-ui.card title="Gestión de gimnasios" subtitle="Vista operativa de suscripciones, renovaciones y riesgo de vencimiento.">
        @if ($errors->has('subscription'))
            <div class="mb-4 rounded-xl border border-amber-300/70 bg-amber-100/70 px-3 py-2 text-sm font-semibold text-amber-900 dark:border-amber-300/40 dark:bg-amber-900/25 dark:text-amber-200">
                {{ $errors->first('subscription') }}
            </div>
        @endif
        @if ($errors->has('custom_price'))
            <div class="mb-4 rounded-xl border border-amber-300/70 bg-amber-100/70 px-3 py-2 text-sm font-semibold text-amber-900 dark:border-amber-300/40 dark:bg-amber-900/25 dark:text-amber-200">
                {{ $errors->first('custom_price') }}
            </div>
        @endif

        <div class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900/60">
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total gimnasios</p>
                <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100">{{ $totalGyms }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-700/60 dark:bg-emerald-900/20">
                <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Activos</p>
                <p class="mt-1 text-2xl font-black text-emerald-700 dark:text-emerald-200">{{ $activeGyms }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-700/60 dark:bg-amber-900/20">
                <p class="text-[11px] font-bold uppercase tracking-wide text-amber-700 dark:text-amber-300">En gracia</p>
                <p class="mt-1 text-2xl font-black text-amber-700 dark:text-amber-200">{{ $graceGyms }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 dark:border-rose-700/60 dark:bg-rose-900/20">
                <p class="text-[11px] font-bold uppercase tracking-wide text-rose-700 dark:text-rose-300">Suspendidos</p>
                <p class="mt-1 text-2xl font-black text-rose-700 dark:text-rose-200">{{ $suspendedGyms }}</p>
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 dark:border-sky-700/60 dark:bg-sky-900/20">
                <p class="text-[11px] font-bold uppercase tracking-wide text-sky-700 dark:text-sky-300">MRR estimado</p>
                <p class="mt-1 text-xl font-black text-sky-700 dark:text-sky-200">{{ \App\Support\Currency::format((float) $estimatedMrr, $appCurrencyCode) }}</p>
                <p class="mt-1 text-[11px] font-semibold text-sky-700/80 dark:text-sky-300/80">No incluye sucursales gestionadas.</p>
            </div>
        </div>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-900/50">
            <div class="flex min-w-[230px] flex-1 flex-wrap items-center gap-3">
                <label class="flex min-w-[220px] flex-1 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <input id="gym-table-filter" type="text" class="w-full bg-transparent text-sm outline-none" placeholder="Buscar gimnasio o plan...">
                </label>
                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
                    Vencen en 7 días: {{ $expiringSoon }}
                </span>
                <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200">
                    Sucursales gestionadas: {{ $branchManagedGyms }}
                </span>
            </div>
            <x-ui.button :href="route('superadmin.gym.index')" size="sm">Crear nuevo gimnasio</x-ui.button>
        </div>

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[1180px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">Gimnasio</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Mensualidad</th>
                    <th class="px-3 py-3">Fecha fin</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Vence en</th>
                    <th class="px-3 py-3">Último pago</th>
                    <th class="px-3 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $managedBranchesByHub = $gyms
                        ->filter(fn ($row) => (bool) ($row->is_branch_managed ?? false) && (int) ($row->billing_owner_gym_id ?? 0) > 0)
                        ->groupBy(fn ($row) => (int) $row->billing_owner_gym_id);
                    $mainGyms = $gyms
                        ->reject(fn ($row) => (bool) ($row->is_branch_managed ?? false))
                        ->values();
                @endphp
                @forelse ($mainGyms as $gym)
                    @php
                        $gymName = (string) ($gym->gym_name ?? '-');
                        $planName = (string) ($gym->plan_name ?? '-');
                        $isMultiBranchPlan = str_contains(mb_strtolower($planName), 'sucursal');
                        $linkedManagedBranches = $managedBranchesByHub->get((int) $gym->gym_id, collect());
                        $linkedBranchCount = $linkedManagedBranches->count();
                        $linkedBranchSearch = $linkedManagedBranches
                            ->map(fn ($branch) => (string) (($branch->gym_name ?? '').' '.($branch->plan_name ?? '')))
                            ->implode(' ');
                        $statusClasses = [
                            'active' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                            'grace' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                            'suspended' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
                        ];
                        $badgeClass = $statusClasses[$gym->status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200';
                        $endDate = \Carbon\Carbon::parse($gym->ends_at);
                        $lastPaymentLabel = match ($gym->last_payment_method) {
                            'cash' => 'Efectivo',
                            'card' => 'Tarjeta',
                            'transfer', 'transferencia' => 'Transferencia',
                            null => 'Sin registro',
                            default => (string) $gym->last_payment_method,
                        };
                    @endphp
                    <tr data-gym-row data-gym-id="{{ (int) $gym->gym_id }}" class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-100" data-gym-search="{{ strtolower($gymName.' '.$planName.' '.$linkedBranchSearch) }}">
                            <p>{{ $gymName }}</p>
                            @if ($isMultiBranchPlan)
                                <button type="button"
                                        data-branches-toggle="{{ (int) $gym->gym_id }}"
                                        aria-expanded="false"
                                        class="mt-2 inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-slate-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-800 transition hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                                    <span>Sucursales ({{ $linkedBranchCount }})</span>
                                    <svg data-branches-caret="{{ (int) $gym->gym_id }}" class="h-3.5 w-3.5 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            @endif
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">
                            <p>{{ $planName }}</p>
                            @if ($isMultiBranchPlan)
                                <p class="mt-1 text-xs font-semibold text-cyan-700 dark:text-cyan-300">
                                    {{ $linkedBranchCount > 0 ? $linkedBranchCount.' sucursal(es) vinculadas' : 'Sin sucursales vinculadas aún' }}
                                </p>
                            @endif
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ \App\Support\Currency::format((float) $gym->price, $appCurrencyCode) }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $endDate->toDateString() }}</td>
                        <td class="px-3 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $badgeClass }}">
                                {{ match ($gym->status) { 'active' => 'Activo', 'grace' => 'Gracia', 'suspended' => 'Suspendido', default => $gym->status } }}
                            </span>
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">
                            @if ($gym->status === 'active')
                                @php $activeDays = (int) ($gym->days_left ?? 0); @endphp
                                <span class="{{ $activeDays <= 7 ? 'text-amber-700 dark:text-amber-300 font-bold' : '' }}">
                                    {{ $activeDays }} días
                                </span>
                            @elseif ($gym->status === 'grace')
                                <span class="font-semibold text-amber-700 dark:text-amber-300">
                                    {{ (int) ($gym->grace_left ?? 0) }} días de gracia
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $lastPaymentLabel }}</td>
                        <td class="px-3 py-3">
                            <div class="space-y-2">
                                <form method="POST" action="{{ route('superadmin.subscriptions.renew', $gym->gym_id) }}" class="grid gap-2 lg:grid-cols-[220px_165px_180px_140px_110px_auto] lg:items-center">
                                    @csrf
                                    <select name="plan_template_id" class="ui-input px-2 py-1.5 text-xs js-plan-template-select">
                                        <option value="">Mantener plan actual</option>
                                        @foreach (($planTemplates ?? collect()) as $template)
                                            <option value="{{ $template->id }}" data-plan-key="{{ (string) ($template->plan_key ?? '') }}">
                                                {{ $template->name }} ({{ \App\Support\PlanDuration::label($template->duration_unit, (int) $template->duration_days, $template->duration_months) }}) - {{ \App\Support\Currency::format((float) $template->price, $appCurrencyCode) }}{{ $template->discount_price !== null ? ' | Desc. '.\App\Support\Currency::format((float) $template->discount_price, $appCurrencyCode) : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number"
                                           name="custom_price"
                                           step="0.01"
                                           min="0"
                                           class="ui-input px-2 py-1.5 text-xs js-custom-price-input"
                                           placeholder="Precio sucursales"
                                           title="Disponible cuando eliges plan sucursales."
                                           disabled>
                                    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-300/80 px-2.5 py-2 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:text-slate-200">
                                        <input type="checkbox"
                                               name="apply_intro_50"
                                               value="1"
                                               class="js-intro-50-checkbox"
                                               disabled>
                                        50% primer mes
                                    </label>
                                    <select name="payment_method" class="ui-input px-2 py-1.5 text-xs" required>
                                        @foreach ($paymentMethods as $method)
                                            <option value="{{ $method }}">{{ match ($method) { 'cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia', 'transferencia' => 'Transferencia', default => $method } }}</option>
                                        @endforeach
                                    </select>
                                    <select name="months" class="ui-input px-2 py-1.5 text-xs js-months-select" required>
                                        <option value="1">1 mes</option>
                                        <option value="3">3 meses</option>
                                        <option value="6">6 meses</option>
                                        <option value="12">12 meses</option>
                                    </select>
                                    <x-ui.button type="submit" size="sm">Renovar</x-ui.button>
                                </form>

                                <form method="POST" action="{{ route('superadmin.subscriptions.suspend', $gym->gym_id) }}"
                                      onsubmit="return confirm('¿Suspender suscripción de este gimnasio?');">
                                    @csrf
                                    <x-ui.button type="submit" variant="danger" size="sm">Suspender</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @if ($isMultiBranchPlan)
                        <tr data-gym-detail-row data-parent-gym-id="{{ (int) $gym->gym_id }}" class="hidden border-b border-slate-300/70 bg-slate-100/70 dark:border-slate-700 dark:bg-slate-900/40">
                            <td colspan="8" class="px-4 py-4">
                                <div class="rounded-xl border border-slate-300 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                    <div class="mb-3 flex items-center justify-between gap-2">
                                        <h4 class="text-sm font-black uppercase tracking-wide text-slate-900 dark:text-slate-100">
                                            Sucursales vinculadas de {{ $gymName }}
                                        </h4>
                                        <span class="rounded-full bg-slate-200 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            {{ $linkedBranchCount }} total
                                        </span>
                                    </div>

                                    @if ($linkedManagedBranches->isEmpty())
                                        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            Este plan sucursales aún no tiene sedes vinculadas.
                                        </div>
                                    @else
                                        <div class="grid gap-3 lg:grid-cols-2 xl:grid-cols-3">
                                            @foreach ($linkedManagedBranches as $branch)
                                                @php
                                                    $branchStatus = (string) ($branch->status ?? '');
                                                    $branchBadgeClass = $statusClasses[$branchStatus] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200';
                                                    $branchEndsAt = \Carbon\Carbon::parse($branch->ends_at);
                                                    $branchDaysLeft = $branchStatus === 'active'
                                                        ? (int) ($branch->days_left ?? max(0, \Carbon\Carbon::today()->diffInDays($branchEndsAt, false)))
                                                        : null;
                                                @endphp
                                                <article class="rounded-xl border border-slate-300 bg-slate-50 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <p class="text-sm font-bold text-slate-900 dark:text-slate-50">{{ (string) ($branch->gym_name ?? 'Sucursal') }}</p>
                                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $branchBadgeClass }}">
                                                            {{ match ($branchStatus) { 'active' => 'Activo', 'grace' => 'Gracia', 'suspended' => 'Suspendido', default => ($branchStatus !== '' ? $branchStatus : '-') } }}
                                                        </span>
                                                    </div>
                                                    <p class="mt-1 text-xs font-semibold text-slate-700 dark:text-slate-200">{{ (string) ($branch->plan_name ?? '-') }}</p>
                                                    <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                                                        <p class="rounded-lg border border-slate-300 bg-white px-2 py-1 text-slate-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                                            <span class="font-semibold">Vence:</span> {{ $branchEndsAt->toDateString() }}
                                                        </p>
                                                        <p class="rounded-lg border border-slate-300 bg-white px-2 py-1 text-slate-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                                            <span class="font-semibold">Mensualidad:</span> {{ \App\Support\Currency::format((float) ($branch->price ?? 0), $appCurrencyCode) }}
                                                        </p>
                                                    </div>
                                                    <p class="mt-2 text-xs text-slate-600 dark:text-slate-300">
                                                        Gestionada por sede principal.
                                                        @if ($branchDaysLeft !== null)
                                                            Quedan {{ $branchDaysLeft }} días.
                                                        @endif
                                                    </p>
                                                </article>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                            No hay gimnasios registrados.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection

@push('scripts')
<script>
    (function () {
        const filterInput = document.getElementById('gym-table-filter');
        const rows = Array.from(document.querySelectorAll('tr[data-gym-row]'));
        const detailRowsByGym = new Map();
        const detailRows = Array.from(document.querySelectorAll('tr[data-gym-detail-row]'));

        detailRows.forEach(function (detailRow) {
            const gymId = String(detailRow.getAttribute('data-parent-gym-id') || '');
            if (gymId === '') return;
            if (!detailRowsByGym.has(gymId)) {
                detailRowsByGym.set(gymId, []);
            }
            detailRowsByGym.get(gymId).push(detailRow);
        });

        const setExpandedState = function (gymId, expanded) {
            const toggleButton = document.querySelector('[data-branches-toggle="' + gymId + '"]');
            const caret = document.querySelector('[data-branches-caret="' + gymId + '"]');
            const branchDetailRows = detailRowsByGym.get(String(gymId)) || [];

            if (toggleButton) {
                toggleButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }

            if (caret) {
                caret.classList.toggle('rotate-180', expanded);
            }

            branchDetailRows.forEach(function (detailRow) {
                detailRow.classList.toggle('hidden', !expanded);
            });
        };

        document.querySelectorAll('[data-branches-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const gymId = String(button.getAttribute('data-branches-toggle') || '');
                if (gymId === '') return;

                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                setExpandedState(gymId, !isExpanded);
            });
        });

        if (!filterInput) return;

        const applyFilter = function () {
            const q = String(filterInput.value || '').trim().toLowerCase();

            rows.forEach(function (row) {
                const searchCell = row.querySelector('[data-gym-search]');
                const value = searchCell ? String(searchCell.getAttribute('data-gym-search') || '') : '';
                const visible = q === '' || value.includes(q);
                const gymId = String(row.getAttribute('data-gym-id') || '');

                row.classList.toggle('hidden', !visible);

                if (!visible && gymId !== '') {
                    setExpandedState(gymId, false);
                }
            });
        };

        filterInput.addEventListener('input', applyFilter);
        applyFilter();

        document.querySelectorAll('form[action*=\"/subscriptions/\"]').forEach(function (form) {
            const planSelect = form.querySelector('.js-plan-template-select');
            const monthsSelect = form.querySelector('.js-months-select');
            const customPriceInput = form.querySelector('.js-custom-price-input');
            const introDiscountCheckbox = form.querySelector('.js-intro-50-checkbox');
            if (!planSelect || !monthsSelect) return;

            const syncMode = function () {
                const hasTemplate = String(planSelect.value || '').trim() !== '';
                monthsSelect.disabled = hasTemplate;
                monthsSelect.classList.toggle('opacity-60', hasTemplate);
                monthsSelect.classList.toggle('cursor-not-allowed', hasTemplate);
                monthsSelect.title = hasTemplate ? 'Se ignora cuando eliges un plan base.' : '';

                if (!customPriceInput) {
                    return;
                }

                const selectedOption = planSelect.options[planSelect.selectedIndex] || null;
                const selectedPlanKey = String(selectedOption?.getAttribute('data-plan-key') || '').toLowerCase();
                const canUseCustomPrice = hasTemplate && selectedPlanKey === 'sucursales';
                customPriceInput.disabled = !canUseCustomPrice;
                customPriceInput.required = false;
                customPriceInput.classList.toggle('opacity-60', !canUseCustomPrice);
                customPriceInput.classList.toggle('cursor-not-allowed', !canUseCustomPrice);
                customPriceInput.title = canUseCustomPrice
                    ? 'Precio personalizado para este cliente con plan sucursales.'
                    : 'Disponible cuando eliges plan sucursales.';
                if (introDiscountCheckbox) {
                    introDiscountCheckbox.disabled = !canUseCustomPrice;
                    introDiscountCheckbox.classList.toggle('cursor-not-allowed', !canUseCustomPrice);
                    if (!canUseCustomPrice) {
                        introDiscountCheckbox.checked = false;
                    }
                }
                if (!canUseCustomPrice) {
                    customPriceInput.value = '';
                }
            };

            planSelect.addEventListener('change', syncMode);
            syncMode();
        });
    })();
</script>
@endpush
