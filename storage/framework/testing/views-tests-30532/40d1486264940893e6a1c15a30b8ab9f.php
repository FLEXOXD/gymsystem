


<?php $__env->startSection('title', 'Ganancias del gimnasio'); ?>
<?php $__env->startSection('page-title', 'Ganancias del gimnasio'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .panel-main-split {
        display: grid;
        gap: 1rem;
    }

    .panel-kpi-grid {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .panel-kpi-card {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        min-height: 7.5rem;
        border-radius: 1rem;
        padding: 0.92rem;
        box-shadow: 0 20px 34px -30px rgb(15 23 42 / 0.3), inset 0 1px 0 rgb(255 255 255 / 0.68);
        backdrop-filter: blur(8px);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .theme-dark .panel-kpi-card,
    .dark .panel-kpi-card {
        box-shadow: 0 22px 38px -30px rgb(2 8 23 / 0.84), inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .panel-kpi-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, rgb(255 255 255 / 0.68), transparent 72%);
        pointer-events: none;
    }

    .theme-dark .panel-kpi-card::before,
    .dark .panel-kpi-card::before {
        background: linear-gradient(90deg, rgb(255 255 255 / 0.08), transparent 72%);
    }

    .panel-kpi-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 24px 38px -32px rgb(15 23 42 / 0.34), inset 0 1px 0 rgb(255 255 255 / 0.72);
    }

    .theme-dark .panel-kpi-card:hover,
    .dark .panel-kpi-card:hover {
        box-shadow: 0 26px 40px -32px rgb(2 8 23 / 0.9), inset 0 1px 0 rgb(255 255 255 / 0.05);
    }

    .panel-kpi-title {
        min-height: 1.75rem;
    }

    .panel-kpi-value {
        font-size: clamp(1.7rem, 2.5vw, 2rem);
        line-height: 0.98;
        letter-spacing: -0.045em;
    }

    .panel-cta-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .panel-cta-grid .ui-button {
        min-height: 2.5rem;
    }

    .panel-cash-today-grid {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .panel-summary-card,
    .panel-side-card {
        border-color: rgb(148 163 184 / 0.22);
        background: linear-gradient(162deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 26px 46px -38px rgb(15 23 42 / 0.34);
    }

    .theme-dark .panel-summary-card,
    .theme-dark .panel-side-card,
    .dark .panel-summary-card,
    .dark .panel-side-card {
        border-color: rgb(71 85 105 / 0.74);
        background: linear-gradient(165deg, rgb(15 23 42 / 0.92), rgb(2 6 23 / 0.84));
        box-shadow: 0 30px 48px -36px rgb(2 8 23 / 0.9);
    }

    .panel-summary-card > header,
    .panel-side-card > header {
        margin-bottom: 1.08rem;
    }

    .panel-summary-card > header .ui-heading,
    .panel-side-card > header .ui-heading {
        letter-spacing: -0.03em;
    }

    .panel-summary-card > header .ui-muted,
    .panel-side-card > header .ui-muted {
        max-width: 44rem;
    }

    .panel-inline-metric {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        box-shadow: 0 18px 32px -30px rgb(15 23 42 / 0.26), inset 0 1px 0 rgb(255 255 255 / 0.66);
        backdrop-filter: blur(8px);
    }

    .theme-dark .panel-inline-metric,
    .dark .panel-inline-metric {
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.86), inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .panel-inline-metric::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, rgb(255 255 255 / 0.68), transparent 70%);
        pointer-events: none;
    }

    .theme-dark .panel-inline-metric::before,
    .dark .panel-inline-metric::before {
        background: linear-gradient(90deg, rgb(255 255 255 / 0.08), transparent 70%);
    }

    .panel-control-shell {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgb(163 230 53 / 0.22);
        border-radius: 1.22rem;
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.16), transparent 34%),
            linear-gradient(145deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
        box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.5);
        backdrop-filter: blur(14px);
        padding: 1.05rem;
    }

    .theme-dark .panel-control-shell,
    .dark .panel-control-shell {
        border-color: rgb(163 230 53 / 0.28);
        background:
            radial-gradient(circle at top right, rgb(163 230 53 / 0.14), transparent 34%),
            linear-gradient(152deg, rgb(3 7 18 / 0.94), rgb(11 18 32 / 0.88));
        box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.9);
    }

    .panel-control-shell::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
        opacity: 0.8;
        pointer-events: none;
    }

    .panel-control-shell::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(95deg, transparent, rgb(163 230 53 / 0.05), transparent);
    }

    .panel-control-grid {
        display: grid;
        gap: 1.05rem;
        position: relative;
        z-index: 1;
    }

    .panel-control-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgb(77 124 15 / 0.94);
    }

    .theme-dark .panel-control-kicker,
    .dark .panel-control-kicker {
        color: rgb(217 249 157 / 0.92);
    }

    .panel-control-kicker::before {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 999px;
        background: rgb(132 204 22 / 0.92);
        box-shadow: 0 0 0 6px rgb(132 204 22 / 0.14);
    }

    .panel-control-heading {
        font-size: clamp(1.14rem, 1.85vw, 1.46rem);
        line-height: 1.08;
        letter-spacing: -0.035em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-control-heading,
    .dark .panel-control-heading {
        color: rgb(241 245 249 / 0.98);
    }

    .panel-control-copy {
        max-width: 48rem;
    }

    .panel-control-copy p {
        margin-top: 0.5rem;
        font-size: 0.89rem;
        line-height: 1.58;
        color: rgb(71 85 105 / 0.94);
    }

    .theme-dark .panel-control-copy p,
    .dark .panel-control-copy p {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-control-progress {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        border: 1px solid rgb(163 230 53 / 0.26);
        background: rgb(236 252 203 / 0.72);
        padding: 0.38rem 0.72rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(77 124 15 / 0.94);
    }

    .theme-dark .panel-control-progress,
    .dark .panel-control-progress {
        border-color: rgb(163 230 53 / 0.28);
        background: rgb(132 204 22 / 0.12);
        color: rgb(217 249 157 / 0.96);
    }

    .panel-control-priority-grid {
        display: grid;
        gap: 0.75rem;
    }

    .panel-control-priority {
        position: relative;
        overflow: hidden;
        border-radius: 1.05rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        min-height: 7rem;
        padding: 0.9rem 0.95rem;
    }

    .theme-dark .panel-control-priority,
    .dark .panel-control-priority {
        border-color: rgb(148 163 184 / 0.16);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
    }

    .panel-control-priority::before {
        content: '';
        position: absolute;
        left: 0.9rem;
        right: 0.9rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .panel-control-priority[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
    }

    .panel-control-priority[data-tone='danger']::before {
        background: linear-gradient(90deg, rgb(244 63 94 / 0.9), rgb(244 63 94 / 0.24));
    }

    .panel-control-priority[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(6 182 212 / 0.9), rgb(6 182 212 / 0.24));
    }

    .panel-control-priority[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(34 197 94 / 0.9), rgb(34 197 94 / 0.24));
    }

    .panel-control-priority-label {
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .panel-control-priority-label,
    .dark .panel-control-priority-label {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-control-priority-value {
        margin-top: 0.45rem;
        font-size: 1.46rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-control-priority-value,
    .dark .panel-control-priority-value {
        color: rgb(248 250 252 / 0.98);
    }

    .panel-control-priority-note {
        margin-top: 0.45rem;
        font-size: 0.76rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-control-priority-note,
    .dark .panel-control-priority-note {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-control-actions {
        display: grid;
        gap: 0.6rem;
        align-content: start;
    }

    .panel-control-actions .ui-button {
        min-height: 2.72rem;
    }

    .panel-control-disclosure {
        position: relative;
        z-index: 1;
        margin-top: 0.9rem;
        border-top: 1px solid rgb(148 163 184 / 0.18);
        padding-top: 0.9rem;
    }

    .theme-dark .panel-control-disclosure,
    .dark .panel-control-disclosure {
        border-top-color: rgb(148 163 184 / 0.12);
    }

    .panel-control-disclosure summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        cursor: pointer;
        list-style: none;
        font-size: 0.76rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.94);
    }

    .panel-control-disclosure summary::-webkit-details-marker {
        display: none;
    }

    .theme-dark .panel-control-disclosure summary,
    .dark .panel-control-disclosure summary {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-control-disclosure summary::after {
        content: '+';
        font-size: 1rem;
        font-weight: 900;
        color: rgb(132 204 22 / 0.92);
    }

    .panel-control-disclosure[open] summary::after {
        content: '-';
    }

    .panel-control-checklist {
        display: grid;
        gap: 0.7rem;
        margin-top: 0.85rem;
    }

    .panel-control-check-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.8rem;
        border-radius: 0.95rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: rgb(255 255 255 / 0.66);
        padding: 0.8rem 0.85rem;
    }

    .theme-dark .panel-control-check-item,
    .dark .panel-control-check-item {
        border-color: rgb(148 163 184 / 0.14);
        background: rgb(15 23 42 / 0.58);
    }

    .panel-control-check-item.is-complete {
        border-color: rgb(132 204 22 / 0.24);
        background: rgb(236 252 203 / 0.5);
    }

    .theme-dark .panel-control-check-item.is-complete,
    .dark .panel-control-check-item.is-complete {
        border-color: rgb(132 204 22 / 0.22);
        background: rgb(132 204 22 / 0.08);
    }

    .panel-control-check-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-control-check-title,
    .dark .panel-control-check-title {
        color: rgb(241 245 249 / 0.97);
    }

    .panel-control-check-copy {
        margin-top: 0.24rem;
        font-size: 0.76rem;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-control-check-copy,
    .dark .panel-control-check-copy {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-control-check-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.34rem 0.58rem;
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .panel-control-check-badge.is-complete {
        background: rgb(190 242 100 / 0.78);
        color: rgb(54 83 20 / 0.96);
    }

    .theme-dark .panel-control-check-badge.is-complete,
    .dark .panel-control-check-badge.is-complete {
        background: rgb(132 204 22 / 0.18);
        color: rgb(217 249 157 / 0.96);
    }

    .panel-control-check-badge.is-pending {
        background: rgb(226 232 240 / 0.88);
        color: rgb(51 65 85 / 0.95);
    }

    .theme-dark .panel-control-check-badge.is-pending,
    .dark .panel-control-check-badge.is-pending {
        background: rgb(51 65 85 / 0.88);
        color: rgb(226 232 240 / 0.94);
    }

    .panel-control-summary-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .panel-control-summary-actions .ui-button {
        min-height: 2.6rem;
    }

    .panel-pro-shell {
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

    .theme-dark .panel-pro-shell,
    .dark .panel-pro-shell {
        border-color: rgb(34 211 238 / 0.22);
        background:
            radial-gradient(circle at top right, rgb(34 211 238 / 0.11), transparent 34%),
            radial-gradient(circle at bottom left, rgb(245 158 11 / 0.08), transparent 28%),
            linear-gradient(155deg, rgb(4 10 28 / 0.94), rgb(11 18 32 / 0.88));
        box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.9);
    }

    .panel-pro-shell::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
        opacity: 0.8;
        pointer-events: none;
    }

    .panel-pro-shell::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(90deg, transparent, rgb(34 211 238 / 0.04), transparent);
    }

    .panel-pro-grid {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1rem;
    }

    .panel-pro-copy {
        max-width: 50rem;
    }

    .panel-pro-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgb(8 145 178 / 0.96);
    }

    .theme-dark .panel-pro-kicker,
    .dark .panel-pro-kicker {
        color: rgb(165 243 252 / 0.94);
    }

    .panel-pro-kicker::before {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 999px;
        background: rgb(34 211 238 / 0.96);
        box-shadow: 0 0 0 6px rgb(34 211 238 / 0.14);
    }

    .panel-pro-heading {
        margin-top: 0.75rem;
        font-size: clamp(1.14rem, 1.85vw, 1.46rem);
        line-height: 1.08;
        letter-spacing: -0.035em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-pro-heading,
    .dark .panel-pro-heading {
        color: rgb(241 245 249 / 0.98);
    }

    .panel-pro-summary {
        margin-top: 0.5rem;
        font-size: 0.89rem;
        line-height: 1.58;
        color: rgb(71 85 105 / 0.94);
    }

    .theme-dark .panel-pro-summary,
    .dark .panel-pro-summary {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-pro-badge {
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

    .theme-dark .panel-pro-badge,
    .dark .panel-pro-badge {
        border-color: rgb(34 211 238 / 0.26);
        background: rgb(8 145 178 / 0.12);
        color: rgb(165 243 252 / 0.95);
    }

    .panel-pro-metrics {
        display: grid;
        gap: 0.75rem;
    }

    .panel-pro-metric {
        position: relative;
        overflow: hidden;
        border-radius: 1.02rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        min-height: 6.7rem;
        padding: 0.9rem 0.95rem;
    }

    .theme-dark .panel-pro-metric,
    .dark .panel-pro-metric {
        border-color: rgb(148 163 184 / 0.16);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
    }

    .panel-pro-metric::before {
        content: '';
        position: absolute;
        left: 0.9rem;
        right: 0.9rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .panel-pro-metric[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
    }

    .panel-pro-metric[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(34 211 238 / 0.9), rgb(34 211 238 / 0.24));
    }

    .panel-pro-metric[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
    }

    .panel-pro-metric[data-tone='accent']::before {
        background: linear-gradient(90deg, rgb(168 85 247 / 0.9), rgb(168 85 247 / 0.24));
    }

    .panel-pro-metric-label {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .panel-pro-metric-label,
    .dark .panel-pro-metric-label {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-pro-metric-value {
        margin-top: 0.45rem;
        font-size: 1.42rem;
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-pro-metric-value,
    .dark .panel-pro-metric-value {
        color: rgb(248 250 252 / 0.98);
    }

    .panel-pro-metric-note {
        margin-top: 0.4rem;
        font-size: 0.76rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-pro-metric-note,
    .dark .panel-pro-metric-note {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-pro-insights {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .panel-pro-chip {
        min-width: min(100%, 14rem);
        flex: 1 1 14rem;
        border-radius: 0.95rem;
        border: 1px solid rgb(148 163 184 / 0.2);
        background: rgb(255 255 255 / 0.66);
        padding: 0.78rem 0.85rem;
        box-shadow: 0 16px 28px -30px rgb(15 23 42 / 0.32);
    }

    .theme-dark .panel-pro-chip,
    .dark .panel-pro-chip {
        border-color: rgb(148 163 184 / 0.14);
        background: rgb(15 23 42 / 0.58);
        box-shadow: 0 20px 30px -30px rgb(2 8 23 / 0.9);
    }

    .panel-pro-chip[data-tone='warning'] {
        border-color: rgb(245 158 11 / 0.22);
        background: rgb(255 251 235 / 0.9);
    }

    .panel-pro-chip[data-tone='success'] {
        border-color: rgb(16 185 129 / 0.22);
        background: rgb(236 253 245 / 0.9);
    }

    .panel-pro-chip[data-tone='info'] {
        border-color: rgb(34 211 238 / 0.22);
        background: rgb(236 254 255 / 0.9);
    }

    .theme-dark .panel-pro-chip[data-tone='warning'],
    .dark .panel-pro-chip[data-tone='warning'] {
        background: rgb(120 53 15 / 0.18);
    }

    .theme-dark .panel-pro-chip[data-tone='success'],
    .dark .panel-pro-chip[data-tone='success'] {
        background: rgb(6 78 59 / 0.18);
    }

    .theme-dark .panel-pro-chip[data-tone='info'],
    .dark .panel-pro-chip[data-tone='info'] {
        background: rgb(8 145 178 / 0.14);
    }

    .panel-pro-chip-title {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(30 41 59 / 0.92);
    }

    .theme-dark .panel-pro-chip-title,
    .dark .panel-pro-chip-title {
        color: rgb(226 232 240 / 0.96);
    }

    .panel-pro-chip-copy {
        margin-top: 0.35rem;
        font-size: 0.76rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-pro-chip-copy,
    .dark .panel-pro-chip-copy {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-pro-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .panel-pro-actions .ui-button {
        min-height: 2.72rem;
    }

    .panel-elite-shell.panel-pro-shell {
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

    .theme-dark .panel-elite-shell.panel-pro-shell,
    .dark .panel-elite-shell.panel-pro-shell {
        border-color: rgb(234 179 8 / 0.28);
        background:
            radial-gradient(circle at top right, rgb(234 179 8 / 0.16), transparent 36%),
            radial-gradient(circle at bottom left, rgb(16 185 129 / 0.11), transparent 30%),
            linear-gradient(155deg, rgb(10 12 24 / 0.96), rgb(17 24 39 / 0.92));
        box-shadow:
            0 36px 72px -46px rgb(2 8 23 / 0.92),
            inset 0 1px 0 rgb(255 255 255 / 0.05);
    }

    .panel-elite-shell.panel-pro-shell::after {
        background: linear-gradient(100deg, transparent 8%, rgb(234 179 8 / 0.08), transparent 74%);
    }

    .panel-elite-shell .panel-pro-grid {
        gap: 1.15rem;
    }

    .panel-elite-shell .panel-elite-head {
        align-items: end;
        gap: 1rem;
    }

    .panel-elite-shell .panel-pro-copy {
        max-width: 54rem;
    }

    .panel-elite-shell .panel-pro-kicker {
        color: rgb(161 98 7 / 0.96);
        letter-spacing: 0.15em;
    }

    .theme-dark .panel-elite-shell .panel-pro-kicker,
    .dark .panel-elite-shell .panel-pro-kicker {
        color: rgb(253 224 71 / 0.94);
    }

    .panel-elite-shell .panel-pro-kicker::before {
        background: rgb(234 179 8 / 0.96);
        box-shadow: 0 0 0 6px rgb(234 179 8 / 0.14);
    }

    .panel-elite-shell .panel-pro-badge {
        border-color: rgb(234 179 8 / 0.24);
        background: rgb(254 249 195 / 0.84);
        color: rgb(161 98 7 / 0.96);
        padding: 0.48rem 0.92rem;
        box-shadow: 0 14px 30px -24px rgb(161 98 7 / 0.28);
    }

    .theme-dark .panel-elite-shell .panel-pro-badge,
    .dark .panel-elite-shell .panel-pro-badge {
        border-color: rgb(234 179 8 / 0.26);
        background: rgb(161 98 7 / 0.12);
        color: rgb(253 224 71 / 0.95);
        box-shadow: 0 14px 32px -24px rgb(234 179 8 / 0.2);
    }

    .panel-elite-shell .panel-pro-heading {
        margin-top: 0.64rem;
        max-width: 17ch;
        font-size: clamp(1.24rem, 2vw, 1.62rem);
        line-height: 1.02;
    }

    .panel-elite-shell .panel-pro-summary {
        max-width: 44rem;
        margin-top: 0.55rem;
        color: rgb(71 85 105 / 0.96);
    }

    .theme-dark .panel-elite-shell .panel-pro-summary,
    .dark .panel-elite-shell .panel-pro-summary {
        color: rgb(203 213 225 / 0.82);
    }

    .panel-elite-shell .panel-pro-metrics {
        gap: 0.82rem;
    }

    .panel-elite-shell .panel-pro-metric {
        min-height: 7.2rem;
        padding: 1rem 1.05rem;
        border-color: rgb(234 179 8 / 0.16);
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.94), rgb(248 250 252 / 0.82));
        box-shadow:
            0 20px 34px -28px rgb(120 53 15 / 0.14),
            inset 0 1px 0 rgb(255 255 255 / 0.86);
    }

    .theme-dark .panel-elite-shell .panel-pro-metric,
    .dark .panel-elite-shell .panel-pro-metric {
        border-color: rgb(234 179 8 / 0.14);
        background:
            linear-gradient(165deg, rgb(15 23 42 / 0.82), rgb(15 23 42 / 0.62));
        box-shadow:
            0 22px 38px -30px rgb(2 8 23 / 0.9),
            inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .panel-elite-shell .panel-pro-metric:first-child {
        border-color: rgb(234 179 8 / 0.26);
        background:
            radial-gradient(circle at top right, rgb(234 179 8 / 0.18), transparent 44%),
            linear-gradient(180deg, rgb(255 251 235 / 0.96), rgb(255 255 255 / 0.84));
    }

    .theme-dark .panel-elite-shell .panel-pro-metric:first-child,
    .dark .panel-elite-shell .panel-pro-metric:first-child {
        border-color: rgb(234 179 8 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(234 179 8 / 0.15), transparent 44%),
            linear-gradient(165deg, rgb(31 41 55 / 0.9), rgb(15 23 42 / 0.74));
    }

    .panel-elite-shell .panel-pro-metric-label {
        color: rgb(120 53 15 / 0.86);
    }

    .theme-dark .panel-elite-shell .panel-pro-metric-label,
    .dark .panel-elite-shell .panel-pro-metric-label {
        color: rgb(253 224 71 / 0.8);
    }

    .panel-elite-shell .panel-pro-metric-value {
        margin-top: 0.58rem;
        font-size: clamp(1.54rem, 2.4vw, 2.04rem);
        line-height: 1;
    }

    .panel-elite-shell .panel-pro-chip {
        border-color: rgb(234 179 8 / 0.16);
        background:
            linear-gradient(180deg, rgb(255 255 255 / 0.84), rgb(255 255 255 / 0.7));
        box-shadow:
            0 18px 30px -28px rgb(120 53 15 / 0.12),
            inset 0 1px 0 rgb(255 255 255 / 0.82);
    }

    .theme-dark .panel-elite-shell .panel-pro-chip,
    .dark .panel-elite-shell .panel-pro-chip {
        border-color: rgb(234 179 8 / 0.12);
        background:
            linear-gradient(165deg, rgb(15 23 42 / 0.72), rgb(15 23 42 / 0.54));
        box-shadow:
            0 20px 34px -28px rgb(2 8 23 / 0.84),
            inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .panel-elite-shell .panel-pro-chip-title {
        color: rgb(120 53 15 / 0.9);
    }

    .theme-dark .panel-elite-shell .panel-pro-chip-title,
    .dark .panel-elite-shell .panel-pro-chip-title {
        color: rgb(253 224 71 / 0.82);
    }

    .panel-elite-shell .panel-pro-actions {
        gap: 0.65rem;
        align-items: center;
        padding-top: 0.85rem;
        border-top: 1px solid rgb(234 179 8 / 0.18);
    }

    .theme-dark .panel-elite-shell .panel-pro-actions,
    .dark .panel-elite-shell .panel-pro-actions {
        border-top-color: rgb(234 179 8 / 0.12);
    }

    .panel-elite-shell .panel-pro-actions .ui-button {
        min-height: 2.84rem;
        border-radius: 0.98rem;
        box-shadow: 0 16px 28px -24px rgb(15 23 42 / 0.32);
    }

    .panel-elite-shell .panel-pro-actions .ui-button:first-child {
        border-color: rgb(234 179 8 / 0.42);
        background: linear-gradient(135deg, rgb(250 204 21), rgb(16 185 129));
        color: rgb(6 23 18);
        box-shadow: 0 20px 36px -24px rgb(16 185 129 / 0.38);
    }

    .theme-dark .panel-elite-shell .panel-pro-actions .ui-button:first-child,
    .dark .panel-elite-shell .panel-pro-actions .ui-button:first-child {
        color: rgb(4 12 16);
    }

    .panel-elite-shell .panel-pro-actions .ui-button:not(:first-child) {
        background: rgb(255 255 255 / 0.54);
        border-color: rgb(234 179 8 / 0.14);
    }

    .theme-dark .panel-elite-shell .panel-pro-actions .ui-button:not(:first-child),
    .dark .panel-elite-shell .panel-pro-actions .ui-button:not(:first-child) {
        background: rgb(15 23 42 / 0.42);
        border-color: rgb(234 179 8 / 0.12);
    }

    .panel-elite-shell .panel-pro-insights {
        display: grid;
        gap: 0.7rem;
    }

    @media (min-width: 768px) {
        .panel-elite-shell .panel-pro-metric:first-child {
            grid-column: span 2;
        }

        .panel-elite-shell .panel-pro-insights {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1200px) {
        .panel-elite-shell .panel-pro-insights {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    .panel-branch-shell {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        border: 1px solid rgb(20 184 166 / 0.22);
        border-radius: 1.22rem;
        background:
            radial-gradient(circle at top right, rgb(34 211 238 / 0.14), transparent 34%),
            radial-gradient(circle at bottom left, rgb(16 185 129 / 0.1), transparent 28%),
            linear-gradient(150deg, rgb(255 255 255 / 0.99), rgb(248 250 252 / 0.95));
        box-shadow: 0 28px 56px -40px rgb(15 23 42 / 0.5);
        backdrop-filter: blur(14px);
        padding: 1.05rem;
    }

    .theme-dark .panel-branch-shell,
    .dark .panel-branch-shell {
        border-color: rgb(34 211 238 / 0.24);
        background:
            radial-gradient(circle at top right, rgb(34 211 238 / 0.11), transparent 34%),
            radial-gradient(circle at bottom left, rgb(16 185 129 / 0.08), transparent 28%),
            linear-gradient(155deg, rgb(3 10 24 / 0.95), rgb(11 24 36 / 0.9));
        box-shadow: 0 30px 58px -42px rgb(2 8 23 / 0.9);
    }

    .panel-branch-shell::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgb(255 255 255 / 0.72), transparent);
        opacity: 0.8;
        pointer-events: none;
    }

    .panel-branch-shell::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(95deg, transparent, rgb(34 211 238 / 0.04), transparent);
    }

    .panel-branch-grid {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1rem;
    }

    .panel-branch-head {
        align-items: end;
        gap: 1rem;
    }

    .panel-branch-copy {
        max-width: 52rem;
    }

    .panel-branch-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgb(13 148 136 / 0.96);
    }

    .theme-dark .panel-branch-kicker,
    .dark .panel-branch-kicker {
        color: rgb(153 246 228 / 0.94);
    }

    .panel-branch-kicker::before {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 999px;
        background: rgb(20 184 166 / 0.96);
        box-shadow: 0 0 0 6px rgb(20 184 166 / 0.14);
    }

    .panel-branch-heading {
        margin-top: 0.75rem;
        font-size: clamp(1.14rem, 1.85vw, 1.46rem);
        line-height: 1.08;
        letter-spacing: -0.035em;
        font-weight: 900;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-branch-heading,
    .dark .panel-branch-heading {
        color: rgb(241 245 249 / 0.98);
    }

    .panel-branch-summary {
        margin-top: 0.5rem;
        font-size: 0.89rem;
        line-height: 1.58;
        color: rgb(71 85 105 / 0.94);
    }

    .theme-dark .panel-branch-summary,
    .dark .panel-branch-summary {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-branch-badge {
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

    .theme-dark .panel-branch-badge,
    .dark .panel-branch-badge {
        border-color: rgb(20 184 166 / 0.26);
        background: rgb(13 148 136 / 0.12);
        color: rgb(153 246 228 / 0.95);
    }

    .panel-branch-metrics {
        display: grid;
        gap: 0.75rem;
    }

    .panel-branch-metric {
        position: relative;
        overflow: hidden;
        min-height: 6.7rem;
        border-radius: 1.02rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: linear-gradient(180deg, rgb(255 255 255 / 0.9), rgb(248 250 252 / 0.74));
        box-shadow: 0 18px 30px -28px rgb(15 23 42 / 0.28);
        padding: 0.9rem 0.95rem;
    }

    .theme-dark .panel-branch-metric,
    .dark .panel-branch-metric {
        border-color: rgb(148 163 184 / 0.16);
        background: linear-gradient(160deg, rgb(15 23 42 / 0.74), rgb(15 23 42 / 0.54));
        box-shadow: 0 20px 34px -28px rgb(2 8 23 / 0.9);
    }

    .panel-branch-metric::before {
        content: '';
        position: absolute;
        left: 0.9rem;
        right: 0.9rem;
        top: 0;
        height: 2px;
        border-radius: 999px;
        background: rgb(148 163 184 / 0.22);
    }

    .panel-branch-metric[data-tone='success']::before {
        background: linear-gradient(90deg, rgb(16 185 129 / 0.9), rgb(16 185 129 / 0.24));
    }

    .panel-branch-metric[data-tone='info']::before {
        background: linear-gradient(90deg, rgb(34 211 238 / 0.9), rgb(34 211 238 / 0.24));
    }

    .panel-branch-metric[data-tone='warning']::before {
        background: linear-gradient(90deg, rgb(245 158 11 / 0.9), rgb(245 158 11 / 0.24));
    }

    .panel-branch-metric[data-tone='accent']::before {
        background: linear-gradient(90deg, rgb(20 184 166 / 0.9), rgb(20 184 166 / 0.24));
    }

    .panel-branch-metric-label {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .panel-branch-metric-label,
    .dark .panel-branch-metric-label {
        color: rgb(148 163 184 / 0.9);
    }

    .panel-branch-metric-value {
        margin-top: 0.45rem;
        font-size: 1.42rem;
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: rgb(15 23 42 / 0.96);
    }

    .theme-dark .panel-branch-metric-value,
    .dark .panel-branch-metric-value {
        color: rgb(248 250 252 / 0.98);
    }

    .panel-branch-metric-note {
        margin-top: 0.4rem;
        font-size: 0.76rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-branch-metric-note,
    .dark .panel-branch-metric-note {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-branch-insights {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .panel-branch-chip {
        min-width: min(100%, 14rem);
        flex: 1 1 14rem;
        border-radius: 0.95rem;
        border: 1px solid rgb(148 163 184 / 0.2);
        background: rgb(255 255 255 / 0.66);
        padding: 0.78rem 0.85rem;
        box-shadow: 0 16px 28px -30px rgb(15 23 42 / 0.32);
    }

    .theme-dark .panel-branch-chip,
    .dark .panel-branch-chip {
        border-color: rgb(148 163 184 / 0.14);
        background: rgb(15 23 42 / 0.58);
        box-shadow: 0 20px 30px -30px rgb(2 8 23 / 0.9);
    }

    .panel-branch-chip[data-tone='warning'] {
        border-color: rgb(245 158 11 / 0.22);
        background: rgb(255 251 235 / 0.92);
    }

    .panel-branch-chip[data-tone='danger'] {
        border-color: rgb(244 63 94 / 0.22);
        background: rgb(255 241 242 / 0.92);
    }

    .panel-branch-chip[data-tone='success'] {
        border-color: rgb(16 185 129 / 0.22);
        background: rgb(236 253 245 / 0.92);
    }

    .panel-branch-chip[data-tone='info'] {
        border-color: rgb(34 211 238 / 0.22);
        background: rgb(236 254 255 / 0.92);
    }

    .theme-dark .panel-branch-chip[data-tone='warning'],
    .dark .panel-branch-chip[data-tone='warning'] {
        background: rgb(120 53 15 / 0.18);
    }

    .theme-dark .panel-branch-chip[data-tone='danger'],
    .dark .panel-branch-chip[data-tone='danger'] {
        background: rgb(136 19 55 / 0.18);
    }

    .theme-dark .panel-branch-chip[data-tone='success'],
    .dark .panel-branch-chip[data-tone='success'] {
        background: rgb(6 78 59 / 0.18);
    }

    .theme-dark .panel-branch-chip[data-tone='info'],
    .dark .panel-branch-chip[data-tone='info'] {
        background: rgb(8 145 178 / 0.14);
    }

    .panel-branch-chip-title {
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: rgb(30 41 59 / 0.92);
    }

    .theme-dark .panel-branch-chip-title,
    .dark .panel-branch-chip-title {
        color: rgb(226 232 240 / 0.96);
    }

    .panel-branch-chip-copy {
        margin-top: 0.35rem;
        font-size: 0.76rem;
        line-height: 1.45;
        color: rgb(71 85 105 / 0.9);
    }

    .theme-dark .panel-branch-chip-copy,
    .dark .panel-branch-chip-copy {
        color: rgb(148 163 184 / 0.88);
    }

    .panel-branch-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .panel-branch-actions .ui-button {
        min-height: 2.72rem;
    }

    @media (min-width: 640px) {
        .panel-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-control-priority-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .panel-control-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-pro-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-branch-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 639px) {
        .panel-control-actions .ui-button {
            width: 100%;
        }

        .panel-pro-actions .ui-button {
            width: 100%;
        }

        .panel-branch-actions .ui-button {
            width: 100%;
        }
    }

    @media (min-width: 1280px) {
        .panel-kpi-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .panel-pro-metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .panel-branch-metrics {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .panel-premium-mode .panel-kpi-card {
        min-height: 7.9rem;
        border-radius: 0.9rem;
        padding: 0.85rem;
    }

    .panel-premium-mode .panel-kpi-title {
        min-height: 2rem;
    }

    .panel-premium-mode .panel-kpi-value {
        font-size: clamp(1.72rem, 2.4vw, 2.12rem);
        letter-spacing: -0.02em;
    }

    .panel-premium-mode .panel-cta-grid {
        display: grid;
        gap: 0.55rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .panel-premium-mode .panel-cta-grid .ui-button {
        width: 100%;
        min-height: 2.6rem;
    }

    .panel-premium-mode .panel-cash-today-grid {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    @media (min-width: 640px) {
        .panel-premium-mode .panel-cta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-today-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .panel-premium-mode .panel-main-split {
            grid-template-columns: minmax(0, 1fr) 320px;
        }

        .panel-control-grid {
            grid-template-columns: minmax(0, 1fr) 19rem;
            align-items: start;
        }

        .panel-control-actions {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-kpi-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cta-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-today-grid {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-session-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1536px) {
        .panel-premium-mode .panel-main-split {
            grid-template-columns: minmax(0, 1fr) 340px;
        }

        .panel-premium-mode .panel-kpi-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cta-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-today-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-premium-mode .panel-cash-session-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currencyFormatter = \App\Support\Currency::class;
        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $monthCurrentLabel = now()->format('M Y');
        $monthPreviousLabel = now()->subMonthNoOverflow()->format('M Y');
        $monthlyBarsMax = max(1, (float) collect($incomeLast6Months)->max('income'));
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $isCashierScoped = (bool) ($isCashierScoped ?? false);
        $planAccessService = app(\App\Services\PlanAccessService::class);
        $activeGymId = (int) (request()->attributes->get('active_gym_id') ?? auth()->user()?->gym_id ?? 0);
        $canUseSalesInventory = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'sales_inventory');
        $canManageCashiers = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'cashiers');
        $canManageClientAccounts = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'client_accounts');
        $canViewReports = $activeGymId > 0 && $planAccessService->canForGym($activeGymId, 'reports_base');
        $contextGym = (string) request()->route('contextGym');
        $hubContextGym = (string) (request()->attributes->get('hub_gym_slug') ?: $contextGym);
        $panelRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $hubPanelRouteParams = ['contextGym' => $hubContextGym];
        $globalPanelRouteParams = ['contextGym' => $hubContextGym, 'scope' => 'global'];
        $panelSessionSummary = $openSessionScopedSummary ?? [
            'opening_balance' => 0,
            'income_total' => 0,
            'expense_total' => 0,
            'net_total' => 0,
            'visible_total' => 0,
            'movements_count' => 0,
        ];
        $clientShowUrl = static fn (int $clientId): string => route('clients.show', ['contextGym' => $contextGym, 'client' => $clientId] + ($isGlobalScope ? ['scope' => 'global'] : []));
        $planControlDashboard = is_array($planControlDashboard ?? null) ? $planControlDashboard : null;
        $planProfessionalDashboard = is_array($planProfessionalDashboard ?? null) ? $planProfessionalDashboard : null;
        $planPremiumDashboard = is_array($planPremiumDashboard ?? null) ? $planPremiumDashboard : null;
        $planBranchesDashboard = is_array($planBranchesDashboard ?? null) ? $planBranchesDashboard : null;
    ?>

    <section class="panel-main-split xl:grid-cols-[minmax(0,1fr)_340px] xl:items-start">
    <div class="space-y-4">
    <?php if($planBranchesDashboard): ?>
        <?php
            $branchAlerts = collect($planBranchesDashboard['alerts'] ?? [])->values();
            $branchMetrics = [
                [
                    'label' => 'Red activa',
                    'value' => (string) ((int) ($planBranchesDashboard['branch_network_gyms_count'] ?? 1)),
                    'note' => ((int) ($planBranchesDashboard['branch_links_count'] ?? 0)) > 0
                        ? (string) ((int) ($planBranchesDashboard['branch_links_count'] ?? 0)).' sucursal(es) + sede principal'
                        : 'Solo sede principal por ahora.',
                    'tone' => 'accent',
                ],
                [
                    'label' => 'Modo actual',
                    'value' => (string) ($planBranchesDashboard['scope_value'] ?? 'Global'),
                    'note' => (string) ($planBranchesDashboard['scope_note'] ?? 'Vista consolidada de la red.'),
                    'tone' => 'info',
                ],
                [
                    'label' => 'Clientes en red',
                    'value' => (string) number_format((int) ($planBranchesDashboard['total_clients'] ?? 0)),
                    'note' => 'Base total visible desde el panel multisucursal.',
                    'tone' => 'success',
                ],
                [
                    'label' => 'Membresias / check-ins',
                    'value' => (string) ((int) ($planBranchesDashboard['active_memberships'] ?? 0)).' / '.(string) ((int) ($planBranchesDashboard['checkins_today'] ?? 0)),
                    'note' => 'Activas y presentes hoy dentro de toda la red.',
                    'tone' => 'warning',
                ],
            ];
        ?>
        <section class="panel-branch-shell">
            <div class="panel-branch-grid">
                <div class="panel-branch-head flex flex-wrap items-start justify-between gap-3">
                    <div class="panel-branch-copy">
                        <span class="panel-branch-kicker">Plan Sucursales / Red operativa</span>
                        <h2 class="panel-branch-heading"><?php echo e($planBranchesDashboard['headline'] ?? 'Tu red multisucursal ya se controla desde una sola vista.'); ?></h2>
                        <p class="panel-branch-summary"><?php echo e($planBranchesDashboard['summary'] ?? 'Consolida clientes, membresias y presencia entre sede principal y sucursales sin salir del panel.'); ?></p>
                    </div>
                    <span class="panel-branch-badge">Sucursales</span>
                </div>

                <div class="panel-branch-metrics">
                    <?php $__currentLoopData = $branchMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="panel-branch-metric" data-tone="<?php echo e($metric['tone']); ?>">
                            <p class="panel-branch-metric-label"><?php echo e($metric['label']); ?></p>
                            <p class="panel-branch-metric-value"><?php echo e($metric['value']); ?></p>
                            <p class="panel-branch-metric-note"><?php echo e($metric['note']); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php if($branchAlerts->isNotEmpty()): ?>
                    <div class="panel-branch-insights">
                        <?php $__currentLoopData = $branchAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="panel-branch-chip" data-tone="<?php echo e($alert['tone'] ?? 'info'); ?>">
                                <p class="panel-branch-chip-title"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                <p class="panel-branch-chip-copy"><?php echo e($alert['description'] ?? ''); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <div class="panel-branch-actions">
                    <?php if($isGlobalScope): ?>
                        <?php if(\Illuminate\Support\Facades\Route::has('branches.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('branches.index', ['contextGym' => $hubContextGym, 'scope' => 'global']),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('branches.index', ['contextGym' => $hubContextGym, 'scope' => 'global'])),'variant' => 'primary']); ?>
                                Abrir modulo sucursales
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
                        <?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('panel.index', $hubPanelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('panel.index', $hubPanelRouteParams)),'variant' => 'secondary']); ?>Abrir sede principal <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('panel.index', $globalPanelRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('panel.index', $globalPanelRouteParams)),'variant' => 'primary']); ?>Cambiar a admin global <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if(\Illuminate\Support\Facades\Route::has('branches.index')): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('branches.index', ['contextGym' => $hubContextGym, 'scope' => 'global']),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('branches.index', ['contextGym' => $hubContextGym, 'scope' => 'global'])),'variant' => 'secondary']); ?>
                                Abrir modulo sucursales
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
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $panelRouteParams)),'variant' => 'ghost']); ?><?php echo e($isGlobalScope ? 'Clientes globales' : 'Clientes sede principal'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if($canViewReports && \Illuminate\Support\Facades\Route::has('reports.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $panelRouteParams)),'variant' => 'ghost']); ?>Ver reportes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $panelRouteParams)),'variant' => 'ghost']); ?><?php echo e($isGlobalScope ? 'Caja consolidada' : 'Caja principal'); ?> <?php echo $__env->renderComponent(); ?>
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
    <?php if($planPremiumDashboard): ?>
        <?php
            $premiumAlerts = collect($planPremiumDashboard['alerts'] ?? [])->values();
            $premiumMetrics = [
                [
                    'label' => 'Portal cliente',
                    'value' => $planPremiumDashboard['portal_status'] ?? 'Activar',
                    'note' => $planPremiumDashboard['portal_note'] ?? 'Aun no hay accesos cliente listos.',
                    'tone' => 'accent',
                ],
                [
                    'label' => 'Clientes con acceso',
                    'value' => (string) ((int) ($planPremiumDashboard['clients_with_app_accounts'] ?? 0)),
                    'note' => (string) ((int) ($planPremiumDashboard['active_clients_with_app_accounts'] ?? 0)).' activos ya pueden usar app.',
                    'tone' => 'success',
                ],
                [
                    'label' => 'Pendientes app',
                    'value' => (string) ((int) ($planPremiumDashboard['active_clients_without_app_accounts'] ?? 0)),
                    'note' => 'Clientes activos aun sin usuario premium.',
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Equipo activo',
                    'value' => (string) ((int) ($planPremiumDashboard['active_cashiers'] ?? 0)).'/'.max(0, (int) ($planPremiumDashboard['max_cashiers'] ?? 0)),
                    'note' => 'Cajeros listos | Promos '.(int) ($planPremiumDashboard['active_promotions_count'] ?? 0),
                    'tone' => 'info',
                ],
            ];
        ?>
        <section class="panel-pro-shell panel-elite-shell">
            <div class="panel-pro-grid">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="panel-pro-copy">
                        <span class="panel-pro-kicker">Plan Elite / Panel</span>
                        <h2 class="panel-pro-heading"><?php echo e($planPremiumDashboard['headline'] ?? 'Experiencia premium lista para clientes y equipo'); ?></h2>
                        <p class="panel-pro-summary"><?php echo e($planPremiumDashboard['summary'] ?? 'Portal cliente, accesos app y equipo dentro de una misma lectura premium.'); ?></p>
                    </div>
                    <span class="panel-pro-badge">Premium</span>
                </div>

                <div class="panel-pro-metrics">
                    <?php $__currentLoopData = $premiumMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="panel-pro-metric" data-tone="<?php echo e($metric['tone']); ?>">
                            <p class="panel-pro-metric-label"><?php echo e($metric['label']); ?></p>
                            <p class="panel-pro-metric-value"><?php echo e($metric['value']); ?></p>
                            <p class="panel-pro-metric-note"><?php echo e($metric['note']); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php if($premiumAlerts->isNotEmpty()): ?>
                    <div class="panel-pro-insights">
                        <?php $__currentLoopData = $premiumAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="panel-pro-chip" data-tone="<?php echo e($alert['tone'] ?? 'info'); ?>">
                                <p class="panel-pro-chip-title"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                <p class="panel-pro-chip-copy"><?php echo e($alert['description'] ?? ''); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <div class="panel-pro-actions">
                    <?php if($canManageClientAccounts && \Illuminate\Support\Facades\Route::has('client-portal.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('client-portal.index', $panelRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('client-portal.index', $panelRouteParams)),'variant' => 'primary']); ?>Portal cliente <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', $panelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $panelRouteParams)),'variant' => 'secondary']); ?>Gestionar clientes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if($canManageCashiers && \Illuminate\Support\Facades\Route::has('staff.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('staff.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('staff.index', $panelRouteParams)),'variant' => 'ghost']); ?>Gestionar cajeros <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php elseif($canViewReports && \Illuminate\Support\Facades\Route::has('reports.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $panelRouteParams)),'variant' => 'ghost']); ?>Ver reportes <?php echo $__env->renderComponent(); ?>
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
                    <?php if($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('sales.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $panelRouteParams)),'variant' => 'ghost']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
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
    <?php if($planProfessionalDashboard): ?>
        <?php
            $professionalAlerts = collect($planProfessionalDashboard['alerts'] ?? [])->values();
            $professionalMetrics = [
                [
                    'label' => 'Total comercial mes',
                    'value' => $currencyFormatter::format((float) ($planProfessionalDashboard['commercial_month_total'] ?? 0), $appCurrencyCode),
                    'note' => 'Membresias y productos ya facturados este mes.',
                    'tone' => 'success',
                ],
                [
                    'label' => 'Cobros de membresias',
                    'value' => $currencyFormatter::format((float) ($planProfessionalDashboard['membership_income_current_month'] ?? 0), $appCurrencyCode),
                    'note' => 'Renovadas o activadas durante el mes actual.',
                    'tone' => 'info',
                ],
                [
                    'label' => 'Ventas de productos',
                    'value' => $currencyFormatter::format((float) ($planProfessionalDashboard['product_sales_income_current_month'] ?? 0), $appCurrencyCode),
                    'note' => (int) ($planProfessionalDashboard['product_sales_month_count'] ?? 0).' ticket(s) | Prom. '.$currencyFormatter::format((float) ($planProfessionalDashboard['average_product_ticket'] ?? 0), $appCurrencyCode, true),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Promos y cajero',
                    'value' => (string) ((int) ($planProfessionalDashboard['active_promotions_count'] ?? 0)),
                    'note' => 'Cajero '.(int) ($planProfessionalDashboard['active_cashiers'] ?? 0).'/'.max(0, (int) ($planProfessionalDashboard['max_cashiers'] ?? 0)).' | Stock bajo '.(int) ($planProfessionalDashboard['low_stock_products_count'] ?? 0),
                    'tone' => 'accent',
                ],
            ];
        ?>
        <section class="panel-pro-shell">
            <div class="panel-pro-grid">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="panel-pro-copy">
                        <span class="panel-pro-kicker">Plan Profesional / Crecimiento</span>
                        <h2 class="panel-pro-heading"><?php echo e($planProfessionalDashboard['headline'] ?? 'Radar comercial activo'); ?></h2>
                        <p class="panel-pro-summary"><?php echo e($planProfessionalDashboard['summary'] ?? 'Ventas, promociones, reportes y primer cajero dentro de una misma lectura compacta.'); ?></p>
                    </div>
                    <span class="panel-pro-badge">Profesional</span>
                </div>

                <div class="panel-pro-metrics">
                    <?php $__currentLoopData = $professionalMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="panel-pro-metric" data-tone="<?php echo e($metric['tone']); ?>">
                            <p class="panel-pro-metric-label"><?php echo e($metric['label']); ?></p>
                            <p class="panel-pro-metric-value"><?php echo e($metric['value']); ?></p>
                            <p class="panel-pro-metric-note"><?php echo e($metric['note']); ?></p>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php if($professionalAlerts->isNotEmpty()): ?>
                    <div class="panel-pro-insights">
                        <?php $__currentLoopData = $professionalAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="panel-pro-chip" data-tone="<?php echo e($alert['tone'] ?? 'info'); ?>">
                                <p class="panel-pro-chip-title"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                <p class="panel-pro-chip-copy"><?php echo e($alert['description'] ?? ''); ?></p>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <div class="panel-pro-actions">
                    <?php if($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('sales.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $panelRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $panelRouteParams)),'variant' => 'primary']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
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
                    <?php if($canViewReports && \Illuminate\Support\Facades\Route::has('reports.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.index', $panelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.index', $panelRouteParams)),'variant' => 'secondary']); ?>Ver reportes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index', $panelRouteParams)),'variant' => 'ghost']); ?>Planes y promos <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if($canManageCashiers && \Illuminate\Support\Facades\Route::has('staff.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('staff.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('staff.index', $panelRouteParams)),'variant' => 'ghost']); ?>Gestionar cajero <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php elseif($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('products.index')): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('products.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('products.index', $panelRouteParams)),'variant' => 'ghost']); ?>
                            <?php echo e(((int) ($planProfessionalDashboard['low_stock_products_count'] ?? 0)) > 0 ? 'Reponer stock bajo' : 'Ver productos'); ?>

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
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php if($planControlDashboard): ?>
        <?php
            $controlChecklist = collect($planControlDashboard['checklist'] ?? [])->values();
            $controlPriorities = collect($planControlDashboard['priorities'] ?? [])->take(3)->values();
            $controlPendingSteps = (int) $controlChecklist->where('completed', false)->count();
            $controlHeroTitle = $controlPendingSteps > 0
                ? 'Activa lo esencial y deja la sede lista para cobrar'
                : 'Opera una sola sede con caja, recepcion y vencidos en orden';
            $controlHeroSummary = $controlPendingSteps > 0
                ? 'Completa solo lo importante y deja el panel libre para operar. El resto queda guardado dentro del checklist.'
                : 'Tu base ya esta lista. Usa esta franja para decidir rapido que cobrar, que renovar y que revisar hoy.';
            $controlNextAction = (array) ($planControlDashboard['next_action'] ?? []);
            $controlNextActionUrl = (string) ($controlNextAction['action_url'] ?? route('reception.index', $panelRouteParams));
            $secondaryControlAction = str_contains($controlNextActionUrl, '/clients')
                ? ['label' => 'Ir a recepcion', 'url' => route('reception.index', $panelRouteParams)]
                : ['label' => 'Cobrar membresia', 'url' => route('clients.index', $panelRouteParams)];
        ?>
        <section class="panel-control-shell">
            <div class="panel-control-grid">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="panel-control-copy">
                            <span class="panel-control-kicker">Plan Control / 1 sede</span>
                            <h2 class="panel-control-heading mt-3"><?php echo e($controlHeroTitle); ?></h2>
                            <p><?php echo e($controlHeroSummary); ?></p>
                        </div>
                        <span class="panel-control-progress"><?php echo e($planControlDashboard['progress_label'] ?? '0 de 0 pasos'); ?></span>
                    </div>

                    <?php if($controlPriorities->isNotEmpty()): ?>
                        <div class="panel-control-priority-grid">
                            <?php $__currentLoopData = $controlPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="panel-control-priority" data-tone="<?php echo e($priority['tone'] ?? 'info'); ?>">
                                    <p class="panel-control-priority-label"><?php echo e($priority['label'] ?? 'Pendiente'); ?></p>
                                    <p class="panel-control-priority-value"><?php echo e($priority['value'] ?? '0'); ?></p>
                                    <p class="panel-control-priority-note"><?php echo e($priority['description'] ?? ''); ?></p>
                                    <?php if(! empty($priority['action_url'])): ?>
                                        <a href="<?php echo e((string) $priority['action_url']); ?>" class="mt-3 inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-lime-700 transition hover:text-lime-800 dark:text-lime-300 dark:hover:text-lime-200">
                                            <?php echo e($priority['action_label'] ?? 'Abrir'); ?>

                                            <span aria-hidden="true">-></span>
                                        </a>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="space-y-4">
                    <div class="panel-control-actions">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $controlNextActionUrl,'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($controlNextActionUrl),'variant' => 'primary']); ?>
                            <?php echo e($controlNextAction['action_label'] ?? 'Abrir panel'); ?>

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
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $secondaryControlAction['url'],'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($secondaryControlAction['url']),'variant' => 'secondary']); ?>
                            <?php echo e($secondaryControlAction['label']); ?>

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

                    <details class="panel-control-disclosure">
                        <summary>Activacion inicial</summary>
                        <div class="panel-control-checklist">
                            <?php $__currentLoopData = $controlChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="panel-control-check-item <?php echo e(! empty($item['completed']) ? 'is-complete' : ''); ?>">
                                    <div class="min-w-0">
                                        <p class="panel-control-check-title"><?php echo e($item['label'] ?? 'Paso'); ?></p>
                                        <p class="panel-control-check-copy"><?php echo e($item['description'] ?? ''); ?></p>
                                        <?php if(empty($item['completed']) && ! empty($item['action_url'])): ?>
                                            <a href="<?php echo e((string) $item['action_url']); ?>" class="mt-3 inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-slate-700 transition hover:text-slate-900 dark:text-slate-200 dark:hover:text-slate-50">
                                                <?php echo e($item['action_label'] ?? 'Abrir'); ?>

                                                <span aria-hidden="true">-></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <span class="panel-control-check-badge <?php echo e(! empty($item['completed']) ? 'is-complete' : 'is-pending'); ?>">
                                        <?php echo e(! empty($item['completed']) ? 'Listo' : 'Pendiente'); ?>

                                    </span>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </details>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['id' => 'tour-panel-summary','class' => 'panel-summary-card','title' => 'Resumen del dia','subtitle' => ''.e($planControlDashboard ? 'Caja, vencidos y accesos en una sola vista para una sede.' : 'Indicadores clave para tomar decisiones rapidas.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-summary','class' => 'panel-summary-card','title' => 'Resumen del dia','subtitle' => ''.e($planControlDashboard ? 'Caja, vencidos y accesos en una sola vista para una sede.' : 'Indicadores clave para tomar decisiones rapidas.').'']); ?>
        <?php if($isCashierScoped): ?>
            <p class="mb-4 ui-alert ui-alert-info">Vista privada: aqui solo ves tus cobros, movimientos y acumulados del mes actual.</p>
        <?php endif; ?>
        <div class="panel-kpi-grid">
            <article class="panel-kpi-card flex flex-col justify-between border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/75">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-slate-500 dark:text-slate-300">Clientes</p>
                <p class="panel-kpi-value mt-1 font-black text-slate-900 dark:text-slate-100"><?php echo e($totalClients); ?></p>
                <p class="min-h-[16px] text-xs text-slate-500 dark:text-slate-300">Base registrada</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-emerald-700 dark:text-emerald-200">Membresías activas</p>
                <p class="panel-kpi-value mt-1 font-black text-emerald-800 dark:text-emerald-100"><?php echo e($activeMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-emerald-700 dark:text-emerald-200">Vigentes hoy</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-amber-200 bg-amber-50 dark:border-amber-400/40 dark:bg-amber-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-amber-700 dark:text-amber-200">Por vencer</p>
                <p class="panel-kpi-value mt-1 font-black text-amber-800 dark:text-amber-100"><?php echo e($expiringSoonMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-amber-700 dark:text-amber-200">Próximas 48 horas</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-rose-700 dark:text-rose-200">Vencid@s</p>
                <p class="panel-kpi-value mt-1 font-black text-rose-800 dark:text-rose-100"><?php echo e($expiredMemberships); ?></p>
                <p class="min-h-[16px] text-xs text-rose-700 dark:text-rose-200">Requieren renovación</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-cyan-200 bg-cyan-50 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-cyan-700 dark:text-cyan-200">Check-ins hoy</p>
                <p class="panel-kpi-value mt-1 font-black text-cyan-800 dark:text-cyan-100"><?php echo e((int) $checkinsToday); ?></p>
                <p class="min-h-[16px] text-xs text-cyan-700 dark:text-cyan-200">Se reinicia 12:00 AM</p>
            </article>
            <article class="panel-kpi-card flex flex-col justify-between border border-violet-200 bg-violet-50 dark:border-violet-400/40 dark:bg-violet-500/15">
                <p class="panel-kpi-title text-xs font-bold uppercase leading-tight tracking-wider text-violet-700 dark:text-violet-200">Planes activos</p>
                <p class="panel-kpi-value mt-1 font-black text-violet-800 dark:text-violet-100"><?php echo e($activePlans); ?></p>
                <p class="min-h-[16px] text-xs text-violet-700 dark:text-violet-200">Oferta vigente</p>
            </article>
        </div>

        <?php if($planControlDashboard): ?>
            <div class="panel-control-summary-actions mt-4">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'tour-panel-go-clients','href' => route('clients.index', $panelRouteParams),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-go-clients','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', $panelRouteParams)),'variant' => 'primary']); ?>Cobrar membresia <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reception.index', $panelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reception.index', $panelRouteParams)),'variant' => 'secondary']); ?>Ir a recepcion <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index', $panelRouteParams)),'variant' => 'ghost']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index', $panelRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index', $panelRouteParams)),'variant' => 'ghost']); ?>Planes <?php echo $__env->renderComponent(); ?>
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
        <?php else: ?>
            <div class="panel-cta-grid mt-4">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reception.index'),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reception.index')),'variant' => 'primary']); ?>Ir a recepcion <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'tour-panel-go-clients','href' => route('clients.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-go-clients','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index')),'variant' => 'secondary']); ?>Panel de clientes <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if($canUseSalesInventory && \Illuminate\Support\Facades\Route::has('sales.index')): ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $panelRouteParams),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $panelRouteParams)),'variant' => 'secondary']); ?>Ventas e inventario <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index'),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index')),'variant' => 'ghost']); ?>Ver planes <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'ghost']); ?>Ir a caja <?php echo $__env->renderComponent(); ?>
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
        <?php endif; ?>
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

    <section class="grid gap-4 xl:grid-cols-3">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Comparativo mensual personal' : 'Comparativo mensual').'','subtitle' => ''.e($isCashierScoped ? 'Compara tus cobros del mes actual contra tu mes anterior.' : 'Si las ventas van mejor o peor vs el mes anterior.').'','class' => 'panel-summary-card xl:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Comparativo mensual personal' : 'Comparativo mensual').'','subtitle' => ''.e($isCashierScoped ? 'Compara tus cobros del mes actual contra tu mes anterior.' : 'Si las ventas van mejor o peor vs el mes anterior.').'','class' => 'panel-summary-card xl:col-span-2']); ?>
            <div class="grid gap-3 md:grid-cols-3">
                <article class="panel-inline-metric rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200"><?php echo e($monthCurrentLabel); ?></p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) $incomeCurrentMonth, $appCurrencyCode)); ?></p>
                    <p class="text-xs text-cyan-700 dark:text-cyan-200"><?php echo e($isCashierScoped ? 'Tus ingresos del mes' : 'Ingresos del mes'); ?></p>
                </article>
                <article class="panel-inline-metric rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300"><?php echo e($monthPreviousLabel); ?></p>
                    <p class="mt-1 text-2xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $incomePreviousMonth, $appCurrencyCode)); ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-300"><?php echo e($isCashierScoped ? 'Tu mes anterior' : 'Mes anterior'); ?></p>
                </article>
                <article class="panel-inline-metric rounded-xl border p-3 <?php echo e($monthlyIncomeDiff >= 0 ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15'); ?>">
                    <p class="text-xs font-bold uppercase tracking-wider <?php echo e($monthlyIncomeDiff >= 0 ? 'text-emerald-700 dark:text-emerald-200' : 'text-rose-700 dark:text-rose-200'); ?>"><?php echo e($isCashierScoped ? 'Variación personal' : 'Variación'); ?></p>
                    <p class="mt-1 text-2xl font-black <?php echo e($monthlyIncomeDiff >= 0 ? 'text-emerald-800 dark:text-emerald-100' : 'text-rose-800 dark:text-rose-100'); ?>">
                        <?php echo e($monthlyIncomeDiff >= 0 ? '+' : ''); ?><?php echo e($currencyFormatter::format((float) $monthlyIncomeDiff, $appCurrencyCode, true)); ?>

                    </p>
                    <p class="text-xs <?php echo e($monthlyIncomeDiff >= 0 ? 'text-emerald-700 dark:text-emerald-200' : 'text-rose-700 dark:text-rose-200'); ?>">
                        <?php if($monthlyIncomePct !== null): ?>
                            <?php echo e($monthlyIncomePct >= 0 ? '+' : ''); ?><?php echo e(number_format((float) $monthlyIncomePct, 1)); ?>%
                        <?php else: ?>
                            Sin base de comparación
                        <?php endif; ?>
                    </p>
                </article>
            </div>

            <div class="mt-4 space-y-2">
                <?php $__currentLoopData = $incomeLast6Months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $barWidth = min(100, max(6, ($row['income'] / $monthlyBarsMax) * 100));
                    ?>
                    <div class="grid grid-cols-[68px_1fr_88px] items-center gap-2 text-xs sm:grid-cols-[84px_1fr_120px]">
                        <span class="font-semibold text-slate-600 dark:text-slate-300"><?php echo e($row['label']); ?></span>
                        <div class="h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-2 rounded-full bg-cyan-500 dark:bg-cyan-400" style="width: <?php echo e(number_format($barWidth, 2, '.', '')); ?>%;"></div>
                        </div>
                        <span class="text-right font-semibold text-slate-700 dark:text-slate-200"><?php echo e($currencyFormatter::format((float) $row['income'], $appCurrencyCode, true)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => ''.e($isCashierScoped ? 'Tu producción de hoy' : 'Caja y ventas hoy').'','class' => 'panel-summary-card xl:col-span-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($isCashierScoped ? 'Tu producción de hoy' : 'Caja y ventas hoy').'','class' => 'panel-summary-card xl:col-span-1']); ?>
            <div class="panel-cash-today-grid">
                <article class="panel-inline-metric rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200"><?php echo e($isCashierScoped ? 'Tus ingresos hoy' : 'Ingresos hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) $incomeToday, $appCurrencyCode)); ?></p>
                </article>
                <article class="panel-inline-metric rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200"><?php echo e($isCashierScoped ? 'Tus egresos hoy' : 'Egresos hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) $expenseToday, $appCurrencyCode)); ?></p>
                </article>
                <article class="panel-inline-metric rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200"><?php echo e($isCashierScoped ? 'Tu balance hoy' : 'Balance hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black <?php echo e((float) $todayBalance >= 0 ? 'text-cyan-800 dark:text-cyan-100' : 'text-rose-800 dark:text-rose-100'); ?>"><?php echo e($currencyFormatter::format((float) $todayBalance, $appCurrencyCode)); ?></p>
                </article>
                <article class="panel-inline-metric rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200"><?php echo e($isCashierScoped ? 'Tus cobros de membresías' : 'Cobros de membresías'); ?></p>
                    <p class="mt-1 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) ($membershipIncomeToday ?? 0), $appCurrencyCode)); ?></p>
                    <p class="text-xs text-emerald-700 dark:text-emerald-200">Mes: <?php echo e($currencyFormatter::format((float) ($membershipIncomeCurrentMonth ?? 0), $appCurrencyCode, true)); ?></p>
                </article>
                <article class="panel-inline-metric rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200"><?php echo e($isCashierScoped ? 'Tus ventas de inventario' : 'Ventas de inventario'); ?></p>
                    <p class="mt-1 text-2xl font-black text-amber-800 dark:text-amber-100"><?php echo e($currencyFormatter::format((float) ($productSalesIncomeToday ?? 0), $appCurrencyCode)); ?></p>
                    <p class="text-xs text-amber-700 dark:text-amber-200">Mes: <?php echo e($currencyFormatter::format((float) ($productSalesIncomeCurrentMonth ?? 0), $appCurrencyCode, true)); ?></p>
                </article>
                <article class="panel-inline-metric rounded-xl border p-3 <?php echo e((float) $netYearToDate >= 0 ? 'border-violet-200 bg-violet-50 dark:border-violet-400/40 dark:bg-violet-500/15' : 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15'); ?>">
                    <p class="text-xs font-bold uppercase tracking-wider <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200'); ?>"><?php echo e($isCashierScoped ? 'Tu acumulado del año' : 'Ganancia del año'); ?></p>
                    <p class="mt-1 text-2xl font-black <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-800 dark:text-violet-100' : 'text-rose-800 dark:text-rose-100'); ?>"><?php echo e($currencyFormatter::format((float) $netYearToDate, $appCurrencyCode)); ?></p>
                    <p class="text-xs <?php echo e((float) $netYearToDate >= 0 ? 'text-violet-700 dark:text-violet-200' : 'text-rose-700 dark:text-rose-200'); ?>"><?php echo e($isCashierScoped ? 'Tus ingresos menos egresos en el año' : 'Ingresos - egresos acumulados del año'); ?></p>
                </article>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'panel-summary-card','title' => ''.e($isCashierScoped ? 'Tu actividad de caja actual' : 'Estado de caja actual').'','subtitle' => ''.e($isCashierScoped ? 'Resumen privado de tus movimientos dentro del turno activo.' : 'Control rápido del turno activo.').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'panel-summary-card','title' => ''.e($isCashierScoped ? 'Tu actividad de caja actual' : 'Estado de caja actual').'','subtitle' => ''.e($isCashierScoped ? 'Resumen privado de tus movimientos dentro del turno activo.' : 'Control rápido del turno activo.').'']); ?>
        <?php if($isGlobalScope): ?>
            <p class="ui-alert ui-alert-info">Modo global activo: esta vista consolida sedes y no permite abrir o cerrar turnos desde el panel.</p>
            <div class="mt-3">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'secondary']); ?>Ver consolidado de caja <?php echo $__env->renderComponent(); ?>
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
        <?php elseif($openSession): ?>
            <?php if($isCashierScoped): ?>
                <div class="panel-cash-session-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <article class="panel-inline-metric rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Turno</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">#<?php echo e($openSession->id); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Tus registros dentro del turno</p>
                    </article>
                    <article class="panel-inline-metric rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['opening_balance'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Monto inicial del turno</p>
                    </article>
                    <article class="panel-inline-metric rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Tus ingresos</p>
                        <p class="mt-1 text-xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['income_total'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-emerald-700 dark:text-emerald-200">Cobros del turno activo</p>
                    </article>
                    <article class="panel-inline-metric rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Tus egresos</p>
                        <p class="mt-1 text-xl font-black text-rose-800 dark:text-rose-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['expense_total'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-rose-700 dark:text-rose-200">Salidas registradas por ti</p>
                    </article>
                    <article class="panel-inline-metric rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Saldo visible</p>
                        <p class="mt-1 text-xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) ($panelSessionSummary['visible_total'] ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-cyan-700 dark:text-cyan-200">Apertura + tus movimientos</p>
                    </article>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Tus movimientos</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e((int) ($panelSessionSummary['movements_count'] ?? 0)); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Registrados por tu usuario</p>
                    </article>
                </div>
            <?php else: ?>
                <div class="panel-cash-session-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="panel-inline-metric rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Turno</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100">#<?php echo e($openSession->id); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Abierto</p>
                    </article>
                    <article class="panel-inline-metric rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Apertura</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e($currencyFormatter::format((float) $openSession->opening_balance, $appCurrencyCode)); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300"><?php echo e($openSession->opened_at?->format('Y-m-d H:i')); ?></p>
                    </article>
                    <article class="panel-inline-metric rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                        <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Esperado actual</p>
                        <p class="mt-1 text-xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e($currencyFormatter::format((float) ($openSessionExpected ?? 0), $appCurrencyCode)); ?></p>
                        <p class="text-xs text-cyan-700 dark:text-cyan-200">Caja operando</p>
                    </article>
                    <article class="panel-inline-metric rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Abierta por</p>
                        <p class="mt-1 text-xl font-black text-slate-900 dark:text-slate-100"><?php echo e($openSession->openedBy?->name ?? '-'); ?></p>
                        <p class="text-xs text-slate-500 dark:text-slate-300">Usuario responsable</p>
                    </article>
                </div>
            <?php endif; ?>
            <div class="mt-3">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'secondary']); ?><?php echo e($isCashierScoped ? 'Ir a tu caja' : 'Ir a caja por turno'); ?> <?php echo $__env->renderComponent(); ?>
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
        <?php else: ?>
            <p class="ui-alert ui-alert-warning">No hay turno de caja abierto ahora mismo.</p>
            <div class="mt-3">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('cash.index'),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('cash.index')),'variant' => 'primary']); ?>Abrir caja <?php echo $__env->renderComponent(); ?>
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
        <?php endif; ?>
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

    <div class="space-y-4">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['id' => 'tour-panel-tracking','class' => 'panel-side-card','title' => 'Centro de seguimiento','subtitle' => 'Abre detalle en modal para evitar saturar la pantalla.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tour-panel-tracking','class' => 'panel-side-card','title' => 'Centro de seguimiento','subtitle' => 'Abre detalle en modal para evitar saturar la pantalla.']); ?>
            <div class="grid gap-3">
                <article class="panel-inline-metric rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200">Renovaciones 48h</p>
                    <p class="mt-1 text-2xl font-black text-amber-800 dark:text-amber-100"><?php echo e($upcomingRenewals->count()); ?></p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-renewals">Ver detalle</button>
                </article>
                <article class="panel-inline-metric rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Check-ins de hoy</p>
                    <p class="mt-1 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e((int) $checkinsToday); ?></p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-checkins">Ver detalle</button>
                </article>
                <article class="panel-inline-metric rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                    <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200"><?php echo e($isCashierScoped ? 'Tus movimientos hoy' : 'Movimientos de hoy'); ?></p>
                    <p class="mt-1 text-2xl font-black text-violet-800 dark:text-violet-100"><?php echo e($movementsTodayCount); ?></p>
                    <button type="button" class="mt-2 ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-movements">Ver detalle</button>
                </article>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['class' => 'panel-side-card','title' => 'Renovar vencid@s','subtitle' => 'Acciones rápidas para clientes con membresía vencida.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'panel-side-card','title' => 'Renovar vencid@s','subtitle' => 'Acciones rápidas para clientes con membresía vencida.']); ?>
            <div class="panel-inline-metric rounded-xl border border-rose-200 bg-rose-50 p-3 dark:border-rose-400/40 dark:bg-rose-500/15">
                <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-200">Total vencid@s</p>
                <p class="mt-1 text-2xl font-black text-rose-800 dark:text-rose-100"><?php echo e($expiredMemberships); ?></p>
            </div>

            <?php if($expiredRenewalCandidates->isNotEmpty()): ?>
                <div class="mt-3 space-y-2">
                    <?php $__currentLoopData = $expiredRenewalCandidates->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expiredClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $expiredLabel = $expiredClient->days_expired === null
                                ? 'Sin fecha'
                                : ($expiredClient->days_expired <= 0 ? 'Hoy' : ($expiredClient->days_expired === 1 ? 'Hace 1 día' : 'Hace '.$expiredClient->days_expired.' días'));
                        ?>
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-200/80 bg-slate-50/80 p-2 dark:border-slate-700 dark:bg-slate-900/70">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100"><?php echo e($expiredClient->client_name); ?></p>
                                <?php if($isGlobalScope): ?>
                                    <p class="mt-0.5 text-[11px] font-semibold text-cyan-700 dark:text-cyan-300"><?php echo e($expiredClient->gym_name ?? '-'); ?></p>
                                <?php endif; ?>
                                <p class="truncate text-xs text-slate-600 dark:text-slate-300"><?php echo e($expiredClient->plan_name); ?> · <?php echo e($expiredLabel); ?></p>
                            </div>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $expiredClient->client_id),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $expiredClient->client_id)),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']); ?><?php echo e($isGlobalScope ? 'Ver' : 'Renovar'); ?> <?php echo $__env->renderComponent(); ?>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <p class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-800 dark:border-emerald-400/40 dark:bg-emerald-500/15 dark:text-emerald-200">
                    No hay vencid@s por renovar.
                </p>
            <?php endif; ?>

            <div class="mt-3 flex flex-wrap gap-2">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', ['filter' => 'expired']),'variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', ['filter' => 'expired'])),'variant' => 'ghost','size' => 'sm']); ?>Ver listado vencid@s <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-open-modal="modal-expired-renewals">Ver detalle</button>
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
    </section>

    <div id="modal-renewals" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalRenewalsTitle">
        <div class="ui-modal-panel max-w-5xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalRenewalsTitle" class="ui-heading text-lg">Próximas renovaciones (48 horas)</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[760px]">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th>Plan</th>
                        <th>Vence</th>
                        <th>Días</th>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingRenewals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $daysLeft = (int) ($membership->days_left ?? 0);
                            $daysLabel = $daysLeft <= 0 ? 'Hoy' : ($daysLeft === 1 ? '1 día' : $daysLeft.' días');
                        ?>
                        <tr>
                            <td><?php echo e($membership->client_name); ?></td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($membership->gym_name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($membership->plan_name); ?></td>
                            <td><?php echo e($membership->ends_at?->format('Y-m-d') ?? '-'); ?></td>
                            <td><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $daysLeft <= 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($daysLeft <= 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info'))]); ?><?php echo e($daysLabel); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                            <td class="text-right"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $membership->client_id),'size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $membership->client_id)),'size' => 'sm','variant' => 'ghost']); ?>Ver cliente <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 6 : 5); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">Sin renovaciones en las próximas 48 horas.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-expired-renewals" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalExpiredRenewalsTitle">
        <div class="ui-modal-panel max-w-5xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalExpiredRenewalsTitle" class="ui-heading text-lg">Renovar vencid@s</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[760px]">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th>Plan</th>
                        <th>Vencio</th>
                        <th>Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $expiredRenewalCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expiredClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $expiredStatusLabel = $expiredClient->membership_status === 'cancelled'
                                ? 'Cancelada'
                                : ($expiredClient->days_expired === null ? 'Sin fecha' : ($expiredClient->days_expired <= 0 ? 'Hoy' : ($expiredClient->days_expired === 1 ? 'Hace 1 día' : 'Hace '.$expiredClient->days_expired.' días')));
                        ?>
                        <tr>
                            <td><?php echo e($expiredClient->client_name); ?></td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($expiredClient->gym_name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($expiredClient->plan_name); ?></td>
                            <td><?php echo e($expiredClient->ends_at?->format('Y-m-d') ?? '-'); ?></td>
                            <td>
                                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $expiredClient->membership_status === 'cancelled' ? 'warning' : 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expiredClient->membership_status === 'cancelled' ? 'warning' : 'danger')]); ?>
                                    <?php echo e($expiredStatusLabel); ?>

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
                            <td class="text-right">
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $expiredClient->client_id),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $expiredClient->client_id)),'size' => 'sm','variant' => ''.e($isGlobalScope ? 'ghost' : 'secondary').'']); ?><?php echo e($isGlobalScope ? 'Ver' : 'Renovar'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 6 : 5); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">No hay clientes vencid@s para renovar.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 flex justify-end">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.index', ['filter' => 'expired']),'variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.index', ['filter' => 'expired'])),'variant' => 'ghost','size' => 'sm']); ?>Ir a clientes vencid@s <?php echo $__env->renderComponent(); ?>
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
    </div>

    <div id="modal-checkins" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalCheckinsTitle">
        <div class="ui-modal-panel max-w-4xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalCheckinsTitle" class="ui-heading text-lg">Check-ins de hoy</h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[640px]">
                    <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th class="text-right">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $todayAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($attendance->time); ?></td>
                            <td><?php echo e($attendance->client?->full_name ?? '-'); ?></td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($attendance->gym?->name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td class="text-right"><?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $clientShowUrl((int) $attendance->client_id),'size' => 'sm','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($clientShowUrl((int) $attendance->client_id)),'size' => 'sm','variant' => 'ghost']); ?>Perfil <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 4 : 3); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300">Aún no hay check-ins hoy.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-movements" class="ui-modal-backdrop hidden panel-modal" role="dialog" aria-modal="true" aria-labelledby="modalMovementsTitle">
        <div class="ui-modal-panel max-w-6xl">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 id="modalMovementsTitle" class="ui-heading text-lg"><?php echo e($isCashierScoped ? 'Tus últimos movimientos de caja' : 'Últimos movimientos de caja'); ?></h3>
                <button type="button" class="ui-button ui-button-ghost px-3 py-1 text-xs" data-close-modal>Cerrar</button>
            </div>
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[940px]">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <?php if($isGlobalScope): ?>
                            <th>Sede</th>
                        <?php endif; ?>
                        <th>Usuario</th>
                        <th>Descripción</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentCashMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($movement->occurred_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                            <td>
                                <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $movement->type === 'income' ? 'success' : 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($movement->type === 'income' ? 'success' : 'danger')]); ?>
                                    <?php echo e($movement->type === 'income' ? 'Ingreso' : 'Egreso'); ?>

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
                            <td><?php echo e($methodLabels[$movement->method] ?? $movement->method); ?></td>
                            <td class="<?php echo e($movement->type === 'income' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'); ?> font-semibold">
                                <?php echo e($movement->type === 'income' ? '+' : '-'); ?><?php echo e($currencyFormatter::format((float) $movement->amount, $appCurrencyCode, true)); ?>

                            </td>
                            <?php if($isGlobalScope): ?>
                                <td><?php echo e($movement->gym?->name ?? '-'); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($movement->createdBy?->name ?? '-'); ?></td>
                            <td class="max-w-[340px] truncate" title="<?php echo e($movement->description ?: '-'); ?>"><?php echo e($movement->description ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isGlobalScope ? 7 : 6); ?>" class="text-center text-sm text-slate-500 dark:text-slate-300"><?php echo e($isCashierScoped ? 'Aún no tienes movimientos registrados.' : 'No hay movimientos registrados aún.'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        function closeAllPanelModals() {
            document.querySelectorAll('.panel-modal').forEach(function (modal) {
                modal.classList.add('hidden');
            });
            document.body.classList.remove('overflow-hidden');
        }

        document.querySelectorAll('[data-open-modal]').forEach(function (button) {
            button.addEventListener('click', function () {
                const modalId = button.getAttribute('data-open-modal');
                const modal = modalId ? document.getElementById(modalId) : null;
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach(function (button) {
            button.addEventListener('click', closeAllPanelModals);
        });

        document.querySelectorAll('.panel-modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeAllPanelModals();
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllPanelModals();
            }
        });

    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/panel/index.blade.php ENDPATH**/ ?>