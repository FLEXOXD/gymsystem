<style>
    :root {
        --premium-bg: #060c14;
        --premium-bg-soft: #0c1522;
        --premium-surface: rgba(11, 18, 30, 0.78);
        --premium-surface-strong: #111b29;
        --premium-surface-stronger: #152235;
        --premium-line: rgba(148, 163, 184, 0.16);
        --premium-line-strong: rgba(132, 245, 204, 0.28);
        --premium-text: #eef5ff;
        --premium-muted: #98a8bb;
        --premium-accent: #84f5cc;
        --premium-accent-strong: #a8ff7b;
        --premium-blue: #71aaff;
        --premium-shadow: 0 28px 80px rgba(0, 0, 0, 0.36);
    }

    body {
        color: var(--premium-text);
        background: linear-gradient(180deg, #040913 0%, #08101c 46%, #070d17 100%);
    }

    body.is-home {
        background:
            radial-gradient(circle at 12% 10%, rgba(113, 170, 255, 0.16), transparent 26%),
            radial-gradient(circle at 84% 12%, rgba(132, 245, 204, 0.16), transparent 20%),
            linear-gradient(180deg, #040913 0%, #08101c 46%, #070d17 100%);
    }

    body::before {
        background:
            radial-gradient(36% 30% at 18% 12%, rgba(113, 170, 255, 0.12), transparent 72%),
            radial-gradient(34% 28% at 76% 68%, rgba(132, 245, 204, 0.1), transparent 74%);
        filter: blur(28px);
    }

    body::after {
        background: radial-gradient(32% 28% at 52% 36%, rgba(168, 255, 123, 0.08), transparent 74%);
        filter: blur(34px);
    }

    .home-scroll-bg::after {
        background:
            linear-gradient(120deg, rgba(4, 10, 18, 0.92) 0%, rgba(5, 13, 22, 0.78) 40%, rgba(4, 10, 18, 0.92) 100%),
            radial-gradient(circle at 76% 20%, rgba(113, 170, 255, 0.12), transparent 34%),
            radial-gradient(circle at 20% 18%, rgba(132, 245, 204, 0.1), transparent 28%);
    }

    .top-wrap {
        padding-top: 0.85rem;
        backdrop-filter: blur(16px);
        background: linear-gradient(180deg, rgba(4, 8, 16, 0.88), rgba(4, 8, 16, 0.18));
    }

    .top-wrap.is-compact {
        padding-top: 0.42rem;
        background: linear-gradient(180deg, rgba(4, 8, 16, 0.96), rgba(4, 8, 16, 0.42));
    }

    .top-nav {
        gap: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.6rem;
        background: rgba(9, 14, 24, 0.8);
        box-shadow: 0 22px 54px rgba(0, 0, 0, 0.28);
        padding: 0.9rem 1.15rem;
    }

    .top-wrap.is-compact .top-nav {
        padding: 0.72rem 1rem;
        border-color: rgba(148, 163, 184, 0.18);
        box-shadow: 0 18px 42px rgba(0, 0, 0, 0.3);
    }

    .brand {
        gap: 0.95rem;
        width: 182px;
        min-width: 182px;
    }

    .brand-logo {
        filter: drop-shadow(0 12px 30px rgba(113, 170, 255, 0.12));
    }

    .brand-fallback {
        background: linear-gradient(145deg, rgba(132, 245, 204, 0.18), rgba(113, 170, 255, 0.2));
        border: 1px solid rgba(132, 245, 204, 0.3);
        color: var(--premium-text);
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.25);
    }

    .menu-links {
        gap: 0.32rem;
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 999px;
        background: rgba(13, 20, 32, 0.66);
        padding: 0.28rem;
    }

    .menu-links a {
        color: #c7d3e3;
        font-size: 0.86rem;
        font-weight: 700;
        padding: 0.56rem 1rem;
        letter-spacing: 0.01em;
    }

    .menu-links a:hover {
        color: #ffffff;
        background: rgba(113, 170, 255, 0.12);
    }

    .menu-links a.is-active {
        color: #ffffff;
        background: linear-gradient(135deg, rgba(113, 170, 255, 0.18), rgba(132, 245, 204, 0.18));
        border: 1px solid rgba(132, 245, 204, 0.22);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.2);
    }

    .nav-actions {
        gap: 0.65rem;
    }

    .mobile-menu-toggle {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1rem;
        background: rgba(12, 19, 30, 0.88);
        color: var(--premium-text);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
    }

    .mobile-nav-panel {
        position: fixed;
        top: 1rem;
        left: 1rem;
        right: 1rem;
        z-index: 45;
        display: block;
        margin-top: 0;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 1.4rem;
        background: rgba(9, 14, 24, 0.96);
        box-shadow: 0 28px 60px rgba(0, 0, 0, 0.38);
        padding: 1rem;
        transform: translateY(-12px);
        opacity: 0;
        pointer-events: none;
        transition: transform 0.24s ease, opacity 0.24s ease;
    }

    .mobile-nav-panel.is-open {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .mobile-nav-links a {
        color: var(--premium-text);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 0.95rem;
        padding: 0.82rem 0.95rem;
        background: rgba(16, 24, 38, 0.72);
    }

    .mobile-nav-links a.is-active {
        border-color: rgba(132, 245, 204, 0.34);
        background: linear-gradient(135deg, rgba(113, 170, 255, 0.18), rgba(132, 245, 204, 0.16));
    }

    .btn {
        min-height: 48px;
        padding: 0.72rem 1.18rem;
        border-radius: 1rem;
        font-size: 0.92rem;
        font-weight: 800;
        letter-spacing: 0.01em;
        transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease, background 0.24s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-demo {
        color: #04110e;
        background: linear-gradient(135deg, var(--premium-accent) 0%, var(--premium-accent-strong) 100%);
        box-shadow: 0 18px 34px rgba(132, 245, 204, 0.2);
    }

    .btn-outline {
        color: var(--premium-text);
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(14, 21, 34, 0.8);
    }

    .btn-wa {
        color: var(--premium-text);
        border: 1px solid rgba(113, 170, 255, 0.24);
        background: linear-gradient(135deg, rgba(20, 30, 48, 0.92), rgba(14, 23, 37, 0.92));
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.24);
    }

    .btn-ghost {
        color: #ced8e7;
        border: 1px solid transparent;
        background: transparent;
    }

    .btn-ghost:hover {
        border-color: rgba(148, 163, 184, 0.18);
        background: rgba(255, 255, 255, 0.04);
    }

    .main {
        padding-bottom: 4.6rem;
    }

    .flash {
        border: 1px solid rgba(132, 245, 204, 0.18);
        border-radius: 1rem;
        background: rgba(10, 18, 30, 0.9);
        color: var(--premium-text);
    }

    .flash-error {
        border-color: rgba(244, 114, 182, 0.22);
        background: rgba(42, 16, 28, 0.78);
    }

    .premium-section {
        margin-top: 4rem;
        position: relative;
        z-index: 2;
    }

    .premium-section-head {
        max-width: 760px;
        margin-bottom: 1.6rem;
    }

    .premium-section-head h2 {
        margin: 0.82rem 0 0;
        font-size: clamp(2rem, 3.1vw, 3.4rem);
        line-height: 1.02;
        letter-spacing: -0.04em;
    }

    .premium-section-head p {
        margin: 0.9rem 0 0;
        color: var(--premium-muted);
        font-size: 1.02rem;
        line-height: 1.7;
    }

    .premium-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.16em;
        font-size: 0.76rem;
        font-weight: 900;
        color: var(--premium-accent);
    }

    .premium-eyebrow::before {
        content: "";
        width: 0.58rem;
        height: 0.58rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--premium-accent), var(--premium-blue));
        box-shadow: 0 0 0 5px rgba(132, 245, 204, 0.08);
    }

    .premium-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 0.35rem 0.72rem;
        border-radius: 999px;
        border: 1px solid rgba(132, 245, 204, 0.2);
        background: rgba(132, 245, 204, 0.08);
        color: #dff8ef;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .premium-hero-section {
        position: relative;
        padding-top: clamp(2.2rem, 4vw, 3.6rem);
    }

    .premium-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(420px, 0.96fr);
        gap: clamp(1.4rem, 3vw, 3rem);
        align-items: center;
    }

    .premium-kicker-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem;
        align-items: center;
    }

    .premium-hero-title {
        margin: 1rem 0 0;
        font-size: clamp(2.8rem, 6vw, 5.3rem);
        line-height: 0.95;
        letter-spacing: -0.06em;
        max-width: 12ch;
    }

    .premium-hero-text {
        margin: 1rem 0 0;
        max-width: 62ch;
        color: var(--premium-muted);
        font-size: 1.08rem;
        line-height: 1.74;
    }

    .premium-hero-actions {
        margin-top: 1.35rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .premium-btn-primary,
    .premium-btn-secondary {
        min-width: 200px;
    }

    .premium-hero-microcopy {
        margin: 1rem 0 0;
        color: #adc0d4;
        font-size: 0.92rem;
        line-height: 1.65;
        max-width: 60ch;
    }

    .premium-hero-microcopy a {
        color: var(--premium-text);
        font-weight: 700;
    }

    .premium-hero-points {
        margin-top: 1.4rem;
        display: grid;
        gap: 0.8rem;
    }

    .premium-hero-point {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.85rem;
        padding: 0.95rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.25rem;
        background: rgba(12, 19, 31, 0.72);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
    }

    .premium-hero-point-mark {
        position: relative;
        width: 1rem;
        height: 1rem;
        border-radius: 999px;
        margin-top: 0.2rem;
        background: linear-gradient(135deg, var(--premium-accent), var(--premium-accent-strong));
        box-shadow: 0 0 0 6px rgba(132, 245, 204, 0.08);
    }

    .premium-hero-point-mark::after {
        content: "";
        position: absolute;
        inset: 0.26rem;
        border-radius: 999px;
        background: #08111c;
    }

    .premium-hero-point h3 {
        margin: 0;
        font-size: 1rem;
        line-height: 1.2;
    }

    .premium-hero-point p {
        margin: 0.42rem 0 0;
        color: var(--premium-muted);
        font-size: 0.92rem;
        line-height: 1.58;
    }

    .premium-scene {
        position: relative;
        min-height: 620px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .premium-scene-glow {
        position: absolute;
        border-radius: 999px;
        filter: blur(16px);
        pointer-events: none;
    }

    .premium-scene-glow--a {
        width: 260px;
        height: 260px;
        top: 8%;
        right: 12%;
        background: rgba(113, 170, 255, 0.16);
    }

    .premium-scene-glow--b {
        width: 220px;
        height: 220px;
        bottom: 12%;
        left: 14%;
        background: rgba(132, 245, 204, 0.18);
    }

    .premium-dashboard {
        position: relative;
        width: min(100%, 620px);
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 2rem;
        background:
            linear-gradient(180deg, rgba(18, 27, 42, 0.94), rgba(10, 17, 28, 0.96)),
            linear-gradient(135deg, rgba(113, 170, 255, 0.1), transparent 35%);
        box-shadow: var(--premium-shadow);
        overflow: hidden;
        backdrop-filter: blur(14px);
    }

    .premium-dashboard::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(transparent 95%, rgba(255, 255, 255, 0.03) 96%),
            linear-gradient(90deg, transparent 95%, rgba(255, 255, 255, 0.02) 96%);
        background-size: 100% 28px, 28px 100%;
        opacity: 0.35;
        pointer-events: none;
    }

    .premium-dashboard-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.8rem;
        padding: 1rem 1.2rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }

    .premium-window-dots {
        display: inline-flex;
        gap: 0.32rem;
    }

    .premium-window-dots span {
        width: 0.6rem;
        height: 0.6rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.2);
    }

    .premium-window-dots span:first-child {
        background: rgba(248, 113, 113, 0.92);
    }

    .premium-window-dots span:nth-child(2) {
        background: rgba(250, 204, 21, 0.92);
    }

    .premium-window-dots span:nth-child(3) {
        background: rgba(74, 222, 128, 0.92);
    }

    .premium-toolbar-pill,
    .premium-toolbar-status {
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #d9e7f8;
    }

    .premium-toolbar-status {
        color: var(--premium-accent);
    }

    .premium-dashboard-shell {
        display: grid;
        grid-template-columns: 92px minmax(0, 1fr);
        gap: 1rem;
        padding: 1.1rem;
    }

    .premium-dashboard-sidebar {
        display: grid;
        gap: 0.58rem;
        align-content: start;
    }

    .premium-sidebar-badge {
        width: 3.2rem;
        height: 3.2rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(132, 245, 204, 0.24), rgba(113, 170, 255, 0.24));
        color: var(--premium-text);
        font-weight: 900;
        letter-spacing: 0.08em;
        margin-bottom: 0.28rem;
    }

    .premium-sidebar-item {
        display: inline-flex;
        align-items: center;
        min-height: 38px;
        padding: 0.65rem 0.76rem;
        border-radius: 0.95rem;
        background: rgba(255, 255, 255, 0.03);
        color: #b8c8d9;
        font-size: 0.78rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .premium-sidebar-item.is-active {
        background: linear-gradient(135deg, rgba(132, 245, 204, 0.16), rgba(113, 170, 255, 0.16));
        color: var(--premium-text);
        box-shadow: inset 0 0 0 1px rgba(132, 245, 204, 0.18);
    }

    .premium-dashboard-body {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.9rem;
    }

    .premium-screen-card {
        border: 1px solid rgba(148, 163, 184, 0.1);
        border-radius: 1.35rem;
        background: rgba(15, 23, 36, 0.82);
        padding: 1rem;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    }

    .premium-screen-card--wide,
    .premium-screen-card--mobile {
        grid-column: span 2;
    }

    .premium-card-head {
        display: flex;
        justify-content: space-between;
        gap: 0.8rem;
        align-items: flex-start;
    }

    .premium-card-label {
        display: block;
        color: #9ab0c7;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .premium-card-figure {
        display: block;
        margin-top: 0.28rem;
        font-size: 1.9rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .premium-card-trend {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        background: rgba(132, 245, 204, 0.08);
        border: 1px solid rgba(132, 245, 204, 0.14);
    }

    .premium-card-trend.is-up {
        color: #d9ffef;
    }

    .premium-chart-bars {
        margin-top: 1rem;
        height: 156px;
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        align-items: end;
        gap: 0.5rem;
    }

    .premium-chart-bars span {
        border-radius: 999px 999px 0.8rem 0.8rem;
        background: linear-gradient(180deg, rgba(132, 245, 204, 0.96), rgba(113, 170, 255, 0.9));
        box-shadow: 0 16px 22px rgba(113, 170, 255, 0.12);
    }

    .premium-task-list {
        margin: 0.9rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.7rem;
    }

    .premium-task-list li {
        display: flex;
        justify-content: space-between;
        gap: 0.8rem;
        color: #dce6f5;
        font-size: 0.92rem;
    }

    .premium-task-list li span {
        color: #96a8bb;
    }

    .premium-checkin-grid {
        margin-top: 0.9rem;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.6rem;
    }

    .premium-checkin-grid article {
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.85rem 0.72rem;
    }

    .premium-checkin-grid strong {
        display: block;
        font-size: 1.4rem;
        line-height: 1;
    }

    .premium-checkin-grid span {
        display: block;
        margin-top: 0.35rem;
        color: #96a8bb;
        font-size: 0.76rem;
        line-height: 1.4;
    }

    .premium-phone-preview {
        margin-top: 0.95rem;
        border-radius: 1.25rem;
        background: linear-gradient(180deg, rgba(11, 17, 28, 0.98), rgba(17, 25, 40, 0.98));
        padding: 0.8rem;
        display: grid;
        gap: 0.7rem;
    }

    .premium-phone-bar {
        width: 34%;
        height: 0.34rem;
        margin: 0 auto;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
    }

    .premium-phone-ticket,
    .premium-phone-notice {
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.85rem;
    }

    .premium-phone-ticket span,
    .premium-phone-notice {
        color: #9ab0c7;
        font-size: 0.8rem;
    }

    .premium-phone-ticket strong {
        display: block;
        margin-top: 0.3rem;
        font-size: 1rem;
    }

    .premium-float-card {
        position: absolute;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.25rem;
        background: rgba(13, 21, 34, 0.9);
        box-shadow: 0 20px 36px rgba(0, 0, 0, 0.3);
        padding: 0.9rem 1rem;
        max-width: 220px;
        backdrop-filter: blur(14px);
    }

    .premium-float-card span {
        display: block;
        color: #9ab0c7;
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .premium-float-card strong {
        display: block;
        margin-top: 0.36rem;
        font-size: 1.45rem;
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .premium-float-card p {
        margin: 0.52rem 0 0;
        color: var(--premium-muted);
        font-size: 0.82rem;
        line-height: 1.55;
    }

    .premium-float-card--payments {
        top: 8%;
        left: -3%;
    }

    .premium-float-card--attendance {
        right: -4%;
        top: 22%;
    }

    .premium-float-card--clients {
        left: 6%;
        bottom: 2%;
    }

    .premium-proof-strip {
        margin-top: 1.45rem;
    }

    .premium-proof-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.9rem;
    }

    .premium-proof-card {
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.35rem;
        background: rgba(11, 18, 30, 0.72);
        padding: 1.15rem;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.18);
    }

    .premium-proof-card strong {
        display: block;
        font-size: 1.5rem;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .premium-proof-card span {
        display: block;
        margin-top: 0.45rem;
        font-size: 0.92rem;
        font-weight: 700;
        color: #dce6f5;
    }

    .premium-proof-card p {
        margin: 0.55rem 0 0;
        color: var(--premium-muted);
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .premium-benefits-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .premium-benefit-card {
        position: relative;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.45rem;
        background: linear-gradient(180deg, rgba(16, 24, 38, 0.88), rgba(11, 18, 30, 0.88));
        padding: 1.25rem;
        overflow: hidden;
        transition: transform 0.24s ease, border-color 0.24s ease, box-shadow 0.24s ease;
    }

    .premium-benefit-card::after {
        content: "";
        position: absolute;
        inset: auto -10% -55% auto;
        width: 180px;
        height: 180px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(132, 245, 204, 0.12), transparent 70%);
        pointer-events: none;
    }

    .premium-benefit-card:hover {
        transform: translateY(-6px);
        border-color: rgba(132, 245, 204, 0.24);
        box-shadow: 0 26px 48px rgba(0, 0, 0, 0.24);
    }

    .premium-benefit-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(132, 245, 204, 0.16), rgba(113, 170, 255, 0.16));
        color: var(--premium-accent);
        box-shadow: inset 0 0 0 1px rgba(132, 245, 204, 0.16);
    }

    .premium-benefit-icon svg {
        width: 1.45rem;
        height: 1.45rem;
    }

    .premium-benefit-card h3 {
        margin: 1rem 0 0;
        font-size: 1.2rem;
        line-height: 1.18;
    }

    .premium-benefit-card p {
        margin: 0.6rem 0 0;
        color: var(--premium-muted);
        line-height: 1.65;
    }

    .premium-module-stack {
        display: grid;
        gap: 1rem;
    }

    .premium-module {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 0.9fr);
        gap: 1.2rem;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.8rem;
        background: linear-gradient(180deg, rgba(14, 21, 34, 0.9), rgba(10, 17, 28, 0.92));
        padding: 1.35rem;
        box-shadow: 0 22px 42px rgba(0, 0, 0, 0.24);
    }

    .premium-module-copy h3 {
        margin: 0.9rem 0 0;
        font-size: clamp(1.6rem, 2.4vw, 2.2rem);
        line-height: 1.06;
        letter-spacing: -0.04em;
    }

    .premium-module-copy p {
        margin: 0.8rem 0 0;
        color: var(--premium-muted);
        line-height: 1.72;
    }

    .premium-module-list {
        margin: 1rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.65rem;
    }

    .premium-module-list li {
        position: relative;
        padding-left: 1.35rem;
        color: #dfe7f2;
        line-height: 1.6;
    }

    .premium-module-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.68rem;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--premium-accent), var(--premium-blue));
        box-shadow: 0 0 0 5px rgba(132, 245, 204, 0.06);
    }

    .premium-module-visual {
        position: relative;
        min-height: 280px;
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 1.5rem;
        background: linear-gradient(180deg, rgba(10, 17, 28, 0.96), rgba(18, 27, 42, 0.96));
        padding: 1rem;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .premium-module-photo {
        position: absolute;
        right: 1rem;
        bottom: 1rem;
        width: 144px;
        height: 108px;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.16);
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.26);
    }

    .premium-module-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .premium-visual-console,
    .premium-visual-metrics,
    .premium-visual-phone {
        width: 100%;
        height: 100%;
        border-radius: 1.3rem;
        background: rgba(255, 255, 255, 0.03);
        padding: 1rem;
    }

    .premium-visual-console {
        display: grid;
        gap: 0.72rem;
        align-content: start;
    }

    .premium-visual-line {
        display: flex;
        justify-content: space-between;
        gap: 0.8rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.9rem;
        color: #dfe7f2;
        font-size: 0.9rem;
    }

    .premium-visual-line span {
        color: #95a9bd;
    }

    .premium-visual-badge {
        width: fit-content;
        border-radius: 999px;
        padding: 0.45rem 0.82rem;
        background: rgba(132, 245, 204, 0.1);
        border: 1px solid rgba(132, 245, 204, 0.16);
        color: #dcfff1;
        font-size: 0.76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .premium-visual-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
        align-content: start;
    }

    .premium-visual-metrics article {
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.9rem;
    }

    .premium-visual-metrics article span {
        display: block;
        color: #95a9bd;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 800;
    }

    .premium-visual-metrics article strong {
        display: block;
        margin-top: 0.42rem;
        font-size: 1.35rem;
    }

    .premium-visual-bars {
        grid-column: span 2;
        height: 126px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        align-items: end;
        gap: 0.65rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.03);
        padding: 0.85rem;
    }

    .premium-visual-bars span {
        border-radius: 999px 999px 0.7rem 0.7rem;
        background: linear-gradient(180deg, rgba(113, 170, 255, 0.95), rgba(132, 245, 204, 0.95));
    }

    .premium-visual-phone {
        width: min(230px, 100%);
        display: grid;
        gap: 0.75rem;
        justify-self: center;
        align-content: start;
    }

    .premium-visual-phone-notch {
        width: 36%;
        height: 0.36rem;
        margin: 0 auto;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
    }

    .premium-visual-phone-card,
    .premium-visual-phone-note {
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.04);
        padding: 1rem;
    }

    .premium-visual-phone-card span,
    .premium-visual-phone-note {
        color: #95a9bd;
        font-size: 0.8rem;
    }

    .premium-visual-phone-card strong {
        display: block;
        margin-top: 0.35rem;
        font-size: 1rem;
        color: #f2f8ff;
    }

    .premium-timeline {
        position: relative;
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .premium-timeline::before {
        content: "";
        position: absolute;
        left: 12%;
        right: 12%;
        top: 1.3rem;
        height: 1px;
        background: linear-gradient(90deg, rgba(113, 170, 255, 0), rgba(113, 170, 255, 0.6), rgba(132, 245, 204, 0));
        pointer-events: none;
    }

    .premium-step {
        position: relative;
        display: grid;
        gap: 0.85rem;
    }

    .premium-step-count {
        position: relative;
        z-index: 1;
        width: 2.7rem;
        height: 2.7rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--premium-accent), var(--premium-blue));
        color: #04110e;
        font-weight: 900;
        letter-spacing: 0.08em;
        box-shadow: 0 14px 24px rgba(113, 170, 255, 0.16);
    }

    .premium-step-card {
        min-height: 100%;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.4rem;
        background: rgba(11, 18, 30, 0.8);
        padding: 1.1rem;
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.2);
    }

    .premium-step-card h3 {
        margin: 0;
        font-size: 1.08rem;
        line-height: 1.3;
    }

    .premium-step-card p {
        margin: 0.62rem 0 0;
        color: var(--premium-muted);
        line-height: 1.62;
    }

    .premium-pricing-highlight {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.9rem;
        margin-bottom: 1rem;
    }

    .premium-pricing-highlight article {
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.3rem;
        background: rgba(11, 18, 30, 0.76);
        padding: 1rem;
    }

    .premium-pricing-highlight strong {
        display: block;
        font-size: 1rem;
    }

    .premium-pricing-highlight span {
        display: block;
        margin-top: 0.42rem;
        color: var(--premium-muted);
        line-height: 1.58;
        font-size: 0.88rem;
    }

    .premium-pricing-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .premium-plan-card {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.7rem;
        background: linear-gradient(180deg, rgba(14, 21, 34, 0.92), rgba(10, 17, 28, 0.94));
        padding: 1.2rem;
        box-shadow: 0 22px 42px rgba(0, 0, 0, 0.24);
        transition: transform 0.24s ease, border-color 0.24s ease, box-shadow 0.24s ease;
    }

    .premium-plan-card:hover {
        transform: translateY(-6px);
        border-color: rgba(132, 245, 204, 0.22);
        box-shadow: 0 28px 54px rgba(0, 0, 0, 0.28);
    }

    .premium-plan-card.is-featured {
        border-color: rgba(132, 245, 204, 0.34);
        box-shadow: 0 0 0 1px rgba(132, 245, 204, 0.18), 0 30px 58px rgba(0, 0, 0, 0.3);
        transform: translateY(-4px);
    }

    .premium-plan-card.is-contact {
        background: linear-gradient(180deg, rgba(18, 27, 42, 0.94), rgba(12, 19, 30, 0.96));
    }

    .premium-plan-top {
        display: flex;
        justify-content: space-between;
        gap: 0.8rem;
        align-items: flex-start;
    }

    .premium-plan-kicker {
        display: inline-flex;
        color: var(--premium-accent);
        font-size: 0.76rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .premium-plan-card h3 {
        margin: 0.5rem 0 0;
        font-size: 1.34rem;
        line-height: 1.1;
    }

    .premium-plan-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 0.32rem 0.65rem;
        border-radius: 999px;
        background: rgba(132, 245, 204, 0.12);
        border: 1px solid rgba(132, 245, 204, 0.18);
        color: #dcfff1;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .premium-plan-badge.is-soft {
        background: rgba(113, 170, 255, 0.12);
        border-color: rgba(113, 170, 255, 0.18);
        color: #dce8ff;
    }

    .premium-plan-price {
        margin-top: 1rem;
        display: grid;
        gap: 0.18rem;
    }

    .premium-plan-price strong {
        font-size: 2.55rem;
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .premium-plan-price span {
        color: var(--premium-muted);
        font-size: 0.92rem;
    }

    .premium-plan-offer {
        margin-top: 0.7rem;
        border: 1px solid rgba(132, 245, 204, 0.14);
        border-radius: 1rem;
        background: rgba(132, 245, 204, 0.08);
        padding: 0.68rem 0.75rem;
        display: grid;
        gap: 0.18rem;
    }

    .premium-plan-offer span {
        color: #9ab0c7;
        font-size: 0.8rem;
    }

    .premium-plan-offer strong {
        color: #f0fff8;
        font-size: 0.95rem;
    }

    .premium-plan-summary {
        margin: 0.85rem 0 0;
        color: var(--premium-muted);
        line-height: 1.66;
    }

    .premium-plan-meta {
        margin-top: 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.03);
        padding: 0.8rem;
        color: #dce6f5;
        line-height: 1.55;
        font-size: 0.9rem;
    }

    .premium-plan-features {
        margin: 0.95rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.62rem;
    }

    .premium-plan-features li {
        position: relative;
        padding-left: 1.3rem;
        color: #dfe7f2;
        font-size: 0.9rem;
        line-height: 1.58;
    }

    .premium-plan-features li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.66rem;
        width: 0.48rem;
        height: 0.48rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--premium-accent), var(--premium-blue));
    }

    .premium-plan-features li.is-highlight {
        padding: 0.7rem 0.8rem 0.7rem 1.9rem;
        border-radius: 1rem;
        border: 1px solid rgba(132, 245, 204, 0.16);
        background: rgba(132, 245, 204, 0.08);
    }

    .premium-plan-features li.is-highlight::before {
        left: 0.85rem;
    }

    .premium-plan-more {
        margin: 0.75rem 0 0;
        color: #9ab0c7;
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .premium-plan-actions {
        margin-top: auto;
        padding-top: 1rem;
        display: grid;
        gap: 0.68rem;
    }

    .premium-plan-actions .inline-form,
    .premium-plan-actions .btn {
        width: 100%;
    }

    .premium-plan-note {
        margin: 0.85rem 0 0;
        color: var(--premium-muted);
        font-size: 0.84rem;
        line-height: 1.58;
    }

    .premium-comparison {
        margin-top: 1.2rem;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.8rem;
        background: rgba(10, 17, 28, 0.84);
        padding: 1.2rem;
        box-shadow: 0 22px 42px rgba(0, 0, 0, 0.24);
    }

    .premium-comparison-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: end;
        margin-bottom: 1rem;
    }

    .premium-comparison-head h3 {
        margin: 0.6rem 0 0;
        font-size: 1.4rem;
    }

    .premium-comparison-head p {
        max-width: 42ch;
        margin: 0;
        color: var(--premium-muted);
        line-height: 1.58;
    }

    .premium-comparison-wrap {
        overflow-x: auto;
    }

    .premium-comparison-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    .premium-comparison-table th,
    .premium-comparison-table td {
        padding: 0.86rem 0.8rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.08);
        text-align: left;
        vertical-align: top;
    }

    .premium-comparison-table thead th {
        color: #eaf3ff;
        font-size: 0.84rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .premium-comparison-table tbody th {
        color: #dce6f5;
        font-size: 0.92rem;
        width: 22%;
    }

    .premium-comparison-table td {
        color: var(--premium-muted);
        line-height: 1.5;
    }

    .premium-confidence-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
        gap: 1rem;
    }

    .premium-confidence-card {
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.55rem;
        background: rgba(11, 18, 30, 0.78);
        padding: 1.2rem;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.22);
    }

    .premium-confidence-card--lead {
        min-height: 100%;
        background:
            radial-gradient(circle at top right, rgba(113, 170, 255, 0.14), transparent 28%),
            linear-gradient(180deg, rgba(14, 21, 34, 0.92), rgba(10, 17, 28, 0.92));
    }

    .premium-confidence-card h3 {
        margin: 0.9rem 0 0;
        font-size: 1.28rem;
        line-height: 1.2;
    }

    .premium-confidence-card p {
        margin: 0.7rem 0 0;
        color: var(--premium-muted);
        line-height: 1.68;
    }

    .premium-confidence-list {
        margin: 1rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.65rem;
    }

    .premium-confidence-list li {
        position: relative;
        padding-left: 1.35rem;
        color: #dfe7f2;
        line-height: 1.58;
    }

    .premium-confidence-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.68rem;
        width: 0.48rem;
        height: 0.48rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--premium-accent), var(--premium-blue));
    }

    .premium-confidence-side {
        display: grid;
        gap: 1rem;
    }

    .premium-faq-list {
        display: grid;
        gap: 0.8rem;
    }

    .premium-faq-item {
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.35rem;
        background: rgba(11, 18, 30, 0.78);
        overflow: hidden;
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.18);
    }

    .premium-faq-button {
        width: 100%;
        border: 0;
        background: transparent;
        color: var(--premium-text);
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        padding: 1rem 1.15rem;
        text-align: left;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 800;
    }

    .premium-faq-question {
        flex: 1 1 auto;
    }

    .premium-faq-icon {
        position: relative;
        width: 1rem;
        height: 1rem;
        flex: 0 0 1rem;
    }

    .premium-faq-icon::before,
    .premium-faq-icon::after {
        content: "";
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 2px;
        border-radius: 999px;
        background: var(--premium-accent);
        transform: translate(-50%, -50%);
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .premium-faq-icon::after {
        transform: translate(-50%, -50%) rotate(90deg);
    }

    .premium-faq-item.is-open .premium-faq-icon::after {
        opacity: 0;
        transform: translate(-50%, -50%) rotate(90deg) scaleX(0.2);
    }

    .premium-faq-content {
        max-height: 0;
        overflow: hidden;
        padding: 0 1.15rem;
        color: var(--premium-muted);
        line-height: 1.66;
        transition: max-height 0.28s ease;
    }

    .premium-faq-item.is-open .premium-faq-content {
        max-height: 240px;
        padding-bottom: 1rem;
    }

    .premium-close-panel {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.95fr);
        gap: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.9rem;
        background:
            radial-gradient(circle at 88% 14%, rgba(132, 245, 204, 0.14), transparent 24%),
            linear-gradient(180deg, rgba(14, 21, 34, 0.94), rgba(10, 17, 28, 0.94));
        padding: 1.4rem;
        box-shadow: 0 28px 56px rgba(0, 0, 0, 0.28);
    }

    .premium-close-copy h2 {
        margin: 0.86rem 0 0;
        font-size: clamp(1.9rem, 3vw, 3rem);
        line-height: 1.02;
        letter-spacing: -0.04em;
    }

    .premium-close-copy p {
        margin: 0.82rem 0 0;
        color: var(--premium-muted);
        line-height: 1.72;
    }

    .premium-close-list {
        margin: 1rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.7rem;
    }

    .premium-close-list li {
        position: relative;
        padding-left: 1.35rem;
        color: #dfe7f2;
        line-height: 1.58;
    }

    .premium-close-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.68rem;
        width: 0.48rem;
        height: 0.48rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--premium-accent), var(--premium-blue));
    }

    .premium-close-card {
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 1.55rem;
        background: rgba(8, 15, 24, 0.82);
        padding: 1.15rem;
        display: grid;
        gap: 1rem;
    }

    .premium-close-steps {
        display: grid;
        gap: 0.72rem;
    }

    .premium-close-steps article {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.75rem;
        align-items: start;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.85rem;
    }

    .premium-close-steps strong {
        width: 2rem;
        height: 2rem;
        border-radius: 0.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(132, 245, 204, 0.12);
        color: #dcfff1;
        font-size: 0.9rem;
    }

    .premium-close-steps span {
        color: var(--premium-muted);
        line-height: 1.58;
    }

    .premium-close-actions {
        display: grid;
        gap: 0.68rem;
    }

    .premium-close-actions .inline-form,
    .premium-close-actions .btn {
        width: 100%;
    }

    .premium-close-footnote {
        margin: 0;
        color: #9ab0c7;
        font-size: 0.84rem;
        line-height: 1.58;
    }

    .premium-close-footnote a {
        color: var(--premium-text);
        font-weight: 700;
    }

    .footer {
        position: relative;
        z-index: 2;
    }

    .footer-panel {
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.9rem;
        background: linear-gradient(180deg, rgba(11, 18, 30, 0.92), rgba(7, 13, 22, 0.94));
        box-shadow: 0 24px 48px rgba(0, 0, 0, 0.26);
    }

    .footer-neon-title {
        color: var(--premium-accent);
    }

    .footer a {
        color: #c6d3e3;
    }

    .footer a:hover {
        color: var(--premium-text);
    }

    .copy {
        color: var(--premium-muted);
    }

    body.quote-modal-open {
        overflow: hidden;
    }

    .quote-modal-backdrop {
        background: rgba(3, 7, 12, 0.76);
        backdrop-filter: blur(16px);
    }

    .quote-modal {
        width: min(1120px, calc(100% - 1.5rem));
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1.9rem;
        background: linear-gradient(180deg, rgba(14, 21, 34, 0.98), rgba(10, 17, 28, 0.98));
        box-shadow: 0 36px 90px rgba(0, 0, 0, 0.38);
        padding: 0;
        overflow: hidden;
    }

    .quote-modal-shell {
        display: grid;
        grid-template-columns: minmax(320px, 0.95fr) minmax(0, 1.05fr);
        align-items: stretch;
        overflow: hidden;
    }

    .quote-modal-close {
        top: 1rem;
        right: 1rem;
        width: 42px;
        height: 42px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(11, 18, 30, 0.86);
        color: var(--premium-text);
    }

    .quote-modal-side {
        position: relative;
        min-width: 0;
        overflow: hidden;
        border-right: 1px solid rgba(148, 163, 184, 0.1);
        background:
            radial-gradient(circle at top right, rgba(132, 245, 204, 0.18), transparent 30%),
            linear-gradient(180deg, rgba(17, 26, 40, 0.98), rgba(11, 18, 30, 0.98));
        padding: 2rem 1.6rem;
    }

    .quote-modal-side::after {
        background: linear-gradient(180deg, rgba(132, 245, 204, 0.18), rgba(113, 170, 255, 0));
    }

    .quote-modal-kicker {
        color: var(--premium-accent);
        font-size: 0.78rem;
        letter-spacing: 0.16em;
        font-weight: 900;
        text-transform: uppercase;
    }

    .quote-modal-title {
        margin-top: 0.8rem;
        font-size: clamp(1.8rem, 3vw, 2.45rem);
        line-height: 1.02;
        letter-spacing: -0.04em;
        color: var(--premium-text);
    }

    .quote-modal-copy {
        margin-top: 0.9rem;
        max-width: 36ch;
        color: #b8c8d9;
        line-height: 1.7;
    }

    .quote-modal-visual {
        position: relative;
        min-height: 340px;
        margin-top: 1.3rem;
    }

    .quote-modal-stat {
        position: relative;
        right: auto;
        top: auto;
        z-index: 2;
        width: min(230px, calc(100% - 0.5rem));
        min-width: 0;
        margin: 0 0 1rem auto;
        padding: 0.85rem 0.95rem;
        border: 1px solid rgba(132, 245, 204, 0.16);
        border-radius: 1.2rem;
        background: rgba(132, 245, 204, 0.08);
    }

    .quote-modal-form-panel {
        padding: 2rem 1.6rem;
        background: rgba(9, 15, 24, 0.9);
        color: var(--premium-text);
    }

    .quote-form-header h3 {
        margin: 0.7rem 0 0;
        font-size: clamp(1.8rem, 3vw, 2.35rem);
        line-height: 1.04;
        letter-spacing: -0.04em;
        color: var(--premium-text);
    }

    .quote-form-header p {
        margin: 0.8rem 0 0;
        color: #b8c8d9;
        line-height: 1.7;
    }

    .quote-form-alert {
        border-radius: 1rem;
        padding: 0.85rem 0.95rem;
    }

    .quote-plan-pill {
        border-radius: 999px;
        border: 1px solid rgba(132, 245, 204, 0.16);
        background: rgba(132, 245, 204, 0.08);
    }

    .quote-form-grid {
        gap: 0.95rem;
    }

    .quote-form-field {
        color: #dce6f5;
        font-size: 0.9rem;
        font-weight: 700;
        display: grid;
        gap: 0.52rem;
    }

    .contact-input {
        width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.14);
        border-radius: 1rem;
        background: rgba(15, 23, 36, 0.88);
        color: var(--premium-text);
        min-height: 52px;
        padding: 0.9rem 0.95rem;
        font-size: 0.96rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .contact-input::placeholder {
        color: #7f94aa;
    }

    .contact-input:focus {
        outline: none;
        border-color: rgba(132, 245, 204, 0.34);
        box-shadow: 0 0 0 4px rgba(132, 245, 204, 0.08);
        background: rgba(16, 24, 38, 0.96);
    }

    textarea.contact-input {
        min-height: 132px;
        resize: vertical;
    }

    .quote-form-help,
    .quote-form-legal {
        color: var(--premium-muted);
        font-size: 0.82rem;
        line-height: 1.55;
    }

    .quote-form-checkbox {
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.03);
        padding: 0.9rem;
    }

    .quote-form-checkbox label {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        color: #dce6f5;
    }

    .quote-form-checkbox input {
        margin-top: 0.2rem;
    }

    .quote-form-submit {
        width: 100%;
        margin-top: 1rem;
        min-height: 54px;
        font-size: 0.96rem;
    }

    @media (max-width: 1220px) {
        .premium-pricing-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 1100px) {
        .premium-hero-grid,
        .premium-module,
        .premium-confidence-grid,
        .premium-close-panel,
        .quote-modal-shell {
            grid-template-columns: 1fr;
        }

        .premium-proof-grid,
        .premium-benefits-grid,
        .premium-pricing-highlight,
        .premium-timeline {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .premium-timeline::before {
            display: none;
        }

        .premium-scene {
            min-height: 560px;
        }

        .premium-float-card--payments {
            left: 0;
        }

        .premium-float-card--attendance {
            right: 0;
        }

        .quote-modal-side {
            border-right: 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
    }

    @media (max-width: 860px) {
        .menu-links,
        .nav-actions {
            display: none;
        }

        .mobile-menu-toggle {
            display: inline-flex;
            margin-left: auto;
        }

        .brand {
            width: auto;
            min-width: 0;
        }

        .top-nav {
            gap: 0.75rem;
        }

        .premium-proof-grid,
        .premium-benefits-grid,
        .premium-pricing-highlight,
        .premium-pricing-grid,
        .premium-timeline,
        .premium-dashboard-body,
        .premium-checkin-grid {
            grid-template-columns: 1fr;
        }

        .premium-scene {
            min-height: 500px;
        }

        .premium-dashboard-shell {
            grid-template-columns: 1fr;
        }

        .premium-dashboard-sidebar {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            align-items: center;
        }

        .premium-sidebar-badge {
            width: auto;
            height: auto;
            min-height: 44px;
            grid-column: span 5;
        }

        .premium-screen-card--wide,
        .premium-screen-card--mobile {
            grid-column: span 1;
        }

        .premium-float-card {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .shell {
            width: min(100% - 1.25rem, 1240px);
        }

        .top-wrap {
            padding-top: 0.6rem;
        }

        .top-nav {
            border-radius: 1.25rem;
            padding: 0.78rem 0.82rem;
        }

        .premium-section {
            margin-top: 3.2rem;
        }

        .premium-hero-title {
            font-size: clamp(2.35rem, 12vw, 3.6rem);
        }

        .premium-hero-actions,
        .premium-close-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .premium-btn-primary,
        .premium-btn-secondary,
        .premium-btn-tertiary,
        .premium-hero-actions .btn,
        .premium-hero-actions .inline-form,
        .premium-hero-actions .inline-form .btn {
            width: 100%;
        }

        .premium-module,
        .premium-plan-card,
        .premium-close-panel,
        .premium-comparison,
        .premium-confidence-card,
        .premium-proof-card {
            border-radius: 1.35rem;
        }

        .premium-module,
        .premium-close-panel,
        .quote-modal-form-panel,
        .quote-modal-side {
            padding: 1rem;
        }

        .premium-module-visual {
            min-height: 230px;
        }

        .premium-module-photo {
            width: 108px;
            height: 84px;
        }

        .premium-close-copy h2,
        .premium-section-head h2 {
            font-size: clamp(1.8rem, 9vw, 2.6rem);
        }

        .footer-panel {
            border-radius: 1.5rem;
        }

        .quote-modal {
            width: calc(100% - 1rem);
            max-height: calc(100vh - 1rem);
            border-radius: 1.45rem;
        }

        .quote-modal-form-panel {
            max-height: none;
        }
    }
</style>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/partials/home-premium-styles.blade.php ENDPATH**/ ?>