<?php $__env->startSection('title', 'Reportes'); ?>
<?php $__env->startSection('page-title', 'Reportes'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .report-hub .report-filter-form {
        align-items: end;
    }

    .report-hub-filter,
    .report-nav-card {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border-color: rgb(148 163 184 / 0.22);
        background: linear-gradient(162deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 26px 46px -38px rgb(15 23 42 / 0.34), inset 0 1px 0 rgb(255 255 255 / 0.82);
        backdrop-filter: blur(14px);
    }

    .theme-dark .report-hub-filter,
    .theme-dark .report-nav-card,
    .dark .report-hub-filter,
    .dark .report-nav-card {
        border-color: rgb(71 85 105 / 0.74);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.84));
        box-shadow: 0 30px 48px -36px rgb(2 8 23 / 0.9), inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-hub-filter::before,
    .report-nav-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, rgb(255 255 255 / 0.78), transparent 76%);
        opacity: 0.88;
        pointer-events: none;
    }

    .theme-dark .report-hub-filter::before,
    .theme-dark .report-nav-card::before,
    .dark .report-hub-filter::before,
    .dark .report-nav-card::before {
        background: linear-gradient(90deg, rgb(255 255 255 / 0.08), transparent 76%);
    }

    .report-hub .report-filter-form label {
        display: flex;
        flex-direction: column;
        gap: 0.38rem;
    }

    .report-hub .report-filter-form .ui-input {
        min-height: 2.85rem;
        border-color: rgb(148 163 184 / 0.24);
        background: rgb(255 255 255 / 0.76);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.72);
    }

    .theme-dark .report-hub .report-filter-form .ui-input,
    .dark .report-hub .report-filter-form .ui-input {
        border-color: rgb(71 85 105 / 0.72);
        background: rgb(15 23 42 / 0.7);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-hub .report-filter-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.65rem;
        min-height: 2.85rem;
        padding: 0.72rem 0.8rem;
        border: 1px solid rgb(148 163 184 / 0.2);
        border-radius: 1rem;
        background: linear-gradient(160deg, rgb(255 255 255 / 0.74), rgb(241 245 249 / 0.84));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.7);
    }

    .theme-dark .report-hub .report-filter-toolbar,
    .dark .report-hub .report-filter-toolbar {
        border-color: rgb(71 85 105 / 0.68);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.7), rgb(2 6 23 / 0.6));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-control-shell {
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

    .theme-dark .report-control-shell,
    .dark .report-control-shell {
        border-color: rgb(163 230 53 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.14), transparent 34%),
            linear-gradient(160deg, rgb(2 6 23 / 0.84), rgb(15 23 42 / 0.62));
        box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.92);
    }

    .report-control-shell::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
        opacity: 0.8;
        pointer-events: none;
    }

    .report-control-shell::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(95deg, transparent, rgb(163 230 53 / 0.05), transparent);
    }

    .report-control-grid {
        display: grid;
        gap: 1.05rem;
        position: relative;
        z-index: 1;
    }

    .report-control-copy {
        max-width: 48rem;
    }

    .report-control-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.17em;
        text-transform: uppercase;
        color: rgb(77 124 15 / 0.94);
    }

    .theme-dark .report-control-kicker,
    .dark .report-control-kicker {
        color: rgb(217 249 157 / 0.94);
    }

    .report-control-kicker::before {
        content: '';
        width: 0.52rem;
        height: 0.52rem;
        border-radius: 999px;
        background: rgb(132 204 22 / 0.94);
        box-shadow: 0 0 0 6px rgb(132 204 22 / 0.12);
    }

    .report-control-heading {
        margin-top: 0.78rem;
        font-size: clamp(1.14rem, 1.85vw, 1.46rem);
        line-height: 1.08;
        letter-spacing: -0.035em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.97);
    }

    .theme-dark .report-control-heading,
    .dark .report-control-heading {
        color: rgb(241 245 249 / 0.98);
    }

    .report-control-summary {
        margin-top: 0.5rem;
        font-size: 0.88rem;
        line-height: 1.58;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .report-control-summary,
    .dark .report-control-summary {
        color: rgb(148 163 184 / 0.9);
    }

    .report-control-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .report-control-actions .ui-button {
        min-height: 2.72rem;
    }

    .report-control-priority-grid {
        display: grid;
        gap: 0.75rem;
    }

    .report-control-priority {
        position: relative;
        overflow: hidden;
        border-radius: 1.05rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        min-height: 7rem;
        padding: 0.9rem 0.95rem;
    }

    .theme-dark .report-control-priority,
    .dark .report-control-priority {
        border-color: rgb(148 163 184 / 0.18);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
    }

    .report-control-priority::before {
        content: '';
        position: absolute;
        left: 0.9rem;
        right: 0.9rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .report-control-priority[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
    }

    .report-control-priority[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
    }

    .report-control-priority[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(6 182 212 / 0.9), rgb(6 182 212 / 0.24));
    }

    .report-control-priority-label {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .report-control-priority-label,
    .dark .report-control-priority-label {
        color: rgb(148 163 184 / 0.9);
    }

    .report-control-priority-value {
        margin-top: 0.42rem;
        font-size: 1.46rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.97);
    }

    .theme-dark .report-control-priority-value,
    .dark .report-control-priority-value {
        color: rgb(248 250 252 / 0.98);
    }

    .report-control-priority-note {
        margin-top: 0.4rem;
        font-size: 0.75rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .report-control-priority-note,
    .dark .report-control-priority-note {
        color: rgb(148 163 184 / 0.88);
    }

    .report-pro-shell {
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

    .theme-dark .report-pro-shell,
    .dark .report-pro-shell {
        border-color: rgb(34 211 238 / 0.22);
        background:
            radial-gradient(circle at top right, rgb(34 211 238 / 0.11), transparent 34%),
            radial-gradient(circle at bottom left, rgb(245 158 11 / 0.08), transparent 28%),
            linear-gradient(155deg, rgb(4 10 28 / 0.94), rgb(11 18 32 / 0.88));
        box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.9);
    }

    .report-pro-shell::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
        opacity: 0.8;
        pointer-events: none;
    }

    .report-pro-shell::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(95deg, transparent, rgb(34 211 238 / 0.04), transparent);
    }

    .report-pro-grid {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1rem;
    }

    .report-pro-copy {
        max-width: 50rem;
    }

    .report-pro-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.17em;
        text-transform: uppercase;
        color: rgb(8 145 178 / 0.96);
    }

    .theme-dark .report-pro-kicker,
    .dark .report-pro-kicker {
        color: rgb(165 243 252 / 0.94);
    }

    .report-pro-kicker::before {
        content: '';
        width: 0.52rem;
        height: 0.52rem;
        border-radius: 999px;
        background: rgb(34 211 238 / 0.96);
        box-shadow: 0 0 0 6px rgb(34 211 238 / 0.14);
    }

    .report-pro-heading {
        margin-top: 0.78rem;
        font-size: clamp(1.14rem, 1.85vw, 1.46rem);
        line-height: 1.08;
        letter-spacing: -0.035em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.97);
    }

    .theme-dark .report-pro-heading,
    .dark .report-pro-heading {
        color: rgb(241 245 249 / 0.98);
    }

    .report-pro-summary {
        margin-top: 0.5rem;
        font-size: 0.88rem;
        line-height: 1.58;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .report-pro-summary,
    .dark .report-pro-summary {
        color: rgb(148 163 184 / 0.9);
    }

    .report-pro-badge {
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

    .theme-dark .report-pro-badge,
    .dark .report-pro-badge {
        border-color: rgb(34 211 238 / 0.26);
        background: rgb(8 145 178 / 0.12);
        color: rgb(165 243 252 / 0.95);
    }

    .report-pro-metrics {
        display: grid;
        gap: 0.75rem;
    }

    .report-pro-metric {
        position: relative;
        overflow: hidden;
        border-radius: 1.02rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        min-height: 6.7rem;
        padding: 0.9rem 0.95rem;
    }

    .theme-dark .report-pro-metric,
    .dark .report-pro-metric {
        border-color: rgb(148 163 184 / 0.16);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
    }

    .report-pro-metric::before {
        content: '';
        position: absolute;
        left: 0.9rem;
        right: 0.9rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .report-pro-metric[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
    }

    .report-pro-metric[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(34 211 238 / 0.9), rgb(34 211 238 / 0.24));
    }

    .report-pro-metric[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
    }

    .report-pro-metric[data-tone='accent']::before {
        background: linear-gradient(90deg, rgb(168 85 247 / 0.9), rgb(168 85 247 / 0.24));
    }

    .report-pro-metric-label {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .report-pro-metric-label,
    .dark .report-pro-metric-label {
        color: rgb(148 163 184 / 0.9);
    }

    .report-pro-metric-value {
        margin-top: 0.42rem;
        font-size: 1.46rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.97);
    }

    .theme-dark .report-pro-metric-value,
    .dark .report-pro-metric-value {
        color: rgb(248 250 252 / 0.98);
    }

    .report-pro-metric-note {
        margin-top: 0.4rem;
        font-size: 0.75rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .report-pro-metric-note,
    .dark .report-pro-metric-note {
        color: rgb(148 163 184 / 0.88);
    }

    .report-pro-insights {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .report-pro-chip {
        min-width: min(100%, 14rem);
        flex: 1 1 14rem;
        border-radius: 0.95rem;
        border: 1px solid rgb(148 163 184 / 0.2);
        background: rgb(255 255 255 / 0.66);
        padding: 0.78rem 0.85rem;
        box-shadow: 0 16px 28px -30px rgb(15 23 42 / 0.32);
    }

    .theme-dark .report-pro-chip,
    .dark .report-pro-chip {
        border-color: rgb(148 163 184 / 0.14);
        background: rgb(15 23 42 / 0.58);
        box-shadow: 0 20px 30px -30px rgb(2 8 23 / 0.9);
    }

    .report-pro-chip[data-tone='warning'] {
        border-color: rgb(245 158 11 / 0.22);
        background: rgb(255 251 235 / 0.9);
    }

    .report-pro-chip[data-tone='success'] {
        border-color: rgb(16 185 129 / 0.22);
        background: rgb(236 253 245 / 0.9);
    }

    .report-pro-chip[data-tone='info'] {
        border-color: rgb(34 211 238 / 0.22);
        background: rgb(236 254 255 / 0.9);
    }

    .theme-dark .report-pro-chip[data-tone='warning'],
    .dark .report-pro-chip[data-tone='warning'] {
        background: rgb(120 53 15 / 0.18);
    }

    .theme-dark .report-pro-chip[data-tone='success'],
    .dark .report-pro-chip[data-tone='success'] {
        background: rgb(6 78 59 / 0.18);
    }

    .theme-dark .report-pro-chip[data-tone='info'],
    .dark .report-pro-chip[data-tone='info'] {
        background: rgb(8 145 178 / 0.14);
    }

    .report-pro-chip-title {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(30 41 59 / 0.92);
    }

    .theme-dark .report-pro-chip-title,
    .dark .report-pro-chip-title {
        color: rgb(226 232 240 / 0.96);
    }

    .report-pro-chip-copy {
        margin-top: 0.35rem;
        font-size: 0.76rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .report-pro-chip-copy,
    .dark .report-pro-chip-copy {
        color: rgb(148 163 184 / 0.88);
    }

    .report-pro-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .report-pro-actions .ui-button {
        min-height: 2.72rem;
    }

    .report-elite-shell.report-pro-shell {
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

    .theme-dark .report-elite-shell.report-pro-shell,
    .dark .report-elite-shell.report-pro-shell {
        border-color: rgb(234 179 8 / 0.28);
        background:
            radial-gradient(circle at top right, rgb(234 179 8 / 0.16), transparent 36%),
            radial-gradient(circle at bottom left, rgb(16 185 129 / 0.11), transparent 30%),
            linear-gradient(155deg, rgb(10 12 24 / 0.96), rgb(17 24 39 / 0.92));
        box-shadow:
            0 36px 72px -46px rgb(2 8 23 / 0.92),
            inset 0 1px 0 rgb(255 255 255 / 0.05);
    }

    .report-elite-shell.report-pro-shell::after {
        background: linear-gradient(100deg, transparent 8%, rgb(234 179 8 / 0.08), transparent 74%);
    }

    .report-elite-shell .report-pro-grid {
        gap: 0.82rem;
    }

    .report-elite-shell .report-elite-head {
        align-items: end;
        gap: 1rem;
    }

    .report-elite-shell .report-pro-copy {
        max-width: 48rem;
    }

    .report-elite-shell .report-pro-kicker {
        color: rgb(161 98 7 / 0.96);
        letter-spacing: 0.15em;
    }

    .theme-dark .report-elite-shell .report-pro-kicker,
    .dark .report-elite-shell .report-pro-kicker {
        color: rgb(253 224 71 / 0.94);
    }

    .report-elite-shell .report-pro-kicker::before {
        background: rgb(234 179 8 / 0.96);
        box-shadow: 0 0 0 6px rgb(234 179 8 / 0.14);
    }

    .report-elite-shell .report-pro-badge {
        border-color: rgb(234 179 8 / 0.24);
        background: rgb(254 249 195 / 0.84);
        color: rgb(161 98 7 / 0.96);
        padding: 0.48rem 0.92rem;
        box-shadow: 0 14px 30px -24px rgb(161 98 7 / 0.28);
    }

    .theme-dark .report-elite-shell .report-pro-badge,
    .dark .report-elite-shell .report-pro-badge {
        border-color: rgb(234 179 8 / 0.26);
        background: rgb(161 98 7 / 0.12);
        color: rgb(253 224 71 / 0.95);
        box-shadow: 0 14px 32px -24px rgb(234 179 8 / 0.2);
    }

    .report-elite-shell .report-pro-heading {
        margin-top: 0.42rem;
        max-width: 24ch;
        font-size: clamp(1.08rem, 1.55vw, 1.36rem);
        line-height: 1.04;
    }

    .report-elite-shell .report-pro-summary {
        max-width: 36rem;
        margin-top: 0.38rem;
        font-size: 0.82rem;
        line-height: 1.42;
        color: rgb(71 85 105 / 0.96);
    }

    .theme-dark .report-elite-shell .report-pro-summary,
    .dark .report-elite-shell .report-pro-summary {
        color: rgb(203 213 225 / 0.82);
    }

    .report-elite-shell .report-pro-metrics {
        gap: 0.68rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .report-elite-shell .report-pro-metric {
        min-height: auto;
        padding: 0.82rem 0.92rem;
        border-color: rgb(234 179 8 / 0.16);
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.94), rgb(248 250 252 / 0.82));
        box-shadow:
            0 20px 34px -28px rgb(120 53 15 / 0.14),
            inset 0 1px 0 rgb(255 255 255 / 0.86);
    }

    .theme-dark .report-elite-shell .report-pro-metric,
    .dark .report-elite-shell .report-pro-metric {
        border-color: rgb(234 179 8 / 0.14);
        background:
            linear-gradient(165deg, rgb(15 23 42 / 0.82), rgb(15 23 42 / 0.62));
        box-shadow:
            0 22px 38px -30px rgb(2 8 23 / 0.9),
            inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-elite-shell .report-pro-metric:first-child {
        border-color: rgb(234 179 8 / 0.26);
        background:
            radial-gradient(circle at top right, rgb(234 179 8 / 0.18), transparent 44%),
            linear-gradient(180deg, rgb(255 251 235 / 0.96), rgb(255 255 255 / 0.84));
        grid-column: span 1;
    }

    .theme-dark .report-elite-shell .report-pro-metric:first-child,
    .dark .report-elite-shell .report-pro-metric:first-child {
        border-color: rgb(234 179 8 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(234 179 8 / 0.15), transparent 44%),
            linear-gradient(165deg, rgb(31 41 55 / 0.9), rgb(15 23 42 / 0.74));
    }

    .report-elite-shell .report-pro-metric-label {
        color: rgb(120 53 15 / 0.86);
    }

    .theme-dark .report-elite-shell .report-pro-metric-label,
    .dark .report-elite-shell .report-pro-metric-label {
        color: rgb(253 224 71 / 0.8);
    }

    .report-elite-shell .report-pro-metric-value {
        margin-top: 0.42rem;
        font-size: clamp(1.32rem, 2vw, 1.72rem);
        line-height: 1;
    }

    .report-elite-shell .report-pro-chip {
        border-color: rgb(234 179 8 / 0.16);
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.84), rgb(255 255 255 / 0.7));
        box-shadow:
            0 18px 30px -28px rgb(120 53 15 / 0.12),
            inset 0 1px 0 rgb(255 255 255 / 0.82);
    }

    .theme-dark .report-elite-shell .report-pro-chip,
    .dark .report-elite-shell .report-pro-chip {
        border-color: rgb(234 179 8 / 0.12);
        background:
            linear-gradient(165deg, rgb(15 23 42 / 0.72), rgb(15 23 42 / 0.54));
        box-shadow:
            0 20px 34px -28px rgb(2 8 23 / 0.84),
            inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-elite-shell .report-pro-chip-title {
        color: rgb(120 53 15 / 0.9);
    }

    .theme-dark .report-elite-shell .report-pro-chip-title,
    .dark .report-elite-shell .report-pro-chip-title {
        color: rgb(253 224 71 / 0.82);
    }

    .report-elite-shell .report-pro-actions {
        gap: 0.55rem;
        align-items: center;
        padding-top: 0.72rem;
        border-top: 1px solid rgb(234 179 8 / 0.18);
    }

    .theme-dark .report-elite-shell .report-pro-actions,
    .dark .report-elite-shell .report-pro-actions {
        border-top-color: rgb(234 179 8 / 0.12);
    }

    .report-elite-shell .report-pro-actions .ui-button {
        min-height: 2.6rem;
        border-radius: 0.98rem;
        box-shadow: 0 16px 28px -24px rgb(15 23 42 / 0.32);
    }

    .report-elite-shell .report-pro-actions .ui-button:first-child {
        border-color: rgb(234 179 8 / 0.42);
        background: linear-gradient(135deg, rgb(250 204 21), rgb(16 185 129));
        color: rgb(6 23 18);
        box-shadow: 0 20px 36px -24px rgb(16 185 129 / 0.38);
    }

    .theme-dark .report-elite-shell .report-pro-actions .ui-button:first-child,
    .dark .report-elite-shell .report-pro-actions .ui-button:first-child {
        color: rgb(4 12 16);
    }

    .report-elite-shell .report-pro-actions .ui-button:not(:first-child) {
        background: rgb(255 255 255 / 0.54);
        border-color: rgb(234 179 8 / 0.14);
    }

    .theme-dark .report-elite-shell .report-pro-actions .ui-button:not(:first-child),
    .dark .report-elite-shell .report-pro-actions .ui-button:not(:first-child) {
        background: rgb(15 23 42 / 0.42);
        border-color: rgb(234 179 8 / 0.12);
    }

    .report-elite-shell .report-pro-insights {
        display: none;
    }

    @media (max-width: 900px) {
        .report-elite-shell .report-pro-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .report-elite-shell .report-pro-metrics {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .report-hub .report-module-grid,
    .report-hub .report-chart-grid {
        align-items: stretch;
    }

    .report-hub .report-module-card,
    .report-hub .report-kpi-card,
    .report-hub .report-chart-card {
        min-height: 100%;
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border-color: rgb(148 163 184 / 0.22);
        background: linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 22px 40px -34px rgb(15 23 42 / 0.3), inset 0 1px 0 rgb(255 255 255 / 0.78);
        backdrop-filter: blur(8px);
    }

    .theme-dark .report-hub .report-module-card,
    .theme-dark .report-hub .report-kpi-card,
    .theme-dark .report-hub .report-chart-card,
    .dark .report-hub .report-module-card,
    .dark .report-hub .report-kpi-card,
    .dark .report-hub .report-chart-card {
        border-color: rgb(71 85 105 / 0.76);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.84));
        box-shadow: 0 26px 42px -34px rgb(2 8 23 / 0.88), inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-hub .report-module-card::before,
    .report-hub .report-kpi-card::before,
    .report-hub .report-chart-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, rgb(255 255 255 / 0.72), transparent 72%);
        pointer-events: none;
    }

    .theme-dark .report-hub .report-module-card::before,
    .theme-dark .report-hub .report-kpi-card::before,
    .theme-dark .report-hub .report-chart-card::before,
    .dark .report-hub .report-module-card::before,
    .dark .report-hub .report-kpi-card::before,
    .dark .report-hub .report-chart-card::before {
        background: linear-gradient(90deg, rgb(255 255 255 / 0.08), transparent 72%);
    }

    .report-hub .report-kpi-card .ui-heading,
    .report-hub .report-kpi-card .font-black {
        letter-spacing: -0.04em;
        line-height: 0.98;
    }

    .report-hub .report-module-card > header,
    .report-hub .report-chart-card > header,
    .report-hub .report-hub-filter > header,
    .report-hub .report-nav-card > header {
        margin-bottom: 1.02rem;
    }

    .report-hub .report-module-content {
        display: flex;
        flex-direction: column;
        gap: 0.95rem;
        min-height: 100%;
    }

    .report-hub .report-module-body {
        flex: 1;
        line-height: 1.58;
    }

    .report-hub .report-module-actions,
    .report-hub .report-nav-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        align-items: center;
        padding-top: 0.85rem;
        border-top: 1px solid rgb(148 163 184 / 0.16);
    }

    .theme-dark .report-hub .report-module-actions,
    .theme-dark .report-hub .report-nav-actions,
    .dark .report-hub .report-module-actions,
    .dark .report-hub .report-nav-actions {
        border-top-color: rgb(71 85 105 / 0.46);
    }

    .report-hub .report-kpi-card[data-tone="income"] {
        border-color: rgb(16 185 129 / 0.28);
        background:
            radial-gradient(circle at top right, rgb(16 185 129 / 0.14), transparent 32%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(236 253 245 / 0.92));
    }

    .report-hub .report-kpi-card[data-tone="expense"] {
        border-color: rgb(244 63 94 / 0.26);
        background:
            radial-gradient(circle at top right, rgb(244 63 94 / 0.14), transparent 32%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(255 241 242 / 0.92));
    }

    .report-hub .report-kpi-card[data-tone="balance"] {
        border-color: rgb(6 182 212 / 0.26);
        background:
            radial-gradient(circle at top right, rgb(6 182 212 / 0.14), transparent 32%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(236 254 255 / 0.92));
    }

    .report-hub .report-kpi-card[data-tone="motion"] {
        border-color: rgb(99 102 241 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(99 102 241 / 0.12), transparent 32%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(238 242 255 / 0.92));
    }

    .report-hub .report-kpi-card[data-tone="attendance"] {
        border-color: rgb(245 158 11 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(245 158 11 / 0.12), transparent 32%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(255 251 235 / 0.92));
    }

    .report-hub .report-kpi-card[data-tone="membership"] {
        border-color: rgb(14 165 233 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(14 165 233 / 0.12), transparent 32%),
            linear-gradient(160deg, rgb(255 255 255 / 0.98), rgb(239 246 255 / 0.92));
    }

    .theme-dark .report-hub .report-kpi-card[data-tone],
    .dark .report-hub .report-kpi-card[data-tone] {
        background:
            radial-gradient(circle at top right, rgb(255 255 255 / 0.05), transparent 34%),
            linear-gradient(165deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.84));
    }

    .report-hub .report-filter-form > .ui-button,
    .report-hub .report-filter-form > div .ui-button {
        min-height: 2.7rem;
    }

    .report-hub .report-chart-surface {
        padding: 0.92rem;
        border: 1px solid rgb(148 163 184 / 0.16);
        border-radius: 1rem;
        background: linear-gradient(160deg, rgb(255 255 255 / 0.64), rgb(241 245 249 / 0.74));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.7);
    }

    .theme-dark .report-hub .report-chart-surface,
    .dark .report-hub .report-chart-surface {
        border-color: rgb(71 85 105 / 0.52);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.6), rgb(2 6 23 / 0.56));
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .report-hub .report-chart-shell {
        height: clamp(220px, 32vh, 340px);
    }

    .report-hub .report-chart-shell canvas {
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 768px) {
        .report-hub {
            gap: 0.8rem;
        }

        .report-pro-actions .ui-button {
            width: 100%;
        }

        .report-hub .ui-card {
            padding: 0.9rem;
        }

        .report-hub .report-filter-form {
            grid-template-columns: minmax(0, 1fr);
            gap: 0.65rem;
        }

        .report-hub .report-filter-toolbar,
        .report-hub .report-module-actions,
        .report-hub .report-nav-actions {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .report-hub .report-module-grid,
        .report-hub .report-chart-grid {
            gap: 0.8rem;
        }

        .report-hub .report-module-card .ui-heading {
            font-size: 1.03rem;
            line-height: 1.2;
        }

        .report-hub .report-module-card p {
            line-height: 1.28;
        }
    }

    @media (min-width: 768px) {
        .report-control-priority-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .report-pro-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .report-control-grid {
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
        }

        .report-control-priority-grid {
            grid-column: 1 / -1;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .report-pro-metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .report-control-actions .ui-button {
            width: 100%;
        }

        .report-hub .report-filter-toolbar .ui-button,
        .report-hub .report-module-actions .ui-button,
        .report-hub .report-nav-actions .ui-button {
            width: 100%;
        }

        .report-hub .report-chart-shell {
            height: 215px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currencyFormatter = \App\Support\Currency::class;
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $contextGym = (string) request()->route('contextGym');
        $isBranchContext = (bool) request()->attributes->get('gym_context_is_branch', false);
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canExportReports = ! $isBranchContext
            && $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'reports_export');
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $canManageClientAccounts = $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'client_accounts');
        $canUseSalesInventoryReports = $activeGymId > 0
            && $planAccessService->canForGym($activeGymId, 'sales_inventory_reports');
        $baseRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $reportRouteParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $membershipsRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $clientEarningsRouteParams = [
            'contextGym' => $contextGym,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $planControlReportsDashboard = is_array($planControlReportsDashboard ?? null) ? $planControlReportsDashboard : null;
        $professionalReportsDashboard = is_array($professionalReportsDashboard ?? null) ? $professionalReportsDashboard : null;
        $premiumReportsDashboard = is_array($premiumReportsDashboard ?? null) ? $premiumReportsDashboard : null;
        $reportFilterSubtitle = $planControlReportsDashboard
            ? 'Lectura ejecutiva de caja, membresias y asistencia para una sola sede.'
            : 'Resumen financiero y operativo del rango seleccionado.';
        $moduleGridClass = $canUseSalesInventoryReports ? 'xl:grid-cols-4' : 'xl:grid-cols-3';
    ?>

    <div class="report-hub space-y-4">
        <?php if($isGlobalScope): ?>
            <div class="ui-alert ui-alert-info">
                Reporte global activo: los datos mostrados suman todas las sedes vinculadas.
            </div>
        <?php endif; ?>

        <?php if($planControlReportsDashboard): ?>
            <?php
                $planControlPriorities = [
                    [
                        'label' => 'Balance',
                        'value' => $currencyFormatter::format((float) ($planControlReportsDashboard['balance'] ?? 0), $appCurrencyCode),
                        'note' => $planControlReportsDashboard['balance_note'] ?? 'Balance del rango actual.',
                        'tone' => $planControlReportsDashboard['balance_tone'] ?? 'success',
                    ],
                    [
                        'label' => 'Membresias activas',
                        'value' => (string) ($planControlReportsDashboard['memberships_value'] ?? '0'),
                        'note' => $planControlReportsDashboard['memberships_note'] ?? 'Base vigente de la sede.',
                        'tone' => $planControlReportsDashboard['memberships_tone'] ?? 'success',
                    ],
                    [
                        'label' => 'Asistencias',
                        'value' => (string) ($planControlReportsDashboard['attendance_value'] ?? '0'),
                        'note' => $planControlReportsDashboard['attendance_note'] ?? 'Actividad registrada en el rango.',
                        'tone' => $planControlReportsDashboard['attendance_tone'] ?? 'info',
                    ],
                    [
                        'label' => 'Movimientos',
                        'value' => (string) ($planControlReportsDashboard['movements_value'] ?? '0'),
                        'note' => $planControlReportsDashboard['movements_note'] ?? 'Movimientos de caja del periodo.',
                        'tone' => $planControlReportsDashboard['movements_tone'] ?? 'neutral',
                    ],
                ];
            ?>
            <section class="report-control-shell">
                <div class="report-control-grid">
                    <div class="report-control-copy">
                        <span class="report-control-kicker">Plan Control / Reportes</span>
                        <h2 class="report-control-heading"><?php echo e($planControlReportsDashboard['headline'] ?? 'Reportes listos para leer la sede sin ruido'); ?></h2>
                        <p class="report-control-summary"><?php echo e($planControlReportsDashboard['summary'] ?? 'Caja, membresias y asistencia quedan ordenadas en una sola lectura ejecutiva.'); ?></p>
                    </div>

                    <div class="report-control-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.income', ['contextGym' => $contextGym] + request()->query()),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.income', ['contextGym' => $contextGym] + request()->query())),'variant' => 'primary']); ?>Detalle ingresos <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.memberships', $membershipsRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.memberships', $membershipsRouteParams)),'variant' => 'secondary']); ?>Estado membresias <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $baseRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $baseRouteParams)),'variant' => 'ghost']); ?>Ir a clientes <?php echo $__env->renderComponent(); ?>
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

                    <div class="report-control-priority-grid">
                        <?php $__currentLoopData = $planControlPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="report-control-priority" data-tone="<?php echo e($priority['tone']); ?>">
                                <p class="report-control-priority-label"><?php echo e($priority['label']); ?></p>
                                <p class="report-control-priority-value"><?php echo e($priority['value']); ?></p>
                                <p class="report-control-priority-note"><?php echo e($priority['note']); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($professionalReportsDashboard): ?>
            <?php
                $professionalReportAlerts = collect($professionalReportsDashboard['alerts'] ?? [])->values();
                $expiringClientsRouteParams = [
                    'contextGym' => $contextGym,
                    'filter' => 'expiring',
                ] + ($isGlobalScope ? ['scope' => 'global'] : []);
                $expiredClientsRouteParams = [
                    'contextGym' => $contextGym,
                    'filter' => 'expired',
                ] + ($isGlobalScope ? ['scope' => 'global'] : []);
                $professionalReportMetrics = [
                    [
                        'label' => 'Total comercial',
                        'value' => $currencyFormatter::format((float) ($professionalReportsDashboard['commercial_total'] ?? 0), $appCurrencyCode),
                        'note' => 'Membresias y productos dentro del rango.',
                        'tone' => 'success',
                    ],
                    [
                        'label' => 'Cobros de membresias',
                        'value' => $currencyFormatter::format((float) ($professionalReportsDashboard['membership_income'] ?? 0), $appCurrencyCode),
                        'note' => 'Membresias activas '.(int) ($professionalReportsDashboard['active_memberships'] ?? 0),
                        'tone' => 'info',
                    ],
                    [
                        'label' => 'Ventas de productos',
                        'value' => $currencyFormatter::format((float) ($professionalReportsDashboard['product_revenue'] ?? 0), $appCurrencyCode),
                        'note' => (int) ($professionalReportsDashboard['product_sales_count'] ?? 0).' ticket(s) | Prom. '.$currencyFormatter::format((float) ($professionalReportsDashboard['average_ticket'] ?? 0), $appCurrencyCode, true),
                        'tone' => 'warning',
                    ],
                    [
                        'label' => 'Promos y stock',
                        'value' => (string) ((int) ($professionalReportsDashboard['active_promotions_count'] ?? 0)),
                        'note' => 'Stock bajo '.(int) ($professionalReportsDashboard['low_stock_products_count'] ?? 0).' | Vencidas '.(int) ($professionalReportsDashboard['expired_memberships'] ?? 0),
                        'tone' => 'accent',
                    ],
                ];
            ?>
            <section class="report-pro-shell">
                <div class="report-pro-grid">
                    <div class="report-elite-head flex flex-wrap items-start justify-between gap-3">
                        <div class="report-pro-copy">
                            <span class="report-pro-kicker">Plan Profesional / Reportes</span>
                            <h2 class="report-pro-heading"><?php echo e($professionalReportsDashboard['headline'] ?? 'Radar de crecimiento'); ?></h2>
                            <p class="report-pro-summary"><?php echo e($professionalReportsDashboard['summary'] ?? 'Membresias, productos y promos dentro de una misma lectura ejecutiva.'); ?></p>
                        </div>
                        <span class="report-pro-badge">Profesional</span>
                    </div>

                    <div class="report-pro-metrics">
                        <?php $__currentLoopData = $professionalReportMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="report-pro-metric" data-tone="<?php echo e($metric['tone']); ?>">
                                <p class="report-pro-metric-label"><?php echo e($metric['label']); ?></p>
                                <p class="report-pro-metric-value"><?php echo e($metric['value']); ?></p>
                                <p class="report-pro-metric-note"><?php echo e($metric['note']); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($professionalReportAlerts->isNotEmpty()): ?>
                        <div class="report-pro-insights">
                            <?php $__currentLoopData = $professionalReportAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="report-pro-chip" data-tone="<?php echo e($alert['tone'] ?? 'info'); ?>">
                                    <p class="report-pro-chip-title"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                    <p class="report-pro-chip-copy"><?php echo e($alert['description'] ?? ''); ?></p>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <div class="report-pro-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.client-earnings', $clientEarningsRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.client-earnings', $clientEarningsRouteParams)),'variant' => 'primary']); ?>Ganancias de clientes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $expiringClientsRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $expiringClientsRouteParams)),'variant' => 'secondary']); ?>Ver por vencer <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index', $baseRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index', $baseRouteParams)),'variant' => 'ghost']); ?>Planes y promos <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if($canUseSalesInventoryReports): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query()),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())),'variant' => 'ghost']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $expiredClientsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $expiredClientsRouteParams)),'variant' => 'ghost']); ?>Ver vencidos <?php echo $__env->renderComponent(); ?>
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

        <?php if($premiumReportsDashboard): ?>
            <?php
                $premiumReportMetrics = [
                    [
                        'label' => 'Total',
                        'value' => $currencyFormatter::format((float) ($premiumReportsDashboard['commercial_total'] ?? 0), $appCurrencyCode),
                        'note' => 'Ingreso del rango.',
                        'tone' => 'success',
                    ],
                    [
                        'label' => 'Con app',
                        'value' => (string) ((int) ($premiumReportsDashboard['clients_with_app_access_count'] ?? 0)),
                        'note' => 'Clientes con acceso.',
                        'tone' => 'accent',
                    ],
                    [
                        'label' => 'Pendientes',
                        'value' => (string) ((int) ($premiumReportsDashboard['active_clients_without_app_access_count'] ?? 0)),
                        'note' => 'Activos sin acceso.',
                        'tone' => 'info',
                    ],
                ];
            ?>
            <section class="report-pro-shell report-elite-shell">
                <div class="report-pro-grid">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="report-pro-copy">
                            <span class="report-pro-kicker">Plan Elite / Reportes</span>
                            <h2 class="report-pro-heading">Reportes premium resumidos</h2>
                            <p class="report-pro-summary">Ingresos y canal cliente en una lectura corta.</p>
                        </div>
                        <span class="report-pro-badge">Premium</span>
                    </div>

                    <div class="report-pro-metrics">
                        <?php $__currentLoopData = $premiumReportMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="report-pro-metric" data-tone="<?php echo e($metric['tone']); ?>">
                                <p class="report-pro-metric-label"><?php echo e($metric['label']); ?></p>
                                <p class="report-pro-metric-value"><?php echo e($metric['value']); ?></p>
                                <p class="report-pro-metric-note"><?php echo e($metric['note']); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="report-pro-actions">
                        <?php if($canManageClientAccounts && \Illuminate\Support\Facades\Route::has('client-portal.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('client-portal.index', $baseRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('client-portal.index', $baseRouteParams)),'variant' => 'primary']); ?>Portal cliente <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.client-earnings', $clientEarningsRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.client-earnings', $clientEarningsRouteParams)),'variant' => 'secondary']); ?>Ganancias de clientes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if($canUseSalesInventoryReports): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query()),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())),'variant' => 'ghost']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.income', ['contextGym' => $contextGym] + request()->query()),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.income', ['contextGym' => $contextGym] + request()->query())),'variant' => 'ghost']); ?>Detalle ingresos <?php echo $__env->renderComponent(); ?>
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

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-hub-filter','title' => 'Panel de reportes','subtitle' => $reportFilterSubtitle]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-hub-filter','title' => 'Panel de reportes','subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reportFilterSubtitle)]); ?>
            <form id="reports-filter-form" method="GET" action="<?php echo e(route('reports.index', ['contextGym' => $contextGym])); ?>" class="report-filter-form grid gap-3 md:grid-cols-4">
                <?php if($isGlobalScope): ?>
                    <input type="hidden" name="scope" value="global">
                <?php endif; ?>
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Desde</span>
                    <input type="date" name="from" value="<?php echo e($from->toDateString()); ?>" class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Hasta</span>
                    <input type="date" name="to" value="<?php echo e($to->toDateString()); ?>" class="ui-input">
                </label>

                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary']); ?>Aplicar filtro <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>

                <div class="report-filter-toolbar">
                    <?php if($canExportReports): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'reports-export-pdf','href' => route('reports.export.pdf', $reportRouteParams),'target' => '_blank','rel' => 'noopener','variant' => 'ghost','class' => 'js-loading-link','dataLoadingText' => 'Generando PDF...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'reports-export-pdf','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.export.pdf', $reportRouteParams)),'target' => '_blank','rel' => 'noopener','variant' => 'ghost','class' => 'js-loading-link','data-loading-text' => 'Generando PDF...']); ?>Exportar PDF <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'reports-export-csv','href' => route('reports.export.csv', $reportRouteParams),'dataUiLoadingIgnore' => '1','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'reports-export-csv','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.export.csv', $reportRouteParams)),'data-ui-loading-ignore' => '1','variant' => 'ghost']); ?>Exportar CSV <?php echo $__env->renderComponent(); ?>
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
                        <p class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                            <?php echo e($isBranchContext ? 'Sucursal secundaria: exportacion bloqueada (solo lectura).' : 'Este plan mantiene lectura en pantalla; PDF y CSV se habilitan al subir a Profesional.'); ?>

                        </p>
                    <?php endif; ?>
                </div>
            </form>
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

        <section class="report-module-grid grid gap-4 <?php echo e($moduleGridClass); ?>">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-module-card','title' => 'Ganancias del gimnasio','subtitle' => 'Resumen financiero principal del negocio.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-module-card','title' => 'Ganancias del gimnasio','subtitle' => 'Resumen financiero principal del negocio.']); ?>
                <div class="report-module-content">
                    <p class="report-module-body text-sm text-slate-600 dark:text-slate-300">Usa esta sección para revisar ingresos, egresos, balance y exportaciones del rango seleccionado.</p>
                    <div class="report-module-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.income', ['contextGym' => $contextGym] + request()->query()),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.income', ['contextGym' => $contextGym] + request()->query())),'variant' => 'secondary']); ?>Detalle ingresos <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('panel.index', $baseRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('panel.index', $baseRouteParams)),'variant' => 'ghost']); ?>Volver al panel <?php echo $__env->renderComponent(); ?>
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

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-module-card','title' => 'Clientes','subtitle' => 'Lectura de asistencia y estado de membresías.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-module-card','title' => 'Clientes','subtitle' => 'Lectura de asistencia y estado de membresías.']); ?>
                <div class="report-module-content">
                    <p class="report-module-body text-sm text-slate-600 dark:text-slate-300">Centraliza lo relacionado con comportamiento de clientes, renovaciones y vigencia de membresías.</p>
                    <div class="report-module-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.attendance', ['contextGym' => $contextGym] + request()->query()),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.attendance', ['contextGym' => $contextGym] + request()->query())),'variant' => 'secondary']); ?>Asistencias <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.memberships', $membershipsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.memberships', $membershipsRouteParams)),'variant' => 'ghost']); ?>Membresías <?php echo $__env->renderComponent(); ?>
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

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-module-card','title' => 'Ganancias de clientes','subtitle' => 'Facturación por cliente con desglose y filtros avanzados.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-module-card','title' => 'Ganancias de clientes','subtitle' => 'Facturación por cliente con desglose y filtros avanzados.']); ?>
                <div class="report-module-content">
                    <p class="report-module-body text-sm text-slate-600 dark:text-slate-300">Revisa cuántos clientes han sido facturados, cuánto aportan y su último movimiento comercial.</p>
                    <div class="report-module-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.client-earnings', $clientEarningsRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.client-earnings', $clientEarningsRouteParams)),'variant' => 'secondary']); ?>Abrir reporte <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $baseRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $baseRouteParams)),'variant' => 'ghost']); ?>Ir a clientes <?php echo $__env->renderComponent(); ?>
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

            <?php if($canUseSalesInventoryReports): ?>
                <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-module-card','title' => 'Ventas e inventario','subtitle' => 'Rendimiento comercial de productos y control de stock.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-module-card','title' => 'Ventas e inventario','subtitle' => 'Rendimiento comercial de productos y control de stock.']); ?>
                    <div class="report-module-content">
                        <p class="report-module-body text-sm text-slate-600 dark:text-slate-300">Sección separada para ver ingresos por productos, utilidad, rotación y alertas de inventario.</p>
                        <div class="report-module-actions">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query()),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())),'variant' => 'secondary']); ?>Abrir reportes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $baseRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $baseRouteParams)),'variant' => 'ghost']); ?>Ir al módulo <?php echo $__env->renderComponent(); ?>
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
            <?php endif; ?>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-kpi-card','dataTone' => 'income']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-kpi-card','data-tone' => 'income']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Total ingresos</p>
                <p class="mt-2 text-3xl font-black text-emerald-700"><?php echo e($currencyFormatter::format((float) $incomeSummary['total_income'], $appCurrencyCode)); ?></p>
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
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-kpi-card','dataTone' => 'expense']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-kpi-card','data-tone' => 'expense']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Total egresos</p>
                <p class="mt-2 text-3xl font-black text-rose-700"><?php echo e($currencyFormatter::format((float) $incomeSummary['total_expense'], $appCurrencyCode)); ?></p>
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
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-kpi-card','dataTone' => 'balance']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-kpi-card','data-tone' => 'balance']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Balance</p>
                <p class="mt-2 text-3xl font-black text-cyan-700"><?php echo e($currencyFormatter::format((float) $incomeSummary['balance'], $appCurrencyCode)); ?></p>
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
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-kpi-card','dataTone' => 'motion']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-kpi-card','data-tone' => 'motion']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Movimientos</p>
                <p class="ui-heading mt-2 text-4xl font-black tracking-tight"><?php echo e((int) $incomeSummary['total_movements']); ?></p>
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
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-kpi-card','dataTone' => 'attendance']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-kpi-card','data-tone' => 'attendance']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Asistencias</p>
                <p class="ui-heading mt-2 text-4xl font-black tracking-tight"><?php echo e((int) $attendanceSummary['total_attendances']); ?></p>
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
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-kpi-card','dataTone' => 'membership']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-kpi-card','data-tone' => 'membership']); ?>
                <p class="ui-muted text-xs font-bold uppercase tracking-wider">Membresías activas</p>
                <p class="ui-heading mt-2 text-4xl font-black tracking-tight"><?php echo e((int) $membershipSummary['active']); ?></p>
                <div class="mt-3 flex gap-2">
                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'success']); ?>Activos <?php echo e((int) $membershipSummary['active']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger']); ?>Vencidos <?php echo e((int) $membershipSummary['expired']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
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
        </section>

        <section class="report-chart-grid grid gap-4 xl:grid-cols-2">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-chart-card','title' => 'Ingresos / egresos por método']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-chart-card','title' => 'Ingresos / egresos por método']); ?>
                <div class="report-chart-surface">
                    <div class="report-chart-shell">
                        <canvas id="methodChart"></canvas>
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
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-chart-card','title' => 'Asistencias por día']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-chart-card','title' => 'Asistencias por día']); ?>
                <div class="report-chart-surface">
                    <div class="report-chart-shell">
                        <canvas id="attendanceChart"></canvas>
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
        </section>

        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'report-nav-card','title' => 'Navegación rápida de reportes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'report-nav-card','title' => 'Navegación rápida de reportes']); ?>
            <div class="report-nav-actions">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'reports-go-income','href' => route('reports.income', ['contextGym' => $contextGym] + request()->query()),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'reports-go-income','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.income', ['contextGym' => $contextGym] + request()->query())),'variant' => 'secondary']); ?>Detalle ingresos <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.attendance', ['contextGym' => $contextGym] + request()->query()),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.attendance', ['contextGym' => $contextGym] + request()->query())),'variant' => 'ghost']); ?>Detalle asistencias <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.memberships', $membershipsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.memberships', $membershipsRouteParams)),'variant' => 'ghost']); ?>Estado membresías <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.client-earnings', $clientEarningsRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.client-earnings', $clientEarningsRouteParams)),'variant' => 'ghost']); ?>Ganancias de clientes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if($canUseSalesInventoryReports): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query()),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.sales-inventory', ['contextGym' => $contextGym] + request()->query())),'variant' => 'ghost']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
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
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const methodLabels = <?php echo json_encode($methodLabels, 15, 512) ?>;
        const methodIncomeData = <?php echo json_encode($methodIncomeData, 15, 512) ?>;
        const methodExpenseData = <?php echo json_encode($methodExpenseData, 15, 512) ?>;
        const attendanceLabels = <?php echo json_encode($attendanceLabels, 15, 512) ?>;
        const attendanceData = <?php echo json_encode($attendanceData, 15, 512) ?>;

        const methodCtx = document.getElementById('methodChart');
        if (methodCtx) {
            new Chart(methodCtx, {
                type: 'bar',
                data: {
                    labels: methodLabels,
                    datasets: [
                        { label: 'Ingresos', data: methodIncomeData, backgroundColor: '#059669' },
                        { label: 'Egresos', data: methodExpenseData, backgroundColor: '#dc2626' },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true },
                    },
                },
            });
        }

        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: attendanceLabels,
                    datasets: [
                        {
                            label: 'Asistencias',
                            data: attendanceData,
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2,132,199,0.15)',
                            fill: true,
                            tension: 0.25,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                        },
                    },
                },
            });
        }

        document.querySelectorAll('.js-loading-link').forEach(function (link) {
            link.addEventListener('click', function () {
                const text = link.getAttribute('data-loading-text');
                if (!text) return;
                link.dataset.originalText = link.textContent;
                link.textContent = text;
                link.classList.add('pointer-events-none', 'opacity-70');
                setTimeout(function () {
                    link.textContent = link.dataset.originalText || link.textContent;
                    link.classList.remove('pointer-events-none', 'opacity-70');
                }, 1800);
            });
        });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/reports/index.blade.php ENDPATH**/ ?>