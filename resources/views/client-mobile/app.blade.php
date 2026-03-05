<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16c172">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GymSystem">
    <link rel="manifest" href="{{ route('client-mobile.manifest', ['gymSlug' => $gym->slug]) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    <link rel="apple-touch-icon" href="{{ asset('pwa/favicon-brand.png?v=20260302') }}">
    <title>App cliente - {{ (string) $gym->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --page-bg: #020617;
            --neon-green: #22c55e;
            --neon-cyan: #22d3ee;
            --card-border: rgba(34,211,238,.35);
            --card-bg: rgba(2,6,23,.84);
        }
        .mobile-guard { display: none; }
        .mobile-shell {
            min-height: 100vh;
            background:
                linear-gradient(155deg, rgba(4,16,33,.42), rgba(2,6,23,.58) 62%),
                radial-gradient(circle at 14% 12%, rgba(34,197,94,.14), transparent 40%),
                radial-gradient(circle at 88% 5%, rgba(34,211,238,.13), transparent 44%),
                url('https://drive.google.com/thumbnail?id=1roKHbuS8zikZL_VWUOC-Tz9wxGoIhZFk&sz=w2048') center 28% / cover no-repeat;
            color: #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        .mobile-shell::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(rgba(34,197,94,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34,197,94,.035) 1px, transparent 1px);
            background-size: 30px 30px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.9), rgba(0,0,0,.2));
            pointer-events: none;
        }
        .hero-card {
            border: 1px solid rgba(34,197,94,.48);
            background: linear-gradient(128deg, rgba(34,197,94,.18), rgba(34,211,238,.09) 55%, rgba(2,6,23,.9));
            box-shadow: 0 22px 56px rgba(0,0,0,.55);
        }
        .glass-card {
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            box-shadow: 0 18px 50px rgba(0,0,0,.48);
            backdrop-filter: blur(4px);
        }
        .home-clean {
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
        }
        .home-welcome {
            text-align: center;
            padding-top: 14px;
            padding-bottom: 2px;
        }
        .home-stage {
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: clamp(420px, 62vh, 700px);
            justify-content: center;
        }
        .home-intro {
            text-align: center;
        }
        .home-intro-title {
            color: #d9f99d;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: .01em;
            line-height: 1.15;
        }
        .home-intro-text {
            margin-top: 6px;
            color: #d1d5db;
            font-size: 15px;
            line-height: 1.4;
        }
        .personal-message-card {
            border: 1px solid rgba(250,204,21,.34);
            background: linear-gradient(140deg, rgba(2,6,23,.9), rgba(71,29,149,.36), rgba(2,6,23,.92));
            border-radius: 16px;
            padding: 12px 13px;
            box-shadow: 0 14px 32px rgba(0,0,0,.34);
        }
        .personal-message-tag {
            color: #fde68a;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }
        .personal-message-line {
            margin-top: 7px;
            color: #f8fafc;
            font-size: 14px;
            line-height: 1.35;
            font-weight: 700;
        }
        .personal-message-context {
            margin-top: 7px;
            color: #cbd5e1;
            font-size: 11px;
            line-height: 1.35;
        }
        .home-btn {
            margin-top: 2px;
        }
        .top-user-menu {
            position: relative;
            z-index: 48;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: max(8px, env(safe-area-inset-top));
            margin-bottom: 14px;
            min-height: 46px;
        }
        .user-chip {
            border: 1px solid rgba(45,212,191,.34);
            background: linear-gradient(140deg, rgba(2,6,23,.94), rgba(15,23,42,.9));
            color: #f8fafc;
            border-radius: 15px;
            padding: 9px 12px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-weight: 800;
            font-size: 13px;
            box-shadow: 0 14px 28px rgba(0,0,0,.35), inset 0 0 0 1px rgba(34,197,94,.12);
            backdrop-filter: blur(4px);
            min-height: 43px;
            margin-left: auto;
            max-width: min(74vw, 266px);
            transition: transform .12s ease, border-color .2s ease, box-shadow .2s ease;
        }
        .user-chip:hover {
            border-color: rgba(45,212,191,.55);
            box-shadow: 0 16px 30px rgba(0,0,0,.38), inset 0 0 0 1px rgba(34,197,94,.18);
        }
        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            color: #dcfce7;
            background: radial-gradient(circle at 30% 20%, rgba(34,197,94,.35), rgba(30,41,59,.95));
        }
        .user-avatar-image {
            width: 100%;
            height: 100%;
            border-radius: inherit;
            object-fit: cover;
            display: block;
        }
        .user-chip-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-chip-caret {
            color: #a7f3d0;
            font-size: 10px;
            font-weight: 900;
        }
        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: min(72vw, 220px);
            border: 1px solid rgba(56,189,248,.33);
            background: rgba(2,6,23,.93);
            border-radius: 16px;
            box-shadow: 0 20px 48px rgba(0,0,0,.5);
            padding: 6px;
            backdrop-filter: blur(6px);
        }
        .user-dropdown-item {
            width: 100%;
            text-align: center;
            border: 0;
            background: transparent;
            color: #e2e8f0;
            border-radius: 10px;
            padding: 10px 9px;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .user-dropdown-item + .user-dropdown-item {
            margin-top: 3px;
        }
        .user-dropdown-item:active {
            background: rgba(30,41,59,.5);
        }
        .user-dropdown-link {
            color: #bfdbfe;
        }
        .user-dropdown-logout {
            color: #fecaca;
        }
        .live-mobile-card {
            border: 1px solid rgba(34,197,94,.52);
            background: radial-gradient(circle at 14% 20%, rgba(34,197,94,.2), transparent 48%), rgba(2,6,23,.88);
            box-shadow: 0 18px 44px rgba(34,197,94,.2);
        }
        .prediction-card {
            border: 1px solid rgba(34,211,238,.34);
            background: linear-gradient(145deg, rgba(2,6,23,.9), rgba(15,23,42,.84));
            box-shadow: 0 16px 36px rgba(0,0,0,.36);
            border-radius: 18px;
            padding: 14px;
        }
        .prediction-title {
            color: #d9f99d;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .prediction-rhythm {
            margin-top: 5px;
            color: #7dd3fc;
            font-size: 12px;
            font-weight: 700;
        }
        .prediction-line {
            margin-top: 8px;
            color: #f8fafc;
            font-size: 14px;
            line-height: 1.4;
            font-weight: 700;
        }
        .prediction-context {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.35;
        }
        .body-state-card {
            border: 1px solid rgba(34,211,238,.32);
            background: linear-gradient(145deg, rgba(2,6,23,.9), rgba(10,25,47,.84));
            box-shadow: 0 16px 36px rgba(0,0,0,.34);
            border-radius: 18px;
            padding: 14px;
        }
        .body-state-title {
            color: #bfdbfe;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .body-state-summary {
            margin-top: 6px;
            color: #dbeafe;
            font-size: 12px;
            line-height: 1.35;
            font-weight: 600;
        }
        .body-state-grid {
            margin-top: 10px;
            display: grid;
            gap: 8px;
        }
        .body-state-row {
            display: grid;
            grid-template-columns: 90px 1fr 32px;
            align-items: center;
            gap: 8px;
        }
        .body-state-label {
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 700;
        }
        .body-state-track {
            position: relative;
            width: 100%;
            height: 9px;
            border-radius: 9999px;
            background: rgba(148,163,184,.25);
            overflow: hidden;
        }
        .body-state-fill {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            border-radius: inherit;
            transition: width .35s ease-out;
        }
        .body-state-fill-force {
            background: linear-gradient(90deg, #60a5fa, #22d3ee);
        }
        .body-state-fill-resistance {
            background: linear-gradient(90deg, #22d3ee, #34d399);
        }
        .body-state-fill-discipline {
            background: linear-gradient(90deg, #84cc16, #22c55e);
        }
        .body-state-fill-recovery {
            background: linear-gradient(90deg, #f59e0b, #fb7185);
        }
        .body-state-value {
            color: #f8fafc;
            font-size: 12px;
            font-weight: 800;
            text-align: right;
        }
        .body-state-context {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.35;
        }
        .training-card {
            border: 1px solid rgba(34,197,94,.32);
            background: linear-gradient(145deg, rgba(2,6,23,.9), rgba(5,46,22,.6));
            box-shadow: 0 16px 36px rgba(0,0,0,.34);
            border-radius: 18px;
            padding: 14px;
        }
        .training-title {
            color: #bbf7d0;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .training-line {
            margin-top: 7px;
            color: #ecfeff;
            font-size: 12px;
            line-height: 1.35;
            font-weight: 600;
        }
        .training-list {
            margin-top: 10px;
            display: grid;
            gap: 7px;
        }
        .training-item {
            border: 1px solid rgba(52,211,153,.24);
            border-radius: 10px;
            background: rgba(2,6,23,.56);
            padding: 9px 10px;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }
        .training-item-name {
            color: #f8fafc;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
        }
        .training-item-dose {
            color: #86efac;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }
        .training-context {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.35;
        }
        .training-session-box {
            margin-top: 12px;
            border: 1px solid rgba(56,189,248,.28);
            border-radius: 12px;
            background: rgba(2,6,23,.58);
            padding: 10px;
            display: grid;
            gap: 8px;
        }
        .training-session-status {
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.35;
        }
        .training-session-timer {
            color: #67e8f9;
            font-size: 12px;
            font-weight: 700;
        }
        .training-session-actions {
            display: grid;
            gap: 8px;
        }
        .training-session-hint {
            color: #bae6fd;
            font-size: 11px;
            line-height: 1.35;
        }
        .training-session-feedback {
            color: #bbf7d0;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.3;
        }
        .training-session-feedback.is-error {
            color: #fecaca;
        }
        .weekly-goal-card {
            border: 1px solid rgba(56,189,248,.3);
            background: linear-gradient(145deg, rgba(2,6,23,.92), rgba(12,74,110,.48));
            box-shadow: 0 16px 36px rgba(0,0,0,.34);
            border-radius: 18px;
            padding: 14px;
        }
        .weekly-goal-title {
            color: #bae6fd;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .weekly-goal-summary {
            margin-top: 7px;
            color: #f8fafc;
            font-size: 13px;
            line-height: 1.35;
            font-weight: 700;
        }
        .weekly-progress-track {
            margin-top: 9px;
            width: 100%;
            height: 9px;
            border-radius: 9999px;
            background: rgba(148,163,184,.24);
            overflow: hidden;
        }
        .weekly-progress-fill {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, #22d3ee, #34d399);
            transition: width .35s ease-out;
        }
        .weekly-progress-meta {
            margin-top: 6px;
            color: #bfdbfe;
            font-size: 11px;
            line-height: 1.35;
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }
        .weekly-alert-list {
            margin-top: 10px;
            display: grid;
            gap: 6px;
        }
        .weekly-alert-item {
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 11px;
            line-height: 1.35;
            font-weight: 700;
        }
        .weekly-alert-info {
            border: 1px solid rgba(56,189,248,.35);
            background: rgba(8,47,73,.56);
            color: #bae6fd;
        }
        .weekly-alert-success {
            border: 1px solid rgba(34,197,94,.36);
            background: rgba(20,83,45,.52);
            color: #bbf7d0;
        }
        .weekly-alert-warning {
            border: 1px solid rgba(251,191,36,.36);
            background: rgba(120,53,15,.52);
            color: #fde68a;
        }
        .weekly-alert-danger {
            border: 1px solid rgba(248,113,113,.42);
            background: rgba(127,29,29,.52);
            color: #fecaca;
        }
        .weekly-goal-edit-panel {
            margin-top: 10px;
            border: 1px solid rgba(56,189,248,.34);
            background: rgba(2,6,23,.72);
            border-radius: 14px;
            padding: 11px;
        }
        .weekly-history-card {
            border: 1px solid rgba(34,197,94,.26);
            background: linear-gradient(145deg, rgba(2,6,23,.92), rgba(22,101,52,.34));
            box-shadow: 0 16px 36px rgba(0,0,0,.33);
            border-radius: 18px;
            padding: 14px;
        }
        .weekly-history-title {
            color: #bbf7d0;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .weekly-history-month {
            margin-top: 2px;
            color: #a7f3d0;
            font-size: 11px;
            font-weight: 800;
        }
        .weekly-history-text {
            margin-top: 7px;
            color: #d1fae5;
            font-size: 11px;
            line-height: 1.35;
        }
        .timeline-legend {
            margin-top: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .timeline-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 700;
            color: #cbd5e1;
        }
        .timeline-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            display: inline-block;
        }
        .timeline-legend-trained {
            background: #22c55e;
            box-shadow: 0 0 0 1px rgba(34,197,94,.4);
        }
        .timeline-legend-neutral {
            background: #cbd5e1;
            box-shadow: 0 0 0 1px rgba(148,163,184,.65);
        }
        .timeline-legend-pending {
            background: rgba(148,163,184,.5);
            box-shadow: 0 0 0 1px rgba(71,85,105,.7);
        }
        .timeline-grid {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 5px;
        }
        .timeline-weekdays {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 5px;
        }
        .timeline-weekday {
            text-align: center;
            color: #86efac;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .timeline-cell {
            min-height: 28px;
            border-radius: 8px;
            border: 1px solid rgba(148,163,184,.34);
            background: rgba(2,6,23,.56);
            color: #94a3b8;
            font-size: 11px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .timeline-cell-attended,
        .timeline-cell-trained {
            border-color: rgba(34,197,94,.48);
            background: rgba(22,163,74,.34);
            color: #dcfce7;
        }
        .timeline-cell-missed {
            border-color: rgba(148,163,184,.55);
            background: rgba(203,213,225,.2);
            color: #e2e8f0;
        }
        .timeline-cell-neutral {
            border-color: rgba(148,163,184,.55);
            background: rgba(203,213,225,.2);
            color: #e2e8f0;
        }
        .timeline-cell-pending {
            border-style: dashed;
            border-color: rgba(71,85,105,.55);
            background: rgba(15,23,42,.28);
            color: rgba(148,163,184,.72);
        }
        .timeline-cell-today {
            box-shadow: inset 0 0 0 1px rgba(34,211,238,.5);
        }
        .timeline-cell-placeholder {
            border-style: dashed;
            border-color: rgba(71,85,105,.28);
            background: rgba(2,6,23,.22);
            color: transparent;
            box-shadow: none;
            pointer-events: none;
        }
        .weekly-history-insight {
            margin-top: 8px;
            border: 1px solid rgba(56,189,248,.28);
            border-radius: 10px;
            background: rgba(2,6,23,.5);
            padding: 8px 10px;
            color: #dbeafe;
            font-size: 11px;
            line-height: 1.35;
            font-weight: 700;
        }
        .section-card {
            position: relative;
        }
        .section-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 10px;
        }
        .section-toolbar-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .section-toggle-btn {
            border: 1px solid rgba(56,189,248,.36);
            background: rgba(2,6,23,.66);
            color: #cbd5e1;
            border-radius: 9999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
        }
        .section-toggle-btn:active {
            transform: translateY(1px);
        }
        .section-card.is-section-collapsed [data-section-body] {
            display: none;
        }
        .section-card.is-section-collapsed .progress-lock-overlay {
            display: none !important;
        }
        .progress-lock-card {
            position: relative;
            overflow: hidden;
        }
        .progress-lock-content {
            transition: opacity .2s ease, filter .2s ease;
        }
        .progress-lock-card.is-locked .progress-lock-content {
            opacity: .34;
            filter: saturate(.45) blur(1px);
            pointer-events: none;
            user-select: none;
        }
        .progress-lock-overlay {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 14px;
            text-align: center;
            border-radius: inherit;
            background: linear-gradient(155deg, rgba(2,6,23,.9), rgba(15,23,42,.84));
            border: 1px solid rgba(56,189,248,.45);
            box-shadow: inset 0 0 0 1px rgba(34,211,238,.22);
            z-index: 5;
            pointer-events: none;
        }
        .progress-lock-card.is-locked .progress-lock-overlay {
            display: flex;
        }
        .progress-lock-overlay-content {
            max-width: 260px;
            display: grid;
            gap: 6px;
        }
        .progress-lock-title {
            color: #e0f2fe;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .progress-lock-text {
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.45;
            font-weight: 700;
        }
        .training-lockable {
            position: relative;
            border-radius: 12px;
        }
        .training-lockable .progress-lock-overlay {
            border-radius: 12px;
        }
        .live-dot {
            position: relative;
            display: inline-flex;
            width: 10px;
            height: 10px;
            border-radius: 9999px;
            background: #22c55e;
        }
        .live-dot::after {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 9999px;
            background: rgba(34,197,94,.45);
            animation: livePulse 1.8s ease-out infinite;
        }
        .live-count-pop { animation: livePop .3s ease-out; }
        .menu-cta {
            width: 100%;
            border: 0;
            border-radius: 18px;
            padding: 12px 14px;
            text-align: left;
            text-decoration: none;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .005em;
            color: #eff6ff;
            transition: transform .14s ease, box-shadow .2s ease;
            box-shadow: 0 14px 26px rgba(2,6,23,.42);
            min-height: 74px;
            display: flex;
            align-items: center;
            gap: 11px;
        }
        .menu-cta:active { transform: translateY(1px) scale(.996); }
        .menu-checkin {
            background: linear-gradient(120deg, #1d4ed8, #0891b2 60%, #16a34a);
        }
        .menu-progress {
            background: linear-gradient(120deg, #0f172a, #164e63 56%, #15803d);
            border: 1px solid rgba(34,211,238,.35);
        }
        .action-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(226,232,240,.28);
            background: rgba(2,6,23,.34);
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 900;
            flex-shrink: 0;
            letter-spacing: .04em;
        }
        .action-icon {
            width: 20px;
            height: 20px;
            display: block;
        }
        .action-copy {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }
        .action-title {
            color: #f8fafc;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.2;
        }
        .action-hint {
            margin-top: 2px;
            color: rgba(226,232,240,.88);
            font-size: 12px;
            line-height: 1.3;
            font-weight: 600;
        }
        .action-arrow {
            color: rgba(236,253,245,.9);
            font-size: 18px;
            font-weight: 900;
            flex-shrink: 0;
            margin-left: 2px;
        }
        .user-chip:focus-visible,
        .menu-cta:focus-visible,
        .module-action:focus-visible,
        .menu-back:focus-visible,
        .module-input:focus-visible {
            outline: 2px solid rgba(34,197,94,.82);
            outline-offset: 2px;
        }
        .menu-back {
            border: 1px solid rgba(34,211,238,.6);
            background: linear-gradient(130deg, rgba(30,64,175,.82), rgba(14,116,144,.78) 58%, rgba(21,128,61,.72));
            color: #f8fafc;
            border-radius: 15px;
            padding: 9px 14px;
            min-height: 43px;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 14px 26px rgba(2,6,23,.36), inset 0 0 0 1px rgba(255,255,255,.14);
            transition: transform .12s ease, background .18s ease, border-color .18s ease;
            backdrop-filter: blur(4px);
            flex-shrink: 0;
        }
        .menu-back:hover {
            border-color: rgba(125,211,252,.9);
        }
        .menu-back:active {
            transform: translateY(1px) scale(.995);
        }
        .menu-back-icon {
            font-size: 13px;
            line-height: 1;
            color: #dbeafe;
        }
        .menu-back-top {
            margin-right: auto;
        }
        .profile-kpi {
            border: 1px solid rgba(56,189,248,.3);
            background: rgba(2,6,23,.66);
            border-radius: 12px;
            padding: 10px 11px;
        }
        .profile-kpi-label {
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.2;
        }
        .profile-kpi-value {
            margin-top: 4px;
            color: #f8fafc;
            font-size: 15px;
            line-height: 1.25;
            font-weight: 800;
            word-break: break-word;
        }
        .profile-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .profile-edit-toggle {
            border: 1px solid rgba(34,211,238,.48);
            background: rgba(2,6,23,.72);
            color: #e2e8f0;
            border-radius: 10px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            white-space: nowrap;
        }
        .profile-edit-toggle:active {
            transform: translateY(1px);
        }
        .profile-message {
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            line-height: 1.35;
            font-weight: 700;
        }
        .profile-message-success {
            border: 1px solid rgba(34,197,94,.46);
            background: rgba(20,83,45,.36);
            color: #dcfce7;
        }
        .profile-message-error {
            border: 1px solid rgba(248,113,113,.45);
            background: rgba(127,29,29,.35);
            color: #fecaca;
        }
        .profile-edit-panel {
            border: 1px solid rgba(34,211,238,.32);
            background: rgba(2,6,23,.8);
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 14px 32px rgba(0,0,0,.3);
        }
        .profile-edit-title {
            color: #f0fdfa;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: .02em;
        }
        .profile-edit-help {
            margin-top: 4px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.4;
        }
        .profile-field-label {
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 700;
        }
        .profile-field-error {
            margin-top: 4px;
            color: #fca5a5;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.3;
        }
        .profile-photo-preview {
            width: 58px;
            height: 58px;
            border-radius: 9999px;
            border: 1px solid rgba(56,189,248,.42);
            object-fit: cover;
            background: rgba(15,23,42,.8);
            box-shadow: 0 10px 22px rgba(0,0,0,.28);
        }
        .fitness-onboarding-card {
            border: 1px solid rgba(34,211,238,.35);
            background: rgba(2,6,23,.78);
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 14px 34px rgba(0,0,0,.34);
        }
        .fitness-onboarding-title {
            color: #f0fdfa;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: .02em;
        }
        .fitness-onboarding-help {
            margin-top: 6px;
            color: #bfdbfe;
            font-size: 12px;
            line-height: 1.4;
        }
        .fitness-field-label {
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 700;
        }
        .fitness-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        .fitness-chip-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        .fitness-chip {
            position: relative;
            display: block;
        }
        .fitness-chip-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .fitness-chip-label {
            border: 1px solid rgba(56,189,248,.28);
            background: rgba(2,6,23,.7);
            border-radius: 12px;
            color: #dbeafe;
            padding: 10px 10px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.25;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
        }
        .fitness-chip-input:checked + .fitness-chip-label {
            border-color: rgba(34,197,94,.62);
            background: linear-gradient(140deg, rgba(22,163,74,.36), rgba(8,145,178,.18));
            color: #ecfeff;
            box-shadow: inset 0 0 0 1px rgba(134,239,172,.26);
        }
        .fitness-chip.is-invalid .fitness-chip-label {
            border-color: rgba(248,113,113,.82);
            background: rgba(127,29,29,.35);
            color: #fee2e2;
            box-shadow: inset 0 0 0 1px rgba(248,113,113,.42);
        }
        .fitness-inline-help {
            margin-top: 8px;
            color: #bbf7d0;
            font-size: 11px;
            line-height: 1.4;
        }
        .fitness-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }
        .fitness-meta-card {
            border: 1px solid rgba(56,189,248,.28);
            border-radius: 12px;
            background: rgba(2,6,23,.68);
            padding: 10px;
        }
        .fitness-meta-label {
            color: #93c5fd;
            font-size: 11px;
            line-height: 1.2;
        }
        .fitness-meta-value {
            margin-top: 4px;
            color: #f8fafc;
            font-size: 15px;
            line-height: 1.2;
            font-weight: 800;
        }
        .fitness-feature-list {
            margin-top: 8px;
            display: grid;
            gap: 8px;
        }
        .fitness-feature-item {
            border: 1px solid rgba(34,211,238,.24);
            border-radius: 12px;
            background: rgba(15,23,42,.62);
            padding: 10px 11px;
            color: #e2e8f0;
            font-size: 13px;
            line-height: 1.3;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .fitness-feature-icon {
            color: #86efac;
            font-weight: 900;
            width: 14px;
            flex-shrink: 0;
        }
        .fitness-profile-note {
            margin-top: 4px;
            color: #bbf7d0;
            font-size: 11px;
            line-height: 1.35;
        }
        .fitness-modal {
            position: fixed;
            inset: 0;
            z-index: 90;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 16px;
        }
        .fitness-modal.hidden {
            display: none;
        }
        .fitness-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, .82);
            backdrop-filter: blur(2px);
        }
        .fitness-modal-dialog {
            position: relative;
            width: min(100%, 430px);
            max-height: calc(100vh - 32px);
            overflow: auto;
            border-radius: 20px;
        }
        .fitness-modal-close {
            border: 1px solid rgba(56,189,248,.44);
            background: rgba(2,6,23,.72);
            color: #e2e8f0;
            border-radius: 10px;
            min-width: 72px;
            min-height: 36px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .04em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .fitness-modal-close:active {
            transform: translateY(1px);
        }
        @media (max-width: 420px) {
            .fitness-grid-2,
            .fitness-chip-grid,
            .fitness-meta-grid {
                grid-template-columns: 1fr;
            }
            .timeline-grid {
                grid-template-columns: repeat(7, minmax(0, 1fr));
            }
            .timeline-weekdays {
                grid-template-columns: repeat(7, minmax(0, 1fr));
            }
        }
        .period-summary {
            border: 1px solid rgba(34,211,238,.32);
            background: rgba(2,6,23,.74);
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 12px 30px rgba(0,0,0,.32);
        }
        .period-label {
            color: #a5f3fc;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .period-count {
            margin-top: 4px;
            color: #f8fafc;
            font-size: 36px;
            line-height: 1;
            font-weight: 900;
        }
        .period-total-label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
        }
        .period-total-value {
            margin-top: 4px;
            color: #e2e8f0;
            font-size: 24px;
            line-height: 1;
            font-weight: 900;
            text-align: right;
        }
        .period-note {
            margin-top: 8px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.35;
        }
        .period-window {
            margin-top: 5px;
            color: #bbf7d0;
            font-size: 11px;
            font-weight: 700;
        }
        .month-log {
            border: 1px solid rgba(34,211,238,.28);
            background: rgba(2,6,23,.72);
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 12px 30px rgba(0,0,0,.28);
        }
        .month-log-title {
            color: #f0fdfa;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: .03em;
        }
        .month-log-subtitle {
            margin-top: 4px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.35;
        }
        .month-log-counter {
            margin-top: 10px;
            color: #bae6fd;
            font-size: 12px;
            font-weight: 700;
        }
        .month-log-list {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            overflow: visible;
            padding-right: 2px;
        }
        .month-log-list.is-scrollable {
            max-height: 252px;
            overflow: auto;
            padding-right: 4px;
        }
        .month-log-item {
            border: 1px solid rgba(56,189,248,.26);
            background: rgba(15,23,42,.72);
            border-radius: 12px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .month-log-date {
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 700;
        }
        .month-log-time {
            color: #a7f3d0;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .03em;
        }
        .month-log-empty {
            margin-top: 10px;
            border: 1px dashed rgba(71,85,105,.55);
            border-radius: 12px;
            padding: 10px;
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
        }
        .status-box {
            border: 1px solid rgba(100,116,139,.5);
            background: rgba(2,6,23,.72);
            color: #e2e8f0;
            border-radius: 11px;
            padding: 9px 11px;
            font-size: 12px;
        }
        .module-loader,
        .boot-screen {
            position: fixed;
            inset: 0;
            z-index: 60;
        }
        .boot-screen {
            background: #01050b;
            display: grid;
            place-items: center;
            transition: opacity .45s ease;
        }
        .boot-screen.is-finished {
            opacity: 0;
            pointer-events: none;
        }
        .boot-panel {
            width: min(82vw, 340px);
        }
        .boot-title {
            color: #d1fae5;
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 12px;
            text-align: left;
        }
        .boot-track {
            height: 11px;
            border-radius: 9999px;
            border: 1px solid rgba(148,163,184,.45);
            background: rgba(15,23,42,.85);
            overflow: hidden;
        }
        .boot-bar {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #22c55e, #22d3ee);
            box-shadow: 0 0 18px rgba(34,197,94,.55);
            transition: width .12s linear;
        }
        .boot-percent {
            margin-top: 8px;
            color: #bbf7d0;
            font-size: 13px;
            font-weight: 800;
            text-align: right;
        }
        .module-loader {
            z-index: 55;
            display: grid;
            place-items: center;
            background: rgba(0, 0, 0, .64);
            backdrop-filter: blur(1.5px);
        }
        .action-guide-modal {
            position: fixed;
            inset: 0;
            z-index: 58;
            display: grid;
            place-items: center;
            padding: 16px;
        }
        .action-guide-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2,6,23,.76);
            backdrop-filter: blur(2px);
        }
        .action-guide-panel {
            position: relative;
            z-index: 1;
            width: min(100%, 360px);
            border-radius: 18px;
            border: 1px solid rgba(34,211,238,.45);
            background: linear-gradient(145deg, rgba(2,6,23,.95), rgba(8,47,73,.76));
            box-shadow: 0 18px 40px rgba(0,0,0,.45);
            padding: 14px;
            display: grid;
            gap: 10px;
        }
        .action-guide-eyebrow {
            color: #86efac;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .action-guide-title {
            color: #f8fafc;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 900;
        }
        .action-guide-text {
            color: #dbeafe;
            font-size: 13px;
            line-height: 1.45;
            font-weight: 600;
        }
        .action-guide-actions {
            display: grid;
            gap: 8px;
        }
        .guide-focus-target {
            box-shadow: 0 0 0 3px rgba(34,211,238,.48), 0 0 30px rgba(34,197,94,.42);
            animation: guidePulse 1.1s ease-in-out 2;
        }
        @keyframes guidePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .module-loader.hidden,
        .boot-screen.hidden {
            display: none !important;
        }
        .action-guide-modal.hidden {
            display: none !important;
        }
        .module-loader-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: transparent;
            border: 0;
            box-shadow: none;
            padding: 0;
            min-width: 0;
        }
        .module-spinner {
            width: 60px;
            height: 60px;
            margin: 0 auto;
            border-radius: 9999px;
            border: 5px solid rgba(226,232,240,.25);
            border-top-color: #22c55e;
            border-right-color: #22d3ee;
            border-bottom-color: rgba(226,232,240,.45);
            animation: moduleSpin .88s linear infinite;
            box-shadow: 0 0 28px rgba(34,197,94,.30);
        }
        .module-loader-text {
            margin-top: 0;
            color: #ffffff;
            font-weight: 900;
            letter-spacing: .02em;
            text-shadow: 0 2px 12px rgba(0,0,0,.5);
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }
        .loader-dots {
            display: inline-flex;
            min-width: 24px;
            justify-content: flex-start;
        }
        .loader-dot {
            opacity: .2;
            animation: dotStep 1s infinite;
        }
        .loader-dot:nth-child(2) { animation-delay: .2s; }
        .loader-dot:nth-child(3) { animation-delay: .4s; }
        @keyframes dotStep {
            0%, 20% { opacity: .2; }
            30%, 80% { opacity: 1; }
            100% { opacity: .2; }
        }
        video {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(34,211,238,.38);
            background: #020617;
        }
        .module-action {
            border: 1px solid rgba(56,189,248,.35);
            background: rgba(2,6,23,.75);
            color: #e2e8f0;
            border-radius: 12px;
            padding: 10px 12px;
            width: 100%;
            font-weight: 700;
        }
        .module-action[disabled] {
            opacity: .64;
            cursor: not-allowed;
        }
        .module-action-primary {
            border: 0;
            background: linear-gradient(120deg, #1d4ed8, #0891b2 58%, #16a34a);
        }
        .module-action-secondary {
            border: 0;
            background: linear-gradient(120deg, #155e75, #0e7490 58%, #0f766e);
        }
        .module-input {
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(56,189,248,.32);
            background: rgba(2,6,23,.72);
            color: #e2e8f0;
            padding: 10px 12px;
            outline: none;
        }
        .module-input:focus {
            border-color: rgba(34,197,94,.65);
            box-shadow: 0 0 0 3px rgba(34,197,94,.2);
        }
        @keyframes livePulse {
            0% { transform: scale(.4); opacity: .9; }
            100% { transform: scale(1.6); opacity: 0; }
        }
        @keyframes livePop {
            0% { transform: scale(.86); }
            100% { transform: scale(1); }
        }
        @keyframes moduleSpin { to { transform: rotate(360deg); } }
        @media (min-width: 900px) and (pointer:fine) {
            .mobile-shell { display: none; }
            .mobile-guard {
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 24px;
                background: #020617;
                color: #cbd5e1;
            }
        }
    </style>
</head>
<body>
<div class="mobile-guard">
    <div class="max-w-xl rounded-2xl border border-slate-700 bg-slate-900/80 p-6 text-center">
        <h1 class="text-2xl font-black text-white">Interfaz exclusiva para celulares</h1>
        <p class="mt-3 text-sm text-slate-300">Para esta app usa celular y PWA instalada.</p>
    </div>
</div>

<div id="boot-screen" class="boot-screen">
    <div class="boot-panel">
        <p class="boot-title">Cargando...</p>
        <div class="boot-track"><span id="boot-progress-bar" class="boot-bar"></span></div>
        <p class="boot-percent"><span id="boot-progress-value">0</span>%</p>
    </div>
</div>

<div id="module-loader" class="module-loader hidden" aria-hidden="true">
    <div class="module-loader-content">
        <span class="module-spinner" aria-hidden="true"></span>
        <p class="module-loader-text">
            Espere
            <span class="loader-dots" aria-hidden="true">
                <span class="loader-dot">.</span>
                <span class="loader-dot">.</span>
                <span class="loader-dot">.</span>
            </span>
        </p>
    </div>
</div>

<div id="action-guide-modal" class="action-guide-modal hidden" aria-hidden="true">
    <button type="button" class="action-guide-backdrop" data-action-guide-dismiss aria-label="Cerrar guía"></button>
    <article class="action-guide-panel" role="dialog" aria-modal="true" aria-labelledby="action-guide-title">
        <p class="action-guide-eyebrow">Guía rápida</p>
        <h3 id="action-guide-title" class="action-guide-title">Te guiamos paso a paso</h3>
        <p id="action-guide-text" class="action-guide-text">Sigue esta indicación para completar tu entrenamiento de hoy.</p>
        <div class="action-guide-actions">
            <button id="action-guide-cta" type="button" class="module-action module-action-primary">Entendido</button>
            <button id="action-guide-dismiss" type="button" class="module-action">Cerrar</button>
        </div>
    </article>
</div>

<main
    class="mobile-shell px-4 pt-6 pb-6"
    data-screen="{{ $screen }}"
    data-app-url="{{ route('client-mobile.app', ['gymSlug' => $gym->slug]) }}"
    data-checkin-url="{{ route('client-mobile.check-in', ['gymSlug' => $gym->slug]) }}"
    data-progress-url="{{ route('client-mobile.progress', ['gymSlug' => $gym->slug]) }}"
    data-training-start-url="{{ route('client-mobile.training.start', ['gymSlug' => $gym->slug]) }}"
    data-training-finish-url="{{ route('client-mobile.training.finish', ['gymSlug' => $gym->slug]) }}"
    data-push-status-url="{{ route('client-mobile.push.status', ['gymSlug' => $gym->slug]) }}"
    data-push-subscribe-url="{{ route('client-mobile.push.subscribe', ['gymSlug' => $gym->slug]) }}"
    data-push-unsubscribe-url="{{ route('client-mobile.push.unsubscribe', ['gymSlug' => $gym->slug]) }}"
    data-push-vapid-key="{{ $webPushPublicKey }}"
>
    @php
        $clientFullName = (string) $client->full_name;
        $clientDocument = trim((string) ($client->document_number ?? ''));
        $clientUsername = trim((string) ($client->app_username ?? ''));
        $clientPhone = trim((string) ($client->phone ?? ''));
        $clientPhotoPath = trim((string) ($client->photo_path ?? ''));
        $clientPhotoUrl = '';
        if ($clientPhotoPath !== '') {
            if (str_starts_with($clientPhotoPath, 'http://') || str_starts_with($clientPhotoPath, 'https://')) {
                $clientPhotoUrl = $clientPhotoPath;
            } else {
                $clientPhotoUrl = asset('storage/'.ltrim($clientPhotoPath, '/'));
            }
        }
        $lastAttendanceDate = trim((string) ($progress['last_attendance_date'] ?? ''));
        $lastAttendanceTime = trim((string) ($progress['last_attendance_time'] ?? ''));
        $lastAttendanceLabel = '-';
        if ($lastAttendanceDate !== '' && $lastAttendanceTime !== '') {
            $lastAttendanceLabel = $lastAttendanceDate.' '.$lastAttendanceTime;
        } elseif ($lastAttendanceDate !== '') {
            $lastAttendanceLabel = $lastAttendanceDate;
        }
        $profileFormHasErrors = $errors->has('profile')
            || $errors->has('current_password')
            || $errors->has('phone')
            || $errors->has('new_password')
            || $errors->has('photo');
        $profileEditOpen = old('_profile_form') === '1' || $profileFormHasErrors;
        $clientNameParts = preg_split('/\s+/', trim($clientFullName)) ?: [];
        $clientInitials = '';
        foreach (array_slice($clientNameParts, 0, 2) as $part) {
            $clientInitials .= mb_strtoupper(mb_substr((string) $part, 0, 1));
        }
        $clientInitials = $clientInitials !== '' ? $clientInitials : 'C';
        $membershipStatusRaw = (string) ($progress['membership_status'] ?? '');
        $membershipStatusLabel = match (mb_strtolower(trim($membershipStatusRaw))) {
            'active' => 'activo',
            'inactive' => 'inactivo',
            'paused' => 'pausado',
            'expired' => 'vencido',
            'pending' => 'pendiente',
            default => $membershipStatusRaw !== '' ? $membershipStatusRaw : '-',
        };
        $fitnessProfileModel = $fitnessProfile ?? null;
        $fitnessGoalOptions = [
            'ganar_musculo' => 'Ganar musculo',
            'perder_grasa' => 'Perder grasa',
            'mantener_forma' => 'Mantener forma',
            'definir' => 'Definir',
            'aumentar_fuerza' => 'Aumentar fuerza',
            'mejorar_resistencia' => 'Mejorar resistencia',
        ];
        $fitnessLevelOptions = [
            'principiante' => 'Principiante',
            'intermedio' => 'Intermedio',
            'avanzado' => 'Avanzado',
        ];
        $fitnessSexOptions = [
            'masculino' => 'Masculino',
            'femenino' => 'Femenino',
            'otro' => 'Otro',
        ];
        $fitnessLimitationsOptions = [
            'ninguna' => 'Ninguna',
            'rodilla' => 'Rodilla',
            'espalda' => 'Espalda',
            'hombro' => 'Hombro',
            'codo' => 'Codo',
            'cuello' => 'Cuello',
            'tobillo' => 'Tobillo',
        ];
        $fitnessLimitations = is_array($fitnessProfileModel?->limitations ?? null)
            ? array_values(array_filter($fitnessProfileModel->limitations, static fn ($item): bool => trim((string) $item) !== ''))
            : [];
        if ($fitnessLimitations === []) {
            $fitnessLimitations = ['ninguna'];
        }
        $fitnessProfileCompleted = (bool) ($fitnessProfileCompleted ?? false);
        if (! $fitnessProfileCompleted) {
            $fitnessProfileCompleted = $fitnessProfileModel !== null
                && trim((string) ($fitnessProfileModel->onboarding_completed_at ?? '')) !== '';
        }
        $showFitnessModal = ! $fitnessProfileCompleted
            && ((bool) ($openFitnessModal ?? false) || old('_fitness_modal') === '1');
        $fitnessFormHasErrors = $errors->has('age')
            || $errors->has('sex')
            || $errors->has('height_cm')
            || $errors->has('weight_kg')
            || $errors->has('goal')
            || $errors->has('experience_level')
            || $errors->has('days_per_week')
            || $errors->has('session_minutes')
            || $errors->has('limitations')
            || $errors->has('limitations.*');
        $fitnessEditOpen = ! $fitnessProfileCompleted || old('_fitness_form') === '1' || $fitnessFormHasErrors;
        $formatMetric = static function (mixed $value, string $unit = ''): string {
            if ($value === null || $value === '' || ! is_numeric($value)) {
                return '-';
            }

            $formatted = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
            return $unit !== '' ? $formatted.' '.$unit : $formatted;
        };
        $fitnessGoalLabel = $fitnessGoalOptions[(string) ($fitnessProfileModel?->goal ?? '')] ?? '-';
        $fitnessLevelLabel = $fitnessLevelOptions[(string) ($fitnessProfileModel?->experience_level ?? '')] ?? '-';
        $fitnessSexLabel = $fitnessSexOptions[(string) ($fitnessProfileModel?->sex ?? '')] ?? '-';
        $fitnessDaysLabel = $fitnessProfileModel?->days_per_week ? ((int) $fitnessProfileModel->days_per_week).' días/semana' : '-';
        $fitnessMinutesLabel = $fitnessProfileModel?->session_minutes ? ((int) $fitnessProfileModel->session_minutes).' min/sesión' : '-';
        $fitnessLimitationsLabel = collect($fitnessLimitations)
            ->map(static fn ($item) => $fitnessLimitationsOptions[(string) $item] ?? ucfirst((string) $item))
            ->implode(', ');
        $fitnessUpdatedLabel = trim((string) ($fitnessProfileModel?->updated_at?->format('Y-m-d H:i') ?? ''));
        $fitnessBodyMetrics = is_array($fitnessProfileModel?->body_metrics ?? null)
            ? $fitnessProfileModel->body_metrics
            : [];
        $fitnessBmiValue = $formatMetric($fitnessBodyMetrics['bmi'] ?? null);
        $fitnessBmiCategory = trim((string) ($fitnessBodyMetrics['bmi_category'] ?? ''));
        if ($fitnessBmiCategory === '') {
            $fitnessBmiCategory = '-';
        }
        $fitnessBmrValue = $formatMetric($fitnessBodyMetrics['bmr_kcal'] ?? null, 'kcal');
        $fitnessMaintenanceValue = $formatMetric($fitnessBodyMetrics['maintenance_kcal'] ?? null, 'kcal');
        $fitnessTargetCaloriesValue = $formatMetric($fitnessBodyMetrics['target_kcal'] ?? null, 'kcal');
        $fitnessBodyFatValue = $formatMetric($fitnessBodyMetrics['estimated_body_fat_pct'] ?? null, '%');
        $fitnessGoalTrackLabel = trim((string) ($fitnessBodyMetrics['goal_track'] ?? ''));
        $hasBodyMetrics = is_numeric($fitnessBodyMetrics['bmi'] ?? null)
            || is_numeric($fitnessBodyMetrics['bmr_kcal'] ?? null)
            || is_numeric($fitnessBodyMetrics['target_kcal'] ?? null);
        $progressPrediction = is_array($progress['prediction'] ?? null) ? $progress['prediction'] : [];
        $predictionRhythmLabel = trim((string) ($progressPrediction['rhythm_label'] ?? 'Sin datos'));
        $predictionPrimaryLine = trim((string) ($progressPrediction['primary_line'] ?? 'Completa tus datos físicos para activar tu predicción.'));
        $predictionSecondaryLine = trim((string) ($progressPrediction['secondary_line'] ?? 'Registra asistencias para mejorar la precisión.'));
        $predictionContextLine = trim((string) ($progressPrediction['context_line'] ?? 'Sin datos de progreso todavía.'));
        $predictionConsistencyPct = (int) ($progressPrediction['consistency_percent'] ?? 0);
        $progressBodyState = is_array($progress['body_state'] ?? null) ? $progress['body_state'] : [];
        $bodyStateForce = max(0, min(100, (int) ($progressBodyState['force'] ?? 0)));
        $bodyStateResistance = max(0, min(100, (int) ($progressBodyState['resistance'] ?? 0)));
        $bodyStateDiscipline = max(0, min(100, (int) ($progressBodyState['discipline'] ?? 0)));
        $bodyStateRecovery = max(0, min(100, (int) ($progressBodyState['recovery'] ?? 0)));
        $bodyStateSummaryLine = trim((string) ($progressBodyState['summary_line'] ?? 'Sin datos para estado corporal.'));
        $bodyStateContextLine = trim((string) ($progressBodyState['context_line'] ?? 'Registra más entrenamientos para estimar este estado.'));
        $progressTrainingPlan = is_array($progress['training_plan'] ?? null) ? $progress['training_plan'] : [];
        $trainingTitle = trim((string) ($progressTrainingPlan['title'] ?? 'Entrenamiento de hoy'));
        $trainingObjectiveLine = trim((string) ($progressTrainingPlan['objective_line'] ?? 'Sin objetivo disponible.'));
        $trainingFocusLine = trim((string) ($progressTrainingPlan['focus_line'] ?? 'Sin enfoque disponible.'));
        $trainingRhythmLine = trim((string) ($progressTrainingPlan['rhythm_line'] ?? 'Sin ritmo configurado.'));
        $trainingAdaptationLine = trim((string) ($progressTrainingPlan['adaptation_line'] ?? 'Sin ajustes por ahora.'));
        $trainingContextLine = trim((string) ($progressTrainingPlan['context_line'] ?? 'Sin contexto disponible.'));
        $trainingExercises = is_array($progressTrainingPlan['exercises'] ?? null) ? $progressTrainingPlan['exercises'] : [];
        $trainingStatus = is_array($progress['training_status'] ?? null) ? $progress['training_status'] : [];
        $trainingCanStart = (bool) ($trainingStatus['can_start'] ?? false);
        $trainingCanFinish = (bool) ($trainingStatus['can_finish'] ?? false);
        $trainingIsActive = (bool) ($trainingStatus['is_active'] ?? false);
        $progressUnlocked = (bool) ($trainingStatus['progress_unlocked'] ?? false);
        $progressLockReason = trim((string) ($trainingStatus['lock_reason'] ?? 'Inicia tu entrenamiento de hoy para desbloquear el panel.'));
        if ($progressLockReason === '') {
            $progressLockReason = 'Inicia tu entrenamiento de hoy para desbloquear el panel.';
        }
        $trainingStatusLabel = trim((string) ($trainingStatus['status_label'] ?? 'Registra asistencia para habilitar entrenamiento.'));
        $trainingHintLine = trim((string) ($trainingStatus['hint_line'] ?? 'Escanea tu asistencia y luego inicia tu entrenamiento.'));
        $trainingRemainingSeconds = max(0, (int) ($trainingStatus['remaining_seconds'] ?? 0));
        $trainingTimerLabel = $trainingIsActive
            ? sprintf(
                '%02d:%02d',
                (int) floor($trainingRemainingSeconds / 60),
                $trainingRemainingSeconds % 60
            )
            : '--:--';
        $personalMessage = is_array($progress['personal_message'] ?? null) ? $progress['personal_message'] : [];
        $personalMessageTag = trim((string) ($personalMessage['tag'] ?? 'Mensaje personal'));
        $personalMessageLine1 = trim((string) ($personalMessage['line_1'] ?? 'Completa tus datos para activar tu mensaje personal.'));
        $personalMessageLine2 = trim((string) ($personalMessage['line_2'] ?? 'Tu progreso diario aparecerá aquí.'));
        $personalMessageContext = trim((string) ($personalMessage['context_line'] ?? 'Mensaje generado según tu actividad reciente.'));
        $weeklyGoalSummary = is_array($progress['weekly_goal'] ?? null) ? $progress['weekly_goal'] : [];
        $weeklyGoalTarget = max(0, min(7, (int) ($weeklyGoalSummary['target'] ?? 3)));
        $weeklyGoalConfiguredTarget = max(3, min(7, (int) ($weeklyGoalSummary['configured_target'] ?? $weeklyGoalTarget)));
        $weeklyGoalVisits = max(0, (int) ($weeklyGoalSummary['visits'] ?? 0));
        $weeklyGoalRemaining = max(0, (int) ($weeklyGoalSummary['remaining'] ?? max(0, $weeklyGoalTarget - $weeklyGoalVisits)));
        $weeklyGoalCompletion = max(0, min(100, (int) ($weeklyGoalSummary['completion_percent'] ?? 0)));
        $weeklyGoalDaysLeft = max(0, (int) ($weeklyGoalSummary['days_left_week'] ?? 0));
        $weeklyCommitmentLine = trim((string) ($weeklyGoalSummary['commitment_line'] ?? ''));
        if ($weeklyCommitmentLine === '') {
            $weeklyCommitmentLine = $weeklyGoalVisits >= $weeklyGoalTarget
                ? 'Has asistido los '.$weeklyGoalTarget.' días que prometiste. Excelente.'
                : 'Esta semana asististe '.$weeklyGoalVisits.' de los '.$weeklyGoalTarget.' días que prometiste.';
        }
        $weeklyRestLine = trim((string) ($weeklyGoalSummary['rest_line'] ?? ''));
        if ($weeklyRestLine === '') {
            $weeklyRestLine = 'Días de descanso planificados: '.max(0, 7 - $weeklyGoalTarget).'.';
        }
        $weeklyGoalAlerts = is_array($weeklyGoalSummary['alerts'] ?? null) ? $weeklyGoalSummary['alerts'] : [];
        if ($weeklyGoalAlerts === []) {
            $weeklyGoalAlerts = [[
                'type' => 'info',
                'text' => 'Sin alertas por ahora.',
            ]];
        }
        $weeklyTimeline = is_array($progress['last30_timeline'] ?? null) ? $progress['last30_timeline'] : [];
        $timelineMonthLabel = trim((string) ($progress['month_label'] ?? ''));
        if ($timelineMonthLabel === '') {
            $timelineMonthLabel = '-';
        }
        $weeklyGoalFormOpen = old('_weekly_goal_form') === '1'
            || $errors->has('weekly_goal')
            || $errors->has('weekly_goal_profile');
        $weeklyGoalSelectedValue = (string) old('weekly_goal', $weeklyGoalConfiguredTarget);
    @endphp
    <section class="mx-auto max-w-md space-y-4 relative z-10">
        <div class="top-user-menu">
            @if ($screen !== 'home')
                <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'home']) }}" class="menu-back menu-back-top" aria-label="Volver al inicio">
                    <span class="menu-back-icon" aria-hidden="true">&larr;</span>
                    <span>Atrás</span>
                </a>
            @endif
            <button id="user-menu-toggle" type="button" class="user-chip" aria-haspopup="menu" aria-expanded="false" aria-controls="user-menu-panel">
                <span class="user-avatar">
                    @if ($clientPhotoUrl !== '')
                        <img src="{{ $clientPhotoUrl }}" alt="Foto de perfil" class="user-avatar-image">
                    @else
                        {{ $clientInitials }}
                    @endif
                </span>
                <span class="user-chip-name">{{ $clientFullName }}</span>
                <span class="user-chip-caret">&#9662;</span>
            </button>
            <div id="user-menu-panel" class="user-dropdown hidden" role="menu" aria-hidden="true">
                <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'profile']) }}" class="user-dropdown-item user-dropdown-link" role="menuitem">Ver perfil</a>
                <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'physical']) }}" class="user-dropdown-item user-dropdown-link" role="menuitem">Datos físicos</a>
                <form id="client-mobile-logout-form" method="POST" action="{{ route('client-mobile.logout', ['gymSlug' => $gym->slug]) }}">
                    @csrf
                    <button type="submit" class="user-dropdown-item user-dropdown-logout" role="menuitem">Cerrar sesión</button>
                </form>
            </div>
        </div>
        <header class="hero-card home-clean home-welcome rounded-3xl p-4">
            <p class="text-xs font-black uppercase tracking-[.18em] text-emerald-100">{{ (string) $gym->name }}</p>
            <h1 class="mt-1 text-xl font-black text-white">Hola, {{ (string) $client->full_name }}</h1>
            <p class="mt-1 text-xs text-emerald-100/90">Listo para entrenar. Elige una opción para continuar.</p>
        </header>

        @if ($screen === 'home')
            <section id="home-view" class="home-stage">
                <article class="glass-card home-clean home-intro rounded-3xl p-4">
                    <h2 class="home-intro-title">¿Qué deseas hacer hoy?</h2>
                    <p class="home-intro-text">Registra tu asistencia o revisa tu rendimiento en el gimnasio.</p>
                </article>

                <article class="personal-message-card">
                    <p class="personal-message-tag">{{ $personalMessageTag }}</p>
                    <p class="personal-message-line">{{ $personalMessageLine1 }}</p>
                    <p class="personal-message-line">{{ $personalMessageLine2 }}</p>
                    <p class="personal-message-context">{{ $personalMessageContext }}</p>
                </article>

                <article id="client-push-card" class="glass-card rounded-3xl p-4 space-y-2">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[.14em] text-cyan-100">Alertas de meta semanal</p>
                            <p id="client-push-status" class="text-[11px] text-slate-300">Verificando notificaciones...</p>
                        </div>
                        <button id="client-push-toggle" type="button" class="module-action module-action-secondary">Activar</button>
                    </div>
                    <p class="text-[11px] text-slate-300">Recibe avisos cuando tu objetivo semanal esté en riesgo o cuando lo completes.</p>
                </article>

                <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'checkin']) }}" class="menu-cta menu-checkin home-btn" aria-label="Registrar asistencia con QR o código">
                    <span class="action-badge" aria-hidden="true">
                        <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3.5" y="3.5" width="6" height="6" rx="1"></rect>
                            <rect x="14.5" y="3.5" width="6" height="6" rx="1"></rect>
                            <rect x="3.5" y="14.5" width="6" height="6" rx="1"></rect>
                            <path d="M15 15h1.5v1.5H15z"></path>
                            <path d="M18 15h2.5v2.5H18z"></path>
                            <path d="M15 18h2.5v2.5H15z"></path>
                            <path d="M19 19h1.5v1.5H19z"></path>
                        </svg>
                    </span>
                    <span class="action-copy">
                        <span class="action-title">Registrar asistencia</span>
                        <span class="action-hint">Escanea QR o escribe tu código</span>
                    </span>
                    <span class="action-arrow" aria-hidden="true">&rsaquo;</span>
                </a>

                @if ($fitnessProfileCompleted)
                    <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'progress']) }}" class="menu-cta menu-progress home-btn" aria-label="Ver mi rendimiento en el gimnasio">
                        <span class="action-badge" aria-hidden="true">
                            <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 10v4"></path>
                                <path d="M7 8v8"></path>
                                <path d="M9 12h6"></path>
                                <path d="M15 8v8"></path>
                                <path d="M18 10v4"></path>
                            </svg>
                        </span>
                        <span class="action-copy">
                            <span class="action-title">Ver mi rendimiento</span>
                            <span class="action-hint">Estado de membresía y visitas</span>
                        </span>
                        <span class="action-arrow" aria-hidden="true">&rsaquo;</span>
                    </a>
                @else
                    <button id="open-fitness-modal-trigger" type="button" class="menu-cta menu-progress home-btn" aria-label="Completar datos físicos para ver rendimiento">
                        <span class="action-badge" aria-hidden="true">
                            <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 10v4"></path>
                                <path d="M7 8v8"></path>
                                <path d="M9 12h6"></path>
                                <path d="M15 8v8"></path>
                                <path d="M18 10v4"></path>
                            </svg>
                        </span>
                        <span class="action-copy">
                            <span class="action-title">Ver mi rendimiento</span>
                            <span class="action-hint">Primero completa tus datos físicos</span>
                        </span>
                        <span class="action-arrow" aria-hidden="true">&rsaquo;</span>
                    </button>
                @endif
            </section>
        @endif

        @if ($screen === 'checkin')
            <section id="checkin-view" class="space-y-4">
                @php
                    $monthEntries = is_array($progress['month_entries'] ?? null) ? $progress['month_entries'] : [];
                @endphp
                <article class="glass-card rounded-3xl p-4 space-y-3">
                    <div class="flex items-center">
                        <h2 class="text-sm font-black uppercase tracking-[.16em] text-cyan-100">Registrar asistencia</h2>
                    </div>
                    <p class="text-xs text-slate-300">Escanea el QR de recepción o escribe el código manual.</p>

                    <video id="scan-video" playsinline muted class="hidden"></video>

                    <div class="flex gap-2">
                        <button id="start-scan" type="button" class="module-action module-action-primary">Escanear QR</button>
                        <button id="stop-scan" type="button" class="module-action hidden">Detener</button>
                    </div>

                    <label class="block space-y-1 text-sm">
                        <span class="text-slate-300">Código manual (fallback)</span>
                        <input id="manual-token" type="text" class="module-input" placeholder="Pega token o contenido QR">
                    </label>
                    <button id="send-manual" type="button" class="module-action module-action-secondary">Validar código</button>
                    <p id="checkin-status" class="status-box">{{ __('messages.client_mobile.ready_to_scan') }}</p>
                </article>

                <article class="period-summary">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="period-label">Asistencias del período</p>
                            <p id="period-visits" class="period-count">{{ (int) ($progress['period_visits'] ?? 0) }}</p>
                        </div>
                        <div>
                            <p class="period-total-label">Total histórico</p>
                            <p id="period-total" class="period-total-value">{{ (int) ($progress['total_visits'] ?? 0) }}</p>
                        </div>
                    </div>
                    <p class="period-note">Las asistencias solo duran el tiempo que te suscribiste.</p>
                    <p id="period-window-label" class="period-window">Período: {{ (string) ($progress['period_window_label'] ?? 'Sin membresía activa') }}</p>
                </article>

                <article class="month-log">
                    <h3 id="month-log-title" class="month-log-title">Asistencias de {{ (string) ($progress['month_label'] ?? '-') }}</h3>
                    <p class="month-log-subtitle">Se muestran unicamente fecha y hora del mes actual.</p>
                    <p class="month-log-counter">Registros del mes: <span id="month-attendance-count">{{ count($monthEntries) }}</span></p>
                    <ul id="month-attendance-list" class="month-log-list {{ count($monthEntries) > 5 ? 'is-scrollable' : '' }}">
                        @foreach ($monthEntries as $entry)
                            @php
                                $entryDate = trim((string) ($entry['date'] ?? ''));
                                $entryTime = trim((string) ($entry['time'] ?? ''));
                            @endphp
                            @if ($entryDate !== '')
                                <li class="month-log-item">
                                    <span class="month-log-date">{{ $entryDate }}</span>
                                    <span class="month-log-time">{{ $entryTime !== '' ? $entryTime : '--:--:--' }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <p id="month-attendance-empty" class="month-log-empty {{ count($monthEntries) > 0 ? 'hidden' : '' }}">Todavia no registras asistencias en este mes.</p>
                </article>
            </section>
        @endif

        @if ($screen === 'progress')
            <section id="progress-view" class="space-y-4">
                <article class="glass-card rounded-3xl p-4">
                    <div class="flex items-center">
                        <h2 class="text-sm font-black uppercase tracking-[.16em] text-cyan-100">Mi rendimiento</h2>
                    </div>
                    <p class="mt-2 text-xs text-slate-300">Estado de tu membresía y progreso en el gimnasio.</p>
                </article>

                <article class="live-mobile-card rounded-3xl p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-2">
                            <span class="live-dot" aria-hidden="true"></span>
                            <p class="text-xs font-black uppercase tracking-[.18em] text-emerald-100">Presentes</p>
                        </div>
                        <p class="text-[11px] font-semibold text-emerald-100/90">ahora</p>
                    </div>
                    <div class="mt-2 flex items-end gap-2">
                        <p class="text-4xl font-black leading-none text-white" id="live-clients-count">{{ (int) ($progress['live_clients_count'] ?? 0) }}</p>
                        <p class="pb-1 text-xs font-semibold text-emerald-100/90">en tu gimnasio</p>
                    </div>
                    <p class="mt-2 text-[11px] text-emerald-100/80" id="live-clients-window">Conteo de {{ (string) ($progress['live_window_label'] ?? 'En vivo') }}. Actualiza automático.</p>
                </article>

                @if (session('goal_status'))
                    <p class="profile-message profile-message-success">{{ (string) session('goal_status') }}</p>
                @endif

                <article class="prediction-card progress-lock-card section-card {{ $progressUnlocked ? '' : 'is-locked' }}" data-progress-lock-card data-section-card="prediction">
                    <div class="section-toolbar">
                        <p class="prediction-title">Prediccion de progreso</p>
                        <button type="button" class="section-toggle-btn" data-section-toggle="prediction" aria-expanded="true">Ocultar</button>
                    </div>
                    <div class="progress-lock-content" data-section-body>
                        <p id="prediction-rhythm" class="prediction-rhythm">{{ $predictionRhythmLabel }} | Constancia: {{ $predictionConsistencyPct }}%</p>
                        <p id="prediction-primary" class="prediction-line">{{ $predictionPrimaryLine }}</p>
                        <p id="prediction-secondary" class="prediction-line">{{ $predictionSecondaryLine }}</p>
                        <p id="prediction-context" class="prediction-context">{{ $predictionContextLine }}</p>
                    </div>
                    <div class="progress-lock-overlay" aria-hidden="{{ $progressUnlocked ? 'true' : 'false' }}">
                        <div class="progress-lock-overlay-content">
                            <p class="progress-lock-title">Progreso en espera</p>
                            <p class="progress-lock-text" data-progress-lock-message>{{ $progressLockReason }}</p>
                        </div>
                    </div>
                </article>

                <article class="weekly-goal-card progress-lock-card section-card {{ $progressUnlocked ? '' : 'is-locked' }}" data-progress-lock-card data-section-card="weekly-goal">
                    <div class="section-toolbar">
                        <p class="weekly-goal-title">Meta semanal</p>
                        <div class="section-toolbar-actions">
                            <button id="open-weekly-goal-edit" type="button" class="profile-edit-toggle">Ajustar objetivo</button>
                            <button type="button" class="section-toggle-btn" data-section-toggle="weekly-goal" aria-expanded="true">Ocultar</button>
                        </div>
                    </div>
                    <div class="progress-lock-content" data-section-body>
                        <p id="weekly-goal-summary" class="weekly-goal-summary">{{ $weeklyGoalVisits }} de {{ $weeklyGoalTarget }} sesiones esta semana.</p>
                        <div class="weekly-progress-track" aria-hidden="true">
                            <span id="weekly-goal-progress-fill" class="weekly-progress-fill" style="width: {{ $weeklyGoalCompletion }}%;"></span>
                        </div>
                        <div class="weekly-progress-meta">
                            <span id="weekly-goal-progress-label">Completado: {{ $weeklyGoalCompletion }}%</span>
                            <span id="weekly-goal-remaining-label">Faltan: {{ $weeklyGoalRemaining }}</span>
                            <span id="weekly-goal-days-left-label">Dias restantes: {{ $weeklyGoalDaysLeft }}</span>
                        </div>

                        <div id="weekly-alert-list" class="weekly-alert-list">
                            @foreach ($weeklyGoalAlerts as $alert)
                                @php
                                    $alertTypeRaw = mb_strtolower(trim((string) ($alert['type'] ?? 'info')));
                                    $alertType = in_array($alertTypeRaw, ['info', 'success', 'warning', 'danger'], true) ? $alertTypeRaw : 'info';
                                    $alertText = trim((string) ($alert['text'] ?? 'Sin alertas por ahora.'));
                                @endphp
                                <p class="weekly-alert-item weekly-alert-{{ $alertType }}">{{ $alertText !== '' ? $alertText : 'Sin alertas por ahora.' }}</p>
                            @endforeach
                        </div>

                        <div id="weekly-goal-edit-panel" class="weekly-goal-edit-panel {{ $weeklyGoalFormOpen ? '' : 'hidden' }}">
                            @if ($errors->has('weekly_goal_profile'))
                                <p class="profile-message profile-message-error">{{ (string) $errors->first('weekly_goal_profile') }}</p>
                            @endif
                            <form method="POST" action="{{ route('client-mobile.weekly-goal.update', ['gymSlug' => $gym->slug]) }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="_weekly_goal_form" value="1">

                                <label class="block space-y-1 text-sm">
                                    <span class="profile-field-label">Objetivo semanal de entrenamientos</span>
                                    <select name="weekly_goal" class="module-input">
                                        @foreach ([3, 4, 5, 6, 7] as $goalOption)
                                            @php
                                                $goalOptionValue = (string) $goalOption;
                                            @endphp
                                            <option value="{{ $goalOptionValue }}" {{ $weeklyGoalSelectedValue === $goalOptionValue ? 'selected' : '' }}>{{ $goalOptionValue }} días por semana</option>
                                        @endforeach
                                    </select>
                                    @error('weekly_goal')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
                                </label>

                                <div class="flex gap-2">
                                    <button type="submit" class="module-action module-action-primary">Guardar meta</button>
                                    <button id="close-weekly-goal-edit" type="button" class="module-action">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="progress-lock-overlay" aria-hidden="{{ $progressUnlocked ? 'true' : 'false' }}">
                        <div class="progress-lock-overlay-content">
                            <p class="progress-lock-title">Progreso en espera</p>
                            <p class="progress-lock-text" data-progress-lock-message>{{ $progressLockReason }}</p>
                        </div>
                    </div>
                </article>

                <article class="weekly-history-card progress-lock-card section-card {{ $progressUnlocked ? '' : 'is-locked' }}" data-progress-lock-card data-section-card="weekly-history">
                    <div class="section-toolbar">
                        <p class="weekly-history-title">Histórico días de suscripción</p>
                        <button type="button" class="section-toggle-btn" data-section-toggle="weekly-history" aria-expanded="true">Ocultar</button>
                    </div>
                    <div class="progress-lock-content" data-section-body>
                        <p id="timeline-month-label" class="weekly-history-month">Mes actual: {{ $timelineMonthLabel }}</p>
                        <p class="weekly-history-text">Cada celda representa un día. Verde: entrenado, gris claro: descanso o falta, opaco: aún no marcado.</p>
                        <div class="timeline-legend" aria-label="Leyenda de estados">
                            <span class="timeline-legend-item"><span class="timeline-legend-dot timeline-legend-trained" aria-hidden="true"></span>Entrenado</span>
                            <span class="timeline-legend-item"><span class="timeline-legend-dot timeline-legend-neutral" aria-hidden="true"></span>Descanso o falta</span>
                            <span class="timeline-legend-item"><span class="timeline-legend-dot timeline-legend-pending" aria-hidden="true"></span>Aún no marcado</span>
                        </div>
                        <div class="timeline-weekdays" aria-hidden="true">
                            @foreach (['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $weekdayLabel)
                                <span class="timeline-weekday">{{ $weekdayLabel }}</span>
                            @endforeach
                        </div>
                        <div id="timeline-grid" class="timeline-grid">
                            @foreach ($weeklyTimeline as $timelineItem)
                                @php
                                    $timelineLabel = trim((string) ($timelineItem['label'] ?? '--'));
                                    $timelineDate = trim((string) ($timelineItem['date'] ?? ''));
                                    $timelineWeekday = trim((string) ($timelineItem['weekday_short'] ?? ''));
                                    $timelineStatusRaw = mb_strtolower(trim((string) ($timelineItem['status'] ?? 'rest')));
                                    $timelineStatus = in_array($timelineStatusRaw, ['trained', 'rest', 'missed', 'pending'], true)
                                        ? $timelineStatusRaw
                                        : 'rest';
                                    $timelineIsToday = (bool) ($timelineItem['is_today'] ?? false);
                                    $timelineIsPlaceholder = (bool) ($timelineItem['is_placeholder'] ?? false);
                                    $timelineStatusLabel = match ($timelineStatus) {
                                        'trained' => 'entrenado',
                                        'missed' => 'faltaste',
                                        'pending' => 'aún no marcado',
                                        default => 'descanso',
                                    };
                                    $timelineDisplayLabel = $timelineIsPlaceholder ? ' ' : $timelineLabel;
                                    $timelineTitle = trim(($timelineWeekday !== '' ? $timelineWeekday.' ' : '').$timelineDate.' - '.$timelineStatusLabel.($timelineIsToday ? ' (hoy)' : ''));
                                @endphp
                                <span
                                    class="timeline-cell {{ $timelineIsPlaceholder ? 'timeline-cell-placeholder' : '' }} {{ (! $timelineIsPlaceholder && $timelineStatus === 'trained') ? 'timeline-cell-trained' : '' }} {{ (! $timelineIsPlaceholder && $timelineStatus === 'pending') ? 'timeline-cell-pending' : '' }} {{ (! $timelineIsPlaceholder && in_array($timelineStatus, ['rest', 'missed'], true)) ? 'timeline-cell-neutral' : '' }} {{ (! $timelineIsPlaceholder && $timelineIsToday) ? 'timeline-cell-today' : '' }}"
                                    @if (! $timelineIsPlaceholder)
                                        title="{{ $timelineTitle }}"
                                        aria-label="{{ $timelineTitle }}"
                                    @else
                                        aria-hidden="true"
                                    @endif
                                >{{ $timelineDisplayLabel }}</span>
                            @endforeach
                        </div>
                        <p id="timeline-week-commitment" class="weekly-history-insight">{{ $weeklyCommitmentLine }}</p>
                        <p id="timeline-week-rest" class="weekly-history-insight">{{ $weeklyRestLine }}</p>
                    </div>
                    <div class="progress-lock-overlay" aria-hidden="{{ $progressUnlocked ? 'true' : 'false' }}">
                        <div class="progress-lock-overlay-content">
                            <p class="progress-lock-title">Progreso en espera</p>
                            <p class="progress-lock-text" data-progress-lock-message>{{ $progressLockReason }}</p>
                        </div>
                    </div>
                </article>

                <article class="body-state-card progress-lock-card section-card {{ $progressUnlocked ? '' : 'is-locked' }}" data-progress-lock-card data-section-card="body-state">
                    <div class="section-toolbar">
                        <p class="body-state-title">Estado del cuerpo</p>
                        <button type="button" class="section-toggle-btn" data-section-toggle="body-state" aria-expanded="true">Ocultar</button>
                    </div>
                    <div class="progress-lock-content" data-section-body>
                        <p id="body-state-summary" class="body-state-summary">{{ $bodyStateSummaryLine }}</p>
                        <div class="body-state-grid">
                            <div class="body-state-row">
                                <span class="body-state-label">Fuerza</span>
                                <span class="body-state-track" aria-hidden="true">
                                    <span id="body-state-force-bar" class="body-state-fill body-state-fill-force" style="width: {{ $bodyStateForce }}%;"></span>
                                </span>
                                <span id="body-state-force-value" class="body-state-value">{{ $bodyStateForce }}</span>
                            </div>
                            <div class="body-state-row">
                                <span class="body-state-label">Resistencia</span>
                                <span class="body-state-track" aria-hidden="true">
                                    <span id="body-state-resistance-bar" class="body-state-fill body-state-fill-resistance" style="width: {{ $bodyStateResistance }}%;"></span>
                                </span>
                                <span id="body-state-resistance-value" class="body-state-value">{{ $bodyStateResistance }}</span>
                            </div>
                            <div class="body-state-row">
                                <span class="body-state-label">Disciplina</span>
                                <span class="body-state-track" aria-hidden="true">
                                    <span id="body-state-discipline-bar" class="body-state-fill body-state-fill-discipline" style="width: {{ $bodyStateDiscipline }}%;"></span>
                                </span>
                                <span id="body-state-discipline-value" class="body-state-value">{{ $bodyStateDiscipline }}</span>
                            </div>
                            <div class="body-state-row">
                                <span class="body-state-label">Recuperacion</span>
                                <span class="body-state-track" aria-hidden="true">
                                    <span id="body-state-recovery-bar" class="body-state-fill body-state-fill-recovery" style="width: {{ $bodyStateRecovery }}%;"></span>
                                </span>
                                <span id="body-state-recovery-value" class="body-state-value">{{ $bodyStateRecovery }}</span>
                            </div>
                        </div>
                        <p id="body-state-context" class="body-state-context">{{ $bodyStateContextLine }}</p>
                    </div>
                    <div class="progress-lock-overlay" aria-hidden="{{ $progressUnlocked ? 'true' : 'false' }}">
                        <div class="progress-lock-overlay-content">
                            <p class="progress-lock-title">Progreso en espera</p>
                            <p class="progress-lock-text" data-progress-lock-message>{{ $progressLockReason }}</p>
                        </div>
                    </div>
                </article>

                <article class="training-card section-card" data-section-card="training">
                    <div class="section-toolbar">
                        <p id="training-title" class="training-title">{{ $trainingTitle }}</p>
                        <button type="button" class="section-toggle-btn" data-section-toggle="training" aria-expanded="true">Ocultar</button>
                    </div>
                    <div data-section-body class="space-y-3">
                    <div class="training-lockable progress-lock-card {{ $progressUnlocked ? '' : 'is-locked' }}" data-progress-lock-card>
                        <div class="progress-lock-content">
                            <p id="training-objective" class="training-line">{{ $trainingObjectiveLine }}</p>
                            <p id="training-focus" class="training-line">{{ $trainingFocusLine }}</p>
                            <p id="training-rhythm" class="training-line">{{ $trainingRhythmLine }}</p>
                            <ul id="training-plan-list" class="training-list">
                                @if ($trainingExercises !== [])
                                    @foreach ($trainingExercises as $exercise)
                                        @php
                                            $exerciseName = trim((string) ($exercise['name'] ?? 'Ejercicio'));
                                            $exerciseDose = trim((string) ($exercise['prescription'] ?? '3 x 10'));
                                        @endphp
                                        <li class="training-item">
                                            <span class="training-item-name">{{ $exerciseName }}</span>
                                            <span class="training-item-dose">{{ $exerciseDose }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="training-item">
                                        <span class="training-item-name">No hay rutina disponible por ahora.</span>
                                        <span class="training-item-dose">-</span>
                                    </li>
                                @endif
                            </ul>
                            <p id="training-adaptation" class="training-line">{{ $trainingAdaptationLine }}</p>
                            <p id="training-context" class="training-context">{{ $trainingContextLine }}</p>
                        </div>
                        <div class="progress-lock-overlay" aria-hidden="{{ $progressUnlocked ? 'true' : 'false' }}">
                            <div class="progress-lock-overlay-content">
                                <p class="progress-lock-title">Progreso en espera</p>
                                <p class="progress-lock-text" data-progress-lock-message>{{ $progressLockReason }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="training-session-box">
                        <p id="training-session-status" class="training-session-status">{{ $trainingStatusLabel }}</p>
                        <p id="training-session-timer" class="training-session-timer {{ $trainingIsActive ? '' : 'hidden' }}">
                            Tiempo restante: <span id="training-session-timer-value">{{ $trainingTimerLabel }}</span>
                        </p>
                        <div class="training-session-actions">
                            <button
                                id="training-start-btn"
                                type="button"
                                class="module-action module-action-primary {{ $trainingCanStart ? '' : 'hidden' }}"
                            >
                                Comenzar entrenamiento
                            </button>
                            <button
                                id="training-finish-btn"
                                type="button"
                                class="module-action module-action-secondary {{ $trainingCanFinish ? '' : 'hidden' }}"
                            >
                                Finalizar entrenamiento
                            </button>
                        </div>
                        <p id="training-session-hint" class="training-session-hint">{{ $trainingHintLine }}</p>
                        <p id="training-session-feedback" class="training-session-feedback hidden"></p>
                    </div>
                    </div>
                </article>
            </section>
        @endif
        @if ($screen === 'profile')
            <section id="profile-view" class="space-y-4">
                <article class="glass-card rounded-3xl p-4">
                    <div class="profile-header-row">
                        <h2 class="text-sm font-black uppercase tracking-[.16em] text-cyan-100">Mi perfil</h2>
                        <button id="open-profile-edit" type="button" class="profile-edit-toggle">Editar perfil</button>
                    </div>
                    <p class="mt-2 text-xs text-slate-300">Datos de tu cuenta y estado actual en el gimnasio.</p>
                </article>

                @if (session('profile_status'))
                    <p class="profile-message profile-message-success">{{ (string) session('profile_status') }}</p>
                @endif
                @if ($errors->has('profile'))
                    <p class="profile-message profile-message-error">{{ (string) $errors->first('profile') }}</p>
                @endif

                <article id="profile-edit-panel" class="profile-edit-panel space-y-3 {{ $profileEditOpen ? '' : 'hidden' }}">
                    <div>
                        <p class="profile-edit-title">Editar seguridad y contacto</p>
                        <p class="profile-edit-help">Para cambiar contraseña, teléfono o foto debes validar tu contraseña actual.</p>
                    </div>
                    <form method="POST" action="{{ route('client-mobile.profile.update', ['gymSlug' => $gym->slug]) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="_profile_form" value="1">

                        <label class="block space-y-1 text-sm">
                            <span class="profile-field-label">Contraseña actual</span>
                            <input id="profile-current-password" type="password" name="current_password" class="module-input" autocomplete="current-password" required>
                            @error('current_password')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
                        </label>

                        <label class="block space-y-1 text-sm">
                            <span class="profile-field-label">Teléfono (opcional)</span>
                            <input type="text" name="phone" value="{{ old('phone', $clientPhone) }}" class="module-input" placeholder="+593..." autocomplete="tel">
                            @error('phone')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
                        </label>

                        <div class="space-y-1 text-sm">
                            <span class="profile-field-label">Foto de perfil (opcional)</span>
                            <div class="flex items-center gap-3">
                                @if ($clientPhotoUrl !== '')
                                    <img src="{{ $clientPhotoUrl }}" alt="Foto actual" class="profile-photo-preview">
                                @else
                                    <span class="profile-photo-preview inline-flex items-center justify-center text-sm font-black text-emerald-100">{{ $clientInitials }}</span>
                                @endif
                                <input type="file" name="photo" class="module-input" accept="image/jpeg,image/png,image/webp">
                            </div>
                            @error('photo')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
                        </div>

                        <label class="block space-y-1 text-sm">
                            <span class="profile-field-label">Nueva contraseña (opcional)</span>
                            <input type="password" name="new_password" class="module-input" autocomplete="new-password">
                            @error('new_password')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
                        </label>

                        <label class="block space-y-1 text-sm">
                            <span class="profile-field-label">Confirmar nueva contraseña</span>
                            <input type="password" name="new_password_confirmation" class="module-input" autocomplete="new-password">
                        </label>

                        <div class="flex gap-2">
                            <button type="submit" class="module-action module-action-primary">Guardar cambios</button>
                            <button id="cancel-profile-edit" type="button" class="module-action">Cancelar</button>
                        </div>
                    </form>
                </article>

                <article class="glass-card rounded-3xl p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Nombre</p>
                            <p class="profile-kpi-value">{{ $clientFullName !== '' ? $clientFullName : '-' }}</p>
                        </div>
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Usuario</p>
                            <p class="profile-kpi-value">{{ $clientUsername !== '' ? $clientUsername : '-' }}</p>
                        </div>
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Documento</p>
                            <p class="profile-kpi-value">{{ $clientDocument !== '' ? $clientDocument : '-' }}</p>
                        </div>
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Teléfono</p>
                            <p class="profile-kpi-value">{{ $clientPhone !== '' ? $clientPhone : '-' }}</p>
                        </div>
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Membresía</p>
                            <p class="profile-kpi-value">{{ $membershipStatusLabel }}</p>
                        </div>
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Vence</p>
                            <p class="profile-kpi-value">{{ (string) ($progress['membership_ends_at'] ?? '-') }}</p>
                        </div>
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Visitas del mes</p>
                            <p class="profile-kpi-value">{{ (int) ($progress['month_visits'] ?? 0) }}</p>
                        </div>
                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Total visitas</p>
                            <p class="profile-kpi-value">{{ (int) ($progress['total_visits'] ?? 0) }}</p>
                        </div>
                    </div>
                    <div class="profile-kpi">
                        <p class="profile-kpi-label">Último ingreso</p>
                        <p class="profile-kpi-value">{{ $lastAttendanceLabel }}</p>
                    </div>
                </article>

            </section>
        @endif
        @if ($screen === 'physical')
            <section id="physical-view" class="space-y-4">
                <article class="glass-card rounded-3xl p-4">
                    <div class="profile-header-row">
                        <h2 class="text-sm font-black uppercase tracking-[.16em] text-cyan-100">Datos físicos</h2>
                        <button id="open-physical-edit" type="button" class="profile-edit-toggle">{{ $fitnessProfileCompleted ? 'Editar datos' : 'Completar ahora' }}</button>
                    </div>
                    <p class="mt-2 text-xs text-slate-300">Resumen de tu estado físico y cálculos para seguimiento en el gimnasio.</p>
                </article>

                @if (session('fitness_status'))
                    <p class="profile-message profile-message-success">{{ (string) session('fitness_status') }}</p>
                @endif

                <article id="physical-edit-panel" class="profile-edit-panel space-y-3 {{ $fitnessEditOpen ? '' : 'hidden' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="profile-edit-title">Editar datos físicos</p>
                            <p class="profile-edit-help">Estos datos se usan para IMC, metabolismo basal y metas de seguimiento.</p>
                        </div>
                        <button id="close-physical-edit" type="button" class="profile-edit-toggle">Cerrar</button>
                    </div>
                    @include('client-mobile.partials.fitness-profile-form', [
                        'gym' => $gym,
                        'fitnessProfileModel' => $fitnessProfileModel,
                        'fitnessGoalOptions' => $fitnessGoalOptions,
                        'fitnessLevelOptions' => $fitnessLevelOptions,
                        'fitnessSexOptions' => $fitnessSexOptions,
                        'fitnessLimitationsOptions' => $fitnessLimitationsOptions,
                        'fitnessLimitations' => $fitnessLimitations,
                        'formIdPrefix' => 'physical-edit',
                        'nextScreen' => 'physical',
                        'isModalForm' => false,
                        'submitLabel' => 'Guardar datos físicos',
                    ])
                </article>

                <article class="glass-card rounded-3xl p-4 space-y-3">
                    @if ($fitnessProfileCompleted)
                        <div class="fitness-meta-grid">
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Edad</p>
                                <p class="fitness-meta-value">{{ (int) ($fitnessProfileModel?->age ?? 0) > 0 ? (int) $fitnessProfileModel->age.' años' : '-' }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Sexo</p>
                                <p class="fitness-meta-value">{{ $fitnessSexLabel }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Altura</p>
                                <p class="fitness-meta-value">{{ $formatMetric($fitnessProfileModel?->height_cm, 'cm') }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Peso actual</p>
                                <p class="fitness-meta-value">{{ $formatMetric($fitnessProfileModel?->weight_kg, 'kg') }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Objetivo</p>
                                <p class="fitness-meta-value">{{ $fitnessGoalLabel }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Nivel</p>
                                <p class="fitness-meta-value">{{ $fitnessLevelLabel }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Disponibilidad</p>
                                <p class="fitness-meta-value">{{ $fitnessDaysLabel }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Duración sesión</p>
                                <p class="fitness-meta-value">{{ $fitnessMinutesLabel }}</p>
                            </div>
                        </div>

                        @if ($hasBodyMetrics)
                            <div class="fitness-meta-grid">
                                <div class="fitness-meta-card">
                                    <p class="fitness-meta-label">IMC</p>
                                    <p class="fitness-meta-value">{{ $fitnessBmiValue }}</p>
                                </div>
                                <div class="fitness-meta-card">
                                    <p class="fitness-meta-label">Categoría IMC</p>
                                    <p class="fitness-meta-value">{{ $fitnessBmiCategory }}</p>
                                </div>
                                <div class="fitness-meta-card">
                                    <p class="fitness-meta-label">Metabolismo basal</p>
                                    <p class="fitness-meta-value">{{ $fitnessBmrValue }}</p>
                                </div>
                                <div class="fitness-meta-card">
                                    <p class="fitness-meta-label">Calorías mantenimiento</p>
                                    <p class="fitness-meta-value">{{ $fitnessMaintenanceValue }}</p>
                                </div>
                                <div class="fitness-meta-card">
                                    <p class="fitness-meta-label">Calorías objetivo</p>
                                    <p class="fitness-meta-value">{{ $fitnessTargetCaloriesValue }}</p>
                                </div>
                                <div class="fitness-meta-card">
                                    <p class="fitness-meta-label">Grasa corporal estimada</p>
                                    <p class="fitness-meta-value">{{ $fitnessBodyFatValue }}</p>
                                </div>
                            </div>
                        @else
                            <p class="fitness-profile-note">Completa altura y peso para generar tus cálculos de IMC y metabolismo.</p>
                        @endif

                        <div class="profile-kpi">
                            <p class="profile-kpi-label">Limitaciones</p>
                            <p class="profile-kpi-value">{{ $fitnessLimitationsLabel !== '' ? $fitnessLimitationsLabel : '-' }}</p>
                        </div>

                        @if ($fitnessGoalTrackLabel !== '')
                            <div class="profile-kpi">
                                <p class="profile-kpi-label">Enfoque según objetivo</p>
                                <p class="profile-kpi-value">{{ $fitnessGoalTrackLabel }}</p>
                            </div>
                        @endif

                        @if ($fitnessUpdatedLabel !== '')
                            <p class="fitness-profile-note">Última actualización: {{ $fitnessUpdatedLabel }}</p>
                        @endif
                    @else
                        <p class="text-xs text-slate-300">Aún no completas tus datos físicos iniciales.</p>
                    @endif
                </article>
            </section>
        @endif

        @if (! $fitnessProfileCompleted)
            <div
                id="fitness-onboarding-modal"
                class="fitness-modal {{ $showFitnessModal ? '' : 'hidden' }}"
                aria-hidden="{{ $showFitnessModal ? 'false' : 'true' }}"
                data-require-redirect-on-close="1"
                data-cancel-url="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'home']) }}"
            >
                <a
                    href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'home', '_close_fitness' => time()]) }}"
                    class="fitness-modal-backdrop"
                    aria-label="Volver al panel anterior"
                ></a>
                <div class="fitness-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="fitness-onboarding-title">
                    <article class="fitness-onboarding-card space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 id="fitness-onboarding-title" class="fitness-onboarding-title">Completa tus datos físicos</h3>
                                <p class="fitness-onboarding-help">Necesitamos estos datos para calcular IMC, metabolismo y habilitar tu pantalla de rendimiento.</p>
                            </div>
                            <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'home', '_close_fitness' => time()]) }}" class="fitness-modal-close" aria-label="Volver al panel anterior">Cerrar</a>
                        </div>

                        @include('client-mobile.partials.fitness-profile-form', [
                            'gym' => $gym,
                            'fitnessProfileModel' => $fitnessProfileModel,
                            'fitnessGoalOptions' => $fitnessGoalOptions,
                            'fitnessLevelOptions' => $fitnessLevelOptions,
                            'fitnessSexOptions' => $fitnessSexOptions,
                            'fitnessLimitationsOptions' => $fitnessLimitationsOptions,
                            'fitnessLimitations' => $fitnessLimitations,
                            'formIdPrefix' => 'fitness-modal',
                            'nextScreen' => 'progress',
                            'isModalForm' => true,
                            'submitLabel' => 'Guardar y continuar',
                        ])
                    </article>
                </div>
            </div>
        @endif
    </section>
</main>

<script>
(function () {
    const shell = document.querySelector('main.mobile-shell');
    if (!shell) return;

    const checkinUrl = shell.dataset.checkinUrl || '';
    const progressUrl = shell.dataset.progressUrl || '';
    const appUrl = shell.dataset.appUrl || window.location.href;
    const trainingStartUrl = shell.dataset.trainingStartUrl || '';
    const trainingFinishUrl = shell.dataset.trainingFinishUrl || '';
    const pushStatusUrl = shell.dataset.pushStatusUrl || '';
    const pushSubscribeUrl = shell.dataset.pushSubscribeUrl || '';
    const pushUnsubscribeUrl = shell.dataset.pushUnsubscribeUrl || '';
    const pushVapidPublicKey = shell.dataset.pushVapidKey || '';
    const currentScreen = shell.dataset.screen || 'home';
    const bootStateKey = 'client-mobile-boot-seen';
    const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const initialProgressPayload = @json($progress);

    const bootScreen = document.getElementById('boot-screen');
    const bootBar = document.getElementById('boot-progress-bar');
    const bootValue = document.getElementById('boot-progress-value');
    const moduleLoader = document.getElementById('module-loader');
    const actionGuideModal = document.getElementById('action-guide-modal');
    const actionGuideTitleEl = document.getElementById('action-guide-title');
    const actionGuideTextEl = document.getElementById('action-guide-text');
    const actionGuideCtaBtn = document.getElementById('action-guide-cta');
    const actionGuideDismissBtn = document.getElementById('action-guide-dismiss');
    const actionGuideDismissEls = Array.from(document.querySelectorAll('[data-action-guide-dismiss]'));

    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userMenuPanel = document.getElementById('user-menu-panel');
    const logoutForm = document.getElementById('client-mobile-logout-form');
    const openProfileEditBtn = document.getElementById('open-profile-edit');
    const cancelProfileEditBtn = document.getElementById('cancel-profile-edit');
    const profileEditPanel = document.getElementById('profile-edit-panel');
    const profileCurrentPasswordInput = document.getElementById('profile-current-password');
    const openPhysicalEditBtn = document.getElementById('open-physical-edit');
    const closePhysicalEditBtn = document.getElementById('close-physical-edit');
    const physicalEditPanel = document.getElementById('physical-edit-panel');
    const openFitnessModalTrigger = document.getElementById('open-fitness-modal-trigger');
    const fitnessModal = document.getElementById('fitness-onboarding-modal');
    const fitnessModalCloseTargets = document.querySelectorAll('[data-fitness-modal-close]');
    const clientPushStatusEl = document.getElementById('client-push-status');
    const clientPushToggleBtn = document.getElementById('client-push-toggle');

    const statusEl = document.getElementById('checkin-status');
    const startBtn = document.getElementById('start-scan');
    const stopBtn = document.getElementById('stop-scan');
    const video = document.getElementById('scan-video');
    const manualInput = document.getElementById('manual-token');
    const sendManual = document.getElementById('send-manual');

    const liveCountEl = document.getElementById('live-clients-count');
    const liveWindowEl = document.getElementById('live-clients-window');
    const predictionRhythmEl = document.getElementById('prediction-rhythm');
    const predictionPrimaryEl = document.getElementById('prediction-primary');
    const predictionSecondaryEl = document.getElementById('prediction-secondary');
    const predictionContextEl = document.getElementById('prediction-context');
    const progressLockCards = Array.from(document.querySelectorAll('[data-progress-lock-card]'));
    const progressLockMessageEls = Array.from(document.querySelectorAll('[data-progress-lock-message]'));
    const bodyStateSummaryEl = document.getElementById('body-state-summary');
    const bodyStateContextEl = document.getElementById('body-state-context');
    const bodyStateForceBarEl = document.getElementById('body-state-force-bar');
    const bodyStateResistanceBarEl = document.getElementById('body-state-resistance-bar');
    const bodyStateDisciplineBarEl = document.getElementById('body-state-discipline-bar');
    const bodyStateRecoveryBarEl = document.getElementById('body-state-recovery-bar');
    const bodyStateForceValueEl = document.getElementById('body-state-force-value');
    const bodyStateResistanceValueEl = document.getElementById('body-state-resistance-value');
    const bodyStateDisciplineValueEl = document.getElementById('body-state-discipline-value');
    const bodyStateRecoveryValueEl = document.getElementById('body-state-recovery-value');
    const trainingTitleEl = document.getElementById('training-title');
    const trainingObjectiveEl = document.getElementById('training-objective');
    const trainingFocusEl = document.getElementById('training-focus');
    const trainingRhythmEl = document.getElementById('training-rhythm');
    const trainingPlanListEl = document.getElementById('training-plan-list');
    const trainingAdaptationEl = document.getElementById('training-adaptation');
    const trainingContextEl = document.getElementById('training-context');
    const trainingSessionStatusEl = document.getElementById('training-session-status');
    const trainingSessionHintEl = document.getElementById('training-session-hint');
    const trainingSessionTimerEl = document.getElementById('training-session-timer');
    const trainingSessionTimerValueEl = document.getElementById('training-session-timer-value');
    const trainingSessionFeedbackEl = document.getElementById('training-session-feedback');
    const trainingStartBtn = document.getElementById('training-start-btn');
    const trainingFinishBtn = document.getElementById('training-finish-btn');
    const openWeeklyGoalEditBtn = document.getElementById('open-weekly-goal-edit');
    const closeWeeklyGoalEditBtn = document.getElementById('close-weekly-goal-edit');
    const weeklyGoalEditPanel = document.getElementById('weekly-goal-edit-panel');
    const weeklyGoalSummaryEl = document.getElementById('weekly-goal-summary');
    const weeklyGoalProgressFillEl = document.getElementById('weekly-goal-progress-fill');
    const weeklyGoalProgressLabelEl = document.getElementById('weekly-goal-progress-label');
    const weeklyGoalRemainingLabelEl = document.getElementById('weekly-goal-remaining-label');
    const weeklyGoalDaysLeftLabelEl = document.getElementById('weekly-goal-days-left-label');
    const weeklyAlertListEl = document.getElementById('weekly-alert-list');
    const timelineMonthLabelEl = document.getElementById('timeline-month-label');
    const timelineGridEl = document.getElementById('timeline-grid');
    const timelineWeekCommitmentEl = document.getElementById('timeline-week-commitment');
    const timelineWeekRestEl = document.getElementById('timeline-week-rest');
    const sectionCardEls = Array.from(document.querySelectorAll('[data-section-card]'));
    const sectionToggleBtnEls = Array.from(document.querySelectorAll('[data-section-toggle]'));
    const periodVisitsEl = document.getElementById('period-visits');
    const periodTotalEl = document.getElementById('period-total');
    const periodWindowLabelEl = document.getElementById('period-window-label');
    const monthLogTitleEl = document.getElementById('month-log-title');
    const monthAttendanceCountEl = document.getElementById('month-attendance-count');
    const monthAttendanceListEl = document.getElementById('month-attendance-list');
    const monthAttendanceEmptyEl = document.getElementById('month-attendance-empty');

    const mobileI18n = {
        ready_to_scan: @json(__('messages.client_mobile.ready_to_scan')),
        manual_token_empty: @json(__('messages.client_mobile.manual_token_empty')),
        validating_entry: @json(__('messages.client_mobile.validating_entry')),
        session_expired_reload: @json(__('messages.client_mobile.session_expired_reload')),
        checkin_success: @json(__('messages.client_mobile.checkin_success')),
        validation_failed: @json(__('messages.client_mobile.validation_failed')),
        network_validation_failed: @json(__('messages.client_mobile.network_validation_failed')),
        scan_in_progress: @json(__('messages.client_mobile.scan_in_progress')),
        camera_open_failed: @json(__('messages.client_mobile.camera_open_failed')),
        scan_qr_unsupported: @json(__('messages.client_mobile.browser_qr_not_supported')),
    };

    let stream = null;
    let scanTimer = null;
    let detector = null;
    let clientPushBusy = false;
    let clientPushState = 'idle';
    let trainingActionBusy = false;
    let trainingCountdownTimer = null;
    let moduleLoaderFailSafeTimer = null;
    let moduleLoaderLocked = false;
    let actionGuideMode = '';
    let actionGuideDismissedMode = '';
    let actionGuideCtaHandler = null;
    let cameraPermissionProbeState = 'unknown';
    let directPermissionPromptArmed = false;
    let directPermissionPromptDone = false;
    const sectionStateStorageKey = 'client-mobile:progress:sections:v1';
    let sectionCollapseState = {};
    let scannerFallbackLibraryPromise = null;
    let fallbackScannerControls = null;
    let fallbackScannerReader = null;
    let scanBusy = false;
    let lastScanToken = '';
    let lastScanAt = 0;

    function isStandaloneMode() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    }

    function syncPwaModeCookie() {
        const mode = isStandaloneMode() ? 'standalone' : 'browser';
        document.cookie = 'gym_pwa_mode=' + mode + '; path=/; max-age=2592000; SameSite=Lax';
    }

    async function registerPwaServiceWorker() {
        if (!('serviceWorker' in navigator) || !window.isSecureContext) return;
        try {
            await navigator.serviceWorker.register('/sw.js');
        } catch (_error) {
            // Keep silent.
        }
    }

    async function requestBrowserPermissionsDirectly() {
        if (directPermissionPromptDone) return;
        directPermissionPromptDone = true;

        // Primero camara para aprovechar el gesto de usuario y mostrar popup nativo.
        if (checkinUrl !== '') {
            try {
                const previewStream = await requestCameraStream();
                if (previewStream) {
                    previewStream.getTracks().forEach((track) => track.stop());
                }
            } catch (_error) {
                // Keep silent: browser may require explicit user action on Escanear QR.
            }
        }

        // Luego notificaciones.
        if ('Notification' in window && Notification.permission === 'default') {
            try {
                await Notification.requestPermission();
            } catch (_error) {
                // Keep silent.
            }
        }
    }

    function armDirectPermissionPrompt() {
        if (directPermissionPromptArmed) return;
        directPermissionPromptArmed = true;

        const trigger = () => {
            void requestBrowserPermissionsDirectly();
        };

        window.addEventListener('pointerdown', trigger, { capture: true, once: true });
        window.addEventListener('touchstart', trigger, { capture: true, once: true, passive: true });
        window.addEventListener('keydown', trigger, { capture: true, once: true });
    }

    function rememberCameraProbeSuccess() {
        cameraPermissionProbeState = 'granted';
    }

    function rememberCameraProbeError(error) {
        const errorName = String(error && error.name ? error.name : '').trim();
        if (errorName === 'NotAllowedError' || errorName === 'PermissionDeniedError' || errorName === 'SecurityError') {
            cameraPermissionProbeState = 'denied';
            return;
        }
        if (errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError') {
            cameraPermissionProbeState = 'unsupported';
            return;
        }
        if (
            errorName === 'NotReadableError'
            || errorName === 'TrackStartError'
            || errorName === 'OverconstrainedError'
            || errorName === 'ConstraintNotSatisfiedError'
        ) {
            // Error tecnico de dispositivo/camara, no bloqueo de permiso.
            cameraPermissionProbeState = 'granted';
        }
    }

    function setUserMenuOpen(isOpen) {
        if (!userMenuPanel) return;

        userMenuPanel.classList.toggle('hidden', !isOpen);
        userMenuPanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        userMenuToggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function setProfileEditOpen(isOpen) {
        if (!profileEditPanel) return;
        profileEditPanel.classList.toggle('hidden', !isOpen);
        if (!isOpen) return;
        window.setTimeout(() => profileCurrentPasswordInput?.focus(), 30);
    }

    function setPhysicalEditOpen(isOpen) {
        if (!physicalEditPanel) return;
        physicalEditPanel.classList.toggle('hidden', !isOpen);
    }

    function setWeeklyGoalEditOpen(isOpen) {
        if (!weeklyGoalEditPanel) return;
        weeklyGoalEditPanel.classList.toggle('hidden', !isOpen);
    }

    function readSectionCollapseState() {
        try {
            const raw = window.localStorage.getItem(sectionStateStorageKey);
            if (!raw) return {};
            const parsed = JSON.parse(raw);
            return parsed && typeof parsed === 'object' ? parsed : {};
        } catch (error) {
            return {};
        }
    }

    function writeSectionCollapseState() {
        try {
            window.localStorage.setItem(sectionStateStorageKey, JSON.stringify(sectionCollapseState));
        } catch (error) {
            // ignore persistence errors
        }
    }

    function setSectionCollapsed(sectionId, collapsed, persistState) {
        const normalizedSectionId = String(sectionId || '').trim();
        if (normalizedSectionId === '') return;

        const cardEl = sectionCardEls.find((item) => String(item.dataset.sectionCard || '') === normalizedSectionId);
        if (!cardEl) return;

        const shouldCollapse = Boolean(collapsed);
        cardEl.classList.toggle('is-section-collapsed', shouldCollapse);

        const toggleButtons = sectionToggleBtnEls.filter((btn) => String(btn.dataset.sectionToggle || '') === normalizedSectionId);
        toggleButtons.forEach((buttonEl) => {
            buttonEl.textContent = shouldCollapse ? 'Mostrar' : 'Ocultar';
            buttonEl.setAttribute('aria-expanded', shouldCollapse ? 'false' : 'true');
        });

        if (shouldCollapse && normalizedSectionId === 'weekly-goal') {
            setWeeklyGoalEditOpen(false);
        }

        if (persistState !== false) {
            sectionCollapseState[normalizedSectionId] = shouldCollapse;
            writeSectionCollapseState();
        }
    }

    function initializeSectionCollapseState() {
        if (currentScreen !== 'progress') return;
        sectionCollapseState = readSectionCollapseState();

        sectionCardEls.forEach((cardEl) => {
            const sectionId = String(cardEl.dataset.sectionCard || '').trim();
            if (sectionId === '') return;
            const isCollapsed = Boolean(sectionCollapseState[sectionId]);
            setSectionCollapsed(sectionId, isCollapsed, false);
        });
    }

    function setFitnessModalOpen(isOpen) {
        if (!fitnessModal) return;

        fitnessModal.classList.toggle('hidden', !isOpen);
        fitnessModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        document.body.style.overflow = isOpen ? 'hidden' : '';

        if (isOpen) {
            resetFitnessFormSubmitState();
            const firstInput = fitnessModal.querySelector('input, button, select, textarea');
            if (firstInput instanceof HTMLElement) {
                window.setTimeout(() => firstInput.focus(), 30);
            }
        }
    }

    function dismissFitnessModal() {
        if (!fitnessModal) return;

        const requiresRedirect = String(fitnessModal.dataset.requireRedirectOnClose || '') === '1';
        const cancelUrl = String(fitnessModal.dataset.cancelUrl || '').trim();
        if (requiresRedirect && cancelUrl !== '') {
            showModuleLoader();
            const forceReloadUrl = cancelUrl + (cancelUrl.includes('?') ? '&' : '?') + '_close_fitness=' + String(Date.now());
            window.location.assign(forceReloadUrl);
            return;
        }

        if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
        }
        setFitnessModalOpen(false);
    }

    function resetFitnessFormSubmitState() {
        const fitnessForms = document.querySelectorAll('form[data-fitness-form]');
        if (!fitnessForms.length) return;

        moduleLoaderLocked = false;
        fitnessForms.forEach((form) => {
            form.dataset.submitting = '0';
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn instanceof HTMLButtonElement) {
                submitBtn.disabled = false;
            }
        });
    }

    function initFitnessForms() {
        const fitnessForms = document.querySelectorAll('form[data-fitness-form]');
        if (!fitnessForms.length) return;

        fitnessForms.forEach((form) => {
            form.dataset.submitting = '0';
            const noneInput = form.querySelector('input[name="limitations[]"][value="ninguna"]');
            const limitationInputs = form.querySelectorAll('input[name="limitations[]"]');
            const escapeSelectorId = (rawId) => {
                const candidate = String(rawId || '');
                if (window.CSS && typeof window.CSS.escape === 'function') {
                    return window.CSS.escape(candidate);
                }
                return candidate.replace(/[^a-zA-Z0-9_-]/g, '\\$&');
            };
            const getSubmitButton = () => {
                const submitBtn = form.querySelector('button[type="submit"]');
                return submitBtn instanceof HTMLButtonElement ? submitBtn : null;
            };
            const getFirstInvalidField = () => {
                const controls = form.querySelectorAll('input, select, textarea');
                for (const control of controls) {
                    if (
                        !(
                            control instanceof HTMLInputElement
                            || control instanceof HTMLSelectElement
                            || control instanceof HTMLTextAreaElement
                        )
                    ) {
                        continue;
                    }

                    if (control.disabled) continue;
                    if (control instanceof HTMLInputElement && control.type === 'hidden') continue;

                    if (!control.checkValidity()) {
                        return control;
                    }
                }
                return null;
            };

            if (noneInput && limitationInputs.length) {
                form.addEventListener('change', (event) => {
                    const target = event.target;
                    if (!(target instanceof HTMLInputElement)) return;
                    if (target.name !== 'limitations[]') return;

                    if (target.value === 'ninguna' && target.checked) {
                        limitationInputs.forEach((input) => {
                            if (!(input instanceof HTMLInputElement)) return;
                            if (input.value === 'ninguna') return;
                            input.checked = false;
                        });
                        return;
                    }

                    if (target.value !== 'ninguna' && target.checked) {
                        noneInput.checked = false;
                    }

                    const hasSelection = Array.from(limitationInputs).some((input) => {
                        return input instanceof HTMLInputElement && input.checked;
                    });

                    if (!hasSelection) {
                        noneInput.checked = true;
                    }
                });
            }

            form.addEventListener('change', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement)) return;
                const chip = target.closest('.fitness-chip');
                if (chip) {
                    chip.classList.remove('is-invalid');
                }
            });

            form.addEventListener('submit', (event) => {
                const submitBtn = getSubmitButton();
                if (form.dataset.submitting === '1') {
                    event.preventDefault();
                    return;
                }

                const firstInvalid = getFirstInvalidField();
                if (firstInvalid instanceof HTMLElement) {
                    event.preventDefault();
                    form.dataset.submitting = '0';
                    if (submitBtn instanceof HTMLButtonElement) {
                        submitBtn.disabled = false;
                    }

                    let visualTarget = firstInvalid;
                    if (firstInvalid instanceof HTMLInputElement && firstInvalid.id !== '') {
                        const linkedLabel = form.querySelector(`label[for="${escapeSelectorId(firstInvalid.id)}"]`);
                        if (linkedLabel instanceof HTMLElement) {
                            visualTarget = linkedLabel;
                            const chip = linkedLabel.closest('.fitness-chip');
                            if (chip) {
                                chip.classList.add('is-invalid');
                            }
                        }
                    }

                    try {
                        visualTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } catch (error) {
                        visualTarget.scrollIntoView();
                    }
                    visualTarget.classList.remove('guide-focus-target');
                    void visualTarget.offsetWidth;
                    visualTarget.classList.add('guide-focus-target');
                    window.setTimeout(() => {
                        visualTarget.classList.remove('guide-focus-target');
                    }, 1600);

                    if (
                        firstInvalid instanceof HTMLInputElement
                        || firstInvalid instanceof HTMLTextAreaElement
                        || firstInvalid instanceof HTMLSelectElement
                    ) {
                        if (typeof firstInvalid.reportValidity === 'function') {
                            firstInvalid.reportValidity();
                        } else {
                            firstInvalid.focus();
                        }
                    }
                    return;
                }

                form.dataset.submitting = '1';
                if (submitBtn instanceof HTMLButtonElement) {
                    submitBtn.disabled = true;
                }
                showModuleLoader({ keepVisibleUntilNavigation: true });
            });
        });
    }

    function hideModuleLoader() {
        if (!moduleLoader) return;
        if (moduleLoaderFailSafeTimer) {
            window.clearTimeout(moduleLoaderFailSafeTimer);
            moduleLoaderFailSafeTimer = null;
        }
        moduleLoaderLocked = false;
        moduleLoader.classList.add('hidden');
        moduleLoader.setAttribute('aria-hidden', 'true');
    }

    function showModuleLoader(options) {
        if (!moduleLoader) return;
        if (moduleLoaderFailSafeTimer) {
            window.clearTimeout(moduleLoaderFailSafeTimer);
            moduleLoaderFailSafeTimer = null;
        }
        const settings = options && typeof options === 'object' ? options : {};
        const keepVisibleUntilNavigation = Boolean(settings.keepVisibleUntilNavigation);
        moduleLoaderLocked = keepVisibleUntilNavigation;

        moduleLoader.classList.remove('hidden');
        moduleLoader.setAttribute('aria-hidden', 'false');

        if (!keepVisibleUntilNavigation) {
            // Fail-safe: if navigation is canceled/back-restored, prevent sticky overlay.
            moduleLoaderFailSafeTimer = window.setTimeout(() => {
                if (document.visibilityState === 'visible') {
                    hideModuleLoader();
                }
            }, 12000);
            return;
        }

        // Extended fallback for long server responses on form save.
        moduleLoaderFailSafeTimer = window.setTimeout(() => {
            if (document.visibilityState !== 'visible') return;
            moduleLoaderLocked = false;
            hideModuleLoader();
            resetFitnessFormSubmitState();
        }, 45000);
    }

    function setActionGuideOpen(isOpen) {
        if (!actionGuideModal) return;

        if (!isOpen && document.activeElement instanceof HTMLElement && actionGuideModal.contains(document.activeElement)) {
            document.activeElement.blur();
        }

        actionGuideModal.classList.toggle('hidden', !isOpen);
        actionGuideModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        if ('inert' in actionGuideModal) {
            actionGuideModal.inert = !isOpen;
        }

        if (isOpen && actionGuideCtaBtn instanceof HTMLElement) {
            window.setTimeout(() => actionGuideCtaBtn.focus(), 20);
        }
    }

    function closeActionGuide(markDismissed = true) {
        if (markDismissed && actionGuideMode !== '') {
            actionGuideDismissedMode = actionGuideMode;
        }
        setActionGuideOpen(false);
    }

    function openActionGuide(mode, title, text, ctaLabel, ctaHandler) {
        if (!actionGuideModal || !actionGuideTitleEl || !actionGuideTextEl || !actionGuideCtaBtn) return;

        actionGuideMode = mode;
        actionGuideTitleEl.textContent = String(title || 'Guía rápida');
        actionGuideTextEl.textContent = String(text || 'Sigue esta indicación para continuar.');
        actionGuideCtaBtn.textContent = String(ctaLabel || 'Entendido');
        actionGuideCtaHandler = typeof ctaHandler === 'function' ? ctaHandler : null;
        setActionGuideOpen(true);
    }

    function buildAppScreenUrl(screen, extraParams) {
        let target;
        try {
            target = new URL(appUrl, window.location.href);
        } catch (error) {
            target = new URL(window.location.href);
        }

        target.searchParams.set('screen', String(screen || 'home'));
        if (extraParams && typeof extraParams === 'object') {
            Object.keys(extraParams).forEach((key) => {
                const value = extraParams[key];
                if (value === null || value === undefined || String(value).trim() === '') return;
                target.searchParams.set(String(key), String(value));
            });
        }

        return target.toString();
    }

    function navigateToAppScreen(screen, extraParams) {
        const url = buildAppScreenUrl(screen, extraParams || {});
        showModuleLoader();
        window.location.assign(url);
    }

    function focusTrainingActionButton(preferFinish) {
        if (currentScreen !== 'progress') return false;

        setSectionCollapsed('training', false, true);
        const targetBtn = preferFinish
            ? (trainingFinishBtn && !trainingFinishBtn.classList.contains('hidden') ? trainingFinishBtn : null)
            : (trainingStartBtn && !trainingStartBtn.classList.contains('hidden')
                ? trainingStartBtn
                : (trainingFinishBtn && !trainingFinishBtn.classList.contains('hidden') ? trainingFinishBtn : null));

        if (!targetBtn) return false;
        targetBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
        targetBtn.classList.remove('guide-focus-target');
        void targetBtn.offsetWidth;
        targetBtn.classList.add('guide-focus-target');
        window.setTimeout(() => targetBtn.classList.remove('guide-focus-target'), 2800);
        return true;
    }

    function consumeFocusParamsFromUrl() {
        let currentUrl;
        try {
            currentUrl = new URL(window.location.href);
        } catch (error) {
            return { focusStart: false, focusFinish: false };
        }

        const focusStart = currentUrl.searchParams.get('focus_training_start') === '1';
        const focusFinish = currentUrl.searchParams.get('focus_training_finish') === '1';
        if (!focusStart && !focusFinish) {
            return { focusStart: false, focusFinish: false };
        }

        currentUrl.searchParams.delete('focus_training_start');
        currentUrl.searchParams.delete('focus_training_finish');
        window.history.replaceState({}, '', currentUrl.toString());
        return { focusStart, focusFinish };
    }

    function resolveGuideMode(progressPayload) {
        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const trainingStatus = payload.training_status && typeof payload.training_status === 'object'
            ? payload.training_status
            : {};
        const canStart = Boolean(trainingStatus.can_start);
        const canFinish = Boolean(trainingStatus.can_finish);
        const isActive = Boolean(trainingStatus.is_active);
        const hasAttendanceToday = Boolean(trainingStatus.has_attendance_today);
        const completedToday = Boolean(trainingStatus.completed_today);

        if (canStart) {
            return currentScreen === 'progress' ? 'start_here' : 'start_redirect';
        }
        if (isActive && canFinish) {
            return currentScreen === 'progress' ? 'finish_here' : 'finish_redirect';
        }
        if (!hasAttendanceToday && !isActive && !completedToday) {
            return currentScreen === 'checkin' ? 'attendance_here' : 'attendance_redirect';
        }

        return '';
    }

    function updateActionGuide(progressPayload, force = false) {
        const mode = resolveGuideMode(progressPayload);
        if (mode === '') {
            actionGuideMode = '';
            closeActionGuide(false);
            return;
        }

        if (!force && actionGuideDismissedMode === mode) {
            return;
        }

        if (!force && actionGuideMode === mode && !actionGuideModal?.classList.contains('hidden')) {
            return;
        }

        if (mode === 'attendance_redirect') {
            openActionGuide(
                mode,
                'Paso 1: registra asistencia',
                'Primero valida tu ingreso por QR, RFID o documento en recepción.',
                'Ir a registrar asistencia',
                () => navigateToAppScreen('checkin')
            );
            return;
        }

        if (mode === 'attendance_here') {
            openActionGuide(
                mode,
                'Escanea para habilitar entrenamiento',
                'Registra tu asistencia en esta pantalla. Luego te llevamos al botón para comenzar entrenamiento.',
                'Entendido',
                () => closeActionGuide(true)
            );
            return;
        }

        if (mode === 'start_redirect') {
            openActionGuide(
                mode,
                'Entrenamiento listo',
                'Tu ingreso ya fue validado. Ahora pulsa el botón para comenzar entrenamiento.',
                'Ir al botón de entrenamiento',
                () => navigateToAppScreen('progress', { focus_training_start: '1' })
            );
            return;
        }

        if (mode === 'start_here') {
            openActionGuide(
                mode,
                'Pulsa comenzar entrenamiento',
                'Desliza hacia abajo y pulsa "Comenzar entrenamiento" para activar el progreso de hoy.',
                'Bajar al botón',
                () => {
                    focusTrainingActionButton(false);
                    closeActionGuide(true);
                }
            );
            return;
        }

        if (mode === 'finish_redirect') {
            openActionGuide(
                mode,
                'Sesión en curso',
                'Tu entrenamiento está activo. Ve a la sección de entrenamiento para finalizar cuando termines.',
                'Ir a finalizar entrenamiento',
                () => navigateToAppScreen('progress', { focus_training_finish: '1' })
            );
            return;
        }

        if (mode === 'finish_here') {
            openActionGuide(
                mode,
                'Finaliza tu entrenamiento',
                'Ya tienes una sesión activa. Baja y pulsa "Finalizar entrenamiento" al terminar.',
                'Bajar al botón',
                () => {
                    focusTrainingActionButton(true);
                    closeActionGuide(true);
                }
            );
        }
    }

    function markBootSeen() {
        try {
            window.sessionStorage.setItem(bootStateKey, '1');
        } catch (error) {
            // ignore storage errors
        }
    }

    function clearBootSeen() {
        try {
            window.sessionStorage.removeItem(bootStateKey);
        } catch (error) {
            // ignore storage errors
        }
    }

    function isBootSeen() {
        try {
            return window.sessionStorage.getItem(bootStateKey) === '1';
        } catch (error) {
            return false;
        }
    }

    function runBootSequence() {
        if (!bootScreen || !bootBar || !bootValue) return;

        let progress = 0;
        const timer = window.setInterval(() => {
            progress = Math.min(100, progress + Math.floor(Math.random() * 11) + 6);
            bootBar.style.width = progress + '%';
            bootValue.textContent = String(progress);

            if (progress < 100) return;

            window.clearInterval(timer);
            window.setTimeout(() => {
                bootScreen.classList.add('is-finished');
            }, 180);

            window.setTimeout(() => {
                bootScreen.classList.add('hidden');
            }, 720);
        }, 120);
    }

    function initBootScreen() {
        if (!bootScreen) return;

        if (isBootSeen()) {
            bootScreen.classList.add('hidden');
            return;
        }

        markBootSeen();
        runBootSequence();
    }

    function readCookie(name) {
        const source = String(document.cookie || '');
        if (source === '') return '';

        const parts = source.split(';');
        for (let i = 0; i < parts.length; i += 1) {
            const part = parts[i].trim();
            if (!part.startsWith(name + '=')) continue;
            return part.slice(name.length + 1);
        }

        return '';
    }

    function resolveCsrfHeaders() {
        const headers = {};
        const metaToken = String(csrfMeta || '').trim();
        if (metaToken !== '') {
            headers['X-CSRF-TOKEN'] = metaToken;
            return headers;
        }

        const cookieRaw = readCookie('XSRF-TOKEN');
        if (cookieRaw === '') return headers;

        try {
            headers['X-XSRF-TOKEN'] = decodeURIComponent(cookieRaw);
        } catch (error) {
            headers['X-XSRF-TOKEN'] = cookieRaw;
        }

        return headers;
    }

    function urlBase64ToUint8Array(base64String) {
        const source = String(base64String || '').trim();
        if (source === '') {
            throw new Error('Falta WEBPUSH_VAPID_PUBLIC_KEY.');
        }

        const padding = '='.repeat((4 - (source.length % 4)) % 4);
        const base64 = (source + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; i += 1) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    async function postJson(url, payload) {
        if (!url) {
            throw new Error('Ruta de notificaciones no disponible.');
        }

        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...resolveCsrfHeaders(),
            },
            body: JSON.stringify(payload || {}),
        });

        let data = null;
        try {
            data = await response.json();
        } catch (error) {
            data = null;
        }

        if (!response.ok || (data && data.ok === false)) {
            const fallbackMessage = response.status === 401
                ? 'Tu sesión expiró. Recarga la app.'
                : 'No se pudo guardar la configuración push.';
            const message = data && data.message ? String(data.message) : fallbackMessage;
            throw new Error(message);
        }

        return data || {};
    }

    async function resolvePushServiceWorkerRegistration() {
        const swUrl = '/sw.js';
        let registration = await navigator.serviceWorker.getRegistration(swUrl).catch(() => null);
        if (!registration) {
            registration = await navigator.serviceWorker.getRegistration().catch(() => null);
        }
        if (!registration) {
            registration = await navigator.serviceWorker.register(swUrl).catch(() => null);
        }

        return registration;
    }

    function setClientPushUi(state, message) {
        const normalizedState = ['idle', 'active', 'loading', 'denied', 'unsupported'].includes(String(state))
            ? String(state)
            : 'idle';
        clientPushState = normalizedState;

        if (!clientPushToggleBtn || !clientPushStatusEl) return;

        const defaultStatusByState = {
            idle: 'Activa notificaciones para recibir avisos de meta semanal.',
            active: 'Notificaciones activas en este dispositivo.',
            loading: 'Procesando notificaciones...',
            denied: 'Bloqueaste notificaciones en el navegador.',
            unsupported: 'Este dispositivo no permite notificaciones push.',
        };

        switch (normalizedState) {
            case 'loading':
                clientPushToggleBtn.textContent = 'Procesando...';
                clientPushToggleBtn.setAttribute('disabled', 'disabled');
                break;
            case 'active':
                clientPushToggleBtn.textContent = 'Desactivar';
                clientPushToggleBtn.removeAttribute('disabled');
                break;
            case 'denied':
                clientPushToggleBtn.textContent = 'Bloqueadas';
                clientPushToggleBtn.setAttribute('disabled', 'disabled');
                break;
            case 'unsupported':
                clientPushToggleBtn.textContent = 'No disponible';
                clientPushToggleBtn.setAttribute('disabled', 'disabled');
                break;
            default:
                clientPushToggleBtn.textContent = 'Activar';
                clientPushToggleBtn.removeAttribute('disabled');
                break;
        }

        const statusText = String(message || defaultStatusByState[normalizedState] || defaultStatusByState.idle);
        clientPushStatusEl.textContent = statusText;
    }

    async function refreshClientPushStatus() {
        if (!clientPushToggleBtn || !clientPushStatusEl) return;

        const hasPushSupport = ('serviceWorker' in navigator) && ('PushManager' in window) && ('Notification' in window);
        if (!hasPushSupport) {
            setClientPushUi('unsupported', 'Tu navegador no soporta notificaciones push.');
            return;
        }

        if (!window.isSecureContext) {
            setClientPushUi('unsupported', 'Push requiere HTTPS (o localhost).');
            return;
        }

        if (String(pushVapidPublicKey || '').trim() === '') {
            setClientPushUi('unsupported', 'Falta configurar llave pública VAPID en el servidor.');
            return;
        }

        try {
            urlBase64ToUint8Array(pushVapidPublicKey);
        } catch (error) {
            const message = error instanceof Error ? error.message : 'WEBPUSH_VAPID_PUBLIC_KEY inválida.';
            setClientPushUi('unsupported', message);
            return;
        }

        let statusPayload = null;
        if (pushStatusUrl !== '') {
            statusPayload = await fetch(pushStatusUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            }).then((response) => {
                return response.ok ? response.json() : null;
            }).catch(() => null);
        }

        if (statusPayload && statusPayload.webpush_ready === false) {
            setClientPushUi('unsupported', 'El servidor aún no tiene push habilitado.');
            return;
        }

        if (Notification.permission === 'denied') {
            setClientPushUi('denied', 'Permiso bloqueado. Debes habilitarlo en el navegador.');
            return;
        }

        const registration = await resolvePushServiceWorkerRegistration();
        const subscription = registration && registration.pushManager
            ? await registration.pushManager.getSubscription().catch(() => null)
            : null;

        if (subscription) {
            setClientPushUi('active', 'Notificaciones activas para este dispositivo.');
            return;
        }

        const activeSubscriptions = Number(statusPayload && statusPayload.active_subscriptions
            ? statusPayload.active_subscriptions
            : 0);
        if (activeSubscriptions > 0) {
            setClientPushUi('idle', 'Tienes notificaciones activas en otro dispositivo.');
            return;
        }

        setClientPushUi('idle');
    }

    async function subscribeClientPush() {
        if (!clientPushToggleBtn || clientPushBusy) return;

        clientPushBusy = true;
        setClientPushUi('loading', 'Activando notificaciones...');

        try {
            if (pushSubscribeUrl === '') {
                throw new Error('No existe ruta para activar notificaciones.');
            }

            const registration = await resolvePushServiceWorkerRegistration();
            if (!registration || !registration.pushManager) {
                throw new Error('No se pudo inicializar Service Worker para push.');
            }

            let currentSubscription = await registration.pushManager.getSubscription().catch(() => null);
            if (!currentSubscription) {
                const permissionResult = await Notification.requestPermission();
                if (permissionResult !== 'granted') {
                    setClientPushUi(permissionResult === 'denied' ? 'denied' : 'idle', 'No concediste permiso de notificaciones.');
                    return;
                }

                currentSubscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(pushVapidPublicKey),
                });
            }

            const supportedEncodings = Array.isArray(PushManager.supportedContentEncodings)
                ? PushManager.supportedContentEncodings
                : [];
            const resolvedEncoding = supportedEncodings.includes('aes128gcm') ? 'aes128gcm' : 'aesgcm';

            await postJson(pushSubscribeUrl, {
                subscription: currentSubscription.toJSON(),
                encoding: resolvedEncoding,
                device_name: window.navigator.platform || null,
            });

            setClientPushUi('active', 'Notificaciones activadas para este dispositivo.');
        } catch (error) {
            const message = error instanceof Error ? error.message : 'No se pudo activar notificaciones push.';
            const nextState = ('Notification' in window && Notification.permission === 'denied') ? 'denied' : 'idle';
            setClientPushUi(nextState, message);
        } finally {
            clientPushBusy = false;
        }
    }

    async function unsubscribeClientPush() {
        if (!clientPushToggleBtn || clientPushBusy) return;

        clientPushBusy = true;
        setClientPushUi('loading', 'Desactivando notificaciones...');

        try {
            const registration = await resolvePushServiceWorkerRegistration();
            const currentSubscription = registration && registration.pushManager
                ? await registration.pushManager.getSubscription().catch(() => null)
                : null;

            if (pushUnsubscribeUrl !== '') {
                await postJson(pushUnsubscribeUrl, {
                    endpoint: currentSubscription ? currentSubscription.endpoint : null,
                });
            }

            if (currentSubscription) {
                await currentSubscription.unsubscribe().catch(() => {
                    // backend is source of truth
                });
            }

            setClientPushUi('idle', 'Notificaciones desactivadas para este dispositivo.');
        } catch (error) {
            const message = error instanceof Error ? error.message : 'No se pudo desactivar notificaciones push.';
            setClientPushUi('active', message);
        } finally {
            clientPushBusy = false;
        }
    }

    function renderMonthAttendance(progressPayload) {
        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const entries = Array.isArray(payload.month_entries) ? payload.month_entries : [];

        if (monthLogTitleEl) {
            const monthLabel = String(payload.month_label || '-');
            monthLogTitleEl.textContent = 'Asistencias de ' + monthLabel;
        }

        if (monthAttendanceCountEl) {
            monthAttendanceCountEl.textContent = String(entries.length);
        }

        if (!monthAttendanceListEl || !monthAttendanceEmptyEl) return;

        monthAttendanceListEl.classList.toggle('is-scrollable', entries.length > 5);

        monthAttendanceListEl.textContent = '';

        if (entries.length <= 0) {
            monthAttendanceEmptyEl.classList.remove('hidden');
            return;
        }

        monthAttendanceEmptyEl.classList.add('hidden');

        entries.forEach((entry) => {
            const dateValue = String(entry && entry.date ? entry.date : '-');
            const timeValue = String(entry && entry.time ? entry.time : '--:--:--');

            const itemEl = document.createElement('li');
            itemEl.className = 'month-log-item';

            const dateEl = document.createElement('span');
            dateEl.className = 'month-log-date';
            dateEl.textContent = dateValue;

            const timeEl = document.createElement('span');
            timeEl.className = 'month-log-time';
            timeEl.textContent = timeValue;

            itemEl.append(dateEl, timeEl);
            monthAttendanceListEl.appendChild(itemEl);
        });
    }

    function renderProgressPrediction(progressPayload) {
        if (!predictionPrimaryEl && !predictionSecondaryEl) return;

        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const prediction = payload.prediction && typeof payload.prediction === 'object'
            ? payload.prediction
            : {};

        const rhythmLabel = String(prediction.rhythm_label || 'Sin datos');
        const consistencyPct = Number.isFinite(Number(prediction.consistency_percent))
            ? Math.max(0, Math.min(100, Math.round(Number(prediction.consistency_percent))))
            : 0;
        const primaryLine = String(prediction.primary_line || 'Completa tus datos físicos para activar tu predicción.');
        const secondaryLine = String(prediction.secondary_line || 'Registra asistencias para mejorar la precisión.');
        const contextLine = String(prediction.context_line || 'Sin datos de progreso todavía.');

        if (predictionRhythmEl) {
            predictionRhythmEl.textContent = rhythmLabel + ' | Constancia: ' + String(consistencyPct) + '%';
        }
        if (predictionPrimaryEl) {
            predictionPrimaryEl.textContent = primaryLine;
        }
        if (predictionSecondaryEl) {
            predictionSecondaryEl.textContent = secondaryLine;
        }
        if (predictionContextEl) {
            predictionContextEl.textContent = contextLine;
        }
    }

    function renderBodyState(progressPayload) {
        if (!bodyStateSummaryEl && !bodyStateForceBarEl) return;

        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const bodyState = payload.body_state && typeof payload.body_state === 'object'
            ? payload.body_state
            : {};

        const force = Number.isFinite(Number(bodyState.force))
            ? Math.max(0, Math.min(100, Math.round(Number(bodyState.force))))
            : 0;
        const resistance = Number.isFinite(Number(bodyState.resistance))
            ? Math.max(0, Math.min(100, Math.round(Number(bodyState.resistance))))
            : 0;
        const discipline = Number.isFinite(Number(bodyState.discipline))
            ? Math.max(0, Math.min(100, Math.round(Number(bodyState.discipline))))
            : 0;
        const recovery = Number.isFinite(Number(bodyState.recovery))
            ? Math.max(0, Math.min(100, Math.round(Number(bodyState.recovery))))
            : 0;

        const summaryLine = String(bodyState.summary_line || 'Sin datos para estado corporal.');
        const contextLine = String(bodyState.context_line || 'Registra más entrenamientos para estimar este estado.');

        if (bodyStateSummaryEl) bodyStateSummaryEl.textContent = summaryLine;
        if (bodyStateContextEl) bodyStateContextEl.textContent = contextLine;

        if (bodyStateForceBarEl) bodyStateForceBarEl.style.width = String(force) + '%';
        if (bodyStateResistanceBarEl) bodyStateResistanceBarEl.style.width = String(resistance) + '%';
        if (bodyStateDisciplineBarEl) bodyStateDisciplineBarEl.style.width = String(discipline) + '%';
        if (bodyStateRecoveryBarEl) bodyStateRecoveryBarEl.style.width = String(recovery) + '%';

        if (bodyStateForceValueEl) bodyStateForceValueEl.textContent = String(force);
        if (bodyStateResistanceValueEl) bodyStateResistanceValueEl.textContent = String(resistance);
        if (bodyStateDisciplineValueEl) bodyStateDisciplineValueEl.textContent = String(discipline);
        if (bodyStateRecoveryValueEl) bodyStateRecoveryValueEl.textContent = String(recovery);
    }

    function renderTrainingPlan(progressPayload) {
        if (!trainingPlanListEl && !trainingObjectiveEl) return;

        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const trainingPlan = payload.training_plan && typeof payload.training_plan === 'object'
            ? payload.training_plan
            : {};

        const title = String(trainingPlan.title || 'Entrenamiento de hoy');
        const objectiveLine = String(trainingPlan.objective_line || 'Sin objetivo disponible.');
        const focusLine = String(trainingPlan.focus_line || 'Sin enfoque disponible.');
        const rhythmLine = String(trainingPlan.rhythm_line || 'Sin ritmo configurado.');
        const adaptationLine = String(trainingPlan.adaptation_line || 'Sin ajustes por ahora.');
        const contextLine = String(trainingPlan.context_line || 'Sin contexto disponible.');
        const exercises = Array.isArray(trainingPlan.exercises) ? trainingPlan.exercises : [];

        if (trainingTitleEl) trainingTitleEl.textContent = title;
        if (trainingObjectiveEl) trainingObjectiveEl.textContent = objectiveLine;
        if (trainingFocusEl) trainingFocusEl.textContent = focusLine;
        if (trainingRhythmEl) trainingRhythmEl.textContent = rhythmLine;
        if (trainingAdaptationEl) trainingAdaptationEl.textContent = adaptationLine;
        if (trainingContextEl) trainingContextEl.textContent = contextLine;

        if (!trainingPlanListEl) return;

        trainingPlanListEl.textContent = '';
        if (exercises.length <= 0) {
            const emptyItemEl = document.createElement('li');
            emptyItemEl.className = 'training-item';

            const emptyNameEl = document.createElement('span');
            emptyNameEl.className = 'training-item-name';
            emptyNameEl.textContent = 'No hay rutina disponible por ahora.';

            const emptyDoseEl = document.createElement('span');
            emptyDoseEl.className = 'training-item-dose';
            emptyDoseEl.textContent = '-';

            emptyItemEl.append(emptyNameEl, emptyDoseEl);
            trainingPlanListEl.appendChild(emptyItemEl);
            return;
        }

        exercises.slice(0, 6).forEach((exercise) => {
            const name = String(exercise && exercise.name ? exercise.name : 'Ejercicio');
            const prescription = String(exercise && exercise.prescription ? exercise.prescription : '3 x 10');

            const itemEl = document.createElement('li');
            itemEl.className = 'training-item';

            const nameEl = document.createElement('span');
            nameEl.className = 'training-item-name';
            nameEl.textContent = name;

            const doseEl = document.createElement('span');
            doseEl.className = 'training-item-dose';
            doseEl.textContent = prescription;

            itemEl.append(nameEl, doseEl);
            trainingPlanListEl.appendChild(itemEl);
        });
    }

    function formatSecondsAsClock(totalSeconds) {
        const normalized = Math.max(0, Math.round(Number(totalSeconds) || 0));
        const minutes = Math.floor(normalized / 60);
        const seconds = normalized % 60;
        const minuteLabel = String(minutes).padStart(2, '0');
        const secondLabel = String(seconds).padStart(2, '0');
        return minuteLabel + ':' + secondLabel;
    }

    function setTrainingFeedback(message, isError) {
        if (!trainingSessionFeedbackEl) return;

        const text = String(message || '').trim();
        if (text === '') {
            trainingSessionFeedbackEl.textContent = '';
            trainingSessionFeedbackEl.classList.add('hidden');
            trainingSessionFeedbackEl.classList.remove('is-error');
            return;
        }

        trainingSessionFeedbackEl.textContent = text;
        trainingSessionFeedbackEl.classList.remove('hidden');
        trainingSessionFeedbackEl.classList.toggle('is-error', Boolean(isError));
    }

    function setTrainingButtonsBusy(isBusy) {
        if (trainingStartBtn && !trainingStartBtn.classList.contains('hidden')) {
            trainingStartBtn.toggleAttribute('disabled', Boolean(isBusy));
        }
        if (trainingFinishBtn && !trainingFinishBtn.classList.contains('hidden')) {
            trainingFinishBtn.toggleAttribute('disabled', Boolean(isBusy));
        }
    }

    function clearTrainingCountdown() {
        if (trainingCountdownTimer) {
            window.clearInterval(trainingCountdownTimer);
            trainingCountdownTimer = null;
        }
    }

    function startTrainingCountdown(endIsoString) {
        if (!trainingSessionTimerEl || !trainingSessionTimerValueEl) return;

        const endMs = Date.parse(String(endIsoString || ''));
        if (!Number.isFinite(endMs)) {
            trainingSessionTimerEl.classList.add('hidden');
            trainingSessionTimerValueEl.textContent = '--:--';
            clearTrainingCountdown();
            return;
        }

        clearTrainingCountdown();

        const tick = () => {
            const remainingSeconds = Math.max(0, Math.floor((endMs - Date.now()) / 1000));
            trainingSessionTimerValueEl.textContent = formatSecondsAsClock(remainingSeconds);
            trainingSessionTimerEl.classList.remove('hidden');

            if (remainingSeconds > 0) return;

            clearTrainingCountdown();
            window.setTimeout(() => {
                refreshProgress();
            }, 350);
        };

        tick();
        trainingCountdownTimer = window.setInterval(tick, 1000);
    }

    function renderProgressLock(trainingStatusPayload) {
        if (!Array.isArray(progressLockCards) || progressLockCards.length === 0) return;

        const trainingStatus = trainingStatusPayload && typeof trainingStatusPayload === 'object'
            ? trainingStatusPayload
            : {};
        const progressUnlocked = Boolean(trainingStatus.progress_unlocked);
        const reasonRaw = String(trainingStatus.lock_reason || '').trim();
        const reason = reasonRaw !== ''
            ? reasonRaw
            : 'Inicia tu entrenamiento de hoy para desbloquear el panel.';

        progressLockCards.forEach((cardEl) => {
            cardEl.classList.toggle('is-locked', !progressUnlocked);
            const overlayEl = cardEl.querySelector('.progress-lock-overlay');
            if (overlayEl) {
                overlayEl.setAttribute('aria-hidden', progressUnlocked ? 'true' : 'false');
            }
        });

        progressLockMessageEls.forEach((messageEl) => {
            messageEl.textContent = reason;
        });

        if (!progressUnlocked) {
            setWeeklyGoalEditOpen(false);
        }
    }

    function renderTrainingStatus(progressPayload) {
        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const trainingStatus = payload.training_status && typeof payload.training_status === 'object'
            ? payload.training_status
            : {};
        renderProgressLock(trainingStatus);

        const canStart = Boolean(trainingStatus.can_start);
        const canFinish = Boolean(trainingStatus.can_finish);
        const isActive = Boolean(trainingStatus.is_active);
        const statusLabel = String(trainingStatus.status_label || 'Registra asistencia para habilitar entrenamiento.');
        const hintLine = String(trainingStatus.hint_line || 'Escanea tu asistencia y luego inicia entrenamiento.');
        const remainingSeconds = Number.isFinite(Number(trainingStatus.remaining_seconds))
            ? Math.max(0, Math.round(Number(trainingStatus.remaining_seconds)))
            : 0;
        const scheduledEndAt = String(trainingStatus.scheduled_end_at || '').trim();

        if (trainingSessionStatusEl) {
            trainingSessionStatusEl.textContent = statusLabel;
        }
        if (trainingSessionHintEl) {
            trainingSessionHintEl.textContent = hintLine;
        }

        if (trainingStartBtn) {
            trainingStartBtn.classList.toggle('hidden', !canStart);
        }
        if (trainingFinishBtn) {
            trainingFinishBtn.classList.toggle('hidden', !canFinish);
        }

        if (isActive) {
            if (scheduledEndAt !== '') {
                startTrainingCountdown(scheduledEndAt);
            } else if (trainingSessionTimerEl && trainingSessionTimerValueEl) {
                trainingSessionTimerValueEl.textContent = formatSecondsAsClock(remainingSeconds);
                trainingSessionTimerEl.classList.remove('hidden');
                clearTrainingCountdown();
            }
        } else if (trainingSessionTimerEl && trainingSessionTimerValueEl) {
            clearTrainingCountdown();
            trainingSessionTimerValueEl.textContent = '--:--';
            trainingSessionTimerEl.classList.add('hidden');
        }

        if (!trainingActionBusy) {
            setTrainingButtonsBusy(false);
        }
    }

    function applyProgressPayload(progressPayload) {
        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};

        if (periodVisitsEl) periodVisitsEl.textContent = String(payload.period_visits ?? 0);
        if (periodTotalEl) periodTotalEl.textContent = String(payload.total_visits ?? 0);
        if (periodWindowLabelEl) {
            const windowLabel = String(payload.period_window_label || 'Sin membresía activa');
            periodWindowLabelEl.textContent = 'Período: ' + windowLabel;
        }

        renderMonthAttendance(payload);
        renderProgressPrediction(payload);
        renderWeeklyGoal(payload);
        renderBodyState(payload);
        renderTrainingPlan(payload);
        renderTrainingStatus(payload);
        updateActionGuide(payload);

        if (liveCountEl) {
            const next = String(payload.live_clients_count ?? 0);
            if (liveCountEl.textContent !== next) {
                liveCountEl.classList.remove('live-count-pop');
                void liveCountEl.offsetWidth;
                liveCountEl.classList.add('live-count-pop');
            }
            liveCountEl.textContent = next;
        }

        if (liveWindowEl) {
            const label = String(payload.live_window_label || 'En vivo');
            liveWindowEl.textContent = 'Conteo de ' + label + '. Actualiza automático.';
        }
    }

    async function postTrainingAction(url) {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...resolveCsrfHeaders(),
            },
            body: JSON.stringify({}),
        });

        let data = null;
        try {
            data = await response.json();
        } catch (error) {
            data = null;
        }

        if (!response.ok || (data && data.ok === false)) {
            const fallback = response.status === 401
                ? 'Tu sesión expiró. Recarga la app.'
                : 'No se pudo ejecutar la acción de entrenamiento.';
            const message = data && data.message ? String(data.message) : fallback;
            throw new Error(message);
        }

        return data || {};
    }

    async function triggerTrainingAction(url, pendingMessage) {
        if (trainingActionBusy) return;
        if (!url) return;

        trainingActionBusy = true;
        setTrainingButtonsBusy(true);
        setTrainingFeedback(pendingMessage, false);

        try {
            const payload = await postTrainingAction(url);
            if (payload && payload.progress && typeof payload.progress === 'object') {
                applyProgressPayload(payload.progress);
            } else {
                await refreshProgress();
            }

            const message = String(payload && payload.message ? payload.message : 'Acción completada.');
            setTrainingFeedback(message, false);
        } catch (error) {
            const message = error instanceof Error ? error.message : 'No se pudo completar la acción.';
            setTrainingFeedback(message, true);
        } finally {
            trainingActionBusy = false;
            setTrainingButtonsBusy(false);
        }
    }

    function compactTimelineForCurrentMonth(rawTimeline, progressPayload) {
        const timeline = Array.isArray(rawTimeline) ? rawTimeline : [];
        if (!timeline.length) return [];

        const todayRaw = String(progressPayload && progressPayload.today ? progressPayload.today : '').trim();
        const monthPrefix = /^\d{4}-\d{2}-\d{2}$/.test(todayRaw) ? todayRaw.slice(0, 7) : '';

        const normalized = timeline.map((item) => {
            const isPlaceholder = Boolean(item && item.is_placeholder);
            if (isPlaceholder) {
                return {
                    date: '',
                    label: '',
                    weekday_short: '',
                    attended: false,
                    is_today: false,
                    is_placeholder: true,
                };
            }

            const date = String(item && item.date ? item.date : '').trim();
            if (monthPrefix !== '' && date !== '' && !date.startsWith(monthPrefix)) {
                return {
                    date: '',
                    label: '',
                    weekday_short: '',
                    attended: false,
                    is_today: false,
                    is_placeholder: true,
                };
            }

            return item;
        });

        const weeks = [];
        for (let index = 0; index < normalized.length; index += 7) {
            weeks.push(normalized.slice(index, index + 7));
        }

        const weekHasVisibleDay = (week) => week.some((item) => {
            if (!item || item.is_placeholder) return false;
            return String(item.date || '').trim() !== '';
        });

        while (weeks.length && !weekHasVisibleDay(weeks[0])) {
            weeks.shift();
        }
        while (weeks.length && !weekHasVisibleDay(weeks[weeks.length - 1])) {
            weeks.pop();
        }

        if (!weeks.length) return [];
        return weeks.flat();
    }

    function renderWeeklyGoal(progressPayload) {
        if (!weeklyGoalSummaryEl && !weeklyGoalProgressFillEl) return;

        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const weeklyGoal = payload.weekly_goal && typeof payload.weekly_goal === 'object'
            ? payload.weekly_goal
            : {};
        const timeline = compactTimelineForCurrentMonth(
            Array.isArray(payload.last30_timeline) ? payload.last30_timeline : [],
            payload
        );
        if (timelineMonthLabelEl) {
            const monthLabel = String(payload.month_label || '').trim();
            timelineMonthLabelEl.textContent = 'Mes actual: ' + (monthLabel !== '' ? monthLabel : '-');
        }

        const target = Number.isFinite(Number(weeklyGoal.target))
            ? Math.max(0, Math.min(7, Math.round(Number(weeklyGoal.target))))
            : 3;
        const visits = Number.isFinite(Number(weeklyGoal.visits))
            ? Math.max(0, Math.round(Number(weeklyGoal.visits)))
            : 0;
        const completionPercent = Number.isFinite(Number(weeklyGoal.completion_percent))
            ? Math.max(0, Math.min(100, Math.round(Number(weeklyGoal.completion_percent))))
            : 0;
        const remaining = Number.isFinite(Number(weeklyGoal.remaining))
            ? Math.max(0, Math.round(Number(weeklyGoal.remaining)))
            : Math.max(0, target - visits);
        const daysLeft = Number.isFinite(Number(weeklyGoal.days_left_week))
            ? Math.max(0, Math.round(Number(weeklyGoal.days_left_week)))
            : 0;
        const commitmentLineRaw = String(weeklyGoal.commitment_line || '').trim();
        const commitmentLine = commitmentLineRaw !== ''
            ? commitmentLineRaw
            : (visits >= target
                ? ('Has asistido los ' + String(target) + ' días que prometiste. Excelente.')
                : ('Esta semana asististe ' + String(visits) + ' de los ' + String(target) + ' días que prometiste.'));
        const restDays = Number.isFinite(Number(weeklyGoal.rest_days))
            ? Math.max(0, Math.round(Number(weeklyGoal.rest_days)))
            : Math.max(0, 7 - target);
        const restLineRaw = String(weeklyGoal.rest_line || '').trim();
        const restLine = restLineRaw !== ''
            ? restLineRaw
            : ('Días de descanso planificados: ' + String(restDays) + '.');

        if (weeklyGoalSummaryEl) {
            weeklyGoalSummaryEl.textContent = String(visits) + ' de ' + String(target) + ' sesiones esta semana.';
        }
        if (weeklyGoalProgressFillEl) {
            weeklyGoalProgressFillEl.style.width = String(completionPercent) + '%';
        }
        if (weeklyGoalProgressLabelEl) {
            weeklyGoalProgressLabelEl.textContent = 'Completado: ' + String(completionPercent) + '%';
        }
        if (weeklyGoalRemainingLabelEl) {
            weeklyGoalRemainingLabelEl.textContent = 'Faltan: ' + String(remaining);
        }
        if (weeklyGoalDaysLeftLabelEl) {
            weeklyGoalDaysLeftLabelEl.textContent = 'Días restantes: ' + String(daysLeft);
        }
        if (timelineWeekCommitmentEl) {
            timelineWeekCommitmentEl.textContent = commitmentLine;
        }
        if (timelineWeekRestEl) {
            timelineWeekRestEl.textContent = restLine;
        }

        if (weeklyAlertListEl) {
            weeklyAlertListEl.textContent = '';
            const alerts = Array.isArray(weeklyGoal.alerts) ? weeklyGoal.alerts : [];
            const safeAlerts = alerts.length > 0 ? alerts : [{ type: 'info', text: 'Sin alertas por ahora.' }];

            safeAlerts.slice(0, 3).forEach((alert) => {
                const typeRaw = String(alert && alert.type ? alert.type : 'info').toLowerCase();
                const type = ['info', 'success', 'warning', 'danger'].includes(typeRaw) ? typeRaw : 'info';
                const text = String(alert && alert.text ? alert.text : 'Sin alertas por ahora.');

                const alertEl = document.createElement('p');
                alertEl.className = 'weekly-alert-item weekly-alert-' + type;
                alertEl.textContent = text;
                weeklyAlertListEl.appendChild(alertEl);
            });
        }

        if (timelineGridEl) {
            timelineGridEl.textContent = '';
            timeline.slice(0, 42).forEach((dayItem) => {
                const label = String(dayItem && dayItem.label ? dayItem.label : '--');
                const date = String(dayItem && dayItem.date ? dayItem.date : '');
                const weekday = String(dayItem && dayItem.weekday_short ? dayItem.weekday_short : '');
                const statusRaw = String(dayItem && dayItem.status ? dayItem.status : '').toLowerCase();
                const status = ['trained', 'rest', 'missed', 'pending'].includes(statusRaw)
                    ? statusRaw
                    : (Boolean(dayItem && dayItem.attended) ? 'trained' : 'rest');
                const isToday = Boolean(dayItem && dayItem.is_today);
                const isPlaceholder = Boolean(dayItem && dayItem.is_placeholder);
                const statusLabel = status === 'trained'
                    ? 'entrenado'
                    : (status === 'missed'
                        ? 'faltaste'
                        : (status === 'pending' ? 'aún no marcado' : 'descanso'));
                const title = ((weekday !== '' ? (weekday + ' ') : '') + date + ' - ' + statusLabel + (isToday ? ' (hoy)' : '')).trim();

                const cellEl = document.createElement('span');
                cellEl.className = 'timeline-cell';
                if (isPlaceholder) {
                    cellEl.classList.add('timeline-cell-placeholder');
                    cellEl.textContent = ' ';
                    cellEl.setAttribute('aria-hidden', 'true');
                    timelineGridEl.appendChild(cellEl);
                    return;
                }

                if (status === 'trained') {
                    cellEl.classList.add('timeline-cell-trained');
                } else if (status === 'pending') {
                    cellEl.classList.add('timeline-cell-pending');
                } else {
                    cellEl.classList.add('timeline-cell-neutral');
                }
                if (isToday) cellEl.classList.add('timeline-cell-today');
                cellEl.textContent = label;
                cellEl.title = title;
                cellEl.setAttribute('aria-label', title);
                timelineGridEl.appendChild(cellEl);
            });
        }
    }

    async function refreshProgress() {
        if (!progressUrl) return;
        try {
            const res = await fetch(progressUrl, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!res.ok) return;

            const payload = await res.json();
            if (!payload || !payload.ok || !payload.progress) return;
            applyProgressPayload(payload.progress);
        } catch (error) {
            // ignore refresh errors
        }
    }

    async function submitToken(rawToken) {
        const token = String(rawToken || '').trim();
        if (token === '') {
            if (statusEl) statusEl.textContent = mobileI18n.manual_token_empty;
            return;
        }

        if (statusEl) statusEl.textContent = mobileI18n.validating_entry;

        try {
            const csrfHeaders = resolveCsrfHeaders();
            const res = await fetch(checkinUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    ...csrfHeaders,
                },
                body: JSON.stringify({ token }),
            });

            let payload = null;
            try {
                payload = await res.json();
            } catch (error) {
                payload = null;
            }

            if (res.status === 419) {
                if (statusEl) statusEl.textContent = mobileI18n.session_expired_reload;
                return;
            }

            if (payload && payload.ok) {
                if (statusEl) statusEl.textContent = payload.message || mobileI18n.checkin_success;
                if (manualInput) manualInput.value = '';
                await refreshProgress();
                stopScan();
                return;
            }

            if (statusEl) statusEl.textContent = (payload && payload.message) || mobileI18n.validation_failed;
        } catch (error) {
            if (statusEl) statusEl.textContent = mobileI18n.network_validation_failed;
        }
    }

    function resetScanMarkers() {
        scanBusy = false;
        lastScanToken = '';
        lastScanAt = 0;
    }

    async function supportsNativeQrDetection() {
        if (!('BarcodeDetector' in window)) {
            return false;
        }

        if (typeof BarcodeDetector.getSupportedFormats !== 'function') {
            return true;
        }

        try {
            const formats = await BarcodeDetector.getSupportedFormats();
            return Array.isArray(formats) && formats.includes('qr_code');
        } catch (_error) {
            return false;
        }
    }

    async function loadFallbackScannerLibrary() {
        if (window.ZXingBrowser && window.ZXingBrowser.BrowserQRCodeReader) {
            return window.ZXingBrowser;
        }

        if (scannerFallbackLibraryPromise) {
            return scannerFallbackLibraryPromise;
        }

        const scriptSources = [
            'https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js',
            'https://cdn.jsdelivr.net/npm/@zxing/browser@0.1.5/umd/zxing-browser.min.js',
        ];

        scannerFallbackLibraryPromise = new Promise((resolve, reject) => {
            const tryLoad = (index) => {
                if (index >= scriptSources.length) {
                    reject(new Error('No se pudo cargar el lector QR alternativo.'));
                    return;
                }

                const script = document.createElement('script');
                script.src = scriptSources[index];
                script.async = true;
                script.onload = () => {
                    if (window.ZXingBrowser && window.ZXingBrowser.BrowserQRCodeReader) {
                        resolve(window.ZXingBrowser);
                        return;
                    }
                    tryLoad(index + 1);
                };
                script.onerror = () => {
                    tryLoad(index + 1);
                };
                document.head.appendChild(script);
            };

            tryLoad(0);
        });

        return scannerFallbackLibraryPromise;
    }

    function stopFallbackScanner() {
        if (fallbackScannerControls && typeof fallbackScannerControls.stop === 'function') {
            try {
                fallbackScannerControls.stop();
            } catch (_error) {
                // ignore stop errors
            }
        }
        fallbackScannerControls = null;

        if (fallbackScannerReader && typeof fallbackScannerReader.reset === 'function') {
            try {
                fallbackScannerReader.reset();
            } catch (_error) {
                // ignore reset errors
            }
        }
        fallbackScannerReader = null;
    }

    function resolveCameraErrorMessage(error) {
        const errorName = String(error && error.name ? error.name : '').trim();
        if (errorName === 'NotAllowedError' || errorName === 'PermissionDeniedError') {
            return 'No se concedio permiso de camara. Pulsa Escanear QR y acepta el popup del navegador.';
        }
        if (errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError') {
            return 'No se encontro una camara disponible en este dispositivo.';
        }
        if (errorName === 'NotReadableError' || errorName === 'TrackStartError') {
            return 'La camara esta en uso por otra app o pestana. Cierra la otra app e intenta de nuevo.';
        }
        if (errorName === 'OverconstrainedError' || errorName === 'ConstraintNotSatisfiedError') {
            return 'No se pudo usar la camara trasera. Intenta otra vez y usa la camara principal.';
        }
        if (errorName === 'SecurityError') {
            return 'El navegador bloqueo la camara por seguridad.';
        }
        if (errorName === 'AbortError') {
            return 'Se interrumpio la apertura de la camara. Intenta nuevamente.';
        }
        return mobileI18n.camera_open_failed;
    }

    async function requestCameraStream() {
        const constraintsAttempts = [
            { video: { facingMode: { ideal: 'environment' } }, audio: false },
            { video: { facingMode: 'environment' }, audio: false },
            { video: true, audio: false },
        ];

        let lastError = null;
        for (const constraints of constraintsAttempts) {
            try {
                const liveStream = await navigator.mediaDevices.getUserMedia(constraints);
                rememberCameraProbeSuccess();
                return liveStream;
            } catch (error) {
                lastError = error;
                rememberCameraProbeError(error);
                const errorName = String(error && error.name ? error.name : '').trim();
                if (
                    errorName === 'NotAllowedError'
                    || errorName === 'PermissionDeniedError'
                    || errorName === 'SecurityError'
                ) {
                    break;
                }
            }
        }

        throw (lastError || new Error('camera_unavailable'));
    }

    async function startFallbackScan() {
        const zxingBrowser = await loadFallbackScannerLibrary();
        const ReaderCtor = zxingBrowser && (
            (typeof zxingBrowser.BrowserQRCodeReader === 'function' && zxingBrowser.BrowserQRCodeReader)
            || (typeof zxingBrowser.BrowserMultiFormatReader === 'function' && zxingBrowser.BrowserMultiFormatReader)
        );
        if (!ReaderCtor) {
            throw new Error('Fallback QR reader unavailable');
        }

        fallbackScannerReader = new ReaderCtor();
        fallbackScannerControls = await fallbackScannerReader.decodeFromVideoDevice(undefined, video, async (result) => {
            if (!result || scanBusy) return;

            const raw = String(typeof result.getText === 'function' ? result.getText() : (result.text || '')).trim();
            if (raw === '') return;

            const now = Date.now();
            if (raw === lastScanToken && (now - lastScanAt) < 1600) return;
            lastScanToken = raw;
            lastScanAt = now;

            scanBusy = true;
            try {
                await submitToken(raw);
            } finally {
                scanBusy = false;
            }
        });

        video.classList.remove('hidden');
        stopBtn.classList.remove('hidden');
        startBtn.classList.add('hidden');
        statusEl.textContent = mobileI18n.scan_in_progress;
    }

    async function startScan() {
        if (!statusEl || !startBtn || !stopBtn || !video) return;

        if (!window.isSecureContext) {
            statusEl.textContent = 'Escanear QR requiere HTTPS en este dispositivo.';
            return;
        }

        const permissionsPolicy = document.permissionsPolicy || document.featurePolicy;
        if (
            permissionsPolicy
            && typeof permissionsPolicy.allowsFeature === 'function'
            && !permissionsPolicy.allowsFeature('camera')
        ) {
            statusEl.textContent = 'La politica de permisos del sitio esta bloqueando camara. Recarga y vuelve a intentar.';
            return;
        }

        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            statusEl.textContent = 'Este navegador no permite abrir la camara.';
            return;
        }

        stopScan();
        startBtn.disabled = true;
        resetScanMarkers();
        statusEl.textContent = 'Abriendo camara...';

        const canUseNativeQr = await supportsNativeQrDetection();
        if (canUseNativeQr) {
            detector = new BarcodeDetector({ formats: ['qr_code'] });

            try {
                stream = await requestCameraStream();
                video.srcObject = stream;
                await video.play();
                rememberCameraProbeSuccess();
                video.classList.remove('hidden');
                stopBtn.classList.remove('hidden');
                startBtn.classList.add('hidden');
                statusEl.textContent = mobileI18n.scan_in_progress;

                scanTimer = window.setInterval(async () => {
                    if (!detector || !video || video.readyState < 2 || scanBusy) return;
                    try {
                        const codes = await detector.detect(video);
                        if (!codes || !codes.length) return;
                        const raw = String(codes[0].rawValue || '').trim();
                        if (raw === '') return;

                        const now = Date.now();
                        if (raw === lastScanToken && (now - lastScanAt) < 1600) return;
                        lastScanToken = raw;
                        lastScanAt = now;

                        scanBusy = true;
                        try {
                            await submitToken(raw);
                        } finally {
                            scanBusy = false;
                        }
                    } catch (_error) {
                        // ignore decode errors
                    }
                }, 320);
            } catch (nativeError) {
                rememberCameraProbeError(nativeError);
                stopScan();
                try {
                    await startFallbackScan();
                    rememberCameraProbeSuccess();
                } catch (fallbackError) {
                    rememberCameraProbeError(fallbackError);
                    const fallbackMessage = String(fallbackError && fallbackError.message ? fallbackError.message : '').trim();
                    if (fallbackMessage.includes('No se pudo cargar el lector QR alternativo')) {
                        statusEl.textContent = 'No se pudo cargar el lector QR alternativo. Verifica internet y vuelve a intentar.';
                    } else {
                        statusEl.textContent = resolveCameraErrorMessage(nativeError);
                    }
                    stopScan();
                }
            } finally {
                startBtn.disabled = false;
            }

            return;
        }

        try {
            await startFallbackScan();
            rememberCameraProbeSuccess();
        } catch (fallbackError) {
            rememberCameraProbeError(fallbackError);
            const fallbackMessage = String(fallbackError && fallbackError.message ? fallbackError.message : '').trim();
            if (fallbackMessage.includes('No se pudo cargar el lector QR alternativo')) {
                statusEl.textContent = 'No se pudo cargar el lector QR alternativo. Verifica internet y vuelve a intentar.';
            } else {
                statusEl.textContent = resolveCameraErrorMessage(fallbackError);
            }
            stopScan();
        } finally {
            startBtn.disabled = false;
        }
    }

    function stopScan() {
        if (scanTimer) {
            window.clearInterval(scanTimer);
            scanTimer = null;
        }

        detector = null;
        stopFallbackScanner();

        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
        }

        if (video && video.srcObject && video.srcObject.getTracks) {
            try {
                video.srcObject.getTracks().forEach((track) => track.stop());
            } catch (_error) {
                // ignore stop errors
            }
        }

        resetScanMarkers();

        if (!video || !startBtn || !stopBtn) return;

        video.pause();
        video.srcObject = null;
        video.classList.add('hidden');
        stopBtn.classList.add('hidden');
        startBtn.classList.remove('hidden');
        startBtn.disabled = false;
    }

    startBtn?.addEventListener('click', startScan);
    stopBtn?.addEventListener('click', stopScan);
    sendManual?.addEventListener('click', () => submitToken(manualInput?.value || ''));
    manualInput?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        submitToken(manualInput.value || '');
    });

    const navLoaderLinks = document.querySelectorAll('a.menu-cta, a.menu-back, a.user-dropdown-link');
    navLoaderLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.defaultPrevented) return;
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

            const href = link.getAttribute('href');
            if (!href) return;

            let targetUrl = null;
            let currentUrl = null;
            try {
                targetUrl = new URL(href, window.location.href);
                currentUrl = new URL(window.location.href);
            } catch (error) {
                targetUrl = null;
                currentUrl = null;
            }

            if (targetUrl && currentUrl) {
                const samePathAndQuery = targetUrl.pathname === currentUrl.pathname
                    && targetUrl.search === currentUrl.search;
                const sameHash = targetUrl.hash === currentUrl.hash;

                // Avoid stuck loader when user taps a link that keeps the same view/anchor.
                if (samePathAndQuery && (sameHash || (targetUrl.hash !== '' && currentUrl.hash === ''))) {
                    hideModuleLoader();
                    setUserMenuOpen(false);
                    if (targetUrl.hash !== '') {
                        window.location.hash = targetUrl.hash;
                    }
                    return;
                }
            }

            event.preventDefault();
            showModuleLoader();
            window.setTimeout(() => {
                window.location.href = href;
            }, 90);
        });
    });

    userMenuToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const shouldOpen = userMenuPanel?.classList.contains('hidden') ?? true;
        setUserMenuOpen(shouldOpen);
    });

    document.addEventListener('click', (event) => {
        if (!userMenuPanel || userMenuPanel.classList.contains('hidden')) return;

        const target = event.target;
        if (!(target instanceof Node)) return;
        if (userMenuPanel.contains(target) || userMenuToggle?.contains(target)) return;

        setUserMenuOpen(false);
    });

    window.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        setUserMenuOpen(false);
        if (fitnessModal && !fitnessModal.classList.contains('hidden')) {
            dismissFitnessModal();
        }
        setWeeklyGoalEditOpen(false);
        if (actionGuideModal && !actionGuideModal.classList.contains('hidden')) {
            closeActionGuide(true);
        }
    });

    actionGuideDismissEls.forEach((element) => {
        element.addEventListener('click', () => {
            closeActionGuide(true);
        });
    });

    actionGuideDismissBtn?.addEventListener('click', () => {
        closeActionGuide(true);
    });

    actionGuideCtaBtn?.addEventListener('click', () => {
        if (typeof actionGuideCtaHandler !== 'function') {
            closeActionGuide(true);
            return;
        }
        actionGuideCtaHandler();
    });

    openFitnessModalTrigger?.addEventListener('click', () => {
        setUserMenuOpen(false);
        setFitnessModalOpen(true);
    });

    fitnessModalCloseTargets.forEach((target) => {
        target.addEventListener('click', () => {
            dismissFitnessModal();
        });
    });

    openProfileEditBtn?.addEventListener('click', () => {
        setProfileEditOpen(true);
    });

    cancelProfileEditBtn?.addEventListener('click', () => {
        setProfileEditOpen(false);
    });

    openPhysicalEditBtn?.addEventListener('click', () => {
        setPhysicalEditOpen(true);
    });

    closePhysicalEditBtn?.addEventListener('click', () => {
        setPhysicalEditOpen(false);
    });

    openWeeklyGoalEditBtn?.addEventListener('click', () => {
        setSectionCollapsed('weekly-goal', false, true);
        setWeeklyGoalEditOpen(true);
    });

    closeWeeklyGoalEditBtn?.addEventListener('click', () => {
        setWeeklyGoalEditOpen(false);
    });

    sectionToggleBtnEls.forEach((buttonEl) => {
        buttonEl.addEventListener('click', () => {
            const sectionId = String(buttonEl.dataset.sectionToggle || '').trim();
            if (sectionId === '') return;

            const cardEl = sectionCardEls.find((item) => String(item.dataset.sectionCard || '') === sectionId);
            if (!cardEl) return;

            const nextCollapsed = !cardEl.classList.contains('is-section-collapsed');
            setSectionCollapsed(sectionId, nextCollapsed, true);
        });
    });

    clientPushToggleBtn?.addEventListener('click', async () => {
        if (clientPushBusy) return;
        if (clientPushState === 'unsupported' || clientPushState === 'denied') return;

        if (clientPushState === 'active') {
            await unsubscribeClientPush();
            return;
        }

        await subscribeClientPush();
    });

    trainingStartBtn?.addEventListener('click', async () => {
        await triggerTrainingAction(trainingStartUrl, 'Iniciando entrenamiento...');
    });

    trainingFinishBtn?.addEventListener('click', async () => {
        await triggerTrainingAction(trainingFinishUrl, 'Finalizando entrenamiento...');
    });

    logoutForm?.addEventListener('submit', () => {
        clearBootSeen();
    });

    window.addEventListener('pageshow', () => {
        cameraPermissionProbeState = 'unknown';
        directPermissionPromptDone = false;
        directPermissionPromptArmed = false;
        moduleLoaderLocked = false;
        hideModuleLoader();
        resetFitnessFormSubmitState();
        armDirectPermissionPrompt();
    });

    window.addEventListener('pagehide', () => {
        stopScan();
    });

    window.addEventListener('popstate', () => {
        moduleLoaderLocked = false;
        hideModuleLoader();
        resetFitnessFormSubmitState();
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState !== 'visible') return;
        if (moduleLoaderLocked) return;
        hideModuleLoader();
        resetFitnessFormSubmitState();
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState !== 'hidden') return;
        stopScan();
    });

    syncPwaModeCookie();
    registerPwaServiceWorker();
    initFitnessForms();
    resetFitnessFormSubmitState();
    initializeSectionCollapseState();
    armDirectPermissionPrompt();
    refreshClientPushStatus();
    initBootScreen();
    hideModuleLoader();
    const focusFlags = consumeFocusParamsFromUrl();
    if (currentScreen === 'progress' && (focusFlags.focusStart || focusFlags.focusFinish)) {
        window.setTimeout(() => {
            focusTrainingActionButton(Boolean(focusFlags.focusFinish));
        }, 380);
    }
    updateActionGuide(initialProgressPayload, true);
    if (fitnessModal && !fitnessModal.classList.contains('hidden')) {
        setFitnessModalOpen(true);
    }
    if (currentScreen === 'progress') {
        refreshProgress();
        window.setInterval(refreshProgress, 20000);
    }
})();
</script>
</body>
</html>


