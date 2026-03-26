@extends('layouts.panel')

@section('title', 'Sucursales')
@section('page-title', 'Modulo multisucursal')

@push('styles')
<style>
    .branches-page {
        display: grid;
        gap: 1rem;
    }

    .branches-hero {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgb(20 184 166 / 0.22);
        border-radius: 1.25rem;
        background:
            radial-gradient(circle at top right, rgb(34 211 238 / 0.14), transparent 34%),
            radial-gradient(circle at bottom left, rgb(16 185 129 / 0.1), transparent 28%),
            linear-gradient(150deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
        box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.42);
        padding: 1.15rem;
    }

    .theme-dark .branches-hero,
    .dark .branches-hero {
        border-color: rgb(34 211 238 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(34 211 238 / 0.11), transparent 34%),
            radial-gradient(circle at bottom left, rgb(16 185 129 / 0.08), transparent 28%),
            linear-gradient(155deg, rgb(3 10 24 / 0.95), rgb(11 24 36 / 0.9));
        box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.9);
    }

    .branches-hero::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
        opacity: 0.8;
        pointer-events: none;
    }

    .branches-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(95deg, transparent, rgb(34 211 238 / 0.04), transparent);
    }

    .branches-hero-grid {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1rem;
    }

    .branches-hero-main {
        display: grid;
        gap: 1rem;
    }

    .branches-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }

    .branches-hero-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }

    .branches-hero-stat {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        min-height: 2.5rem;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: rgb(255 255 255 / 0.76);
        padding: 0.48rem 0.78rem;
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.78);
    }

    .theme-dark .branches-hero-stat,
    .dark .branches-hero-stat {
        border-color: rgb(71 85 105 / 0.74);
        background: rgb(15 23 42 / 0.62);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .branches-hero-stat-value {
        font-size: 1rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.98);
    }

    .theme-dark .branches-hero-stat-value,
    .dark .branches-hero-stat-value {
        color: rgb(248 250 252 / 0.98);
    }

    .branches-hero-stat-label {
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .branches-hero-stat-label,
    .dark .branches-hero-stat-label {
        color: rgb(148 163 184 / 0.9);
    }

    .branches-hero-aside {
        position: relative;
        overflow: hidden;
        border-radius: 1.05rem;
        border: 1px solid rgb(148 163 184 / 0.22);
        background: linear-gradient(160deg, rgb(255 255 255 / 0.92), rgb(248 250 252 / 0.76));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        padding: 1rem;
    }

    .theme-dark .branches-hero-aside,
    .dark .branches-hero-aside {
        border-color: rgb(71 85 105 / 0.78);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.78), rgb(15 23 42 / 0.56));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.88);
    }

    .branches-hero-aside::before {
        content: '';
        position: absolute;
        left: 1rem;
        right: 1rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgb(34 211 238 / 0.92), rgb(34 211 238 / 0.22));
    }

    .branches-hero-aside-label {
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .branches-hero-aside-label,
    .dark .branches-hero-aside-label {
        color: rgb(148 163 184 / 0.9);
    }

    .branches-hero-aside-heading {
        margin-top: 0.5rem;
        font-size: 1.06rem;
        line-height: 1.08;
        letter-spacing: -0.03em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.98);
    }

    .theme-dark .branches-hero-aside-heading,
    .dark .branches-hero-aside-heading {
        color: rgb(248 250 252 / 0.98);
    }

    .branches-hero-aside-copy {
        margin-top: 0.45rem;
        font-size: 0.82rem;
        line-height: 1.56;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .branches-hero-aside-copy,
    .dark .branches-hero-aside-copy {
        color: rgb(148 163 184 / 0.9);
    }

    .branches-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgb(13 148 136 / 0.96);
    }

    .theme-dark .branches-kicker,
    .dark .branches-kicker {
        color: rgb(153 246 228 / 0.94);
    }

    .branches-kicker::before {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 999px;
        background: rgb(20 184 166 / 0.96);
        box-shadow: 0 0 0 6px rgb(20 184 166 / 0.14);
    }

    .branches-heading {
        margin-top: 0.75rem;
        font-size: clamp(1.16rem, 1.9vw, 1.5rem);
        line-height: 1.08;
        letter-spacing: -0.035em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .branches-heading,
    .dark .branches-heading {
        color: rgb(241 245 249 / 0.98);
    }

    .branches-summary {
        margin-top: 0.55rem;
        max-width: 52rem;
        font-size: 0.92rem;
        line-height: 1.62;
        color: rgb(71 85 105 / 0.94);
    }

    .theme-dark .branches-summary,
    .dark .branches-summary {
        color: rgb(148 163 184 / 0.9);
    }

    .branches-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border-radius: 999px;
        border: 1px solid rgb(20 184 166 / 0.22);
        background: rgb(240 253 250 / 0.88);
        padding: 0.44rem 0.78rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(13 148 136 / 0.96);
    }

    .theme-dark .branches-badge,
    .dark .branches-badge {
        border-color: rgb(20 184 166 / 0.26);
        background: rgb(13 148 136 / 0.12);
        color: rgb(153 246 228 / 0.95);
    }

    .branches-surface {
        border-color: rgb(148 163 184 / 0.22);
        background: linear-gradient(162deg, rgb(255 255 255 / 0.985), rgb(248 250 252 / 0.95));
        box-shadow: 0 26px 46px -38px rgb(15 23 42 / 0.34);
    }

    .theme-dark .branches-surface,
    .dark .branches-surface {
        border-color: rgb(71 85 105 / 0.74);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.84));
        box-shadow: 0 30px 48px -36px rgb(2 8 23 / 0.9);
    }

    .branches-kpi-grid {
        display: grid;
        gap: 0.75rem;
    }

    .branches-kpi-card {
        position: relative;
        overflow: hidden;
        min-height: 7.1rem;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / 0.2);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.92), rgb(248 250 252 / 0.76));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        padding: 0.95rem 1rem;
    }

    .theme-dark .branches-kpi-card,
    .dark .branches-kpi-card {
        border-color: rgb(148 163 184 / 0.14);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
    }

    .branches-kpi-card::before {
        content: '';
        position: absolute;
        left: 1rem;
        right: 1rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .branches-kpi-card[data-tone='neutral']::before {
        background: linear-gradient(90deg, rgb(71 85 105 / 0.86), rgb(148 163 184 / 0.22));
    }

    .branches-kpi-card[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(34 211 238 / 0.9), rgb(34 211 238 / 0.24));
    }

    .branches-kpi-card[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
    }

    .branches-kpi-card[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(99 102 241 / 0.9), rgb(99 102 241 / 0.24));
    }

    .branches-kpi-card[data-tone='income']::before {
        background: linear-gradient(90deg, rgb(34 197 94 / 0.9), rgb(34 197 94 / 0.24));
    }

    .branches-kpi-card[data-tone='expense']::before {
        background: linear-gradient(90deg, rgb(244 63 94 / 0.9), rgb(244 63 94 / 0.24));
    }

    .branches-kpi-card[data-tone='balance']::before {
        background: linear-gradient(90deg, rgb(6 182 212 / 0.9), rgb(6 182 212 / 0.24));
    }

    .branches-kpi-label {
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .branches-kpi-label,
    .dark .branches-kpi-label {
        color: rgb(148 163 184 / 0.9);
    }

    .branches-kpi-value {
        margin-top: 0.5rem;
        font-size: clamp(1.5rem, 2vw, 1.92rem);
        line-height: 1.02;
        font-weight: 900;
        letter-spacing: -0.04em;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .branches-kpi-value,
    .dark .branches-kpi-value {
        color: rgb(248 250 252 / 0.98);
    }

    .branches-note {
        border-radius: 1rem;
        border: 1px solid rgb(34 211 238 / 0.22);
        background: linear-gradient(145deg, rgb(236 254 255 / 0.9), rgb(255 255 255 / 0.92));
        color: rgb(14 116 144 / 0.96);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.76);
    }

    .theme-dark .branches-note,
    .dark .branches-note {
        border-color: rgb(34 211 238 / 0.24);
        background: rgb(8 145 178 / 0.14);
        color: rgb(207 250 254);
    }

    .branches-table-wrap {
        overflow-x: auto;
        border-radius: 1.08rem;
        border: 1px solid rgb(203 213 225 / 0.76);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.82), 0 22px 40px -34px rgb(15 23 42 / 0.18);
    }

    .theme-dark .branches-table-wrap,
    .dark .branches-table-wrap {
        border-color: rgb(51 65 85 / 0.86);
        background: linear-gradient(180deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.86));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04), 0 26px 44px -34px rgb(2 8 23 / 0.86);
    }

    .branches-table {
        min-width: 1200px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .branches-table thead th {
        position: sticky;
        top: 0;
        z-index: 6;
        background: linear-gradient(180deg, rgb(248 250 252 / 0.96), rgb(241 245 249 / 0.93));
        border-bottom: 1px solid rgb(203 213 225 / 0.72);
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .branches-table thead th,
    .dark .branches-table thead th {
        background: linear-gradient(180deg, rgb(15 23 42 / 0.94), rgb(15 23 42 / 0.9));
        border-bottom-color: rgb(51 65 85 / 0.88);
        color: rgb(148 163 184 / 0.94);
    }

    .branches-table tbody td {
        border-bottom: 1px solid rgb(226 232 240 / 0.78);
        vertical-align: middle;
    }

    .theme-dark .branches-table tbody td,
    .dark .branches-table tbody td {
        border-bottom-color: rgb(51 65 85 / 0.58);
    }

    .branches-table tbody tr:hover td {
        background: rgb(14 165 233 / 0.05);
    }

    .theme-dark .branches-table tbody tr:hover td,
    .dark .branches-table tbody tr:hover td {
        background: rgb(30 41 59 / 0.92);
    }

    .branches-status-pill,
    .branches-hub-pill,
    .branches-readonly-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        border: 1px solid transparent;
        border-radius: 999px;
        padding: 0.38rem 0.72rem;
        font-size: 0.67rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        line-height: 1;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .branches-hub-pill {
        border-color: rgb(6 182 212 / 0.24);
        background: rgb(236 254 255 / 0.92);
        color: rgb(14 116 144 / 0.96);
    }

    .branches-readonly-pill {
        border-color: rgb(148 163 184 / 0.22);
        background: rgb(241 245 249 / 0.92);
        color: rgb(71 85 105 / 0.94);
    }

    .theme-dark .branches-hub-pill,
    .dark .branches-hub-pill {
        border-color: rgb(34 211 238 / 0.24);
        background: rgb(8 145 178 / 0.14);
        color: rgb(207 250 254);
    }

    .theme-dark .branches-readonly-pill,
    .dark .branches-readonly-pill {
        border-color: rgb(100 116 139 / 0.28);
        background: rgb(51 65 85 / 0.58);
        color: rgb(226 232 240);
    }

    .branches-status-pill.is-active {
        border-color: rgb(16 185 129 / 0.28);
        background: rgb(236 253 245 / 0.92);
        color: rgb(5 150 105);
    }

    .branches-status-pill.is-grace {
        border-color: rgb(245 158 11 / 0.3);
        background: rgb(255 251 235 / 0.94);
        color: rgb(180 83 9);
    }

    .branches-status-pill.is-suspended {
        border-color: rgb(244 63 94 / 0.28);
        background: rgb(255 241 242 / 0.94);
        color: rgb(190 24 93);
    }

    .branches-status-pill.is-muted {
        border-color: rgb(148 163 184 / 0.3);
        background: rgb(241 245 249 / 0.94);
        color: rgb(71 85 105);
    }

    .theme-dark .branches-status-pill.is-active,
    .dark .branches-status-pill.is-active {
        border-color: rgb(52 211 153 / 0.22);
        background: rgb(5 150 105 / 0.3);
        color: rgb(209 250 229);
    }

    .theme-dark .branches-status-pill.is-grace,
    .dark .branches-status-pill.is-grace {
        border-color: rgb(251 191 36 / 0.24);
        background: rgb(180 83 9 / 0.34);
        color: rgb(254 243 199);
    }

    .theme-dark .branches-status-pill.is-suspended,
    .dark .branches-status-pill.is-suspended {
        border-color: rgb(251 113 133 / 0.24);
        background: rgb(190 24 93 / 0.3);
        color: rgb(255 228 230);
    }

    .theme-dark .branches-status-pill.is-muted,
    .dark .branches-status-pill.is-muted {
        border-color: rgb(100 116 139 / 0.3);
        background: rgb(51 65 85 / 0.6);
        color: rgb(226 232 240);
    }

    .branches-name {
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .branches-name,
    .dark .branches-name {
        color: rgb(248 250 252 / 0.98);
    }

    .branches-subtle {
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .branches-subtle,
    .dark .branches-subtle {
        color: rgb(148 163 184 / 0.9);
    }

    .branches-context-grid {
        display: grid;
        gap: 0.85rem;
    }

    .branches-context-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgb(148 163 184 / 0.22);
        border-radius: 1rem;
        background: linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 20px 34px -30px rgb(15 23 42 / 0.22);
        padding: 1rem;
    }

    .theme-dark .branches-context-card,
    .dark .branches-context-card {
        border-color: rgb(71 85 105 / 0.78);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.9), rgb(2 6 23 / 0.84));
        box-shadow: 0 24px 36px -30px rgb(2 8 23 / 0.88);
    }

    .branches-context-card::before {
        content: '';
        position: absolute;
        left: 1rem;
        right: 1rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(34 211 238 / 0.36);
    }

    .branches-context-card[data-tone='primary']::before {
        background: linear-gradient(90deg, rgb(34 211 238 / 0.92), rgb(34 211 238 / 0.24));
    }

    .branches-context-card[data-tone='hub']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.22));
    }

    .branches-context-label {
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .branches-context-label,
    .dark .branches-context-label {
        color: rgb(148 163 184 / 0.92);
    }

    .branches-context-heading {
        margin-top: 0.55rem;
        font-size: 1rem;
        line-height: 1.15;
        font-weight: 900;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .branches-context-heading,
    .dark .branches-context-heading {
        color: rgb(248 250 252 / 0.98);
    }

    .branches-context-copy {
        margin-top: 0.45rem;
        font-size: 0.85rem;
        line-height: 1.55;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .branches-context-copy,
    .dark .branches-context-copy {
        color: rgb(148 163 184 / 0.92);
    }

    .branches-context-meta {
        margin-top: 0.55rem;
        font-size: 0.75rem;
        color: rgb(100 116 139 / 0.92);
    }

    .theme-dark .branches-context-meta,
    .dark .branches-context-meta {
        color: rgb(148 163 184 / 0.82);
    }

    .branches-context-actions {
        margin-top: 0.85rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
    }

    .branches-action-stack {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .branches-action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
    }

    @media (min-width: 640px) {
        .branches-context-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .branches-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1200px) {
        .branches-hero-grid {
            grid-template-columns: minmax(0, 1fr) 18rem;
            align-items: start;
        }
    }

    @media (min-width: 1280px) {
        .branches-kpi-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
</style>
@endpush

@section('content')
    @php
        $money = static fn (float $amount): string => \App\Support\Currency::format($amount, $appCurrencyCode);
        $routeExtras = request()->query('pwa_mode') === 'standalone' ? ['pwa_mode' => 'standalone'] : [];
        $hubSlug = (string) ($hubGym->slug ?? request()->attributes->get('hub_gym_slug', request()->route('contextGym')));
        $globalPanelUrl = route('panel.index', ['contextGym' => $hubSlug, 'scope' => 'global'] + $routeExtras);
        $hubPanelUrl = route('panel.index', ['contextGym' => $hubSlug] + $routeExtras);
        $hubClientsUrl = route('clients.index', ['contextGym' => $hubSlug] + $routeExtras);
        $hubReportsUrl = route('reports.index', ['contextGym' => $hubSlug] + $routeExtras);
        $globalReportsUrl = route('reports.index', ['contextGym' => $hubSlug, 'scope' => 'global'] + $routeExtras);
        $linkedBranchCount = max(0, (int) (($kpis['total_gyms'] ?? 0) - 1));
    @endphp

    <div class="branches-page">
        <section class="branches-hero">
            <div class="branches-hero-grid">
                <div class="branches-hero-main">
                    <div>
                        <span class="branches-kicker">Plan Sucursales / Consolidado</span>
                        <h2 class="branches-heading">Controla sede principal y sucursales desde una sola lectura.</h2>
                        <p class="branches-summary">
                            Revisa el rendimiento combinado de tu red, cambia entre contextos y detecta rapido que sede necesita atencion hoy.
                        </p>
                    </div>
                    <div class="branches-hero-stats">
                        <span class="branches-hero-stat">
                            <span class="branches-hero-stat-value">{{ (int) ($kpis['total_gyms'] ?? 0) }}</span>
                            <span class="branches-hero-stat-label">Sedes visibles</span>
                        </span>
                        <span class="branches-hero-stat">
                            <span class="branches-hero-stat-value">{{ $linkedBranchCount }}</span>
                            <span class="branches-hero-stat-label">Sucursales</span>
                        </span>
                        <span class="branches-hero-stat">
                            <span class="branches-hero-stat-value">{{ number_format((int) ($kpis['checkins_today'] ?? 0)) }}</span>
                            <span class="branches-hero-stat-label">Check-ins hoy</span>
                        </span>
                    </div>
                    <div class="branches-hero-actions">
                        <x-ui.button :href="$globalPanelUrl" variant="primary">Abrir panel global</x-ui.button>
                        <x-ui.button :href="$hubPanelUrl" variant="secondary">Abrir sede principal</x-ui.button>
                        <x-ui.button :href="$globalReportsUrl" variant="ghost">Reportes globales</x-ui.button>
                    </div>
                </div>

                <aside class="branches-hero-aside">
                    <span class="branches-badge">Admin global</span>
                    <p class="branches-hero-aside-label">Contexto actual del modulo</p>
                    <h3 class="branches-hero-aside-heading">{{ $hubGym->name ?? 'Red multisucursal' }}</h3>
                    <p class="branches-hero-aside-copy">
                        Esta vista siempre entra en modo consolidado y solo lectura. Usa los atajos para bajar a la sede principal o a una sucursal puntual cuando necesites operar.
                    </p>
                    <p class="branches-context-meta">{{ $linkedBranchCount }} sucursal(es) vinculadas + sede principal</p>
                </aside>
            </div>
        </section>

        <x-ui.card class="branches-surface" title="Resumen consolidado" subtitle="Totales operativos combinados entre sede principal y sucursales vinculadas.">
            <div class="branches-kpi-grid">
                <article class="branches-kpi-card" data-tone="neutral">
                    <p class="branches-kpi-label">Sedes en red</p>
                    <p class="branches-kpi-value">{{ (int) ($kpis['total_gyms'] ?? 0) }}</p>
                </article>
                <article class="branches-kpi-card" data-tone="info">
                    <p class="branches-kpi-label">Clientes</p>
                    <p class="branches-kpi-value">{{ number_format((int) ($kpis['total_clients'] ?? 0)) }}</p>
                </article>
                <article class="branches-kpi-card" data-tone="success">
                    <p class="branches-kpi-label">Membresias activas</p>
                    <p class="branches-kpi-value">{{ number_format((int) ($kpis['active_memberships'] ?? 0)) }}</p>
                </article>
                <article class="branches-kpi-card" data-tone="warning">
                    <p class="branches-kpi-label">Check-ins hoy</p>
                    <p class="branches-kpi-value">{{ number_format((int) ($kpis['checkins_today'] ?? 0)) }}</p>
                </article>
                <article class="branches-kpi-card" data-tone="income">
                    <p class="branches-kpi-label">Ingresos 30 dias</p>
                    <p class="branches-kpi-value">{{ $money((float) ($kpis['income_30d'] ?? 0)) }}</p>
                </article>
                <article class="branches-kpi-card" data-tone="expense">
                    <p class="branches-kpi-label">Egresos 30 dias</p>
                    <p class="branches-kpi-value">{{ $money((float) ($kpis['expense_30d'] ?? 0)) }}</p>
                </article>
                <article class="branches-kpi-card" data-tone="balance">
                    <p class="branches-kpi-label">Balance 30 dias</p>
                    <p class="branches-kpi-value">{{ $money((float) ($kpis['balance_30d'] ?? 0)) }}</p>
                </article>
            </div>
        </x-ui.card>

        <x-ui.card class="branches-surface" title="Atajos de contexto" subtitle="Separa admin global, sede principal y sucursales operativas sin mezclar la red.">
            <div class="branches-context-grid">
                <article class="branches-context-card" data-tone="primary">
                    <p class="branches-context-label">Panel global</p>
                    <h3 class="branches-context-heading">Admin global</h3>
                    <p class="branches-context-copy">
                        Entra al consolidado de solo lectura para comparar todas las sedes antes de bajar a una operativa puntual.
                    </p>
                    <p class="branches-context-meta">{{ $linkedBranchCount }} sucursal(es) vinculadas + sede principal</p>
                    <div class="branches-context-actions">
                        <x-ui.button :href="$globalPanelUrl" variant="primary" size="sm">Abrir panel</x-ui.button>
                    </div>
                </article>

                <article class="branches-context-card" data-tone="hub">
                    <p class="branches-context-label">Sede principal</p>
                    <h3 class="branches-context-heading">{{ $hubGym->name ?? 'Sede principal' }}</h3>
                    <p class="branches-context-copy">
                        Abre el contexto operativo completo de la sede principal para cobrar, revisar clientes y usar caja sin modo global.
                    </p>
                    <p class="branches-context-meta">
                        {{ $hubGym->address ?: collect([$hubGym->address_city ?? null, $hubGym->address_state ?? null])->filter()->implode(', ') ?: '-' }}
                    </p>
                    <div class="branches-context-actions">
                        <x-ui.button :href="$hubPanelUrl" variant="secondary" size="sm">Abrir panel</x-ui.button>
                        <x-ui.button :href="$hubClientsUrl" variant="ghost" size="sm">Clientes</x-ui.button>
                        <x-ui.button :href="$hubReportsUrl" variant="ghost" size="sm">Reportes</x-ui.button>
                    </div>
                </article>
            </div>
        </x-ui.card>

        <x-ui.card class="branches-surface" title="Gestion de vinculos" subtitle="Las sucursales se crean y administran desde SuperAdmin.">
            <p class="branches-note px-4 py-3 text-sm font-semibold">
                Tu gimnasio solo puede visualizar las sedes vinculadas. Para crear, editar o desvincular sucursales, usa el panel de SuperAdmin.
            </p>
        </x-ui.card>

        <x-ui.card class="branches-surface" title="Detalle por sede" subtitle="Rendimiento individual de cada sede vinculada.">
            <div class="branches-table-wrap">
                <table class="ui-table branches-table">
                    <thead>
                        <tr>
                            <th class="px-3 py-3">Sede</th>
                            <th class="px-3 py-3">Plan</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Clientes</th>
                            <th class="px-3 py-3">Membresias</th>
                            <th class="px-3 py-3">Check-ins hoy</th>
                            <th class="px-3 py-3">Ingresos 30d</th>
                            <th class="px-3 py-3">Egresos 30d</th>
                            <th class="px-3 py-3">Balance 30d</th>
                            <th class="px-3 py-3">Gestion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($branchRows ?? collect()) as $row)
                            @php
                                $status = (string) ($row['subscription_status'] ?? '-');
                                $branchRouteParams = ['contextGym' => (string) ($row['slug'] ?? '')] + $routeExtras;
                                $badgeClass = match ($status) {
                                    'active' => 'is-active',
                                    'grace' => 'is-grace',
                                    'suspended' => 'is-suspended',
                                    default => 'is-muted',
                                };
                            @endphp
                            <tr class="text-sm odd:bg-white even:bg-slate-50 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                                <td class="px-3 py-3">
                                    <p class="branches-name font-semibold">{{ $row['name'] }}</p>
                                    <p class="branches-subtle text-xs">{{ $row['slug'] }}</p>
                                    <p class="branches-subtle mt-1 text-xs">{{ $row['address'] ?? '-' }}</p>
                                    @if (! empty($row['is_hub']))
                                        <span class="branches-hub-pill mt-2">Sede principal</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 branches-subtle">{{ $row['plan_name'] }}</td>
                                <td class="px-3 py-3">
                                    <span class="branches-status-pill {{ $badgeClass }}">
                                        {{ $status !== '' ? $status : '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 branches-subtle">{{ number_format((int) $row['clients_total']) }}</td>
                                <td class="px-3 py-3 branches-subtle">{{ number_format((int) $row['active_memberships']) }}</td>
                                <td class="px-3 py-3 branches-subtle">{{ number_format((int) $row['checkins_today']) }}</td>
                                <td class="px-3 py-3 text-emerald-700 dark:text-emerald-300">{{ $money((float) $row['income_30d']) }}</td>
                                <td class="px-3 py-3 text-rose-700 dark:text-rose-300">{{ $money((float) $row['expense_30d']) }}</td>
                                <td class="px-3 py-3 text-cyan-700 dark:text-cyan-300">{{ $money((float) $row['balance_30d']) }}</td>
                                <td class="px-3 py-3">
                                    <div class="branches-action-stack">
                                        <x-ui.button :href="route('panel.index', $branchRouteParams)" :variant="! empty($row['is_hub']) ? 'secondary' : 'primary'" size="sm">
                                            Abrir panel
                                        </x-ui.button>
                                        <div class="branches-action-group">
                                            <x-ui.button :href="route('clients.index', $branchRouteParams)" variant="ghost" size="sm">Clientes</x-ui.button>
                                            <x-ui.button :href="route('reports.index', $branchRouteParams)" variant="ghost" size="sm">Reportes</x-ui.button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-3 py-6 text-center text-sm branches-subtle">
                                    No hay sedes vinculadas todavia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
@endsection
