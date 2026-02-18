<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tarjeta Cliente {{ $client->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111;
            background: #fff;
        }
        .page {
            width: 100%;
            text-align: center;
        }
        .card {
            display: inline-block;
            width: 8.5cm;
            min-height: 5.4cm;
            border: 1px solid #111;
            border-radius: 8px;
            padding: 8px;
            text-align: left;
        }
        .header {
            border-bottom: 1px solid #111;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .gym-name {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
        }
        .logo {
            max-width: 34px;
            max-height: 34px;
            margin-bottom: 4px;
        }
        .line-label {
            font-size: 9px;
            color: #444;
            text-transform: uppercase;
            margin: 0;
        }
        .line-value {
            font-size: 12px;
            font-weight: 700;
            margin: 2px 0 6px;
            word-break: break-word;
        }
        .doc {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 10px;
        }
        .qr-box {
            width: 2.8cm;
            height: 2.8cm;
            border: 1px solid #111;
            padding: 3px;
            margin: 6px auto 0;
            text-align: center;
        }
        .qr-box img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .qr-value {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 7px;
            margin-top: 6px;
            word-break: break-all;
            text-align: center;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <div class="header">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo gimnasio" class="logo">
                @endif
                <p class="gym-name">{{ $gym?->name ?? 'Gimnasio' }}</p>
            </div>

            <p class="line-label">Cliente</p>
            <p class="line-value">{{ $client->full_name }}</p>

            <p class="line-label">Documento</p>
            <p class="line-value doc">{{ $client->document_number }}</p>

            <div class="qr-box">
                <img src="data:image/svg+xml;base64,{{ $qrSvgBase64 }}" alt="QR Cliente">
            </div>
            <div class="qr-value">{{ $qrValue }}</div>
        </div>
    </div>
</body>
</html>
