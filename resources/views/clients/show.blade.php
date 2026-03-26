@extends('layouts.panel')

@section('title', 'Cliente #'.$client->id)
@section('page-title', 'Cliente: '.$client->full_name)

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .client-hero-card {
            border: 1px solid rgb(148 163 184 / 0.22);
            background:
                radial-gradient(circle at top right, rgb(14 165 233 / 0.14), transparent 34%),
                linear-gradient(180deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.98));
            box-shadow: 0 28px 46px -36px rgb(15 23 42 / 0.34), inset 0 1px 0 rgb(255 255 255 / 0.78);
            backdrop-filter: blur(10px);
        }

        .theme-dark .client-hero-card,
        .dark .client-hero-card {
            border-color: rgb(51 65 85 / 0.9);
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.18), transparent 34%),
                linear-gradient(180deg, rgb(2 6 23 / 0.96), rgb(15 23 42 / 0.9));
            box-shadow: 0 30px 48px -36px rgb(2 8 23 / 0.9), inset 0 1px 0 rgb(255 255 255 / 0.04);
        }

        .client-hero-stat {
            border: 1px solid rgb(148 163 184 / 0.28);
            background: rgb(255 255 255 / 0.7);
            border-radius: 1rem;
            padding: 0.8rem 0.9rem;
            box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.22), inset 0 1px 0 rgb(255 255 255 / 0.64);
        }

        .theme-dark .client-hero-stat,
        .dark .client-hero-stat {
            border-color: rgb(148 163 184 / 0.18);
            background: rgb(15 23 42 / 0.62);
            box-shadow: 0 20px 32px -28px rgb(2 8 23 / 0.82), inset 0 1px 0 rgb(255 255 255 / 0.04);
        }

        .client-hero-stat-label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgb(100 116 139);
        }

        .theme-dark .client-hero-stat-label {
            color: rgb(148 163 184);
        }

        .client-hero-actions {
            display: grid;
            gap: 0.75rem;
            width: 100%;
            align-content: start;
        }

        .client-hero-layout {
            display: grid;
            gap: 1.25rem;
        }

        .client-hero-actions-full {
            grid-column: 1 / -1;
        }

        .client-action-popover {
            width: min(19rem, calc(100vw - 2rem));
        }

        .client-hero-status {
            display: flex;
            justify-content: flex-start;
        }

        .client-control-shell {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid rgb(163 230 53 / 0.22);
            border-radius: 1.22rem;
            background:
                radial-gradient(circle at top right, rgb(163 230 53 / 0.16), transparent 34%),
                linear-gradient(152deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
            box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.5);
            backdrop-filter: blur(14px);
            padding: 1.05rem;
        }

        .theme-dark .client-control-shell {
            border-color: rgb(163 230 53 / 0.24);
            background:
                radial-gradient(circle at top right, rgb(163 230 53 / 0.14), transparent 34%),
                linear-gradient(160deg, rgb(2 6 23 / 0.86), rgb(15 23 42 / 0.68));
            box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.92);
        }

        .client-control-shell::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
            opacity: 0.8;
            pointer-events: none;
        }

        .client-control-shell::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(95deg, transparent, rgb(163 230 53 / 0.05), transparent);
        }

        .client-control-grid {
            display: grid;
            gap: 1.05rem;
            position: relative;
            z-index: 1;
        }

        .client-control-copy {
            max-width: 48rem;
        }

        .client-control-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.17em;
            text-transform: uppercase;
            color: rgb(77 124 15 / 0.94);
        }

        .theme-dark .client-control-kicker {
            color: rgb(217 249 157 / 0.94);
        }

        .client-control-kicker::before {
            content: '';
            width: 0.52rem;
            height: 0.52rem;
            border-radius: 999px;
            background: rgb(132 204 22 / 0.94);
            box-shadow: 0 0 0 6px rgb(132 204 22 / 0.12);
        }

        .client-control-heading {
            margin-top: 0.78rem;
            font-size: clamp(1.14rem, 1.85vw, 1.46rem);
            line-height: 1.08;
            letter-spacing: -0.035em;
            font-weight: 900;
            color: rgb(15 23 42 / 0.97);
        }

        .theme-dark .client-control-heading {
            color: rgb(241 245 249 / 0.98);
        }

        .client-control-summary {
            margin-top: 0.5rem;
            font-size: 0.88rem;
            line-height: 1.58;
            color: rgb(71 85 105 / 0.92);
        }

        .theme-dark .client-control-summary {
            color: rgb(148 163 184 / 0.9);
        }

        .client-control-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            align-items: center;
        }

        .client-control-actions .ui-button {
            min-height: 2.72rem;
        }

        .client-control-priority-grid {
            display: grid;
            gap: 0.75rem;
        }

        .client-control-priority {
            position: relative;
            overflow: hidden;
            border-radius: 1.05rem;
            border: 1px solid rgb(148 163 184 / 0.24);
            background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
            box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
            min-height: 7rem;
            padding: 0.9rem 0.95rem;
        }

        .theme-dark .client-control-priority {
            border-color: rgb(148 163 184 / 0.18);
            background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
            box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
        }

        .client-control-priority::before {
            content: '';
            position: absolute;
            left: 0.9rem;
            right: 0.9rem;
            top: 0;
            height: 2px;
            border-radius: 999px;
            background: rgb(148 163 184 / 0.22);
        }

        .client-control-priority[data-tone='warning']::before {
            background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
        }

        .client-control-priority[data-tone='success']::before {
            background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
        }

        .client-control-priority[data-tone='info']::before {
            background: linear-gradient(90deg, rgb(6 182 212 / 0.9), rgb(6 182 212 / 0.24));
        }

        .client-control-priority-label {
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgb(71 85 105 / 0.92);
        }

        .theme-dark .client-control-priority-label {
            color: rgb(148 163 184 / 0.9);
        }

        .client-control-priority-value {
            margin-top: 0.42rem;
            font-size: 1.46rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: rgb(15 23 42 / 0.97);
        }

        .theme-dark .client-control-priority-value {
            color: rgb(248 250 252 / 0.98);
        }

        .client-control-priority-note {
            margin-top: 0.4rem;
            font-size: 0.75rem;
            line-height: 1.45;
            color: rgb(71 85 105 / 0.9);
        }

        .theme-dark .client-control-priority-note {
            color: rgb(148 163 184 / 0.88);
        }

        .client-pro-shell {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid rgb(34 211 238 / 0.2);
            border-radius: 1.22rem;
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.12), transparent 34%),
                radial-gradient(circle at bottom left, rgb(245 158 11 / 0.1), transparent 28%),
                linear-gradient(152deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
            box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.5);
            backdrop-filter: blur(14px);
            padding: 1.05rem;
        }

        .theme-dark .client-pro-shell,
        .dark .client-pro-shell {
            border-color: rgb(34 211 238 / 0.22);
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.11), transparent 34%),
                radial-gradient(circle at bottom left, rgb(245 158 11 / 0.08), transparent 28%),
                linear-gradient(155deg, rgb(4 10 28 / 0.94), rgb(11 18 32 / 0.88));
            box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.9);
        }

        .client-pro-shell::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
            opacity: 0.8;
            pointer-events: none;
        }

        .client-pro-shell::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(95deg, transparent, rgb(34 211 238 / 0.04), transparent);
        }

        .client-pro-grid {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 1rem;
        }

        .client-pro-copy {
            max-width: 50rem;
        }

        .client-pro-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.17em;
            text-transform: uppercase;
            color: rgb(8 145 178 / 0.96);
        }

        .theme-dark .client-pro-kicker,
        .dark .client-pro-kicker {
            color: rgb(165 243 252 / 0.94);
        }

        .client-pro-kicker::before {
            content: '';
            width: 0.52rem;
            height: 0.52rem;
            border-radius: 999px;
            background: rgb(34 211 238 / 0.96);
            box-shadow: 0 0 0 6px rgb(34 211 238 / 0.14);
        }

        .client-pro-heading {
            margin-top: 0.78rem;
            font-size: clamp(1.14rem, 1.85vw, 1.46rem);
            line-height: 1.08;
            letter-spacing: -0.035em;
            font-weight: 900;
            color: rgb(15 23 42 / 0.97);
        }

        .theme-dark .client-pro-heading,
        .dark .client-pro-heading {
            color: rgb(241 245 249 / 0.98);
        }

        .client-pro-summary {
            margin-top: 0.5rem;
            font-size: 0.88rem;
            line-height: 1.58;
            color: rgb(71 85 105 / 0.92);
        }

        .theme-dark .client-pro-summary,
        .dark .client-pro-summary {
            color: rgb(148 163 184 / 0.9);
        }

        .client-pro-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 999px;
            border: 1px solid rgb(34 211 238 / 0.22);
            background: rgb(236 254 255 / 0.84);
            padding: 0.44rem 0.78rem;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgb(8 145 178 / 0.96);
        }

        .theme-dark .client-pro-badge,
        .dark .client-pro-badge {
            border-color: rgb(34 211 238 / 0.26);
            background: rgb(8 145 178 / 0.12);
            color: rgb(165 243 252 / 0.95);
        }

        .client-pro-metrics {
            display: grid;
            gap: 0.75rem;
        }

        .client-pro-metric {
            position: relative;
            overflow: hidden;
            border-radius: 1.02rem;
            border: 1px solid rgb(148 163 184 / 0.24);
            background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
            box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
            min-height: 6.7rem;
            padding: 0.9rem 0.95rem;
        }

        .theme-dark .client-pro-metric,
        .dark .client-pro-metric {
            border-color: rgb(148 163 184 / 0.16);
            background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
            box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
        }

        .client-pro-metric::before {
            content: '';
            position: absolute;
            left: 0.9rem;
            right: 0.9rem;
            top: 0;
            height: 2px;
            border-radius: 999px;
            background: rgb(148 163 184 / 0.22);
        }

        .client-pro-metric[data-tone='success']::before {
            background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
        }

        .client-pro-metric[data-tone='info']::before {
            background: linear-gradient(90deg, rgb(34 211 238 / 0.9), rgb(34 211 238 / 0.24));
        }

        .client-pro-metric[data-tone='warning']::before {
            background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
        }

        .client-pro-metric[data-tone='accent']::before {
            background: linear-gradient(90deg, rgb(168 85 247 / 0.9), rgb(168 85 247 / 0.24));
        }

        .client-pro-metric-label {
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgb(71 85 105 / 0.92);
        }

        .theme-dark .client-pro-metric-label,
        .dark .client-pro-metric-label {
            color: rgb(148 163 184 / 0.9);
        }

        .client-pro-metric-value {
            margin-top: 0.42rem;
            font-size: 1.46rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: rgb(15 23 42 / 0.97);
        }

        .theme-dark .client-pro-metric-value,
        .dark .client-pro-metric-value {
            color: rgb(248 250 252 / 0.98);
        }

        .client-pro-metric-note {
            margin-top: 0.4rem;
            font-size: 0.75rem;
            line-height: 1.45;
            color: rgb(71 85 105 / 0.9);
        }

        .theme-dark .client-pro-metric-note,
        .dark .client-pro-metric-note {
            color: rgb(148 163 184 / 0.88);
        }

        .client-pro-insights {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .client-pro-chip {
            min-width: min(100%, 14rem);
            flex: 1 1 14rem;
            border-radius: 0.95rem;
            border: 1px solid rgb(148 163 184 / 0.2);
            background: rgb(255 255 255 / 0.66);
            padding: 0.78rem 0.85rem;
            box-shadow: 0 16px 28px -30px rgb(15 23 42 / 0.32);
        }

        .theme-dark .client-pro-chip,
        .dark .client-pro-chip {
            border-color: rgb(148 163 184 / 0.14);
            background: rgb(15 23 42 / 0.58);
            box-shadow: 0 20px 30px -30px rgb(2 8 23 / 0.9);
        }

        .client-pro-chip[data-tone='warning'] {
            border-color: rgb(245 158 11 / 0.22);
            background: rgb(255 251 235 / 0.9);
        }

        .client-pro-chip[data-tone='danger'] {
            border-color: rgb(244 63 94 / 0.2);
            background: rgb(255 241 242 / 0.9);
        }

        .client-pro-chip[data-tone='success'] {
            border-color: rgb(16 185 129 / 0.22);
            background: rgb(236 253 245 / 0.9);
        }

        .client-pro-chip[data-tone='info'] {
            border-color: rgb(34 211 238 / 0.22);
            background: rgb(236 254 255 / 0.9);
        }

        .theme-dark .client-pro-chip[data-tone='warning'],
        .dark .client-pro-chip[data-tone='warning'] {
            background: rgb(120 53 15 / 0.18);
        }

        .theme-dark .client-pro-chip[data-tone='danger'],
        .dark .client-pro-chip[data-tone='danger'] {
            background: rgb(127 29 29 / 0.18);
        }

        .theme-dark .client-pro-chip[data-tone='success'],
        .dark .client-pro-chip[data-tone='success'] {
            background: rgb(6 78 59 / 0.18);
        }

        .theme-dark .client-pro-chip[data-tone='info'],
        .dark .client-pro-chip[data-tone='info'] {
            background: rgb(8 145 178 / 0.14);
        }

        .client-pro-chip-title {
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgb(30 41 59 / 0.92);
        }

        .theme-dark .client-pro-chip-title,
        .dark .client-pro-chip-title {
            color: rgb(226 232 240 / 0.96);
        }

        .client-pro-chip-copy {
            margin-top: 0.35rem;
            font-size: 0.76rem;
            line-height: 1.45;
            color: rgb(71 85 105 / 0.9);
        }

        .theme-dark .client-pro-chip-copy,
        .dark .client-pro-chip-copy {
            color: rgb(148 163 184 / 0.88);
        }

        .client-pro-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            align-items: center;
        }

        .client-pro-actions .ui-button {
            min-height: 2.72rem;
        }

        .client-elite-shell.client-pro-shell {
            border-color: rgb(234 179 8 / 0.24);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.18), transparent 36%),
                radial-gradient(circle at bottom left, rgb(16 185 129 / 0.1), transparent 30%),
                linear-gradient(150deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.97));
            box-shadow:
                0 34px 68px -44px rgb(120 53 15 / 0.24),
                inset 0 1px 0 rgb(255 255 255 / 0.9);
            padding: 1.2rem;
        }

        .theme-dark .client-elite-shell.client-pro-shell,
        .dark .client-elite-shell.client-pro-shell {
            border-color: rgb(234 179 8 / 0.28);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.16), transparent 36%),
                radial-gradient(circle at bottom left, rgb(16 185 129 / 0.11), transparent 30%),
                linear-gradient(155deg, rgb(10 12 24 / 0.96), rgb(17 24 39 / 0.92));
            box-shadow:
                0 36px 72px -46px rgb(2 8 23 / 0.92),
                inset 0 1px 0 rgb(255 255 255 / 0.05);
        }

        .client-elite-shell.client-pro-shell::after {
            background: linear-gradient(100deg, transparent 8%, rgb(234 179 8 / 0.08), transparent 74%);
        }

        .client-elite-shell .client-pro-grid {
            gap: 0.82rem;
        }

        .client-elite-shell .client-elite-head {
            align-items: end;
            gap: 1rem;
        }

        .client-elite-shell .client-pro-copy {
            max-width: 48rem;
        }

        .client-elite-shell .client-pro-kicker {
            color: rgb(161 98 7 / 0.96);
            letter-spacing: 0.15em;
        }

        .theme-dark .client-elite-shell .client-pro-kicker,
        .dark .client-elite-shell .client-pro-kicker {
            color: rgb(253 224 71 / 0.94);
        }

        .client-elite-shell .client-pro-kicker::before {
            background: rgb(234 179 8 / 0.96);
            box-shadow: 0 0 0 6px rgb(234 179 8 / 0.14);
        }

        .client-elite-shell .client-pro-badge {
            border-color: rgb(234 179 8 / 0.24);
            background: rgb(254 249 195 / 0.84);
            color: rgb(161 98 7 / 0.96);
            padding: 0.48rem 0.92rem;
            box-shadow: 0 14px 30px -24px rgb(161 98 7 / 0.28);
        }

        .theme-dark .client-elite-shell .client-pro-badge,
        .dark .client-elite-shell .client-pro-badge {
            border-color: rgb(234 179 8 / 0.26);
            background: rgb(161 98 7 / 0.12);
            color: rgb(253 224 71 / 0.95);
            box-shadow: 0 14px 32px -24px rgb(234 179 8 / 0.2);
        }

        .client-elite-shell .client-pro-heading {
            margin-top: 0.42rem;
            max-width: 24ch;
            font-size: clamp(1.08rem, 1.55vw, 1.36rem);
            line-height: 1.04;
        }

        .client-elite-shell .client-pro-summary {
            max-width: 36rem;
            margin-top: 0.38rem;
            font-size: 0.82rem;
            line-height: 1.42;
            color: rgb(71 85 105 / 0.96);
        }

        .theme-dark .client-elite-shell .client-pro-summary,
        .dark .client-elite-shell .client-pro-summary {
            color: rgb(203 213 225 / 0.82);
        }

        .client-elite-shell .client-pro-metrics {
            gap: 0.68rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .client-elite-shell .client-pro-metric {
            min-height: auto;
            padding: 0.82rem 0.92rem;
            border-color: rgb(234 179 8 / 0.16);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.94), rgb(248 250 252 / 0.82));
            box-shadow:
                0 20px 34px -28px rgb(120 53 15 / 0.14),
                inset 0 1px 0 rgb(255 255 255 / 0.86);
        }

        .theme-dark .client-elite-shell .client-pro-metric,
        .dark .client-elite-shell .client-pro-metric {
            border-color: rgb(234 179 8 / 0.14);
            background:
                linear-gradient(165deg, rgb(15 23 42 / 0.82), rgb(15 23 42 / 0.62));
            box-shadow:
                0 22px 38px -30px rgb(2 8 23 / 0.9),
                inset 0 1px 0 rgb(255 255 255 / 0.04);
        }

        .client-elite-shell .client-pro-metric:first-child {
            border-color: rgb(234 179 8 / 0.26);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.18), transparent 44%),
                linear-gradient(180deg, rgb(255 251 235 / 0.96), rgb(255 255 255 / 0.84));
            grid-column: span 1;
        }

        .theme-dark .client-elite-shell .client-pro-metric:first-child,
        .dark .client-elite-shell .client-pro-metric:first-child {
            border-color: rgb(234 179 8 / 0.24);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.15), transparent 44%),
                linear-gradient(165deg, rgb(31 41 55 / 0.9), rgb(15 23 42 / 0.74));
        }

        .client-elite-shell .client-pro-metric-label {
            color: rgb(120 53 15 / 0.86);
        }

        .theme-dark .client-elite-shell .client-pro-metric-label,
        .dark .client-elite-shell .client-pro-metric-label {
            color: rgb(253 224 71 / 0.8);
        }

        .client-elite-shell .client-pro-metric-value {
            margin-top: 0.42rem;
            font-size: clamp(1.32rem, 2vw, 1.72rem);
            line-height: 1;
        }

        .client-elite-shell .client-pro-chip {
            border-color: rgb(234 179 8 / 0.16);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.84), rgb(255 255 255 / 0.7));
            box-shadow:
                0 18px 30px -28px rgb(120 53 15 / 0.12),
                inset 0 1px 0 rgb(255 255 255 / 0.82);
        }

        .theme-dark .client-elite-shell .client-pro-chip,
        .dark .client-elite-shell .client-pro-chip {
            border-color: rgb(234 179 8 / 0.12);
            background:
                linear-gradient(165deg, rgb(15 23 42 / 0.72), rgb(15 23 42 / 0.54));
            box-shadow:
                0 20px 34px -28px rgb(2 8 23 / 0.84),
                inset 0 1px 0 rgb(255 255 255 / 0.04);
        }

        .client-elite-shell .client-pro-chip-title {
            color: rgb(120 53 15 / 0.9);
        }

        .theme-dark .client-elite-shell .client-pro-chip-title,
        .dark .client-elite-shell .client-pro-chip-title {
            color: rgb(253 224 71 / 0.82);
        }

        .client-elite-shell .client-pro-actions {
            gap: 0.55rem;
            align-items: center;
            padding-top: 0.72rem;
            border-top: 1px solid rgb(234 179 8 / 0.18);
        }

        .theme-dark .client-elite-shell .client-pro-actions,
        .dark .client-elite-shell .client-pro-actions {
            border-top-color: rgb(234 179 8 / 0.12);
        }

        .client-elite-shell .client-pro-actions .ui-button {
            min-height: 2.6rem;
            border-radius: 0.98rem;
            box-shadow: 0 16px 28px -24px rgb(15 23 42 / 0.32);
        }

        .client-elite-shell .client-pro-actions .ui-button:first-child {
            border-color: rgb(234 179 8 / 0.42);
            background: linear-gradient(135deg, rgb(250 204 21), rgb(16 185 129));
            color: rgb(6 23 18);
            box-shadow: 0 20px 36px -24px rgb(16 185 129 / 0.38);
        }

        .theme-dark .client-elite-shell .client-pro-actions .ui-button:first-child,
        .dark .client-elite-shell .client-pro-actions .ui-button:first-child {
            color: rgb(4 12 16);
        }

        .client-elite-shell .client-pro-actions .ui-button:not(:first-child) {
            background: rgb(255 255 255 / 0.54);
            border-color: rgb(234 179 8 / 0.14);
        }

        .theme-dark .client-elite-shell .client-pro-actions .ui-button:not(:first-child),
        .dark .client-elite-shell .client-pro-actions .ui-button:not(:first-child) {
            background: rgb(15 23 42 / 0.42);
            border-color: rgb(234 179 8 / 0.12);
        }

        .client-elite-shell .client-pro-insights {
            display: none;
        }

        @media (max-width: 900px) {
            .client-elite-shell .client-pro-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .client-elite-shell .client-pro-metrics {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .client-detail-shell {
            --client-sticky-offset: 5.7rem;
            position: relative;
            scroll-padding-top: calc(var(--client-sticky-offset) + 0.9rem);
        }

        .client-hero-card,
        .client-tab-panel,
        .client-tabs-wrap {
            scroll-margin-top: calc(var(--client-sticky-offset) + 0.8rem);
        }

        .client-tabs-wrap {
            position: sticky;
            top: calc(var(--client-sticky-offset) - 0.35rem);
            z-index: 18;
            border-radius: 1.05rem;
            border: 1px solid rgb(148 163 184 / 0.3);
            background: linear-gradient(145deg, rgb(248 250 252 / 0.95), rgb(241 245 249 / 0.92));
            box-shadow: 0 20px 32px -28px rgb(15 23 42 / 0.5);
            backdrop-filter: blur(7px);
        }

        .theme-dark .client-tabs-wrap {
            border-color: rgb(148 163 184 / 0.18);
            background: linear-gradient(150deg, rgb(15 23 42 / 0.9), rgb(2 6 23 / 0.8));
            box-shadow: 0 22px 36px -30px rgb(2 8 23 / 0.9);
        }

        .client-tabs-strip {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .client-tabs-strip::-webkit-scrollbar {
            display: none;
        }

        .client-tab-chip {
            min-height: 2.7rem;
            white-space: nowrap;
            border-radius: 0.95rem;
        }

        .client-tab-chip-active {
            box-shadow: 0 14px 24px -16px rgb(14 165 233 / 0.85);
        }

        .client-tab-chip-idle {
            opacity: 0.95;
        }

        .client-tab-chip-idle:hover {
            opacity: 1;
        }

        .client-tab-panel .ui-card {
            border: 1px solid rgb(148 163 184 / 0.28);
            background: linear-gradient(156deg, rgb(255 255 255 / 0.96), rgb(248 250 252 / 0.94));
            box-shadow: 0 22px 38px -34px rgb(15 23 42 / 0.56);
            backdrop-filter: blur(8px);
        }

        .theme-dark .client-tab-panel .ui-card,
        .dark .client-tab-panel .ui-card {
            border-color: rgb(148 163 184 / 0.18);
            background: linear-gradient(160deg, rgb(2 6 23 / 0.86), rgb(15 23 42 / 0.7));
            box-shadow: 0 24px 40px -30px rgb(2 8 23 / 0.88);
        }

        .client-tab-panel .ui-card > header {
            margin-bottom: 0.95rem;
        }

        .client-tab-panel .ui-card > header .ui-heading {
            letter-spacing: -0.025em;
        }

        .client-tab-panel .ui-card > header .ui-muted {
            margin-top: 0.3rem;
            font-size: 0.86rem;
            max-width: 42rem;
        }

        .client-tab-panel .ui-table thead tr {
            background: rgb(241 245 249 / 0.94);
            border-bottom-color: rgb(203 213 225 / 0.8);
        }

        .theme-dark .client-tab-panel .ui-table thead tr {
            background: rgb(51 65 85 / 0.88);
            border-bottom-color: rgb(71 85 105 / 0.9);
        }

        .client-tab-panel .ui-table th {
            font-size: 0.69rem;
            letter-spacing: 0.11em;
        }

        .client-tab-panel .ui-table th,
        .client-tab-panel .ui-table td {
            padding-top: 0.86rem;
            padding-bottom: 0.86rem;
        }

        .client-empty-state {
            border-style: dashed !important;
            border-color: rgb(148 163 184 / 0.52) !important;
            background: linear-gradient(160deg, rgb(248 250 252 / 0.95), rgb(241 245 249 / 0.84)) !important;
        }

        .theme-dark .client-empty-state {
            border-color: rgb(100 116 139 / 0.62) !important;
            background: linear-gradient(160deg, rgb(15 23 42 / 0.85), rgb(2 6 23 / 0.75)) !important;
        }

        .client-action-popover {
            border: 1px solid rgb(148 163 184 / 0.28);
            background: rgb(255 255 255 / 0.92);
            box-shadow: 0 20px 34px -24px rgb(15 23 42 / 0.62);
            backdrop-filter: blur(8px);
            padding: 0.38rem;
        }

        .theme-dark .client-action-popover {
            border-color: rgb(71 85 105 / 0.74);
            background: rgb(2 6 23 / 0.93);
            box-shadow: 0 22px 38px -26px rgb(2 8 23 / 0.92);
        }

        .client-action-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            border-radius: 0.78rem;
            border: 1px solid rgb(148 163 184 / 0.28);
            background: rgb(248 250 252 / 0.82);
            padding: 0.6rem 0.72rem;
            text-align: left;
            font-size: 0.79rem;
            font-weight: 700;
            color: rgb(15 23 42 / 0.96);
            transition: border-color 120ms ease, background-color 120ms ease, transform 120ms ease;
        }

        .client-action-item:hover {
            border-color: rgb(14 165 233 / 0.55);
            background: rgb(224 242 254 / 0.85);
            transform: translateY(-1px);
        }

        .theme-dark .client-action-item {
            border-color: rgb(71 85 105 / 0.72);
            background: rgb(15 23 42 / 0.8);
            color: rgb(226 232 240 / 0.94);
        }

        .theme-dark .client-action-item:hover {
            border-color: rgb(34 211 238 / 0.6);
            background: rgb(8 47 73 / 0.55);
        }

        .client-credentials-toolbar {
            margin-bottom: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .client-credentials-hero {
            border-radius: 1rem;
            border: 1px solid rgb(34 211 238 / 0.32);
            background: linear-gradient(155deg, rgb(6 182 212 / 0.13), rgb(14 116 144 / 0.06));
            padding: 0.95rem;
            gap: 0.9rem;
        }

        .theme-dark .client-credentials-hero {
            border-color: rgb(34 211 238 / 0.25);
            background: linear-gradient(165deg, rgb(6 182 212 / 0.16), rgb(8 47 73 / 0.22));
        }

        .client-credentials-actions {
            display: grid;
            gap: 0.45rem;
            grid-template-columns: repeat(auto-fit, minmax(9.8rem, 1fr));
        }

        .client-layout-wide {
            display: grid;
            gap: 1.4rem;
        }

        @media (min-width: 1536px) {
            .client-layout-wide {
                grid-template-columns: minmax(0, 1fr) minmax(18rem, 23rem);
                align-items: start;
            }
        }

        .client-tab-panel {
            animation: clientFade .16s ease-out;
        }

        @keyframes clientFade {
            from {
                opacity: 0;
                transform: translateY(3px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 640px) {
            .client-hero-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .client-control-priority-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .client-pro-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 639px) {
            .client-control-actions .ui-button {
                width: 100%;
            }

            .client-pro-actions .ui-button {
                width: 100%;
            }
        }

        @media (min-width: 1280px) {
            .client-detail-shell {
                --client-sticky-offset: 6.35rem;
            }

            .client-hero-layout {
                grid-template-columns: minmax(0, 1fr) 18rem;
                align-items: start;
            }

            .client-hero-actions {
                grid-template-columns: 1fr;
            }

            .client-hero-status {
                justify-content: flex-end;
            }

            .client-control-grid {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: start;
            }

            .client-pro-metrics {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 1279px) {
            .client-detail-shell {
                --client-sticky-offset: 7.1rem;
            }
        }

        @media (max-width: 900px) {
            .client-detail-shell {
                --client-sticky-offset: 7.7rem;
            }
        }

        @media (max-width: 639px) {
            .client-detail-shell {
                --client-sticky-offset: 8.2rem;
            }

            .client-action-popover {
                left: 0;
                right: 0;
                width: auto;
            }

            .client-tabs-wrap {
                position: static;
                top: auto;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $photoPath = trim((string) ($client->photo_path ?? ''));
        $photoUrl = null;
        if ($photoPath !== '') {
            if (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://')) {
                $photoUrl = $photoPath;
            } elseif (str_starts_with($photoPath, 'storage/') || str_starts_with($photoPath, '/storage/')) {
                $photoUrl = url('/'.ltrim($photoPath, '/'));
            } else {
                $photoUrl = url('/storage/'.ltrim($photoPath, '/'));
            }
        }

        $today = now()->startOfDay();
        $lastAttendance = $client->attendances->first();
        $lastAttendanceLabel = 'Sin asistencia';
        if ($lastAttendance?->date) {
            $attDate = $lastAttendance->date->copy()->startOfDay();
            $attTimeRaw = trim((string) ($lastAttendance->time ?? ''));
            $attTime = $attTimeRaw !== '' ? mb_substr($attTimeRaw, 0, 5) : '--:--';
            if ($attDate->isSameDay($today)) {
                $lastAttendanceLabel = 'Hoy '.$attTime;
            } else {
                $daysAgo = $attDate->diffInDays($today);
                if ($daysAgo <= 30) {
                    $lastAttendanceLabel = ($daysAgo === 1 ? 'Hace 1 día' : "Hace {$daysAgo} días").' '.$attTime;
                } else {
                    $lastAttendanceLabel = $attDate->translatedFormat('d M Y').' '.$attTime;
                }
            }
        }

        $statusLabels = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'expired' => 'Vencido',
            'cancelled' => 'Cancelado',
            'scheduled' => 'Programada',
        ];

        $adjustmentTypeLabels = [
            'reschedule_start' => 'Mover fecha de inicio',
            'extend_access' => 'Sumar días al final',
            'manual_window' => 'Corregir fechas manualmente',
        ];

        $adjustmentTypeHelp = [
            'reschedule_start' => 'Mueve la fecha de inicio y recalcula el fin con la duración normal del plan.',
            'extend_access' => 'Agrega días al final sin cambiar la fecha de inicio.',
            'manual_window' => 'Corrige inicio y fin manualmente solo para casos excepcionales o administrativos.',
        ];

        $adjustmentReasonLabels = [
            'payment_registered_late' => 'Pago recibido antes del registro',
            'future_start_requested' => 'Inicio acordado para otra fecha',
            'grace_period' => 'Prórroga o permiso temporal',
            'administrative_correction' => 'Corrección administrativa',
            'owner_exception' => 'Excepción autorizada por el dueño',
        ];

        $adjustmentReasonHelp = [
            'payment_registered_late' => 'Cuando el cliente pagó antes y recién lo estás registrando o moviendo.',
            'future_start_requested' => 'Cuando el cliente pidió empezar después de la fecha de cobro.',
            'grace_period' => 'Cuando vas a regalar o prorrogar días adicionales al final.',
            'administrative_correction' => 'Cuando corriges un error interno de fechas o registro.',
            'owner_exception' => 'Cuando el dueño autorizó una excepción fuera del flujo normal.',
        ];

        $adjustmentReasonMap = [
            'reschedule_start' => ['payment_registered_late', 'future_start_requested', 'administrative_correction'],
            'extend_access' => ['grace_period', 'administrative_correction', 'owner_exception'],
            'manual_window' => ['future_start_requested', 'administrative_correction', 'owner_exception'],
        ];

        $adjustmentReasonOptions = collect($adjustmentReasonLabels)
            ->map(fn (string $label, string $value): array => [
                'value' => $value,
                'label' => $label,
                'help' => $adjustmentReasonHelp[$value] ?? '',
            ])
            ->values()
            ->all();

        $latestMembershipStartsAt = $latestMembership?->starts_at?->copy()->startOfDay();
        $latestMembershipEndsAt = $latestMembership?->ends_at?->copy()->startOfDay();
        $daysLeft = $latestMembershipEndsAt
            ? $today->diffInDays($latestMembershipEndsAt, false)
            : null;
        $daysUntilStart = $latestMembershipStartsAt
            ? $today->diffInDays($latestMembershipStartsAt, false)
            : null;
        $isCancelledMembership = $latestMembership && (string) $latestMembership->status === 'cancelled';
        $isScheduledMembership = $latestMembershipStartsAt !== null
            && $latestMembershipStartsAt->greaterThan($today)
            && ! $isCancelledMembership;
        $isExpiredMembership = $latestMembership
            && ($isCancelledMembership || $latestMembershipEndsAt === null || $latestMembershipEndsAt->lt($today));

        $membershipBadgeVariant = 'muted';
        $membershipBadgeText = 'Sin membresía';
        $membershipLabel = 'Sin membresía';
        $membershipDateLabel = 'Vence';
        $membershipDateValue = 'N/A';
        $membershipCountdownLabel = 'Restan';
        $membershipCountdownValue = 'N/A';
        $membershipStartsLabel = $latestMembershipStartsAt?->translatedFormat('d M Y') ?? 'N/A';
        $membershipEndsLabel = $latestMembershipEndsAt?->translatedFormat('d M Y') ?? 'N/A';
        $remainingLabel = 'N/A';

        if ($latestMembership) {
            if ($isScheduledMembership) {
                $membershipBadgeVariant = 'info';
                $membershipBadgeText = 'Programada';
                $membershipLabel = 'Pendiente de iniciar';
                $membershipDateLabel = 'Inicia';
                $membershipDateValue = $membershipStartsLabel;
                $membershipCountdownLabel = 'Ventana';
                $membershipCountdownValue = $membershipStartsLabel.' -> '.$membershipEndsLabel;
                $remainingLabel = $daysUntilStart === null
                    ? 'Pendiente'
                    : ($daysUntilStart === 0 ? 'Inicia hoy' : 'Empieza en '.$daysUntilStart.' días');
            } elseif (! $isExpiredMembership && $daysLeft !== null && $daysLeft <= 7) {
                $membershipBadgeVariant = 'warning';
                $membershipBadgeText = 'Por vencer';
                $membershipLabel = 'Vigente';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = $daysLeft === 0 ? 'Hoy' : $daysLeft.' días';
                $remainingLabel = $membershipCountdownValue;
            } elseif (! $isExpiredMembership) {
                $membershipBadgeVariant = 'success';
                $membershipBadgeText = 'Vigente';
                $membershipLabel = 'Vigente';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = $daysLeft === null ? 'N/A' : ($daysLeft === 0 ? 'Hoy' : $daysLeft.' días');
                $remainingLabel = $membershipCountdownValue;
            } elseif ($isCancelledMembership) {
                $membershipBadgeVariant = 'danger';
                $membershipBadgeText = 'Cancelada';
                $membershipLabel = 'Cancelada';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = 'Sin acceso';
                $remainingLabel = $membershipCountdownValue;
            } else {
                $membershipBadgeVariant = 'danger';
                $membershipBadgeText = 'Vencida';
                $membershipLabel = 'Vencida';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = $daysLeft === null
                    ? 'Vencida'
                    : (abs($daysLeft) === 0 ? 'Venció hoy' : 'Hace '.abs($daysLeft).' días');
                $remainingLabel = $membershipCountdownValue;
            }
        }

        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $attendanceMethodLabels = [
            'document' => 'Documento',
            'rfid' => 'RFID',
            'qr' => 'QR',
        ];

        $membershipFormMode = trim((string) old('membership_form_mode', ''));
        $membershipErrorKeys = ['plan_id', 'starts_at', 'status', 'payment_method', 'payment_received_at', 'promotion_id', 'cash'];
        $membershipAdjustmentErrorKeys = ['membership_adjustment', 'adjustment_type', 'reason', 'notes', 'extra_days', 'starts_at', 'ends_at'];
        $rfidErrorKeys = ['rfid', 'value'];
        $appAccountErrorKeys = ['app_username', 'app_password', 'app_password_confirmation'];

        $openMembershipModal = (bool) old('_open_membership_modal', false)
            || $membershipFormMode === 'create'
            || ($errors->hasAny($membershipErrorKeys) && $membershipFormMode !== 'adjustment');
        $openAdjustmentModal = ! empty($canAdjustMemberships) && (
            (bool) old('_open_adjust_membership_modal', false)
            || $membershipFormMode === 'adjustment'
            || ($errors->hasAny($membershipAdjustmentErrorKeys) && $membershipFormMode === 'adjustment')
        );
        $openRfidModal = $errors->hasAny($rfidErrorKeys);
        $openAppAccountTab = ! empty($canManageClientAccounts) && (
            $errors->hasAny($appAccountErrorKeys) || old('active_tab') === 'app_access'
        );

        $progressTabUrl = null;
        if (! empty($canShowProgress)) {
            $progressTabUrlParams = [
                'client' => $client->id,
                'tab' => 'progress',
            ];
            $contextGym = trim((string) request()->route('contextGym'));
            if ($contextGym !== '') {
                $progressTabUrlParams['contextGym'] = $contextGym;
            }
            $pwaMode = strtolower(trim((string) request()->query('pwa_mode', '')));
            if ($pwaMode === 'standalone') {
                $progressTabUrlParams['pwa_mode'] = 'standalone';
            }
            $progressTabUrl = route('clients.show', $progressTabUrlParams);
        }

        $initialTab = 'summary';
        $allowedTabs = ['summary', 'membership', 'attendance', 'credentials'];
        if (! empty($canShowProgress)) {
            $allowedTabs[] = 'progress';
        }
        if (! empty($canManageClientAccounts)) {
            $allowedTabs[] = 'app_access';
        }

        $requestedTab = trim((string) old('active_tab', request()->query('tab', '')));
        if (in_array($requestedTab, $allowedTabs, true)) {
            $initialTab = $requestedTab;
        }

        if ($openMembershipModal || $openAdjustmentModal) {
            $initialTab = 'membership';
        }
        if ($openRfidModal) {
            $initialTab = 'credentials';
        }
        if ($openAppAccountTab) {
            $initialTab = 'app_access';
        }

        $attendancePreview = $client->attendances->take(4);
        $paymentsPreview = $recentMembershipPayments->take(4);

        $adjustmentMemberships = $client->memberships
            ->map(function ($membership): array {
                return [
                    'id' => (int) $membership->id,
                    'planName' => (string) ($membership->plan?->name ?? 'Sin plan'),
                    'status' => (string) ($membership->status ?? ''),
                    'startsAt' => $membership->starts_at?->toDateString(),
                    'endsAt' => $membership->ends_at?->toDateString(),
                    'bonusDays' => (int) ($membership->bonus_days ?? 0),
                    'durationUnit' => (string) ($membership->plan?->duration_unit ?? 'days'),
                    'durationDays' => (int) ($membership->plan?->duration_days ?? 30),
                    'durationMonths' => $membership->plan?->duration_months !== null
                        ? (int) $membership->plan->duration_months
                        : null,
                    'adjustUrl' => route('memberships.adjust', ['membership' => $membership->id]),
                ];
            })
            ->values();

        $initialAdjustmentMembershipId = old('adjust_membership_id');
        if ($initialAdjustmentMembershipId === null && $latestMembership) {
            $initialAdjustmentMembershipId = (int) $latestMembership->id;
        }

        $oldAdjustmentInput = [
            'adjustmentType' => (string) old('adjustment_type', 'reschedule_start'),
            'reason' => (string) old('reason', 'payment_registered_late'),
            'startsAt' => (string) old('starts_at', ''),
            'endsAt' => (string) old('ends_at', ''),
            'extraDays' => (string) old('extra_days', ''),
            'notes' => (string) old('notes', ''),
        ];

        $hasAdjustmentOldInput = $membershipFormMode === 'adjustment'
            && (
                old('adjustment_type') !== null
                || old('reason') !== null
                || old('starts_at') !== null
                || old('ends_at') !== null
                || old('extra_days') !== null
                || old('notes') !== null
            );
        $contextGym = trim((string) request()->route('contextGym'));
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $clientRouteParams = ['contextGym' => $contextGym, 'client' => $client->id] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $moduleRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $clientReportRouteParams = [
            'contextGym' => $contextGym,
            'search' => $client->document_number,
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $planControlClientDashboard = is_array($planControlClientDashboard ?? null) ? $planControlClientDashboard : null;
        $professionalClientDashboard = is_array($professionalClientDashboard ?? null) ? $professionalClientDashboard : null;
        $premiumClientDashboard = is_array($premiumClientDashboard ?? null) ? $premiumClientDashboard : null;
    @endphp

    <div x-data="clientShowPage({
            initialTab: @js($initialTab),
            openMembershipModal: @js($openMembershipModal),
            openAdjustmentModal: @js($openAdjustmentModal),
            openRfidModal: @js($openRfidModal),
            adjustmentMemberships: @js($adjustmentMemberships),
            adjustmentTypeHelp: @js($adjustmentTypeHelp),
            adjustmentReasonOptions: @js($adjustmentReasonOptions),
            adjustmentReasonMap: @js($adjustmentReasonMap),
            initialAdjustmentMembershipId: @js($initialAdjustmentMembershipId ? (int) $initialAdjustmentMembershipId : null),
            membershipDefaults: @js([
                'currentPlanId' => $professionalClientDashboard['current_plan_id'] ?? ($latestMembership?->plan_id !== null ? (int) $latestMembership->plan_id : null),
                'suggestedPromotionId' => $professionalClientDashboard['suggested_promotion_id'] ?? null,
            ]),
            oldAdjustmentInput: @js($oldAdjustmentInput),
            hasAdjustmentOldInput: @js($hasAdjustmentOldInput),
         })"
         x-init="init()"
         class="client-detail-shell space-y-4 sm:space-y-6">

        @include('clients.partials._header', [
            'client' => $client,
            'photoUrl' => $photoUrl,
            'membershipBadgeVariant' => $membershipBadgeVariant,
            'membershipBadgeText' => $membershipBadgeText,
            'membershipLabel' => $membershipLabel,
            'membershipDateLabel' => $membershipDateLabel,
            'membershipDateValue' => $membershipDateValue,
            'membershipCountdownLabel' => $membershipCountdownLabel,
            'membershipCountdownValue' => $membershipCountdownValue,
            'lastAttendanceLabel' => $lastAttendanceLabel,
            'canAdjustMemberships' => $canAdjustMemberships,
            'latestMembership' => $latestMembership,
            'progressTabUrl' => $progressTabUrl,
            'canShowProgress' => $canShowProgress,
        ])

        @if ($planControlClientDashboard)
            <section class="client-control-shell">
                <div class="client-control-grid">
                    <div class="client-control-copy">
                        <span class="client-control-kicker">Plan Control / Ficha</span>
                        <h2 class="client-control-heading">{{ $planControlClientDashboard['headline'] ?? 'Ficha lista para cobrar y operar' }}</h2>
                        <p class="client-control-summary">{{ $planControlClientDashboard['summary'] ?? 'Usa esta vista para revisar estado, ultimo cobro y asistencia sin perder limpieza visual.' }}</p>
                    </div>

                    <div class="client-control-actions">
                        <x-ui.button type="button" variant="primary" x-on:click="{{ ! empty($planControlClientDashboard['current_plan_id']) ? 'openRenewalModal()' : 'openMembershipModal()' }}">
                            {{ $planControlClientDashboard['renewal_action_label'] ?? 'Cobrar membresia' }}
                        </x-ui.button>
                        <x-ui.button :href="route('reception.index', $moduleRouteParams)" variant="secondary">Ir a recepcion</x-ui.button>
                        <x-ui.button :href="route('cash.index', $moduleRouteParams)" variant="ghost">Ir a caja</x-ui.button>
                        @if (! empty($canViewReports))
                            <x-ui.button :href="route('reports.client-earnings', $clientReportRouteParams)" variant="ghost">Reporte del cliente</x-ui.button>
                        @endif
                    </div>
                </div>

                <div class="client-control-priority-grid mt-4">
                    <article class="client-control-priority" data-tone="{{ $planControlClientDashboard['status_tone'] ?? 'neutral' }}">
                        <p class="client-control-priority-label">Estado de membresia</p>
                        <p class="client-control-priority-value">{{ $planControlClientDashboard['status_value'] ?? 'Sin membresia' }}</p>
                        <p class="client-control-priority-note">{{ $planControlClientDashboard['status_note'] ?? '' }}</p>
                    </article>

                    <article class="client-control-priority" data-tone="success">
                        <p class="client-control-priority-label">Ultimo cobro</p>
                        <p class="client-control-priority-value">{{ \App\Support\Currency::format((float) ($planControlClientDashboard['last_payment_amount'] ?? 0), $appCurrencyCode) }}</p>
                        <p class="client-control-priority-note">{{ $planControlClientDashboard['last_payment_label'] ?? 'Sin cobro registrado.' }}</p>
                    </article>

                    <article class="client-control-priority" data-tone="{{ $planControlClientDashboard['attendance_tone'] ?? 'neutral' }}">
                        <p class="client-control-priority-label">Ultima asistencia</p>
                        <p class="client-control-priority-value">{{ $planControlClientDashboard['attendance_value'] ?? 'Sin registro' }}</p>
                        <p class="client-control-priority-note">{{ $planControlClientDashboard['attendance_note'] ?? '' }}</p>
                    </article>
                </div>
            </section>
        @endif

        @if ($professionalClientDashboard)
            @php
                $professionalClientAlerts = collect($professionalClientDashboard['alerts'] ?? [])->values();
                $professionalClientMetrics = [
                    [
                        'label' => 'Ultimo cobro',
                        'value' => \App\Support\Currency::format((float) ($professionalClientDashboard['last_payment_amount'] ?? 0), $appCurrencyCode),
                        'note' => $professionalClientDashboard['last_payment_label'] ?? 'Sin cobro registrado',
                        'tone' => 'success',
                    ],
                    [
                        'label' => 'Facturacion membresias',
                        'value' => \App\Support\Currency::format((float) ($professionalClientDashboard['total_membership_revenue'] ?? 0), $appCurrencyCode),
                        'note' => 'Historial acumulado de pagos de membresia.',
                        'tone' => 'info',
                    ],
                    [
                        'label' => 'Promo',
                        'value' => $professionalClientDashboard['promotion_title'] ?? 'Sin promo',
                        'note' => $professionalClientDashboard['promotion_subtitle'] ?? '',
                        'tone' => 'accent',
                    ],
                    [
                        'label' => 'Productos',
                        'value' => \App\Support\Currency::format((float) ($professionalClientDashboard['product_sales_revenue'] ?? 0), $appCurrencyCode),
                        'note' => (int) ($professionalClientDashboard['product_sales_count'] ?? 0).' ticket(s) | '.($professionalClientDashboard['last_product_sale_label'] ?? 'Sin ventas'),
                        'tone' => 'warning',
                    ],
                ];
            @endphp
            <section class="client-pro-shell">
                <div class="client-pro-grid">
                    <div class="client-elite-head flex flex-wrap items-start justify-between gap-3">
                        <div class="client-pro-copy">
                            <span class="client-pro-kicker">Plan Profesional / Cliente</span>
                            <h2 class="client-pro-heading">{{ $professionalClientDashboard['headline'] ?? 'Foco comercial del cliente' }}</h2>
                            <p class="client-pro-summary">{{ $professionalClientDashboard['summary'] ?? 'Renovacion, promo y venta adicional en una sola lectura compacta.' }}</p>
                        </div>
                        <span class="client-pro-badge">Profesional</span>
                    </div>

                    <div class="client-pro-metrics">
                        @foreach ($professionalClientMetrics as $metric)
                            <article class="client-pro-metric" data-tone="{{ $metric['tone'] }}">
                                <p class="client-pro-metric-label">{{ $metric['label'] }}</p>
                                <p class="client-pro-metric-value">{{ $metric['value'] }}</p>
                                <p class="client-pro-metric-note">{{ $metric['note'] }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="client-pro-insights">
                        <article class="client-pro-chip" data-tone="info">
                            <p class="client-pro-chip-title">Asistencia</p>
                            <p class="client-pro-chip-copy">{{ $professionalClientDashboard['attendance_label'] ?? 'Sin asistencia registrada' }}</p>
                        </article>
                        @foreach ($professionalClientAlerts as $alert)
                            <article class="client-pro-chip" data-tone="{{ $alert['tone'] ?? 'info' }}">
                                <p class="client-pro-chip-title">{{ $alert['title'] ?? 'Alerta' }}</p>
                                <p class="client-pro-chip-copy">{{ $alert['description'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="client-pro-actions">
                        <x-ui.button type="button" variant="primary" x-on:click="openRenewalModal()">
                            {{ ! empty($professionalClientDashboard['current_plan_id']) ? 'Renovar mismo plan' : 'Cobrar membresia' }}
                        </x-ui.button>
                        @if (! empty($professionalClientDashboard['suggested_promotion_id']))
                            <x-ui.button type="button" variant="secondary" x-on:click="openPromotionRenewalModal()">Renovar con promo</x-ui.button>
                        @elseif (! empty($canUseSalesInventory))
                            <x-ui.button :href="route('sales.index', $moduleRouteParams)" variant="secondary">Registrar venta</x-ui.button>
                        @endif
                        @if (! empty($canViewReports))
                            <x-ui.button :href="route('reports.client-earnings', $clientReportRouteParams)" variant="ghost">Reporte del cliente</x-ui.button>
                        @endif
                        @if (! empty($canManagePromotions))
                            <x-ui.button :href="route('plans.index', $moduleRouteParams)" variant="ghost">Planes y promos</x-ui.button>
                        @elseif (! empty($canShowProgress) && ! empty($progressTabUrl))
                            <x-ui.button :href="$progressTabUrl" variant="ghost">Ver progreso</x-ui.button>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if ($premiumClientDashboard)
            @php
                $premiumClientMetrics = [
                    [
                        'label' => 'Acceso app',
                        'value' => $premiumClientDashboard['app_access_value'] ?? 'Pendiente',
                        'note' => $premiumClientDashboard['app_access_note'] ?? 'Usuario listo o pendiente.',
                        'tone' => ! empty($premiumClientDashboard['app_access_ready']) ? 'success' : 'warning',
                    ],
                    [
                        'label' => 'Ultimo cobro',
                        'value' => \App\Support\Currency::format((float) ($premiumClientDashboard['last_payment_amount'] ?? 0), $appCurrencyCode),
                        'note' => $premiumClientDashboard['last_payment_label'] ?? 'Sin cobro registrado',
                        'tone' => 'info',
                    ],
                    [
                        'label' => 'Facturacion',
                        'value' => \App\Support\Currency::format((float) ($premiumClientDashboard['total_membership_revenue'] ?? 0), $appCurrencyCode),
                        'note' => 'Total acumulado de membresia.',
                        'tone' => 'accent',
                    ],
                ];
            @endphp
            <section class="client-pro-shell client-elite-shell">
                <div class="client-pro-grid">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="client-pro-copy">
                            <span class="client-pro-kicker">Plan Elite / Cliente</span>
                            <h2 class="client-pro-heading">Estado premium del cliente</h2>
                            <p class="client-pro-summary">Acceso app y cobro en un resumen corto.</p>
                        </div>
                        <span class="client-pro-badge">Premium</span>
                    </div>

                    <div class="client-pro-metrics">
                        @foreach ($premiumClientMetrics as $metric)
                            <article class="client-pro-metric" data-tone="{{ $metric['tone'] }}">
                                <p class="client-pro-metric-label">{{ $metric['label'] }}</p>
                                <p class="client-pro-metric-value">{{ $metric['value'] }}</p>
                                <p class="client-pro-metric-note">{{ $metric['note'] }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="client-pro-actions">
                        @if (! empty($premiumClientDashboard['app_access_ready']) && ! empty($canManageClientAccounts) && \Illuminate\Support\Facades\Route::has('client-portal.index'))
                            <x-ui.button :href="route('client-portal.index', $moduleRouteParams)" variant="primary">Portal cliente</x-ui.button>
                        @elseif (! empty($canManageClientAccounts))
                            <x-ui.button :href="route('clients.show', array_merge($clientRouteParams, ['tab' => 'app_access']))" variant="primary">Configurar acceso app</x-ui.button>
                        @endif
                        @if (! empty($canViewReports))
                            <x-ui.button :href="route('reports.client-earnings', $clientReportRouteParams)" variant="secondary">Reporte del cliente</x-ui.button>
                        @endif
                        @if (! empty($canShowProgress) && ! empty($progressTabUrl))
                            <x-ui.button :href="$progressTabUrl" variant="ghost">Ver progreso</x-ui.button>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @include('clients.partials._tabs', [
            'canShowProgress' => $canShowProgress,
        ])

        <section x-cloak x-show="activeTab === 'summary'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_summary', [
                'client' => $client,
                'photoUrl' => $photoUrl,
                'latestMembership' => $latestMembership,
                'membershipBadgeVariant' => $membershipBadgeVariant,
                'membershipBadgeText' => $membershipBadgeText,
                'membershipDateLabel' => $membershipDateLabel,
                'membershipDateValue' => $membershipDateValue,
                'membershipCountdownLabel' => $membershipCountdownLabel,
                'membershipCountdownValue' => $membershipCountdownValue,
                'membershipStartsLabel' => $membershipStartsLabel,
                'membershipEndsLabel' => $membershipEndsLabel,
                'remainingLabel' => $remainingLabel,
                'lastAttendanceLabel' => $lastAttendanceLabel,
                'attendancePreview' => $attendancePreview,
                'paymentsPreview' => $paymentsPreview,
                'methodLabels' => $methodLabels,
                'statusLabels' => $statusLabels,
                'canAdjustMemberships' => $canAdjustMemberships,
                'progressTabUrl' => $progressTabUrl,
            ])
        </section>

        @if (! empty($canShowProgress))
            <section x-cloak x-show="activeTab === 'progress'" x-transition.opacity class="client-tab-panel">
                @include('clients.partials._tab_progress', [
                    'client' => $client,
                    'progressOverview' => $progressOverview,
                    'canManageClientAccounts' => $canManageClientAccounts,
                ])
            </section>
        @endif

        <section x-cloak x-show="activeTab === 'membership'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_membership_payments', [
                'client' => $client,
                'latestMembership' => $latestMembership,
                'recentMembershipPayments' => $recentMembershipPayments,
                'membershipAdjustments' => $membershipAdjustments,
                'methodLabels' => $methodLabels,
                'statusLabels' => $statusLabels,
                'adjustmentTypeLabels' => $adjustmentTypeLabels,
                'adjustmentReasonLabels' => $adjustmentReasonLabels,
                'canAdjustMemberships' => $canAdjustMemberships,
            ])
        </section>

        <section x-cloak x-show="activeTab === 'attendance'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_attendances', [
                'client' => $client,
                'attendanceMethodLabels' => $attendanceMethodLabels,
            ])
        </section>

        <section x-cloak x-show="activeTab === 'credentials'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_credentials', [
                'client' => $client,
                'activeQrCredential' => $activeQrCredential,
                'activeQrSvg' => $activeQrSvg,
            ])
        </section>

        @if (! empty($canManageClientAccounts))
            <section x-cloak x-show="activeTab === 'app_access'" x-transition.opacity class="client-tab-panel">
                @include('clients.partials._tab_usuario_app', [
                    'client' => $client,
                ])
            </section>
        @endif

        @include('clients.partials._modal_membership', [
            'client' => $client,
            'plans' => $plans,
            'promotions' => $promotions,
            'canManagePromotions' => $canManagePromotions ?? false,
        ])

        @if (! empty($canAdjustMemberships))
            @include('clients.partials._modal_membership_adjustment', [
                'client' => $client,
                'adjustmentTypeLabels' => $adjustmentTypeLabels,
                'adjustmentReasonLabels' => $adjustmentReasonLabels,
            ])
        @endif

        @include('clients.partials._modal_rfid', [
            'client' => $client,
        ])

        @include('clients.partials._modal_confirm')
    </div>
@endsection

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        window.clientShowPage = function clientShowPage(config) {
            return {
                activeTab: config.initialTab || 'summary',
                actionsOpen: false,
                quickMoreOpen: false,
                membershipModalOpen: Boolean(config.openMembershipModal),
                adjustmentModalOpen: Boolean(config.openAdjustmentModal),
                rfidModalOpen: Boolean(config.openRfidModal),
                confirmOpen: false,
                confirmMessage: '',
                pendingDeactivateFormId: null,
                qrCopyFeedback: '',
                whatsappCopyFeedback: '',
                adjustmentMemberships: Array.isArray(config.adjustmentMemberships) ? config.adjustmentMemberships : [],
                adjustmentTypeHelp: config.adjustmentTypeHelp || {},
                adjustmentReasonOptions: Array.isArray(config.adjustmentReasonOptions) ? config.adjustmentReasonOptions : [],
                adjustmentReasonMap: config.adjustmentReasonMap || {},
                membershipDefaults: config.membershipDefaults || {},
                selectedAdjustmentMembershipId: config.initialAdjustmentMembershipId || null,
                hasAdjustmentOldInput: Boolean(config.hasAdjustmentOldInput),
                adjustmentForm: {
                    type: config.oldAdjustmentInput?.adjustmentType || 'reschedule_start',
                    reason: config.oldAdjustmentInput?.reason || 'payment_registered_late',
                    startsAt: config.oldAdjustmentInput?.startsAt || '',
                    endsAt: config.oldAdjustmentInput?.endsAt || '',
                    extraDays: config.oldAdjustmentInput?.extraDays || '',
                    notes: config.oldAdjustmentInput?.notes || '',
                },

                init() {
                    if (!this.selectedAdjustmentMembershipId && this.adjustmentMemberships.length > 0) {
                        this.selectedAdjustmentMembershipId = this.adjustmentMemberships[0].id;
                    }

                    if (this.adjustmentMemberships.length > 0) {
                        this.setSelectedAdjustmentMembership(
                            this.selectedAdjustmentMembershipId,
                            this.hasAdjustmentOldInput
                        );
                    }

                    this.syncAdjustmentReason();

                    if (this.membershipModalOpen) {
                        this.$nextTick(() => this.$refs.membershipPlanInput?.focus());
                    }

                    if (this.adjustmentModalOpen) {
                        this.$nextTick(() => this.$refs.adjustmentTypeInput?.focus());
                    }

                    if (this.rfidModalOpen) {
                        this.$nextTick(() => this.$refs.rfidValueInput?.focus());
                    }
                },

                setTab(tab) {
                    this.activeTab = tab;
                    this.actionsOpen = false;
                },

                openMembershipModal() {
                    this.membershipModalOpen = true;
                    this.actionsOpen = false;
                    this.activeTab = 'membership';
                    this.$nextTick(() => this.$refs.membershipPlanInput?.focus());
                },

                openRenewalModal() {
                    this.openMembershipModalWithDefaults({
                        planId: this.membershipDefaults.currentPlanId || null,
                        promotionId: '',
                    });
                },

                openPromotionRenewalModal() {
                    this.openMembershipModalWithDefaults({
                        planId: this.membershipDefaults.currentPlanId || null,
                        promotionId: this.membershipDefaults.suggestedPromotionId || '',
                    });
                },

                openMembershipModalWithDefaults(options = {}) {
                    this.membershipModalOpen = true;
                    this.actionsOpen = false;
                    this.activeTab = 'membership';
                    this.$nextTick(() => {
                        if (Object.prototype.hasOwnProperty.call(options, 'planId') && options.planId && this.$refs.membershipPlanInput) {
                            this.$refs.membershipPlanInput.value = String(options.planId);
                        }

                        if (this.$refs.membershipPromotionInput) {
                            const promotionValue = Object.prototype.hasOwnProperty.call(options, 'promotionId')
                                ? String(options.promotionId || '')
                                : '';
                            this.$refs.membershipPromotionInput.value = promotionValue;
                        }

                        this.$refs.membershipPlanInput?.focus();
                    });
                },

                closeMembershipModal() {
                    this.membershipModalOpen = false;
                },

                openMembershipAdjustmentModal(membershipId = null) {
                    if (membershipId !== null) {
                        this.setSelectedAdjustmentMembership(membershipId, false);
                    } else if (!this.selectedAdjustmentMembershipId && this.adjustmentMemberships.length > 0) {
                        this.setSelectedAdjustmentMembership(this.adjustmentMemberships[0].id, false);
                    }

                    this.adjustmentModalOpen = true;
                    this.actionsOpen = false;
                    this.activeTab = 'membership';
                    this.syncAdjustmentReason();
                    this.$nextTick(() => this.$refs.adjustmentTypeInput?.focus());
                },

                closeMembershipAdjustmentModal() {
                    this.adjustmentModalOpen = false;
                },

                setSelectedAdjustmentMembership(membershipId, preserveValues = false) {
                    if (membershipId === null || membershipId === undefined || membershipId === '') {
                        return;
                    }

                    const membership = this.adjustmentMemberships.find((item) => String(item.id) === String(membershipId));
                    if (!membership) {
                        return;
                    }

                    this.selectedAdjustmentMembershipId = membership.id;

                    if (!preserveValues) {
                        this.adjustmentForm.startsAt = membership.startsAt || '';
                        this.adjustmentForm.endsAt = membership.endsAt || '';
                        this.adjustmentForm.extraDays = '';
                        this.adjustmentForm.notes = '';
                        this.adjustmentForm.type = 'reschedule_start';
                    } else if (!this.adjustmentForm.startsAt) {
                        this.adjustmentForm.startsAt = membership.startsAt || '';
                    }

                    this.syncAdjustmentReason();
                },

                selectedAdjustmentMembership() {
                    return this.adjustmentMemberships.find((item) => String(item.id) === String(this.selectedAdjustmentMembershipId)) || null;
                },

                adjustmentFormAction() {
                    return this.selectedAdjustmentMembership()?.adjustUrl || '#';
                },

                allowedAdjustmentReasons() {
                    const type = String(this.adjustmentForm.type || '').trim();
                    const allowedValues = Array.isArray(this.adjustmentReasonMap[type]) ? this.adjustmentReasonMap[type] : [];

                    return this.adjustmentReasonOptions.filter((option) => allowedValues.includes(option.value));
                },

                currentAdjustmentTypeHelp() {
                    const type = String(this.adjustmentForm.type || '').trim();
                    return String(this.adjustmentTypeHelp[type] || '');
                },

                currentAdjustmentReasonHelp() {
                    const reason = String(this.adjustmentForm.reason || '').trim();
                    const option = this.adjustmentReasonOptions.find((item) => item.value === reason);

                    return option?.help || '';
                },

                syncAdjustmentReason() {
                    const allowedReasons = this.allowedAdjustmentReasons();
                    const currentReason = String(this.adjustmentForm.reason || '').trim();
                    if (allowedReasons.length === 0) {
                        this.adjustmentForm.reason = '';
                        return;
                    }

                    if (!allowedReasons.some((option) => option.value === currentReason)) {
                        this.adjustmentForm.reason = allowedReasons[0].value;
                    }
                },

                adjustmentPreview() {
                    const membership = this.selectedAdjustmentMembership();
                    if (!membership) {
                        return {
                            startsAt: '',
                            endsAt: '',
                            statusLabel: 'Sin membresía seleccionada',
                            deltaLabel: 'Sin cambios',
                            planLabel: 'Selecciona una membresía para continuar.',
                        };
                    }

                    let startsAt = membership.startsAt || '';
                    let endsAt = membership.endsAt || '';

                    if (this.adjustmentForm.type === 'reschedule_start') {
                        startsAt = this.adjustmentForm.startsAt || startsAt;
                        endsAt = startsAt ? this.calculatePlanEnd(startsAt, membership) : endsAt;
                    } else if (this.adjustmentForm.type === 'extend_access') {
                        const extraDays = Math.max(0, Number(this.adjustmentForm.extraDays || 0));
                        endsAt = this.addDaysToInput(endsAt, extraDays);
                    } else if (this.adjustmentForm.type === 'manual_window') {
                        startsAt = this.adjustmentForm.startsAt || startsAt;
                        endsAt = this.adjustmentForm.endsAt || endsAt;
                    }

                    return {
                        startsAt,
                        endsAt,
                        statusLabel: this.resolveWindowStatus(startsAt, endsAt, membership.status),
                        deltaLabel: this.buildDaysDeltaLabel(membership.endsAt || '', endsAt),
                        planLabel: membership.planName || 'Sin plan',
                    };
                },

                calculatePlanEnd(startsAt, membership) {
                    const startDate = this.parseDate(startsAt);
                    if (!startDate) {
                        return '';
                    }

                    const bonusDays = Math.max(0, Number(membership.bonusDays || 0));
                    if ((membership.durationUnit || 'days') === 'months') {
                        const months = Math.max(1, Number(membership.durationMonths || 1));
                        const endDate = this.addMonthsNoOverflow(startDate, months);
                        if (bonusDays > 0) {
                            endDate.setDate(endDate.getDate() + bonusDays);
                        }

                        return this.formatDateInput(endDate);
                    }

                    const durationDays = Math.max(1, Number(membership.durationDays || 1));
                    const totalDays = durationDays + bonusDays - 1;
                    const endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                    endDate.setDate(endDate.getDate() + totalDays);

                    return this.formatDateInput(endDate);
                },

                resolveWindowStatus(startsAt, endsAt, rawStatus) {
                    if (rawStatus === 'cancelled') {
                        return 'Cancelada';
                    }

                    const today = this.todayInput();
                    if (startsAt && startsAt > today) {
                        return 'Programada';
                    }

                    if (!endsAt || endsAt < today) {
                        return 'Vencida';
                    }

                    return 'Vigente';
                },

                buildDaysDeltaLabel(previousEndsAt, nextEndsAt) {
                    const previous = this.parseDate(previousEndsAt);
                    const next = this.parseDate(nextEndsAt);
                    if (!previous || !next) {
                        return 'Sin cambios';
                    }

                    const millisecondsPerDay = 24 * 60 * 60 * 1000;
                    const delta = Math.round((next.getTime() - previous.getTime()) / millisecondsPerDay);
                    if (delta === 0) {
                        return 'Sin cambio neto';
                    }

                    return delta > 0 ? `+${delta} días` : `${delta} días`;
                },

                addDaysToInput(value, days) {
                    const baseDate = this.parseDate(value);
                    if (!baseDate) {
                        return '';
                    }

                    const nextDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate());
                    nextDate.setDate(nextDate.getDate() + days);

                    return this.formatDateInput(nextDate);
                },

                addMonthsNoOverflow(date, months) {
                    const baseYear = date.getFullYear();
                    const baseMonth = date.getMonth();
                    const baseDay = date.getDate();
                    const targetMonthIndex = baseMonth + months;
                    const targetYear = baseYear + Math.floor(targetMonthIndex / 12);
                    const targetMonth = ((targetMonthIndex % 12) + 12) % 12;
                    const lastDayOfTargetMonth = new Date(targetYear, targetMonth + 1, 0).getDate();
                    const safeDay = Math.min(baseDay, lastDayOfTargetMonth);

                    return new Date(targetYear, targetMonth, safeDay);
                },

                parseDate(value) {
                    const raw = String(value || '').trim();
                    if (!/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                        return null;
                    }

                    const [year, month, day] = raw.split('-').map(Number);
                    return new Date(year, month - 1, day);
                },

                formatDateInput(date) {
                    if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
                        return '';
                    }

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
                },

                formatDateLabel(value) {
                    const parsed = this.parseDate(value);
                    if (!parsed) {
                        return '-';
                    }

                    return parsed.toLocaleDateString('es-EC', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    });
                },

                todayInput() {
                    return this.formatDateInput(new Date());
                },

                openRfidModal() {
                    this.rfidModalOpen = true;
                    this.actionsOpen = false;
                    this.$nextTick(() => this.$refs.rfidValueInput?.focus());
                },

                closeRfidModal() {
                    this.rfidModalOpen = false;
                },

                requestDeactivate(formId, label) {
                    this.pendingDeactivateFormId = formId;
                    this.confirmMessage = 'Se desactivará ' + label + '. Esta acción no se puede deshacer desde esta pantalla.';
                    this.confirmOpen = true;
                },

                closeConfirm() {
                    this.confirmOpen = false;
                    this.pendingDeactivateFormId = null;
                },

                confirmDeactivate() {
                    if (!this.pendingDeactivateFormId) {
                        return;
                    }

                    const form = document.getElementById(this.pendingDeactivateFormId);
                    if (form) {
                        form.submit();
                    }
                },

                async copyQr(value) {
                    if (!value) {
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(value);
                        this.qrCopyFeedback = 'Valor QR copiado.';
                    } catch (error) {
                        this.qrCopyFeedback = 'No se pudo copiar automáticamente.';
                    }
                },

                async copyWhatsappMessage(value) {
                    if (!value) {
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(value);
                        this.whatsappCopyFeedback = 'Mensaje de WhatsApp copiado.';
                    } catch (error) {
                        this.whatsappCopyFeedback = 'No se pudo copiar el mensaje automáticamente.';
                    }
                },
            };
        };
    </script>
@endpush
