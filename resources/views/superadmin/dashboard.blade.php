@extends('layouts.panel')

@section('title', 'Panel SuperAdmin')
@section('page-title', 'Panel Global')

@section('content')
    @php
        $totalGyms = (int) ($kpis['total_gyms'] ?? 0);
        $activeGyms = (int) ($kpis['active_gyms'] ?? 0);
        $graceGyms = (int) ($kpis['grace_gyms'] ?? 0);
        $suspendedGyms = (int) ($kpis['suspended_gyms'] ?? 0);
        $mrrEstimated = (float) ($kpis['mrr_estimated'] ?? 0);
        $renewalsSoon = (int) ($kpis['vencen_en_7_dias'] ?? 0);
        $graceToday = (int) ($kpis['en_gracia_hoy'] ?? 0);
        $healthRate = $totalGyms > 0 ? (int) round(($activeGyms / $totalGyms) * 100) : 0;
        $attentionLoad = $graceGyms + $suspendedGyms + $renewalsSoon;
        $avgMrrPerGym = $activeGyms > 0 ? $mrrEstimated / max($activeGyms, 1) : 0;
    @endphp

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">SuperAdmin SaaS</span>
                    <h2 class="sa-title">Controla renovaciones, cartera y crecimiento desde una sola vista.</h2>
                    <p class="sa-subtitle">
                        Esta pantalla ahora prioriza lectura rápida y acción inmediata: primero salud del negocio,
                        luego riesgos y después accesos directos para operar gimnasios, planes y altas nuevas.
                    </p>

                    <div class="sa-actions">
                        <x-ui.button :href="route('superadmin.gyms.index')">Ver cartera global</x-ui.button>
                        <x-ui.button :href="route('superadmin.gym.index')" variant="secondary">Crear gimnasio</x-ui.button>
                        <x-ui.button :href="route('superadmin.plan-templates.index')" variant="ghost">Editar planes</x-ui.button>
                    </div>
                </div>

                <aside class="sa-note-card">
                    <p class="sa-note-label">Prioridades de hoy</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>{{ $renewalsSoon }} renovaciones cercanas</strong>
                            <span>Gimnasios que vencen en los próximos 7 días y necesitan seguimiento comercial.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>{{ $graceGyms }} en gracia / {{ $suspendedGyms }} suspendidos</strong>
                            <span>Separo los casos en riesgo para que no compitan visualmente con la cartera sana.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>{{ $healthRate }}% de cartera activa</strong>
                            <span>Indicador rápido para saber si la operación está creciendo o reteniendo clientes.</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Total gimnasios</p>
                <p class="sa-stat-value">{{ $totalGyms }}</p>
                <p class="sa-stat-meta">Base instalada total bajo control del sistema.</p>
            </article>
            <article class="sa-stat-card is-success">
                <p class="sa-stat-label">Activos</p>
                <p class="sa-stat-value">{{ $activeGyms }}</p>
                <p class="sa-stat-meta">Cartera saludable y operando sin interrupciones.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">En gracia</p>
                <p class="sa-stat-value">{{ $graceGyms }}</p>
                <p class="sa-stat-meta">Seguimiento prioritario para evitar suspensiones.</p>
            </article>
            <article class="sa-stat-card is-danger">
                <p class="sa-stat-label">Suspendidos</p>
                <p class="sa-stat-value">{{ $suspendedGyms }}</p>
                <p class="sa-stat-meta">Casos que requieren reactivación o cierre comercial.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">MRR estimado</p>
                <p class="sa-stat-value text-2xl">{{ \App\Support\Currency::format($mrrEstimated, $appCurrencyCode) }}</p>
                <p class="sa-stat-meta">Ingreso recurrente mensual sin contar sucursales gestionadas aparte.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Vencen en 7 días</p>
                <p class="sa-stat-value">{{ $renewalsSoon }}</p>
                <p class="sa-stat-meta">Carga comercial inmediata para el equipo de renovación.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">En gracia hoy</p>
                <p class="sa-stat-value">{{ $graceToday }}</p>
                <p class="sa-stat-meta">Parte de la cartera que ya consume días de tolerancia.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">MRR promedio</p>
                <p class="sa-stat-value text-2xl">{{ \App\Support\Currency::format($avgMrrPerGym, $appCurrencyCode) }}</p>
                <p class="sa-stat-meta">Ingreso promedio por gimnasio actualmente activo.</p>
            </article>
        </section>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.85fr)]">
            <x-ui.card title="Lectura ejecutiva" subtitle="Indicadores sinteticos para revisar la salud del portafolio antes de entrar al detalle.">
                <div class="sa-mini-grid">
                    <article class="sa-mini-card">
                        <strong>{{ $healthRate }}% cartera saludable</strong>
                        <span>
                            Porcentaje de gimnasios activos sobre la base total. Mejora la lectura frente a una lista plana de KPIs.
                        </span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ $attentionLoad }} casos con seguimiento</strong>
                        <span>
                            Suma renovaciones cercanas, gimnasios en gracia y suspendidos para priorizar atencion.
                        </span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ \App\Support\Currency::format($mrrEstimated, $appCurrencyCode) }} recurrentes</strong>
                        <span>
                            Vista comercial del ingreso mensual que soporta decisiones de pricing y retención.
                        </span>
                    </article>
                </div>
            </x-ui.card>

            <x-ui.card title="Acciones rápidas" subtitle="Atajos para los flujos que más usa SuperAdmin en operación diaria.">
                <ul class="sa-check-list">
                    <li>Alta de gimnasios con admin principal y plan inicial.</li>
                    <li>Revisión de cartera global con filtros y renovación inline.</li>
                    <li>Edición de planes base conectados con la landing pública.</li>
                    <li>Acceso directo al listado operativo para editar admins y credenciales.</li>
                </ul>

                <div class="mt-4 flex flex-wrap gap-2">
                    <x-ui.button :href="route('superadmin.gym-list.index')" size="sm" variant="ghost">Gestión de admins</x-ui.button>
                    <x-ui.button :href="route('superadmin.branches.index')" size="sm" variant="ghost">Sucursales</x-ui.button>
                    <x-ui.button :href="route('superadmin.quotations.index')" size="sm" variant="ghost">Cotizaciones</x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection
