<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR de acceso - {{ $client->full_name }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 18px;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
        }
        .card {
            width: min(420px, 100%);
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            background: rgba(15, 23, 42, 0.82);
            padding: 18px;
            box-shadow: 0 18px 45px rgba(2, 6, 23, 0.45);
        }
        .gym {
            margin: 0 0 8px;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #93c5fd;
        }
        .name {
            margin: 0 0 4px;
            font-size: 22px;
            line-height: 1.2;
            font-weight: 800;
            color: #f8fafc;
        }
        .doc {
            margin: 0 0 16px;
            font-size: 12px;
            color: #cbd5e1;
        }
        .qr-wrap {
            border-radius: 12px;
            background: #f8fafc;
            padding: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .qr-wrap svg {
            width: min(320px, 100%);
            height: auto;
        }
        .qr-value {
            margin-top: 12px;
            font-size: 11px;
            word-break: break-all;
            color: #cbd5e1;
            text-align: center;
        }
        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.5);
            padding: 10px 14px;
            font-weight: 700;
            font-size: 14px;
            color: #f8fafc;
            background: #1d4ed8;
        }
        .btn.btn-ghost {
            background: transparent;
        }
    </style>
</head>
<body>
    <main class="card">
        <p class="gym">{{ $gym?->name ?? 'Gimnasio' }}</p>
        <h1 class="name">{{ $client->full_name }}</h1>
        <p class="doc">Documento: {{ $client->document_number }}</p>

        <div class="qr-wrap">
            {!! $qrSvg !!}
        </div>
        <p class="qr-value">{{ $qrValue }}</p>
        <div class="actions">
            <a class="btn" href="{{ $qrDownloadUrl }}">Descargar QR</a>
            <a class="btn btn-ghost" href="{{ $qrImageUrl }}" target="_blank" rel="noopener">Ver QR</a>
        </div>
    </main>
</body>
</html>
