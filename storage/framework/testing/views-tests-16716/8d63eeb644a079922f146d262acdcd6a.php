        .contact-shell {
            margin-top: 1.3rem;
            border: 1px solid rgba(112, 239, 175, .2);
            border-radius: 1.6rem;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, .14), transparent 28%),
                radial-gradient(circle at 86% 14%, rgba(45, 212, 191, .12), transparent 26%),
                linear-gradient(180deg, rgba(7, 12, 17, .96) 0%, rgba(4, 8, 12, .98) 100%);
            padding: clamp(1rem, 2vw, 1.45rem);
            box-shadow: 0 32px 80px rgba(0, 0, 0, .28);
        }
        .contact-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(320px, .95fr);
            gap: 1.15rem;
            align-items: stretch;
        }
        .contact-kicker {
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .45rem .78rem;
            width: fit-content;
            border-radius: 999px;
            border: 1px solid rgba(111, 235, 178, .2);
            background: rgba(8, 18, 24, .76);
            color: #97e8be;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .contact-kicker::before {
            content: "";
            width: .5rem;
            height: .5rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #47ff6f 0%, #2dd4bf 100%);
            box-shadow: 0 0 18px rgba(71, 255, 111, .48);
        }
        .contact-hero-title {
            margin: .85rem 0 0;
            max-width: 12ch;
            font-size: clamp(2.35rem, 5vw, 4.45rem);
            line-height: .98;
            letter-spacing: -.05em;
        }
        .contact-hero-subtitle {
            margin: 1rem 0 0;
            max-width: 60ch;
            color: #bfd0dc;
            font-size: 1.02rem;
            line-height: 1.72;
        }
        .contact-highlight-list {
            margin-top: 1.15rem;
            display: flex;
            flex-wrap: wrap;
            gap: .7rem;
        }
        .contact-highlight {
            display: inline-flex;
            align-items: center;
            gap: .58rem;
            min-height: 42px;
            padding: .62rem .88rem;
            border-radius: 999px;
            border: 1px solid rgba(136, 161, 180, .16);
            background: rgba(9, 16, 22, .82);
            color: #edf7ff;
            font-size: .88rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .contact-highlight-dot {
            width: .55rem;
            height: .55rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #47ff6f 0%, #2dd4bf 100%);
            box-shadow: 0 0 16px rgba(71, 255, 111, .38);
            flex: 0 0 .55rem;
        }
        .contact-hero-actions {
            margin-top: 1.1rem;
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
        }
        .contact-hero-actions .btn {
            min-height: 48px;
        }
        .contact-hero-note {
            margin: 1rem 0 0;
            color: #93aab8;
            font-size: .82rem;
            line-height: 1.6;
        }
        .contact-stage-panel {
            position: relative;
            min-height: 100%;
            padding: 1.05rem;
            border-radius: 1.4rem;
            border: 1px solid rgba(115, 236, 175, .14);
            background:
                radial-gradient(circle at top right, rgba(54, 211, 153, .18), transparent 24%),
                linear-gradient(180deg, rgba(8, 16, 22, .92) 0%, rgba(7, 13, 18, .98) 100%);
            overflow: hidden;
            display: grid;
            gap: .9rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .03);
        }
        .contact-stage-panel::after {
            content: "";
            position: absolute;
            right: -70px;
            bottom: -70px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(45, 212, 191, .16), transparent 66%);
            filter: blur(8px);
            pointer-events: none;
        }
        .contact-stage-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .42rem .68rem;
            border-radius: 999px;
            background: rgba(9, 20, 26, .88);
            border: 1px solid rgba(119, 243, 185, .16);
            color: #b7f5d5;
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .contact-stage-card {
            position: relative;
            z-index: 1;
            padding: .95rem 1rem;
            border-radius: 1.05rem;
            border: 1px solid rgba(140, 160, 176, .14);
            background: rgba(8, 15, 21, .78);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .03);
        }
        .contact-stage-card--primary {
            background:
                linear-gradient(135deg, rgba(13, 60, 45, .96), rgba(8, 18, 24, .96)),
                rgba(8, 15, 21, .78);
            border-color: rgba(89, 224, 158, .18);
        }
        .contact-stage-card span {
            display: block;
            color: #8bcfad;
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .contact-stage-card strong {
            display: block;
            margin-top: .45rem;
            color: #f5fbff;
            font-size: 1.08rem;
            line-height: 1.35;
        }
        .contact-stage-card p {
            margin: .52rem 0 0;
            color: #b8cad6;
            font-size: .86rem;
            line-height: 1.55;
        }
        .contact-stage-timeline {
            position: relative;
            z-index: 1;
            display: grid;
            gap: .75rem;
        }
        .contact-stage-step {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: .72rem;
            align-items: start;
            padding: .85rem .9rem;
            border-radius: 1rem;
            border: 1px solid rgba(140, 160, 176, .12);
            background: rgba(7, 13, 19, .76);
        }
        .contact-stage-step em {
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(71, 255, 111, .18), rgba(45, 212, 191, .18));
            border: 1px solid rgba(96, 234, 173, .18);
            color: #c2fbdc;
            font-style: normal;
            font-size: .74rem;
            font-weight: 900;
            letter-spacing: .08em;
        }
        .contact-stage-step strong {
            display: block;
            color: #eef7ff;
            font-size: .92rem;
            line-height: 1.35;
        }
        .contact-stage-step p {
            margin: .2rem 0 0;
            color: #94a9ba;
            font-size: .82rem;
            line-height: 1.55;
        }
        .contact-stage-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }
        .contact-content-grid {
            margin-top: 1.15rem;
            display: grid;
            grid-template-columns: minmax(0, .98fr) minmax(0, 1.02fr);
            gap: 1rem;
        }
        .contact-stack {
            display: grid;
            gap: 1rem;
        }
        .contact-panel,
        .contact-form-card {
            border: 1px solid rgba(118, 140, 160, .14);
            border-radius: 1.35rem;
            background:
                linear-gradient(180deg, rgba(8, 14, 19, .94) 0%, rgba(5, 9, 13, .98) 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .03);
        }
        .contact-panel {
            padding: 1.1rem;
        }
        .contact-panel-header small,
        .contact-form-header small {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #8fe0b8;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .contact-panel-header h2,
        .contact-form-header h2 {
            margin: .65rem 0 0;
            color: #f6fbff;
            font-size: clamp(1.45rem, 2.6vw, 2.05rem);
            line-height: 1.14;
            letter-spacing: -.03em;
        }
        .contact-panel-header p,
        .contact-form-header p {
            margin: .7rem 0 0;
            color: #9eb2c0;
            line-height: 1.65;
            font-size: .95rem;
        }
        .contact-info-grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .8rem;
        }
        .contact-info-card {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            min-height: 100%;
            border: 1px solid rgba(124, 145, 164, .14);
            border-radius: 1.08rem;
            background: rgba(7, 13, 18, .8);
            padding: 1rem;
        }
        .contact-icon {
            width: 2.85rem;
            height: 2.85rem;
            border-radius: .95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 2.85rem;
            border: 1px solid rgba(116, 231, 177, .16);
            color: #dffcef;
            background: linear-gradient(135deg, rgba(34, 197, 94, .18), rgba(45, 212, 191, .14));
            box-shadow: 0 16px 28px rgba(0, 0, 0, .14);
        }
        .contact-icon svg {
            width: 1.24rem;
            height: 1.24rem;
        }
        .contact-icon--mail {
            background: linear-gradient(135deg, rgba(59, 130, 246, .18), rgba(14, 165, 233, .12));
            border-color: rgba(107, 189, 255, .18);
        }
        .contact-icon--map {
            background: linear-gradient(135deg, rgba(71, 255, 111, .18), rgba(16, 185, 129, .12));
        }
        .contact-icon--calendar {
            background: linear-gradient(135deg, rgba(251, 191, 36, .18), rgba(245, 158, 11, .12));
            border-color: rgba(252, 211, 77, .18);
        }
        .contact-info-copy {
            display: grid;
            gap: .34rem;
        }
        .contact-info-eyebrow {
            color: #86dcb2;
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .contact-info-title {
            margin: 0;
            color: #f5fbff;
            font-size: 1rem;
            line-height: 1.35;
        }
        .contact-info-text {
            margin: 0;
            color: #9eb2c0;
            font-size: .88rem;
            line-height: 1.6;
        }
        .contact-info-link {
            margin-top: .15rem;
            width: fit-content;
            color: #dffcef;
            font-size: .84rem;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px solid rgba(143, 224, 184, .32);
        }
        .contact-panel--trust {
            padding: 1.1rem;
        }
        .contact-trust-list {
            margin: 1rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: .8rem;
        }
        .contact-trust-item {
            padding: .95rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(124, 145, 164, .14);
            background: rgba(7, 13, 18, .76);
            color: #ddecf8;
            line-height: 1.6;
        }
        .contact-trust-item strong {
            display: block;
            color: #f4fbff;
            font-size: .95rem;
        }
        .contact-trust-item span {
            display: block;
            margin-top: .25rem;
            color: #9eb2c0;
            font-size: .88rem;
        }
        .contact-trust-chip-row {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: .62rem;
        }
        .contact-trust-chip {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: .55rem .82rem;
            border-radius: 999px;
            border: 1px solid rgba(118, 140, 160, .16);
            background: rgba(8, 14, 19, .84);
            color: #e8f4ff;
            font-size: .82rem;
            font-weight: 700;
        }
        .contact-form-card {
            position: relative;
            overflow: hidden;
            padding: 1.15rem;
        }
        .contact-form-card::before {
            content: "";
            position: absolute;
            top: -90px;
            right: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(45, 212, 191, .16), transparent 68%);
            pointer-events: none;
        }
        .contact-form-header,
        .contact-form-mini-note,
        .contact-form-grid {
            position: relative;
            z-index: 1;
        }
        .contact-form-mini-note {
            margin-top: .95rem;
            padding: .95rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(115, 236, 175, .14);
            background: rgba(8, 16, 22, .84);
            color: #b7cad7;
            font-size: .88rem;
            line-height: 1.6;
        }
        .contact-form-mini-note strong {
            display: block;
            color: #f5fbff;
            margin-bottom: .18rem;
        }
        .contact-form-grid {
            margin-top: 1.05rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }
        .contact-form-field {
            display: grid;
            gap: .44rem;
        }
        .contact-form-field--full {
            grid-column: 1 / -1;
        }
        .contact-label,
        .quote-form-label {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            color: #f4fbff;
            font-size: .84rem;
            font-weight: 700;
            line-height: 1.3;
        }
        .contact-label em,
        .quote-form-label em {
            font-style: normal;
            color: #7ff1be;
        }
        .contact-helper {
            margin: 0;
            color: #8ea5b6;
            font-size: .78rem;
            line-height: 1.55;
        }
        .contact-input {
            width: 100%;
            min-height: 54px;
            border-radius: 1rem;
            border: 1px solid rgba(121, 143, 160, .16);
            background: rgba(8, 15, 20, .88);
            color: #f5fbff;
            padding: .88rem 1rem;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease, background .2s ease;
        }
        .contact-input:hover {
            border-color: rgba(116, 231, 177, .28);
        }
        .contact-input::placeholder {
            color: #778c9e;
        }
        .contact-input:focus {
            border-color: rgba(98, 236, 179, .74);
            box-shadow: 0 0 0 4px rgba(52, 211, 153, .12);
            background: rgba(10, 18, 24, .96);
            transform: translateY(-1px);
        }
        .contact-input.is-invalid {
            border-color: rgba(248, 113, 113, .74);
            box-shadow: 0 0 0 4px rgba(248, 113, 113, .12);
        }
        textarea.contact-input {
            min-height: 164px;
            resize: vertical;
        }
        .contact-feedback,
        .quote-form-error {
            color: #ffb7b7;
            font-size: .78rem;
            font-weight: 600;
            line-height: 1.45;
        }
        .contact-submit {
            grid-column: 1 / -1;
            display: grid;
            gap: .58rem;
            margin-top: .08rem;
        }
        .contact-submit .btn {
            width: 100%;
            justify-content: center;
            min-height: 54px;
        }
        .contact-submit-note {
            margin: 0;
            color: #8fa6b6;
            font-size: .78rem;
            line-height: 1.55;
        }
        .contact-status-backdrop {
            position: fixed;
            inset: 0;
            z-index: 105;
            background: rgba(1, 5, 8, .78);
            backdrop-filter: blur(10px);
            display: none;
        }
        .contact-status-backdrop.is-open {
            display: block;
        }
        .contact-status-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: min(520px, calc(100% - 1.2rem));
            z-index: 106;
            display: none;
            padding: 1.15rem;
            border-radius: 1.5rem;
            border: 1px solid rgba(106, 239, 176, .22);
            background:
                radial-gradient(circle at top right, rgba(45, 212, 191, .14), transparent 24%),
                linear-gradient(180deg, rgba(8, 14, 19, .98) 0%, rgba(4, 8, 12, .99) 100%);
            box-shadow: 0 32px 90px rgba(0, 0, 0, .42);
            color: #f4fbff;
        }
        .contact-status-modal.is-open {
            display: block;
        }
        .contact-status-inner {
            display: grid;
            gap: .95rem;
        }
        .contact-status-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .42rem .7rem;
            border-radius: 999px;
            background: rgba(7, 16, 22, .9);
            border: 1px solid rgba(117, 240, 177, .18);
            color: #9ae7bf;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
        }
        .contact-status-icon {
            width: 3.2rem;
            height: 3.2rem;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(116, 231, 177, .18);
            background: linear-gradient(135deg, rgba(34, 197, 94, .18), rgba(45, 212, 191, .14));
            color: #f2fff7;
        }
        .contact-status-icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        .contact-status-modal h4 {
            margin: 0;
            font-size: 1.36rem;
            line-height: 1.2;
            letter-spacing: -.02em;
        }
        .contact-status-modal p {
            margin: .52rem 0 0;
            color: #b4c8d6;
            line-height: 1.65;
        }
        .contact-status-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: .7rem;
        }
        .contact-status-link,
        .contact-status-close {
            min-height: 46px;
            padding: .7rem 1rem;
            border-radius: .95rem;
            font-size: .88rem;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }
        .contact-status-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(116, 231, 177, .18);
            background: rgba(8, 16, 22, .9);
            color: #f1fff8;
        }
        .contact-status-close {
            border: 1px solid rgba(116, 231, 177, .22);
            background: linear-gradient(135deg, rgba(34, 197, 94, .22), rgba(16, 185, 129, .14));
            color: #f4fff7;
        }
        .contact-status-modal.is-error {
            border-color: rgba(248, 113, 113, .24);
            background:
                radial-gradient(circle at top right, rgba(248, 113, 113, .14), transparent 24%),
                linear-gradient(180deg, rgba(18, 10, 13, .98) 0%, rgba(10, 6, 8, .99) 100%);
        }
        .contact-status-modal.is-error .contact-status-badge {
            border-color: rgba(248, 113, 113, .2);
            color: #ffc6c6;
        }
        .contact-status-modal.is-error .contact-status-icon {
            border-color: rgba(248, 113, 113, .2);
            background: linear-gradient(135deg, rgba(248, 113, 113, .18), rgba(190, 24, 93, .12));
        }
        .contact-status-modal.is-error p {
            color: #e8c0c0;
        }
        .contact-status-modal.is-error .contact-status-close {
            border-color: rgba(248, 113, 113, .22);
            background: linear-gradient(135deg, rgba(248, 113, 113, .18), rgba(190, 24, 93, .12));
            color: #fff0f0;
        }
        body.quote-modal-open {
            overflow: hidden;
        }
        .quote-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 108;
            background: rgba(2, 6, 9, .82);
            backdrop-filter: blur(10px);
            display: none;
        }
        .quote-modal-backdrop.is-open {
            display: block;
        }
        .quote-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: min(1120px, calc(100% - 1.2rem));
            height: min(92vh, 900px);
            max-height: min(92vh, 900px);
            border: 1px solid rgba(113, 240, 178, .18);
            border-radius: 1.75rem;
            background:
                radial-gradient(circle at top right, rgba(45, 212, 191, .08), transparent 22%),
                linear-gradient(180deg, rgba(6, 11, 15, .98) 0%, rgba(3, 6, 10, .99) 100%);
            color: #eff7ff;
            box-shadow: 0 36px 90px rgba(0, 0, 0, .44);
            z-index: 109;
            display: none;
            overflow: hidden;
        }
        .quote-modal.is-open {
            display: block;
        }
        .quote-modal-shell {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
            height: 100%;
            min-height: 0;
        }
        .quote-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 2;
            width: 46px;
            height: 46px;
            border: 1px solid rgba(124, 145, 164, .18);
            border-radius: 999px;
            background: rgba(8, 13, 18, .92);
            color: #e8f3fb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .22);
        }
        .quote-modal-close svg {
            width: 18px;
            height: 18px;
        }
        .quote-modal-side {
            position: relative;
            overflow: hidden;
            padding: 2rem;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, .16), transparent 26%),
                radial-gradient(circle at 88% 14%, rgba(45, 212, 191, .14), transparent 24%),
                linear-gradient(180deg, rgba(7, 14, 19, .96) 0%, rgba(4, 8, 12, .98) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1.2rem;
        }
        .quote-modal-side::after {
            content: "";
            position: absolute;
            right: -80px;
            bottom: -80px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(45, 212, 191, .16), transparent 68%);
            filter: blur(8px);
            pointer-events: none;
        }
        .quote-modal-kicker {
            margin: 0;
            color: #95e6bf;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .quote-modal-title {
            margin: .8rem 0 0;
            max-width: 12ch;
            font-size: clamp(2rem, 4.1vw, 3.5rem);
            line-height: 1;
            letter-spacing: -.05em;
            font-weight: 900;
        }
        .quote-modal-copy {
            margin: 1rem 0 0;
            max-width: 40ch;
            color: #c0d3e0;
            font-size: .98rem;
            line-height: 1.7;
        }
        .quote-modal-visual {
            position: relative;
            display: grid;
            gap: 1rem;
        }
        .quote-visual-frame {
            position: relative;
            z-index: 1;
            padding: 1rem;
            border-radius: 1.35rem;
            border: 1px solid rgba(124, 145, 164, .16);
            background: rgba(8, 14, 19, .72);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .03);
        }
        .quote-visual-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: .55rem;
        }
        .quote-visual-chip {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: .45rem .72rem;
            border-radius: 999px;
            border: 1px solid rgba(124, 145, 164, .16);
            background: rgba(6, 12, 17, .84);
            color: #e6f5ff;
            font-size: .78rem;
            font-weight: 700;
        }
        .quote-visual-dashboard {
            margin-top: .95rem;
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(0, .88fr);
            gap: .75rem;
        }
        .quote-visual-card {
            padding: .95rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(124, 145, 164, .14);
            background: rgba(7, 13, 18, .82);
            display: grid;
            gap: .42rem;
        }
        .quote-visual-card.is-accent {
            background: linear-gradient(135deg, rgba(13, 60, 45, .96), rgba(8, 18, 24, .96));
            border-color: rgba(89, 224, 158, .2);
        }
        .quote-visual-card-label {
            color: #8ddcb4;
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .quote-visual-card-value {
            color: #f5fbff;
            font-size: 1rem;
            line-height: 1.35;
        }
        .quote-visual-card p {
            margin: 0;
            color: #9bb0c0;
            font-size: .83rem;
            line-height: 1.55;
        }
        .quote-visual-list {
            margin: 1rem 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: .64rem;
        }
        .quote-visual-list li {
            display: flex;
            align-items: flex-start;
            gap: .58rem;
            color: #c0d3e0;
            font-size: .84rem;
            line-height: 1.55;
        }
        .quote-visual-list li::before {
            content: "";
            width: .56rem;
            height: .56rem;
            border-radius: 999px;
            margin-top: .44rem;
            flex: 0 0 .56rem;
            background: linear-gradient(135deg, #47ff6f 0%, #2dd4bf 100%);
            box-shadow: 0 0 16px rgba(71, 255, 111, .34);
        }
        .quote-modal-stat-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }
        .quote-modal-stat-card {
            padding: .95rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(124, 145, 164, .14);
            background: rgba(8, 14, 19, .72);
        }
        .quote-modal-stat-card span {
            display: block;
            color: #8ddcb4;
            font-size: .72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
        }
        .quote-modal-stat-card strong {
            display: block;
            margin-top: .45rem;
            color: #f5fbff;
            font-size: .96rem;
            line-height: 1.45;
        }
        .quote-modal-form-panel {
            padding: 2rem 1.45rem 1.45rem 2rem;
            background:
                radial-gradient(circle at top right, rgba(45, 212, 191, .06), transparent 22%),
                linear-gradient(180deg, rgba(8, 12, 16, .96) 0%, rgba(5, 8, 11, .99) 100%);
            color: #eff7ff;
            min-height: 0;
            height: 100%;
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
        }
        .quote-modal-form-panel::-webkit-scrollbar {
            width: 10px;
        }
        .quote-modal-form-panel::-webkit-scrollbar-track {
            background: rgba(10, 15, 20, .7);
            border-radius: 999px;
        }
        .quote-modal-form-panel::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(71, 255, 111, .72), rgba(45, 212, 191, .72));
            border-radius: 999px;
            border: 2px solid rgba(5, 8, 11, .98);
        }
        .quote-modal-form-panel {
            scrollbar-width: thin;
            scrollbar-color: rgba(71, 255, 111, .72) rgba(10, 15, 20, .7);
        }
        .quote-form-header h3 {
            margin: .52rem 0 0;
            color: #f6fbff;
            font-size: clamp(1.6rem, 2.4vw, 2.1rem);
            line-height: 1.14;
            letter-spacing: -.03em;
        }
        .quote-form-header p {
            margin: .72rem 0 0;
            color: #9fb2c0;
            line-height: 1.65;
        }
        .quote-form-hint-grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .7rem;
        }
        .quote-form-hint {
            padding: .82rem .88rem;
            border-radius: 1rem;
            border: 1px solid rgba(124, 145, 164, .14);
            background: rgba(7, 13, 18, .8);
            display: grid;
            gap: .22rem;
        }
        .quote-form-hint strong {
            color: #f6fbff;
            font-size: .84rem;
        }
        .quote-form-hint span {
            color: #8fa5b6;
            font-size: .78rem;
            line-height: 1.5;
        }
        .quote-form-alert {
            margin-top: 1rem;
            padding: .95rem 1rem;
            border-radius: 1.1rem;
            border: 1px solid transparent;
            display: grid;
            gap: .28rem;
        }
        .quote-form-alert strong {
            color: inherit;
            font-size: .92rem;
        }
        .quote-form-alert span {
            color: inherit;
            font-size: .88rem;
            line-height: 1.6;
        }
        .quote-form-alert.is-success {
            background: rgba(34, 197, 94, .12);
            border-color: rgba(89, 224, 158, .18);
            color: #c6f7dd;
        }
        .quote-form-alert.is-error {
            background: rgba(248, 113, 113, .1);
            border-color: rgba(248, 113, 113, .18);
            color: #ffd2d2;
        }
        .quote-plan-pill {
            margin-top: 1rem;
            display: none;
            align-items: center;
            gap: .48rem;
            width: fit-content;
            min-height: 38px;
            padding: .52rem .82rem;
            border-radius: 999px;
            border: 1px solid rgba(89, 224, 158, .18);
            background: rgba(34, 197, 94, .12);
            color: #9ae7bf;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
        }
        .quote-plan-pill.is-visible {
            display: inline-flex;
        }
        .quote-plan-pill strong {
            color: #f6fbff;
        }
        .quote-form-grid {
            margin-top: 1.1rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .88rem;
        }
        .quote-form-field {
            display: grid;
            gap: .44rem;
            color: #eef7ff;
            font-size: .84rem;
            font-weight: 700;
        }
        .quote-form-field--full {
            grid-column: 1 / -1;
        }
        .quote-form-inline {
            display: grid;
            grid-template-columns: 132px minmax(0, 1fr);
            gap: .65rem;
        }
        .quote-form-prefix {
            appearance: none;
        }
        .quote-form-field .contact-input {
            min-height: 54px;
            border-color: rgba(121, 143, 160, .16);
            background: rgba(9, 15, 20, .92);
            color: #f5fbff;
        }
        .quote-form-field .contact-input::placeholder {
            color: #74899a;
        }
        .quote-form-field .contact-input:focus {
            border-color: rgba(98, 236, 179, .74);
            box-shadow: 0 0 0 4px rgba(52, 211, 153, .12);
        }
        .quote-form-field textarea.contact-input {
            min-height: 112px;
        }
        .quote-form-help {
            color: #8ea5b6;
            font-size: .78rem;
            line-height: 1.55;
        }
        .quote-form-checkbox {
            margin-top: .08rem;
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(124, 145, 164, .14);
            background: rgba(7, 13, 18, .8);
            display: grid;
            gap: .5rem;
        }
        .quote-form-checkbox label {
            display: flex;
            align-items: flex-start;
            gap: .72rem;
            color: #eef7ff;
            font-size: .86rem;
            font-weight: 700;
        }
        .quote-form-checkbox input {
            margin-top: .22rem;
            width: 18px;
            height: 18px;
            accent-color: #47ff6f;
        }
        .quote-form-legal {
            margin: 0;
            color: #8ea5b6;
            font-size: .78rem;
            line-height: 1.55;
        }
        .quote-form-submit {
            margin-top: .55rem;
            width: 100%;
            min-height: 54px;
            justify-content: center;
            font-size: .94rem;
        }
        @media (max-width: 1100px) {
            .contact-hero,
            .contact-content-grid {
                grid-template-columns: 1fr;
            }
            .contact-stage-panel {
                min-height: 0;
            }
        }
        @media (max-width: 720px) {
            .contact-shell {
                border-radius: 1.3rem;
            }
            .contact-hero-title {
                font-size: clamp(2.1rem, 9vw, 3rem);
            }
            .contact-highlight-list,
            .contact-hero-actions,
            .contact-trust-chip-row {
                gap: .6rem;
            }
            .contact-status-actions {
                justify-content: stretch;
            }
            .contact-status-link,
            .contact-status-close {
                width: 100%;
                justify-content: center;
            }
            .quote-form-hint-grid,
            .quote-modal-stat-grid,
            .quote-visual-dashboard {
                grid-template-columns: 1fr;
            }
        }
<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/partials/contact-premium-styles.blade.php ENDPATH**/ ?>