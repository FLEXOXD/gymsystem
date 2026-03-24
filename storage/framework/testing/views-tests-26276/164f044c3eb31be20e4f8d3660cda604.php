        .about-section.about-premium {
            border-top: 0;
            margin-top: 1.4rem;
            padding-top: .45rem;
        }
        .about-premium-kicker {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .42rem .78rem;
            border-radius: 999px;
            border: 1px solid rgba(110, 255, 163, .24);
            background: rgba(7, 17, 12, .68);
            color: #a9ffbf;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
        }
        .about-premium-kicker::before {
            content: "";
            width: .5rem;
            height: .5rem;
            border-radius: 999px;
            background: linear-gradient(145deg, #4dff7a, #ffc167);
            box-shadow: 0 0 16px rgba(77, 255, 122, .52);
        }
        .about-premium-hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, .92fr);
            gap: 1rem;
            padding: clamp(1.2rem, 2.5vw, 2rem);
            border: 1px solid rgba(63, 102, 79, .72);
            border-radius: 1.55rem;
            background: linear-gradient(145deg, rgba(6, 15, 11, .96), rgba(10, 24, 16, .9) 46%, rgba(10, 18, 14, .94));
            overflow: hidden;
            isolation: isolate;
            box-shadow: 0 26px 54px rgba(2, 12, 8, .42);
        }
        .about-premium-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(112deg, rgba(3, 13, 9, .96) 0%, rgba(3, 13, 9, .72) 44%, rgba(3, 13, 9, .9) 100%),
                var(--about-premium-hero-image, linear-gradient(145deg, #08160f, #0d2218));
            background-size: cover;
            background-position: center;
            opacity: .56;
            z-index: -2;
        }
        .about-premium-hero::after {
            content: "";
            position: absolute;
            inset: auto -10% -24% 40%;
            height: 68%;
            background:
                radial-gradient(circle at 18% 44%, rgba(87, 255, 126, .26), transparent 36%),
                radial-gradient(circle at 72% 34%, rgba(255, 168, 74, .2), transparent 34%);
            filter: blur(12px);
            z-index: -1;
            pointer-events: none;
        }
        .about-premium-hero-copy,
        .about-premium-hero-panel {
            position: relative;
            z-index: 1;
        }
        .about-premium-hero-copy {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }
        .about-premium-hero-title {
            margin: .8rem 0 0;
            max-width: 11ch;
            font-size: clamp(2.55rem, 5.2vw, 4.9rem);
            line-height: .94;
            letter-spacing: -.05em;
            color: #f5fff8;
            text-wrap: balance;
        }
        .about-premium-hero-lead {
            margin: 1rem 0 0;
            max-width: 62ch;
            color: #c5d8cb;
            font-size: 1.04rem;
            line-height: 1.72;
        }
        .about-premium-badge-row {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: .62rem;
        }
        .about-premium-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .58rem .8rem;
            border: 1px solid rgba(82, 128, 100, .72);
            border-radius: 999px;
            background: rgba(7, 16, 11, .72);
            color: #e5f7eb;
            font-size: .84rem;
            font-weight: 700;
            backdrop-filter: blur(12px);
        }
        .about-premium-badge::before {
            content: "";
            width: .44rem;
            height: .44rem;
            border-radius: 999px;
            flex: 0 0 .44rem;
            background: linear-gradient(145deg, #47ff6f, #ffb058);
            box-shadow: 0 0 12px rgba(71, 255, 111, .55);
        }
        .about-premium-actions {
            margin-top: 1.35rem;
            display: flex;
            flex-wrap: wrap;
            gap: .7rem;
        }
        .about-premium-actions .btn,
        .about-premium-cta-actions .btn {
            min-height: 48px;
            padding-inline: 1.08rem;
            font-size: .9rem;
        }
        .about-premium-hero-panel {
            display: grid;
            gap: .82rem;
            align-self: stretch;
        }
        .about-premium-panel-card,
        .about-premium-story-card,
        .about-premium-story-visual,
        .about-premium-mission,
        .about-premium-feature-card,
        .about-premium-outcome-card,
        .about-premium-founder,
        .about-premium-trust-card,
        .about-premium-cta {
            border: 1px solid rgba(54, 92, 71, .78);
            border-radius: 1.3rem;
            background: linear-gradient(145deg, rgba(8, 18, 13, .94), rgba(10, 24, 17, .88));
            box-shadow: 0 20px 42px rgba(2, 12, 8, .3);
        }
        .about-premium-panel-card {
            padding: 1rem;
            backdrop-filter: blur(14px);
        }
        .about-premium-panel-card--primary {
            padding: 1.15rem;
            background:
                linear-gradient(145deg, rgba(9, 18, 14, .94), rgba(10, 30, 19, .9)),
                radial-gradient(circle at top right, rgba(255, 165, 82, .12), transparent 42%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .05), 0 22px 42px rgba(2, 12, 8, .28);
        }
        .about-premium-panel-label {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #9dffb7;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .about-premium-panel-label::before {
            content: "";
            width: 1.6rem;
            height: 1px;
            background: rgba(106, 255, 151, .8);
        }
        .about-premium-panel-title {
            display: block;
            margin-top: .55rem;
            color: #f5fff8;
            font-size: clamp(1.35rem, 2.4vw, 1.85rem);
            line-height: 1.14;
            letter-spacing: -.03em;
        }
        .about-premium-panel-copy {
            margin: .75rem 0 0;
            color: #bfd4c7;
            line-height: 1.66;
        }
        .about-premium-panel-pill-row {
            margin-top: .92rem;
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .about-premium-panel-pill {
            padding: .42rem .68rem;
            border-radius: 999px;
            border: 1px solid rgba(86, 128, 103, .8);
            background: rgba(9, 17, 12, .7);
            color: #dff8e6;
            font-size: .78rem;
            font-weight: 700;
        }
        .about-premium-panel-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .82rem;
        }
        .about-premium-mini-card,
        .about-premium-feature-card,
        .about-premium-outcome-card,
        .about-premium-trust-card {
            transition: transform .24s ease, border-color .24s ease, box-shadow .24s ease, background .24s ease;
        }
        .about-premium-mini-card {
            min-height: 100%;
            padding: .95rem;
            border: 1px solid rgba(56, 94, 73, .76);
            border-radius: 1.05rem;
            background: linear-gradient(145deg, rgba(8, 16, 12, .88), rgba(12, 23, 17, .82));
        }
        .about-premium-mini-card:hover,
        .about-premium-feature-card:hover,
        .about-premium-outcome-card:hover,
        .about-premium-trust-card:hover {
            transform: translateY(-4px);
            border-color: rgba(113, 255, 157, .72);
            box-shadow: 0 24px 36px rgba(2, 12, 8, .34);
        }
        .about-premium-icon-slot {
            width: 2.7rem;
            height: 2.7rem;
            border-radius: .9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #f5fff8;
            background:
                linear-gradient(145deg, rgba(77, 255, 122, .26), rgba(255, 176, 88, .16)),
                rgba(9, 18, 13, .92);
            border: 1px solid rgba(103, 150, 121, .52);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04), 0 14px 26px rgba(2, 12, 8, .18);
        }
        .about-premium-icon-slot svg {
            width: 1.2rem;
            height: 1.2rem;
            stroke: currentColor;
        }
        .about-premium-mini-card strong,
        .about-premium-feature-card h3,
        .about-premium-outcome-card h3,
        .about-premium-trust-card h3,
        .about-premium-founder-side h4 {
            display: block;
            margin-top: .78rem;
            color: #f1fff6;
            font-size: 1.04rem;
            line-height: 1.22;
            letter-spacing: -.02em;
        }
        .about-premium-mini-card p,
        .about-premium-feature-card p,
        .about-premium-outcome-card p,
        .about-premium-trust-card p,
        .about-premium-copy,
        .about-premium-founder-role {
            margin: .48rem 0 0;
            color: #bdd1c4;
            line-height: 1.64;
        }
        .about-premium-story-grid {
            margin-top: 1.05rem;
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(0, .96fr);
            gap: 1rem;
            align-items: stretch;
        }
        .about-premium-story-card,
        .about-premium-story-visual,
        .about-premium-mission,
        .about-premium-founder,
        .about-premium-cta {
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .about-premium-story-card::before,
        .about-premium-mission::before,
        .about-premium-founder::before,
        .about-premium-cta::before {
            content: "";
            position: absolute;
            inset: auto auto 0 -8%;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(72, 255, 108, .12), transparent 68%);
            pointer-events: none;
        }
        .about-premium-section-title {
            margin: .72rem 0 0;
            color: #f5fff8;
            font-size: clamp(1.7rem, 3vw, 2.55rem);
            line-height: 1.06;
            letter-spacing: -.035em;
            text-wrap: balance;
        }
        .about-premium-copy + .about-premium-copy {
            margin-top: .7rem;
        }
        .about-premium-story-steps {
            margin-top: 1.05rem;
            display: grid;
            gap: .78rem;
        }
        .about-premium-story-step {
            display: grid;
            grid-template-columns: 70px minmax(0, 1fr);
            gap: .82rem;
            padding: .88rem;
            border-radius: 1rem;
            border: 1px solid rgba(58, 96, 75, .72);
            background: linear-gradient(145deg, rgba(7, 16, 12, .84), rgba(11, 22, 17, .74));
        }
        .about-premium-story-step-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 58px;
            border-radius: .9rem;
            background: linear-gradient(145deg, rgba(77, 255, 122, .3), rgba(255, 183, 95, .22));
            color: #f8fff9;
            font-size: 1.08rem;
            font-weight: 900;
            letter-spacing: .08em;
        }
        .about-premium-story-step strong {
            color: #effff5;
            font-size: 1rem;
            line-height: 1.24;
        }
        .about-premium-story-step p {
            margin: .36rem 0 0;
            color: #bbd0c2;
            line-height: 1.6;
        }
        .about-premium-story-visual {
            min-height: 100%;
            display: flex;
            align-items: stretch;
            background: linear-gradient(145deg, rgba(8, 18, 13, .96), rgba(11, 25, 18, .88));
        }
        .about-premium-story-visual img {
            width: 100%;
            height: 100%;
            min-height: 520px;
            object-fit: cover;
            display: block;
            filter: saturate(1.05) contrast(1.02);
        }
        .about-premium-story-placeholder {
            width: 100%;
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.4rem;
            color: #e5f7ec;
            font-weight: 700;
            background: radial-gradient(circle at top, rgba(75, 255, 126, .14), transparent 46%), linear-gradient(145deg, #09150f, #0d2317);
        }
        .about-premium-story-overlay {
            position: absolute;
            inset: auto 1rem 1rem 1rem;
            padding: 1rem;
            border-radius: 1.15rem;
            border: 1px solid rgba(92, 140, 112, .7);
            background: linear-gradient(145deg, rgba(5, 13, 10, .9), rgba(10, 21, 15, .86));
            box-shadow: 0 18px 34px rgba(2, 12, 8, .38);
            backdrop-filter: blur(14px);
        }
        .about-premium-story-overlay-label,
        .about-premium-founder-side-label {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #9effba;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .about-premium-story-overlay-label::before,
        .about-premium-founder-side-label::before {
            content: "";
            width: 1.25rem;
            height: 1px;
            background: rgba(104, 255, 151, .75);
        }
        .about-premium-story-pains,
        .about-premium-soft-list {
            margin: .78rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: .62rem;
        }
        .about-premium-story-pains li,
        .about-premium-soft-list li {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            color: #e3f7ea;
            line-height: 1.52;
        }
        .about-premium-story-pains li::before,
        .about-premium-soft-list li::before,
        .about-premium-mission-point::before {
            content: "";
            width: .5rem;
            height: .5rem;
            margin-top: .36rem;
            border-radius: 999px;
            flex: 0 0 .5rem;
            background: linear-gradient(145deg, #46ff6f, #ffc169);
            box-shadow: 0 0 12px rgba(71, 255, 111, .4);
        }
        .about-premium-mission {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(0, .96fr);
            gap: 1rem;
        }
        .about-premium-mission::after {
            content: "";
            position: absolute;
            inset: -20% auto auto 58%;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(255, 172, 82, .12), transparent 68%);
            pointer-events: none;
        }
        .about-premium-mission-points {
            display: grid;
            gap: .75rem;
            align-content: start;
        }
        .about-premium-mission-point {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            padding: .92rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(60, 96, 76, .72);
            background: linear-gradient(145deg, rgba(7, 15, 12, .88), rgba(11, 23, 17, .8));
        }
        .about-premium-mission-point strong {
            color: #f1fff6;
            font-size: .98rem;
            line-height: 1.28;
        }
        .about-premium-mission-point span {
            display: block;
            margin-top: .3rem;
            color: #b8cebf;
            line-height: 1.56;
        }
        .about-premium-heading {
            margin-top: 1.2rem;
            margin-bottom: 1rem;
        }
        .about-premium-feature-grid,
        .about-premium-outcome-grid,
        .about-premium-trust-grid {
            display: grid;
            gap: .86rem;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .about-premium-feature-card,
        .about-premium-outcome-card,
        .about-premium-trust-card {
            min-height: 100%;
            padding: 1.02rem;
            position: relative;
            overflow: hidden;
        }
        .about-premium-feature-card::before,
        .about-premium-outcome-card::before,
        .about-premium-trust-card::before {
            content: "";
            position: absolute;
            inset: auto -20px -20px auto;
            width: 90px;
            height: 90px;
            background: radial-gradient(circle, rgba(255, 169, 79, .12), transparent 68%);
            pointer-events: none;
        }
        .about-premium-card-caption {
            display: inline-flex;
            align-items: center;
            gap: .36rem;
            margin-top: .82rem;
            color: #98ffc0;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .about-premium-card-caption::before {
            content: "";
            width: 18px;
            height: 1px;
            background: rgba(104, 255, 150, .8);
        }
        .about-premium-outcome-pill {
            display: inline-flex;
            align-items: center;
            margin-top: .92rem;
            padding: .42rem .68rem;
            border-radius: 999px;
            border: 1px solid rgba(90, 137, 108, .74);
            background: rgba(8, 16, 12, .72);
            color: #e7faed;
            font-size: .78rem;
            font-weight: 700;
        }
        .about-premium-founder {
            margin-top: 1.12rem;
            display: grid;
            grid-template-columns: 220px minmax(0, 1fr) minmax(260px, .7fr);
            gap: 1rem;
            align-items: center;
        }
        .about-premium-founder-identity {
            display: grid;
            gap: .8rem;
            justify-items: center;
        }
        .about-premium-founder-avatar {
            position: relative;
            width: 180px;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: linear-gradient(145deg, rgba(74, 255, 117, .15), rgba(255, 179, 88, .12)), rgba(8, 16, 12, .82);
            border: 1px solid rgba(93, 142, 112, .76);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04), 0 18px 36px rgba(2, 12, 8, .28);
        }
        .about-premium-founder-avatar::before,
        .about-premium-founder-avatar::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            border: 1px solid rgba(103, 152, 121, .42);
        }
        .about-premium-founder-avatar::before {
            inset: 12px;
        }
        .about-premium-founder-avatar::after {
            inset: -8px;
            opacity: .48;
        }
        .about-premium-founder-avatar-core {
            width: 124px;
            aspect-ratio: 1;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #4fff79, #ffb45e);
            color: #06120b;
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: .08em;
            box-shadow: 0 18px 30px rgba(6, 20, 11, .34);
        }
        .about-premium-founder-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .5rem .78rem;
            border-radius: 999px;
            border: 1px solid rgba(85, 132, 104, .74);
            background: rgba(7, 15, 11, .72);
            color: #dff5e6;
            font-size: .82rem;
            font-weight: 700;
            text-align: center;
        }
        .about-premium-founder-tags {
            margin-top: .95rem;
            display: flex;
            flex-wrap: wrap;
            gap: .55rem;
        }
        .about-premium-founder-tag {
            display: inline-flex;
            align-items: center;
            padding: .48rem .74rem;
            border-radius: 999px;
            border: 1px solid rgba(86, 129, 104, .72);
            background: rgba(8, 16, 12, .72);
            color: #e7f9ed;
            font-size: .8rem;
            font-weight: 700;
        }
        .about-premium-founder-side {
            padding: 1rem;
            border-radius: 1.1rem;
            border: 1px solid rgba(59, 95, 75, .76);
            background: linear-gradient(145deg, rgba(7, 15, 11, .9), rgba(11, 23, 17, .82));
        }
        .about-premium-founder-side h4 {
            margin-top: .82rem;
        }
        .about-premium-cta {
            margin-top: 1.18rem;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            background: linear-gradient(145deg, rgba(9, 18, 13, .96), rgba(12, 30, 18, .92));
        }
        .about-premium-cta::after {
            content: "";
            position: absolute;
            inset: auto -10% -30% auto;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(76, 255, 118, .18), transparent 68%);
            pointer-events: none;
        }
        .about-premium-cta-title {
            margin: .72rem 0 0;
            color: #f6fff9;
            font-size: clamp(1.8rem, 3.1vw, 2.8rem);
            line-height: 1.05;
            letter-spacing: -.035em;
        }
        .about-premium-cta-copy {
            margin: .68rem 0 0;
            max-width: 60ch;
            color: #bfd5c8;
            line-height: 1.64;
        }
        .about-premium-cta-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: .72rem;
        }
        @media (max-width: 1160px) {
            .about-premium-hero,
            .about-premium-mission,
            .about-premium-founder,
            .about-premium-cta {
                grid-template-columns: 1fr;
            }
            .about-premium-panel-grid,
            .about-premium-feature-grid,
            .about-premium-outcome-grid,
            .about-premium-trust-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .about-premium-hero-title {
                max-width: 14ch;
            }
            .about-premium-cta-actions {
                justify-content: flex-start;
            }
        }
        @media (max-width: 920px) {
            .about-premium-story-grid {
                grid-template-columns: 1fr;
            }
            .about-premium-story-visual img,
            .about-premium-story-placeholder {
                min-height: 360px;
            }
        }
        @media (max-width: 720px) {
            .about-premium-hero,
            .about-premium-story-card,
            .about-premium-story-visual,
            .about-premium-mission,
            .about-premium-feature-card,
            .about-premium-outcome-card,
            .about-premium-founder,
            .about-premium-trust-card,
            .about-premium-cta {
                padding: 1rem;
            }
            .about-premium-hero-title {
                max-width: none;
                font-size: clamp(2.1rem, 10vw, 3.15rem);
            }
            .about-premium-hero-lead,
            .about-premium-copy,
            .about-premium-cta-copy,
            .about-premium-mini-card p,
            .about-premium-feature-card p,
            .about-premium-outcome-card p,
            .about-premium-trust-card p {
                font-size: .96rem;
            }
            .about-premium-panel-grid,
            .about-premium-feature-grid,
            .about-premium-outcome-grid,
            .about-premium-trust-grid {
                grid-template-columns: 1fr;
            }
            .about-premium-story-step {
                grid-template-columns: 58px minmax(0, 1fr);
            }
            .about-premium-founder-avatar {
                width: 154px;
            }
            .about-premium-founder-avatar-core {
                width: 108px;
                font-size: 1.72rem;
            }
            .about-premium-actions,
            .about-premium-cta-actions {
                display: grid;
                grid-template-columns: 1fr;
            }
            .about-premium-actions .inline-form,
            .about-premium-actions .btn,
            .about-premium-cta-actions .inline-form,
            .about-premium-cta-actions .btn {
                width: 100%;
            }
            .about-premium-story-overlay {
                inset: auto .8rem .8rem .8rem;
                padding: .9rem;
            }
        }
<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/partials/about-premium-styles.blade.php ENDPATH**/ ?>