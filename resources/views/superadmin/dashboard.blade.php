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
        $ownerActivityRows = collect($reports['owner_activity_rows'] ?? []);
        $ownersOnlineNow = $ownerActivityRows->where('status_key', 'online')->count();
        $dashboardTimezone = trim((string) (auth()->user()?->timezone ?? config('app.timezone', 'UTC')));
        if (
            $dashboardTimezone === ''
            || $dashboardTimezone === 'UTC'
            || ! in_array($dashboardTimezone, timezone_identifiers_list(), true)
        ) {
            $dashboardTimezone = 'America/Guayaquil';
        }
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
                            <span>{{ $newGymsYear }} altas acumuladas en el a&ntilde;o para medir crecimiento comercial.</span>
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
                <p class="sa-stat-label">Cobrado este a&ntilde;o</p>
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
                        <strong>{{ \App\Support\Currency::format($currentYearDiscount, $appCurrencyCode) }} en descuentos del a&ntilde;o</strong>
                        <span>Lectura anual para no perder margen cuando ofreces planes por 3, 6 o 12 meses.</span>
                    </article>
                    <article class="sa-mini-card">
                        <strong>{{ $chargeCountYear }} cobros registrados este a&ntilde;o</strong>
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
                    <li>{{ $newGymsYear }} gimnasios nuevos acumulados este a&ntilde;o.</li>
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

        <x-ui.card title="Actividad de admins principales" subtitle="Solo se muestran los duenos/admins de cada gimnasio. No incluye SuperAdmin ni cajeros.">
            <div class="mb-4 sa-mini-grid">
                <article class="sa-mini-card">
                    <strong>{{ $ownersOnlineNow }} activos ahora mismo</strong>
                    <span>Se consideran activos si registraron actividad dentro de los ultimos 5 minutos.</span>
                </article>
                <article class="sa-mini-card">
                    <strong>{{ $ownerActivityRows->count() }} gimnasios monitoreados</strong>
                    <span>La tabla mezcla login real y uso del panel para cubrir web, Recuerdame y app instalada.</span>
                </article>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table min-w-[1240px]">
                    <thead>
                        <tr>
                            <th>Gimnasio</th>
                            <th>Admin principal</th>
                            <th>Estado</th>
                            <th>Ultima actividad</th>
                            <th>Ultimo login</th>
                            <th>Canal</th>
                            <th>IP</th>
                            <th>Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ownerActivityRows as $row)
                            @php
                                $lastActivityAt = $row['last_activity_at'] ?? null;
                                $lastActivityAtLocal = $lastActivityAt instanceof \Illuminate\Support\Carbon
                                    ? $lastActivityAt->copy()->timezone($dashboardTimezone)
                                    : null;
                                $lastLoginAt = $row['last_login_at'] ?? null;
                                $lastLoginAtLocal = $lastLoginAt instanceof \Illuminate\Support\Carbon
                                    ? $lastLoginAt->copy()->timezone($dashboardTimezone)
                                    : null;
                                $statusIsOnline = ($row['status_key'] ?? '') === 'online';
                            @endphp
                            <tr>
                                <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $row['gym_name'] ?? 'Gym' }}</td>
                                <td>{{ $row['user_name'] ?? 'Admin principal' }}</td>
                                <td>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide {{ $statusIsOnline ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                        {{ $row['status_label'] ?? 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $lastActivityAtLocal?->format('d/m/Y H:i') ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-300">
                                        @if (($row['signal'] ?? 'activity') === 'login_manual')
                                            Login manual
                                        @elseif (($row['signal'] ?? 'activity') === 'sesion_recordada')
                                            Sesion recordada
                                        @else
                                            Uso del panel
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $lastLoginAtLocal?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    <div>{{ $row['channel_label'] ?? 'Web' }}</div>
                                    @if ((bool) ($row['via_remember'] ?? false))
                                        <div class="text-xs text-slate-500 dark:text-slate-300">via Recuerdame</div>
                                    @endif
                                </td>
                                <td>{{ $row['ip_address'] ?? '-' }}</td>
                                <td>{{ $row['user_email'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="sa-empty-row">Todavia no hay actividad registrada de admins principales.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

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

        <x-ui.card title="Scanner pagina" subtitle="QR fijo para abrir tu pagina publica y descargarlo cuando lo necesites.">
            <div class="sa-page-scanner-grid">
                <div class="sa-page-scanner-copy">
                    <p class="sa-page-scanner-label">Enlace fijo</p>
                    <p id="superadmin-page-qr-url" class="sa-page-scanner-url">{{ $scannerPageUrl }}</p>
                    <p class="sa-page-scanner-hint">
                        Este QR se genera siempre desde este mismo enlace dentro del panel, para que no dependas de una imagen vieja
                        o de un archivo externo que se dañe con el tiempo.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <x-ui.button :href="$scannerPageUrl" target="_blank" rel="noopener">Abrir pagina</x-ui.button>
                        <x-ui.button type="button" variant="secondary" id="superadmin-page-qr-download">Descargar QR</x-ui.button>
                        <x-ui.button type="button" variant="ghost" id="superadmin-page-qr-copy">Copiar enlace</x-ui.button>
                    </div>

                    <p id="superadmin-page-qr-feedback" class="sa-page-scanner-feedback">
                        Listo para escanear o descargar.
                    </p>
                </div>

                <div class="sa-page-scanner-preview">
                    <div id="superadmin-page-qr-svg" class="sa-page-scanner-frame">
                        {!! $scannerPageQrSvg !!}
                    </div>
                    <p class="mt-3 text-xs ui-muted">
                        Escanealo con cualquier celular para abrir <strong>flexjok.duckdns.org</strong>.
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection

@push('styles')
    <style>
        .sa-page-scanner-grid {
            display: grid;
            gap: 1rem;
            align-items: center;
            grid-template-columns: minmax(0, 1.15fr) minmax(260px, 0.85fr);
        }

        .sa-page-scanner-copy {
            min-width: 0;
        }

        .sa-page-scanner-label {
            margin: 0;
            font-size: 0.74rem;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgb(71 85 105 / 0.9);
        }

        .dark .sa-page-scanner-label {
            color: rgb(148 163 184 / 0.88);
        }

        .sa-page-scanner-url {
            margin: 0.55rem 0 0;
            font-size: clamp(1rem, 0.95rem + 0.22vw, 1.08rem);
            line-height: 1.45;
            font-weight: 800;
            color: rgb(15 23 42 / 0.96);
            word-break: break-word;
        }

        .dark .sa-page-scanner-url {
            color: rgb(241 245 249 / 0.96);
        }

        .sa-page-scanner-hint {
            margin: 0.75rem 0 0;
            max-width: 40rem;
            font-size: 0.92rem;
            line-height: 1.6;
            color: rgb(71 85 105 / 0.92);
        }

        .dark .sa-page-scanner-hint {
            color: rgb(148 163 184 / 0.92);
        }

        .sa-page-scanner-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .sa-page-scanner-frame {
            width: min(100%, 320px);
            border-radius: 1.35rem;
            border: 1px solid rgb(16 185 129 / 0.34);
            background: linear-gradient(145deg, rgb(255 255 255 / 0.98), rgb(241 245 249 / 0.95));
            padding: 1rem;
            box-shadow: 0 20px 40px -28px rgb(2 8 23 / 0.45);
        }

        .sa-page-scanner-frame svg {
            display: block;
            width: 100%;
            height: auto;
        }

        .sa-page-scanner-feedback {
            min-height: 1.4rem;
            margin: 0.95rem 0 0;
            font-size: 0.84rem;
            font-weight: 700;
            color: rgb(5 150 105 / 0.98);
        }

        @media (max-width: 900px) {
            .sa-page-scanner-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const scannerPageUrl = @json($scannerPageUrl);
            const qrContainer = document.getElementById('superadmin-page-qr-svg');
            const downloadButton = document.getElementById('superadmin-page-qr-download');
            const copyButton = document.getElementById('superadmin-page-qr-copy');
            const feedback = document.getElementById('superadmin-page-qr-feedback');

            function setFeedback(text, isError) {
                if (!feedback) {
                    return;
                }

                feedback.textContent = text;
                feedback.style.color = isError ? 'rgb(244 63 94)' : 'rgb(5 150 105)';
            }

            async function copyText(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                    return;
                }

                const helper = document.createElement('textarea');
                helper.value = text;
                helper.setAttribute('readonly', 'readonly');
                helper.style.position = 'fixed';
                helper.style.opacity = '0';
                document.body.appendChild(helper);
                helper.select();
                document.execCommand('copy');
                document.body.removeChild(helper);
            }

            copyButton?.addEventListener('click', async function () {
                try {
                    await copyText(scannerPageUrl);
                    setFeedback('Enlace copiado.', false);
                } catch (error) {
                    setFeedback('No se pudo copiar el enlace.', true);
                }
            });

            downloadButton?.addEventListener('click', function () {
                try {
                    const svg = qrContainer?.querySelector('svg');
                    if (!svg) {
                        setFeedback('No se encontro el QR para descargar.', true);
                        return;
                    }

                    const serialized = new XMLSerializer().serializeToString(svg);
                    const blob = new Blob([serialized], { type: 'image/svg+xml;charset=utf-8' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'scanner-pagina-flexjok.svg';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    setFeedback('QR descargado.', false);
                } catch (error) {
                    setFeedback('No se pudo descargar el QR.', true);
                }
            });
        })();
    </script>
@endpush
