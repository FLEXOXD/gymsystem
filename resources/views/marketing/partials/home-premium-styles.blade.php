<style>
    @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap');

    :root {
        --premium-bg: #090909;
        --premium-bg-soft: #111111;
        --premium-panel: #171717;
        --premium-panel-strong: #1e1e1e;
        --premium-line: rgba(255, 255, 255, 0.08);
        --premium-line-strong: rgba(184, 255, 31, 0.34);
        --premium-text: #f3f1e9;
        --premium-muted: #b8b5a8;
        --premium-accent: #b8ff1f;
        --premium-shadow: 0 28px 70px rgba(0, 0, 0, 0.45);
        --premium-heading: "Barlow Condensed", "Arial Narrow", sans-serif;
        --premium-body: "Manrope", "Segoe UI", sans-serif;
    }

    body {
        color: var(--premium-text);
        background:
            radial-gradient(circle at 85% 12%, rgba(184, 255, 31, 0.18), transparent 18%),
            radial-gradient(circle at 16% 84%, rgba(184, 255, 31, 0.08), transparent 26%),
            linear-gradient(180deg, #070707 0%, #0f0f0f 56%, #090909 100%);
        font-family: var(--premium-body);
    }

    body::before {
        background:
            radial-gradient(circle at 18% 18%, rgba(184, 255, 31, 0.06), transparent 30%),
            radial-gradient(circle at 80% 26%, rgba(184, 255, 31, 0.05), transparent 24%);
        filter: blur(18px);
    }

    body::after {
        background: radial-gradient(circle at 52% 30%, rgba(255, 255, 255, 0.02), transparent 38%);
        filter: blur(32px);
    }

    .home-scroll-bg::after {
        background:
            linear-gradient(180deg, rgba(7, 7, 7, 0.88), rgba(7, 7, 7, 0.68)),
            radial-gradient(circle at 88% 10%, rgba(184, 255, 31, 0.14), transparent 24%),
            radial-gradient(circle at 8% 88%, rgba(184, 255, 31, 0.06), transparent 22%);
    }

    .top-wrap {
        padding-top: .85rem;
        backdrop-filter: blur(20px);
        background: linear-gradient(180deg, rgba(9, 9, 9, .92), rgba(9, 9, 9, .18));
    }

    .top-wrap.is-compact {
        padding-top: .45rem;
        background: linear-gradient(180deg, rgba(9, 9, 9, .98), rgba(9, 9, 9, .52));
    }

    .top-nav {
        gap: 1.1rem;
        border: 1px solid rgba(184, 255, 31, .16);
        border-radius: 1.45rem;
        background: rgba(24, 24, 24, .9);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .34);
        padding: .8rem 1rem;
    }

    .brand { width: 174px; min-width: 174px; }
    .brand-logo { filter: drop-shadow(0 0 14px rgba(184, 255, 31, .14)); }

    .brand-fallback {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(184, 255, 31, .28), rgba(124, 166, 31, .18));
        border: 1px solid rgba(184, 255, 31, .36);
        color: #0b0d07;
        font-family: var(--premium-heading);
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: .06em;
    }

    .menu-links {
        gap: .3rem;
        border: 1px solid rgba(255, 255, 255, .06);
        border-radius: 999px;
        background: rgba(255, 255, 255, .03);
        padding: .24rem;
    }

    .menu-links a,
    .mobile-nav-links a {
        color: rgba(243, 241, 233, .84);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        border-radius: 999px;
    }

    .menu-links a {
        padding: .58rem .86rem;
        transition: color .24s ease, background .24s ease, transform .24s ease;
    }

    .menu-links a:hover,
    .menu-links a.is-active,
    .mobile-nav-links a.is-active {
        color: #101106;
        background: linear-gradient(135deg, var(--premium-accent), #d9ff78);
        transform: translateY(-1px);
    }

    .nav-actions { gap: .65rem; }

    .mobile-menu-toggle {
        display: none;
        border: 1px solid rgba(184, 255, 31, .16);
        border-radius: 1rem;
        background: rgba(24, 24, 24, .92);
        color: var(--premium-text);
        box-shadow: 0 14px 34px rgba(0, 0, 0, .28);
    }

    .mobile-nav-panel {
        position: fixed;
        top: 1rem;
        left: 1rem;
        right: 1rem;
        z-index: 45;
        margin-top: 0;
        border: 1px solid rgba(184, 255, 31, .16);
        border-radius: 1.3rem;
        background: rgba(18, 18, 18, .96);
        box-shadow: 0 28px 60px rgba(0, 0, 0, .42);
        padding: 1rem;
        transform: translateY(-14px);
        opacity: 0;
        pointer-events: none;
        transition: transform .24s ease, opacity .24s ease;
    }

    .mobile-nav-panel.is-open { transform: translateY(0); opacity: 1; pointer-events: auto; }
    .mobile-nav-links a { border: 1px solid rgba(255, 255, 255, .07); background: rgba(255, 255, 255, .02); }

    .btn {
        min-height: 50px;
        padding: .8rem 1.22rem;
        border-radius: .9rem;
        border: 1px solid transparent;
        font-family: var(--premium-body);
        font-size: .9rem;
        font-weight: 800;
        letter-spacing: .03em;
        text-transform: uppercase;
        transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease, background .24s ease, color .24s ease;
    }

    .btn:hover { transform: translateY(-2px); }

    .btn-demo {
        color: #0d1004;
        background: linear-gradient(135deg, var(--premium-accent), #dbff73);
        border-color: rgba(184, 255, 31, .32);
        box-shadow: 0 16px 28px rgba(184, 255, 31, .16);
    }

    .btn-outline { color: var(--premium-text); border-color: rgba(255, 255, 255, .12); background: rgba(255, 255, 255, .03); }
    .btn-wa { color: var(--premium-text); border-color: rgba(184, 255, 31, .18); background: linear-gradient(180deg, rgba(42, 52, 19, .68), rgba(22, 24, 14, .92)); box-shadow: 0 18px 34px rgba(0, 0, 0, .24); }
    .btn-ghost { color: rgba(243, 241, 233, .86); border-color: rgba(255, 255, 255, .08); background: rgba(255, 255, 255, .01); }
    .btn-ghost:hover { border-color: rgba(184, 255, 31, .18); color: var(--premium-text); background: rgba(184, 255, 31, .06); }

    .flash { border: 1px solid rgba(184, 255, 31, .18); border-radius: 1rem; background: rgba(20, 20, 20, .88); color: var(--premium-text); }
    .flash-error { border-color: rgba(255, 117, 117, .24); background: rgba(44, 18, 18, .78); }

    .premium-section { position: relative; z-index: 2; margin-top: 4rem; }
    .premium-section-head { max-width: 760px; margin-bottom: 1.6rem; }

    .premium-kicker,
    .premium-team-role,
    .premium-plan-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        color: var(--premium-accent);
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .premium-kicker::before {
        content: "";
        width: .56rem;
        height: .56rem;
        border-radius: 999px;
        background: var(--premium-accent);
        box-shadow: 0 0 0 5px rgba(184, 255, 31, .08);
    }

    .premium-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: .38rem .78rem;
        border-radius: 999px;
        background: rgba(184, 255, 31, .11);
        border: 1px solid rgba(184, 255, 31, .24);
        color: #f5ffd8;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .premium-section-head h2,
    .premium-hero-title,
    .premium-screen-copy strong,
    .premium-plan-card h3,
    .premium-close-copy h2,
    .premium-close-card h3,
    .premium-community-copy h2,
    .premium-empty-state h3 {
        font-family: var(--premium-heading);
        text-transform: uppercase;
    }

    .premium-section-head h2 {
        margin: .85rem 0 0;
        font-size: clamp(2.3rem, 4vw, 3.9rem);
        line-height: .94;
    }

    .premium-section-head p { margin: .9rem 0 0; color: var(--premium-muted); font-size: 1rem; line-height: 1.7; }

    .premium-hero-section { padding-top: clamp(2.1rem, 4vw, 3.5rem); }

    .premium-hero-section::before {
        content: "FITNESS";
        position: absolute;
        left: clamp(.3rem, 2vw, 1.2rem);
        top: 1rem;
        font-family: var(--premium-heading);
        font-size: clamp(7rem, 22vw, 16rem);
        font-weight: 800;
        line-height: .8;
        letter-spacing: .04em;
        color: rgba(184, 255, 31, .05);
        pointer-events: none;
    }

    .premium-hero-layout { display: grid; grid-template-columns: minmax(420px, .92fr) minmax(700px, 1.18fr); gap: clamp(1.2rem, 2.6vw, 2.3rem); align-items: center; }
    .premium-hero-copy { position: relative; z-index: 1; }
    .premium-hero-topline { display: flex; flex-wrap: wrap; gap: .65rem; align-items: center; }
    .premium-hero-title { margin: .9rem 0 0; max-width: 10.8ch; font-size: clamp(2.95rem, 5.3vw, 5.2rem); line-height: .9; }
    .premium-hero-text { margin: .85rem 0 0; max-width: 50ch; color: var(--premium-muted); font-size: .98rem; line-height: 1.72; }
    .premium-hero-actions { margin-top: 1.4rem; display: flex; flex-wrap: wrap; gap: .7rem; }
    .premium-hero-note { margin: 1rem 0 0; max-width: 56ch; color: rgba(243, 241, 233, .7); font-size: .92rem; line-height: 1.7; }
    .premium-hero-note a { color: var(--premium-text); font-weight: 700; }
    .premium-hero-points { margin-top: 1.05rem; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }

    .premium-hero-point,
    .premium-feature-card,
    .premium-review-card,
    .premium-faq-item,
    .premium-empty-state {
        box-shadow: 0 18px 34px rgba(0, 0, 0, .2);
    }

    .premium-hero-point {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: .68rem;
        padding: .78rem .84rem;
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 1.2rem;
        background: rgba(24, 24, 24, .8);
    }

    .premium-point-bullet { width: .95rem; height: .95rem; border-radius: 999px; background: var(--premium-accent); box-shadow: 0 0 0 6px rgba(184, 255, 31, .07); margin-top: .16rem; }
    .premium-hero-point h3 { margin: 0; font-size: .92rem; line-height: 1.18; }
    .premium-hero-point p { margin: .34rem 0 0; color: var(--premium-muted); font-size: .82rem; line-height: 1.5; }

    .premium-hero-visual { position: relative; min-height: 530px; display: grid; justify-items: end; align-items: center; }
    .premium-screen-shell { width: min(100%, 960px); display: grid; gap: .9rem; }
    .premium-screen-frame { position: relative; width: 100%; border: 1px solid rgba(184, 255, 31, .18); border-radius: 1.7rem; background: rgba(25, 25, 25, .92); box-shadow: var(--premium-shadow); overflow: hidden; }
    .premium-screen-toolbar { display: flex; align-items: center; justify-content: space-between; gap: .8rem; padding: .95rem 1.15rem; background: linear-gradient(180deg, rgba(82, 82, 72, .9), rgba(62, 62, 56, .9)); }
    .premium-screen-brand { color: var(--premium-accent); font-family: var(--premium-heading); font-size: 1.1rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .premium-screen-chip { color: #fbfbf7; font-size: .72rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }

    .premium-screen-canvas {
        position: relative;
        display: grid;
        grid-template-rows: minmax(0, 1fr) auto;
        gap: .95rem;
        min-height: auto;
        padding: 1.15rem;
        background:
            radial-gradient(circle at 78% 18%, rgba(184, 255, 31, .12), transparent 18%),
            linear-gradient(180deg, rgba(8, 8, 8, .98), rgba(12, 12, 12, .94));
    }

    .premium-screen-overlay { position: absolute; inset: 0; background: radial-gradient(circle at 70% 16%, rgba(184, 255, 31, .18), transparent 18%), linear-gradient(180deg, rgba(0, 0, 0, .08), rgba(0, 0, 0, .32)); pointer-events: none; }
    .premium-screen-main { position: relative; z-index: 1; display: grid; grid-template-columns: minmax(250px, .58fr) minmax(420px, 1.42fr); gap: .85rem; align-items: stretch; }
    .premium-screen-copy {
        position: relative;
        z-index: 1;
        max-width: none;
        min-height: 330px;
        padding: clamp(1.15rem, 2.2vw, 1.55rem);
        border: 1px solid rgba(255, 255, 255, .06);
        border-radius: 1.35rem;
        background:
            linear-gradient(180deg, rgba(26, 26, 26, .94), rgba(17, 17, 17, .98)),
            linear-gradient(135deg, rgba(184, 255, 31, .08), transparent 45%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .premium-screen-copy span { display: inline-flex; align-items: center; gap: .5rem; color: var(--premium-accent); font-size: .78rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
    .premium-screen-copy strong { display: block; margin-top: .82rem; max-width: 10.8ch; color: #f0f4cf; font-size: clamp(1.9rem, 3.2vw, 3.1rem); line-height: .92; }
    .premium-screen-copy p { margin: .78rem 0 0; max-width: 28ch; color: rgba(243, 241, 233, .82); font-size: .92rem; line-height: 1.56; }
    .premium-screen-media {
        position: relative;
        min-height: 330px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .07);
        border-radius: 1.35rem;
        background: rgba(12, 12, 12, .96);
    }
    .premium-screen-media::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 2;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .04), transparent 24%),
            linear-gradient(180deg, rgba(8, 8, 8, .04), rgba(8, 8, 8, .22)),
            radial-gradient(circle at 18% 24%, rgba(184, 255, 31, .16), transparent 28%);
        pointer-events: none;
    }
    .premium-screen-media-slide {
        position: absolute;
        inset: 0;
        opacity: 0;
        transform: scale(1.04);
        transition: opacity .45s ease, transform .7s ease;
        pointer-events: none;
    }
    .premium-screen-media-slide.is-active {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }
    .premium-screen-media-image {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
    }

    .premium-screen-nav { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .75rem; }
    .premium-screen-nav-button {
        display: grid;
        gap: .35rem;
        justify-items: start;
        padding: .88rem .92rem;
        border: 1px solid rgba(255, 255, 255, .08);
        border-radius: 1rem;
        background: rgba(10, 10, 10, .58);
        color: #f7f9ec;
        text-align: left;
        cursor: pointer;
        transition: border-color .24s ease, background .24s ease, transform .24s ease, box-shadow .24s ease;
    }
    .premium-screen-nav-button:hover { transform: translateY(-2px); }
    .premium-screen-nav-button span { color: rgba(243, 241, 233, .52); font-size: .68rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .premium-screen-nav-button strong { color: #f7f9ec; font-size: .9rem; line-height: 1.15; }
    .premium-screen-nav-button.is-active,
    .premium-screen-nav-button[aria-pressed="true"] {
        border-color: rgba(184, 255, 31, .34);
        background: linear-gradient(180deg, rgba(184, 255, 31, .14), rgba(13, 13, 13, .88));
        box-shadow: inset 0 0 0 1px rgba(184, 255, 31, .08);
    }

    .premium-screen-panel { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .8rem; }
    .premium-screen-panel article { padding: .78rem .86rem; border: 1px solid rgba(255, 255, 255, .07); border-radius: 1rem; background: rgba(10, 10, 10, .6); backdrop-filter: blur(10px); }
    .premium-screen-panel span { display: block; color: rgba(243, 241, 233, .66); font-size: .72rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .premium-screen-panel strong { display: block; margin-top: .42rem; color: #f7f9ec; font-size: 1.2rem; line-height: 1; }

    .premium-float-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; }
    .premium-float-card {
        position: relative;
        z-index: 1;
        max-width: none;
        padding: 1rem;
        border: 1px solid rgba(184, 255, 31, .16);
        border-radius: 1.15rem;
        background: rgba(22, 22, 22, .92);
        box-shadow: 0 24px 40px rgba(0, 0, 0, .32);
        backdrop-filter: blur(12px);
    }

    .premium-float-card span { display: block; color: rgba(243, 241, 233, .68); font-size: .72rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .premium-float-card strong { display: block; margin-top: .45rem; color: var(--premium-accent); font-family: var(--premium-heading); font-size: 1.55rem; line-height: 1; text-transform: uppercase; }
    .premium-float-card p { margin: .55rem 0 0; color: var(--premium-muted); font-size: .84rem; line-height: 1.6; }
    .premium-float-card.is-a,
    .premium-float-card.is-b { top: auto; right: auto; bottom: auto; left: auto; }

    .premium-feature-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .premium-feature-card { position: relative; min-height: 100%; border: 1px solid rgba(255, 255, 255, .07); border-radius: 1.35rem; background: linear-gradient(180deg, rgba(23, 23, 23, .96), rgba(16, 16, 16, .98)); padding: 1.35rem; overflow: hidden; }
    .premium-feature-card.is-accent { background: linear-gradient(180deg, rgba(124, 166, 31, .94), rgba(93, 124, 21, .94)); border-color: rgba(184, 255, 31, .34); color: #f7ffdb; }
    .premium-feature-card.is-accent p,
    .premium-feature-card.is-accent h3,
    .premium-feature-card.is-accent .premium-feature-icon { color: #f7ffdb; }
    .premium-feature-icon { width: 3rem; height: 3rem; border-radius: 1rem; display: inline-flex; align-items: center; justify-content: center; background: rgba(184, 255, 31, .1); color: var(--premium-accent); box-shadow: inset 0 0 0 1px rgba(184, 255, 31, .18); }
    .premium-feature-icon svg { width: 1.4rem; height: 1.4rem; }
    .premium-feature-card h3 { margin: .95rem 0 0; font-family: var(--premium-body); font-size: 1.5rem; font-weight: 700; line-height: 1.14; }
    .premium-feature-card p { margin: .65rem 0 0; color: var(--premium-muted); line-height: 1.7; }

    .premium-program-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.1rem; }
    .premium-program-card { position: relative; min-height: 300px; border: 1px solid rgba(255, 255, 255, .06); border-radius: 1.2rem; overflow: hidden; background: var(--premium-panel); box-shadow: 0 18px 34px rgba(0, 0, 0, .22); }
    .premium-program-card::before { content: ""; position: absolute; inset: 0; background: var(--premium-program-image) center/cover no-repeat; transform: scale(1.02); transition: transform .26s ease; }
    .premium-program-card:hover::before { transform: scale(1.08); }
    .premium-program-shade { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(7, 7, 7, .9) 0%, rgba(7, 7, 7, .58) 52%, rgba(7, 7, 7, .22) 100%), linear-gradient(180deg, rgba(7, 7, 7, .04), rgba(7, 7, 7, .24)); }
    .premium-program-copy { position: absolute; inset: auto 1.15rem 1.15rem 1.15rem; z-index: 1; max-width: 63%; }
    .premium-program-copy h3 { margin: 0; font-family: var(--premium-body); font-size: 1rem; font-weight: 700; line-height: 1.2; }
    .premium-program-copy p { margin: .5rem 0 0; color: rgba(243, 241, 233, .82); font-size: .88rem; line-height: 1.55; }
    .premium-program-link { display: inline-flex; align-items: center; gap: .45rem; margin-top: .72rem; color: #f1ffd0; font-size: .78rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; text-decoration: none; }
    .premium-program-link::after { content: ">"; font-size: .92rem; }

    .premium-community-panel { position: relative; overflow: hidden; border: 1px solid rgba(184, 255, 31, .14); border-radius: 1.8rem; min-height: 440px; background: var(--premium-panel); box-shadow: var(--premium-shadow); }
    .premium-community-panel::before { content: ""; position: absolute; inset: 0; background: var(--premium-community-image) center/cover no-repeat; transform: scale(1.02); }
    .premium-community-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(11, 11, 11, .2), rgba(11, 11, 11, .7)), radial-gradient(circle at 50% 80%, rgba(184, 255, 31, .16), transparent 34%); }
    .premium-community-copy { position: relative; z-index: 1; max-width: 720px; padding: clamp(2rem, 5vw, 3rem); text-align: center; margin: 0 auto; }
    .premium-community-copy h2 { margin: .9rem auto 0; max-width: 14ch; font-size: clamp(3rem, 6vw, 5rem); line-height: .9; }
    .premium-community-copy p { margin: .9rem auto 0; max-width: 58ch; color: rgba(243, 241, 233, .86); line-height: 1.75; }
    .premium-community-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .8rem; margin-top: 1.5rem; }
    .premium-community-stat { padding: .95rem .8rem; border: 1px solid rgba(255, 255, 255, .1); border-radius: 1rem; background: rgba(10, 10, 10, .46); backdrop-filter: blur(10px); }
    .premium-community-stat strong { display: block; font-family: var(--premium-heading); font-size: 2rem; line-height: 1; color: #f4ffd0; text-transform: uppercase; }
    .premium-community-stat span { display: block; margin-top: .36rem; color: rgba(243, 241, 233, .74); font-size: .72rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .premium-community-actions { margin-top: 1.35rem; display: flex; flex-wrap: wrap; justify-content: center; gap: .7rem; }

    .premium-team-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.15rem; align-items: stretch; }
    .premium-team-card { position: relative; display: grid; grid-template-columns: minmax(220px, .9fr) minmax(0, 1fr); overflow: hidden; min-height: 320px; border-radius: 1.4rem; background: linear-gradient(180deg, rgba(21, 21, 21, .98), rgba(14, 14, 14, .98)); border: 1px solid rgba(255, 255, 255, .06); box-shadow: 0 18px 34px rgba(0, 0, 0, .22); }
    .premium-team-photo { position: relative; min-height: 100%; overflow: hidden; background: linear-gradient(180deg, rgba(184, 255, 31, .1), rgba(0, 0, 0, 0)); }
    .premium-team-photo::before { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(184, 255, 31, .16), transparent 44%); z-index: 1; }
    .premium-team-photo img { width: 100%; height: 100%; object-fit: cover; object-position: center 28%; display: block; }
    .premium-team-card:nth-child(2) .premium-team-photo img { object-position: center 20%; }
    .premium-team-card:nth-child(3) .premium-team-photo img { object-position: center 30%; }
    .premium-team-card:nth-child(4) .premium-team-photo img { object-position: center 56%; }
    .premium-team-content { display: flex; flex-direction: column; justify-content: flex-end; padding: 1.15rem 1.1rem 1.2rem; }
    .premium-team-content p { margin: .7rem 0 0; color: var(--premium-muted); line-height: 1.65; font-size: .92rem; }

    .premium-plan-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
    .premium-plan-card { display: flex; flex-direction: column; min-height: 100%; border: 1px solid rgba(255, 255, 255, .08); border-radius: 1.55rem; background: linear-gradient(180deg, rgba(23, 23, 23, .98), rgba(16, 16, 16, .98)); padding: 1.2rem; box-shadow: 0 22px 42px rgba(0, 0, 0, .24); transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease; }
    .premium-plan-card:hover { transform: translateY(-6px); border-color: rgba(184, 255, 31, .2); box-shadow: 0 28px 54px rgba(0, 0, 0, .28); }
    .premium-plan-card.is-featured { background: linear-gradient(180deg, rgba(108, 148, 26, .92), rgba(72, 98, 17, .94)); border-color: rgba(184, 255, 31, .36); color: #f8ffd9; transform: translateY(-4px); }
    .premium-plan-card.is-featured .premium-plan-summary,
    .premium-plan-card.is-featured .premium-plan-features li,
    .premium-plan-card.is-featured .premium-plan-price span { color: rgba(248, 255, 217, .88); }
    .premium-plan-head { display: flex; justify-content: space-between; gap: .75rem; align-items: flex-start; }
    .premium-plan-card h3 { margin: .45rem 0 0; font-size: 2rem; line-height: .92; }
    .premium-plan-badge { display: inline-flex; align-items: center; justify-content: center; min-height: 32px; padding: .34rem .7rem; border-radius: 999px; background: rgba(184, 255, 31, .16); border: 1px solid rgba(184, 255, 31, .22); color: #f6ffd5; font-size: .72rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .premium-plan-badge.is-soft { background: rgba(255, 255, 255, .06); border-color: rgba(255, 255, 255, .08); color: rgba(243, 241, 233, .82); }
    .premium-plan-summary { margin: .8rem 0 0; color: var(--premium-muted); line-height: 1.7; }
    .premium-plan-price { margin-top: 1rem; }
    .premium-plan-price strong { display: block; font-family: var(--premium-heading); font-size: 3.1rem; line-height: .9; text-transform: uppercase; }
    .premium-plan-price span { display: block; margin-top: .3rem; color: rgba(243, 241, 233, .7); font-size: .82rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .premium-plan-offer { margin-top: .9rem; padding: .82rem .9rem; border: 1px solid rgba(255, 255, 255, .08); border-radius: 1rem; background: rgba(255, 255, 255, .04); }
    .premium-plan-offer span,
    .premium-plan-offer strong { display: block; }
    .premium-plan-offer span { color: rgba(243, 241, 233, .66); font-size: .76rem; letter-spacing: .08em; text-transform: uppercase; }
    .premium-plan-offer strong { margin-top: .28rem; font-size: .95rem; line-height: 1.5; }
    .premium-plan-features { margin: 1rem 0 0; padding: 0; list-style: none; display: grid; gap: .62rem; }
    .premium-plan-features li { position: relative; padding-left: 1.2rem; color: rgba(243, 241, 233, .88); line-height: 1.55; font-size: .92rem; }
    .premium-plan-features li::before { content: ""; position: absolute; left: 0; top: .6rem; width: .46rem; height: .46rem; border-radius: 999px; background: var(--premium-accent); box-shadow: 0 0 0 4px rgba(184, 255, 31, .07); }
    .premium-plan-actions { margin-top: auto; padding-top: 1.1rem; display: flex; flex-wrap: wrap; gap: .7rem; }

    .premium-empty-state { padding: 1.5rem; border: 1px solid rgba(184, 255, 31, .14); border-radius: 1.4rem; background: rgba(20, 20, 20, .92); }
    .premium-empty-state h3 { margin: 0; font-size: 2rem; line-height: .95; }
    .premium-empty-state p { margin: .8rem 0 0; color: var(--premium-muted); line-height: 1.7; }
    .premium-empty-state .btn { margin-top: 1rem; }

    .premium-review-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
    .premium-review-card { border: 1px solid rgba(255, 255, 255, .07); border-radius: 1.25rem; background: linear-gradient(180deg, rgba(28, 28, 28, .95), rgba(16, 16, 16, .98)); padding: 1.15rem; }
    .premium-review-rating { display: inline-block; color: #f8c95b; letter-spacing: .18em; font-size: .88rem; }
    .premium-review-card strong { display: block; margin-top: .7rem; font-family: var(--premium-body); font-size: 1.04rem; }
    .premium-review-card p { margin: .5rem 0 0; color: var(--premium-muted); line-height: 1.68; }

    .premium-faq-list { display: grid; gap: .75rem; }
    .premium-faq-item { border: 1px solid rgba(255, 255, 255, .07); border-radius: 1.2rem; background: rgba(21, 21, 21, .9); overflow: hidden; }
    .premium-faq-button { width: 100%; border: 0; background: transparent; color: var(--premium-text); padding: 1rem 1.05rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; text-align: left; font-size: .98rem; font-weight: 700; cursor: pointer; }
    .premium-faq-plus { position: relative; width: 1.1rem; height: 1.1rem; flex: 0 0 1.1rem; }
    .premium-faq-plus::before,
    .premium-faq-plus::after { content: ""; position: absolute; left: 50%; top: 50%; width: 1rem; height: 2px; background: var(--premium-accent); transform: translate(-50%, -50%); transition: transform .24s ease, opacity .24s ease; }
    .premium-faq-plus::after { transform: translate(-50%, -50%) rotate(90deg); }
    .premium-faq-content { max-height: 0; overflow: hidden; padding: 0 1.05rem; color: var(--premium-muted); line-height: 1.7; transition: max-height .28s ease, padding .28s ease; }
    .premium-faq-item.is-open .premium-faq-content { max-height: 240px; padding: 0 1.05rem 1rem; }
    .premium-faq-item.is-open .premium-faq-plus::after { opacity: 0; transform: translate(-50%, -50%) rotate(90deg) scaleX(.4); }

    .premium-close-panel {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(320px, .9fr);
        gap: 1.1rem;
        border: 1px solid rgba(184, 255, 31, .16);
        border-radius: 1.85rem;
        background: radial-gradient(circle at 92% 10%, rgba(184, 255, 31, .14), transparent 24%), linear-gradient(180deg, rgba(18, 18, 18, .98), rgba(11, 11, 11, .98));
        padding: clamp(1.4rem, 4vw, 2.2rem);
        box-shadow: var(--premium-shadow);
    }

    .premium-close-copy h2 { margin: .9rem 0 0; max-width: 12ch; font-size: clamp(2.5rem, 4.8vw, 4.6rem); line-height: .9; }
    .premium-close-copy p,
    .premium-close-card p { margin: .95rem 0 0; color: var(--premium-muted); line-height: 1.8; }
    .premium-close-list { margin: 1rem 0 0; padding: 0; list-style: none; display: grid; gap: .56rem; }
    .premium-close-list li { position: relative; padding-left: 1.15rem; color: rgba(243, 241, 233, .9); line-height: 1.6; }
    .premium-close-list li::before { content: ""; position: absolute; left: 0; top: .62rem; width: .46rem; height: .46rem; border-radius: 999px; background: var(--premium-accent); box-shadow: 0 0 0 4px rgba(184, 255, 31, .07); }
    .premium-close-card { border: 1px solid rgba(255, 255, 255, .08); border-radius: 1.4rem; background: rgba(255, 255, 255, .03); padding: 1.2rem; }
    .premium-close-card h3 { margin: .9rem 0 0; font-size: 2rem; line-height: .94; }
    .premium-close-actions { margin-top: 1.15rem; display: flex; flex-wrap: wrap; gap: .7rem; }

    .footer { margin-top: 3rem; padding-bottom: 1.2rem; position: relative; z-index: 2; }
    .footer-panel { border: 1px solid rgba(184, 255, 31, .16); border-radius: 1.45rem; padding: 2rem 1.6rem 1.1rem; background: radial-gradient(circle at 92% 16%, rgba(184, 255, 31, .14), transparent 24%), linear-gradient(180deg, rgba(18, 18, 18, .98), rgba(10, 10, 10, .98)); box-shadow: 0 22px 46px rgba(0, 0, 0, .26); }
    .footer-grid { display: grid; grid-template-columns: 1.3fr .9fr .9fr .9fr; gap: 1.15rem; }
    .footer-brand { align-items: flex-start; }
    .footer-brand-logo { width: min(210px, 100%); height: auto; transform: none; margin: 0 0 1rem; filter: drop-shadow(0 0 12px rgba(184, 255, 31, .14)); }
    .footer-neon-title { color: var(--premium-accent); font-family: var(--premium-heading); font-size: 2rem; font-weight: 800; letter-spacing: .04em; line-height: .9; text-transform: uppercase; text-shadow: 0 0 10px rgba(184, 255, 31, .34); animation: none; }
    .footer h4 { margin: 0; color: var(--premium-text); font-family: var(--premium-heading); font-size: 1.3rem; line-height: .92; text-transform: uppercase; }
    .footer-lead,
    .footer p,
    .footer li,
    .footer a { color: var(--premium-muted); line-height: 1.7; text-decoration: none; text-align: left; }
    .footer ul { margin: .8rem 0 0; padding: 0; list-style: none; display: grid; gap: .26rem; }
    .footer a:hover { color: var(--premium-text); }
    .copy { margin-top: 1.15rem; padding-top: .85rem; border-top: 1px solid rgba(255, 255, 255, .08); color: rgba(243, 241, 233, .58); text-align: center; font-size: .84rem; }

    .reveal { opacity: 0; transform: translateY(22px); transition: opacity .55s ease, transform .55s ease; }
    .reveal.is-visible { opacity: 1; transform: translateY(0); }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
        .reveal { opacity: 1 !important; transform: none !important; }
    }

    @media (max-width: 1180px) {
        .premium-hero-layout { grid-template-columns: 1fr; }
        .premium-hero-visual { min-height: 0; justify-items: center; }
        .premium-program-grid,
        .premium-team-grid,
        .premium-plan-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .premium-close-panel { grid-template-columns: 1fr; }
    }

    @media (max-width: 1080px) {
        .top-nav { gap: .7rem; padding: .68rem .82rem; }
        .brand { width: 146px; min-width: 146px; }
        .menu-links a { padding: .48rem .72rem; font-size: .74rem; }
        .nav-actions .btn { min-height: 44px; padding: .7rem .96rem; font-size: .8rem; }
        .premium-feature-grid,
        .premium-community-stats,
        .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .premium-hero-points { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .premium-screen-main { grid-template-columns: minmax(240px, .62fr) minmax(320px, 1.38fr); }
        .premium-screen-nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .premium-screen-panel { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .premium-program-copy { max-width: 70%; }
    }

    @media (max-width: 820px) {
        .shell { width: min(1240px, calc(100% - 1rem)); }
        .menu-links,
        .nav-actions { display: none; }
        .mobile-menu-toggle { display: inline-flex; }
        .premium-hero-title { max-width: 10ch; font-size: clamp(3.1rem, 12vw, 5rem); }
        .premium-hero-visual { min-height: 0; }
        .premium-feature-grid,
        .premium-program-grid,
        .premium-team-grid,
        .premium-plan-grid,
        .premium-review-grid,
        .premium-community-stats,
        .footer-grid { grid-template-columns: 1fr; }
        .premium-hero-points { grid-template-columns: 1fr; }
        .premium-screen-main,
        .premium-team-card { grid-template-columns: 1fr; }
        .premium-screen-copy,
        .premium-screen-media { min-height: 320px; }
        .premium-screen-nav,
        .premium-float-grid { grid-template-columns: 1fr; }
        .premium-screen-panel { grid-template-columns: 1fr; }
        .premium-program-card { min-height: 260px; }
        .premium-program-copy { max-width: none; }
        .premium-team-photo { min-height: 260px; }
        .premium-team-photo img,
        .premium-team-card:nth-child(2) .premium-team-photo img,
        .premium-team-card:nth-child(3) .premium-team-photo img,
        .premium-team-card:nth-child(4) .premium-team-photo img { object-position: center 30%; }
        .premium-float-card { margin-top: 0; }
        .premium-community-copy { text-align: left; }
        .premium-community-copy h2,
        .premium-community-copy p { margin-left: 0; margin-right: 0; }
        .premium-community-actions { justify-content: flex-start; }
    }

    @media (max-width: 640px) {
        .top-nav { justify-content: space-between; padding: .6rem .7rem; }
        .brand { width: 120px; min-width: 120px; }
        .premium-hero-section::before { left: .3rem; top: 1.4rem; font-size: 5.7rem; }
        .premium-hero-title { font-size: clamp(2.8rem, 16vw, 4.2rem); }
        .premium-hero-actions .btn,
        .premium-hero-actions .inline-form,
        .premium-community-actions .btn,
        .premium-community-actions .inline-form,
        .premium-plan-actions .btn,
        .premium-plan-actions .inline-form,
        .premium-close-actions .btn,
        .premium-close-actions .inline-form { width: 100%; }
        .premium-screen-toolbar { padding: .8rem .9rem; }
        .premium-screen-canvas { padding: .9rem; }
        .premium-screen-copy { min-height: 0; padding: 1.25rem 1rem; }
        .premium-screen-copy strong { max-width: none; font-size: clamp(2.15rem, 11vw, 3.4rem); }
        .premium-screen-media { min-height: 260px; }
        .premium-screen-panel article { padding: .82rem .9rem; }
        .premium-program-card { min-height: 230px; }
        .premium-team-photo { min-height: 230px; }
        .premium-hero-visual { display: block; min-height: 0; }
        .premium-community-panel { min-height: 0; }
        .premium-community-copy { padding: 1.4rem 1rem; }
        .premium-close-panel { padding: 1.1rem; }
        .footer-panel { padding: 1.4rem 1rem 1rem; }
    }
</style>
