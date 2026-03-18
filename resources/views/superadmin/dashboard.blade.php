@extends('layouts.panel')

@section('title', 'Panel SuperAdmin')
@section('page-title', 'Panel Global')

@section('content')
    @php
        $totalGyms = (int) ($kpis['total_gyms'] ?? 0);
        $activeGyms = (int) ($kpis['active_gyms'] ?? 0);
        $graceGyms = (int) ($kpis['grace_gyms'] ?? 0);
        $suspendedGyms = (int) ($kpis['suspended_gyms'] ?? 0);
        $currentCycleRevenue = (float) ($kpis['current_cycle_revenue'] ?? 0);
        $currentCycleDiscount = (float) ($kpis['current_cycle_discount'] ?? 0);
        $recurringMrr = (float) ($kpis['recurring_mrr'] ?? 0);
        $annualRunRate = (float) ($kpis['annual_run_rate'] ?? 0);
        $currentMonthRevenue = (float) ($kpis['current_month_revenue'] ?? 0);
        $currentYearRevenue = (float) ($kpis['current_year_revenue'] ?? 0);
        $currentMonthDiscount = (float) ($kpis['current_month_discount'] ?? 0);
        $currentYearDiscount = (float) ($kpis['current_year_discount'] ?? 0);
        $chargeCountMonth = (int) ($kpis['charges_this_month'] ?? 0);
        $chargeCountYear = (int) ($kpis['charges_this_year'] ?? 0);
        $newGymsMonth = (int) ($kpis['new_gyms_month'] ?? 0);
        $newGymsYear = (int) ($kpis['new_gyms_year'] ?? 0);
        $avgTicketMonth = (float) ($kpis['avg_ticket_month'] ?? 0);
        $discountedSubscriptions = (int) ($kpis['discounted_subscriptions'] ?? 0);
        $renewalsSoon = (int) ($kpis['vencen_en_7_dias'] ?? 0);
        $graceToday = (int) ($kpis['en_gracia_hoy'] ?? 0);
        $healthRate = $totalGyms > 0 ? (int) round(($activeGyms / $totalGyms) * 100) : 0;
        $planMix = collect($planMix ?? []);
        $monthlyRows = collect($reports['monthly_rows'] ?? []);
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">SuperAdmin SaaS</span>
                    <h2 class="sa-title">Ve cobros, descuentos y crecimiento sin adivinar numeros.</h2>
                    <p class="sa-subtitle">
                        Este panel ahora separa lo que ya cobraste, lo que proyecta tu cartera activa y los nuevos gimnasios
                        que entran al sistema. Asi puedes leer mejor promociones, pagos adelantados y salud comercial.
                    </p>

                    <div class="sa-actions">
                        <x-ui.button :href="route('superadmin.gyms.index')">Ver cartera global</x-ui.button>
                        <x-ui.button :href="route('superadmin.gym.index')" variant="secondary">Crear gimnasio</x-ui.button>
                        <x-ui.button :href="route('superadmin.plan-templates.index')" variant="ghost">Editar planes</x-ui.button>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Lectura rapida</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>{{ \App\Support\Currency::format($currentMonthRevenue, $appCurrencyCode) }} cobrados este mes</strong>
                            <span>{{ $chargeCountMonth }} movimientos registrados con descuentos ya aplicados.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>{{ $newGymsMonth }} gimnasios nuevos este mes</strong>
                            <span>{{ $newGymsYear }} altas acumuladas en el ano para medir crecimiento comercial.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>{{ $discountedSubscriptions }} suscripciones con descuento vigente</strong>
                            <span>{{ \App\Support\Currency::format($currentCycleDiscount, $appCurrencyCode) }} descontados en la cartera activa actual.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total gimnasios</p>
                <p class="sa-stat-value">{{ $totalGyms }}</p>
                <p class="sa-stat-meta">Base total instalada dentro del sistema.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Activos</p>
                <p class="sa-stat-value">{{ $activeGyms }}</p>
                <p class="sa-stat-meta">{{ $healthRate }}% de la cartera sigue operando sin corte.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">En gracia</p>
                <p class="sa-stat-value">{{ $graceGyms }}</p>
                <p class="sa-stat-meta">{{ $graceToday }} requieren seguimiento inmediato hoy.</p>
            </article>
            <article class="sa-stat-card is-danger">
                <p class="sa-stat-label">Suspendidos</p>
                <p class="sa-stat-value">{{ $suspendedGyms }}</p>
                <p class="sa-stat-meta">Casos fuera de operacion comercial.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Cobro ciclo vigente</p>
                <p class="sa-stat-value text-2xl">{{ \App\Support\Currency::format($currentCycleRevenue, $appCurrencyCode) }}</p>
                <p class="sa-stat-meta">Total del ciclo activo, respetando meses pagados y descuentos del cobro actual.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Cobrado este mes</p>
                <p class="sa-stat-value text-2xl">{{ \App\Support\Currency::format($currentMonthRevenue, $appCurrencyCode) }}</p>
                <p class="sa-stat-meta">Ingreso registrado en el mes actual.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Cobrado este ano</p>
                <p class="sa-stat-value text-2xl">{{ \App\Support\Currency::format($currentYearRevenue, $appCurrencyCode) }}</p>
                <p class="sa-stat-meta">Acumulado anual segun historial de altas y renovaciones.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Proyeccion anual</p>
                <p class="sa-stat-value text-2xl">{{ \App\Support\Currency::format($annualRunRate, $appCurrencyCode) }}</p>
                <p class="sa-stat-meta">MRR base de la cartera actual multiplicado por 12.</p>
            </article>
        </section>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.85fr)]">
            <x-ui.card title="Reporte comercial" subtitle="Resumen de ingresos, descuentos y altas para que el panel sea util de verdad.">
                <div class="sa-mini-grid">
                    <article class="sa-mini-card">
                        <strong>{{ \App\Support\Currency::format($currentMonthDiscount, $appCurrencyCode) }} en descuentos del mes</strong>
                        <span>Te muestra cuanto cediste comercialmente en promociones activas durante este mes.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ \App\Support\Currency::format($currentYearDiscount, $appCurrencyCode) }} en descuentos del ano</strong>
                        <span>Lectura anual para no perder margen cuando ofreces planes por 3, 6 o 12 meses.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ $chargeCountYear }} cobros registrados este ano</strong>
                        <span>Incluye altas y renovaciones que quedaron guardadas como eventos comerciales.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ \App\Support\Currency::format($avgTicketMonth, $appCurrencyCode) }} ticket promedio del mes</strong>
                        <span>Ayuda a leer si estan entrando planes mas grandes o pagos por varios meses.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ \App\Support\Currency::format($recurringMrr, $appCurrencyCode) }} MRR base</strong>
                        <span>Mensualidad recurrente proyectada despues de promociones temporales.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ $renewalsSoon }} renovaciones cercanas</strong>
                        <span>Gimnasios que vencen en los proximos 7 dias y ya merecen seguimiento.</span>
                    </article>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="ui-table min-w-[760px]">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Cobrado</th>
                                <th>Descuento</th>
                                <th>Cobros</th>
                                <th>Gimnasios nuevos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthlyRows as $row)
                                <tr>
                                    <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $row['month_label'] }}</td>
                                    <td>{{ \App\Support\Currency::format((float) ($row['revenue'] ?? 0), $appCurrencyCode) }}</td>
                                    <td>{{ \App\Support\Currency::format((float) ($row['discount'] ?? 0), $appCurrencyCode) }}</td>
                                    <td>{{ (int) ($row['charges'] ?? 0) }}</td>
                                    <td>{{ (int) ($row['new_gyms'] ?? 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="sa-empty-row">Todavia no hay historial comercial para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <x-ui.card title="Alertas y crecimiento" subtitle="Indicadores rapidos para revisar cartera, altas nuevas y margen comercial.">
                <ul class="sa-check-list">
                    <li>{{ $newGymsMonth }} gimnasios nuevos este mes.</li>
                    <li>{{ $newGymsYear }} gimnasios nuevos acumulados este ano.</li>
                    <li>{{ $chargeCountMonth }} cobros registrados durante el mes actual.</li>
                    <li>{{ \App\Support\Currency::format($currentCycleDiscount, $appCurrencyCode) }} descontados dentro del ciclo vigente.</li>
                    <li>{{ $discountedSubscriptions }} cuentas activas estan operando con descuento en este momento.</li>
                    <li>{{ $renewalsSoon }} renovaciones proximas y {{ $graceToday }} cuentas hoy en gracia.</li>
                </ul>

                <div class="mt-4 flex flex-wrap gap-2">
                    <x-ui.button :href="route('superadmin.gym-list.index')" size="sm" variant="ghost">Gestion de admins</x-ui.button>
                    <x-ui.button :href="route('superadmin.branches.index')" size="sm" variant="ghost">Sucursales</x-ui.button>
                    <x-ui.button :href="route('superadmin.quotations.index')" size="sm" variant="ghost">Cotizaciones</x-ui.button>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card title="Conteo por plan" subtitle="Cuantos gimnasios hay hoy en cada uno de tus 4 planes comerciales.">
            <div class="sa-mini-grid">
                @forelse ($planMix as $row)
                    <article class="sa-mini-card">
                        <strong>{{ $row['name'] }}: {{ (int) ($row['count'] ?? 0) }}</strong>
                        <span>Gimnasios que hoy operan con este plan base.</span>
                    </article>
                @empty
                    <article class="sa-mini-card">
                        <strong>Sin datos</strong>
                        <span>Todavia no hay cartera suficiente para agrupar por plan.</span>
                    </article>
                @endforelse
            </div>
        </x-ui.card>
    </div>
@endsection
