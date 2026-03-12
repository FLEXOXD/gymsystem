<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesion expirada</title>
    <style>
        :root {
            color-scheme: dark;
            --bg-0: #07111f;
            --bg-1: #0f172a;
            --line: rgba(56, 189, 248, 0.22);
            --text-0: #f8fafc;
            --text-1: #cbd5e1;
            --warn: #f59e0b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: ui-sans-serif, system-ui, sans-serif;
            background:
                radial-gradient(circle at top, rgba(56, 189, 248, 0.16), transparent 34%),
                linear-gradient(180deg, var(--bg-1), var(--bg-0));
            color: var(--text-0);
        }

        .expired-card {
            width: min(100%, 560px);
            border-radius: 28px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.96), rgba(7, 17, 31, 0.98));
            padding: 24px;
            box-shadow: 0 28px 70px rgba(2, 6, 23, 0.48);
        }

        .expired-badge {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #fde68a;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        h1 {
            margin: 16px 0 8px;
            font-size: clamp(30px, 8vw, 44px);
            line-height: 0.96;
            font-weight: 900;
        }

        p {
            margin: 0;
            color: var(--text-1);
            font-size: 16px;
            line-height: 1.55;
        }

        .expired-panel {
            margin-top: 18px;
            border-radius: 20px;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(15, 23, 42, 0.7);
            padding: 16px;
        }

        .expired-panel strong {
            display: block;
            color: #fef3c7;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .expired-link {
            display: inline-flex;
            margin-top: 18px;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, #22d3ee, #3b82f6);
            color: #082f49;
            font-weight: 900;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main class="expired-card">
        <span class="expired-badge">Sesion expirada</span>
        <h1>Este enlace ya no sirve</h1>
        <p>Vuelve a la computadora, abre otra vez el botón de escaneo y genera un enlace nuevo para seguir usando la cámara del celular.</p>

        <section class="expired-panel">
            <strong>Qué pasó</strong>
            <p>El enlace móvil se renueva el primer día de cada mes o al generar una sesión nueva. Usa siempre el último QR visible en la computadora.</p>
        </section>

        <a class="expired-link" href="javascript:history.back()">Volver</a>
    </main>
</body>
</html>
