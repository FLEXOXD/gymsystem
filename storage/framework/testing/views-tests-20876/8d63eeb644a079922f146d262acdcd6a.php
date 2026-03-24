        .contact-simple-shell {
            position: relative;
            margin-top: 1.35rem;
            overflow: hidden;
            border-radius: 1.8rem;
            border: 1px solid rgba(255, 255, 255, .1);
            background: #050608;
            box-shadow: 0 34px 90px rgba(0, 0, 0, .35);
            isolation: isolate;
        }
        .contact-simple-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(90deg, rgba(0, 0, 0, .28), rgba(0, 0, 0, .18)),
                var(--contact-bg-image);
            background-size: cover;
            background-position: center;
            transform: scale(1.04);
        }
        .contact-simple-shell::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(5, 6, 8, .95) 0%, rgba(5, 6, 8, .86) 42%, rgba(5, 6, 8, .9) 100%),
                radial-gradient(circle at top right, rgba(241, 188, 147, .18), transparent 28%);
            pointer-events: none;
        }
        .contact-simple-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(340px, .95fr);
            gap: 1.1rem;
            padding: clamp(1.2rem, 2.3vw, 1.75rem);
        }
        .contact-simple-copy {
            display: grid;
            align-content: start;
            gap: 1rem;
            padding: clamp(.2rem, 1vw, .65rem);
        }
        .contact-simple-kicker {
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            width: fit-content;
            padding: .5rem .85rem;
            border-radius: 999px;
            background: rgba(13, 13, 15, .78);
            border: 1px solid rgba(241, 188, 147, .2);
            color: #f2c5a2;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .contact-simple-kicker::before {
            content: "";
            width: .55rem;
            height: .55rem;
            border-radius: 999px;
            background: #f1bc93;
            box-shadow: 0 0 16px rgba(241, 188, 147, .5);
        }
        .contact-simple-title {
            margin: 0;
            max-width: 10ch;
            color: #ffffff;
            font-size: clamp(2.3rem, 4.8vw, 4.3rem);
            line-height: .98;
            letter-spacing: -.05em;
        }
        .contact-simple-text {
            margin: 0;
            max-width: 58ch;
            color: #d1d7dc;
            font-size: 1rem;
            line-height: 1.75;
        }
        .contact-simple-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .8rem;
        }
        .contact-action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            padding: .9rem 1.35rem;
            border-radius: 999px;
            border: 1px solid rgba(241, 188, 147, .24);
            background: #f1bc93;
            color: #1b140f;
            font-size: .94rem;
            font-weight: 800;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
            box-shadow: 0 20px 35px rgba(0, 0, 0, .22);
        }
        .contact-action-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 24px 40px rgba(0, 0, 0, .26);
        }
        .contact-action-button--secondary {
            background: rgba(8, 8, 10, .62);
            color: #ffffff;
        }
        .contact-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }
        .contact-detail-card {
            display: grid;
            gap: .6rem;
            padding: 1rem;
            border-radius: 1.25rem;
            border: 1px solid rgba(255, 255, 255, .08);
            background: rgba(8, 8, 10, .66);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
            backdrop-filter: blur(12px);
        }
        .contact-detail-icon {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            color: #f3c49d;
            background: rgba(243, 196, 157, .12);
            border: 1px solid rgba(243, 196, 157, .16);
        }
        .contact-detail-icon svg {
            width: 1.35rem;
            height: 1.35rem;
        }
        .contact-detail-label {
            color: #f0bd95;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .contact-detail-value {
            color: #ffffff;
            font-size: 1.08rem;
            line-height: 1.45;
        }
        .contact-detail-note {
            margin: 0;
            color: #bfc8cf;
            font-size: .9rem;
            line-height: 1.6;
        }
        .contact-form-card {
            display: grid;
            gap: 1rem;
            padding: 1.2rem;
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, .1);
            background: rgba(8, 8, 10, .8);
            backdrop-filter: blur(14px);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
        }
        .contact-form-header small {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .38rem .7rem;
            border-radius: 999px;
            background: rgba(243, 196, 157, .1);
            border: 1px solid rgba(243, 196, 157, .16);
            color: #f0bd95;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .contact-form-header h2 {
            margin: .75rem 0 0;
            color: #ffffff;
            font-size: clamp(1.7rem, 3vw, 2.3rem);
            line-height: 1.08;
            letter-spacing: -.03em;
        }
        .contact-form-header p {
            margin: .6rem 0 0;
            color: #c8d0d6;
            font-size: .95rem;
            line-height: 1.7;
        }
        .contact-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }
        .contact-form-field {
            display: grid;
            gap: .48rem;
        }
        .contact-form-field--full,
        .contact-submit {
            grid-column: 1 / -1;
        }
        .contact-label {
            color: #f5f7fa;
            font-size: .88rem;
            font-weight: 700;
        }
        .contact-label em {
            color: #f0bd95;
            font-style: normal;
        }
        .contact-input {
            width: 100%;
            min-height: 52px;
            padding: .95rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, .1);
            background: rgba(255, 255, 255, .96);
            color: #17181a;
            font: inherit;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        textarea.contact-input {
            min-height: 154px;
            resize: vertical;
        }
        .contact-input:focus {
            outline: none;
            border-color: rgba(241, 188, 147, .85);
            box-shadow: 0 0 0 4px rgba(241, 188, 147, .15);
        }
        .contact-input.is-invalid {
            border-color: rgba(239, 68, 68, .75);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, .12);
        }
        .contact-helper,
        .contact-feedback,
        .contact-submit-note {
            margin: 0;
            font-size: .84rem;
            line-height: 1.6;
        }
        .contact-helper,
        .contact-submit-note {
            color: #bfc7ce;
        }
        .contact-feedback {
            color: #fecaca;
        }
        .contact-submit {
            display: grid;
            gap: .6rem;
        }
        .contact-submit-button {
            width: 100%;
            min-height: 54px;
            border: none;
            border-radius: 999px;
            background: #f1bc93;
            color: #19130e;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
            box-shadow: 0 18px 34px rgba(0, 0, 0, .22);
        }
        .contact-submit-button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 22px 38px rgba(0, 0, 0, .26);
        }
        .contact-submit-button:disabled {
            opacity: .8;
            cursor: wait;
        }
        .contact-status-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1300;
            background: rgba(0, 0, 0, .7);
            opacity: 0;
            pointer-events: none;
            transition: opacity .25s ease;
        }
        .contact-status-backdrop.is-open {
            opacity: 1;
            pointer-events: auto;
        }
        .contact-status-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            z-index: 1310;
            width: min(92vw, 520px);
            transform: translate(-50%, -46%) scale(.96);
            opacity: 0;
            pointer-events: none;
            transition: transform .25s ease, opacity .25s ease;
        }
        .contact-status-modal.is-open {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
            pointer-events: auto;
        }
        .contact-status-inner {
            display: grid;
            gap: 1rem;
            padding: 1.3rem;
            border-radius: 1.45rem;
            border: 1px solid rgba(255, 255, 255, .1);
            background: #0a0b0d;
            box-shadow: 0 30px 80px rgba(0, 0, 0, .38);
        }
        .contact-status-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: .38rem .68rem;
            border-radius: 999px;
            background: rgba(47, 184, 115, .14);
            color: #99efbb;
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .contact-status-modal.is-error .contact-status-badge {
            background: rgba(239, 68, 68, .14);
            color: #fecaca;
        }
        .contact-status-icon {
            width: 3.25rem;
            height: 3.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            color: #9ef0bc;
            background: rgba(47, 184, 115, .12);
            border: 1px solid rgba(47, 184, 115, .16);
        }
        .contact-status-modal.is-error .contact-status-icon {
            color: #fecaca;
            background: rgba(239, 68, 68, .12);
            border-color: rgba(239, 68, 68, .16);
        }
        .contact-status-icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        .contact-status-inner h4 {
            margin: 0;
            color: #ffffff;
            font-size: 1.25rem;
        }
        .contact-status-inner p {
            margin: .45rem 0 0;
            color: #c9d0d5;
            line-height: 1.65;
        }
        .contact-status-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .7rem;
        }
        .contact-status-link,
        .contact-status-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: .8rem 1rem;
            border-radius: 999px;
            font: inherit;
            font-weight: 800;
            text-decoration: none;
        }
        .contact-status-link {
            background: #f1bc93;
            color: #1b140f;
            border: none;
        }
        .contact-status-close {
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            color: #ffffff;
            cursor: pointer;
        }
        @media (max-width: 1080px) {
            .contact-simple-grid {
                grid-template-columns: 1fr;
            }
            .contact-simple-title {
                max-width: 12ch;
            }
        }
        @media (max-width: 720px) {
            .contact-detail-grid,
            .contact-form-grid {
                grid-template-columns: 1fr;
            }
            .contact-simple-actions {
                flex-direction: column;
            }
            .contact-action-button,
            .contact-status-link,
            .contact-status-close {
                width: 100%;
            }
            .contact-simple-shell {
                border-radius: 1.35rem;
            }
            .contact-form-card {
                padding: 1rem;
            }
            .contact-status-inner {
                padding: 1rem;
            }
        }
<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/partials/contact-premium-styles.blade.php ENDPATH**/ ?>