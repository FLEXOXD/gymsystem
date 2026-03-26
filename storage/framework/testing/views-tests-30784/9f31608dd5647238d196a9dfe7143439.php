<?php $__env->startSection('title', 'Clientes'); ?>
<?php $__env->startSection('page-title', 'Clientes'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .clients-page-shell {
            position: relative;
        }

        .clients-control-shell {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid rgb(163 230 53 / 0.22);
            border-radius: 1.22rem;
            background:
                radial-gradient(circle at top right, rgb(163 230 53 / 0.16), transparent 34%),
                linear-gradient(150deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
            box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.5);
            backdrop-filter: blur(14px);
            padding: 1.05rem;
        }

        .dark .clients-control-shell {
            border-color: rgb(163 230 53 / 0.24);
            background:
                radial-gradient(circle at top right, rgb(163 230 53 / 0.14), transparent 34%),
                linear-gradient(160deg, rgb(2 6 23 / 0.84), rgb(15 23 42 / 0.62));
            box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.92);
        }

        .clients-control-shell::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
            opacity: 0.8;
            pointer-events: none;
        }

        .clients-control-shell::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(95deg, transparent, rgb(163 230 53 / 0.05), transparent);
        }

        .clients-control-grid {
            display: grid;
            gap: 1.05rem;
            position: relative;
            z-index: 1;
        }

        .clients-control-copy {
            max-width: 48rem;
        }

        .clients-control-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.17em;
            text-transform: uppercase;
            color: rgb(77 124 15 / 0.94);
        }

        .dark .clients-control-kicker {
            color: rgb(217 249 157 / 0.94);
        }

        .clients-control-kicker::before {
            content: '';
            width: 0.52rem;
            height: 0.52rem;
            border-radius: 999px;
            background: rgb(132 204 22 / 0.94);
            box-shadow: 0 0 0 6px rgb(132 204 22 / 0.12);
        }

        .clients-control-heading {
            margin-top: 0.78rem;
            font-size: clamp(1.14rem, 1.85vw, 1.46rem);
            line-height: 1.08;
            letter-spacing: -0.035em;
            font-weight: 900;
            color: rgb(15 23 42 / 0.97);
        }

        .dark .clients-control-heading {
            color: rgb(241 245 249 / 0.98);
        }

        .clients-control-summary {
            margin-top: 0.5rem;
            font-size: 0.88rem;
            line-height: 1.58;
            color: rgb(71 85 105 / 0.92);
        }

        .dark .clients-control-summary {
            color: rgb(148 163 184 / 0.9);
        }

        .clients-control-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            align-items: center;
        }

        .clients-control-actions .ui-button {
            min-height: 2.72rem;
        }

        .clients-control-priority-grid {
            display: grid;
            gap: 0.75rem;
        }

        .clients-control-priority {
            position: relative;
            overflow: hidden;
            border-radius: 1.05rem;
            border: 1px solid rgb(148 163 184 / 0.24);
            background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
            box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
            min-height: 7rem;
            padding: 0.9rem 0.95rem;
        }

        .dark .clients-control-priority {
            border-color: rgb(148 163 184 / 0.18);
            background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
            box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
        }

        .clients-control-priority::before {
            content: '';
            position: absolute;
            left: 0.9rem;
            right: 0.9rem;
            top: 0;
            height: 2px;
            border-radius: 999px;
            background: rgb(148 163 184 / 0.22);
        }

        .clients-control-priority[data-tone='warning']::before {
            background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
        }

        .clients-control-priority[data-tone='success']::before {
            background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
        }

        .clients-control-priority[data-tone='info']::before {
            background: linear-gradient(90deg, rgb(6 182 212 / 0.9), rgb(6 182 212 / 0.24));
        }

        .clients-control-priority-label {
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgb(71 85 105 / 0.92);
        }

        .dark .clients-control-priority-label {
            color: rgb(148 163 184 / 0.9);
        }

        .clients-control-priority-value {
            margin-top: 0.42rem;
            font-size: 1.46rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: rgb(15 23 42 / 0.97);
        }

        .dark .clients-control-priority-value {
            color: rgb(248 250 252 / 0.98);
        }

        .clients-control-priority-note {
            margin-top: 0.4rem;
            font-size: 0.75rem;
            line-height: 1.45;
            color: rgb(71 85 105 / 0.9);
        }

        .dark .clients-control-priority-note {
            color: rgb(148 163 184 / 0.88);
        }

        .clients-pro-shell {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid rgb(34 211 238 / 0.2);
            border-radius: 1.22rem;
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.12), transparent 34%),
                radial-gradient(circle at bottom left, rgb(245 158 11 / 0.1), transparent 28%),
                linear-gradient(150deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
            box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.5);
            backdrop-filter: blur(14px);
            padding: 1.05rem;
        }

        .dark .clients-pro-shell {
            border-color: rgb(34 211 238 / 0.22);
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.11), transparent 34%),
                radial-gradient(circle at bottom left, rgb(245 158 11 / 0.08), transparent 28%),
                linear-gradient(155deg, rgb(4 10 28 / 0.94), rgb(11 18 32 / 0.88));
            box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.9);
        }

        .clients-pro-shell::before {
            content: '';
            position: absolute;
            inset: 0 0 auto;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
            opacity: 0.8;
            pointer-events: none;
        }

        .clients-pro-shell::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(95deg, transparent, rgb(34 211 238 / 0.04), transparent);
        }

        .clients-pro-grid {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 1rem;
        }

        .clients-pro-copy {
            max-width: 50rem;
        }

        .clients-pro-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.17em;
            text-transform: uppercase;
            color: rgb(8 145 178 / 0.96);
        }

        .dark .clients-pro-kicker {
            color: rgb(165 243 252 / 0.94);
        }

        .clients-pro-kicker::before {
            content: '';
            width: 0.52rem;
            height: 0.52rem;
            border-radius: 999px;
            background: rgb(34 211 238 / 0.96);
            box-shadow: 0 0 0 6px rgb(34 211 238 / 0.14);
        }

        .clients-pro-heading {
            margin-top: 0.78rem;
            font-size: clamp(1.14rem, 1.85vw, 1.46rem);
            line-height: 1.08;
            letter-spacing: -0.035em;
            font-weight: 900;
            color: rgb(15 23 42 / 0.97);
        }

        .dark .clients-pro-heading {
            color: rgb(241 245 249 / 0.98);
        }

        .clients-pro-summary {
            margin-top: 0.5rem;
            font-size: 0.88rem;
            line-height: 1.58;
            color: rgb(71 85 105 / 0.92);
        }

        .dark .clients-pro-summary {
            color: rgb(148 163 184 / 0.9);
        }

        .clients-pro-badge {
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

        .dark .clients-pro-badge {
            border-color: rgb(34 211 238 / 0.26);
            background: rgb(8 145 178 / 0.12);
            color: rgb(165 243 252 / 0.95);
        }

        .clients-pro-metrics {
            display: grid;
            gap: 0.75rem;
        }

        .clients-pro-metric {
            position: relative;
            overflow: hidden;
            border-radius: 1.02rem;
            border: 1px solid rgb(148 163 184 / 0.24);
            background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
            box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
            min-height: 6.7rem;
            padding: 0.9rem 0.95rem;
        }

        .dark .clients-pro-metric {
            border-color: rgb(148 163 184 / 0.16);
            background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
            box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
        }

        .clients-pro-metric::before {
            content: '';
            position: absolute;
            left: 0.9rem;
            right: 0.9rem;
            top: 0;
            height: 2px;
            border-radius: 999px;
            background: rgb(148 163 184 / 0.22);
        }

        .clients-pro-metric[data-tone='warning']::before {
            background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
        }

        .clients-pro-metric[data-tone='danger']::before {
            background: linear-gradient(90deg, rgb(244 63 94 / 0.9), rgb(244 63 94 / 0.24));
        }

        .clients-pro-metric[data-tone='info']::before {
            background: linear-gradient(90deg, rgb(34 211 238 / 0.9), rgb(34 211 238 / 0.24));
        }

        .clients-pro-metric[data-tone='accent']::before {
            background: linear-gradient(90deg, rgb(168 85 247 / 0.9), rgb(168 85 247 / 0.24));
        }

        .clients-pro-metric-label {
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgb(71 85 105 / 0.92);
        }

        .dark .clients-pro-metric-label {
            color: rgb(148 163 184 / 0.9);
        }

        .clients-pro-metric-value {
            margin-top: 0.42rem;
            font-size: 1.46rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: rgb(15 23 42 / 0.97);
        }

        .dark .clients-pro-metric-value {
            color: rgb(248 250 252 / 0.98);
        }

        .clients-pro-metric-note {
            margin-top: 0.4rem;
            font-size: 0.75rem;
            line-height: 1.45;
            color: rgb(71 85 105 / 0.9);
        }

        .dark .clients-pro-metric-note {
            color: rgb(148 163 184 / 0.88);
        }

        .clients-pro-insights {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .clients-pro-chip {
            min-width: min(100%, 14rem);
            flex: 1 1 14rem;
            border-radius: 0.95rem;
            border: 1px solid rgb(148 163 184 / 0.2);
            background: rgb(255 255 255 / 0.66);
            padding: 0.78rem 0.85rem;
            box-shadow: 0 16px 28px -30px rgb(15 23 42 / 0.32);
        }

        .dark .clients-pro-chip {
            border-color: rgb(148 163 184 / 0.14);
            background: rgb(15 23 42 / 0.58);
            box-shadow: 0 20px 30px -30px rgb(2 8 23 / 0.9);
        }

        .clients-pro-chip[data-tone='warning'] {
            border-color: rgb(245 158 11 / 0.22);
            background: rgb(255 251 235 / 0.9);
        }

        .clients-pro-chip[data-tone='danger'] {
            border-color: rgb(244 63 94 / 0.2);
            background: rgb(255 241 242 / 0.9);
        }

        .clients-pro-chip[data-tone='success'] {
            border-color: rgb(16 185 129 / 0.22);
            background: rgb(236 253 245 / 0.9);
        }

        .clients-pro-chip[data-tone='info'] {
            border-color: rgb(34 211 238 / 0.22);
            background: rgb(236 254 255 / 0.9);
        }

        .dark .clients-pro-chip[data-tone='warning'] {
            background: rgb(120 53 15 / 0.18);
        }

        .dark .clients-pro-chip[data-tone='danger'] {
            background: rgb(127 29 29 / 0.18);
        }

        .dark .clients-pro-chip[data-tone='success'] {
            background: rgb(6 78 59 / 0.18);
        }

        .dark .clients-pro-chip[data-tone='info'] {
            background: rgb(8 145 178 / 0.14);
        }

        .clients-pro-chip-title {
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgb(30 41 59 / 0.92);
        }

        .dark .clients-pro-chip-title {
            color: rgb(226 232 240 / 0.96);
        }

        .clients-pro-chip-copy {
            margin-top: 0.35rem;
            font-size: 0.76rem;
            line-height: 1.45;
            color: rgb(71 85 105 / 0.9);
        }

        .dark .clients-pro-chip-copy {
            color: rgb(148 163 184 / 0.88);
        }

        .clients-pro-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            align-items: center;
        }

        .clients-pro-actions .ui-button {
            min-height: 2.72rem;
        }

        .clients-elite-shell.clients-pro-shell {
            border-color: rgb(234 179 8 / 0.24);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.18), transparent 36%),
                radial-gradient(circle at bottom left, rgb(16 185 129 / 0.1), transparent 30%),
                linear-gradient(148deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.97));
            box-shadow:
                0 34px 68px -44px rgb(120 53 15 / 0.24),
                inset 0 1px 0 rgb(255 255 255 / 0.9);
            padding: 1.2rem;
        }

        .dark .clients-elite-shell.clients-pro-shell {
            border-color: rgb(234 179 8 / 0.28);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.16), transparent 36%),
                radial-gradient(circle at bottom left, rgb(16 185 129 / 0.11), transparent 30%),
                linear-gradient(155deg, rgb(10 12 24 / 0.96), rgb(17 24 39 / 0.92));
            box-shadow:
                0 36px 72px -46px rgb(2 8 23 / 0.92),
                inset 0 1px 0 rgb(255 255 255 / 0.05);
        }

        .clients-elite-shell.clients-pro-shell::after {
            background: linear-gradient(100deg, transparent 8%, rgb(234 179 8 / 0.08), transparent 74%);
        }

        .clients-elite-shell .clients-pro-grid {
            gap: 0.82rem;
        }

        .clients-elite-shell .clients-elite-head {
            align-items: end;
            gap: 1rem;
        }

        .clients-elite-shell .clients-pro-copy {
            max-width: 48rem;
        }

        .clients-elite-shell .clients-pro-kicker {
            color: rgb(161 98 7 / 0.96);
            letter-spacing: 0.15em;
        }

        .dark .clients-elite-shell .clients-pro-kicker {
            color: rgb(253 224 71 / 0.94);
        }

        .clients-elite-shell .clients-pro-kicker::before {
            background: rgb(234 179 8 / 0.96);
            box-shadow: 0 0 0 6px rgb(234 179 8 / 0.14);
        }

        .clients-elite-shell .clients-pro-badge {
            border-color: rgb(234 179 8 / 0.24);
            background: rgb(254 249 195 / 0.84);
            color: rgb(161 98 7 / 0.96);
            padding: 0.48rem 0.92rem;
            box-shadow: 0 14px 30px -24px rgb(161 98 7 / 0.28);
        }

        .dark .clients-elite-shell .clients-pro-badge {
            border-color: rgb(234 179 8 / 0.26);
            background: rgb(161 98 7 / 0.12);
            color: rgb(253 224 71 / 0.95);
            box-shadow: 0 14px 32px -24px rgb(234 179 8 / 0.2);
        }

        .clients-elite-shell .clients-pro-heading {
            margin-top: 0.42rem;
            max-width: 24ch;
            font-size: clamp(1.08rem, 1.55vw, 1.36rem);
            line-height: 1.04;
        }

        .clients-elite-shell .clients-pro-summary {
            max-width: 36rem;
            margin-top: 0.38rem;
            font-size: 0.82rem;
            line-height: 1.42;
            color: rgb(71 85 105 / 0.96);
        }

        .dark .clients-elite-shell .clients-pro-summary {
            color: rgb(203 213 225 / 0.82);
        }

        .clients-elite-shell .clients-pro-metrics {
            gap: 0.68rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .clients-elite-shell .clients-pro-metric {
            min-height: auto;
            padding: 0.82rem 0.92rem;
            border-color: rgb(234 179 8 / 0.16);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.94), rgb(248 250 252 / 0.82));
            box-shadow:
                0 20px 34px -28px rgb(120 53 15 / 0.14),
                inset 0 1px 0 rgb(255 255 255 / 0.86);
        }

        .dark .clients-elite-shell .clients-pro-metric {
            border-color: rgb(234 179 8 / 0.14);
            background:
                linear-gradient(165deg, rgb(15 23 42 / 0.82), rgb(15 23 42 / 0.62));
            box-shadow:
                0 22px 38px -30px rgb(2 8 23 / 0.9),
                inset 0 1px 0 rgb(255 255 255 / 0.04);
        }

        .clients-elite-shell .clients-pro-metric:first-child {
            border-color: rgb(234 179 8 / 0.26);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.18), transparent 44%),
                linear-gradient(180deg, rgb(255 251 235 / 0.96), rgb(255 255 255 / 0.84));
            grid-column: span 1;
        }

        .dark .clients-elite-shell .clients-pro-metric:first-child {
            border-color: rgb(234 179 8 / 0.24);
            background:
                radial-gradient(circle at top right, rgb(234 179 8 / 0.15), transparent 44%),
                linear-gradient(165deg, rgb(31 41 55 / 0.9), rgb(15 23 42 / 0.74));
        }

        .clients-elite-shell .clients-pro-metric-label {
            color: rgb(120 53 15 / 0.86);
        }

        .dark .clients-elite-shell .clients-pro-metric-label {
            color: rgb(253 224 71 / 0.8);
        }

        .clients-elite-shell .clients-pro-metric-value {
            margin-top: 0.42rem;
            font-size: clamp(1.32rem, 2vw, 1.72rem);
            line-height: 1;
        }

        .clients-elite-shell .clients-pro-chip {
            border-color: rgb(234 179 8 / 0.16);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.84), rgb(255 255 255 / 0.7));
            box-shadow:
                0 18px 30px -28px rgb(120 53 15 / 0.12),
                inset 0 1px 0 rgb(255 255 255 / 0.82);
        }

        .dark .clients-elite-shell .clients-pro-chip {
            border-color: rgb(234 179 8 / 0.12);
            background:
                linear-gradient(165deg, rgb(15 23 42 / 0.72), rgb(15 23 42 / 0.54));
            box-shadow:
                0 20px 34px -28px rgb(2 8 23 / 0.84),
                inset 0 1px 0 rgb(255 255 255 / 0.04);
        }

        .clients-elite-shell .clients-pro-chip-title {
            color: rgb(120 53 15 / 0.9);
        }

        .dark .clients-elite-shell .clients-pro-chip-title {
            color: rgb(253 224 71 / 0.82);
        }

        .clients-elite-shell .clients-pro-actions {
            gap: 0.55rem;
            align-items: center;
            padding-top: 0.72rem;
            border-top: 1px solid rgb(234 179 8 / 0.18);
        }

        .dark .clients-elite-shell .clients-pro-actions {
            border-top-color: rgb(234 179 8 / 0.12);
        }

        .clients-elite-shell .clients-pro-actions .ui-button {
            min-height: 2.6rem;
            border-radius: 0.98rem;
            box-shadow: 0 16px 28px -24px rgb(15 23 42 / 0.32);
        }

        .clients-elite-shell .clients-pro-actions .ui-button:first-child {
            border-color: rgb(234 179 8 / 0.42);
            background: linear-gradient(135deg, rgb(250 204 21), rgb(16 185 129));
            color: rgb(6 23 18);
            box-shadow: 0 20px 36px -24px rgb(16 185 129 / 0.38);
        }

        .dark .clients-elite-shell .clients-pro-actions .ui-button:first-child {
            color: rgb(4 12 16);
        }

        .clients-elite-shell .clients-pro-actions .ui-button:not(:first-child) {
            background: rgb(255 255 255 / 0.54);
            border-color: rgb(234 179 8 / 0.14);
        }

        .dark .clients-elite-shell .clients-pro-actions .ui-button:not(:first-child) {
            background: rgb(15 23 42 / 0.42);
            border-color: rgb(234 179 8 / 0.12);
        }

        .clients-elite-shell .clients-pro-insights {
            display: none;
        }

        @media (max-width: 900px) {
            .clients-elite-shell .clients-pro-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .clients-elite-shell .clients-pro-metrics {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (min-width: 1280px) {
            .clients-page-shell {
                padding-left: 0.35rem;
                padding-right: 0.5rem;
            }

            .clients-control-grid {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: start;
            }

            .clients-pro-metrics {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (min-width: 768px) {
            .clients-control-priority-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .clients-pro-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .clients-control-actions .ui-button {
                width: 100%;
            }

            .clients-pro-actions .ui-button {
                width: 100%;
            }
        }

        .clients-kpi-grid {
            gap: 0.85rem;
        }

        .clients-kpi-card {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid rgb(148 163 184 / 0.28);
            background: linear-gradient(150deg, rgb(248 250 252 / 0.95), rgb(241 245 249 / 0.88));
            box-shadow: 0 18px 30px -24px rgb(15 23 42 / 0.38);
            padding: 1rem;
        }

        .dark .clients-kpi-card {
            border-color: rgb(148 163 184 / 0.2);
            background: linear-gradient(145deg, rgb(15 23 42 / 0.88), rgb(15 23 42 / 0.64));
            box-shadow: 0 20px 40px -30px rgb(2 8 23 / 0.92);
        }

        .clients-kpi-card::before {
            content: '';
            position: absolute;
            left: 0.8rem;
            right: 0.8rem;
            top: 0;
            height: 2px;
            border-radius: 999px;
            background: rgb(148 163 184 / 0.36);
        }

        .clients-kpi-card[data-tone='success']::before {
            background: linear-gradient(90deg, rgb(16 185 129 / 0.95), rgb(16 185 129 / 0.3));
        }

        .clients-kpi-card[data-tone='warning']::before {
            background: linear-gradient(90deg, rgb(245 158 11 / 0.95), rgb(245 158 11 / 0.3));
        }

        .clients-kpi-card[data-tone='danger']::before {
            background: linear-gradient(90deg, rgb(244 63 94 / 0.95), rgb(244 63 94 / 0.3));
        }

        .clients-kpi-label {
            font-size: 0.69rem;
            font-weight: 800;
            letter-spacing: 0.17em;
            text-transform: uppercase;
            color: rgb(71 85 105 / 0.95);
        }

        .dark .clients-kpi-label {
            color: rgb(148 163 184 / 0.88);
        }

        .clients-kpi-value {
            margin-top: 0.4rem;
            font-size: clamp(2rem, 2.7vw, 2.35rem);
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: rgb(15 23 42 / 0.96);
        }

        .dark .clients-kpi-value {
            color: rgb(241 245 249 / 0.97);
        }

        .clients-kpi-note {
            margin-top: 0.4rem;
            font-size: 0.74rem;
            color: rgb(71 85 105 / 0.9);
        }

        .dark .clients-kpi-note {
            color: rgb(148 163 184 / 0.9);
        }

        .clients-main-card {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid rgb(148 163 184 / 0.28);
            background: linear-gradient(155deg, rgb(255 255 255 / 0.95), rgb(248 250 252 / 0.94));
            box-shadow: 0 24px 46px -38px rgb(15 23 42 / 0.48);
        }

        .clients-main-card::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at top right, rgb(14 165 233 / 0.08), transparent 28%),
                radial-gradient(circle at bottom left, rgb(16 185 129 / 0.06), transparent 24%);
            opacity: 0.9;
        }

        .dark .clients-main-card {
            border-color: rgb(148 163 184 / 0.2);
            background: linear-gradient(165deg, rgb(2 6 23 / 0.8), rgb(15 23 42 / 0.52));
            box-shadow: 0 24px 48px -34px rgb(2 8 23 / 0.9);
        }

        .clients-main-card > header .ui-heading {
            font-size: clamp(1.3rem, 1.6vw, 1.62rem);
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .clients-main-card > header .ui-muted {
            margin-top: 0.35rem;
            font-size: 0.9rem;
        }

        .clients-main-card > * {
            position: relative;
            z-index: 1;
        }

        .clients-toolbar-shell {
            padding: 1rem;
            border: 1px solid rgb(148 163 184 / 0.22);
            border-radius: 1.05rem;
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.82),
                0 18px 32px -32px rgb(15 23 42 / 0.26);
        }

        .clients-search-form {
            align-items: stretch;
        }

        .clients-search-input {
            min-height: 2.85rem;
            border-radius: 0.92rem;
        }

        .clients-toolbar-button,
        .clients-create-button {
            min-height: 2.85rem;
            border-radius: 0.92rem;
        }

        .clients-filter-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.35rem;
            padding: 0.62rem 0.92rem;
            border-radius: 999px;
            border: 1px solid rgb(148 163 184 / 0.24);
            background: rgb(255 255 255 / 0.78);
            color: rgb(51 65 85 / 0.9);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: border-color 140ms ease, background-color 140ms ease, color 140ms ease, box-shadow 140ms ease, transform 140ms ease;
        }

        .clients-filter-chip:hover {
            border-color: rgb(56 189 248 / 0.38);
            background: rgb(240 249 255 / 0.92);
            color: rgb(12 74 110 / 0.96);
            transform: translateY(-1px);
        }

        .clients-filter-chip.is-active {
            border-color: rgb(14 165 233 / 0.34);
            background:
                linear-gradient(135deg, rgb(14 165 233 / 0.2), rgb(16 185 129 / 0.18));
            color: rgb(15 23 42 / 0.96);
            box-shadow: 0 16px 28px -24px rgb(14 165 233 / 0.62);
        }

        .clients-table-wrap {
            border-color: rgb(148 163 184 / 0.3);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.96), rgb(248 250 252 / 0.94));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.82),
                0 24px 40px -34px rgb(15 23 42 / 0.16);
        }

        .dark .clients-table-wrap {
            border-color: rgb(148 163 184 / 0.16);
            background:
                linear-gradient(180deg, rgb(15 23 42 / 0.94), rgb(15 23 42 / 0.9));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.03),
                0 28px 42px -38px rgb(2 8 23 / 0.62);
        }

        .clients-table-scroll {
            max-height: 560px;
            overflow-x: auto;
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-gutter: stable both-edges;
        }

        .clients-table-scroll .ui-table {
            min-width: 1200px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .clients-table-wrap .ui-table thead tr {
            background: transparent;
            border-bottom-color: transparent;
        }

        .dark .clients-table-wrap .ui-table thead tr {
            background: transparent;
            border-bottom-color: transparent;
        }

        .clients-table-wrap .ui-table th {
            position: sticky;
            top: 0;
            z-index: 6;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.13em;
            color: rgb(71 85 105 / 0.94);
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgb(226 232 240 / 0.78);
            background:
                linear-gradient(180deg, rgb(248 250 252 / 0.98), rgb(241 245 249 / 0.95));
            backdrop-filter: blur(10px);
        }

        .dark .clients-table-wrap .ui-table th {
            color: rgb(148 163 184 / 0.96);
            border-bottom-color: rgb(51 65 85 / 0.78);
            background:
                linear-gradient(180deg, rgb(15 23 42 / 0.96), rgb(15 23 42 / 0.92));
        }

        .clients-table-wrap .ui-table td {
            padding-top: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgb(226 232 240 / 0.72);
            vertical-align: middle;
        }

        .dark .clients-table-wrap .ui-table td {
            border-bottom-color: rgb(51 65 85 / 0.54);
        }

        .clients-table-wrap .ui-table tbody tr {
            transition: background-color 140ms ease, box-shadow 140ms ease, transform 140ms ease;
        }

        .clients-table-wrap .ui-table tbody tr:hover td {
            background: rgb(14 165 233 / 0.06);
            border-bottom-color: rgb(56 189 248 / 0.26);
        }

        .dark .clients-table-wrap .ui-table tbody tr:hover td {
            background: rgb(30 41 59 / 0.9);
            border-bottom-color: rgb(56 189 248 / 0.24);
        }

        .clients-table-wrap .ui-table tbody tr:hover td:first-child {
            border-top-left-radius: 0.88rem;
            border-bottom-left-radius: 0.88rem;
        }

        .clients-table-wrap .ui-table tbody tr:hover td:last-child {
            border-top-right-radius: 0.88rem;
            border-bottom-right-radius: 0.88rem;
        }

        .theme-light .clients-control-shell {
            border-color: rgb(163 230 53 / 0.18);
            background:
                radial-gradient(circle at top right, rgb(163 230 53 / 0.1), transparent 34%),
                linear-gradient(150deg, rgb(255 255 255 / 0.98), rgb(241 245 249 / 0.96));
            box-shadow: 0 28px 48px -40px rgb(15 23 42 / 0.12);
        }

        .theme-light .clients-pro-shell {
            border-color: rgb(34 211 238 / 0.16);
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.08), transparent 34%),
                radial-gradient(circle at bottom left, rgb(245 158 11 / 0.06), transparent 28%),
                linear-gradient(150deg, rgb(255 255 255 / 0.98), rgb(241 245 249 / 0.96));
            box-shadow: 0 28px 48px -40px rgb(15 23 42 / 0.12);
        }

        .theme-light .clients-control-priority,
        .theme-light .clients-pro-metric,
        .theme-light .clients-pro-chip,
        .theme-light .clients-kpi-card,
        .theme-light .clients-main-card {
            border-color: rgb(203 213 225 / 0.82);
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.84),
                0 18px 30px -30px rgb(15 23 42 / 0.11);
        }

        .theme-light .clients-main-card > header .ui-muted,
        .theme-light .clients-kpi-note,
        .theme-light .clients-client-meta {
            color: rgb(71 85 105 / 0.92);
        }

        .theme-light .clients-toolbar-shell,
        .theme-light .clients-table-footer {
            border-color: rgb(203 213 225 / 0.82);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.94));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.9),
                0 18px 30px -32px rgb(15 23 42 / 0.1);
        }

        .theme-light .clients-filter-chip {
            border-color: rgb(203 213 225 / 0.86);
            background: rgb(255 255 255 / 0.94);
            color: rgb(51 65 85 / 0.92);
        }

        .theme-light .clients-filter-chip.is-active {
            border-color: rgb(56 189 248 / 0.4);
            color: rgb(8 47 73 / 0.96);
        }

        .theme-light .clients-client-doc {
            color: rgb(51 65 85 / 0.94);
        }

        .theme-light .clients-client-avatar {
            border-color: rgb(255 255 255 / 0.9);
            box-shadow:
                0 14px 26px -22px rgb(14 165 233 / 0.22),
                0 0 0 1px rgb(203 213 225 / 0.55);
        }

        .theme-light .clients-table-wrap {
            border-color: rgb(203 213 225 / 0.82);
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.9),
                0 24px 38px -36px rgb(15 23 42 / 0.11);
        }

        .theme-dark .clients-control-shell,
        .dark .clients-control-shell {
            border-color: rgb(163 230 53 / 0.18);
            background:
                radial-gradient(circle at top right, rgb(163 230 53 / 0.08), transparent 34%),
                linear-gradient(160deg, rgb(9 16 32 / 0.96), rgb(15 23 42 / 0.88));
        }

        .theme-dark .clients-pro-shell,
        .dark .clients-pro-shell {
            border-color: rgb(34 211 238 / 0.18);
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.08), transparent 34%),
                radial-gradient(circle at bottom left, rgb(245 158 11 / 0.05), transparent 28%),
                linear-gradient(155deg, rgb(6 12 28 / 0.96), rgb(15 23 42 / 0.9));
        }

        .theme-dark .clients-main-card,
        .dark .clients-main-card,
        .theme-dark .clients-kpi-card,
        .dark .clients-kpi-card,
        .theme-dark .clients-table-wrap,
        .dark .clients-table-wrap {
            border-color: rgb(51 65 85 / 0.74);
        }

        .theme-dark .clients-toolbar-shell,
        .dark .clients-toolbar-shell,
        .theme-dark .clients-table-footer,
        .dark .clients-table-footer {
            border-color: rgb(51 65 85 / 0.74);
            background:
                linear-gradient(180deg, rgb(15 23 42 / 0.9), rgb(17 24 39 / 0.86));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.04),
                0 18px 30px -32px rgb(2 8 23 / 0.72);
        }

        .theme-dark .clients-filter-chip,
        .dark .clients-filter-chip {
            border-color: rgb(51 65 85 / 0.78);
            background: rgb(15 23 42 / 0.88);
            color: rgb(226 232 240 / 0.9);
        }

        .theme-dark .clients-filter-chip:hover,
        .dark .clients-filter-chip:hover {
            border-color: rgb(56 189 248 / 0.36);
            background: rgb(12 74 110 / 0.24);
            color: rgb(186 230 253 / 0.95);
        }

        .theme-dark .clients-filter-chip.is-active,
        .dark .clients-filter-chip.is-active {
            border-color: rgb(56 189 248 / 0.32);
            background: linear-gradient(135deg, rgb(8 47 73 / 0.88), rgb(6 95 70 / 0.74));
            color: rgb(240 249 255 / 0.98);
        }

        .theme-dark .clients-client-avatar,
        .dark .clients-client-avatar {
            border-color: rgb(51 65 85 / 0.92);
            box-shadow: 0 16px 28px -24px rgb(2 8 23 / 0.76);
        }

        .clients-client-doc {
            font-size: 0.76rem;
            font-weight: 700;
            color: rgb(71 85 105 / 0.95);
        }

        .clients-client-avatar {
            width: 2.8rem;
            height: 2.8rem;
            border-radius: 1rem;
            border: 1px solid rgb(255 255 255 / 0.58);
            box-shadow: 0 14px 26px -22px rgb(15 23 42 / 0.42);
            flex: 0 0 auto;
        }

        .dark .clients-client-doc {
            color: rgb(148 163 184 / 0.96);
        }

        .clients-client-meta {
            font-size: 0.7rem;
            color: rgb(100 116 139 / 0.93);
        }

        .clients-table-footer {
            margin-top: 1rem;
            padding: 0.9rem 1rem;
            border: 1px solid rgb(148 163 184 / 0.22);
            border-radius: 1rem;
            background:
                linear-gradient(180deg, rgb(255 255 255 / 0.78), rgb(248 250 252 / 0.74));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.82),
                0 16px 30px -32px rgb(15 23 42 / 0.24);
        }

        .clients-empty-state {
            padding-top: 2.3rem;
            padding-bottom: 2.3rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .dark .clients-client-meta {
            color: rgb(148 163 184 / 0.82);
        }

        .clients-row-actions {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            justify-content: flex-end;
            position: relative;
        }

        .clients-row-view {
            min-width: 6.8rem;
        }

        .clients-row-menu-toggle {
            min-height: 2.35rem;
            min-width: 2.35rem;
            padding: 0.45rem;
            border-radius: 0.75rem;
        }

        .clients-row-menu-icon {
            width: 1rem;
            height: 1rem;
            opacity: 0.82;
            flex: 0 0 auto;
        }

        .clients-row-menu {
            position: absolute;
            top: calc(100% + 0.4rem);
            right: 0;
            z-index: 35;
            min-width: 12rem;
            border-radius: 0.85rem;
            border: 1px solid rgb(148 163 184 / 0.35);
            background: rgb(255 255 255 / 0.94);
            box-shadow: 0 18px 36px -22px rgb(2 8 23 / 0.6);
            backdrop-filter: blur(6px);
            padding: 0.35rem;
        }

        .dark .clients-row-menu {
            border-color: rgb(148 163 184 / 0.24);
            background: rgb(2 6 23 / 0.92);
            box-shadow: 0 20px 40px -24px rgb(2 8 23 / 0.92);
        }

        .clients-row-menu-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            border-radius: 0.68rem;
            padding: 0.55rem 0.62rem;
            text-align: left;
            font-size: 0.76rem;
            font-weight: 700;
            color: rgb(15 23 42 / 0.96);
            transition: background-color 120ms ease, color 120ms ease;
        }

        .dark .clients-row-menu-item {
            color: rgb(226 232 240 / 0.95);
        }

        .clients-row-menu-item:hover {
            background: rgb(14 165 233 / 0.12);
            color: rgb(12 74 110 / 0.98);
        }

        .dark .clients-row-menu-item:hover {
            background: rgb(34 211 238 / 0.13);
            color: rgb(165 243 252 / 0.98);
        }

        .clients-row-menu-item.is-danger {
            color: rgb(190 18 60 / 0.95);
        }

        .dark .clients-row-menu-item.is-danger {
            color: rgb(251 113 133 / 0.98);
        }

        .clients-row-menu-item-icon {
            width: 0.95rem;
            height: 0.95rem;
            opacity: 0.78;
            flex: 0 0 auto;
        }

        .clients-row-menu-backdrop {
            display: none;
        }

        @media (max-width: 640px) {
            .clients-table-scroll {
                padding-bottom: 0.25rem;
            }

            .clients-table-scroll .ui-table {
                min-width: 1120px;
            }

            .clients-table-scroll.table-mobile-stack {
                overflow-y: auto;
                overflow-x: hidden;
                overscroll-behavior-y: contain;
                -webkit-overflow-scrolling: touch;
                padding-bottom: calc(4.5rem + env(safe-area-inset-bottom));
                scroll-padding-bottom: calc(4.5rem + env(safe-area-inset-bottom));
            }

            .clients-row-menu-backdrop {
                position: fixed;
                inset: 0;
                z-index: 84;
                display: block;
                background: rgb(15 23 42 / 0.28);
                backdrop-filter: blur(2px);
            }

            .clients-row-menu {
                position: fixed;
                left: 0.75rem;
                right: 0.75rem;
                top: auto;
                bottom: calc(5.6rem + env(safe-area-inset-bottom));
                z-index: 90;
                min-width: 0;
                max-height: min(18rem, calc(100dvh - 7rem));
                overflow-y: auto;
                border-radius: 1rem;
                padding: 0.45rem;
            }

            .clients-row-menu-item {
                min-height: 2.85rem;
                padding: 0.78rem 0.82rem;
                font-size: 0.82rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $canManagePromotions = (bool) ($canManagePromotions ?? false);
        $canManageClientAccounts = (bool) ($canManageClientAccounts ?? false);
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isOwner = (bool) (auth()->user()?->isOwner() ?? false);
        $contextGym = (string) request()->route('contextGym');
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $canUseSalesInventory = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'sales_inventory');
        $canViewReports = $isOwner && $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'reports_base');
        $clientsRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $planControlClientsDashboard = is_array($planControlClientsDashboard ?? null) ? $planControlClientsDashboard : null;
        $professionalClientsDashboard = is_array($professionalClientsDashboard ?? null) ? $professionalClientsDashboard : null;
        $premiumClientsDashboard = is_array($premiumClientsDashboard ?? null) ? $premiumClientsDashboard : null;
        $filters = [
            'all' => 'Todos',
            'active' => 'Activos',
            'expiring' => 'Por vencer',
            'expired' => 'Vencid@s',
            'attended_today' => 'Asistieron hoy',
        ];
        $baseFilterQuery = request()->query();
        unset($baseFilterQuery['page']);

        $planCatalog = $plans
            ->map(fn ($plan) => [
                'id' => (int) $plan->id,
                'name' => (string) $plan->name,
                'duration_days' => (int) $plan->duration_days,
                'duration_unit' => \App\Support\PlanDuration::normalizeUnit((string) ($plan->duration_unit ?? 'days')),
                'duration_months' => $plan->duration_months !== null ? (int) $plan->duration_months : null,
                'price' => (float) $plan->price,
            ])
            ->values();

        $promotionCatalog = ($promotions ?? collect())
            ->map(fn ($promotion) => [
                'id' => (int) $promotion->id,
                'plan_id' => $promotion->plan_id !== null ? (int) $promotion->plan_id : null,
                'name' => (string) $promotion->name,
                'type' => (string) $promotion->type,
                'value' => (float) ($promotion->value ?? 0),
                'starts_at' => optional($promotion->starts_at)?->toDateString(),
                'ends_at' => optional($promotion->ends_at)?->toDateString(),
                'max_uses' => $promotion->max_uses !== null ? (int) $promotion->max_uses : null,
                'times_used' => (int) ($promotion->times_used ?? 0),
            ])
            ->values();

        $formDefaults = [
            'first_name' => (string) old('first_name', ''),
            'last_name' => (string) old('last_name', ''),
            'document_number' => (string) old('document_number', ''),
            'phone' => (string) old('phone', ''),
            'gender' => (string) old('gender', 'neutral'),
            'start_membership' => old('start_membership') ? true : false,
            'plan_id' => old('plan_id') !== null ? (string) old('plan_id') : '',
            'membership_starts_at' => (string) old('membership_starts_at', now()->toDateString()),
            'membership_price' => old('membership_price') !== null ? (string) old('membership_price') : '',
            'promotion_id' => $canManagePromotions && old('promotion_id') !== null ? (string) old('promotion_id') : '',
            'payment_method' => (string) old('payment_method', 'cash'),
            'amount_paid' => old('amount_paid') !== null ? (string) old('amount_paid') : '',
            'create_app_account' => $canManageClientAccounts && old('create_app_account') ? true : false,
            'app_username' => $canManageClientAccounts ? (string) old('app_username', '') : '',
        ];

        $createErrorKeys = [
            'first_name',
            'last_name',
            'document_number',
            'phone',
            'gender',
            'photo',
            'start_membership',
            'plan_id',
            'membership_starts_at',
            'membership_price',
            'promotion_id',
            'payment_method',
            'amount_paid',
            'create_app_account',
            'app_username',
            'app_password',
            'app_password_confirmation',
            'cash',
        ];
        $editErrorKeys = ['edit_client_id', 'edit_first_name', 'edit_last_name', 'edit_phone', 'clients'];
        $deleteErrorKeys = ['delete_client_id', 'owner_password', 'clients'];
        $clientRowsById = collect($clients->items())->keyBy('id');
        $oldEditClientId = (int) old('edit_client_id', 0);
        $oldDeleteClientId = (int) old('delete_client_id', 0);
        $oldEditClient = $oldEditClientId > 0 ? $clientRowsById->get($oldEditClientId) : null;
        $oldDeleteClient = $oldDeleteClientId > 0 ? $clientRowsById->get($oldDeleteClientId) : null;
        $showCreateErrorSummary = collect($createErrorKeys)->contains(fn (string $key): bool => $errors->has($key));
        $showEditErrorSummary = collect($editErrorKeys)->contains(fn (string $key): bool => $errors->has($key));
        $showDeleteErrorSummary = collect($deleteErrorKeys)->contains(fn (string $key): bool => $errors->has($key));
        $createErrorMessages = collect($createErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->unique()
            ->values();
        $editErrorMessages = collect($editErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->unique()
            ->values();
        $deleteErrorMessages = collect($deleteErrorKeys)
            ->flatMap(fn (string $key) => $errors->get($key))
            ->unique()
            ->values();

        $editModalDefaults = [
            'open' => old('_open_edit_modal') ? true : false,
            'action' => (string) ($oldEditClient['edit_url'] ?? ''),
            'id' => $oldEditClientId > 0 ? $oldEditClientId : null,
            'first_name' => (string) old('edit_first_name', (string) ($oldEditClient['first_name'] ?? '')),
            'last_name' => (string) old('edit_last_name', (string) ($oldEditClient['last_name'] ?? '')),
            'phone' => (string) old('edit_phone', (string) ($oldEditClient['phone'] ?? '')),
            'full_name' => (string) ($oldEditClient['full_name'] ?? ''),
        ];

        $deleteModalDefaults = [
            'open' => old('_open_delete_modal') ? true : false,
            'action' => (string) ($oldDeleteClient['delete_url'] ?? ''),
            'id' => $oldDeleteClientId > 0 ? $oldDeleteClientId : null,
            'full_name' => (string) ($oldDeleteClient['full_name'] ?? ''),
            'owner_scope_label' => (string) ($oldDeleteClient['owner_scope_label'] ?? 'dueño del gimnasio'),
            'owner_modal_hint' => (string) ($oldDeleteClient['owner_modal_hint'] ?? 'Confirma con la contraseña del dueño del gimnasio.'),
        ];
    ?>

    <div x-data="clientsIndexPage({
            openCreateModal: <?php echo \Illuminate\Support\Js::from($openCreateModal)->toHtml() ?>,
             plans: <?php echo \Illuminate\Support\Js::from($planCatalog)->toHtml() ?>,
             promotions: <?php echo \Illuminate\Support\Js::from($promotionCatalog)->toHtml() ?>,
             defaults: <?php echo \Illuminate\Support\Js::from($formDefaults)->toHtml() ?>,
             editModal: <?php echo \Illuminate\Support\Js::from($editModalDefaults)->toHtml() ?>,
             deleteModal: <?php echo \Illuminate\Support\Js::from($deleteModalDefaults)->toHtml() ?>,
             documentCheckUrl: <?php echo \Illuminate\Support\Js::from(route('clients.check-document'))->toHtml() ?>,
             allowCreate: <?php echo \Illuminate\Support\Js::from(! $isGlobalScope)->toHtml() ?>,
             canManageClientAccounts: <?php echo \Illuminate\Support\Js::from($canManageClientAccounts)->toHtml() ?>,
         })"
         x-init="init()"
         class="clients-page-shell space-y-4">

        <?php if($planControlClientsDashboard): ?>
            <?php
                $controlClientPriorities = collect($planControlClientsDashboard['priorities'] ?? [])->values();
                $controlPrimaryAction = (array) ($planControlClientsDashboard['primary_action'] ?? []);
                $controlPrimaryFilter = (string) ($controlPrimaryAction['filter'] ?? 'active');
            ?>
            <section class="clients-control-shell">
                <div class="clients-control-grid">
                    <div class="clients-control-copy">
                        <span class="clients-control-kicker">Plan Control / Clientes</span>
                        <h2 class="clients-control-heading"><?php echo e($planControlClientsDashboard['headline'] ?? 'Clientes listos para operar y cobrar'); ?></h2>
                        <p class="clients-control-summary"><?php echo e($planControlClientsDashboard['summary'] ?? 'Usa esta vista para buscar, cobrar y controlar vencimientos con una sola sede.'); ?></p>
                    </div>

                    <div class="clients-control-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', array_merge($clientsRouteParams, ['filter' => $controlPrimaryFilter])),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', array_merge($clientsRouteParams, ['filter' => $controlPrimaryFilter]))),'variant' => 'primary']); ?>
                            <?php echo e($controlPrimaryAction['label'] ?? 'Ver activos'); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if(! $isGlobalScope): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','xOn:click' => 'openCreateClient()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','x-on:click' => 'openCreateClient()']); ?>Nuevo cliente <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $clientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $clientsRouteParams)),'variant' => 'ghost']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if($canViewReports): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $clientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $clientsRouteParams)),'variant' => 'ghost']); ?>Reportes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($controlClientPriorities->isNotEmpty()): ?>
                    <div class="clients-control-priority-grid mt-4">
                        <?php $__currentLoopData = $controlClientPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="clients-control-priority" data-tone="<?php echo e($priority['tone'] ?? 'neutral'); ?>">
                                <p class="clients-control-priority-label"><?php echo e($priority['label'] ?? 'Lectura'); ?></p>
                                <p class="clients-control-priority-value"><?php echo e($priority['value'] ?? '0'); ?></p>
                                <p class="clients-control-priority-note"><?php echo e($priority['note'] ?? ''); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if($professionalClientsDashboard): ?>
            <?php
                $professionalClientAlerts = collect($professionalClientsDashboard['alerts'] ?? [])->values();
                $professionalClientMetrics = [
                    [
                        'label' => 'Renovaciones por mover',
                        'value' => (string) ((int) ($professionalClientsDashboard['renewal_opportunities'] ?? 0)),
                        'note' => 'Por vencer y vencidos listos para cobrar.',
                        'tone' => 'warning',
                    ],
                    [
                        'label' => 'Clientes vencidos',
                        'value' => (string) ((int) ($professionalClientsDashboard['expired_count'] ?? 0)),
                        'note' => 'Base fria que aun puedes recuperar.',
                        'tone' => 'danger',
                    ],
                    [
                        'label' => 'Promos activas',
                        'value' => (string) ((int) ($professionalClientsDashboard['active_promotions_count'] ?? 0)),
                        'note' => 'Campanas listas para empujar renovaciones.',
                        'tone' => 'accent',
                    ],
                    [
                        'label' => 'Clientes con promo',
                        'value' => (string) ((int) ($professionalClientsDashboard['clients_with_promotion_count'] ?? 0)),
                        'note' => 'Check-ins hoy '.(int) ($professionalClientsDashboard['attended_today_count'] ?? 0),
                        'tone' => 'info',
                    ],
                ];
            ?>
            <section class="clients-pro-shell">
                <div class="clients-pro-grid">
                    <div class="clients-elite-head flex flex-wrap items-start justify-between gap-3">
                        <div class="clients-pro-copy">
                            <span class="clients-pro-kicker">Plan Profesional / Clientes</span>
                            <h2 class="clients-pro-heading"><?php echo e($professionalClientsDashboard['headline'] ?? 'Base comercial de clientes'); ?></h2>
                            <p class="clients-pro-summary"><?php echo e($professionalClientsDashboard['summary'] ?? 'Renueva, recupera y empuja promos desde una lectura comercial mucho mas ligera.'); ?></p>
                        </div>
                        <span class="clients-pro-badge">Profesional</span>
                    </div>

                    <div class="clients-pro-metrics">
                        <?php $__currentLoopData = $professionalClientMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="clients-pro-metric" data-tone="<?php echo e($metric['tone']); ?>">
                                <p class="clients-pro-metric-label"><?php echo e($metric['label']); ?></p>
                                <p class="clients-pro-metric-value"><?php echo e($metric['value']); ?></p>
                                <p class="clients-pro-metric-note"><?php echo e($metric['note']); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($professionalClientAlerts->isNotEmpty()): ?>
                        <div class="clients-pro-insights">
                            <?php $__currentLoopData = $professionalClientAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="clients-pro-chip" data-tone="<?php echo e($alert['tone'] ?? 'info'); ?>">
                                    <p class="clients-pro-chip-title"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                    <p class="clients-pro-chip-copy"><?php echo e($alert['description'] ?? ''); ?></p>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <div class="clients-pro-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', array_merge($clientsRouteParams, ['filter' => 'expiring'])),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', array_merge($clientsRouteParams, ['filter' => 'expiring']))),'variant' => 'primary']); ?>Ver por vencer <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', array_merge($clientsRouteParams, ['filter' => 'expired'])),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', array_merge($clientsRouteParams, ['filter' => 'expired']))),'variant' => 'secondary']); ?>Ver vencidos <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if($canManagePromotions && $isOwner): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index', $clientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index', $clientsRouteParams)),'variant' => 'ghost']); ?>Planes y promos <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php elseif($canViewReports): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $clientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $clientsRouteParams)),'variant' => 'ghost']); ?>Ver reportes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $clientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $clientsRouteParams)),'variant' => 'ghost']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if($canUseSalesInventory): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $clientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $clientsRouteParams)),'variant' => 'ghost']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php elseif($canManagePromotions && $isOwner && $canViewReports): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $clientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $clientsRouteParams)),'variant' => 'ghost']); ?>Ver reportes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($premiumClientsDashboard): ?>
            <?php
                $premiumClientMetrics = [
                    [
                        'label' => 'Con app',
                        'value' => (string) ((int) ($premiumClientsDashboard['clients_with_app_access_count'] ?? 0)),
                        'note' => (string) ((int) ($premiumClientsDashboard['active_clients_with_app_access_count'] ?? 0)).' activos listos.',
                        'tone' => 'success',
                    ],
                    [
                        'label' => 'Pendientes',
                        'value' => (string) ((int) ($premiumClientsDashboard['active_clients_without_app_access_count'] ?? 0)),
                        'note' => 'Activos sin acceso app.',
                        'tone' => 'warning',
                    ],
                    [
                        'label' => 'Renovaciones',
                        'value' => (string) ((int) ($premiumClientsDashboard['renewal_pipeline'] ?? 0)),
                        'note' => 'Por vencer y vencidos.',
                        'tone' => 'accent',
                    ],
                ];
            ?>
            <section class="clients-pro-shell clients-elite-shell">
                <div class="clients-pro-grid">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="clients-pro-copy">
                            <span class="clients-pro-kicker">Plan Elite / Clientes</span>
                            <h2 class="clients-pro-heading">Clientes premium sin ruido</h2>
                            <p class="clients-pro-summary">Acceso app y renovaciones en un resumen corto.</p>
                        </div>
                        <span class="clients-pro-badge">Premium</span>
                    </div>

                    <div class="clients-pro-metrics">
                        <?php $__currentLoopData = $premiumClientMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="clients-pro-metric" data-tone="<?php echo e($metric['tone']); ?>">
                                <p class="clients-pro-metric-label"><?php echo e($metric['label']); ?></p>
                                <p class="clients-pro-metric-value"><?php echo e($metric['value']); ?></p>
                                <p class="clients-pro-metric-note"><?php echo e($metric['note']); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="clients-pro-actions">
                        <?php if($canManageClientAccounts && \Illuminate\Support\Facades\Route::has('client-portal.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('client-portal.index', $clientsRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('client-portal.index', $clientsRouteParams)),'variant' => 'primary']); ?>Portal cliente <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if(! $isGlobalScope): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','xOn:click' => 'openCreateClient()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','x-on:click' => 'openCreateClient()']); ?>Nuevo cliente <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', array_merge($clientsRouteParams, ['filter' => 'expiring'])),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', array_merge($clientsRouteParams, ['filter' => 'expiring']))),'variant' => 'ghost']); ?>Ver por vencer <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="clients-kpi-grid grid sm:grid-cols-2 xl:grid-cols-4">
            <article class="clients-kpi-card" data-tone="neutral">
                <p class="clients-kpi-label">Total clientes</p>
                <p class="clients-kpi-value"><?php echo e($stats['total']); ?></p>
                <p class="clients-kpi-note">Base actual del listado</p>
            </article>
            <article class="clients-kpi-card" data-tone="success">
                <p class="clients-kpi-label">Activos</p>
                <p class="clients-kpi-value"><?php echo e($stats['active']); ?></p>
                <p class="clients-kpi-note">Membresía vigente</p>
            </article>
            <article class="clients-kpi-card" data-tone="warning">
                <p class="clients-kpi-label">Por vencer</p>
                <p class="clients-kpi-value"><?php echo e($stats['expiring']); ?></p>
                <p class="clients-kpi-note">En los próximos 7 días</p>
            </article>
            <article class="clients-kpi-card" data-tone="danger">
                <p class="clients-kpi-label">Vencid@s</p>
                <p class="clients-kpi-value"><?php echo e($stats['expired']); ?></p>
                <p class="clients-kpi-note">Requieren renovación</p>
            </article>
        </section>

        <?php if($isGlobalScope): ?>
            <div class="ui-alert ui-alert-warning">
                Modo global activo: listado consolidado por sede. Para crear o editar clientes selecciona una sucursal específica.
            </div>
        <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Clientes del gimnasio','subtitle' => ''.e($planControlClientsDashboard ? 'Base ordenada para recepcion, renovaciones y seguimiento diario.' : 'Vista operacional para recepcion, renovaciones y retencion.').'','class' => 'clients-main-card']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Clientes del gimnasio','subtitle' => ''.e($planControlClientsDashboard ? 'Base ordenada para recepcion, renovaciones y seguimiento diario.' : 'Vista operacional para recepcion, renovaciones y retencion.').'','class' => 'clients-main-card']); ?>
            <div class="clients-toolbar-shell space-y-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="<?php echo e(route('clients.index', $clientsRouteParams)); ?>" class="clients-search-form grid gap-3 lg:grid-cols-[1fr_auto_auto] lg:w-full lg:max-w-3xl">
                        <input type="hidden" name="filter" value="<?php echo e($quickFilter); ?>">
                        <input id="clients-search"
                               type="text"
                               name="q"
                               value="<?php echo e($search); ?>"
                               placeholder="Buscar por nombre, apellido o documento..."
                               class="clients-search-input ui-input">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary','class' => 'clients-toolbar-button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary','class' => 'clients-toolbar-button']); ?>Buscar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $clientsRouteParams),'variant' => 'ghost','class' => 'clients-toolbar-button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $clientsRouteParams)),'variant' => 'ghost','class' => 'clients-toolbar-button']); ?>Limpiar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </form>

                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'clients-open-create','type' => 'button','variant' => $isGlobalScope ? 'ghost' : 'primary','xOn:click' => 'openCreateClient()','class' => 'clients-create-button whitespace-nowrap','disabled' => $isGlobalScope,'title' => ''.e($isGlobalScope ? 'Selecciona una sede para crear clientes' : 'Crear cliente').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'clients-open-create','type' => 'button','variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalScope ? 'ghost' : 'primary'),'x-on:click' => 'openCreateClient()','class' => 'clients-create-button whitespace-nowrap','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isGlobalScope),'title' => ''.e($isGlobalScope ? 'Selecciona una sede para crear clientes' : 'Crear cliente').'']); ?>
                        <?php echo e($isGlobalScope ? 'Solo lectura global' : '+ Nuevo cliente'); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                </div>

                <div id="clients-filter-chips" class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filterKey => $filterLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isActiveFilter = $quickFilter === $filterKey;
                            $chipQuery = array_merge($baseFilterQuery, ['filter' => $filterKey]);
                        ?>
                        <a href="<?php echo e(route('clients.index', $chipQuery)); ?>" class="clients-filter-chip <?php echo e($isActiveFilter ? 'is-active' : ''); ?>"><?php echo e($filterLabel); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <p class="text-xs ui-muted sm:hidden">
                Desliza para recorrer el listado de clientes.
            </p>

            <div id="clients-table" class="clients-table-wrap mt-4 overflow-hidden rounded-2xl border border-slate-300/70 dark:border-white/10">
                <div class="clients-table-scroll max-h-[560px] overflow-x-auto overflow-y-auto">
                    <table class="ui-table min-w-[1200px]">
                        <thead>
                        <tr class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50/95 text-left text-xs uppercase tracking-wider text-slate-500 backdrop-blur dark:border-slate-700 dark:bg-slate-800/95 dark:text-slate-300">
                            <th class="px-3 py-4">ID</th>
                            <th class="px-3 py-4">Cliente</th>
                            <th class="px-3 py-4">Plan</th>
                            <th class="px-3 py-4">Vence</th>
                            <th class="px-3 py-4">Días restantes</th>
                            <th class="px-3 py-4">Pago</th>
                            <th class="px-3 py-4">Última asistencia</th>
                            <th class="px-3 py-4">Estado</th>
                            <?php if($isGlobalScope): ?>
                                <th class="px-3 py-4">Sede</th>
                            <?php endif; ?>
                            <th class="px-3 py-4">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $daysToneClass = match ($client['days_badge']['tone']) {
                                    'success' => 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-500/20 dark:text-emerald-200 dark:border-emerald-400/30',
                                    'warning' => 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-500/20 dark:text-amber-200 dark:border-amber-400/30',
                                    'danger' => 'bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-500/20 dark:text-rose-200 dark:border-rose-400/30',
                                    'danger-strong' => 'bg-rose-600/80 text-rose-50 border border-rose-300/60',
                                    default => 'bg-slate-100 text-slate-700 border border-slate-300 dark:bg-slate-500/20 dark:text-slate-200 dark:border-slate-400/30',
                                };
                                $showUrl = (string) ($client['show_url'] ?? route('clients.show', ['contextGym' => $contextGym, 'client' => $client['id']] + ($isGlobalScope ? ['scope' => 'global'] : [])));
                                $progressUrl = (string) ($client['progress_url'] ?? $showUrl);
                                $canShowProgress = ! empty($client['can_show_progress']);
                                $canManage = ! empty($client['can_manage']);
                                $hasSecondaryActions = $canShowProgress || $canManage;
                            ?>
                            <tr class="border-b border-slate-200 text-sm text-slate-800 odd:bg-white even:bg-slate-100 hover:bg-sky-100/70 dark:border-slate-800 dark:text-slate-200 dark:odd:bg-slate-900 dark:even:bg-slate-950/50 dark:hover:bg-cyan-500/10">
                                <td class="px-3 py-4 font-bold text-slate-800 dark:text-slate-200">#<?php echo e($client['id']); ?></td>
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php if($client['photo_url']): ?>
                                            <img src="<?php echo e($client['photo_url']); ?>"
                                                 alt="<?php echo e($client['full_name']); ?>"
                                                 class="clients-client-avatar object-cover">
                                        <?php else: ?>
                                            <div class="clients-client-avatar flex items-center justify-center bg-slate-700 text-xs font-black uppercase tracking-wider text-white">
                                                <?php echo e($client['initials']); ?>

                                            </div>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-900 dark:text-slate-100"><?php echo e($client['full_name']); ?></p>
                                            <p class="clients-client-doc truncate"><?php echo e($client['document_number']); ?></p>
                                            <p class="clients-client-meta truncate">Alta: <?php echo e($client['created_by_display']); ?></p>
                                            <p class="clients-client-meta truncate">Última gestión: <?php echo e($client['last_managed_by_display']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 font-semibold text-slate-800 dark:text-slate-100"><?php echo e($client['plan_name']); ?></td>
                                <td class="px-3 py-4 text-slate-700 dark:text-slate-200"><?php echo e($client['membership_ends_at_human']); ?></td>
                                <td class="px-3 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide <?php echo e($daysToneClass); ?>">
                                        <?php echo e($client['days_badge']['label']); ?>

                                    </span>
                                </td>
                                <td class="px-3 py-4">
                                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $client['payment_badge']['variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client['payment_badge']['variant'])]); ?>
                                        <?php echo e($client['payment_badge']['label']); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                </td>
                                <td class="px-3 py-4 text-slate-700 dark:text-slate-200"><?php echo e($client['last_checkin_label']); ?></td>
                                <td class="px-3 py-4">
                                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $client['status_badge']['variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($client['status_badge']['variant'])]); ?>
                                        <?php echo e($client['status_badge']['label']); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                </td>
                                <?php if($isGlobalScope): ?>
                                    <td class="px-3 py-4">
                                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'info']); ?>
                                            <?php echo e($client['gym_name'] ?? '-'); ?>

                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="px-3 py-4 min-w-[11rem]">
                                    <div x-data="{ open: false }" class="clients-row-actions">
                                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $showUrl,'variant' => 'secondary','size' => 'sm','class' => 'ui-action-button clients-row-view']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showUrl),'variant' => 'secondary','size' => 'sm','class' => 'ui-action-button clients-row-view']); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="ui-action-button-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <span class="ui-action-button-label">Ver</span>
                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                        <?php if($hasSecondaryActions): ?>
                                            <button type="button"
                                                    class="ui-button ui-button-ghost clients-row-menu-toggle"
                                                    x-on:click="open = !open"
                                                    x-on:keydown.escape.stop="open = false"
                                                    x-bind:aria-expanded="open.toString()"
                                                    aria-haspopup="menu"
                                                    title="Más acciones">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="clients-row-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="5" r="1.8"/>
                                                    <circle cx="12" cy="12" r="1.8"/>
                                                    <circle cx="12" cy="19" r="1.8"/>
                                                </svg>
                                                <span class="sr-only">Más acciones</span>
                                            </button>

                                            <div x-cloak
                                                 x-show="open"
                                                 x-transition.opacity.duration.120ms
                                                 class="clients-row-menu-backdrop"
                                                 aria-hidden="true"
                                                 x-on:click="open = false"></div>

                                            <div x-cloak
                                                 x-show="open"
                                                 x-transition.origin.top.right.duration.120ms
                                                 x-on:click.outside="open = false"
                                                 class="clients-row-menu"
                                                 role="menu">
                                                <?php if($canShowProgress): ?>
                                                    <a href="<?php echo e($progressUrl); ?>" class="clients-row-menu-item" role="menuitem" x-on:click="open = false">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="clients-row-menu-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M4 19h16"/>
                                                            <path d="M7 16l3-4 3 2 4-6"/>
                                                            <circle cx="7" cy="16" r="1"/>
                                                            <circle cx="10" cy="12" r="1"/>
                                                            <circle cx="13" cy="14" r="1"/>
                                                            <circle cx="17" cy="8" r="1"/>
                                                        </svg>
                                                        <span>Rendimiento</span>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if($canManage): ?>
                                                    <button type="button"
                                                            class="clients-row-menu-item"
                                                            role="menuitem"
                                                            x-on:click="open = false; openEditClient({
                                                                action: <?php echo \Illuminate\Support\Js::from((string) ($client['edit_url'] ?? ''))->toHtml() ?>,
                                                                id: <?php echo e((int) $client['id']); ?>,
                                                                first_name: <?php echo \Illuminate\Support\Js::from((string) ($client['first_name'] ?? ''))->toHtml() ?>,
                                                                last_name: <?php echo \Illuminate\Support\Js::from((string) ($client['last_name'] ?? ''))->toHtml() ?>,
                                                                phone: <?php echo \Illuminate\Support\Js::from((string) ($client['phone'] ?? ''))->toHtml() ?>,
                                                                full_name: <?php echo \Illuminate\Support\Js::from((string) ($client['full_name'] ?? ''))->toHtml() ?>
                                                            })">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="clients-row-menu-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M12 20h9"/>
                                                            <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z"/>
                                                        </svg>
                                                        <span>Editar</span>
                                                    </button>

                                                    <button type="button"
                                                            class="clients-row-menu-item is-danger"
                                                            role="menuitem"
                                                            x-on:click="open = false; openDeleteClient({
                                                                action: <?php echo \Illuminate\Support\Js::from((string) ($client['delete_url'] ?? ''))->toHtml() ?>,
                                                                id: <?php echo e((int) $client['id']); ?>,
                                                                full_name: <?php echo \Illuminate\Support\Js::from((string) ($client['full_name'] ?? ''))->toHtml() ?>,
                                                                owner_scope_label: <?php echo \Illuminate\Support\Js::from((string) ($client['owner_scope_label'] ?? 'dueño del gimnasio'))->toHtml() ?>,
                                                                owner_modal_hint: <?php echo \Illuminate\Support\Js::from((string) ($client['owner_modal_hint'] ?? 'Confirma con la contraseña del dueño del gimnasio.'))->toHtml() ?>
                                                            })">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="clients-row-menu-item-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M3 6h18"/>
                                                            <path d="M8 6V4h8v2"/>
                                                            <path d="M19 6l-1 14H6L5 6"/>
                                                            <path d="M10 11v6"/>
                                                            <path d="M14 11v6"/>
                                                        </svg>
                                                        <span>Eliminar</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($isGlobalScope ? 10 : 9); ?>" class="clients-empty-state px-3 py-8 text-center text-sm text-slate-600 dark:text-slate-300">
                                    No hay clientes para los filtros actuales.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clients-table-footer mt-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <p class="text-sm ui-muted">
                    Mostrando <?php echo e($clients->firstItem() ?? 0); ?> - <?php echo e($clients->lastItem() ?? 0); ?> de <?php echo e($clients->total()); ?> clientes
                </p>
                <div>
                    <?php echo e($clients->onEachSide(1)->links()); ?>

                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

        <div x-cloak
             x-show="modalOpen"
             x-transition.opacity
             class="ui-modal-backdrop items-start"
             x-on:click.self="closeCreateClient()"
             x-on:keydown.escape.window="closeCreateClient()">
            <div class="ui-modal-shell w-full max-w-3xl"
                 x-transition.scale.origin.top
                 role="dialog"
                 aria-modal="true">
                <form method="POST"
                      action="<?php echo e(route('clients.store')); ?>"
                      enctype="multipart/form-data"
                      novalidate
                      class="flex h-full min-h-0 flex-1 flex-col space-y-0"
                      x-on:submit="submitCreateClient($event)">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="_open_create_modal" value="1">

                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-100">Crear cliente</h3>
                            <p class="mt-1 text-sm text-slate-400">Alta rápida de cliente con membresía opcional.</p>
                        </div>
                        <button type="button"
                                class="ui-button ui-button-ghost px-2 py-1 text-sm"
                                x-on:click="closeCreateClient()"
                                aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                        <?php if($showCreateErrorSummary): ?>
                            <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                                <p class="font-semibold">Corrige los siguientes campos antes de guardar:</p>
                                <ul class="mt-1 list-disc space-y-1 pl-5 text-xs">
                                    <?php $__currentLoopData = $createErrorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($message); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Nombre</span>
                                <input type="text"
                                       name="first_name"
                                       x-model="form.first_name"
                                       x-on:blur="normalizeNameField('first_name')"
                                       x-on:input="clearClientFieldError('first_name')"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.first_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       x-ref="firstNameInput">
                                <p x-cloak x-show="clientValidationErrors.first_name" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.first_name"></p>
                                <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Apellido</span>
                                <input type="text"
                                       name="last_name"
                                       x-model="form.last_name"
                                       x-on:blur="normalizeNameField('last_name')"
                                       x-on:input="clearClientFieldError('last_name')"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.last_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''">
                                <p x-cloak x-show="clientValidationErrors.last_name" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.last_name"></p>
                                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                <span>Documento</span>
                                <input type="text"
                                       name="document_number"
                                       x-model.trim="form.document_number"
                                       x-on:input="clearClientFieldError('document_number')"
                                       x-on:input.debounce.350ms="checkDocument()"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.document_number ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       placeholder="Cédula, DNI o pasaporte">
                                <p x-cloak x-show="clientValidationErrors.document_number" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.document_number"></p>
                                <?php $__errorArgs = ['document_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div x-cloak x-show="documentState === 'checking'" class="text-xs text-slate-400">Validando documento...</div>
                                <div x-cloak x-show="documentState === 'exists'" class="rounded-lg border border-rose-500/40 bg-rose-500/10 p-2 text-xs text-rose-200">
                                    <p>Este documento ya existe en este gimnasio.</p>
                                    <a class="mt-2 inline-flex items-center gap-1 font-semibold text-cyan-300 underline"
                                       x-bind:href="documentMatchUrl"
                                       x-show="documentMatchUrl">
                                        Abrir cliente
                                    </a>
                                </div>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Teléfono</span>
                                <input type="text"
                                       name="phone"
                                       x-model="form.phone"
                                       x-on:input="clearClientFieldError('phone')"
                                       required
                                       class="ui-input"
                                       x-bind:class="clientValidationErrors.phone ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       placeholder="Ej: 0991234567">
                                <p x-cloak x-show="clientValidationErrors.phone" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.phone"></p>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Género</span>
                                <select name="gender" x-model="form.gender" class="ui-input">
                                    <option value="male">Hombre</option>
                                    <option value="female">Mujer</option>
                                    <option value="neutral">Neutral</option>
                                </select>
                                <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <?php if($canManageClientAccounts): ?>
                                <div class="space-y-3 rounded-xl border border-cyan-500/30 bg-cyan-500/5 p-3 md:col-span-2">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-100">
                                            <input type="checkbox"
                                                   name="create_app_account"
                                                   value="1"
                                                   x-model="form.create_app_account"
                                                   class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-cyan-500 focus:ring-cyan-400/40">
                                            Crear usuario y contraseña para app cliente
                                        </label>
                                        <span class="inline-flex rounded-full border border-cyan-400/30 bg-cyan-500/15 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-cyan-100">
                                            Premium / Sucursales
                                        </span>
                                    </div>

                                    <div class="grid gap-3 md:grid-cols-2" x-cloak x-show="form.create_app_account">
                                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                                            <span>Usuario app</span>
                                            <input type="text"
                                                   name="app_username"
                                                   x-model.trim="form.app_username"
                                                   x-on:input="clearClientFieldError('app_username')"
                                                   autocomplete="off"
                                                   class="ui-input"
                                                   x-bind:class="clientValidationErrors.app_username ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                                   placeholder="ej: maria.perez">
                                            <p x-cloak x-show="clientValidationErrors.app_username" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.app_username"></p>
                                            <?php $__errorArgs = ['app_username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </label>

                                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                                            <span>Contraseña app</span>
                                            <input type="password"
                                                   name="app_password"
                                                   x-model="form.app_password"
                                                   x-on:input="clearClientFieldError('app_password')"
                                                   autocomplete="new-password"
                                                   class="ui-input"
                                                   x-bind:class="clientValidationErrors.app_password ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                                   placeholder="Mínimo 8 caracteres">
                                            <p x-cloak x-show="clientValidationErrors.app_password" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.app_password"></p>
                                            <?php $__errorArgs = ['app_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </label>

                                        <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                            <span>Confirmar contraseña app</span>
                                            <input type="password"
                                                   name="app_password_confirmation"
                                                   x-model="form.app_password_confirmation"
                                                   x-on:input="clearClientFieldError('app_password_confirmation')"
                                                   autocomplete="new-password"
                                                   class="ui-input"
                                                   x-bind:class="clientValidationErrors.app_password_confirmation ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                                   placeholder="Repite la contraseña">
                                            <p x-cloak x-show="clientValidationErrors.app_password_confirmation" class="text-xs font-semibold text-rose-300" x-text="clientValidationErrors.app_password_confirmation"></p>
                                            <?php $__errorArgs = ['app_password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="space-y-2 md:col-span-2">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Foto del cliente</span>
                                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="ui-input"
                                           x-on:change="onPhotoSelected($event)">
                                </label>
                                <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="block text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                <div class="flex items-center gap-3 rounded-xl border border-slate-700 bg-slate-900/60 p-3">
                                    <template x-if="photoPreview">
                                        <img x-bind:src="photoPreview" alt="Preview" class="h-16 w-16 rounded-full border border-slate-600 object-cover">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full border border-slate-600 bg-slate-800 text-lg font-black uppercase text-slate-200"
                                             x-text="avatarInitials()"></div>
                                    </template>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-200">Vista previa</p>
                                        <p class="text-xs text-slate-400">Si no subes imagen se mostraran iniciales.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-200">
                                    <input type="checkbox" name="start_membership" value="1" x-model="form.start_membership" x-on:change="onMembershipToggle()" class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-cyan-500 focus:ring-cyan-400/40">
                                    Iniciar membresía ahora
                                </label>

                                <span class="inline-flex rounded-full border border-slate-600 bg-slate-800 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-200"
                                      x-text="membershipBadgeLabel"></span>
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-2" x-cloak x-show="form.start_membership">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Plan</span>
                                    <select name="plan_id" x-model="form.plan_id" x-on:change="onPlanChange()" x-bind:disabled="!form.start_membership" class="ui-input">
                                        <option value="">Selecciona un plan</option>
                                        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($plan->id); ?>"><?php echo e($plan->name); ?> (<?php echo e(\App\Support\PlanDuration::label($plan->duration_unit, (int) $plan->duration_days, $plan->duration_months)); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Fecha inicio</span>
                                    <input type="date" name="membership_starts_at" x-model="form.membership_starts_at" x-on:input="recalculateMembershipEnd()" x-bind:disabled="!form.start_membership" class="ui-input">
                                    <?php $__errorArgs = ['membership_starts_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Precio</span>
                                    <input type="number" name="membership_price" x-model="form.membership_price" min="0" step="0.01" x-bind:disabled="!form.start_membership" class="ui-input">
                                    <?php $__errorArgs = ['membership_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </label>

                                <?php if($canManagePromotions): ?>
                                    <label class="space-y-1 text-sm font-semibold text-slate-300">
                                        <span>Promoción (opcional)</span>
                                        <select name="promotion_id" x-model="form.promotion_id" x-on:change="onPromotionChange()" x-bind:disabled="!form.start_membership" class="ui-input">
                                            <option value="">Sin promoción</option>
                                            <template x-for="promo in availablePromotions()" :key="promo.id">
                                                <option :value="String(promo.id)" x-text="promotionOptionLabel(promo)"></option>
                                            </template>
                                        </select>
                                        <?php $__errorArgs = ['promotion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </label>
                                <?php else: ?>
                                    <div class="rounded-lg border border-amber-500/40 bg-amber-500/10 p-2 text-xs text-amber-200 md:col-span-2">
                                        Promociones no disponibles en tu plan actual.
                                        <?php $__errorArgs = ['promotion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 font-semibold text-rose-200"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                <?php endif; ?>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Método de pago</span>
                                    <select name="payment_method" x-model="form.payment_method" x-bind:disabled="!form.start_membership" class="ui-input">
                                        <option value="cash">Efectivo</option>
                                        <option value="transfer">Transferencia</option>
                                        <option value="card">Tarjeta</option>
                                    </select>
                                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </label>

                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Monto pagado</span>
                                    <input type="number" name="amount_paid" x-model="form.amount_paid" min="0" step="0.01" x-bind:disabled="!form.start_membership" class="ui-input">
                                    <?php $__errorArgs = ['amount_paid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </label>

                                <div class="space-y-2 rounded-lg border border-slate-700 bg-slate-900/70 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Fecha fin estimada</p>
                                    <p class="text-sm font-bold text-slate-100" x-text="membershipEndLabel"></p>
                                    <p class="text-xs text-slate-400" x-show="promotionSummaryLabel" x-text="promotionSummaryLabel"></p>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
                                          x-bind:class="paymentBadgeClass"
                                          x-text="paymentStatusLabel"></span>
                                </div>
                            </div>

                            <p x-cloak x-show="form.start_membership && plans.length === 0" class="mt-3 rounded-lg border border-amber-500/40 bg-amber-500/10 p-2 text-xs text-amber-200">
                                No hay planes activos. Crea un plan antes de iniciar membresías desde este modal.
                            </p>
                            <?php $__errorArgs = ['cash'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="mt-3 rounded-xl border-2 border-rose-400/80 bg-rose-500/20 p-3 text-rose-100 shadow-lg">
                                    <p class="text-xs font-black uppercase tracking-wide">Debe abrir caja para cobrar</p>
                                    <p class="mt-1 text-sm font-semibold"><?php echo e($message); ?></p>
                                    <div class="mt-2">
                                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $clientsRouteParams),'variant' => 'secondary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $clientsRouteParams)),'variant' => 'secondary','size' => 'sm']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                    </div>
                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <footer class="ui-modal-sticky-footer flex items-center justify-end gap-3 px-5 py-4">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','xOn:click' => 'closeCreateClient()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','x-on:click' => 'closeCreateClient()']); ?>Cancelar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'success','xBind:disabled' => 'submitting || documentState === \'exists\' || (form.start_membership && plans.length === 0)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'success','x-bind:disabled' => 'submitting || documentState === \'exists\' || (form.start_membership && plans.length === 0)']); ?>
                            Guardar
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </footer>
                </form>
            </div>
        </div>

        <div x-cloak
             x-show="editModalOpen"
             x-transition.opacity
             class="ui-modal-backdrop items-start"
             x-on:click.self="closeEditClient()"
             x-on:keydown.escape.window="closeEditClient()">
            <div class="ui-modal-shell w-full max-w-2xl"
                 x-transition.scale.origin.top
                 role="dialog"
                 aria-modal="true">
                <form method="POST"
                      x-bind:action="editForm.action || '#'"
                      novalidate
                      class="flex h-full min-h-0 flex-1 flex-col space-y-0"
                      x-on:submit="submitEditClient($event)">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="_open_edit_modal" value="1">
                    <input type="hidden" name="edit_client_id" x-bind:value="editForm.id || ''">

                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-100">Editar cliente</h3>
                            <p class="mt-1 text-sm text-slate-400">Solo puedes actualizar nombre, apellido y teléfono.</p>
                        </div>
                        <button type="button"
                                class="ui-button ui-button-ghost px-2 py-1 text-sm"
                                x-on:click="closeEditClient()"
                                aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                        <?php if($showEditErrorSummary): ?>
                            <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                                <p class="font-semibold">No se pudo actualizar este cliente.</p>
                                <ul class="mt-1 list-disc space-y-1 pl-5 text-xs">
                                    <?php $__currentLoopData = $editErrorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($message); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="rounded-2xl border border-cyan-500/20 bg-cyan-500/5 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-cyan-200/80">Cliente seleccionado</p>
                            <p class="mt-2 text-lg font-black text-slate-100" x-text="editClientLabel()"></p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Nombre</span>
                                <input type="text"
                                       name="edit_first_name"
                                       x-model="editForm.first_name"
                                       x-on:blur="normalizeEditNameField('first_name')"
                                       x-on:input="clearEditFieldError('first_name')"
                                       class="ui-input"
                                       x-bind:class="editValidationErrors.first_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       x-ref="editFirstNameInput">
                                <p x-cloak x-show="editValidationErrors.first_name" class="text-xs font-semibold text-rose-300" x-text="editValidationErrors.first_name"></p>
                                <?php $__errorArgs = ['edit_first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Apellido</span>
                                <input type="text"
                                       name="edit_last_name"
                                       x-model="editForm.last_name"
                                       x-on:blur="normalizeEditNameField('last_name')"
                                       x-on:input="clearEditFieldError('last_name')"
                                       class="ui-input"
                                       x-bind:class="editValidationErrors.last_name ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''">
                                <p x-cloak x-show="editValidationErrors.last_name" class="text-xs font-semibold text-rose-300" x-text="editValidationErrors.last_name"></p>
                                <?php $__errorArgs = ['edit_last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                <span>Teléfono</span>
                                <input type="text"
                                       name="edit_phone"
                                       x-model="editForm.phone"
                                       x-on:input="clearEditFieldError('phone')"
                                       class="ui-input"
                                       x-bind:class="editValidationErrors.phone ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                       placeholder="Ej: 0991234567">
                                <p x-cloak x-show="editValidationErrors.phone" class="text-xs font-semibold text-rose-300" x-text="editValidationErrors.phone"></p>
                                <?php $__errorArgs = ['edit_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>
                        </div>
                    </div>

                    <footer class="ui-modal-sticky-footer flex items-center justify-end gap-3 px-5 py-4">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','xOn:click' => 'closeEditClient()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','x-on:click' => 'closeEditClient()']); ?>Cancelar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary','xBind:disabled' => 'editSubmitting']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','x-bind:disabled' => 'editSubmitting']); ?>Guardar cambios <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </footer>
                </form>
            </div>
        </div>

        <div x-cloak
             x-show="deleteModalOpen"
             x-transition.opacity
             class="ui-modal-backdrop items-start"
             x-on:click.self="closeDeleteClient()"
             x-on:keydown.escape.window="closeDeleteClient()">
            <div class="ui-modal-shell w-full max-w-xl"
                 x-transition.scale.origin.top
                 role="dialog"
                 aria-modal="true">
                <form method="POST"
                      x-bind:action="deleteForm.action || '#'"
                      novalidate
                      class="flex h-full min-h-0 flex-1 flex-col space-y-0"
                      x-on:submit="submitDeleteClient($event)">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <input type="hidden" name="_open_delete_modal" value="1">
                    <input type="hidden" name="delete_client_id" x-bind:value="deleteForm.id || ''">

                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 class="text-xl font-black text-slate-100">Eliminar cliente</h3>
                            <p class="mt-1 text-sm text-slate-400">Esta acción borrará también sus membresías y datos vinculados.</p>
                        </div>
                        <button type="button"
                                class="ui-button ui-button-ghost px-2 py-1 text-sm"
                                x-on:click="closeDeleteClient()"
                                aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body space-y-5 px-5 py-5">
                        <?php if($showDeleteErrorSummary): ?>
                            <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-200">
                                <p class="font-semibold">No se pudo eliminar este cliente.</p>
                                <ul class="mt-1 list-disc space-y-1 pl-5 text-xs">
                                    <?php $__currentLoopData = $deleteErrorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($message); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-rose-200/80">Cliente a eliminar</p>
                            <p class="mt-2 text-lg font-black text-white" x-text="deleteForm.full_name || 'Cliente sin nombre'"></p>
                            <p class="mt-3 text-sm text-rose-100/90" x-text="deleteForm.owner_modal_hint || 'Confirma con la contraseña del dueño autorizado.'"></p>
                        </div>

                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                            <span>Contraseña del <span class="lowercase" x-text="deleteForm.owner_scope_label || 'dueño del gimnasio'"></span></span>
                            <input type="password"
                                   name="owner_password"
                                   x-model="deleteForm.owner_password"
                                   x-on:input="clearDeleteFieldError('owner_password')"
                                   class="ui-input"
                                   x-bind:class="deleteValidationErrors.owner_password ? 'border-rose-400 focus:border-rose-400 focus:ring-rose-400/30' : ''"
                                   autocomplete="current-password"
                                   x-ref="deletePasswordInput">
                            <p x-cloak x-show="deleteValidationErrors.owner_password" class="text-xs font-semibold text-rose-300" x-text="deleteValidationErrors.owner_password"></p>
                            <?php $__errorArgs = ['owner_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                    </div>

                    <footer class="ui-modal-sticky-footer flex items-center justify-end gap-3 px-5 py-4">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','xOn:click' => 'closeDeleteClient()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','x-on:click' => 'closeDeleteClient()']); ?>Cancelar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-rose-400/35 bg-rose-500/90 px-4 py-2 text-sm font-bold text-white transition hover:bg-rose-500 disabled:cursor-not-allowed disabled:opacity-60"
                                x-bind:disabled="deleteSubmitting">
                            Eliminar cliente
                        </button>
                    </footer>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        window.clientsIndexPage = function clientsIndexPage(config) {
            const allowCreate = Boolean(config.allowCreate);
            return {
                allowCreate: allowCreate,
                canManageClientAccounts: Boolean(config.canManageClientAccounts),
                modalOpen: allowCreate && Boolean(config.openCreateModal),
                editModalOpen: Boolean(config.editModal?.open),
                deleteModalOpen: Boolean(config.deleteModal?.open),
                submitting: false,
                editSubmitting: false,
                deleteSubmitting: false,
                documentState: 'idle',
                documentMatchUrl: null,
                plans: Array.isArray(config.plans) ? config.plans : [],
                promotions: Array.isArray(config.promotions) ? config.promotions : [],
                documentCheckUrl: config.documentCheckUrl,
                clientValidationErrors: {},
                editValidationErrors: {},
                deleteValidationErrors: {},
                form: {
                    first_name: config.defaults?.first_name ?? '',
                    last_name: config.defaults?.last_name ?? '',
                    document_number: config.defaults?.document_number ?? '',
                    phone: config.defaults?.phone ?? '',
                    gender: config.defaults?.gender ?? 'neutral',
                    start_membership: Boolean(config.defaults?.start_membership),
                    plan_id: config.defaults?.plan_id ?? '',
                    membership_starts_at: config.defaults?.membership_starts_at ?? new Date().toISOString().slice(0, 10),
                    membership_price: config.defaults?.membership_price ?? '',
                    promotion_id: config.defaults?.promotion_id ?? '',
                    payment_method: config.defaults?.payment_method ?? 'cash',
                    amount_paid: config.defaults?.amount_paid ?? '',
                    create_app_account: Boolean(config.defaults?.create_app_account),
                    app_username: config.defaults?.app_username ?? '',
                    app_password: '',
                    app_password_confirmation: '',
                },
                editForm: {
                    action: config.editModal?.action ?? '',
                    id: config.editModal?.id ?? '',
                    first_name: config.editModal?.first_name ?? '',
                    last_name: config.editModal?.last_name ?? '',
                    phone: config.editModal?.phone ?? '',
                    full_name: config.editModal?.full_name ?? '',
                },
                deleteForm: {
                    action: config.deleteModal?.action ?? '',
                    id: config.deleteModal?.id ?? '',
                    full_name: config.deleteModal?.full_name ?? '',
                    owner_scope_label: config.deleteModal?.owner_scope_label ?? 'dueño del gimnasio',
                    owner_modal_hint: config.deleteModal?.owner_modal_hint ?? 'Confirma con la contraseña del dueño del gimnasio.',
                    owner_password: '',
                },
                photoPreview: null,
                membershipEndLabel: 'N/A',
                promotionSummaryLabel: '',

                init() {
                    this.recalculateMembershipEnd();
                    if (this.form.document_number !== '') {
                        this.checkDocument();
                    }

                    if (this.form.start_membership) {
                        this.onPlanChange();
                    }

                    if (this.modalOpen) {
                        this.$nextTick(() => this.$refs.firstNameInput?.focus());
                    }

                    if (this.editModalOpen) {
                        this.$nextTick(() => this.$refs.editFirstNameInput?.focus());
                    }

                    if (this.deleteModalOpen) {
                        this.$nextTick(() => this.$refs.deletePasswordInput?.focus());
                    }
                },

                openCreateClient() {
                    if (!this.allowCreate) {
                        return;
                    }
                    this.editModalOpen = false;
                    this.deleteModalOpen = false;
                    this.modalOpen = true;
                    this.$nextTick(() => this.$refs.firstNameInput?.focus());
                },

                closeCreateClient() {
                    if (this.submitting) {
                        return;
                    }
                    this.modalOpen = false;
                    this.clearClientValidationErrors();
                },

                editClientLabel() {
                    const fullName = String(this.editForm.full_name || '').trim();
                    if (fullName !== '') {
                        return fullName;
                    }

                    return `${this.editForm.first_name || ''} ${this.editForm.last_name || ''}`.trim() || 'Cliente sin nombre';
                },

                openEditClient(payload) {
                    this.modalOpen = false;
                    this.deleteModalOpen = false;
                    this.editSubmitting = false;
                    this.editValidationErrors = {};
                    this.editForm = {
                        action: payload?.action ?? '',
                        id: payload?.id ?? '',
                        first_name: payload?.first_name ?? '',
                        last_name: payload?.last_name ?? '',
                        phone: payload?.phone ?? '',
                        full_name: payload?.full_name ?? '',
                    };
                    this.editModalOpen = true;
                    this.$nextTick(() => this.$refs.editFirstNameInput?.focus());
                },

                closeEditClient() {
                    if (this.editSubmitting) {
                        return;
                    }

                    this.editModalOpen = false;
                    this.clearEditValidationErrors();
                },

                openDeleteClient(payload) {
                    this.modalOpen = false;
                    this.editModalOpen = false;
                    this.deleteSubmitting = false;
                    this.deleteValidationErrors = {};
                    this.deleteForm = {
                        action: payload?.action ?? '',
                        id: payload?.id ?? '',
                        full_name: payload?.full_name ?? '',
                        owner_scope_label: payload?.owner_scope_label ?? 'dueño del gimnasio',
                        owner_modal_hint: payload?.owner_modal_hint ?? 'Confirma con la contraseña del dueño del gimnasio.',
                        owner_password: '',
                    };
                    this.deleteModalOpen = true;
                    this.$nextTick(() => this.$refs.deletePasswordInput?.focus());
                },

                closeDeleteClient() {
                    if (this.deleteSubmitting) {
                        return;
                    }

                    this.deleteModalOpen = false;
                    this.clearDeleteValidationErrors();
                    this.deleteForm.owner_password = '';
                },

                avatarInitials() {
                    const first = (this.form.first_name || '').trim().charAt(0);
                    const last = (this.form.last_name || '').trim().charAt(0);
                    const initials = `${first}${last}`.trim().toUpperCase();
                    return initials !== '' ? initials : '--';
                },

                onPhotoSelected(event) {
                    const [file] = event.target.files || [];
                    if (!file) {
                        this.photoPreview = null;
                        return;
                    }

                    this.photoPreview = URL.createObjectURL(file);
                },

                formatPersonName(value) {
                    const raw = String(value ?? '').trim().replace(/\s+/g, ' ');
                    if (raw === '') {
                        return '';
                    }

                    return raw
                        .split(/(\s+|-|')/u)
                        .map((segment) => {
                            if (segment === '' || /^(\s+|-|')$/u.test(segment)) {
                                return segment;
                            }

                            const chars = Array.from(segment);
                            const [first, ...rest] = chars;
                            return first.toLocaleUpperCase('es-ES') + rest.join('').toLocaleLowerCase('es-ES');
                        })
                        .join('');
                },

                normalizeNameField(field) {
                    if (field !== 'first_name' && field !== 'last_name') {
                        return;
                    }

                    this.form[field] = this.formatPersonName(this.form[field]);
                },

                clearClientFieldError(field) {
                    if (!field) {
                        return;
                    }

                    delete this.clientValidationErrors[field];
                },

                clearClientValidationErrors() {
                    this.clientValidationErrors = {};
                },

                clearEditFieldError(field) {
                    if (!field) {
                        return;
                    }

                    delete this.editValidationErrors[field];
                },

                clearEditValidationErrors() {
                    this.editValidationErrors = {};
                },

                setEditFieldError(field, message) {
                    if (!field || !message) {
                        return;
                    }

                    this.editValidationErrors[field] = message;
                },

                clearDeleteFieldError(field) {
                    if (!field) {
                        return;
                    }

                    delete this.deleteValidationErrors[field];
                },

                clearDeleteValidationErrors() {
                    this.deleteValidationErrors = {};
                },

                setDeleteFieldError(field, message) {
                    if (!field || !message) {
                        return;
                    }

                    this.deleteValidationErrors[field] = message;
                },

                setClientFieldError(field, message) {
                    if (!field || !message) {
                        return;
                    }

                    this.clientValidationErrors[field] = message;
                },

                isSequentialDigits(value) {
                    const text = String(value || '');
                    if (text.length < 6 || text.length > 10) {
                        return false;
                    }

                    return '0123456789'.includes(text) || '9876543210'.includes(text);
                },

                validateDocumentField() {
                    const raw = String(this.form.document_number || '').trim();
                    const canonical = raw.toUpperCase().replace(/[-\s]/g, '');

                    if (raw === '') {
                        return 'Ingresa el documento del cliente.';
                    }

                    if (!/^[A-Za-z0-9\- ]+$/.test(raw)) {
                        return 'El documento solo puede usar letras, números, espacios y guion.';
                    }

                    if (canonical.length < 6 || canonical.length > 20) {
                        return 'El documento debe tener entre 6 y 20 caracteres utiles.';
                    }

                    if (!/\d/.test(canonical)) {
                        return 'El documento debe incluir al menos un número.';
                    }

                    if (/^(.)\1+$/.test(canonical)) {
                        return 'El documento ingresado no parece válido.';
                    }

                    if (/^\d+$/.test(canonical) && this.isSequentialDigits(canonical)) {
                        return 'El documento ingresado no parece válido.';
                    }

                    return '';
                },

                validatePhoneField() {
                    return this.validatePhoneValue(this.form.phone);
                },

                validatePhoneValue(value) {
                    const raw = String(value || '').trim();
                    const digits = raw.replace(/\D/g, '');

                    if (raw === '') {
                        return 'Ingresa el teléfono del cliente.';
                    }

                    if (!/^[0-9+\-\s()]+$/.test(raw)) {
                        return 'El teléfono solo puede contener números y los símbolos + - ( ).';
                    }

                    if (digits.length < 7 || digits.length > 15) {
                        return 'El teléfono debe tener entre 7 y 15 dígitos.';
                    }

                    if (/^(\d)\1+$/.test(digits)) {
                        return 'El teléfono ingresado no parece válido.';
                    }

                    if (this.isSequentialDigits(digits)) {
                        return 'El teléfono ingresado no parece válido.';
                    }

                    return '';
                },

                validateAppUsernameField() {
                    const username = String(this.form.app_username || '').trim().toLowerCase();
                    if (username === '') {
                        return 'Ingresa el usuario para la app cliente.';
                    }

                    if (username.length < 4 || username.length > 80) {
                        return 'El usuario debe tener entre 4 y 80 caracteres.';
                    }

                    if (!/^[a-z0-9._-]+$/.test(username)) {
                        return 'El usuario solo puede usar letras minúsculas, números, punto, guion y guion bajo.';
                    }

                    return '';
                },

                validateCreateClientForm() {
                    this.clearClientValidationErrors();

                    const firstName = String(this.form.first_name || '').trim();
                    const lastName = String(this.form.last_name || '').trim();
                    const documentError = this.validateDocumentField();
                    const phoneError = this.validatePhoneField();

                    if (firstName === '') {
                        this.setClientFieldError('first_name', 'Ingresa el nombre del cliente.');
                    }

                    if (lastName === '') {
                        this.setClientFieldError('last_name', 'Ingresa el apellido del cliente.');
                    }

                    if (documentError !== '') {
                        this.setClientFieldError('document_number', documentError);
                    } else if (this.documentState === 'exists') {
                        this.setClientFieldError('document_number', 'Este documento ya está registrado en este gimnasio.');
                    }

                    if (phoneError !== '') {
                        this.setClientFieldError('phone', phoneError);
                    }

                    if (this.canManageClientAccounts && this.form.create_app_account) {
                        const appUsernameError = this.validateAppUsernameField();
                        const appPassword = String(this.form.app_password || '');
                        const appPasswordConfirmation = String(this.form.app_password_confirmation || '');

                        if (appUsernameError !== '') {
                            this.setClientFieldError('app_username', appUsernameError);
                        }

                        if (appPassword.length < 8) {
                            this.setClientFieldError('app_password', 'La contraseña debe tener al menos 8 caracteres.');
                        }

                        if (appPasswordConfirmation === '') {
                            this.setClientFieldError('app_password_confirmation', 'Confirma la contraseña de la app cliente.');
                        } else if (appPasswordConfirmation !== appPassword) {
                            this.setClientFieldError('app_password_confirmation', 'La confirmación de contraseña no coincide.');
                        }
                    }

                    return Object.keys(this.clientValidationErrors).length === 0;
                },

                normalizeEditNameField(field) {
                    if (field !== 'first_name' && field !== 'last_name') {
                        return;
                    }

                    this.editForm[field] = this.formatPersonName(this.editForm[field]);
                },

                validateEditClientForm() {
                    this.clearEditValidationErrors();

                    const firstName = String(this.editForm.first_name || '').trim();
                    const lastName = String(this.editForm.last_name || '').trim();
                    const phoneError = this.validatePhoneValue(this.editForm.phone);

                    if (firstName === '') {
                        this.setEditFieldError('first_name', 'Ingresa el nombre del cliente.');
                    }

                    if (lastName === '') {
                        this.setEditFieldError('last_name', 'Ingresa el apellido del cliente.');
                    }

                    if (phoneError !== '') {
                        this.setEditFieldError('phone', phoneError);
                    }

                    return Object.keys(this.editValidationErrors).length === 0;
                },

                focusFirstEditValidationError() {
                    if (this.editValidationErrors.first_name) {
                        this.$refs.editFirstNameInput?.focus();
                        return;
                    }

                    const fieldOrder = ['last_name', 'phone'];
                    for (const fieldName of fieldOrder) {
                        if (!this.editValidationErrors[fieldName]) {
                            continue;
                        }

                        const input = this.$el.querySelector(`[name="edit_${fieldName}"]`);
                        if (input) {
                            input.focus();
                        }
                        return;
                    }
                },

                submitEditClient(event) {
                    this.normalizeEditNameField('first_name');
                    this.normalizeEditNameField('last_name');
                    this.editSubmitting = false;

                    if (!this.validateEditClientForm()) {
                        event.preventDefault();
                        this.focusFirstEditValidationError();
                        return;
                    }

                    this.editSubmitting = true;
                },

                validateDeleteClientForm() {
                    this.clearDeleteValidationErrors();

                    if (String(this.deleteForm.owner_password || '').trim() === '') {
                        this.setDeleteFieldError('owner_password', 'Ingresa la contraseña del dueño autorizado.');
                    }

                    return Object.keys(this.deleteValidationErrors).length === 0;
                },

                submitDeleteClient(event) {
                    this.deleteSubmitting = false;

                    if (!this.validateDeleteClientForm()) {
                        event.preventDefault();
                        this.$refs.deletePasswordInput?.focus();
                        return;
                    }

                    this.deleteSubmitting = true;
                },

                focusFirstClientValidationError() {
                    if (this.clientValidationErrors.first_name) {
                        this.$refs.firstNameInput?.focus();
                        return;
                    }

                    const fieldOrder = ['last_name', 'document_number', 'phone', 'app_username', 'app_password', 'app_password_confirmation'];
                    for (const fieldName of fieldOrder) {
                        if (!this.clientValidationErrors[fieldName]) {
                            continue;
                        }

                        const input = this.$el.querySelector(`[name="${fieldName}"]`);
                        if (input) {
                            input.focus();
                        }
                        return;
                    }
                },

                submitCreateClient(event) {
                    this.normalizeNameField('first_name');
                    this.normalizeNameField('last_name');
                    if (this.canManageClientAccounts) {
                        this.form.app_username = String(this.form.app_username || '').trim().toLowerCase();
                        if (!this.form.create_app_account) {
                            this.form.app_username = '';
                            this.form.app_password = '';
                            this.form.app_password_confirmation = '';
                        }
                    }
                    this.submitting = false;

                    if (!this.validateCreateClientForm()) {
                        event.preventDefault();
                        this.focusFirstClientValidationError();
                        return;
                    }

                    this.submitting = true;
                },

                normalizePlanDurationUnit(unit) {
                    return String(unit || '').toLowerCase() === 'months' ? 'months' : 'days';
                },

                addMonthsNoOverflow(baseDate, monthsToAdd) {
                    const months = Math.max(1, Number(monthsToAdd || 1));
                    const baseDay = baseDate.getDate();
                    const baseMonthIndex = baseDate.getMonth() + months;
                    const targetYear = baseDate.getFullYear() + Math.floor(baseMonthIndex / 12);
                    const targetMonth = ((baseMonthIndex % 12) + 12) % 12;
                    const targetLastDay = new Date(targetYear, targetMonth + 1, 0).getDate();
                    const targetDay = Math.min(baseDay, targetLastDay);

                    return new Date(targetYear, targetMonth, targetDay);
                },

                computeMembershipEndDate(startDate, plan, bonusDays) {
                    const unit = this.normalizePlanDurationUnit(plan.duration_unit);
                    const safeBonusDays = Math.max(0, Math.round(Number(bonusDays || 0)));
                    let endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());

                    if (unit === 'months') {
                        const months = Math.max(1, Math.round(Number(plan.duration_months || 1)));
                        endDate = this.addMonthsNoOverflow(startDate, months);
                    } else {
                        const days = Math.max(1, Math.round(Number(plan.duration_days || 1)));
                        endDate.setDate(endDate.getDate() + days - 1);
                    }

                    if (safeBonusDays > 0) {
                        endDate.setDate(endDate.getDate() + safeBonusDays);
                    }

                    return endDate;
                },

                availablePromotions() {
                    const planId = String(this.form.plan_id || '');
                    const startDate = this.form.membership_starts_at || new Date().toISOString().slice(0, 10);

                    return this.promotions.filter((promo) => {
                        const promoPlanId = promo.plan_id !== null ? String(promo.plan_id) : '';
                        const isPlanMatch = promoPlanId === '' || promoPlanId === planId;
                        const fromOk = !promo.starts_at || promo.starts_at <= startDate;
                        const toOk = !promo.ends_at || promo.ends_at >= startDate;
                        const usesOk = promo.max_uses === null || Number(promo.times_used) < Number(promo.max_uses);

                        return isPlanMatch && fromOk && toOk && usesOk;
                    });
                },

                selectedPromotion() {
                    const promotionId = String(this.form.promotion_id || '');
                    if (promotionId === '') {
                        return null;
                    }

                    return this.availablePromotions().find((promo) => String(promo.id) === promotionId) || null;
                },

                promotionOptionLabel(promo) {
                    const value = Number(promo.value || 0);
                    const byType = {
                        percentage: `-${value}%`,
                        fixed: `-${value.toFixed(2)}`,
                        final_price: `Precio final ${value.toFixed(2)}`,
                        bonus_days: `+${Math.max(0, Math.round(value))} días`,
                        two_for_one: '2x1',
                        bring_friend: 'Trae a un amigo',
                    };
                    return `${promo.name} (${byType[promo.type] || promo.type})`;
                },

                computePromotionalPrice(planPrice, promo) {
                    const base = Math.max(0, Number(planPrice || 0));
                    if (!promo) {
                        return { finalPrice: base, bonusDays: 0, summary: '' };
                    }

                    const value = Number(promo.value || 0);
                    let finalPrice = base;
                    let bonusDays = 0;
                    let summary = promo.name;

                    if (promo.type === 'percentage') {
                        const percent = Math.min(Math.max(value, 0), 100);
                        finalPrice = Math.max(0, base - (base * percent / 100));
                        summary += `: -${percent}%`;
                    } else if (promo.type === 'fixed') {
                        finalPrice = Math.max(0, base - Math.max(0, value));
                        summary += `: -${value.toFixed(2)}`;
                    } else if (promo.type === 'final_price') {
                        finalPrice = Math.max(0, value);
                        summary += `: precio final ${finalPrice.toFixed(2)}`;
                    } else if (promo.type === 'bonus_days') {
                        bonusDays = Math.max(0, Math.round(value));
                        summary += `: +${bonusDays} días`;
                    } else if (promo.type === 'two_for_one' || promo.type === 'bring_friend') {
                        const percent = value > 0 ? Math.min(Math.max(value, 0), 100) : 50;
                        finalPrice = Math.max(0, base - (base * percent / 100));
                        summary += `: -${percent}%`;
                    }

                    return {
                        finalPrice: Number(finalPrice.toFixed(2)),
                        bonusDays,
                        summary,
                    };
                },

                onMembershipToggle() {
                    if (!this.form.start_membership) {
                        this.form.promotion_id = '';
                        this.promotionSummaryLabel = '';
                        return;
                    }

                    this.onPlanChange();
                    this.recalculateMembershipEnd();
                },

                onPromotionChange() {
                    this.onPlanChange();
                },

                onPlanChange() {
                    const plan = this.plans.find((item) => String(item.id) === String(this.form.plan_id));
                    if (!plan) {
                        this.form.promotion_id = '';
                        this.promotionSummaryLabel = '';
                        this.recalculateMembershipEnd();
                        return;
                    }

                    const promo = this.selectedPromotion();
                    const pricing = this.computePromotionalPrice(plan.price, promo);

                    this.form.membership_price = Number(pricing.finalPrice).toFixed(2);
                    this.form.amount_paid = Number(pricing.finalPrice).toFixed(2);
                    this.promotionSummaryLabel = pricing.summary;

                    if (this.form.promotion_id !== '' && !promo) {
                        this.form.promotion_id = '';
                        this.promotionSummaryLabel = '';
                    }

                    this.recalculateMembershipEnd();
                },

                recalculateMembershipEnd() {
                    const plan = this.plans.find((item) => String(item.id) === String(this.form.plan_id));
                    const start = this.form.membership_starts_at;
                    const promo = this.selectedPromotion();
                    const promoPricing = plan ? this.computePromotionalPrice(plan.price, promo) : { bonusDays: 0 };

                    if (!plan || !start) {
                        this.membershipEndLabel = 'N/A';
                        return;
                    }

                    const startDate = new Date(`${start}T00:00:00`);
                    if (Number.isNaN(startDate.getTime())) {
                        this.membershipEndLabel = 'N/A';
                        return;
                    }

                    const endDate = this.computeMembershipEndDate(startDate, plan, promoPricing.bonusDays);
                    this.membershipEndLabel = endDate.toLocaleDateString('es-EC', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    });
                },

                async checkDocument() {
                    const value = (this.form.document_number || '').trim();
                    this.documentMatchUrl = null;

                    if (value === '') {
                        this.documentState = 'idle';
                        return;
                    }

                    this.documentState = 'checking';

                    try {
                        const url = new URL(this.documentCheckUrl, window.location.origin);
                        url.searchParams.set('document_number', value);

                        const response = await fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            this.documentState = 'idle';
                            return;
                        }

                        const payload = await response.json();
                        if (payload.exists) {
                            this.documentState = 'exists';
                            this.documentMatchUrl = payload.show_url || null;
                            return;
                        }

                        this.documentState = 'available';
                    } catch (error) {
                        this.documentState = 'idle';
                    }
                },

                get membershipBadgeLabel() {
                    if (!this.form.start_membership) {
                        return 'Sin membresía';
                    }

                    if (this.membershipEndLabel === 'N/A') {
                        return 'Pendiente de datos';
                    }

                    return 'Activa (automática)';
                },

                get paymentStatusLabel() {
                    const price = Number(this.form.membership_price || 0);
                    const paid = Number(this.form.amount_paid || 0);

                    if (price <= 0) {
                        return 'Pendiente';
                    }

                    return paid >= price ? 'AL DÍA' : 'PENDIENTE';
                },

                get paymentBadgeClass() {
                    return this.paymentStatusLabel === 'AL DÍA'
                        ? 'border border-emerald-400/40 bg-emerald-500/20 text-emerald-200'
                        : 'border border-amber-400/40 bg-amber-500/20 text-amber-200';
                },
            };
        };
    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/index.blade.php ENDPATH**/ ?>