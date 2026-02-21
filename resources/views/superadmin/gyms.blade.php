@extends('layouts.panel')

@section('title', 'SuperAdmin Gimnasios')
@section('page-title', 'Gimnasios y Suscripciones')

@section('content')
    @php
        $today = \Carbon\Carbon::today();
        $expiringLimit = $today->copy()->addDays(7);
        $totalGyms = $gyms->count();
        $activeGyms = $gyms->where('status', 'active')->count();
        $graceGyms = $gyms->where('status', 'grace')->count();
        $suspendedGyms = $gyms->where('status', 'suspended')->count();
        $expiringSoon = $gyms->filter(function ($row) use ($today, $expiringLimit) {
            if (($row->status ?? null) === 'suspended' || empty($row->ends_at)) {
                return false;
            }

            $endDate = \Carbon\Carbon::parse($row->ends_at);

            return $endDate->betweenIncluded($today, $expiringLimit);
        })->count();
        $estimatedMrr = $gyms->filter(function ($row) {
            return in_array((string) ($row->status ?? ''), ['active', 'grace'], true);
        })->sum(function ($row) {
            return (float) ($row->price ?? 0);
        });
    @endphp

    <x-ui.card title="Gestion de gimnasios" subtitle="Vista operativa de suscripciones, renovaciones y riesgo de vencimiento.">
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
                    Vencen en 7 dias: {{ $expiringSoon }}
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
                    <th class="px-3 py-3">Ultimo pago</th>
                    <th class="px-3 py-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($gyms as $gym)
                    @php
                        $gymName = (string) ($gym->gym_name ?? '-');
                        $planName = (string) ($gym->plan_name ?? '-');
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
                    <tr data-gym-row class="border-b border-slate-100 align-top text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 font-semibold text-slate-800 dark:text-slate-100" data-gym-search="{{ strtolower($gymName.' '.$planName) }}">
                            {{ $gymName }}
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $planName }}</td>
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
                                    {{ $activeDays }} dias
                                </span>
                            @elseif ($gym->status === 'grace')
                                <span class="font-semibold text-amber-700 dark:text-amber-300">
                                    {{ (int) ($gym->grace_left ?? 0) }} dias de gracia
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $lastPaymentLabel }}</td>
                        <td class="px-3 py-3">
                            <div class="space-y-2">
                                <form method="POST" action="{{ route('superadmin.subscriptions.renew', $gym->gym_id) }}" class="grid gap-2 lg:grid-cols-[220px_140px_110px_auto] lg:items-center">
                                    @csrf
                                    <select name="plan_template_id" class="ui-input px-2 py-1.5 text-xs js-plan-template-select">
                                        <option value="">Mantener plan actual</option>
                                        @foreach (($planTemplates ?? collect()) as $template)
                                            <option value="{{ $template->id }}">
                                                {{ $template->name }} ({{ \App\Support\PlanDuration::label($template->duration_unit, (int) $template->duration_days, $template->duration_months) }}) - {{ \App\Support\Currency::format((float) $template->price, $appCurrencyCode) }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                      onsubmit="return confirm('Suspender suscripcion de este gimnasio?');">
                                    @csrf
                                    <x-ui.button type="submit" variant="danger" size="sm">Suspender</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
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
        if (!filterInput) return;

        const rows = Array.from(document.querySelectorAll('tr[data-gym-row]'));

        filterInput.addEventListener('input', function () {
            const q = String(filterInput.value || '').trim().toLowerCase();

            rows.forEach(function (row) {
                const searchCell = row.querySelector('[data-gym-search]');
                const value = searchCell ? String(searchCell.getAttribute('data-gym-search') || '') : '';
                const visible = q === '' || value.includes(q);
                row.classList.toggle('hidden', !visible);
            });
        });

        document.querySelectorAll('form[action*=\"/subscriptions/\"]').forEach(function (form) {
            const planSelect = form.querySelector('.js-plan-template-select');
            const monthsSelect = form.querySelector('.js-months-select');
            if (!planSelect || !monthsSelect) return;

            const syncMode = function () {
                const hasTemplate = String(planSelect.value || '').trim() !== '';
                monthsSelect.disabled = hasTemplate;
                monthsSelect.classList.toggle('opacity-60', hasTemplate);
                monthsSelect.classList.toggle('cursor-not-allowed', hasTemplate);
                monthsSelect.title = hasTemplate ? 'Se ignora cuando eliges un plan base.' : '';
            };

            planSelect.addEventListener('change', syncMode);
            syncMode();
        });
    })();
</script>
@endpush
