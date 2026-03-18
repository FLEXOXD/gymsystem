<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16c172">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FlexGym">
    <link rel="manifest" href="{{ route('client-mobile.manifest', ['gymSlug' => $gym->slug, 'v' => '20260317']) }}">
    <link rel="icon" href="{{ asset('favicon.ico?v=20260317') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/fg-favicon-32.png?v=20260317') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('pwa/fg-favicon-16.png?v=20260317') }}">
    <link rel="shortcut icon" href="{{ asset('pwa/fg-favicon-32.png?v=20260317') }}">
    <link rel="apple-touch-icon" href="{{ asset('pwa/fg-favicon-180.png?v=20260317') }}">
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
        html, body {
            overflow-x: hidden;
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
            overflow: visible;
        }
        .mobile-shell.has-training-fab #progress-view {
            padding-bottom: calc(210px + env(safe-area-inset-bottom));
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
        .nutrition-home-card {
            border: 1px solid rgba(45, 212, 191, .34);
            background:
                radial-gradient(circle at top right, rgba(45, 212, 191, .12), transparent 46%),
                linear-gradient(155deg, rgba(2, 6, 23, .94), rgba(7, 89, 133, .34), rgba(2, 6, 23, .94));
            border-radius: 20px;
            padding: 12px;
            box-shadow: 0 18px 34px rgba(0, 0, 0, .35);
        }
        .nutrition-home-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .nutrition-home-eyebrow {
            color: #67e8f9;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .15em;
            text-transform: uppercase;
        }
        .nutrition-home-kcal {
            border: 1px solid rgba(45, 212, 191, .46);
            background: rgba(15, 23, 42, .8);
            color: #d1fae5;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }
        .nutrition-home-title {
            margin-top: 7px;
            color: #f8fafc;
            font-size: 20px;
            font-weight: 900;
            line-height: 1.1;
        }
        .nutrition-home-subtitle {
            margin-top: 5px;
            color: #bfdbfe;
            font-size: 12px;
            line-height: 1.35;
        }
        .nutrition-home-summary {
            margin-top: 8px;
            color: #e2e8f0;
            font-size: 12px;
            line-height: 1.45;
        }
        .nutrition-home-impact {
            margin-top: 8px;
            border: 1px solid rgba(147, 197, 253, .3);
            background: rgba(15, 23, 42, .7);
            border-radius: 12px;
            padding: 8px 9px;
            color: #dbeafe;
            font-size: 12px;
            line-height: 1.4;
            font-weight: 700;
        }
        .nutrition-home-list {
            margin-top: 10px;
            display: grid;
            gap: 9px;
        }
        .nutrition-meal-card {
            border: 1px solid rgba(14, 165, 233, .26);
            background: rgba(2, 6, 23, .8);
            border-radius: 14px;
            padding: 9px;
            display: grid;
            grid-template-columns: 74px minmax(0, 1fr);
            gap: 9px;
            align-items: center;
        }
        .nutrition-meal-image {
            width: 74px;
            height: 74px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid rgba(56, 189, 248, .25);
            background: #0f172a;
        }
        .nutrition-meal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }
        .nutrition-meal-slot {
            color: #a5f3fc;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .nutrition-meal-kcal {
            color: #6ee7b7;
            font-size: 12px;
            font-weight: 900;
        }
        .nutrition-meal-dish {
            margin-top: 4px;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.3;
        }
        .nutrition-meal-portion {
            margin-top: 3px;
            color: #cbd5e1;
            font-size: 11px;
            line-height: 1.35;
        }
        .nutrition-meal-macros {
            margin-top: 4px;
            color: #99f6e4;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .01em;
        }
        .nutrition-home-note {
            margin-top: 9px;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.4;
        }
        .nutrition-home-empty {
            margin-top: 10px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.42;
        }
        .nutrition-home-cta {
            margin-top: 10px;
        }
        .nutrition-screen {
            display: grid;
            gap: 14px;
        }
        .nutrition-header-card {
            border: 1px solid rgba(56,189,248,.34);
            border-radius: 20px;
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.14), transparent 38%),
                linear-gradient(156deg, rgba(2,6,23,.95), rgba(8,47,73,.9), rgba(2,6,23,.95));
            padding: 13px;
            box-shadow: 0 18px 32px rgba(0,0,0,.36);
        }
        .nutrition-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .nutrition-header-tag {
            color: #67e8f9;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .nutrition-header-chip {
            border: 1px solid rgba(56,189,248,.36);
            border-radius: 999px;
            background: rgba(15,23,42,.74);
            color: #d1fae5;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }
        .nutrition-header-title {
            margin-top: 7px;
            color: #fff;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.05;
        }
        .nutrition-header-subtitle {
            margin-top: 5px;
            color: #bfdbfe;
            font-size: 12px;
            line-height: 1.4;
        }
        .nutrition-header-lines {
            margin-top: 10px;
            display: grid;
            gap: 7px;
        }
        .nutrition-header-line {
            border: 1px solid rgba(56,189,248,.24);
            border-radius: 11px;
            background: rgba(2,6,23,.56);
            color: #dbeafe;
            font-size: 12px;
            line-height: 1.42;
            font-weight: 700;
            padding: 8px 9px;
        }
        .nutrition-allergy-alert {
            border: 1px solid rgba(248,113,113,.34);
            border-radius: 14px;
            background: linear-gradient(145deg, rgba(30,12,24,.92), rgba(69,10,10,.38), rgba(15,23,42,.84));
            padding: 11px;
        }
        .nutrition-allergy-alert-title {
            color: #fecaca;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .nutrition-allergy-alert-text {
            margin-top: 5px;
            color: #fee2e2;
            font-size: 12px;
            line-height: 1.42;
            font-weight: 700;
        }
        .nutrition-totals-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        .nutrition-total-card {
            border: 1px solid rgba(56,189,248,.28);
            border-radius: 12px;
            background: rgba(2,6,23,.66);
            padding: 8px 9px;
        }
        .nutrition-total-label {
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.2;
        }
        .nutrition-total-value {
            margin-top: 4px;
            color: #f8fafc;
            font-size: 16px;
            line-height: 1.2;
            font-weight: 900;
        }
        .nutrition-day-list {
            display: grid;
            gap: 9px;
        }
        .nutrition-day-item {
            border: 1px solid rgba(14,165,233,.28);
            border-radius: 14px;
            background: rgba(2,6,23,.76);
            padding: 9px;
            display: grid;
            grid-template-columns: 78px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
        }
        .nutrition-day-image {
            width: 78px;
            height: 78px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid rgba(56,189,248,.28);
            background: #0f172a;
        }
        .nutrition-day-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }
        .nutrition-day-slot {
            color: #a5f3fc;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .nutrition-day-kcal {
            color: #6ee7b7;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }
        .nutrition-day-dish {
            margin-top: 4px;
            color: #fff;
            font-size: 15px;
            line-height: 1.25;
            font-weight: 800;
        }
        .nutrition-day-portion {
            margin-top: 4px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.35;
        }
        .nutrition-day-macros {
            margin-top: 5px;
            color: #99f6e4;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .01em;
        }
        .nutrition-empty-card {
            border: 1px solid rgba(56,189,248,.3);
            border-radius: 14px;
            background: rgba(2,6,23,.62);
            padding: 11px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.45;
        }
        .nutrition-screen-footnote {
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.45;
        }
        .nutrition-customize-card {
            border: 1px solid rgba(45,212,191,.34);
            border-radius: 16px;
            background: linear-gradient(145deg, rgba(2,6,23,.92), rgba(15,118,110,.28), rgba(2,6,23,.92));
            padding: 12px;
            box-shadow: 0 16px 32px rgba(0,0,0,.35);
        }
        .nutrition-customize-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .nutrition-customize-title {
            color: #e6fffa;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .nutrition-customize-status {
            border: 1px solid rgba(45,212,191,.34);
            border-radius: 999px;
            background: rgba(2,6,23,.65);
            color: #99f6e4;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            white-space: nowrap;
        }
        .nutrition-customize-status.is-custom {
            border-color: rgba(134,239,172,.48);
            color: #bbf7d0;
            background: rgba(20,83,45,.28);
        }
        .nutrition-customize-text {
            margin-top: 8px;
            color: #dbeafe;
            font-size: 12px;
            line-height: 1.4;
        }
        .nutrition-customize-actions {
            margin-top: 10px;
            display: grid;
            gap: 8px;
        }
        .nutrition-customize-trigger {
            width: 100%;
            border: 1px solid rgba(56,189,248,.45);
            border-radius: 12px;
            background: linear-gradient(120deg, rgba(29,78,216,.95), rgba(8,145,178,.94), rgba(16,185,129,.9));
            color: #eff6ff;
            min-height: 42px;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: .01em;
            padding: 0 12px;
        }
        .nutrition-customize-reset {
            width: 100%;
            border: 1px solid rgba(148,163,184,.34);
            border-radius: 12px;
            background: rgba(2,6,23,.72);
            color: #cbd5e1;
            min-height: 38px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 0 10px;
        }
        .nutrition-customize-note {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.38;
        }
        .nutrition-customize-modal {
            position: fixed;
            inset: 0;
            z-index: 95;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 14px;
        }
        .nutrition-customize-modal.hidden {
            display: none;
        }
        .nutrition-customize-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2,6,23,.84);
            backdrop-filter: blur(2px);
        }
        .nutrition-customize-panel {
            position: relative;
            width: min(100%, 440px);
            max-height: calc(100vh - 28px);
            overflow: auto;
            border: 1px solid rgba(56,189,248,.35);
            border-radius: 20px;
            background:
                radial-gradient(circle at top right, rgba(45,212,191,.14), transparent 34%),
                linear-gradient(156deg, rgba(2,6,23,.97), rgba(15,23,42,.96), rgba(2,6,23,.98));
            box-shadow: 0 26px 48px rgba(0,0,0,.48);
            padding: 14px;
        }
        .nutrition-customize-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }
        .nutrition-customize-eyebrow {
            color: #67e8f9;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .nutrition-customize-heading {
            margin-top: 4px;
            color: #fff;
            font-size: 19px;
            font-weight: 900;
            line-height: 1.1;
        }
        .nutrition-customize-subtitle {
            margin-top: 4px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.4;
        }
        .nutrition-customize-close {
            border: 1px solid rgba(56,189,248,.44);
            background: rgba(2,6,23,.72);
            color: #e2e8f0;
            border-radius: 10px;
            min-width: 78px;
            min-height: 36px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .04em;
        }
        .nutrition-customize-groups {
            margin-top: 12px;
            display: grid;
            gap: 10px;
        }
        .nutrition-customize-group {
            border: 1px solid rgba(56,189,248,.24);
            border-radius: 13px;
            background: rgba(2,6,23,.58);
            padding: 9px;
        }
        .nutrition-customize-group-title {
            color: #a5f3fc;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .nutrition-customize-chip-grid {
            margin-top: 8px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 7px;
        }
        .nutrition-customize-chip {
            border: 1px solid rgba(56,189,248,.28);
            border-radius: 10px;
            background: rgba(15,23,42,.78);
            color: #e2e8f0;
            min-height: 36px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .01em;
            padding: 0 9px;
            text-align: left;
        }
        .nutrition-customize-chip.is-selected {
            border-color: rgba(74,222,128,.62);
            background: linear-gradient(120deg, rgba(22,101,52,.42), rgba(6,95,70,.44));
            color: #dcfce7;
            box-shadow: inset 0 0 0 1px rgba(134,239,172,.28);
        }
        .nutrition-customize-chip.is-disabled,
        .nutrition-customize-chip:disabled {
            border-color: rgba(248,113,113,.42);
            background: rgba(31,41,55,.82);
            color: rgba(203,213,225,.72);
            cursor: not-allowed;
            box-shadow: none;
        }
        .nutrition-customize-chip.is-disabled::after {
            content: 'Bloq.';
            margin-left: 6px;
            color: #fca5a5;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .02em;
        }
        .nutrition-allergy-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .nutrition-allergy-chip {
            border: 1px solid rgba(248,113,113,.34);
            border-radius: 999px;
            background: rgba(127,29,29,.44);
            color: #fecaca;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            white-space: nowrap;
            letter-spacing: .03em;
        }
        .nutrition-allergy-text {
            margin-top: 6px;
            color: #fecdd3;
            font-size: 11px;
            line-height: 1.38;
            font-weight: 700;
        }
        .nutrition-allergy-disclaimer {
            margin-top: 8px;
            border: 1px solid rgba(248,113,113,.26);
            border-radius: 10px;
            background: rgba(69,10,10,.35);
            color: #fecaca;
            font-size: 10px;
            line-height: 1.4;
            font-weight: 700;
            padding: 7px 8px;
        }
        .nutrition-customize-filters {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        .nutrition-customize-select-wrap {
            display: grid;
            gap: 5px;
        }
        .nutrition-customize-label {
            color: #93c5fd;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .03em;
        }
        .nutrition-customize-select {
            border: 1px solid rgba(56,189,248,.28);
            border-radius: 10px;
            background: rgba(2,6,23,.68);
            color: #f8fafc;
            min-height: 34px;
            font-size: 12px;
            font-weight: 700;
            padding: 0 9px;
        }
        .nutrition-customize-toggle {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #bfdbfe;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
        }
        .nutrition-customize-feedback {
            margin-top: 9px;
            border: 1px solid rgba(56,189,248,.22);
            border-radius: 10px;
            background: rgba(2,6,23,.55);
            color: #dbeafe;
            font-size: 11px;
            line-height: 1.4;
            padding: 8px 9px;
        }
        .nutrition-customize-panel-actions {
            margin-top: 11px;
            display: grid;
            gap: 8px;
        }
        .nutrition-adapt-badge {
            margin-top: 6px;
            display: inline-flex;
            border: 1px solid rgba(45,212,191,.34);
            border-radius: 999px;
            background: rgba(20,83,45,.26);
            color: #bbf7d0;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: 3px 9px;
        }
        .nutrition-shopping-list {
            display: grid;
            gap: 8px;
        }
        .nutrition-shopping-item {
            border: 1px solid rgba(56,189,248,.24);
            border-radius: 12px;
            background: rgba(2,6,23,.62);
            padding: 9px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .nutrition-shopping-check {
            width: 16px;
            height: 16px;
            accent-color: #22c55e;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .nutrition-shopping-copy {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }
        .nutrition-shopping-label {
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.35;
        }
        .nutrition-shopping-hint {
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.3;
            font-weight: 600;
        }
        .nutrition-shopping-item.is-done .nutrition-shopping-label {
            color: #94a3b8;
            text-decoration: line-through;
        }
        .nutrition-shopping-reason {
            margin-left: auto;
            color: #86efac;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }
        .nutrition-shopping-empty {
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.35;
        }
        .nutrition-shopping-add-row {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
        }
        .nutrition-shopping-input {
            min-height: 38px;
            font-size: 12px;
        }
        .nutrition-shopping-add-btn {
            border: 1px solid rgba(56,189,248,.38);
            border-radius: 10px;
            background: rgba(15,23,42,.84);
            color: #e0f2fe;
            min-width: 88px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .03em;
        }
        .nutrition-shopping-clear-btn {
            margin-top: 9px;
            width: 100%;
            border: 1px solid rgba(148,163,184,.28);
            border-radius: 10px;
            background: rgba(2,6,23,.56);
            color: #cbd5e1;
            min-height: 34px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .nutrition-shopping-panel {
            max-height: min(86vh, 760px);
            overflow: auto;
            padding-bottom: calc(14px + env(safe-area-inset-bottom));
        }
        .nutrition-preview-list {
            margin-top: 8px;
            display: grid;
            gap: 7px;
            max-height: 180px;
            overflow: auto;
            padding-right: 2px;
        }
        .nutrition-preview-item {
            border: 1px solid rgba(56,189,248,.24);
            border-radius: 10px;
            background: rgba(2,6,23,.62);
            padding: 7px 8px;
            display: grid;
            gap: 3px;
        }
        .nutrition-preview-slot {
            color: #7dd3fc;
            font-size: 10px;
            line-height: 1.2;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .nutrition-preview-dish {
            color: #e2e8f0;
            font-size: 12px;
            line-height: 1.35;
            font-weight: 700;
        }
        .nutrition-allergy-chip.is-warn {
            border-color: rgba(248,113,113,.4);
            background: rgba(127,29,29,.56);
            color: #fecaca;
        }
        .top-user-menu {
            position: fixed;
            top: calc(env(safe-area-inset-top) + 10px);
            left: 50%;
            transform: translateX(-50%);
            width: min(calc(100vw - 2rem), 28rem);
            box-sizing: border-box;
            z-index: 60;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            min-height: 46px;
        }
        .top-user-menu-spacer {
            height: calc(46px + env(safe-area-inset-top) + 18px);
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
        .leaderboard-chip {
            border: 1px solid rgba(250, 204, 21, .4);
            background: linear-gradient(145deg, rgba(2, 6, 23, .96), rgba(88, 28, 135, .34), rgba(2, 6, 23, .92));
            color: #fef3c7;
            border-radius: 15px;
            padding: 9px 12px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 43px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .35), inset 0 0 0 1px rgba(250, 204, 21, .08);
            backdrop-filter: blur(5px);
            transition: transform .12s ease, border-color .2s ease, box-shadow .2s ease;
            flex: 0 0 auto;
        }
        .leaderboard-chip:hover,
        .leaderboard-chip:active {
            border-color: rgba(250, 204, 21, .62);
            box-shadow: 0 16px 34px rgba(0, 0, 0, .38), inset 0 0 0 1px rgba(250, 204, 21, .14);
        }
        .leaderboard-chip-icon {
            width: 30px;
            height: 30px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(250, 204, 21, .18), rgba(245, 158, 11, .08));
            color: #facc15;
            box-shadow: inset 0 0 0 1px rgba(250, 204, 21, .16);
            flex: 0 0 auto;
        }
        .leaderboard-chip-copy {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 0;
        }
        .leaderboard-chip-title {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            line-height: 1.05;
        }
        .leaderboard-chip-status {
            margin-top: 2px;
            color: #fde68a;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.15;
            white-space: nowrap;
        }
        .leaderboard-modal {
            position: fixed;
            inset: 0;
            z-index: 74;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: max(16px, env(safe-area-inset-top)) 14px 18px;
            background: rgba(2, 6, 23, .72);
            backdrop-filter: blur(10px);
        }
        .leaderboard-modal.hidden {
            display: none;
        }
        .leaderboard-modal-backdrop {
            position: absolute;
            inset: 0;
            border: 0;
            background: transparent;
        }
        .leaderboard-modal-dialog {
            position: relative;
            width: min(100%, 430px);
            z-index: 1;
        }
        .leaderboard-card {
            border: 1px solid rgba(250, 204, 21, .26);
            background:
                radial-gradient(circle at top left, rgba(250, 204, 21, .14), transparent 32%),
                linear-gradient(155deg, rgba(2, 6, 23, .97), rgba(17, 24, 39, .95), rgba(29, 78, 216, .18));
            border-radius: 24px;
            padding: 16px 14px 14px;
            box-shadow: 0 28px 64px rgba(0, 0, 0, .5);
        }
        .leaderboard-tag {
            color: #fde68a;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .leaderboard-title {
            margin-top: 5px;
            color: #fff;
            font-size: 21px;
            font-weight: 900;
            line-height: 1.05;
        }
        .leaderboard-subtitle {
            margin-top: 5px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.4;
        }
        .leaderboard-close {
            border: 1px solid rgba(250, 204, 21, .38);
            background: rgba(15, 23, 42, .84);
            color: #fde68a;
            border-radius: 12px;
            padding: 8px 11px;
            font-size: 12px;
            font-weight: 800;
        }
        .leaderboard-window {
            margin-top: 12px;
            color: #a5f3fc;
            font-size: 12px;
            font-weight: 700;
        }
        .leaderboard-helper {
            margin-top: 8px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.42;
        }
        .leaderboard-list {
            margin-top: 12px;
            display: grid;
            gap: 10px;
        }
        .leaderboard-row {
            display: grid;
            grid-template-columns: 46px minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            border: 1px solid rgba(148, 163, 184, .18);
            background: rgba(15, 23, 42, .82);
            border-radius: 18px;
            padding: 11px 12px;
        }
        .leaderboard-row.is-current {
            border-color: rgba(45, 212, 191, .42);
            box-shadow: inset 0 0 0 1px rgba(45, 212, 191, .12);
        }
        .leaderboard-rank-badge {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 900;
            color: #f8fafc;
            background: linear-gradient(145deg, rgba(30, 41, 59, .96), rgba(15, 23, 42, .82));
            border: 1px solid rgba(148, 163, 184, .2);
        }
        .leaderboard-rank-badge.is-first {
            color: #111827;
            background: linear-gradient(145deg, #fde68a, #f59e0b);
            border-color: rgba(250, 204, 21, .58);
        }
        .leaderboard-rank-badge.is-second {
            color: #0f172a;
            background: linear-gradient(145deg, #e2e8f0, #94a3b8);
            border-color: rgba(226, 232, 240, .56);
        }
        .leaderboard-rank-badge.is-third {
            color: #fff7ed;
            background: linear-gradient(145deg, #fdba74, #9a3412);
            border-color: rgba(251, 146, 60, .52);
        }
        .leaderboard-row-name {
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.15;
        }
        .leaderboard-row-meta {
            margin-top: 5px;
            color: #cbd5e1;
            font-size: 11px;
            line-height: 1.45;
        }
        .leaderboard-row-score {
            color: #d9f99d;
            font-size: 16px;
            font-weight: 900;
            white-space: nowrap;
        }
        .leaderboard-empty {
            margin-top: 12px;
            border: 1px dashed rgba(148, 163, 184, .28);
            border-radius: 18px;
            padding: 14px 12px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.45;
            text-align: center;
        }
        .leaderboard-current-card {
            margin-top: 12px;
            border: 1px solid rgba(45, 212, 191, .28);
            background: linear-gradient(145deg, rgba(8, 47, 73, .7), rgba(15, 23, 42, .84));
            border-radius: 18px;
            padding: 12px;
        }
        .leaderboard-current-tag {
            color: #67e8f9;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .leaderboard-current-title {
            margin-top: 5px;
            color: #fff;
            font-size: 16px;
            font-weight: 900;
        }
        .leaderboard-current-meta {
            margin-top: 6px;
            color: #cbd5e1;
            font-size: 12px;
            line-height: 1.45;
        }
        .leaderboard-current-hint {
            margin-top: 7px;
            color: #a5f3fc;
            font-size: 11px;
            line-height: 1.4;
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
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .training-item-left {
            min-width: 0;
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }
        .training-item-toggle {
            border: 1px solid rgba(56,189,248,.34);
            background: rgba(2,6,23,.72);
            color: #bae6fd;
            width: 22px;
            height: 22px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            line-height: 1;
            flex: 0 0 auto;
        }
        .training-item-toggle[aria-pressed="true"] {
            border-color: rgba(34,197,94,.72);
            background: rgba(22,163,74,.24);
            color: #bbf7d0;
        }
        .training-item.is-done {
            border-color: rgba(34,197,94,.38);
            background: rgba(5,46,22,.58);
        }
        .training-item.is-done .training-item-name {
            color: #bbf7d0;
        }
        .training-item.is-done .training-item-dose {
            color: #4ade80;
        }
        .training-item-name {
            color: #f8fafc;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
            min-width: 0;
        }
        .training-item-dose {
            color: #86efac;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }
        .training-completion-box {
            margin-top: 10px;
            border: 1px solid rgba(56,189,248,.22);
            border-radius: 12px;
            background: rgba(2,6,23,.52);
            padding: 10px;
        }
        .training-completion-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 8px;
        }
        .training-completion-title {
            color: #bae6fd;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .training-completion-value {
            color: #f8fafc;
            font-size: 12px;
            font-weight: 800;
        }
        .training-completion-track {
            margin-top: 7px;
            width: 100%;
            height: 8px;
            border-radius: 9999px;
            background: rgba(148,163,184,.28);
            overflow: hidden;
        }
        .training-completion-fill {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, #22d3ee, #22c55e);
            transition: width .25s ease;
        }
        .training-completion-hint {
            margin-top: 7px;
            color: #bfdbfe;
            font-size: 11px;
            line-height: 1.35;
        }
        .training-rest-box {
            margin-top: 10px;
            border: 1px solid rgba(56,189,248,.22);
            border-radius: 12px;
            background: rgba(2,6,23,.52);
            padding: 10px;
        }
        .training-rest-title {
            color: #a5f3fc;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }
        .training-rest-row {
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .training-rest-timer {
            color: #f8fafc;
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .03em;
        }
        .training-rest-btn {
            border: 1px solid rgba(56,189,248,.34);
            background: rgba(8,47,73,.66);
            color: #bae6fd;
            border-radius: 9999px;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
        }
        .training-rest-btn:active {
            transform: translateY(1px);
        }
        .training-rest-hint {
            margin-top: 7px;
            color: #bae6fd;
            font-size: 11px;
            line-height: 1.35;
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
        .training-session-actions .training-idle-btn {
            border: 1px solid rgba(148,163,184,.35);
            background: rgba(30,41,59,.72);
            color: #cbd5e1;
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
        .today-summary-card {
            border: 1px solid rgba(34,211,238,.28);
            background: linear-gradient(145deg, rgba(2,6,23,.94), rgba(8,47,73,.42));
            border-radius: 18px;
            padding: 13px;
            box-shadow: 0 16px 34px rgba(0,0,0,.32);
        }
        .today-summary-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .today-summary-title {
            color: #cffafe;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .today-summary-chip {
            border: 1px solid rgba(56,189,248,.34);
            border-radius: 9999px;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #bae6fd;
            background: rgba(2,6,23,.66);
            white-space: nowrap;
        }
        .today-summary-chip.is-ready {
            border-color: rgba(34,197,94,.48);
            color: #bbf7d0;
            background: rgba(20,83,45,.45);
        }
        .today-summary-chip.is-active {
            border-color: rgba(34,211,238,.54);
            color: #a5f3fc;
            background: rgba(14,116,144,.42);
        }
        .today-summary-chip.is-done {
            border-color: rgba(16,185,129,.58);
            color: #d1fae5;
            background: rgba(6,95,70,.42);
        }
        .today-summary-grid {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        .today-summary-item {
            border: 1px solid rgba(56,189,248,.2);
            border-radius: 11px;
            background: rgba(2,6,23,.52);
            padding: 8px;
            min-height: 66px;
        }
        .today-summary-label {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.25;
        }
        .today-summary-value {
            margin-top: 5px;
            color: #f8fafc;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.2;
        }
        .today-summary-tip {
            margin-top: 9px;
            color: #bfdbfe;
            font-size: 11px;
            line-height: 1.35;
            font-weight: 700;
        }
        .training-fab-dock {
            position: fixed;
            left: 50%;
            bottom: calc(11px + env(safe-area-inset-bottom));
            transform: translateX(-50%);
            width: min(432px, calc(100vw - 20px));
            z-index: 66;
            transition: opacity .2s ease, transform .2s ease;
            pointer-events: none;
        }
        .training-fab-shell {
            border: 1px solid rgba(56,189,248,.34);
            background:
                linear-gradient(145deg, rgba(2,6,23,.95), rgba(8,47,73,.7)),
                radial-gradient(circle at top right, rgba(34,197,94,.18), transparent 45%);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,.48);
            backdrop-filter: blur(7px);
            padding: 10px;
            display: grid;
            gap: 8px;
            pointer-events: auto;
        }
        .training-fab-shell.is-ready {
            border-color: rgba(34,197,94,.52);
            box-shadow: 0 20px 40px rgba(0,0,0,.48), 0 0 0 1px rgba(34,197,94,.22), 0 0 22px rgba(34,197,94,.26);
            animation: fabReadyPulse 2.1s ease-in-out infinite;
        }
        .training-fab-status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .training-fab-status {
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.35;
            flex: 1;
            min-width: 0;
        }
        .training-fab-timer {
            color: #67e8f9;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
            margin-left: 8px;
        }
        .training-win-modal {
            position: fixed;
            inset: 0;
            z-index: 68;
            display: grid;
            place-items: center;
            padding: 16px;
        }
        .training-win-modal.hidden {
            display: none;
        }
        .training-win-backdrop {
            position: absolute;
            inset: 0;
            border: 0;
            background: rgba(2,6,23,.8);
            backdrop-filter: blur(3px);
        }
        .training-win-panel {
            position: relative;
            z-index: 1;
            width: min(100%, 372px);
            border: 1px solid rgba(34,211,238,.42);
            border-radius: 22px;
            padding: 16px 14px 14px;
            background:
                radial-gradient(circle at top right, rgba(34,197,94,.2), transparent 42%),
                linear-gradient(145deg, rgba(2,6,23,.97), rgba(8,47,73,.88));
            box-shadow: 0 24px 54px rgba(0,0,0,.52);
            overflow: hidden;
            animation: winPanelPop .34s ease-out;
        }
        .training-win-confetti {
            pointer-events: none;
            position: absolute;
            inset: 0;
        }
        .training-win-confetti-piece {
            position: absolute;
            top: -14%;
            width: 7px;
            height: 15px;
            border-radius: 9999px;
            opacity: .86;
            animation: winConfettiFall 1.5s linear forwards;
        }
        .training-win-confetti-piece:nth-child(1) { left: 7%; background: #22d3ee; animation-delay: .03s; }
        .training-win-confetti-piece:nth-child(2) { left: 15%; background: #facc15; animation-delay: .1s; }
        .training-win-confetti-piece:nth-child(3) { left: 24%; background: #4ade80; animation-delay: .18s; }
        .training-win-confetti-piece:nth-child(4) { left: 34%; background: #38bdf8; animation-delay: .07s; }
        .training-win-confetti-piece:nth-child(5) { left: 43%; background: #a3e635; animation-delay: .16s; }
        .training-win-confetti-piece:nth-child(6) { left: 53%; background: #f472b6; animation-delay: .09s; }
        .training-win-confetti-piece:nth-child(7) { left: 62%; background: #22c55e; animation-delay: .05s; }
        .training-win-confetti-piece:nth-child(8) { left: 72%; background: #eab308; animation-delay: .13s; }
        .training-win-confetti-piece:nth-child(9) { left: 82%; background: #06b6d4; animation-delay: .2s; }
        .training-win-confetti-piece:nth-child(10) { left: 91%; background: #84cc16; animation-delay: .12s; }
        .training-win-badge {
            position: relative;
            z-index: 1;
            color: #bbf7d0;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
        }
        .training-win-title {
            position: relative;
            z-index: 1;
            margin-top: 5px;
            color: #fff;
            font-size: 22px;
            font-weight: 900;
            line-height: 1.06;
        }
        .training-win-text {
            position: relative;
            z-index: 1;
            margin-top: 7px;
            color: #dbeafe;
            font-size: 13px;
            line-height: 1.45;
            font-weight: 600;
        }
        .training-win-grid {
            position: relative;
            z-index: 1;
            margin-top: 11px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        .training-win-stat {
            border: 1px solid rgba(56,189,248,.24);
            border-radius: 12px;
            background: rgba(2,6,23,.6);
            padding: 8px 7px;
            min-height: 72px;
        }
        .training-win-stat-label {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.2;
        }
        .training-win-stat-value {
            margin-top: 6px;
            color: #f8fafc;
            font-size: 16px;
            font-weight: 900;
            line-height: 1.1;
        }
        .training-win-actions {
            position: relative;
            z-index: 1;
            margin-top: 12px;
            display: grid;
            gap: 8px;
        }
        .training-win-actions .training-win-secondary {
            border: 1px solid rgba(56,189,248,.34);
            background: rgba(2,6,23,.72);
            color: #cbd5e1;
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
            opacity: .54;
            filter: saturate(.62) blur(.55px);
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
        .menu-nutrition {
            background: linear-gradient(120deg, #10263d, #0b5d74 55%, #0f9f63);
            border: 1px solid rgba(56,189,248,.36);
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
        .training-finish-confirm-modal {
            position: fixed;
            inset: 0;
            z-index: 67;
            display: grid;
            place-items: center;
            padding: 16px;
        }
        .training-finish-confirm-modal.hidden {
            display: none !important;
        }
        .training-finish-confirm-backdrop {
            position: absolute;
            inset: 0;
            border: 0;
            background: rgba(2,6,23,.8);
            backdrop-filter: blur(2px);
        }
        .training-finish-confirm-panel {
            position: relative;
            z-index: 1;
            width: min(100%, 360px);
            border-radius: 18px;
            border: 1px solid rgba(34,211,238,.4);
            background: linear-gradient(145deg, rgba(2,6,23,.96), rgba(8,47,73,.8));
            box-shadow: 0 18px 40px rgba(0,0,0,.5);
            padding: 14px;
            display: grid;
            gap: 10px;
            animation: winPanelPop .22s ease-out;
        }
        .training-finish-confirm-eyebrow {
            color: #a5f3fc;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .training-finish-confirm-title {
            color: #f8fafc;
            font-size: 19px;
            line-height: 1.15;
            font-weight: 900;
        }
        .training-finish-confirm-text {
            color: #dbeafe;
            font-size: 13px;
            line-height: 1.45;
            font-weight: 600;
        }
        .training-finish-confirm-actions {
            display: grid;
            gap: 8px;
        }
        .training-finish-confirm-cancel {
            border: 1px solid rgba(148,163,184,.35);
            background: rgba(30,41,59,.72);
            color: #cbd5e1;
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
        @keyframes fabReadyPulse {
            0% { transform: translateY(0); }
            50% { transform: translateY(-1px); }
            100% { transform: translateY(0); }
        }
        @keyframes winPanelPop {
            0% { transform: translateY(8px) scale(.96); opacity: .08; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes winConfettiFall {
            0% { transform: translateY(-4px) rotate(0deg); opacity: 0; }
            8% { opacity: .9; }
            100% { transform: translateY(260px) rotate(520deg); opacity: 0; }
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

<div id="training-finish-confirm-modal" class="training-finish-confirm-modal hidden" aria-hidden="true">
    <button type="button" class="training-finish-confirm-backdrop" data-training-finish-confirm-cancel aria-label="Cancelar finalizacion"></button>
    <article class="training-finish-confirm-panel" role="dialog" aria-modal="true" aria-labelledby="training-finish-confirm-title">
        <p class="training-finish-confirm-eyebrow">Confirmar accion</p>
        <h3 id="training-finish-confirm-title" class="training-finish-confirm-title">Finalizar entrenamiento</h3>
        <p class="training-finish-confirm-text">Estás por cerrar la sesión actual. Se guardará tu progreso y no podrás seguir en modo activo.</p>
        <div class="training-finish-confirm-actions">
            <button id="training-finish-confirm-accept" type="button" class="module-action module-action-secondary">Si, finalizar ahora</button>
            <button id="training-finish-confirm-cancel" type="button" class="module-action training-finish-confirm-cancel">Cancelar y seguir entrenando</button>
        </div>
    </article>
</div>

<div id="training-win-modal" class="training-win-modal hidden" aria-hidden="true">
    <button type="button" class="training-win-backdrop" data-training-win-close aria-label="Cerrar resumen"></button>
    <article class="training-win-panel" role="dialog" aria-modal="true" aria-labelledby="training-win-title">
        <div class="training-win-confetti" aria-hidden="true">
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
            <span class="training-win-confetti-piece"></span>
        </div>
        <p class="training-win-badge">Sesion completada</p>
        <h3 id="training-win-title" class="training-win-title">Excelente trabajo</h3>
        <p id="training-win-text" class="training-win-text">Tu entrenamiento terminó correctamente. Tu progreso ya fue actualizado.</p>
        <div class="training-win-grid">
            <div class="training-win-stat">
                <p class="training-win-stat-label">Ejercicios</p>
                <p id="training-win-exercises" class="training-win-stat-value">0/0</p>
            </div>
            <div class="training-win-stat">
                <p class="training-win-stat-label">Meta semana</p>
                <p id="training-win-weekly" class="training-win-stat-value">0/0</p>
            </div>
            <div class="training-win-stat">
                <p class="training-win-stat-label">Constancia</p>
                <p id="training-win-consistency" class="training-win-stat-value">0%</p>
            </div>
        </div>
        <div class="training-win-actions">
            <button id="training-win-close-btn" type="button" class="module-action module-action-primary">Seguir avanzando</button>
            <button id="training-win-close-secondary" type="button" class="module-action training-win-secondary">Cerrar</button>
        </div>
    </article>
</div>

<main
    class="mobile-shell px-4 pt-6 pb-6 {{ $screen === 'progress' ? 'has-training-fab' : '' }}"
    data-screen="{{ $screen }}"
    data-action-guide-key="{{ $gym->slug }}:{{ (string) ($client->id ?? 'guest') }}"
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
        $fitnessFormHasErrors = $errors->has('birth_date')
            || $errors->has('sex')
            || $errors->has('height_cm')
            || $errors->has('weight_kg')
            || $errors->has('goal')
            || $errors->has('secondary_goal')
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
        $fitnessPrimaryGoalLabel = $fitnessGoalOptions[(string) ($fitnessProfileModel?->goal ?? '')] ?? '-';
        $fitnessSecondaryGoalLabel = $fitnessGoalOptions[(string) ($fitnessProfileModel?->secondary_goal ?? '')] ?? '';
        $fitnessGoalLabel = $fitnessPrimaryGoalLabel;
        if ($fitnessPrimaryGoalLabel !== '-' && $fitnessSecondaryGoalLabel !== '') {
            $fitnessGoalLabel = $fitnessPrimaryGoalLabel.' + '.$fitnessSecondaryGoalLabel;
        }
        $fitnessLevelLabel = $fitnessLevelOptions[(string) ($fitnessProfileModel?->experience_level ?? '')] ?? '-';
        $fitnessSexLabel = $fitnessSexOptions[(string) ($fitnessProfileModel?->sex ?? '')] ?? '-';
        $fitnessBirthDateRaw = trim((string) ($fitnessProfileModel?->birth_date?->toDateString() ?? ''));
        $fitnessBirthDateLabel = $fitnessBirthDateRaw !== '' ? $fitnessBirthDateRaw : '-';
        $fitnessAgeYears = null;
        if ($fitnessBirthDateRaw !== '') {
            try {
                $fitnessAgeYears = \Carbon\Carbon::parse($fitnessBirthDateRaw)->age;
            } catch (\Throwable) {
                $fitnessAgeYears = null;
            }
        }
        if (! is_int($fitnessAgeYears) || $fitnessAgeYears <= 0) {
            $fitnessAgeYears = (int) ($fitnessProfileModel?->age ?? 0) > 0 ? (int) $fitnessProfileModel->age : null;
        }
        $fitnessAgeLabel = is_int($fitnessAgeYears) && $fitnessAgeYears > 0
            ? $fitnessAgeYears.' años'
            : '-';
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
        if ($fitnessGoalTrackLabel === '') {
            $fitnessGoalTrackLabel = trim((string) ($fitnessBodyMetrics['goal_summary_label'] ?? ''));
        }
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
        $nutritionPlan = is_array($progress['nutrition_plan'] ?? null) ? $progress['nutrition_plan'] : [];
        $nutritionReady = (bool) ($nutritionPlan['is_ready'] ?? false);
        $nutritionTitle = trim((string) ($nutritionPlan['title'] ?? 'Nutrición personalizada'));
        $nutritionSubtitle = trim((string) ($nutritionPlan['subtitle'] ?? 'Esto debes comer hoy según tus datos.'));
        $nutritionSummaryLine = trim((string) ($nutritionPlan['summary_line'] ?? 'Completa tus datos físicos para activar este módulo.'));
        $nutritionImpactLine = trim((string) ($nutritionPlan['impact_line'] ?? 'Sin impacto estimado por ahora.'));
        $nutritionGoalLabel = trim((string) ($nutritionPlan['goal_label'] ?? 'General'));
        $nutritionTargetKcal = is_numeric($nutritionPlan['target_kcal'] ?? null)
            ? (int) round((float) $nutritionPlan['target_kcal'])
            : null;
        $nutritionMaintenanceKcal = is_numeric($nutritionPlan['maintenance_kcal'] ?? null)
            ? (int) round((float) $nutritionPlan['maintenance_kcal'])
            : null;
        $nutritionFootnote = trim((string) ($nutritionPlan['footnote'] ?? ''));
        $nutritionItemsRaw = is_array($nutritionPlan['items'] ?? null) ? $nutritionPlan['items'] : [];
        $nutritionItems = [];
        foreach ($nutritionItemsRaw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $imagePath = trim((string) ($item['image_path'] ?? ''));
            $nutritionItems[] = [
                'slot' => trim((string) ($item['slot'] ?? 'Comida')),
                'dish' => trim((string) ($item['dish'] ?? 'Plato sugerido')),
                'portion' => trim((string) ($item['portion'] ?? 'Porción sugerida')),
                'kcal' => max(0, (int) ($item['kcal'] ?? 0)),
                'protein_g' => max(0, (int) ($item['protein_g'] ?? 0)),
                'carbs_g' => max(0, (int) ($item['carbs_g'] ?? 0)),
                'fat_g' => max(0, (int) ($item['fat_g'] ?? 0)),
                'image_url' => $imagePath !== '' ? asset(ltrim($imagePath, '/')) : asset('images/nutrition/nutri-lunch.svg'),
            ];
        }
        $nutritionTotalKcal = array_sum(array_map(static fn (array $item): int => (int) ($item['kcal'] ?? 0), $nutritionItems));
        $nutritionTotalProtein = array_sum(array_map(static fn (array $item): int => (int) ($item['protein_g'] ?? 0), $nutritionItems));
        $nutritionTotalCarbs = array_sum(array_map(static fn (array $item): int => (int) ($item['carbs_g'] ?? 0), $nutritionItems));
        $nutritionTotalFat = array_sum(array_map(static fn (array $item): int => (int) ($item['fat_g'] ?? 0), $nutritionItems));
        $nutritionIngredientGroups = [
            [
                'title' => 'Proteínas',
                'items' => [
                    ['key' => 'pollo', 'label' => 'Pollo'],
                    ['key' => 'atun', 'label' => 'Atún'],
                    ['key' => 'huevo', 'label' => 'Huevo'],
                    ['key' => 'pavo', 'label' => 'Pavo'],
                    ['key' => 'carne', 'label' => 'Carne magra'],
                    ['key' => 'pescado', 'label' => 'Pescado'],
                    ['key' => 'yogur', 'label' => 'Yogur'],
                    ['key' => 'tofu', 'label' => 'Tofu'],
                    ['key' => 'queso_fresco', 'label' => 'Queso fresco'],
                    ['key' => 'leche', 'label' => 'Leche'],
                    ['key' => 'sardina', 'label' => 'Sardina'],
                    ['key' => 'queso_cottage', 'label' => 'Queso cottage'],
                ],
            ],
            [
                'title' => 'Carbohidratos',
                'items' => [
                    ['key' => 'avena', 'label' => 'Avena'],
                    ['key' => 'arroz', 'label' => 'Arroz'],
                    ['key' => 'quinoa', 'label' => 'Quinoa'],
                    ['key' => 'papa', 'label' => 'Papa'],
                    ['key' => 'camote', 'label' => 'Camote'],
                    ['key' => 'pasta', 'label' => 'Pasta integral'],
                    ['key' => 'pan_integral', 'label' => 'Pan integral'],
                    ['key' => 'fruta', 'label' => 'Fruta'],
                    ['key' => 'legumbres', 'label' => 'Legumbres'],
                    ['key' => 'platano', 'label' => 'Plátano'],
                    ['key' => 'yuca', 'label' => 'Yuca'],
                    ['key' => 'tortilla_integral', 'label' => 'Tortilla integral'],
                    ['key' => 'maiz', 'label' => 'Maíz'],
                    ['key' => 'arepa_integral', 'label' => 'Arepa integral'],
                ],
            ],
            [
                'title' => 'Fibra y grasas buenas',
                'items' => [
                    ['key' => 'verduras', 'label' => 'Verduras'],
                    ['key' => 'ensalada', 'label' => 'Ensalada'],
                    ['key' => 'tomate', 'label' => 'Tomate'],
                    ['key' => 'cebolla', 'label' => 'Cebolla'],
                    ['key' => 'aguacate', 'label' => 'Aguacate'],
                    ['key' => 'frutos_secos', 'label' => 'Frutos secos'],
                    ['key' => 'mani', 'label' => 'Maní'],
                    ['key' => 'aceite_oliva', 'label' => 'Aceite de oliva'],
                    ['key' => 'mantequilla_mani', 'label' => 'Mantequilla de maní'],
                    ['key' => 'chia', 'label' => 'Chía'],
                    ['key' => 'zanahoria', 'label' => 'Zanahoria'],
                    ['key' => 'pepino', 'label' => 'Pepino'],
                    ['key' => 'espinaca', 'label' => 'Espinaca'],
                    ['key' => 'semilla_girasol', 'label' => 'Semilla de girasol'],
                ],
            ],
        ];
        $nutritionClientPayload = [
            'ready' => $nutritionReady,
            'goal_label' => $nutritionGoalLabel,
            'target_kcal' => $nutritionTargetKcal,
            'maintenance_kcal' => $nutritionMaintenanceKcal,
            'items' => $nutritionItems,
        ];
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
        $trainingHasAttendanceToday = (bool) ($trainingStatus['has_attendance_today'] ?? false);
        $trainingCompletedToday = (bool) ($trainingStatus['completed_today'] ?? false);
        $todaySummaryStateLabel = 'En espera';
        $todaySummaryChipClass = '';
        if ($trainingIsActive) {
            $todaySummaryStateLabel = 'Activo';
            $todaySummaryChipClass = 'is-active';
        } elseif ($trainingCompletedToday) {
            $todaySummaryStateLabel = 'Completado';
            $todaySummaryChipClass = 'is-done';
        } elseif ($trainingCanStart || $trainingCanFinish) {
            $todaySummaryStateLabel = 'Listo';
            $todaySummaryChipClass = 'is-ready';
        }
        $todaySummaryAttendanceLabel = $trainingHasAttendanceToday ? 'Registrada' : 'Pendiente';
        $todaySummaryWeeklyLabel = $weeklyGoalVisits.'/'.$weeklyGoalTarget;
        $todaySummaryTimer = $trainingIsActive ? $trainingTimerLabel : '--:--';
        $todaySummaryTip = $trainingHintLine !== ''
            ? $trainingHintLine
            : 'Inicia tu entrenamiento para desbloquear todo el panel.';
    @endphp
    <section class="mx-auto max-w-md space-y-4 relative z-10">
        <div class="top-user-menu">
            @if ($screen !== 'home')
                <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'home']) }}" class="menu-back menu-back-top" aria-label="Volver al inicio">
                    <span class="menu-back-icon" aria-hidden="true">&larr;</span>
                    <span>Atrás</span>
                </a>
            @endif
            @if ($screen === 'home')
                <button id="leaderboard-toggle" type="button" class="leaderboard-chip" aria-haspopup="dialog" aria-expanded="false" aria-controls="leaderboard-modal">
                    <span class="leaderboard-chip-icon" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6.5 5h11l-1.2 7.1a4.7 4.7 0 0 1-4.3 3.9h-.1a4.7 4.7 0 0 1-4.3-3.9L6.5 5Z"></path>
                            <path d="M6.5 7 4 5.8v1.4A3.2 3.2 0 0 0 6.2 10M17.5 7 20 5.8v1.4A3.2 3.2 0 0 1 17.8 10"></path>
                            <path d="M9 19h6M10 16.5V19M14 16.5V19"></path>
                        </svg>
                    </span>
                    <span class="leaderboard-chip-copy">
                        <span class="leaderboard-chip-title">Top 5</span>
                        <span id="leaderboard-chip-status" class="leaderboard-chip-status">Compite este mes</span>
                    </span>
                </button>
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
                <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'nutrition']) }}" class="user-dropdown-item user-dropdown-link" role="menuitem">Guía nutricional</a>
                <form id="client-mobile-logout-form" method="POST" action="{{ route('client-mobile.logout', ['gymSlug' => $gym->slug]) }}">
                    @csrf
                    <button type="submit" class="user-dropdown-item user-dropdown-logout" role="menuitem">Cerrar sesión</button>
                </form>
            </div>
        </div>
        <div class="top-user-menu-spacer" aria-hidden="true"></div>
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
                    <p class="text-[11px] text-slate-300">Recibe avisos cuando tu objetivo semanal está en riesgo o cuando lo completes.</p>
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
                    <button id="open-fitness-modal-trigger" data-open-fitness-modal="1" data-next-screen="progress" type="button" class="menu-cta menu-progress home-btn" aria-label="Completar datos físicos para ver rendimiento">
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

                @if ($fitnessProfileCompleted)
                    <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'nutrition']) }}" class="menu-cta menu-nutrition home-btn" aria-label="Abrir guía nutricional personalizada">
                        <span class="action-badge" aria-hidden="true">
                            <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12.4 6.3c.4-1.8 1.9-3.2 3.8-3.5.2 1.8-.5 3.8-2.4 4.8"></path>
                                <path d="M10.1 6.9c-.9-1.1-2.2-1.8-3.6-1.8 0 1.7.8 3.2 2.2 4"></path>
                                <path d="M9.1 7.9c.8 0 1.5.2 2.1.6.5.3 1.1.3 1.6 0 .6-.4 1.3-.6 2.1-.6 2.6 0 4.7 2.3 4.7 5.2 0 3.6-2.4 6.8-5.4 6.8-.8 0-1.3-.2-1.8-.4a1.9 1.9 0 0 0-1.7 0c-.5.2-1 .4-1.8.4-3 0-5.4-3.2-5.4-6.8 0-2.9 2.1-5.2 4.7-5.2Z"></path>
                            </svg>
                        </span>
                        <span class="action-copy">
                            <span class="action-title">Guía nutricional</span>
                            <span class="action-hint">Comidas, calorías y macros según tus datos</span>
                        </span>
                        <span class="action-arrow" aria-hidden="true">&rsaquo;</span>
                    </a>
                @else
                    <button id="open-fitness-modal-trigger-nutrition" data-open-fitness-modal="1" data-next-screen="nutrition" type="button" class="menu-cta menu-nutrition home-btn" aria-label="Completar datos físicos para activar guía nutricional">
                        <span class="action-badge" aria-hidden="true">
                            <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12.4 6.3c.4-1.8 1.9-3.2 3.8-3.5.2 1.8-.5 3.8-2.4 4.8"></path>
                                <path d="M10.1 6.9c-.9-1.1-2.2-1.8-3.6-1.8 0 1.7.8 3.2 2.2 4"></path>
                                <path d="M9.1 7.9c.8 0 1.5.2 2.1.6.5.3 1.1.3 1.6 0 .6-.4 1.3-.6 2.1-.6 2.6 0 4.7 2.3 4.7 5.2 0 3.6-2.4 6.8-5.4 6.8-.8 0-1.3-.2-1.8-.4a1.9 1.9 0 0 0-1.7 0c-.5.2-1 .4-1.8.4-3 0-5.4-3.2-5.4-6.8 0-2.9 2.1-5.2 4.7-5.2Z"></path>
                            </svg>
                        </span>
                        <span class="action-copy">
                            <span class="action-title">Guía nutricional</span>
                            <span class="action-hint">Completa primero tus datos físicos</span>
                        </span>
                        <span class="action-arrow" aria-hidden="true">&rsaquo;</span>
                    </button>
                @endif
            </section>
        @endif

        @if ($screen === 'home')
            <div id="leaderboard-modal" class="leaderboard-modal hidden" aria-hidden="true">
                <button type="button" class="leaderboard-modal-backdrop" data-leaderboard-close aria-label="Cerrar top 5"></button>
                <div class="leaderboard-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="leaderboard-title">
                    <article class="leaderboard-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="leaderboard-tag">Corona mensual</p>
                                <h3 id="leaderboard-title" class="leaderboard-title">Top 5 del mes</h3>
                                <p class="leaderboard-subtitle">Compite por constancia. La asistencia manda y el tiempo suma como bono justo.</p>
                            </div>
                            <button type="button" class="leaderboard-close" data-leaderboard-close>Cerrar</button>
                        </div>
                        <p id="leaderboard-window" class="leaderboard-window">Mes actual</p>
                        <p id="leaderboard-helper" class="leaderboard-helper">La asistencia pesa más y el tiempo solo suma como extra según tu objetivo personal durante el mes.</p>
                        <div id="leaderboard-top-list" class="leaderboard-list"></div>
                        <p id="leaderboard-empty" class="leaderboard-empty hidden">Todavía no hay puntajes suficientes para mostrar el Top 5 del mes.</p>
                        <article id="leaderboard-current-card" class="leaderboard-current-card hidden">
                            <p class="leaderboard-current-tag">Tu lugar actual</p>
                            <p id="leaderboard-current-title" class="leaderboard-current-title">Aún no entras al ranking</p>
                            <p id="leaderboard-current-meta" class="leaderboard-current-meta">Cuando sumes sesiones válidas este mes aparecerá tu posición aquí.</p>
                            <p id="leaderboard-current-hint" class="leaderboard-current-hint">Recuerda: cada visita válida suma fuerte y el tiempo solo te da empuje extra.</p>
                        </article>
                    </article>
                </div>
            </div>
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
                    <p id="month-attendance-empty" class="month-log-empty {{ count($monthEntries) > 0 ? 'hidden' : '' }}">Todavía no registras asistencias en este mes.</p>
                </article>
            </section>
        @endif

        @if ($screen === 'nutrition')
            <section id="nutrition-view" class="nutrition-screen">
                <article class="nutrition-header-card">
                    <div class="nutrition-header-top">
                        <p class="nutrition-header-tag">Guía nutricional</p>
                        @if ($nutritionTargetKcal !== null)
                            <span class="nutrition-header-chip">{{ $nutritionTargetKcal }} kcal/día</span>
                        @endif
                    </div>
                    <h2 class="nutrition-header-title">{{ $nutritionTitle !== '' ? $nutritionTitle : 'Nutrición personalizada' }}</h2>
                    <p class="nutrition-header-subtitle">{{ $nutritionSubtitle !== '' ? $nutritionSubtitle : 'Esto debes comer hoy según tus datos.' }}</p>
                    <div class="nutrition-header-lines">
                        <p class="nutrition-header-line">Objetivo actual: {{ $nutritionGoalLabel !== '' ? $nutritionGoalLabel : 'General' }}.</p>
                        <p class="nutrition-header-line">{{ $nutritionSummaryLine !== '' ? $nutritionSummaryLine : 'Sin resumen por ahora.' }}</p>
                        <p class="nutrition-header-line">{{ $nutritionImpactLine !== '' ? $nutritionImpactLine : 'Sin impacto estimado por ahora.' }}</p>
                    </div>
                </article>

                <article class="nutrition-allergy-alert">
                    <p class="nutrition-allergy-alert-title">Aviso de alergias</p>
                    <p id="nutrition-allergy-alert-text" class="nutrition-allergy-alert-text">
                        Declara tus alergias en "Personalizar con lo que tengo". Esta guía es informativa y no reemplaza evaluación profesional.
                    </p>
                </article>

                @if ($nutritionReady && count($nutritionItems) > 0)
                    <article class="nutrition-customize-card">
                        <div class="nutrition-customize-head">
                            <p class="nutrition-customize-title">Asistente de despensa</p>
                            <span id="nutrition-customize-status" class="nutrition-customize-status">Plan base</span>
                        </div>
                        <p class="nutrition-customize-text">
                            Elige lo que sí tienes en casa y te armamos un plan más realista para hoy.
                        </p>
                        <div class="nutrition-customize-actions">
                            <button id="nutrition-customize-open" type="button" class="nutrition-customize-trigger">
                                Personalizar con lo que tengo
                            </button>
                            <button id="nutrition-customize-reset" type="button" class="nutrition-customize-reset hidden">
                                Restablecer plan sugerido
                            </button>
                        </div>
                        <p id="nutrition-customize-note" class="nutrition-customize-note">
                            Consejo: marca mínimo 1 proteína y 1 carbohidrato para mejores resultados.
                        </p>
                    </article>

                    <article class="glass-card rounded-3xl p-4">
                        <p class="text-xs font-black uppercase tracking-[.15em] text-cyan-100">Resumen nutricional</p>
                        <div class="nutrition-totals-grid mt-3">
                            <div class="nutrition-total-card">
                                <p class="nutrition-total-label">Total del día</p>
                                <p id="nutrition-total-kcal" class="nutrition-total-value">{{ $nutritionTotalKcal }} kcal</p>
                            </div>
                            <div class="nutrition-total-card">
                                <p class="nutrition-total-label">Mantenimiento</p>
                                <p id="nutrition-maintenance-kcal" class="nutrition-total-value">{{ $nutritionMaintenanceKcal !== null ? $nutritionMaintenanceKcal.' kcal' : '-' }}</p>
                            </div>
                            <div class="nutrition-total-card">
                                <p class="nutrition-total-label">Proteína total</p>
                                <p id="nutrition-total-protein" class="nutrition-total-value">{{ $nutritionTotalProtein }} g</p>
                            </div>
                            <div class="nutrition-total-card">
                                <p class="nutrition-total-label">Carb/Grasa total</p>
                                <p class="nutrition-total-value">
                                    <span id="nutrition-total-carbs">{{ $nutritionTotalCarbs }}</span> g
                                    /
                                    <span id="nutrition-total-fat">{{ $nutritionTotalFat }}</span> g
                                </p>
                            </div>
                        </div>
                    </article>

                    <article class="glass-card rounded-3xl p-4">
                        <p class="text-xs font-black uppercase tracking-[.15em] text-cyan-100">Plan de comidas de hoy</p>
                        <div id="nutrition-day-list" class="nutrition-day-list mt-3">
                            @foreach ($nutritionItems as $nutritionItem)
                                <article class="nutrition-day-item">
                                    <img
                                        src="{{ (string) ($nutritionItem['image_url'] ?? asset('images/nutrition/nutri-lunch.svg')) }}"
                                        alt="Plato recomendado"
                                        class="nutrition-day-image"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                    <div>
                                        <div class="nutrition-day-head">
                                            <p class="nutrition-day-slot">{{ (string) ($nutritionItem['slot'] ?? 'Comida') }}</p>
                                            <p class="nutrition-day-kcal">{{ (int) ($nutritionItem['kcal'] ?? 0) }} kcal</p>
                                        </div>
                                        <p class="nutrition-day-dish">{{ (string) ($nutritionItem['dish'] ?? 'Plato sugerido') }}</p>
                                        <p class="nutrition-day-portion">{{ (string) ($nutritionItem['portion'] ?? 'Porción sugerida') }}</p>
                                        <p class="nutrition-day-macros">
                                            P {{ (int) ($nutritionItem['protein_g'] ?? 0) }}g
                                            | C {{ (int) ($nutritionItem['carbs_g'] ?? 0) }}g
                                            | G {{ (int) ($nutritionItem['fat_g'] ?? 0) }}g
                                        </p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </article>

                    @if ($nutritionFootnote !== '')
                        <p id="nutrition-screen-footnote" class="nutrition-screen-footnote">{{ $nutritionFootnote }}</p>
                    @endif

                    <article id="nutrition-shopping-card" class="glass-card rounded-3xl p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-black uppercase tracking-[.15em] text-cyan-100">Compra inteligente</p>
                            <span id="nutrition-shopping-count" class="nutrition-customize-status">0 productos</span>
                        </div>
                        <p id="nutrition-shopping-preview" class="mt-2 text-xs text-slate-300">
                            Administra tu lista en un modal para no alargar esta pantalla.
                        </p>
                        <div class="nutrition-customize-actions mt-3">
                            <button id="nutrition-shopping-open" type="button" class="nutrition-customize-trigger">
                                Ver lista en modal
                            </button>
                            <button type="button" class="nutrition-customize-reset" data-nutrition-open-customizer>
                                Actualizar platos
                            </button>
                        </div>
                    </article>

                    <div id="nutrition-customize-modal" class="nutrition-customize-modal hidden" aria-hidden="true">
                        <button type="button" class="nutrition-customize-backdrop" data-nutrition-customize-close aria-label="Cerrar personalización"></button>
                        <article class="nutrition-customize-panel" role="dialog" aria-modal="true" aria-labelledby="nutrition-customize-title">
                            <div class="nutrition-customize-top">
                                <div>
                                    <p class="nutrition-customize-eyebrow">Modo wow</p>
                                    <h3 id="nutrition-customize-title" class="nutrition-customize-heading">Plan según lo que tienes</h3>
                                    <p class="nutrition-customize-subtitle">Marca ingredientes reales, presupuesto y tiempo. Nosotros ajustamos platos y macros.</p>
                                </div>
                                <button type="button" class="nutrition-customize-close" data-nutrition-customize-close>Cerrar</button>
                            </div>

                            <div class="nutrition-customize-groups">
                                @foreach ($nutritionIngredientGroups as $nutritionGroup)
                                    <section class="nutrition-customize-group">
                                        <p class="nutrition-customize-group-title">{{ (string) ($nutritionGroup['title'] ?? 'Ingredientes') }}</p>
                                        <div class="nutrition-customize-chip-grid">
                                            @foreach ((array) ($nutritionGroup['items'] ?? []) as $nutritionIngredient)
                                                @php
                                                    $ingredientKey = trim((string) ($nutritionIngredient['key'] ?? ''));
                                                    $ingredientLabel = trim((string) ($nutritionIngredient['label'] ?? $ingredientKey));
                                                @endphp
                                                @if ($ingredientKey !== '')
                                                    <button
                                                        type="button"
                                                        class="nutrition-customize-chip"
                                                        data-nutrition-ingredient="{{ $ingredientKey }}"
                                                        aria-pressed="false"
                                                    >
                                                        {{ $ingredientLabel }}
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach

                                <section class="nutrition-customize-group">
                                    <div class="nutrition-allergy-head">
                                        <p class="nutrition-customize-group-title">Alergias e intolerancias</p>
                                        <span class="nutrition-allergy-chip">Se excluyen automáticamente</span>
                                    </div>
                                    <p class="nutrition-allergy-text">
                                        Marca lo que te causa reacción. Esos ingredientes no se usarán en tu plan ni en la lista de compras sugerida.
                                    </p>
                                    <div class="nutrition-customize-chip-grid">
                                        <button type="button" class="nutrition-customize-chip" data-nutrition-allergy="huevo" aria-pressed="false">Huevo</button>
                                        <button type="button" class="nutrition-customize-chip" data-nutrition-allergy="lacteos" aria-pressed="false">Lácteos</button>
                                        <button type="button" class="nutrition-customize-chip" data-nutrition-allergy="pescado" aria-pressed="false">Pescado/mariscos</button>
                                        <button type="button" class="nutrition-customize-chip" data-nutrition-allergy="mani" aria-pressed="false">Maní</button>
                                        <button type="button" class="nutrition-customize-chip" data-nutrition-allergy="frutos_secos" aria-pressed="false">Frutos secos</button>
                                        <button type="button" class="nutrition-customize-chip" data-nutrition-allergy="gluten" aria-pressed="false">Gluten</button>
                                        <button type="button" class="nutrition-customize-chip" data-nutrition-allergy="soya" aria-pressed="false">Soya</button>
                                    </div>
                                    <p class="nutrition-allergy-disclaimer">
                                        Aviso legal: debes verificar ingredientes y etiquetas antes de consumir. La app no sustituye consejo médico ni se responsabiliza por omisiones del usuario.
                                    </p>
                                </section>
                            </div>

                            <div class="nutrition-customize-filters">
                                <label class="nutrition-customize-select-wrap">
                                    <span class="nutrition-customize-label">Tiempo disponible</span>
                                    <select id="nutrition-customize-time" class="nutrition-customize-select">
                                        <option value="rapido">Rápido (15-20 min)</option>
                                        <option value="normal" selected>Normal (20-40 min)</option>
                                        <option value="relajado">Relajado (40+ min)</option>
                                    </select>
                                </label>
                                <label class="nutrition-customize-select-wrap">
                                    <span class="nutrition-customize-label">Presupuesto</span>
                                    <select id="nutrition-customize-budget" class="nutrition-customize-select">
                                        <option value="bajo">Bajo</option>
                                        <option value="medio" selected>Medio</option>
                                        <option value="alto">Libre</option>
                                    </select>
                                </label>
                            </div>

                            <label class="nutrition-customize-toggle">
                                <input id="nutrition-customize-strict" type="checkbox" value="1">
                                Solo platos que usen ingredientes seleccionados.
                            </label>

                            <label class="nutrition-customize-toggle">
                                <input id="nutrition-customize-allergy-ack" type="checkbox" value="1">
                                Confirmo que revisé alergias e ingredientes antes de consumir.
                            </label>

                            <p id="nutrition-customize-feedback" class="nutrition-customize-feedback">
                                Selecciona tus ingredientes para personalizar tus comidas.
                            </p>

                            <section class="nutrition-customize-group">
                                <div class="nutrition-allergy-head">
                                    <p class="nutrition-customize-group-title">Vista previa de platos</p>
                                    <span id="nutrition-customize-preview-status" class="nutrition-allergy-chip">Sin aplicar</span>
                                </div>
                                <p class="nutrition-allergy-text">
                                    Aquí ves qué platos se arman con lo que marques, antes de aplicar el plan.
                                </p>
                                <ul id="nutrition-customize-preview-list" class="nutrition-preview-list"></ul>
                                <p id="nutrition-customize-preview-empty" class="nutrition-shopping-empty">Aún no hay vista previa disponible.</p>
                            </section>

                            <div class="nutrition-customize-panel-actions">
                                <button id="nutrition-customize-apply" type="button" class="nutrition-customize-trigger">Aplicar plan wow</button>
                                <button type="button" class="nutrition-customize-reset" data-nutrition-customize-close>Cancelar</button>
                            </div>
                        </article>
                    </div>

                    <div id="nutrition-shopping-modal" class="nutrition-customize-modal hidden" aria-hidden="true">
                        <button type="button" class="nutrition-customize-backdrop" data-nutrition-shopping-close aria-label="Cerrar lista de compras"></button>
                        <article class="nutrition-customize-panel nutrition-shopping-panel" role="dialog" aria-modal="true" aria-labelledby="nutrition-shopping-title">
                            <div class="nutrition-customize-top">
                                <div>
                                    <p class="nutrition-customize-eyebrow">Compra inteligente</p>
                                    <h3 id="nutrition-shopping-title" class="nutrition-customize-heading">Lista de compras en modal</h3>
                                    <p class="nutrition-customize-subtitle">No estiramos la pantalla. Gestiona todo aquí y vuelve a tu plan.</p>
                                </div>
                                <button type="button" class="nutrition-customize-close" data-nutrition-shopping-close>Cerrar</button>
                            </div>

                            <div class="nutrition-customize-actions mt-3">
                                <button id="nutrition-shopping-autofill" type="button" class="nutrition-customize-trigger">Actualizar lista por plan</button>
                                <button type="button" class="nutrition-customize-reset" data-nutrition-open-customizer>Ver platos con lo que tengo</button>
                            </div>

                            <p class="mt-2 text-xs text-slate-300">
                                Si agregas nuevos ingredientes, entra a "Ver platos con lo que tengo", márcalos y aplica para ver el plato final.
                            </p>
                            <ul id="nutrition-shopping-list" class="nutrition-shopping-list mt-3"></ul>
                            <p id="nutrition-shopping-empty" class="nutrition-shopping-empty">Aún no tienes productos en tu lista.</p>
                            <div class="nutrition-shopping-add-row">
                                <input id="nutrition-shopping-input" type="text" class="module-input nutrition-shopping-input" maxlength="80" placeholder="Ej: yogur griego, tomate, avena...">
                                <button id="nutrition-shopping-add" type="button" class="nutrition-shopping-add-btn">Agregar</button>
                            </div>
                            <button id="nutrition-shopping-clear-done" type="button" class="nutrition-shopping-clear-btn">Limpiar marcados</button>
                            <p class="nutrition-allergy-disclaimer">
                                Nota: agregar productos en esta lista no cambia platos automáticamente. Para generar platos, usa "Ver platos con lo que tengo".
                            </p>
                        </article>
                    </div>
                @else
                    <article class="nutrition-empty-card">
                        Completa tus datos físicos para generar una guía nutricional personalizada.
                    </article>
                    <a href="{{ route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'physical']) }}" class="module-action module-action-secondary">
                        Configurar datos físicos
                    </a>
                @endif
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

                <article class="today-summary-card">
                    <div class="today-summary-head">
                        <p class="today-summary-title">Resumen de hoy</p>
                        <span id="today-summary-state-chip" class="today-summary-chip {{ $todaySummaryChipClass }}">{{ $todaySummaryStateLabel }}</span>
                    </div>
                    <div class="today-summary-grid">
                        <div class="today-summary-item">
                            <p class="today-summary-label">Asistencia</p>
                            <p id="today-summary-attendance" class="today-summary-value">{{ $todaySummaryAttendanceLabel }}</p>
                        </div>
                        <div class="today-summary-item">
                            <p class="today-summary-label">Meta semanal</p>
                            <p id="today-summary-weekly" class="today-summary-value">{{ $todaySummaryWeeklyLabel }}</p>
                        </div>
                        <div class="today-summary-item">
                            <p class="today-summary-label">Tiempo activo</p>
                            <p id="today-summary-timer" class="today-summary-value">{{ $todaySummaryTimer }}</p>
                        </div>
                    </div>
                    <p id="today-summary-tip" class="today-summary-tip">{{ $todaySummaryTip }}</p>
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
                            <span id="weekly-goal-days-left-label">Días restantes: {{ $weeklyGoalDaysLeft }}</span>
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
                                <span class="body-state-label">Recuperación</span>
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
                                    @foreach ($trainingExercises as $exerciseIndex => $exercise)
                                        @php
                                            $exerciseName = trim((string) ($exercise['name'] ?? 'Ejercicio'));
                                            $exerciseDose = trim((string) ($exercise['prescription'] ?? '3 x 10'));
                                        @endphp
                                        <li class="training-item">
                                            <span class="training-item-left">
                                                <button
                                                    type="button"
                                                    class="training-item-toggle"
                                                    data-training-check="exercise-{{ $exerciseIndex }}"
                                                    aria-pressed="false"
                                                    aria-label="Marcar ejercicio completado"
                                                >○</button>
                                                <span class="training-item-name">{{ $exerciseName }}</span>
                                            </span>
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
                            <div class="training-completion-box">
                                <div class="training-completion-head">
                                    <p class="training-completion-title">Avance de la sesión</p>
                                    <p id="training-completion-value" class="training-completion-value">0/{{ min(6, count($trainingExercises)) }} ejercicios</p>
                                </div>
                                <div class="training-completion-track">
                                    <span id="training-completion-fill" class="training-completion-fill" style="width: 0%;"></span>
                                </div>
                                <p id="training-completion-hint" class="training-completion-hint">Marca cada ejercicio completado para mantener tu foco.</p>
                            </div>
                            <div class="training-rest-box">
                                <p class="training-rest-title">Descanso guiado</p>
                                <div class="training-rest-row">
                                    <p id="training-rest-timer" class="training-rest-timer">01:00</p>
                                    <button id="training-rest-toggle-btn" type="button" class="training-rest-btn">Iniciar 60s</button>
                                </div>
                                <p id="training-rest-hint" class="training-rest-hint">Usa este contador entre series para sostener intensidad.</p>
                            </div>
                        </div>
                        <div class="progress-lock-overlay" aria-hidden="{{ $progressUnlocked ? 'true' : 'false' }}">
                            <div class="progress-lock-overlay-content">
                                <p class="progress-lock-title">Progreso en espera</p>
                                <p class="progress-lock-text" data-progress-lock-message>{{ $progressLockReason }}</p>
                            </div>
                        </div>
                    </div>
                    </div>
                </article>
            </section>
            <div id="training-fab-dock" class="training-fab-dock">
                <div class="training-fab-shell">
                    <div class="training-fab-status-row">
                        <p id="training-session-status" class="training-fab-status">{{ $trainingStatusLabel }}</p>
                        <p id="training-session-timer" class="training-fab-timer {{ $trainingIsActive ? '' : 'hidden' }}">
                            <span id="training-session-timer-value">{{ $trainingTimerLabel }}</span>
                        </p>
                    </div>
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
                        <button
                            id="training-idle-btn"
                            type="button"
                            class="module-action training-idle-btn {{ ($trainingCanStart || $trainingCanFinish) ? 'hidden' : '' }}"
                            disabled
                        >
                            {{ $trainingHasAttendanceToday ? 'Entrenamiento completado' : 'Registra asistencia para iniciar' }}
                        </button>
                    </div>
                    <p id="training-session-hint" class="training-session-hint">{{ $trainingHintLine }}</p>
                    <p id="training-session-feedback" class="training-session-feedback hidden"></p>
                </div>
            </div>
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
                        <p class="profile-kpi-label">último ingreso</p>
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
                                <p class="fitness-meta-label">Fecha de nacimiento</p>
                                <p class="fitness-meta-value">{{ $fitnessBirthDateLabel }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Edad (auto)</p>
                                <p class="fitness-meta-value">{{ $fitnessAgeLabel }}</p>
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
                                <p class="fitness-meta-label">Objetivo principal</p>
                                <p class="fitness-meta-value">{{ $fitnessPrimaryGoalLabel }}</p>
                            </div>
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Objetivo secundario</p>
                                <p class="fitness-meta-value">{{ $fitnessSecondaryGoalLabel !== '' ? $fitnessSecondaryGoalLabel : 'Sin secundario' }}</p>
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
                            <div class="fitness-meta-card">
                                <p class="fitness-meta-label">Objetivo combinado</p>
                                <p class="fitness-meta-value">{{ $fitnessGoalLabel }}</p>
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
                            <p class="fitness-profile-note">última actualización: {{ $fitnessUpdatedLabel }}</p>
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
                                <p class="fitness-onboarding-help">Necesitamos estos datos para calcular IMC, metabolismo y habilitar tu pantalla de rendimiento y tu guía nutricional.</p>
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
                            'nextScreen' => $openFitnessModalNextScreen ?? 'progress',
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
    const nutritionInitialPayload = @json($nutritionClientPayload);
    const nutritionImageMap = {
        breakfast: @json(asset('images/nutrition/nutri-breakfast.svg')),
        lunch: @json(asset('images/nutrition/nutri-lunch.svg')),
        snack: @json(asset('images/nutrition/nutri-snack.svg')),
        dinner: @json(asset('images/nutrition/nutri-dinner.svg')),
    };

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
    const trainingFinishConfirmModal = document.getElementById('training-finish-confirm-modal');
    const trainingFinishConfirmAcceptBtn = document.getElementById('training-finish-confirm-accept');
    const trainingFinishConfirmCancelBtn = document.getElementById('training-finish-confirm-cancel');
    const trainingFinishConfirmCancelEls = Array.from(document.querySelectorAll('[data-training-finish-confirm-cancel]'));
    const trainingWinModal = document.getElementById('training-win-modal');
    const trainingWinTextEl = document.getElementById('training-win-text');
    const trainingWinExercisesEl = document.getElementById('training-win-exercises');
    const trainingWinWeeklyEl = document.getElementById('training-win-weekly');
    const trainingWinConsistencyEl = document.getElementById('training-win-consistency');
    const trainingWinCloseBtn = document.getElementById('training-win-close-btn');
    const trainingWinCloseSecondaryBtn = document.getElementById('training-win-close-secondary');
    const trainingWinCloseEls = Array.from(document.querySelectorAll('[data-training-win-close]'));

    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userMenuPanel = document.getElementById('user-menu-panel');
    const logoutForm = document.getElementById('client-mobile-logout-form');
    const leaderboardToggleBtn = document.getElementById('leaderboard-toggle');
    const leaderboardChipStatusEl = document.getElementById('leaderboard-chip-status');
    const leaderboardModal = document.getElementById('leaderboard-modal');
    const leaderboardCloseEls = Array.from(document.querySelectorAll('[data-leaderboard-close]'));
    const leaderboardWindowEl = document.getElementById('leaderboard-window');
    const leaderboardHelperEl = document.getElementById('leaderboard-helper');
    const leaderboardListEl = document.getElementById('leaderboard-top-list');
    const leaderboardEmptyEl = document.getElementById('leaderboard-empty');
    const leaderboardCurrentCardEl = document.getElementById('leaderboard-current-card');
    const leaderboardCurrentTitleEl = document.getElementById('leaderboard-current-title');
    const leaderboardCurrentMetaEl = document.getElementById('leaderboard-current-meta');
    const leaderboardCurrentHintEl = document.getElementById('leaderboard-current-hint');
    const openProfileEditBtn = document.getElementById('open-profile-edit');
    const cancelProfileEditBtn = document.getElementById('cancel-profile-edit');
    const profileEditPanel = document.getElementById('profile-edit-panel');
    const profileCurrentPasswordInput = document.getElementById('profile-current-password');
    const openPhysicalEditBtn = document.getElementById('open-physical-edit');
    const closePhysicalEditBtn = document.getElementById('close-physical-edit');
    const physicalEditPanel = document.getElementById('physical-edit-panel');
    const openFitnessModalTriggers = Array.from(document.querySelectorAll('[data-open-fitness-modal]'));
    const fitnessModal = document.getElementById('fitness-onboarding-modal');
    const fitnessModalNextScreenInput = fitnessModal ? fitnessModal.querySelector('input[name="next_screen"]') : null;
    const allowedFitnessNextScreens = ['home', 'progress', 'physical', 'nutrition'];
    const defaultFitnessNextScreenRaw = fitnessModalNextScreenInput instanceof HTMLInputElement
        ? String(fitnessModalNextScreenInput.value || '').trim().toLowerCase()
        : '';
    const fitnessModalDefaultNextScreen = allowedFitnessNextScreens.includes(defaultFitnessNextScreenRaw)
        ? defaultFitnessNextScreenRaw
        : 'progress';
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
    const trainingFabShellEl = document.querySelector('.training-fab-shell');
    const trainingStartBtn = document.getElementById('training-start-btn');
    const trainingFinishBtn = document.getElementById('training-finish-btn');
    const trainingIdleBtn = document.getElementById('training-idle-btn');
    const todaySummaryStateChipEl = document.getElementById('today-summary-state-chip');
    const todaySummaryAttendanceEl = document.getElementById('today-summary-attendance');
    const todaySummaryWeeklyEl = document.getElementById('today-summary-weekly');
    const todaySummaryTimerEl = document.getElementById('today-summary-timer');
    const todaySummaryTipEl = document.getElementById('today-summary-tip');
    const trainingCompletionValueEl = document.getElementById('training-completion-value');
    const trainingCompletionFillEl = document.getElementById('training-completion-fill');
    const trainingCompletionHintEl = document.getElementById('training-completion-hint');
    const trainingRestToggleBtn = document.getElementById('training-rest-toggle-btn');
    const trainingRestTimerEl = document.getElementById('training-rest-timer');
    const trainingRestHintEl = document.getElementById('training-rest-hint');
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
    const nutritionDayListEl = document.getElementById('nutrition-day-list');
    const nutritionCustomizeOpenBtn = document.getElementById('nutrition-customize-open');
    const nutritionCustomizeResetBtn = document.getElementById('nutrition-customize-reset');
    const nutritionCustomizeStatusEl = document.getElementById('nutrition-customize-status');
    const nutritionCustomizeNoteEl = document.getElementById('nutrition-customize-note');
    const nutritionTotalKcalEl = document.getElementById('nutrition-total-kcal');
    const nutritionTotalProteinEl = document.getElementById('nutrition-total-protein');
    const nutritionTotalCarbsEl = document.getElementById('nutrition-total-carbs');
    const nutritionTotalFatEl = document.getElementById('nutrition-total-fat');
    const nutritionAllergyAlertTextEl = document.getElementById('nutrition-allergy-alert-text');
    const nutritionCustomizeModal = document.getElementById('nutrition-customize-modal');
    const nutritionCustomizeCloseEls = Array.from(document.querySelectorAll('[data-nutrition-customize-close]'));
    const nutritionCustomizeIngredientBtns = Array.from(document.querySelectorAll('[data-nutrition-ingredient]'));
    const nutritionCustomizeAllergyBtns = Array.from(document.querySelectorAll('[data-nutrition-allergy]'));
    const nutritionCustomizeTimeEl = document.getElementById('nutrition-customize-time');
    const nutritionCustomizeBudgetEl = document.getElementById('nutrition-customize-budget');
    const nutritionCustomizeStrictEl = document.getElementById('nutrition-customize-strict');
    const nutritionCustomizeAllergyAckEl = document.getElementById('nutrition-customize-allergy-ack');
    const nutritionCustomizeFeedbackEl = document.getElementById('nutrition-customize-feedback');
    const nutritionCustomizeApplyBtn = document.getElementById('nutrition-customize-apply');
    const nutritionCustomizePreviewListEl = document.getElementById('nutrition-customize-preview-list');
    const nutritionCustomizePreviewEmptyEl = document.getElementById('nutrition-customize-preview-empty');
    const nutritionCustomizePreviewStatusEl = document.getElementById('nutrition-customize-preview-status');
    const nutritionOpenCustomizerBtns = Array.from(document.querySelectorAll('[data-nutrition-open-customizer]'));
    const nutritionShoppingCountEl = document.getElementById('nutrition-shopping-count');
    const nutritionShoppingPreviewEl = document.getElementById('nutrition-shopping-preview');
    const nutritionShoppingOpenBtn = document.getElementById('nutrition-shopping-open');
    const nutritionShoppingModal = document.getElementById('nutrition-shopping-modal');
    const nutritionShoppingCloseEls = Array.from(document.querySelectorAll('[data-nutrition-shopping-close]'));
    const nutritionShoppingListEl = document.getElementById('nutrition-shopping-list');
    const nutritionShoppingEmptyEl = document.getElementById('nutrition-shopping-empty');
    const nutritionShoppingAutoFillBtn = document.getElementById('nutrition-shopping-autofill');
    const nutritionShoppingInputEl = document.getElementById('nutrition-shopping-input');
    const nutritionShoppingAddBtn = document.getElementById('nutrition-shopping-add');
    const nutritionShoppingClearDoneBtn = document.getElementById('nutrition-shopping-clear-done');
    const nutritionAllowedIngredientKeys = Array.from(new Set(
        nutritionCustomizeIngredientBtns
            .map((button) => String(button.dataset.nutritionIngredient || '').trim())
            .filter((value) => value !== '')
    ));

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
    let trainingRestTimer = null;
    let trainingRestRemainingSeconds = 60;
    let trainingRestRunning = false;
    let previousTrainingIsActive = false;
    let previousTrainingCompleted = false;
    let trainingChecklist = {};
    let trainingChecklistDate = '';
    let moduleLoaderFailSafeTimer = null;
    let moduleLoaderLocked = false;
    let actionGuideMode = '';
    let actionGuideDismissedMode = '';
    let actionGuideCtaHandler = null;
    let trainingFinishConfirmResolver = null;
    let cameraPermissionProbeState = 'unknown';
    let directPermissionPromptArmed = false;
    let directPermissionPromptDone = false;
    const sectionStateStorageKey = 'client-mobile:progress:sections:v1';
    const actionGuideSeenStorageBase = 'client-mobile:action-guide-seen:v1';
    const actionGuideIdentity = String(shell.dataset.actionGuideKey || '').trim();
    const actionGuideSeenStorageKey = actionGuideSeenStorageBase + ':' + (actionGuideIdentity !== '' ? actionGuideIdentity : 'global');
    const trainingChecklistStorageBase = 'client-mobile:training-checklist:v1';
    const trainingChecklistStorageKey = trainingChecklistStorageBase + ':' + (actionGuideIdentity !== '' ? actionGuideIdentity : 'global');
    const trainingWinSeenStorageBase = 'client-mobile:training-win-seen:v1';
    const trainingWinSeenStorageKey = trainingWinSeenStorageBase + ':' + (actionGuideIdentity !== '' ? actionGuideIdentity : 'global');
    const nutritionStorageBase = 'client-mobile:nutrition-custom:v3';
    const nutritionStorageKey = nutritionStorageBase + ':' + (actionGuideIdentity !== '' ? actionGuideIdentity : 'global');
    const nutritionShoppingStorageBase = 'client-mobile:nutrition-shopping:v1';
    const nutritionShoppingStorageKey = nutritionShoppingStorageBase + ':' + (actionGuideIdentity !== '' ? actionGuideIdentity : 'global');
    const nutritionSlotOrder = ['Desayuno', 'Almuerzo', 'Snack', 'Cena'];
    const nutritionSlotShares = {
        Desayuno: 0.28,
        Almuerzo: 0.34,
        Snack: 0.14,
        Cena: 0.24,
    };
    const nutritionIngredientCatalog = {
        pollo: { label: 'Pollo', category: 'protein', portion: '170 g', kcal: 280, protein: 45, carbs: 0, fat: 8, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '600 g' },
        atun: { label: 'Atún', category: 'protein', portion: '160 g', kcal: 220, protein: 41, carbs: 0, fat: 5, prep: 'rapido', budget: 'medio', slots: ['Almuerzo', 'Cena'], shopping: '2 latas' },
        huevo: { label: 'Huevo', category: 'protein', portion: '3 unidades', kcal: 210, protein: 18, carbs: 1, fat: 15, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack', 'Cena'], shopping: '1 docena' },
        pavo: { label: 'Pavo', category: 'protein', portion: '170 g', kcal: 260, protein: 43, carbs: 0, fat: 7, prep: 'normal', budget: 'medio', slots: ['Almuerzo', 'Cena'], shopping: '500 g' },
        carne: { label: 'Carne magra', category: 'protein', portion: '170 g', kcal: 310, protein: 40, carbs: 0, fat: 14, prep: 'normal', budget: 'medio', slots: ['Almuerzo', 'Cena'], shopping: '500 g' },
        pescado: { label: 'Pescado', category: 'protein', portion: '170 g', kcal: 250, protein: 38, carbs: 0, fat: 10, prep: 'normal', budget: 'alto', slots: ['Almuerzo', 'Cena'], shopping: '500 g' },
        yogur: { label: 'Yogur', category: 'protein', portion: '250 g', kcal: 170, protein: 17, carbs: 14, fat: 5, prep: 'rapido', budget: 'medio', slots: ['Desayuno', 'Snack'], shopping: '1 kg' },
        tofu: { label: 'Tofu', category: 'protein', portion: '180 g', kcal: 170, protein: 21, carbs: 5, fat: 8, prep: 'normal', budget: 'medio', slots: ['Almuerzo', 'Cena'], shopping: '400 g' },
        queso_fresco: { label: 'Queso fresco', category: 'protein', portion: '80 g', kcal: 210, protein: 15, carbs: 2, fat: 15, prep: 'rapido', budget: 'medio', slots: ['Desayuno', 'Snack', 'Cena'], shopping: '250 g' },
        queso_cottage: { label: 'Queso cottage', category: 'protein', portion: '120 g', kcal: 118, protein: 14, carbs: 4, fat: 4, prep: 'rapido', budget: 'medio', slots: ['Desayuno', 'Snack', 'Cena'], shopping: '250 g' },
        leche: { label: 'Leche', category: 'protein', portion: '250 ml', kcal: 145, protein: 9, carbs: 12, fat: 7, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack'], shopping: '1 L' },
        sardina: { label: 'Sardina', category: 'protein', portion: '130 g', kcal: 230, protein: 30, carbs: 0, fat: 11, prep: 'rapido', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '2 latas' },

        avena: { label: 'Avena', category: 'carb', portion: '70 g', kcal: 266, protein: 9, carbs: 46, fat: 5, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack'], shopping: '500 g' },
        arroz: { label: 'Arroz', category: 'carb', portion: '170 g', kcal: 220, protein: 4, carbs: 48, fat: 1, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '1 kg' },
        quinoa: { label: 'Quinoa', category: 'carb', portion: '140 g', kcal: 220, protein: 8, carbs: 38, fat: 4, prep: 'normal', budget: 'medio', slots: ['Almuerzo', 'Cena'], shopping: '500 g' },
        papa: { label: 'Papa', category: 'carb', portion: '220 g', kcal: 190, protein: 5, carbs: 43, fat: 0, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '1 kg' },
        camote: { label: 'Camote', category: 'carb', portion: '220 g', kcal: 200, protein: 4, carbs: 46, fat: 0, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '1 kg' },
        pasta: { label: 'Pasta integral', category: 'carb', portion: '150 g', kcal: 260, protein: 10, carbs: 51, fat: 2, prep: 'normal', budget: 'medio', slots: ['Almuerzo', 'Cena'], shopping: '500 g' },
        pan_integral: { label: 'Pan integral', category: 'carb', portion: '2 rebanadas', kcal: 170, protein: 7, carbs: 30, fat: 3, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack'], shopping: '1 funda' },
        fruta: { label: 'Fruta', category: 'fruit', portion: '1 porción', kcal: 90, protein: 1, carbs: 22, fat: 0, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack'], shopping: '6 unidades' },
        legumbres: { label: 'Legumbres', category: 'carb', portion: '130 g', kcal: 190, protein: 12, carbs: 31, fat: 2, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '500 g' },
        platano: { label: 'Plátano', category: 'fruit', portion: '1 unidad', kcal: 120, protein: 1, carbs: 31, fat: 0, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack'], shopping: '5 unidades' },
        yuca: { label: 'Yuca', category: 'carb', portion: '180 g', kcal: 220, protein: 2, carbs: 52, fat: 0, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '1 kg' },
        tortilla_integral: { label: 'Tortilla integral', category: 'carb', portion: '2 unidades', kcal: 160, protein: 6, carbs: 28, fat: 3, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Almuerzo', 'Cena'], shopping: '1 paquete' },
        maiz: { label: 'Maíz', category: 'carb', portion: '160 g', kcal: 180, protein: 5, carbs: 39, fat: 2, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '4 choclos o 1 funda' },
        arepa_integral: { label: 'Arepa integral', category: 'carb', portion: '1 unidad', kcal: 170, protein: 4, carbs: 31, fat: 3, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack'], shopping: '4 unidades' },

        verduras: { label: 'Verduras', category: 'veggie', portion: '200 g', kcal: 70, protein: 4, carbs: 12, fat: 1, prep: 'normal', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '1 kg mix' },
        ensalada: { label: 'Ensalada', category: 'veggie', portion: '200 g', kcal: 55, protein: 2, carbs: 10, fat: 1, prep: 'rapido', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '1 funda grande' },
        tomate: { label: 'Tomate', category: 'veggie', portion: '150 g', kcal: 30, protein: 1, carbs: 7, fat: 0, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Almuerzo', 'Cena'], shopping: '4 unidades' },
        cebolla: { label: 'Cebolla', category: 'veggie', portion: '120 g', kcal: 45, protein: 1, carbs: 10, fat: 0, prep: 'rapido', budget: 'bajo', slots: ['Almuerzo', 'Cena'], shopping: '3 unidades' },
        zanahoria: { label: 'Zanahoria', category: 'veggie', portion: '150 g', kcal: 55, protein: 1, carbs: 13, fat: 0, prep: 'rapido', budget: 'bajo', slots: ['Almuerzo', 'Cena', 'Snack'], shopping: '4 unidades' },
        pepino: { label: 'Pepino', category: 'veggie', portion: '150 g', kcal: 24, protein: 1, carbs: 5, fat: 0, prep: 'rapido', budget: 'bajo', slots: ['Almuerzo', 'Cena', 'Snack'], shopping: '2 unidades' },
        espinaca: { label: 'Espinaca', category: 'veggie', portion: '120 g', kcal: 30, protein: 3, carbs: 4, fat: 0, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Almuerzo', 'Cena'], shopping: '1 atado' },
        aguacate: { label: 'Aguacate', category: 'fat', portion: '80 g', kcal: 128, protein: 2, carbs: 6, fat: 12, prep: 'rapido', budget: 'medio', slots: ['Desayuno', 'Almuerzo', 'Cena'], shopping: '2 unidades' },
        frutos_secos: { label: 'Frutos secos', category: 'fat', portion: '20 g', kcal: 120, protein: 4, carbs: 4, fat: 10, prep: 'rapido', budget: 'alto', slots: ['Snack', 'Desayuno'], shopping: '200 g' },
        mani: { label: 'Maní', category: 'fat', portion: '20 g', kcal: 110, protein: 5, carbs: 4, fat: 9, prep: 'rapido', budget: 'bajo', slots: ['Snack', 'Desayuno'], shopping: '250 g' },
        aceite_oliva: { label: 'Aceite de oliva', category: 'fat', portion: '1 cda', kcal: 90, protein: 0, carbs: 0, fat: 10, prep: 'rapido', budget: 'medio', slots: ['Almuerzo', 'Cena'], shopping: '250 ml' },
        mantequilla_mani: { label: 'Mantequilla de maní', category: 'fat', portion: '1 cda', kcal: 95, protein: 4, carbs: 3, fat: 8, prep: 'rapido', budget: 'medio', slots: ['Desayuno', 'Snack'], shopping: '1 frasco' },
        chia: { label: 'Chía', category: 'fat', portion: '15 g', kcal: 70, protein: 3, carbs: 6, fat: 5, prep: 'rapido', budget: 'medio', slots: ['Desayuno', 'Snack'], shopping: '150 g' },
        semilla_girasol: { label: 'Semilla de girasol', category: 'fat', portion: '15 g', kcal: 87, protein: 3, carbs: 3, fat: 7, prep: 'rapido', budget: 'bajo', slots: ['Desayuno', 'Snack'], shopping: '200 g' },
    };
    const nutritionSlotBlueprints = {
        Desayuno: { required: ['protein', 'carb'], optional: ['fruit', 'fat'] },
        Almuerzo: { required: ['protein', 'carb', 'veggie'], optional: ['fat'] },
        Snack: { required: ['protein', 'fruit_or_carb'], optional: ['fat'] },
        Cena: { required: ['protein', 'veggie'], optional: ['carb', 'fat'] },
    };
    const nutritionDefaultShoppingIdeas = [
        'huevo',
        'avena',
        'yogur',
        'fruta',
        'verduras',
        'zanahoria',
        'tomate',
        'pan_integral',
        'legumbres',
        'atun',
        'sardina',
        'aceite_oliva',
        'queso_fresco',
    ];
    const nutritionAllergyCatalog = {
        huevo: {
            label: 'Huevo',
            excludes: ['huevo'],
        },
        lacteos: {
            label: 'Lácteos',
            excludes: ['leche', 'yogur', 'queso_fresco', 'queso_cottage'],
        },
        pescado: {
            label: 'Pescado/mariscos',
            excludes: ['pescado', 'atun', 'sardina'],
        },
        mani: {
            label: 'Maní',
            excludes: ['mani', 'mantequilla_mani'],
        },
        frutos_secos: {
            label: 'Frutos secos',
            excludes: ['frutos_secos', 'semilla_girasol'],
        },
        gluten: {
            label: 'Gluten',
            excludes: ['avena', 'pasta', 'pan_integral', 'tortilla_integral', 'arepa_integral'],
        },
        soya: {
            label: 'Soya',
            excludes: ['tofu'],
        },
    };
    const nutritionAllowedAllergyKeys = Object.keys(nutritionAllergyCatalog);
    const nutritionDefaultPreferences = {
        ingredients: [],
        allergies: [],
        time: 'normal',
        budget: 'medio',
        strict: false,
        allergy_ack: false,
    };
    const nutritionInitialItems = Array.isArray(nutritionInitialPayload && nutritionInitialPayload.items)
        ? nutritionInitialPayload.items
            .filter((item) => item && typeof item === 'object')
            .map((item) => ({
                slot: String(item.slot || 'Comida').trim(),
                dish: String(item.dish || 'Plato sugerido').trim(),
                portion: String(item.portion || 'Porción sugerida').trim(),
                kcal: Math.max(0, Math.round(Number(item.kcal || 0))),
                protein_g: Math.max(0, Math.round(Number(item.protein_g || 0))),
                carbs_g: Math.max(0, Math.round(Number(item.carbs_g || 0))),
                fat_g: Math.max(0, Math.round(Number(item.fat_g || 0))),
                image_url: String(item.image_url || nutritionImageMap.lunch),
                is_adapted: false,
                match_coverage: 0,
            }))
        : [];
    const nutritionTargetKcal = Math.max(1200, Math.round(Number(nutritionInitialPayload && nutritionInitialPayload.target_kcal ? nutritionInitialPayload.target_kcal : 0)) || 1900);
    let nutritionCurrentItems = nutritionInitialItems.slice();
    let nutritionIsCustomPlan = false;
    let nutritionCurrentPreferences = { ...nutritionDefaultPreferences };
    let nutritionCurrentMissingIngredients = [];
    let nutritionShoppingItems = [];
    let actionGuideAlreadySeen = false;
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

        // Primero cámara para aprovechar el gesto de usuario y mostrar popup nativo.
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
            // Error técnico de dispositivo/cámara, no bloqueó de permiso.
            cameraPermissionProbeState = 'granted';
        }
    }

    function setUserMenuOpen(isOpen) {
        if (!userMenuPanel) return;

        userMenuPanel.classList.toggle('hidden', !isOpen);
        userMenuPanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        userMenuToggle?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function setLeaderboardOpen(isOpen) {
        if (!leaderboardModal) return;

        leaderboardModal.classList.toggle('hidden', !isOpen);
        leaderboardModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        leaderboardToggleBtn?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            setUserMenuOpen(false);
        }
    }

    function renderLeaderboard(progressPayload) {
        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const leaderboard = payload.leaderboard && typeof payload.leaderboard === 'object'
            ? payload.leaderboard
            : {};
        const entries = Array.isArray(leaderboard.entries) ? leaderboard.entries : [];
        const currentClient = leaderboard.current_client && typeof leaderboard.current_client === 'object'
            ? leaderboard.current_client
            : null;
        const currentClientInTop = Boolean(leaderboard.current_client_in_top);

        if (leaderboardChipStatusEl) {
            const chipLabel = String(leaderboard.chip_label || '').trim();
            leaderboardChipStatusEl.textContent = chipLabel !== '' ? chipLabel : 'Compite este mes';
        }
        if (leaderboardWindowEl) {
            const windowLabel = String(leaderboard.window_label || '').trim();
            leaderboardWindowEl.textContent = windowLabel !== '' ? windowLabel : 'Mes actual';
        }
        if (leaderboardHelperEl) {
            const helperLabel = String(leaderboard.helper || '').trim();
            leaderboardHelperEl.textContent = helperLabel !== '' ? helperLabel : 'La asistencia pesa más y el tiempo solo suma como extra según tu objetivo personal durante el mes.';
        }
        if (leaderboardEmptyEl) {
            leaderboardEmptyEl.classList.toggle('hidden', entries.length > 0);
        }
        if (leaderboardListEl) {
            leaderboardListEl.textContent = '';

            entries.forEach((entry) => {
                const rank = Math.max(1, Math.round(Number(entry.rank || 0)));
                const rowEl = document.createElement('article');
                rowEl.className = 'leaderboard-row';
                if (Boolean(entry.is_current_client)) {
                    rowEl.classList.add('is-current');
                }

                const rankBadgeEl = document.createElement('span');
                rankBadgeEl.className = 'leaderboard-rank-badge';
                if (rank === 1) {
                    rankBadgeEl.classList.add('is-first');
                    rankBadgeEl.textContent = '1';
                } else if (rank === 2) {
                    rankBadgeEl.classList.add('is-second');
                    rankBadgeEl.textContent = '2';
                } else if (rank === 3) {
                    rankBadgeEl.classList.add('is-third');
                    rankBadgeEl.textContent = '3';
                } else {
                    rankBadgeEl.textContent = String(rank);
                }

                const copyEl = document.createElement('div');
                const nameEl = document.createElement('p');
                nameEl.className = 'leaderboard-row-name';
                nameEl.textContent = String(entry.name || 'Cliente');

                const metaEl = document.createElement('p');
                metaEl.className = 'leaderboard-row-meta';
                metaEl.textContent = [
                    String(entry.attendance_label || '0 asistencias'),
                    String(entry.time_label || '0 min'),
                    String(entry.goal_label || '0 metas completas'),
                ].join(' | ');

                copyEl.appendChild(nameEl);
                copyEl.appendChild(metaEl);

                const scoreEl = document.createElement('p');
                scoreEl.className = 'leaderboard-row-score';
                scoreEl.textContent = String(entry.score_label || '0.0 pts');

                rowEl.appendChild(rankBadgeEl);
                rowEl.appendChild(copyEl);
                rowEl.appendChild(scoreEl);
                leaderboardListEl.appendChild(rowEl);
            });
        }

        if (!leaderboardCurrentCardEl || !leaderboardCurrentTitleEl || !leaderboardCurrentMetaEl || !leaderboardCurrentHintEl) {
            return;
        }

        if (currentClient && !currentClientInTop) {
            leaderboardCurrentCardEl.classList.remove('hidden');
            leaderboardCurrentTitleEl.textContent = 'Vas #' + String(currentClient.rank || '-') + ' con ' + String(currentClient.score_label || '0.0 pts');
            leaderboardCurrentMetaEl.textContent = [
                String(currentClient.name || 'Tu perfil'),
                String(currentClient.attendance_label || '0 asistencias'),
                String(currentClient.time_label || '0 min'),
            ].join(' | ');
            leaderboardCurrentHintEl.textContent = 'Sigue sumando asistencias válidas este mes para meterte al Top 5.';
            return;
        }

        if (!currentClient) {
            leaderboardCurrentCardEl.classList.remove('hidden');
            leaderboardCurrentTitleEl.textContent = 'Todavía no sumas puntaje';
            leaderboardCurrentMetaEl.textContent = 'Cuando registres una sesión válida este mes aparecerás con tu posición actual.';
            leaderboardCurrentHintEl.textContent = 'La asistencia pesa más y el tiempo te da un empuje extra durante el mes.';
            return;
        }

        leaderboardCurrentCardEl.classList.add('hidden');
    }

    function escapeNutritionHtml(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function calculateNutritionTotals(items) {
        return (Array.isArray(items) ? items : []).reduce((totals, item) => {
            totals.kcal += Math.max(0, Math.round(Number(item.kcal || 0)));
            totals.protein += Math.max(0, Math.round(Number(item.protein_g || 0)));
            totals.carbs += Math.max(0, Math.round(Number(item.carbs_g || 0)));
            totals.fat += Math.max(0, Math.round(Number(item.fat_g || 0)));
            return totals;
        }, { kcal: 0, protein: 0, carbs: 0, fat: 0 });
    }

    function resolveNutritionImageBySlot(slot) {
        const normalizedSlot = String(slot || '').trim().toLowerCase();
        if (normalizedSlot === 'desayuno') return nutritionImageMap.breakfast;
        if (normalizedSlot === 'almuerzo') return nutritionImageMap.lunch;
        if (normalizedSlot === 'snack') return nutritionImageMap.snack;
        if (normalizedSlot === 'cena') return nutritionImageMap.dinner;
        return nutritionImageMap.lunch;
    }

    function resolveNutritionMacroRatios(goalLabel) {
        const normalizedGoal = String(goalLabel || '').trim().toLowerCase();
        if (normalizedGoal.includes('perder') || normalizedGoal.includes('definir')) {
            return { protein: 0.36, carbs: 0.34, fat: 0.30 };
        }
        if (normalizedGoal.includes('resistencia')) {
            return { protein: 0.25, carbs: 0.50, fat: 0.25 };
        }
        if (normalizedGoal.includes('fuerza') || normalizedGoal.includes('musculo') || normalizedGoal.includes('músculo')) {
            return { protein: 0.30, carbs: 0.45, fat: 0.25 };
        }
        return { protein: 0.30, carbs: 0.40, fat: 0.30 };
    }

    function normalizeNutritionPreferences(raw) {
        const source = raw && typeof raw === 'object' ? raw : {};
        const normalized = {
            ingredients: [],
            allergies: [],
            time: 'normal',
            budget: 'medio',
            strict: false,
            allergy_ack: false,
        };

        const candidateAllergies = Array.isArray(source.allergies) ? source.allergies : [];
        normalized.allergies = Array.from(new Set(candidateAllergies
            .map((item) => String(item || '').trim())
            .filter((item) => nutritionAllowedAllergyKeys.includes(item))));

        const allergyBlockedKeys = new Set();
        normalized.allergies.forEach((allergyKey) => {
            const allergyMeta = nutritionAllergyCatalog[allergyKey];
            if (!allergyMeta || !Array.isArray(allergyMeta.excludes)) return;
            allergyMeta.excludes.forEach((ingredientKey) => {
                allergyBlockedKeys.add(String(ingredientKey || '').trim());
            });
        });

        const candidateIngredients = Array.isArray(source.ingredients) ? source.ingredients : [];
        normalized.ingredients = Array.from(new Set(candidateIngredients
            .map((item) => String(item || '').trim())
            .filter((item) => nutritionAllowedIngredientKeys.includes(item))
            .filter((item) => !allergyBlockedKeys.has(item))));

        const candidateTime = String(source.time || '').trim().toLowerCase();
        if (['rapido', 'normal', 'relajado'].includes(candidateTime)) {
            normalized.time = candidateTime;
        }

        const candidateBudget = String(source.budget || '').trim().toLowerCase();
        if (['bajo', 'medio', 'alto'].includes(candidateBudget)) {
            normalized.budget = candidateBudget;
        }

        normalized.strict = Boolean(source.strict);
        normalized.allergy_ack = Boolean(source.allergy_ack);
        return normalized;
    }

    function resolveAllergyLabels(allergyKeys) {
        const keys = Array.isArray(allergyKeys) ? allergyKeys : [];
        return keys
            .map((key) => nutritionAllergyCatalog[String(key || '').trim()])
            .filter((meta) => meta && typeof meta === 'object')
            .map((meta) => String(meta.label || '').trim())
            .filter((label) => label !== '');
    }

    function resolveAllergyExcludedKeys(preferences) {
        const normalized = normalizeNutritionPreferences(preferences);
        const excluded = new Set();

        normalized.allergies.forEach((allergyKey) => {
            const allergyMeta = nutritionAllergyCatalog[allergyKey];
            if (!allergyMeta || !Array.isArray(allergyMeta.excludes)) return;
            allergyMeta.excludes.forEach((ingredientKey) => {
                const key = String(ingredientKey || '').trim();
                if (key !== '') {
                    excluded.add(key);
                }
            });
        });

        return excluded;
    }

    function updateNutritionAllergyAlert(preferences) {
        if (!nutritionAllergyAlertTextEl) return;
        const normalized = normalizeNutritionPreferences(preferences);
        const allergyLabels = resolveAllergyLabels(normalized.allergies);
        if (allergyLabels.length === 0) {
            nutritionAllergyAlertTextEl.textContent = 'Declara tus alergias en "Personalizar con lo que tengo". Esta guía es informativa y no reemplaza evaluación profesional.';
            return;
        }

        nutritionAllergyAlertTextEl.textContent = 'Alergias declaradas: '
            + allergyLabels.join(', ')
            + '. Esos ingredientes fueron excluidos automáticamente. Verifica etiquetas antes de consumir.';
    }

    function readNutritionPreferences() {
        try {
            const raw = window.localStorage.getItem(nutritionStorageKey);
            if (!raw) return null;
            const parsed = JSON.parse(raw);
            if (!parsed || typeof parsed !== 'object') return null;
            const normalized = normalizeNutritionPreferences(parsed);
            return {
                ...normalized,
                applied: Boolean(parsed.applied),
            };
        } catch (error) {
            return null;
        }
    }

    function writeNutritionPreferences(preferences, applied) {
        try {
            const payload = {
                ...normalizeNutritionPreferences(preferences),
                applied: Boolean(applied),
            };
            window.localStorage.setItem(nutritionStorageKey, JSON.stringify(payload));
        } catch (error) {
            // ignore persistence errors
        }
    }

    function clearNutritionPreferences() {
        try {
            window.localStorage.removeItem(nutritionStorageKey);
        } catch (error) {
            // ignore persistence errors
        }
    }

    function slugifyShoppingKey(raw) {
        return String(raw || '')
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function normalizeNutritionShoppingItems(items) {
        const rawItems = Array.isArray(items) ? items : [];
        return rawItems
            .filter((item) => item && typeof item === 'object')
            .map((item) => {
                const key = String(item.key || '').trim();
                const label = String(item.label || '').trim();
                if (key === '' || label === '') return null;
                return {
                    key,
                    label,
                    reason: String(item.reason || 'sugerido').trim(),
                    hint: String(item.hint || '').trim(),
                    checked: Boolean(item.checked),
                };
            })
            .filter((item) => item !== null);
    }

    function readNutritionShoppingItems() {
        try {
            const raw = window.localStorage.getItem(nutritionShoppingStorageKey);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            return normalizeNutritionShoppingItems(parsed);
        } catch (error) {
            return [];
        }
    }

    function writeNutritionShoppingItems(items) {
        try {
            window.localStorage.setItem(
                nutritionShoppingStorageKey,
                JSON.stringify(normalizeNutritionShoppingItems(items))
            );
        } catch (error) {
            // ignore persistence errors
        }
    }

    function resolveNutritionShoppingReasonLabel(reason) {
        const normalized = String(reason || '').trim().toLowerCase();
        if (normalized === 'faltante') return 'Prioridad';
        if (normalized === 'comprar') return 'Completar';
        if (normalized === 'manual') return 'Manual';
        return 'Sugerido';
    }

    function renderNutritionShoppingItems() {
        if (!nutritionShoppingListEl || !nutritionShoppingEmptyEl) return;
        nutritionShoppingListEl.textContent = '';

        nutritionShoppingItems.forEach((item) => {
            const row = document.createElement('li');
            row.className = 'nutrition-shopping-item' + (item.checked ? ' is-done' : '');
            row.dataset.shoppingKey = item.key;

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'nutrition-shopping-check';
            checkbox.checked = Boolean(item.checked);
            checkbox.setAttribute('aria-label', 'Marcar compra: ' + item.label);

            const copyWrap = document.createElement('div');
            copyWrap.className = 'nutrition-shopping-copy';

            const label = document.createElement('span');
            label.className = 'nutrition-shopping-label';
            label.textContent = item.label;
            copyWrap.appendChild(label);

            const hintText = String(item.hint || '').trim();
            if (hintText !== '') {
                const hint = document.createElement('span');
                hint.className = 'nutrition-shopping-hint';
                hint.textContent = 'Compra sugerida: ' + hintText;
                copyWrap.appendChild(hint);
            }

            const reason = document.createElement('span');
            reason.className = 'nutrition-shopping-reason';
            reason.textContent = resolveNutritionShoppingReasonLabel(item.reason);

            row.appendChild(checkbox);
            row.appendChild(copyWrap);
            row.appendChild(reason);
            nutritionShoppingListEl.appendChild(row);
        });

        nutritionShoppingEmptyEl.classList.toggle('hidden', nutritionShoppingItems.length > 0);

        if (nutritionShoppingCountEl) {
            const pending = nutritionShoppingItems.filter((item) => !item.checked).length;
            nutritionShoppingCountEl.textContent = String(pending) + ' productos';
        }
        if (nutritionShoppingPreviewEl) {
            const pending = nutritionShoppingItems.filter((item) => !item.checked).length;
            nutritionShoppingPreviewEl.textContent = pending > 0
                ? 'Tienes ' + String(pending) + ' productos pendientes. Ábrelos en modal.'
                : 'Tu lista está al día. Puedes agregar más productos cuando quieras.';
        }
    }

    function mergeNutritionShoppingSuggestions(suggestions) {
        const incoming = normalizeNutritionShoppingItems(suggestions);
        const mergedMap = new Map(
            normalizeNutritionShoppingItems(nutritionShoppingItems).map((item) => [item.key, item])
        );

        incoming.forEach((item) => {
            if (!mergedMap.has(item.key)) {
                mergedMap.set(item.key, item);
                return;
            }
            const prev = mergedMap.get(item.key);
            if (!prev) return;
            mergedMap.set(item.key, {
                ...prev,
                reason: prev.reason === 'manual' ? prev.reason : item.reason,
                hint: prev.hint !== '' ? prev.hint : item.hint,
            });
        });

        const reasonOrder = {
            faltante: 0,
            comprar: 1,
            sugerido: 2,
            manual: 3,
        };
        nutritionShoppingItems = Array.from(mergedMap.values()).sort((a, b) => {
            if (Boolean(a.checked) !== Boolean(b.checked)) {
                return a.checked ? 1 : -1;
            }
            const aReason = String(a.reason || '').trim().toLowerCase();
            const bReason = String(b.reason || '').trim().toLowerCase();
            const aOrder = Object.prototype.hasOwnProperty.call(reasonOrder, aReason) ? reasonOrder[aReason] : 99;
            const bOrder = Object.prototype.hasOwnProperty.call(reasonOrder, bReason) ? reasonOrder[bReason] : 99;
            if (aOrder !== bOrder) {
                return aOrder - bOrder;
            }
            return String(a.label || '').localeCompare(String(b.label || ''), 'es', { sensitivity: 'base' });
        });
        writeNutritionShoppingItems(nutritionShoppingItems);
        renderNutritionShoppingItems();
    }

    function buildDefaultNutritionShoppingSuggestions(selectedSet, excludedSet) {
        const safeSelectedSet = selectedSet instanceof Set ? selectedSet : new Set();
        const safeExcludedSet = excludedSet instanceof Set ? excludedSet : new Set();
        return nutritionDefaultShoppingIdeas
            .filter((key) => !safeSelectedSet.has(key))
            .filter((key) => !safeExcludedSet.has(key))
            .slice(0, 8)
            .map((key) => {
                const meta = nutritionIngredientCatalog[key];
                if (!meta) return null;
                return {
                    key,
                    label: meta.label,
                    reason: 'sugerido',
                    hint: String(meta.shopping || '').trim(),
                    checked: false,
                };
            })
            .filter((item) => item !== null);
    }

    function addManualNutritionShoppingItem(rawLabel) {
        const label = String(rawLabel || '').trim();
        if (label === '') return;
        const keySlug = slugifyShoppingKey(label);
        if (keySlug === '') return;
        const key = 'manual:' + keySlug;
        if (!nutritionShoppingItems.some((item) => item.key === key)) {
            nutritionShoppingItems.push({
                key,
                label,
                reason: 'manual',
                hint: '',
                checked: false,
            });
            writeNutritionShoppingItems(nutritionShoppingItems);
            renderNutritionShoppingItems();
        }
    }

    function setNutritionCustomizeModalOpen(isOpen) {
        if (!nutritionCustomizeModal) return;
        nutritionCustomizeModal.classList.toggle('hidden', !isOpen);
        nutritionCustomizeModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }

    function setNutritionShoppingModalOpen(isOpen) {
        if (!nutritionShoppingModal) return;
        nutritionShoppingModal.classList.toggle('hidden', !isOpen);
        nutritionShoppingModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }

    function syncNutritionIngredientButtons(preferences) {
        const selected = new Set(Array.isArray(preferences.ingredients) ? preferences.ingredients : []);
        const blocked = resolveAllergyExcludedKeys(preferences);
        nutritionCustomizeIngredientBtns.forEach((button) => {
            const ingredient = String(button.dataset.nutritionIngredient || '').trim();
            const isBlocked = blocked.has(ingredient);
            const isSelected = !isBlocked && selected.has(ingredient);
            button.classList.toggle('is-selected', isSelected);
            button.classList.toggle('is-disabled', isBlocked);
            button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            button.disabled = isBlocked;
            if (isBlocked) {
                button.setAttribute('aria-disabled', 'true');
                button.classList.remove('is-selected');
                button.setAttribute('aria-pressed', 'false');
            } else {
                button.removeAttribute('aria-disabled');
            }
        });
    }

    function syncNutritionAllergyButtons(preferences) {
        const selected = new Set(Array.isArray(preferences.allergies) ? preferences.allergies : []);
        nutritionCustomizeAllergyBtns.forEach((button) => {
            const allergyKey = String(button.dataset.nutritionAllergy || '').trim();
            const isSelected = selected.has(allergyKey);
            button.classList.toggle('is-selected', isSelected);
            button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        });
    }

    function syncNutritionModalControls(preferences) {
        const normalized = normalizeNutritionPreferences(preferences);
        syncNutritionIngredientButtons(normalized);
        syncNutritionAllergyButtons(normalized);
        if (nutritionCustomizeTimeEl instanceof HTMLSelectElement) {
            nutritionCustomizeTimeEl.value = normalized.time;
        }
        if (nutritionCustomizeBudgetEl instanceof HTMLSelectElement) {
            nutritionCustomizeBudgetEl.value = normalized.budget;
        }
        if (nutritionCustomizeStrictEl instanceof HTMLInputElement) {
            nutritionCustomizeStrictEl.checked = normalized.strict;
        }
        if (nutritionCustomizeAllergyAckEl instanceof HTMLInputElement) {
            nutritionCustomizeAllergyAckEl.checked = normalized.allergy_ack;
        }
    }

    function collectNutritionPreferencesFromModal() {
        const selectedIngredients = nutritionCustomizeIngredientBtns
            .filter((button) => button.classList.contains('is-selected'))
            .map((button) => String(button.dataset.nutritionIngredient || '').trim())
            .filter((item) => item !== '');
        const selectedAllergies = nutritionCustomizeAllergyBtns
            .filter((button) => button.classList.contains('is-selected'))
            .map((button) => String(button.dataset.nutritionAllergy || '').trim())
            .filter((item) => item !== '');

        return normalizeNutritionPreferences({
            ingredients: selectedIngredients,
            allergies: selectedAllergies,
            time: nutritionCustomizeTimeEl instanceof HTMLSelectElement ? nutritionCustomizeTimeEl.value : nutritionDefaultPreferences.time,
            budget: nutritionCustomizeBudgetEl instanceof HTMLSelectElement ? nutritionCustomizeBudgetEl.value : nutritionDefaultPreferences.budget,
            strict: nutritionCustomizeStrictEl instanceof HTMLInputElement ? nutritionCustomizeStrictEl.checked : false,
            allergy_ack: nutritionCustomizeAllergyAckEl instanceof HTMLInputElement ? nutritionCustomizeAllergyAckEl.checked : false,
        });
    }

    function renderNutritionCustomizePreview(preferences) {
        if (!nutritionCustomizePreviewListEl || !nutritionCustomizePreviewEmptyEl || !nutritionCustomizePreviewStatusEl) {
            return;
        }

        const normalized = normalizeNutritionPreferences(preferences);
        const generated = buildNutritionPlanFromPreferences(normalized);
        const items = Array.isArray(generated.items) ? generated.items : [];
        nutritionCustomizePreviewListEl.textContent = '';

        items.forEach((item) => {
            const row = document.createElement('li');
            row.className = 'nutrition-preview-item';

            const slot = document.createElement('span');
            slot.className = 'nutrition-preview-slot';
            slot.textContent = String(item.slot || 'Comida');

            const dish = document.createElement('span');
            dish.className = 'nutrition-preview-dish';
            dish.textContent = Boolean(item.is_blocked)
                ? 'Faltan ingredientes para esta comida'
                : String(item.dish || 'Plato sugerido');

            row.appendChild(slot);
            row.appendChild(dish);
            nutritionCustomizePreviewListEl.appendChild(row);
        });

        nutritionCustomizePreviewEmptyEl.classList.toggle('hidden', items.length > 0);
        const blockedCount = items.filter((item) => Boolean(item && item.is_blocked)).length;
        if (blockedCount > 0) {
            nutritionCustomizePreviewStatusEl.textContent = 'Faltan ' + String(blockedCount);
            nutritionCustomizePreviewStatusEl.classList.add('is-warn');
        } else {
            nutritionCustomizePreviewStatusEl.textContent = items.length > 0 ? 'Listo' : 'Sin aplicar';
            nutritionCustomizePreviewStatusEl.classList.remove('is-warn');
        }
    }

    function ingredientMatchesCategory(ingredientKey, category) {
        const meta = nutritionIngredientCatalog[ingredientKey];
        if (!meta) return false;
        const ingredientCategory = String(meta.category || '').trim();
        if (category === 'fruit_or_carb') {
            return ingredientCategory === 'fruit' || ingredientCategory === 'carb';
        }
        return ingredientCategory === category;
    }

    function resolveCategoryLabel(category) {
        if (category === 'protein') return 'proteína';
        if (category === 'carb') return 'carbohidrato';
        if (category === 'veggie') return 'vegetales';
        if (category === 'fat') return 'grasa saludable';
        if (category === 'fruit_or_carb') return 'fruta o carbohidrato';
        return category;
    }

    function scoreIngredientForSlot(ingredientKey, slot, preferences, selectedSet) {
        const meta = nutritionIngredientCatalog[ingredientKey];
        if (!meta) return -9999;
        const goalLabel = String(nutritionInitialPayload && nutritionInitialPayload.goal_label ? nutritionInitialPayload.goal_label : '').toLowerCase();

        let score = selectedSet.has(ingredientKey) ? 24 : 0;
        if (preferences.budget === String(meta.budget || 'medio')) {
            score += 8;
        } else if (preferences.budget === 'bajo' && String(meta.budget || '') === 'alto') {
            score -= 8;
        }

        if (preferences.time === String(meta.prep || 'normal')) {
            score += 8;
        } else if (preferences.time === 'rapido' && String(meta.prep || '') === 'relajado') {
            score -= 7;
        }

        const preferredSlots = Array.isArray(meta.slots) ? meta.slots : [];
        if (preferredSlots.length > 0) {
            score += preferredSlots.includes(slot) ? 10 : -6;
        }

        if (slot === 'Desayuno' && ['atun', 'pescado', 'carne', 'pavo', 'sardina'].includes(ingredientKey)) {
            score -= 8;
        }
        if (slot === 'Snack' && ['pollo', 'carne', 'pavo', 'pescado', 'atun', 'sardina'].includes(ingredientKey)) {
            score -= 8;
        }

        score += Number(meta.protein || 0) * 0.22;
        score += Number(meta.carbs || 0) * (slot === 'Almuerzo' ? 0.06 : 0.03);
        score += Number(meta.fat || 0) * 0.03;

        if (goalLabel.includes('perder') || goalLabel.includes('definir')) {
            score += Number(meta.protein || 0) * 0.14;
            score -= Number(meta.kcal || 0) * 0.03;
        } else if (goalLabel.includes('musculo') || goalLabel.includes('músculo') || goalLabel.includes('fuerza')) {
            score += Number(meta.protein || 0) * 0.16;
            score += Number(meta.kcal || 0) * 0.01;
        }

        return score;
    }

    function pickBestIngredientForCategory(category, slot, preferences, selectedSet, strictMode, excludedSet) {
        const blocked = excludedSet instanceof Set ? excludedSet : new Set();
        const candidates = Object.keys(nutritionIngredientCatalog)
            .filter((key) => ingredientMatchesCategory(key, category))
            .filter((key) => {
                const meta = nutritionIngredientCatalog[key];
                if (!meta) return false;
                const allowedSlots = Array.isArray(meta.slots) ? meta.slots : [];
                return allowedSlots.length === 0 || allowedSlots.includes(slot);
            })
            .filter((key) => !blocked.has(key))
            .filter((key) => !strictMode || selectedSet.has(key));

        if (!candidates.length) {
            return null;
        }

        let bestKey = null;
        let bestScore = -Infinity;
        candidates.forEach((key) => {
            const score = scoreIngredientForSlot(key, slot, preferences, selectedSet);
            if (score > bestScore) {
                bestScore = score;
                bestKey = key;
            }
        });

        return bestKey;
    }

    function composeDishName(slot, ingredientKeys) {
        const labels = ingredientKeys
            .map((key) => nutritionIngredientCatalog[key]?.label || '')
            .filter((label) => label !== '');
        if (!labels.length) {
            return 'Plato sugerido';
        }
        if (labels.length === 1) {
            return labels[0];
        }
        const hasIngredient = (key) => ingredientKeys.includes(key);

        if (slot === 'Desayuno') {
            if (hasIngredient('avena')) {
                const complement = labels.filter((label) => label !== 'Avena').slice(0, 2).join(' y ');
                return complement !== '' ? 'Avena con ' + complement : 'Avena proteica';
            }
            if (hasIngredient('huevo')) {
                const complement = labels.filter((label) => label !== 'Huevo').slice(0, 2).join(' y ');
                return complement !== '' ? 'Huevos con ' + complement : 'Huevos al gusto';
            }
            return 'Desayuno de ' + labels.slice(0, 2).join(' + ');
        }

        if (slot === 'Almuerzo') {
            return 'Bowl de ' + labels.slice(0, 3).join(' con ');
        }

        if (slot === 'Snack') {
            return 'Snack de ' + labels.slice(0, 2).join(' + ');
        }

        if (slot === 'Cena') {
            return 'Cena ligera de ' + labels.slice(0, 3).join(' con ');
        }

        return labels[0] + ' con ' + labels.slice(1).join(' + ');
    }

    function composePortionLine(ingredientKeys) {
        const pieces = ingredientKeys
            .map((key) => nutritionIngredientCatalog[key])
            .filter((meta) => meta && typeof meta === 'object')
            .map((meta) => String(meta.portion || '').trim() + ' ' + String(meta.label || '').trim())
            .filter((text) => text.trim() !== '');
        if (!pieces.length) {
            return 'Porción sugerida';
        }
        return pieces.join(' + ');
    }

    function buildNutritionIngredientGroups(ingredientKeys) {
        return (Array.isArray(ingredientKeys) ? ingredientKeys : []).reduce((groups, key) => {
            const meta = nutritionIngredientCatalog[key];
            if (!meta) return groups;

            const category = String(meta.category || '').trim();
            if (Object.prototype.hasOwnProperty.call(groups, category)) {
                groups[category].push(key);
            } else {
                groups.other.push(key);
            }

            return groups;
        }, {
            protein: [],
            carb: [],
            fruit: [],
            veggie: [],
            fat: [],
            other: [],
        });
    }

    function pickFirstNutritionIngredient(group) {
        return Array.isArray(group) && group.length > 0 ? group[0] : null;
    }

    function joinNutritionLabels(labels) {
        const safeLabels = (Array.isArray(labels) ? labels : [])
            .map((label) => String(label || '').trim())
            .filter((label) => label !== '');

        if (!safeLabels.length) return '';
        if (safeLabels.length === 1) return safeLabels[0];
        if (safeLabels.length === 2) return safeLabels[0] + ' y ' + safeLabels[1];

        return safeLabels.slice(0, -1).join(', ') + ' y ' + safeLabels[safeLabels.length - 1];
    }

    function appendNutritionSide(base, side) {
        return side !== '' ? base + ', ' + side : base;
    }

    function resolveDishIngredientLabel(key) {
        const customLabels = {
            carne: 'carne magra',
            pasta: 'pasta integral',
            pan_integral: 'pan integral',
            tortilla_integral: 'tortilla integral',
            arepa_integral: 'arepa integral',
            frutos_secos: 'frutos secos',
            mantequilla_mani: 'mantequilla de mani',
            semilla_girasol: 'semillas de girasol',
        };

        if (Object.prototype.hasOwnProperty.call(customLabels, key)) {
            return customLabels[key];
        }

        const meta = nutritionIngredientCatalog[key];
        return meta ? String(meta.label || '').trim().toLowerCase() : '';
    }

    function resolvePreparedIngredientLabel(key) {
        const preparedLabels = {
            pollo: 'pollo a la plancha',
            atun: 'atun escurrido',
            huevo: 'huevo revuelto',
            pavo: 'pavo a la plancha',
            carne: 'carne magra a la plancha',
            pescado: 'pescado a la plancha',
            yogur: 'yogur natural',
            tofu: 'tofu salteado',
            queso_fresco: 'queso fresco',
            queso_cottage: 'queso cottage',
            leche: 'leche',
            sardina: 'sardina',
            avena: 'avena cocida',
            arroz: 'arroz cocido',
            quinoa: 'quinoa cocida',
            papa: 'papa cocida',
            camote: 'camote al horno',
            pasta: 'pasta integral cocida',
            pan_integral: 'pan integral tostado',
            fruta: 'fruta fresca',
            legumbres: 'legumbres cocidas',
            platano: 'platano',
            yuca: 'yuca cocida',
            tortilla_integral: 'tortilla integral caliente',
            maiz: 'maiz cocido',
            arepa_integral: 'arepa integral',
            verduras: 'verduras salteadas',
            ensalada: 'ensalada fresca',
            tomate: 'tomate en rodajas',
            cebolla: 'cebolla salteada',
            zanahoria: 'zanahoria cocida',
            pepino: 'pepino en rodajas',
            espinaca: 'espinaca salteada',
            aguacate: 'aguacate',
            frutos_secos: 'frutos secos',
            mani: 'mani',
            aceite_oliva: 'aceite de oliva',
            mantequilla_mani: 'mantequilla de mani',
            chia: 'chia',
            semilla_girasol: 'semillas de girasol',
        };

        if (Object.prototype.hasOwnProperty.call(preparedLabels, key)) {
            return preparedLabels[key];
        }

        return resolveDishIngredientLabel(key);
    }

    function pickNutritionVariant(options, slot, ingredientKeys) {
        const variants = (Array.isArray(options) ? options : [])
            .map((option) => String(option || '').trim())
            .filter((option) => option !== '');

        if (!variants.length) return '';

        const seed = [slot]
            .concat((Array.isArray(ingredientKeys) ? ingredientKeys.slice() : []).sort())
            .join('|');
        let hash = 0;

        for (let index = 0; index < seed.length; index += 1) {
            hash = ((hash * 31) + seed.charCodeAt(index)) >>> 0;
        }

        return variants[hash % variants.length];
    }

    function composeBreakfastDish(ingredientKeys) {
        const groups = buildNutritionIngredientGroups(ingredientKeys);
        const protein = pickFirstNutritionIngredient(groups.protein);
        const carb = pickFirstNutritionIngredient(groups.carb);
        const fruit = pickFirstNutritionIngredient(groups.fruit);
        const veggie = pickFirstNutritionIngredient(groups.veggie);
        const fat = pickFirstNutritionIngredient(groups.fat);

        if (carb === 'avena' && protein === 'yogur') {
            const extras = joinNutritionLabels([fruit, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            const suffix = extras !== '' ? ', ' + extras : '';
            return pickNutritionVariant([
                'Parfait de yogur con avena' + suffix,
                'Bowl de yogur con avena' + suffix,
                'Avena cremosa con yogur' + suffix,
            ], 'Desayuno', ingredientKeys);
        }

        if (carb === 'avena') {
            const extras = joinNutritionLabels([protein, fruit, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            const suffix = extras !== '' ? ' con ' + extras : '';
            return pickNutritionVariant([
                'Avena proteica' + suffix,
                'Avena cocida' + suffix,
                'Bowl de avena' + suffix,
            ], 'Desayuno', ingredientKeys);
        }

        if (carb === 'pan_integral') {
            const filling = joinNutritionLabels([protein, veggie, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            return pickNutritionVariant([
                'Tostadas integrales con ' + filling,
                'Pan integral tostado con ' + filling,
                'Sandwich abierto de ' + filling,
            ], 'Desayuno', ingredientKeys);
        }

        if (carb === 'arepa_integral') {
            const filling = joinNutritionLabels([protein, veggie, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            return pickNutritionVariant([
                'Arepa integral rellena de ' + filling,
                'Arepa integral con ' + filling,
                'Arepa tostada con ' + filling,
            ], 'Desayuno', ingredientKeys);
        }

        if (carb === 'tortilla_integral') {
            const filling = joinNutritionLabels([protein, veggie, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            return pickNutritionVariant([
                'Wrap integral de ' + filling,
                'Tortilla integral rellena de ' + filling,
                'Roll integral con ' + filling,
            ], 'Desayuno', ingredientKeys);
        }

        const pieces = [protein, carb, fruit, fat, veggie]
            .filter((key) => key !== null)
            .map(resolveDishIngredientLabel);
        return pieces.length > 0 ? 'Desayuno de ' + joinNutritionLabels(pieces.slice(0, 3)) : 'Plato sugerido';
    }

    function composeSnackDish(ingredientKeys) {
        const groups = buildNutritionIngredientGroups(ingredientKeys);
        const protein = pickFirstNutritionIngredient(groups.protein);
        const carb = pickFirstNutritionIngredient(groups.carb);
        const fruit = pickFirstNutritionIngredient(groups.fruit);
        const veggie = pickFirstNutritionIngredient(groups.veggie);
        const fat = pickFirstNutritionIngredient(groups.fat);

        if (protein === 'yogur' || protein === 'queso_cottage' || protein === 'queso_fresco') {
            const base = fruit !== null ? fruit : carb;
            const extras = joinNutritionLabels([base, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            const suffix = extras !== '' ? ' con ' + extras : '';
            return pickNutritionVariant([
                'Parfait rapido de ' + resolveDishIngredientLabel(protein) + suffix,
                'Vaso de ' + resolveDishIngredientLabel(protein) + suffix,
                'Bowl ligero de ' + resolveDishIngredientLabel(protein) + suffix,
            ], 'Snack', ingredientKeys);
        }

        if (carb === 'pan_integral') {
            const filling = joinNutritionLabels([protein, veggie, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            return pickNutritionVariant([
                'Mini sandwich integral de ' + filling,
                'Tostada integral con ' + filling,
                'Pan integral con ' + filling,
            ], 'Snack', ingredientKeys);
        }

        if (carb === 'arepa_integral') {
            const filling = joinNutritionLabels([protein, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            return pickNutritionVariant([
                'Arepa integral con ' + filling,
                'Arepa ligera de ' + filling,
                'Media arepa con ' + filling,
            ], 'Snack', ingredientKeys);
        }

        if (carb === 'tortilla_integral') {
            const filling = joinNutritionLabels([protein, veggie, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            return pickNutritionVariant([
                'Wrap rapido de ' + filling,
                'Roll integral de ' + filling,
                'Tortilla integral con ' + filling,
            ], 'Snack', ingredientKeys);
        }

        if (carb === 'avena') {
            const extras = joinNutritionLabels([protein, fruit, fat].filter((key) => key !== null).map(resolveDishIngredientLabel));
            const suffix = extras !== '' ? ' con ' + extras : '';
            return pickNutritionVariant([
                'Bowl de avena' + suffix,
                'Avena suave' + suffix,
                'Avena rapida' + suffix,
            ], 'Snack', ingredientKeys);
        }

        const pieces = [protein, fruit, carb, fat]
            .filter((key) => key !== null)
            .map(resolveDishIngredientLabel);
        return pieces.length > 0 ? 'Snack de ' + joinNutritionLabels(pieces.slice(0, 3)) : 'Plato sugerido';
    }

    function composeSavoryDish(slot, ingredientKeys) {
        const groups = buildNutritionIngredientGroups(ingredientKeys);
        const protein = pickFirstNutritionIngredient(groups.protein);
        const carb = pickFirstNutritionIngredient(groups.carb);
        const veggie = pickFirstNutritionIngredient(groups.veggie);
        const fat = pickFirstNutritionIngredient(groups.fat);
        const proteinLabel = resolveDishIngredientLabel(protein);
        const veggieLabel = resolveDishIngredientLabel(veggie);
        const fatLabel = resolveDishIngredientLabel(fat);

        if (carb === 'tortilla_integral') {
            const filling = joinNutritionLabels([proteinLabel, veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                'Wrap integral de ' + filling,
                'Tortilla integral rellena de ' + filling,
                'Roll salado de ' + filling,
            ], slot, ingredientKeys);
        }

        if (carb === 'arroz') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                appendNutritionSide('Arroz con ' + proteinLabel, side),
                appendNutritionSide(proteinLabel + ' con arroz', side),
                appendNutritionSide('Salteado de ' + proteinLabel + ' con arroz', side),
            ], slot, ingredientKeys);
        }

        if (carb === 'quinoa') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                appendNutritionSide('Bowl de quinoa con ' + proteinLabel, side),
                appendNutritionSide('Quinoa con ' + proteinLabel, side),
                appendNutritionSide(proteinLabel + ' con quinoa', side),
            ], slot, ingredientKeys);
        }

        if (carb === 'pasta') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                appendNutritionSide('Pasta integral con ' + proteinLabel, side),
                appendNutritionSide(proteinLabel + ' con pasta integral', side),
                appendNutritionSide('Bowl de pasta integral con ' + proteinLabel, side),
            ], slot, ingredientKeys);
        }

        if (carb === 'papa') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            if (protein === 'atun' || protein === 'sardina') {
                return pickNutritionVariant([
                    appendNutritionSide('Ensalada de ' + proteinLabel + ' con papa cocida', side),
                    appendNutritionSide(proteinLabel + ' con papa cocida', side),
                    appendNutritionSide('Bowl de ' + proteinLabel + ' con papa cocida', side),
                ], slot, ingredientKeys);
            }

            return pickNutritionVariant([
                appendNutritionSide(proteinLabel + ' con papa cocida', side),
                appendNutritionSide(proteinLabel + ' con papas cocidas', side),
                appendNutritionSide('Plato de ' + proteinLabel + ' con papa cocida', side),
            ], slot, ingredientKeys);
        }

        if (carb === 'camote') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                appendNutritionSide(proteinLabel + ' con camote al horno', side),
                appendNutritionSide('Plato de ' + proteinLabel + ' con camote asado', side),
                proteinLabel + ' con camote y ' + (side !== '' ? side : 'vegetales'),
            ], slot, ingredientKeys);
        }

        if (carb === 'legumbres') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                appendNutritionSide('Guiso de ' + proteinLabel + ' con legumbres', side),
                appendNutritionSide(proteinLabel + ' con legumbres', side),
                appendNutritionSide('Bowl de legumbres con ' + proteinLabel, side),
            ], slot, ingredientKeys);
        }

        if (carb === 'yuca') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                appendNutritionSide(proteinLabel + ' con yuca cocida', side),
                appendNutritionSide('Plato de ' + proteinLabel + ' con yuca cocida', side),
                proteinLabel + ' con yuca y ' + (side !== '' ? side : 'vegetales'),
            ], slot, ingredientKeys);
        }

        if (carb === 'maiz') {
            const side = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                appendNutritionSide(proteinLabel + ' con maiz', side),
                appendNutritionSide('Salteado de ' + proteinLabel + ' con maiz', side),
                appendNutritionSide('Bowl de ' + proteinLabel + ' con maiz', side),
            ], slot, ingredientKeys);
        }

        if ((protein === 'atun' || protein === 'sardina') && veggieLabel !== '') {
            const extras = joinNutritionLabels([veggieLabel, fatLabel].filter((label) => label !== ''));
            return pickNutritionVariant([
                'Ensalada de ' + proteinLabel + (extras !== '' ? ' con ' + extras : ''),
                proteinLabel + ' con ' + extras,
                'Bowl fresco de ' + proteinLabel + (extras !== '' ? ' con ' + extras : ''),
            ], slot, ingredientKeys);
        }

        const pieces = [proteinLabel, veggieLabel, fatLabel]
            .filter((label) => label !== '');
        if (!pieces.length) {
            return 'Plato sugerido';
        }

        return pickNutritionVariant([
            pieces[0] + (pieces.length > 1 ? ' con ' + joinNutritionLabels(pieces.slice(1)) : ''),
            'Plato de ' + joinNutritionLabels(pieces),
            'Bowl salado de ' + joinNutritionLabels(pieces),
        ], slot, ingredientKeys);
    }

    function composeDishName(slot, ingredientKeys) {
        if (slot === 'Desayuno') {
            return composeBreakfastDish(ingredientKeys);
        }
        if (slot === 'Snack') {
            return composeSnackDish(ingredientKeys);
        }
        if (slot === 'Almuerzo' || slot === 'Cena') {
            return composeSavoryDish(slot, ingredientKeys);
        }

        const labels = (Array.isArray(ingredientKeys) ? ingredientKeys : [])
            .map(resolveDishIngredientLabel)
            .filter((label) => label !== '');
        return labels.length > 0 ? joinNutritionLabels(labels.slice(0, 3)) : 'Plato sugerido';
    }

    function composePortionLine(ingredientKeys) {
        const pieces = (Array.isArray(ingredientKeys) ? ingredientKeys : [])
            .map((key) => {
                const meta = nutritionIngredientCatalog[key];
                if (!meta) return '';

                const portion = String(meta.portion || '').trim();
                const preparedLabel = resolvePreparedIngredientLabel(key);
                if (portion === '' && preparedLabel === '') return '';
                if (portion === '') return preparedLabel;
                if (preparedLabel === '') return portion;
                return portion + ' de ' + preparedLabel;
            })
            .filter((text) => text !== '');

        if (!pieces.length) {
            return 'PorciÃ³n sugerida';
        }

        return pieces.join(' + ');
    }

    function composePortionLine(ingredientKeys) {
        const pieces = (Array.isArray(ingredientKeys) ? ingredientKeys : [])
            .map((key) => {
                const meta = nutritionIngredientCatalog[key];
                if (!meta) return '';

                const portion = String(meta.portion || '').trim();
                const preparedLabel = resolvePreparedIngredientLabel(key);
                if (portion === '' && preparedLabel === '') return '';
                if (portion === '') return preparedLabel;
                if (preparedLabel === '') return portion;
                return portion + ' de ' + preparedLabel;
            })
            .filter((text) => text !== '');

        if (!pieces.length) {
            return 'Porcion sugerida';
        }

        return pieces.join(' + ');
    }

    function suggestIngredientByCategory(category, slot, preferences, selectedSet, excludedSet) {
        const candidate = pickBestIngredientForCategory(category, slot, preferences, selectedSet, false, excludedSet);
        if (!candidate) return null;
        return candidate;
    }

    function buildNutritionMealFromBlueprint(slot, preferences, selectedSet, excludedSet) {
        const blueprint = nutritionSlotBlueprints[slot] || { required: [], optional: [] };
        const strictMode = Boolean(preferences.strict);
        const chosenKeys = [];
        const missingCategories = [];
        const suggestedKeys = [];

        blueprint.required.forEach((category) => {
            const found = pickBestIngredientForCategory(category, slot, preferences, selectedSet, strictMode, excludedSet);
            if (found) {
                chosenKeys.push(found);
                return;
            }

            missingCategories.push(category);
            if (!strictMode) {
                const suggested = suggestIngredientByCategory(category, slot, preferences, selectedSet, excludedSet);
                if (suggested) {
                    chosenKeys.push(suggested);
                    suggestedKeys.push(suggested);
                }
            }
        });

        blueprint.optional.forEach((category) => {
            const optionalFound = pickBestIngredientForCategory(category, slot, preferences, selectedSet, strictMode, excludedSet);
            if (!optionalFound) return;
            if (!chosenKeys.includes(optionalFound)) {
                chosenKeys.push(optionalFound);
            }
        });

        if (!chosenKeys.length || (strictMode && missingCategories.length > 0)) {
            return {
                slot,
                blocked: true,
                missingCategories,
                suggestedKeys,
            };
        }

        const nutritionTotals = chosenKeys.reduce((acc, key) => {
            const meta = nutritionIngredientCatalog[key];
            if (!meta) return acc;
            acc.kcal += Number(meta.kcal || 0);
            acc.protein += Number(meta.protein || 0);
            acc.carbs += Number(meta.carbs || 0);
            acc.fat += Number(meta.fat || 0);
            return acc;
        }, { kcal: 0, protein: 0, carbs: 0, fat: 0 });

        const selectedCount = chosenKeys.filter((key) => selectedSet.has(key)).length;
        const coverage = Math.round((selectedCount / Math.max(1, chosenKeys.length)) * 100);

        return {
            slot,
            blocked: false,
            chosenKeys,
            suggestedKeys,
            dish: composeDishName(slot, chosenKeys),
            portion: composePortionLine(chosenKeys),
            kcal: Math.max(0, Math.round(nutritionTotals.kcal)),
            protein_g: Math.max(0, Math.round(nutritionTotals.protein)),
            carbs_g: Math.max(0, Math.round(nutritionTotals.carbs)),
            fat_g: Math.max(0, Math.round(nutritionTotals.fat)),
            image_url: resolveNutritionImageBySlot(slot),
            match_coverage: coverage,
        };
    }

    function buildNutritionPlanFromPreferences(preferences) {
        const normalized = normalizeNutritionPreferences(preferences);
        const allergyExcludedSet = resolveAllergyExcludedKeys(normalized);
        const selectedSet = new Set(
            normalized.ingredients.filter((key) => !allergyExcludedSet.has(key))
        );
        const generatedItems = [];
        const missingIngredients = [];
        let strictMissedSlots = 0;
        let accumulatedTarget = 0;

        nutritionSlotOrder.forEach((slot, index) => {
            const slotShare = Number(nutritionSlotShares[slot] || 0.25);
            let targetSlotKcal = Math.max(120, Math.round(nutritionTargetKcal * slotShare));
            if (index === nutritionSlotOrder.length - 1) {
                targetSlotKcal = Math.max(120, nutritionTargetKcal - accumulatedTarget);
            }
            accumulatedTarget += targetSlotKcal;

            const meal = buildNutritionMealFromBlueprint(slot, normalized, selectedSet, allergyExcludedSet);
            if (meal.blocked) {
                strictMissedSlots += 1;
                const categorySuggestions = meal.missingCategories
                    .map((category) => suggestIngredientByCategory(category, slot, normalized, selectedSet, allergyExcludedSet))
                    .filter((value) => value !== null);

                categorySuggestions.forEach((key) => {
                    const meta = nutritionIngredientCatalog[key];
                    if (!meta) return;
                    missingIngredients.push({
                        key,
                        label: meta.label,
                        reason: 'faltante',
                        hint: String(meta.shopping || '').trim(),
                    });
                });

                generatedItems.push({
                    slot,
                    dish: 'No alcanza para ' + slot.toLowerCase(),
                    portion: 'Falta: ' + meal.missingCategories.map(resolveCategoryLabel).join(', '),
                    kcal: 0,
                    protein_g: 0,
                    carbs_g: 0,
                    fat_g: 0,
                    image_url: resolveNutritionImageBySlot(slot),
                    is_adapted: true,
                    match_coverage: 0,
                    is_blocked: true,
                });
                return;
            }

            let adjustedKcal = targetSlotKcal;
            if (meal.kcal <= 0) {
                adjustedKcal = 0;
            }
            const factor = meal.kcal > 0 ? (adjustedKcal / meal.kcal) : 0;
            const protein = Math.max(0, Math.round(meal.protein_g * factor));
            const carbs = Math.max(0, Math.round(meal.carbs_g * factor));
            const fat = Math.max(0, Math.round(meal.fat_g * factor));

            meal.suggestedKeys.forEach((key) => {
                const meta = nutritionIngredientCatalog[key];
                if (!meta) return;
                missingIngredients.push({
                    key,
                    label: meta.label,
                    reason: 'comprar',
                    hint: String(meta.shopping || '').trim(),
                });
            });

            generatedItems.push({
                slot,
                dish: meal.dish,
                portion: meal.portion,
                kcal: adjustedKcal,
                protein_g: protein,
                carbs_g: carbs,
                fat_g: fat,
                image_url: meal.image_url,
                is_adapted: true,
                match_coverage: meal.match_coverage,
                is_blocked: false,
            });
        });

        const dedupMissing = Array.from(new Map(
            missingIngredients.map((item) => [String(item.key || '').trim(), item])
        ).values()).filter((item) => item.key !== '');

        const totals = calculateNutritionTotals(generatedItems);
        return {
            items: generatedItems,
            totals,
            selectedCount: selectedSet.size,
            strictMissedSlots,
            strict: normalized.strict,
            allergies: normalized.allergies.slice(),
            missingIngredients: dedupMissing,
        };
    }

    function renderNutritionTotals(items) {
        const totals = calculateNutritionTotals(items);
        if (nutritionTotalKcalEl) {
            nutritionTotalKcalEl.textContent = String(totals.kcal) + ' kcal';
        }
        if (nutritionTotalProteinEl) {
            nutritionTotalProteinEl.textContent = String(totals.protein) + ' g';
        }
        if (nutritionTotalCarbsEl) {
            nutritionTotalCarbsEl.textContent = String(totals.carbs);
        }
        if (nutritionTotalFatEl) {
            nutritionTotalFatEl.textContent = String(totals.fat);
        }
    }

    function renderNutritionItems(items) {
        if (!nutritionDayListEl) return;
        const list = Array.isArray(items) ? items : [];
        nutritionDayListEl.innerHTML = list.map((item) => {
            const dish = escapeNutritionHtml(item.dish || 'Plato sugerido');
            const portion = escapeNutritionHtml(item.portion || 'Porción sugerida');
            const slot = escapeNutritionHtml(item.slot || 'Comida');
            const image = escapeNutritionHtml(item.image_url || resolveNutritionImageBySlot(item.slot));
            const kcal = Math.max(0, Math.round(Number(item.kcal || 0)));
            const protein = Math.max(0, Math.round(Number(item.protein_g || 0)));
            const carbs = Math.max(0, Math.round(Number(item.carbs_g || 0)));
            const fat = Math.max(0, Math.round(Number(item.fat_g || 0)));
            const showBadge = Boolean(item.is_adapted);
            const isBlocked = Boolean(item.is_blocked);
            const coverage = Math.max(0, Math.min(100, Math.round(Number(item.match_coverage || 0))));

            return ''
                + '<article class="nutrition-day-item">'
                + '<img src="' + image + '" alt="Plato recomendado" class="nutrition-day-image" loading="lazy" decoding="async">'
                + '<div>'
                + '<div class="nutrition-day-head">'
                + '<p class="nutrition-day-slot">' + slot + '</p>'
                + '<p class="nutrition-day-kcal">' + String(kcal) + ' kcal</p>'
                + '</div>'
                + '<p class="nutrition-day-dish">' + dish + '</p>'
                + '<p class="nutrition-day-portion">' + portion + '</p>'
                + '<p class="nutrition-day-macros">P ' + String(protein) + 'g | C ' + String(carbs) + 'g | G ' + String(fat) + 'g</p>'
                + (isBlocked
                    ? '<span class="nutrition-adapt-badge">Faltan ingredientes</span>'
                    : (showBadge ? '<span class="nutrition-adapt-badge">Adaptado ' + String(coverage) + '%</span>' : '')
                )
                + '</div>'
                + '</article>';
        }).join('');
    }

    function setNutritionCustomStatus(isCustom) {
        if (!nutritionCustomizeStatusEl) return;
        nutritionCustomizeStatusEl.textContent = isCustom ? 'Plan personalizado' : 'Plan base';
        nutritionCustomizeStatusEl.classList.toggle('is-custom', isCustom);
        nutritionCustomizeResetBtn?.classList.toggle('hidden', !isCustom);
        nutritionIsCustomPlan = isCustom;
    }

    function applyNutritionPlan(items, options) {
        nutritionCurrentItems = Array.isArray(items) ? items.slice() : [];
        renderNutritionItems(nutritionCurrentItems);
        renderNutritionTotals(nutritionCurrentItems);
        setNutritionCustomStatus(Boolean(options && options.custom));
        updateNutritionAllergyAlert(nutritionCurrentPreferences);
        nutritionCurrentMissingIngredients = Array.isArray(options && options.missingIngredients)
            ? options.missingIngredients.slice()
            : [];

        const note = options && typeof options.note === 'string'
            ? options.note.trim()
            : '';
        if (nutritionCustomizeNoteEl) {
            nutritionCustomizeNoteEl.textContent = note !== ''
                ? note
                : 'Consejo: marca mínimo 1 proteína y 1 carbohidrato para mejores resultados.';
        }

        if (Boolean(options && options.refreshShopping)) {
            const selectedSet = new Set(Array.isArray(nutritionCurrentPreferences.ingredients) ? nutritionCurrentPreferences.ingredients : []);
            const excludedSet = resolveAllergyExcludedKeys(nutritionCurrentPreferences);
            const missingSuggestions = nutritionCurrentMissingIngredients
                .filter((item) => item && typeof item === 'object')
                .map((item) => {
                    const key = String(item.key || '').trim();
                    const meta = key !== '' ? nutritionIngredientCatalog[key] : null;
                    return {
                        key,
                        label: String(item.label || '').trim(),
                        reason: String(item.reason || 'faltante').trim(),
                        hint: String(item.hint || (meta && meta.shopping ? meta.shopping : '')).trim(),
                        checked: false,
                    };
                })
                .filter((item) => item.key !== '' && item.label !== '')
                .filter((item) => !excludedSet.has(item.key));

            if (missingSuggestions.length > 0) {
                mergeNutritionShoppingSuggestions(missingSuggestions);
            } else if (nutritionShoppingItems.length === 0) {
                mergeNutritionShoppingSuggestions(buildDefaultNutritionShoppingSuggestions(selectedSet, excludedSet));
            } else {
                renderNutritionShoppingItems();
            }
        }
    }

    function initializeNutritionCustomization() {
        const hasNutritionUi = currentScreen === 'nutrition'
            && Boolean(nutritionCustomizeOpenBtn)
            && Boolean(nutritionDayListEl)
            && Boolean(nutritionInitialPayload && nutritionInitialPayload.ready);
        if (!hasNutritionUi) return;

        nutritionShoppingItems = readNutritionShoppingItems();
        const savedPreferences = readNutritionPreferences();
        if (savedPreferences) {
            nutritionCurrentPreferences = normalizeNutritionPreferences(savedPreferences);
            syncNutritionModalControls(nutritionCurrentPreferences);
            updateNutritionAllergyAlert(nutritionCurrentPreferences);
            if (savedPreferences.applied) {
                const generatedPlan = buildNutritionPlanFromPreferences(nutritionCurrentPreferences);
                applyNutritionPlan(generatedPlan.items, {
                    custom: true,
                    note: generatedPlan.strict && generatedPlan.strictMissedSlots > 0
                        ? 'Modo estricto: algunas comidas no se pudieron construir con tus ingredientes actuales.'
                        : 'Plan personalizado con tus ingredientes, tiempo y presupuesto.',
                    missingIngredients: generatedPlan.missingIngredients,
                    refreshShopping: true,
                });
            } else {
                renderNutritionShoppingItems();
            }
            updateNutritionSelectionFeedback();
            return;
        }

        nutritionCurrentPreferences = { ...nutritionDefaultPreferences };
        syncNutritionModalControls(nutritionCurrentPreferences);
        updateNutritionAllergyAlert(nutritionCurrentPreferences);
        applyNutritionPlan(nutritionInitialItems, {
            custom: false,
            refreshShopping: true,
        });
        updateNutritionSelectionFeedback();
    }

    function updateNutritionSelectionFeedback() {
        if (!nutritionCustomizeFeedbackEl) return;
        const prefs = collectNutritionPreferencesFromModal();
        const proteins = prefs.ingredients.filter((item) => {
            const meta = nutritionIngredientCatalog[item];
            return meta && meta.category === 'protein';
        }).length;
        const carbs = prefs.ingredients.filter((item) => {
            const meta = nutritionIngredientCatalog[item];
            return meta && (meta.category === 'carb' || meta.category === 'fruit');
        }).length;
        const allergyLabels = resolveAllergyLabels(prefs.allergies);
        const strictLabel = prefs.strict ? 'Modo estricto activo.' : 'Modo flexible activo.';
        const allergyLabel = allergyLabels.length > 0
            ? 'Alergias: ' + allergyLabels.join(', ') + '.'
            : 'Alergias: ninguna declarada.';
        const ackLabel = prefs.allergy_ack
            ? 'Aviso de alergias confirmado.'
            : 'Falta confirmar aviso de alergias.';
        nutritionCustomizeFeedbackEl.textContent = 'Ingredientes seleccionados: ' + String(prefs.ingredients.length)
            + '. Proteínas: ' + String(proteins)
            + ', Carbohidratos: ' + String(carbs)
            + '. ' + strictLabel
            + ' ' + allergyLabel
            + ' ' + ackLabel;
        renderNutritionCustomizePreview(prefs);
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

    leaderboardToggleBtn?.addEventListener('click', () => {
        const isOpen = leaderboardModal ? !leaderboardModal.classList.contains('hidden') : false;
        setLeaderboardOpen(!isOpen);
    });
    leaderboardCloseEls.forEach((element) => {
        element.addEventListener('click', () => {
            setLeaderboardOpen(false);
        });
    });

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

    function readActionGuideSeenState() {
        try {
            return window.localStorage.getItem(actionGuideSeenStorageKey) === '1';
        } catch (error) {
            return false;
        }
    }

    function markActionGuideSeen() {
        actionGuideAlreadySeen = true;
        try {
            window.localStorage.setItem(actionGuideSeenStorageKey, '1');
        } catch (error) {
            // ignore persistence errors
        }
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

    function setFitnessModalNextScreen(nextScreen) {
        if (!(fitnessModalNextScreenInput instanceof HTMLInputElement)) return;
        const normalized = String(nextScreen || '').trim().toLowerCase();
        fitnessModalNextScreenInput.value = allowedFitnessNextScreens.includes(normalized)
            ? normalized
            : fitnessModalDefaultNextScreen;
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
            markActionGuideSeen();
        }
        setActionGuideOpen(false);
    }

    function openActionGuide(mode, title, text, ctaLabel, ctaHandler) {
        if (!actionGuideModal || !actionGuideTitleEl || !actionGuideTextEl || !actionGuideCtaBtn) return;

        actionGuideMode = mode;
        if (actionGuideMode !== '') {
            markActionGuideSeen();
        }
        actionGuideTitleEl.textContent = String(title || 'Guía rápida');
        actionGuideTextEl.textContent = String(text || 'Sigue esta indicación para continuar.');
        actionGuideCtaBtn.textContent = String(ctaLabel || 'Entendido');
        actionGuideCtaHandler = typeof ctaHandler === 'function' ? ctaHandler : null;
        setActionGuideOpen(true);
    }

    function setTrainingFinishConfirmOpen(isOpen) {
        if (!trainingFinishConfirmModal) return;

        if (!isOpen && document.activeElement instanceof HTMLElement && trainingFinishConfirmModal.contains(document.activeElement)) {
            document.activeElement.blur();
        }

        trainingFinishConfirmModal.classList.toggle('hidden', !isOpen);
        trainingFinishConfirmModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        if ('inert' in trainingFinishConfirmModal) {
            trainingFinishConfirmModal.inert = !isOpen;
        }

        if (isOpen && trainingFinishConfirmCancelBtn instanceof HTMLElement) {
            window.setTimeout(() => trainingFinishConfirmCancelBtn.focus(), 20);
        }
    }

    function resolveTrainingFinishConfirm(accepted) {
        const resolver = trainingFinishConfirmResolver;
        trainingFinishConfirmResolver = null;
        setTrainingFinishConfirmOpen(false);
        if (typeof resolver === 'function') {
            resolver(Boolean(accepted));
        }
    }

    function requestTrainingFinishConfirm() {
        if (!trainingFinishConfirmModal) {
            return Promise.resolve(true);
        }
        if (typeof trainingFinishConfirmResolver === 'function') {
            return Promise.resolve(false);
        }
        return new Promise((resolve) => {
            trainingFinishConfirmResolver = resolve;
            setTrainingFinishConfirmOpen(true);
        });
    }

    function readTrainingWinSeenKey() {
        try {
            return String(window.sessionStorage.getItem(trainingWinSeenStorageKey) || '').trim();
        } catch (error) {
            return '';
        }
    }

    function markTrainingWinSeenKey(key) {
        try {
            window.sessionStorage.setItem(trainingWinSeenStorageKey, String(key || '').trim());
        } catch (error) {
            // ignore storage errors
        }
    }

    function setTrainingWinOpen(isOpen) {
        if (!trainingWinModal) return;

        if (!isOpen && document.activeElement instanceof HTMLElement && trainingWinModal.contains(document.activeElement)) {
            document.activeElement.blur();
        }

        trainingWinModal.classList.toggle('hidden', !isOpen);
        trainingWinModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        if ('inert' in trainingWinModal) {
            trainingWinModal.inert = !isOpen;
        }

        if (isOpen) {
            const confettiEls = Array.from(trainingWinModal.querySelectorAll('.training-win-confetti-piece'));
            confettiEls.forEach((pieceEl) => {
                if (!(pieceEl instanceof HTMLElement)) return;
                pieceEl.style.animation = 'none';
            });
            void trainingWinModal.offsetWidth;
            confettiEls.forEach((pieceEl) => {
                if (!(pieceEl instanceof HTMLElement)) return;
                pieceEl.style.animation = '';
            });
            if (trainingWinCloseBtn instanceof HTMLElement) {
                window.setTimeout(() => trainingWinCloseBtn.focus(), 20);
            }
        }
    }

    function closeTrainingWin() {
        setTrainingWinOpen(false);
    }

    function getTrainingCompletionSnapshot() {
        const toggleButtons = getTrainingChecklistButtons();
        const total = toggleButtons.length;
        const done = toggleButtons.reduce((count, buttonEl) => (
            buttonEl.getAttribute('aria-pressed') === 'true' ? count + 1 : count
        ), 0);

        return { done, total };
    }

    function openTrainingWin(progressPayload, message, force = false) {
        if (!trainingWinModal || currentScreen !== 'progress') return;

        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const trainingStatus = payload.training_status && typeof payload.training_status === 'object'
            ? payload.training_status
            : {};
        const weeklyGoal = payload.weekly_goal && typeof payload.weekly_goal === 'object'
            ? payload.weekly_goal
            : {};
        const prediction = payload.prediction && typeof payload.prediction === 'object'
            ? payload.prediction
            : {};

        const todayToken = String(payload.today || '').trim();
        const completedToday = Boolean(trainingStatus.completed_today);
        const displayKey = todayToken + ':' + (completedToday ? 'done' : 'active');
        if (!force && displayKey !== '' && readTrainingWinSeenKey() === displayKey) {
            return;
        }
        if (displayKey !== '') {
            markTrainingWinSeenKey(displayKey);
        }

        const completion = getTrainingCompletionSnapshot();
        const weeklyTarget = Number.isFinite(Number(weeklyGoal.target))
            ? Math.max(0, Math.round(Number(weeklyGoal.target)))
            : 0;
        const weeklyVisits = Number.isFinite(Number(weeklyGoal.visits))
            ? Math.max(0, Math.round(Number(weeklyGoal.visits)))
            : 0;
        const consistencyPct = Number.isFinite(Number(prediction.consistency_percent))
            ? Math.max(0, Math.min(100, Math.round(Number(prediction.consistency_percent))))
            : 0;

        if (trainingWinTextEl) {
            const summary = String(message || trainingStatus.status_label || '').trim();
            trainingWinTextEl.textContent = summary !== ''
                ? summary
                : 'Tu entrenamiento terminó correctamente. Tu progreso ya fue actualizado.';
        }
        if (trainingWinExercisesEl) {
            trainingWinExercisesEl.textContent = String(completion.done) + '/' + String(completion.total);
        }
        if (trainingWinWeeklyEl) {
            trainingWinWeeklyEl.textContent = String(weeklyVisits) + '/' + String(weeklyTarget);
        }
        if (trainingWinConsistencyEl) {
            trainingWinConsistencyEl.textContent = String(consistencyPct) + '%';
        }

        setTrainingWinOpen(true);
        if (typeof navigator.vibrate === 'function') {
            navigator.vibrate([40, 35, 60, 35, 90]);
        }
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
            ? (trainingFinishBtn && !trainingFinishBtn.classList.contains('hidden')
                ? trainingFinishBtn
                : (trainingStartBtn && !trainingStartBtn.classList.contains('hidden') ? trainingStartBtn : null))
            : (trainingStartBtn && !trainingStartBtn.classList.contains('hidden')
                ? trainingStartBtn
                : (trainingFinishBtn && !trainingFinishBtn.classList.contains('hidden') ? trainingFinishBtn : null));
        const fallbackBtn = trainingIdleBtn && !trainingIdleBtn.classList.contains('hidden') ? trainingIdleBtn : null;
        const resolvedTarget = targetBtn || fallbackBtn;

        if (!resolvedTarget) return false;
        resolvedTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
        resolvedTarget.classList.remove('guide-focus-target');
        void resolvedTarget.offsetWidth;
        resolvedTarget.classList.add('guide-focus-target');
        window.setTimeout(() => resolvedTarget.classList.remove('guide-focus-target'), 2800);
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
        if (actionGuideAlreadySeen) {
            closeActionGuide(false);
            return;
        }

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
                'Primero válida tu ingreso por QR, RFID o documento en recepción.',
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
                'Pulsa "Comenzar entrenamiento" en el botón flotante para activar el progreso de hoy.',
                'Ir al botón flotante',
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
                'Tu entrenamiento está activo. Usa el botón flotante para finalizar cuando termines.',
                'Ir a finalizar entrenamiento',
                () => navigateToAppScreen('progress', { focus_training_finish: '1' })
            );
            return;
        }

        if (mode === 'finish_here') {
            openActionGuide(
                mode,
                'Finaliza tu entrenamiento',
                'Ya tienes una sesión activa. Pulsa "Finalizar entrenamiento" en el botón flotante al terminar.',
                'Ir al botón flotante',
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
            renderTrainingChecklistProgress();
            return;
        }

        exercises.slice(0, 6).forEach((exercise, index) => {
            const name = String(exercise && exercise.name ? exercise.name : 'Ejercicio');
            const prescription = String(exercise && exercise.prescription ? exercise.prescription : '3 x 10');
            const checkKey = 'exercise-' + String(index);

            const itemEl = document.createElement('li');
            itemEl.className = 'training-item';
            itemEl.dataset.trainingKey = checkKey;

            const leftEl = document.createElement('span');
            leftEl.className = 'training-item-left';

            const toggleBtn = document.createElement('button');
            toggleBtn.type = 'button';
            toggleBtn.className = 'training-item-toggle';
            toggleBtn.dataset.trainingCheck = checkKey;
            toggleBtn.setAttribute('aria-pressed', 'false');
            toggleBtn.setAttribute('aria-label', 'Marcar ejercicio completado');
            toggleBtn.textContent = '○';

            const nameEl = document.createElement('span');
            nameEl.className = 'training-item-name';
            nameEl.textContent = name;

            const doseEl = document.createElement('span');
            doseEl.className = 'training-item-dose';
            doseEl.textContent = prescription;

            leftEl.append(toggleBtn, nameEl);
            itemEl.append(leftEl, doseEl);
            trainingPlanListEl.appendChild(itemEl);
        });

        syncTrainingChecklistUi();
    }

    function readTrainingChecklistState() {
        try {
            const raw = window.localStorage.getItem(trainingChecklistStorageKey);
            if (!raw) {
                return { date: '', done: {} };
            }

            const parsed = JSON.parse(raw);
            if (!parsed || typeof parsed !== 'object') {
                return { date: '', done: {} };
            }

            const date = typeof parsed.date === 'string' ? parsed.date : '';
            const doneRaw = parsed.done && typeof parsed.done === 'object' ? parsed.done : {};
            const done = {};
            Object.keys(doneRaw).forEach((key) => {
                if (!key || !doneRaw[key]) return;
                done[String(key)] = 1;
            });

            return { date, done };
        } catch (error) {
            return { date: '', done: {} };
        }
    }

    function persistTrainingChecklistState() {
        try {
            window.localStorage.setItem(trainingChecklistStorageKey, JSON.stringify({
                date: trainingChecklistDate,
                done: trainingChecklist,
            }));
        } catch (error) {
            // ignore storage errors
        }
    }

    function syncTrainingChecklistDate(todayToken) {
        const nextToken = String(todayToken || '').trim();
        if (nextToken === '') {
            trainingChecklistDate = '';
            trainingChecklist = {};
            persistTrainingChecklistState();
            return;
        }

        const stored = readTrainingChecklistState();
        if (stored.date === nextToken) {
            trainingChecklistDate = stored.date;
            trainingChecklist = stored.done;
            return;
        }

        trainingChecklistDate = nextToken;
        trainingChecklist = {};
        persistTrainingChecklistState();
    }

    function getTrainingChecklistButtons() {
        return Array.from(document.querySelectorAll('[data-training-check]'));
    }

    function renderTrainingChecklistProgress() {
        if (!trainingCompletionValueEl && !trainingCompletionFillEl) return;

        const toggleButtons = getTrainingChecklistButtons();
        const total = toggleButtons.length;
        const done = toggleButtons.reduce((count, buttonEl) => (
            buttonEl.getAttribute('aria-pressed') === 'true' ? count + 1 : count
        ), 0);
        const percent = total > 0 ? Math.max(0, Math.min(100, Math.round((done / total) * 100))) : 0;

        if (trainingCompletionValueEl) {
            trainingCompletionValueEl.textContent = String(done) + '/' + String(total) + ' ejercicios';
        }
        if (trainingCompletionFillEl) {
            trainingCompletionFillEl.style.width = String(percent) + '%';
        }
        if (trainingCompletionHintEl) {
            if (total <= 0) {
                trainingCompletionHintEl.textContent = 'Se cargará tu avance cuando haya ejercicios disponibles.';
            } else if (done >= total) {
                trainingCompletionHintEl.textContent = 'Excelente. Completaste toda la rutina de hoy.';
            } else {
                trainingCompletionHintEl.textContent = 'Llevas ' + String(done) + ' de ' + String(total) + '. Sigue con la siguiente serie.';
            }
        }
    }

    function syncTrainingChecklistUi() {
        const toggleButtons = getTrainingChecklistButtons();
        toggleButtons.forEach((buttonEl) => {
            const key = String(buttonEl.dataset.trainingCheck || '').trim();
            const isDone = key !== '' && Boolean(trainingChecklist[key]);
            buttonEl.setAttribute('aria-pressed', isDone ? 'true' : 'false');
            buttonEl.textContent = isDone ? '✓' : '○';
            const rowEl = buttonEl.closest('.training-item');
            if (rowEl) {
                rowEl.classList.toggle('is-done', isDone);
            }
        });
        renderTrainingChecklistProgress();
    }

    function toggleTrainingChecklist(buttonEl) {
        const key = String(buttonEl && buttonEl.dataset ? buttonEl.dataset.trainingCheck || '' : '').trim();
        if (key === '') return;
        const isDone = buttonEl.getAttribute('aria-pressed') === 'true';
        if (isDone) {
            delete trainingChecklist[key];
        } else {
            trainingChecklist[key] = 1;
        }
        persistTrainingChecklistState();
        syncTrainingChecklistUi();
    }

    function resetTrainingChecklist() {
        trainingChecklist = {};
        persistTrainingChecklistState();
        syncTrainingChecklistUi();
    }

    function clearTrainingRestCountdown() {
        if (trainingRestTimer) {
            window.clearInterval(trainingRestTimer);
            trainingRestTimer = null;
        }
    }

    function renderTrainingRestUi(message) {
        if (trainingRestTimerEl) {
            trainingRestTimerEl.textContent = formatSecondsAsClock(trainingRestRemainingSeconds);
        }
        if (trainingRestToggleBtn) {
            if (trainingRestRunning) {
                trainingRestToggleBtn.textContent = 'Pausar';
            } else if (trainingRestRemainingSeconds < 60 && trainingRestRemainingSeconds > 0) {
                trainingRestToggleBtn.textContent = 'Reanudar';
            } else {
                trainingRestToggleBtn.textContent = 'Iniciar 60s';
            }
        }
        if (trainingRestHintEl) {
            const fallback = trainingRestRunning
                ? 'Descanso en curso. Respira y prepárate para la siguiente serie.'
                : 'Usa este contador entre series para sostener intensidad.';
            trainingRestHintEl.textContent = String(message || fallback);
        }
    }

    function resetTrainingRestCountdown(message) {
        clearTrainingRestCountdown();
        trainingRestRunning = false;
        trainingRestRemainingSeconds = 60;
        renderTrainingRestUi(message);
    }

    function toggleTrainingRestCountdown() {
        if (trainingRestRunning) {
            trainingRestRunning = false;
            clearTrainingRestCountdown();
            renderTrainingRestUi('Descanso pausado. Puedes reanudar cuando quieras.');
            return;
        }

        if (trainingRestRemainingSeconds <= 0 || trainingRestRemainingSeconds > 60) {
            trainingRestRemainingSeconds = 60;
        }

        trainingRestRunning = true;
        renderTrainingRestUi('Descanso en curso. Respira y prepárate para la siguiente serie.');
        clearTrainingRestCountdown();
        trainingRestTimer = window.setInterval(() => {
            trainingRestRemainingSeconds = Math.max(0, trainingRestRemainingSeconds - 1);
            renderTrainingRestUi();
            if (trainingRestRemainingSeconds > 0) return;

            clearTrainingRestCountdown();
            trainingRestRunning = false;
            renderTrainingRestUi('Descanso completado. Continúa con tu siguiente ejercicio.');
            if (typeof navigator.vibrate === 'function') {
                navigator.vibrate([80, 50, 80]);
            }
        }, 1000);
    }

    function renderTodaySummary(progressPayload, statusHint) {
        if (!todaySummaryStateChipEl && !todaySummaryAttendanceEl && !todaySummaryWeeklyEl && !todaySummaryTimerEl) return;

        const payload = progressPayload && typeof progressPayload === 'object' ? progressPayload : {};
        const trainingStatus = payload.training_status && typeof payload.training_status === 'object'
            ? payload.training_status
            : {};
        const weeklyGoal = payload.weekly_goal && typeof payload.weekly_goal === 'object'
            ? payload.weekly_goal
            : {};

        const isActive = Boolean(trainingStatus.is_active);
        const canStart = Boolean(trainingStatus.can_start);
        const canFinish = Boolean(trainingStatus.can_finish);
        const completedToday = Boolean(trainingStatus.completed_today);
        const hasAttendanceToday = Boolean(trainingStatus.has_attendance_today);
        const target = Number.isFinite(Number(weeklyGoal.target)) ? Math.max(0, Math.round(Number(weeklyGoal.target))) : 0;
        const visits = Number.isFinite(Number(weeklyGoal.visits)) ? Math.max(0, Math.round(Number(weeklyGoal.visits))) : 0;
        const remainingSeconds = Number.isFinite(Number(trainingStatus.remaining_seconds))
            ? Math.max(0, Math.round(Number(trainingStatus.remaining_seconds)))
            : 0;
        const hintLine = String(statusHint || trainingStatus.hint_line || '').trim();

        let chipLabel = 'En espera';
        let chipClass = '';
        if (isActive) {
            chipLabel = 'Activo';
            chipClass = 'is-active';
        } else if (completedToday) {
            chipLabel = 'Completado';
            chipClass = 'is-done';
        } else if (canStart || canFinish) {
            chipLabel = 'Listo';
            chipClass = 'is-ready';
        }

        if (todaySummaryStateChipEl) {
            todaySummaryStateChipEl.textContent = chipLabel;
            todaySummaryStateChipEl.classList.remove('is-ready', 'is-active', 'is-done');
            if (chipClass !== '') {
                todaySummaryStateChipEl.classList.add(chipClass);
            }
        }
        if (todaySummaryAttendanceEl) {
            todaySummaryAttendanceEl.textContent = hasAttendanceToday ? 'Registrada' : 'Pendiente';
        }
        if (todaySummaryWeeklyEl) {
            todaySummaryWeeklyEl.textContent = String(visits) + '/' + String(target > 0 ? target : 0);
        }
        if (todaySummaryTimerEl) {
            todaySummaryTimerEl.textContent = isActive ? formatSecondsAsClock(remainingSeconds) : '--:--';
        }
        if (todaySummaryTipEl) {
            todaySummaryTipEl.textContent = hintLine !== ''
                ? hintLine
                : 'Inicia tu entrenamiento para desbloquear todo el panel.';
        }
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
        if (trainingFabShellEl && isBusy) {
            trainingFabShellEl.classList.remove('is-ready');
        }
        if (trainingStartBtn && !trainingStartBtn.classList.contains('hidden')) {
            trainingStartBtn.toggleAttribute('disabled', Boolean(isBusy));
        }
        if (trainingFinishBtn && !trainingFinishBtn.classList.contains('hidden')) {
            trainingFinishBtn.toggleAttribute('disabled', Boolean(isBusy));
        }
        if (trainingIdleBtn && !trainingIdleBtn.classList.contains('hidden')) {
            trainingIdleBtn.toggleAttribute('disabled', true);
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
            if (todaySummaryTimerEl) {
                todaySummaryTimerEl.textContent = formatSecondsAsClock(remainingSeconds);
            }

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
        const completedToday = Boolean(trainingStatus.completed_today);
        const hasAttendanceToday = Boolean(trainingStatus.has_attendance_today);
        const statusLabel = String(trainingStatus.status_label || 'Registra asistencia para habilitar entrenamiento.');
        const hintLine = String(trainingStatus.hint_line || 'Escanea tu asistencia y luego inicia entrenamiento.');
        const remainingSeconds = Number.isFinite(Number(trainingStatus.remaining_seconds))
            ? Math.max(0, Math.round(Number(trainingStatus.remaining_seconds)))
            : 0;
        const scheduledEndAt = String(trainingStatus.scheduled_end_at || '').trim();
        const todayToken = String(payload.today || '').trim();

        if (todayToken !== trainingChecklistDate) {
            syncTrainingChecklistDate(todayToken);
            syncTrainingChecklistUi();
        }

        if (canStart && !isActive && !canFinish) {
            resetTrainingChecklist();
        }

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
        if (trainingIdleBtn) {
            const showIdle = !canStart && !canFinish;
            trainingIdleBtn.classList.toggle('hidden', !showIdle);
            if (showIdle) {
                if (completedToday) {
                    trainingIdleBtn.textContent = 'Entrenamiento completado';
                } else if (hasAttendanceToday) {
                    trainingIdleBtn.textContent = 'Esperando próxima sesión';
                } else {
                    trainingIdleBtn.textContent = 'Registra asistencia para iniciar';
                }
            }
        }
        if (trainingFabShellEl) {
            const hasAction = canStart || canFinish;
            trainingFabShellEl.classList.toggle('is-ready', hasAction && !trainingActionBusy);
        }

        if (isActive) {
            if (scheduledEndAt !== '') {
                startTrainingCountdown(scheduledEndAt);
            } else if (trainingSessionTimerEl && trainingSessionTimerValueEl) {
                trainingSessionTimerValueEl.textContent = formatSecondsAsClock(remainingSeconds);
                trainingSessionTimerEl.classList.remove('hidden');
                if (todaySummaryTimerEl) {
                    todaySummaryTimerEl.textContent = formatSecondsAsClock(remainingSeconds);
                }
                clearTrainingCountdown();
            }
        } else if (trainingSessionTimerEl && trainingSessionTimerValueEl) {
            clearTrainingCountdown();
            trainingSessionTimerValueEl.textContent = '--:--';
            trainingSessionTimerEl.classList.add('hidden');
            if (todaySummaryTimerEl) {
                todaySummaryTimerEl.textContent = '--:--';
            }
        }

        if (!isActive && !canFinish) {
            resetTrainingRestCountdown();
        }

        const completedByTransition = previousTrainingIsActive && !isActive && completedToday && !previousTrainingCompleted;
        previousTrainingIsActive = isActive;
        previousTrainingCompleted = completedToday;
        if (completedByTransition) {
            openTrainingWin(payload, 'Sesion finalizada. Tu progreso se actualizo correctamente.');
        }

        renderTodaySummary(payload, hintLine);
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
        renderLeaderboard(payload);
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
            if (url === trainingFinishUrl && payload && payload.progress && typeof payload.progress === 'object') {
                const finishStatus = payload.progress.training_status && typeof payload.progress.training_status === 'object'
                    ? payload.progress.training_status
                    : {};
                if (Boolean(finishStatus.completed_today)) {
                    openTrainingWin(payload.progress, message, true);
                }
            }
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
            return 'No se concedió permiso de cámara. Pulsa Escanear QR y acepta el popup del navegador.';
        }
        if (errorName === 'NotFoundError' || errorName === 'DevicesNotFoundError') {
            return 'No se encontró una cámara disponible en este dispositivo.';
        }
        if (errorName === 'NotReadableError' || errorName === 'TrackStartError') {
            return 'La cámara está en uso por otra app o pestaña. Cierra la otra app e intenta de nuevo.';
        }
        if (errorName === 'OverconstrainedError' || errorName === 'ConstraintNotSatisfiedError') {
            return 'No se pudo usar la cámara trasera. Intenta otra vez y usa la cámara principal.';
        }
        if (errorName === 'SecurityError') {
            return 'El navegador bloqueó la cámara por seguridad.';
        }
        if (errorName === 'AbortError') {
            return 'Se interrumpio la apertura de la cámara. Intenta nuevamente.';
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

        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            statusEl.textContent = 'Este navegador no permite abrir la cámara.';
            return;
        }

        stopScan();
        startBtn.disabled = true;
        resetScanMarkers();
        statusEl.textContent = 'Abriendo cámara...';

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
        if (shouldOpen) {
            setLeaderboardOpen(false);
        }
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
        if (trainingFinishConfirmModal && !trainingFinishConfirmModal.classList.contains('hidden')) {
            resolveTrainingFinishConfirm(false);
            return;
        }
        if (nutritionCustomizeModal && !nutritionCustomizeModal.classList.contains('hidden')) {
            setNutritionCustomizeModalOpen(false);
            return;
        }
        setUserMenuOpen(false);
        setLeaderboardOpen(false);
        closeTrainingWin();
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
        if (actionGuideMode !== '') {
            markActionGuideSeen();
        }

        if (typeof actionGuideCtaHandler !== 'function') {
            closeActionGuide(false);
            return;
        }
        actionGuideCtaHandler();
    });

    trainingFinishConfirmCancelEls.forEach((element) => {
        element.addEventListener('click', () => {
            resolveTrainingFinishConfirm(false);
        });
    });
    trainingFinishConfirmCancelBtn?.addEventListener('click', () => {
        resolveTrainingFinishConfirm(false);
    });
    trainingFinishConfirmAcceptBtn?.addEventListener('click', () => {
        resolveTrainingFinishConfirm(true);
    });

    trainingWinCloseEls.forEach((element) => {
        element.addEventListener('click', () => {
            closeTrainingWin();
        });
    });
    trainingWinCloseBtn?.addEventListener('click', () => {
        closeTrainingWin();
    });
    trainingWinCloseSecondaryBtn?.addEventListener('click', () => {
        closeTrainingWin();
    });

    nutritionCustomizeOpenBtn?.addEventListener('click', () => {
        setUserMenuOpen(false);
        setNutritionShoppingModalOpen(false);
        syncNutritionModalControls(nutritionCurrentPreferences);
        updateNutritionSelectionFeedback();
        setNutritionCustomizeModalOpen(true);
    });

    nutritionShoppingOpenBtn?.addEventListener('click', () => {
        setUserMenuOpen(false);
        renderNutritionShoppingItems();
        setNutritionShoppingModalOpen(true);
    });

    nutritionShoppingCloseEls.forEach((element) => {
        element.addEventListener('click', () => {
            setNutritionShoppingModalOpen(false);
        });
    });

    nutritionOpenCustomizerBtns.forEach((button) => {
        button.addEventListener('click', () => {
            setNutritionShoppingModalOpen(false);
            syncNutritionModalControls(nutritionCurrentPreferences);
            updateNutritionSelectionFeedback();
            setNutritionCustomizeModalOpen(true);
        });
    });

    nutritionCustomizeCloseEls.forEach((element) => {
        element.addEventListener('click', () => {
            setNutritionCustomizeModalOpen(false);
        });
    });

    nutritionCustomizeIngredientBtns.forEach((button) => {
        button.addEventListener('click', () => {
            button.classList.toggle('is-selected');
            const isSelected = button.classList.contains('is-selected');
            button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            updateNutritionSelectionFeedback();
        });
    });

    nutritionCustomizeAllergyBtns.forEach((button) => {
        button.addEventListener('click', () => {
            button.classList.toggle('is-selected');
            const isSelected = button.classList.contains('is-selected');
            button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');

            const nextPreferences = collectNutritionPreferencesFromModal();
            syncNutritionIngredientButtons(nextPreferences);
            updateNutritionSelectionFeedback();
            updateNutritionAllergyAlert(nextPreferences);
        });
    });

    nutritionCustomizeTimeEl?.addEventListener('change', () => {
        updateNutritionSelectionFeedback();
    });

    nutritionCustomizeBudgetEl?.addEventListener('change', () => {
        updateNutritionSelectionFeedback();
    });

    nutritionCustomizeStrictEl?.addEventListener('change', () => {
        updateNutritionSelectionFeedback();
    });

    nutritionCustomizeAllergyAckEl?.addEventListener('change', () => {
        updateNutritionSelectionFeedback();
    });

    nutritionCustomizeApplyBtn?.addEventListener('click', () => {
        const preferences = collectNutritionPreferencesFromModal();
        const selectedCount = preferences.ingredients.length;

        if (selectedCount < 2) {
            if (nutritionCustomizeFeedbackEl) {
                nutritionCustomizeFeedbackEl.textContent = 'Para personalizar mejor, selecciona al menos 2 ingredientes.';
            }
            return;
        }

        if (!preferences.allergy_ack) {
            if (nutritionCustomizeFeedbackEl) {
                nutritionCustomizeFeedbackEl.textContent = 'Debes confirmar el aviso de alergias para aplicar el plan.';
            }
            return;
        }

        nutritionCurrentPreferences = preferences;
        const generatedPlan = buildNutritionPlanFromPreferences(preferences);
        if (!Array.isArray(generatedPlan.items) || generatedPlan.items.length === 0) {
            if (nutritionCustomizeFeedbackEl) {
                nutritionCustomizeFeedbackEl.textContent = 'No encontramos combinaciones suficientes con esa selección. Prueba agregando más ingredientes.';
            }
            return;
        }
        const allergyLabels = resolveAllergyLabels(preferences.allergies);
        const allergyNote = allergyLabels.length > 0
            ? 'Alergias excluidas: ' + allergyLabels.join(', ') + '.'
            : 'Sin alergias declaradas.';
        const note = generatedPlan.strict && generatedPlan.strictMissedSlots > 0
            ? 'Modo estricto activo: algunas comidas no se armaron porque faltan ingredientes. Revisa la lista de compras. ' + allergyNote
            : 'Plan personalizado exitoso: comidas adaptadas a lo que tienes hoy. ' + allergyNote;

        applyNutritionPlan(generatedPlan.items, {
            custom: true,
            note,
            missingIngredients: generatedPlan.missingIngredients,
            refreshShopping: true,
        });
        writeNutritionPreferences(preferences, true);
        setNutritionCustomizeModalOpen(false);
    });

    nutritionCustomizeResetBtn?.addEventListener('click', () => {
        nutritionCurrentPreferences = { ...nutritionDefaultPreferences };
        applyNutritionPlan(nutritionInitialItems, {
            custom: false,
            note: 'Volviste al plan sugerido por la app. Puedes personalizarlo de nuevo cuando quieras.',
            missingIngredients: [],
            refreshShopping: true,
        });
        clearNutritionPreferences();
        syncNutritionModalControls(nutritionCurrentPreferences);
        if (nutritionCustomizeFeedbackEl) {
            nutritionCustomizeFeedbackEl.textContent = 'Selecciona tus ingredientes para personalizar tus comidas.';
        }
    });

    nutritionShoppingAutoFillBtn?.addEventListener('click', () => {
        const selectedSet = new Set(Array.isArray(nutritionCurrentPreferences.ingredients) ? nutritionCurrentPreferences.ingredients : []);
        const excludedSet = resolveAllergyExcludedKeys(nutritionCurrentPreferences);
        const fromMissing = nutritionCurrentMissingIngredients
            .map((item) => {
                const key = String(item.key || '').trim();
                const meta = key !== '' ? nutritionIngredientCatalog[key] : null;
                return {
                    key,
                    label: String(item.label || '').trim(),
                    reason: 'faltante',
                    hint: String(item.hint || (meta && meta.shopping ? meta.shopping : '')).trim(),
                    checked: false,
                };
            })
            .filter((item) => item.key !== '' && item.label !== '')
            .filter((item) => !excludedSet.has(item.key));
        if (fromMissing.length > 0) {
            mergeNutritionShoppingSuggestions(fromMissing);
            return;
        }
        mergeNutritionShoppingSuggestions(buildDefaultNutritionShoppingSuggestions(selectedSet, excludedSet));
    });

    nutritionShoppingAddBtn?.addEventListener('click', () => {
        addManualNutritionShoppingItem(nutritionShoppingInputEl?.value || '');
        if (nutritionShoppingInputEl) {
            nutritionShoppingInputEl.value = '';
            nutritionShoppingInputEl.focus();
        }
    });

    nutritionShoppingInputEl?.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        addManualNutritionShoppingItem(nutritionShoppingInputEl.value || '');
        nutritionShoppingInputEl.value = '';
    });

    nutritionShoppingClearDoneBtn?.addEventListener('click', () => {
        nutritionShoppingItems = nutritionShoppingItems.filter((item) => !item.checked);
        writeNutritionShoppingItems(nutritionShoppingItems);
        renderNutritionShoppingItems();
    });

    nutritionShoppingListEl?.addEventListener('change', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLInputElement) || target.type !== 'checkbox') return;
        const row = target.closest('[data-shopping-key]');
        if (!(row instanceof HTMLElement)) return;
        const key = String(row.dataset.shoppingKey || '').trim();
        if (key === '') return;
        nutritionShoppingItems = nutritionShoppingItems.map((item) => (
            item.key === key ? { ...item, checked: target.checked } : item
        ));
        writeNutritionShoppingItems(nutritionShoppingItems);
        renderNutritionShoppingItems();
    });

    openFitnessModalTriggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            setFitnessModalNextScreen(String(trigger.dataset.nextScreen || '').trim());
            setUserMenuOpen(false);
            setFitnessModalOpen(true);
        });
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

    trainingPlanListEl?.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        const checkBtn = target.closest('[data-training-check]');
        if (!(checkBtn instanceof HTMLButtonElement)) return;
        toggleTrainingChecklist(checkBtn);
    });

    trainingRestToggleBtn?.addEventListener('click', () => {
        toggleTrainingRestCountdown();
    });

    trainingStartBtn?.addEventListener('click', async () => {
        await triggerTrainingAction(trainingStartUrl, 'Iniciando entrenamiento...');
    });

    trainingFinishBtn?.addEventListener('click', async () => {
        const shouldFinish = await requestTrainingFinishConfirm();
        if (!shouldFinish) return;
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
        clearTrainingCountdown();
        clearTrainingRestCountdown();
        closeTrainingWin();
        resolveTrainingFinishConfirm(false);
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
    actionGuideAlreadySeen = readActionGuideSeenState();
    armDirectPermissionPrompt();
    refreshClientPushStatus();
    initBootScreen();
    initializeNutritionCustomization();
    hideModuleLoader();
    const focusFlags = consumeFocusParamsFromUrl();
    if (currentScreen === 'progress' && (focusFlags.focusStart || focusFlags.focusFinish)) {
        window.setTimeout(() => {
            focusTrainingActionButton(Boolean(focusFlags.focusFinish));
        }, 380);
    }
    syncTrainingChecklistDate(String(initialProgressPayload && initialProgressPayload.today ? initialProgressPayload.today : '').trim());
    syncTrainingChecklistUi();
    renderTrainingRestUi();
    if (currentScreen === 'progress') {
        applyProgressPayload(initialProgressPayload);
    } else {
        renderLeaderboard(initialProgressPayload);
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
