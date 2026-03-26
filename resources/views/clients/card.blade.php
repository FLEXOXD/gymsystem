<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Tarjeta Cliente #{{ $client->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            background: #111827;
            color: #fff;
        }
        .btn-secondary { background: #475569; }
        .card-wrap {
            width: 8.5cm;
            min-height: 5.4cm;
            background: #fff;
            border: 1.5px solid #111827;
            border-radius: 10px;
            padding: 10px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
            border-bottom: 1px solid #111827;
            padding-bottom: 6px;
        }
        .gym-logo {
            width: 38px;
            height: 38px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #111827;
        }
        .gym-name {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: right;
            max-width: 75%;
        }
        .content {
            display: grid;
            grid-template-columns: 1fr 2.8cm;
            gap: 8px;
            align-items: center;
        }
        .meta p {
            margin: 0 0 4px;
            line-height: 1.25;
        }
        .label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #374151;
        }
        .value {
            font-size: 12px;
            font-weight: 700;
            word-break: break-word;
        }
        .doc {
            font-size: 11px;
            font-family: Consolas, "Courier New", monospace;
        }
        .qr-box {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #111827;
            border-radius: 6px;
            padding: 4px;
            background: #fff;
            width: 2.8cm;
            height: 2.8cm;
        }
        .qr-box svg {
            width: 100%;
            height: 100%;
        }
        .qr-value {
            margin-top: 6px;
            font-size: 8px;
            font-family: Consolas, "Courier New", monospace;
            word-break: break-all;
            color: #374151;
        }
        @media print {
            @page {
                size: auto;
                margin: 8mm;
            }
            body {
                background: #fff;
                padding: 0;
            }
            .toolbar { display: none !important; }
            .card-wrap {
                border: 1px solid #000;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    @php
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $contextGym = trim((string) (request()->route('contextGym') ?? ''));
        $backToClientUrl = route('clients.show', ['contextGym' => $contextGym, 'client' => $client->id] + ($isGlobalScope ? ['scope' => 'global'] : []));
    @endphp
    <div class="toolbar">
        <button class="btn" type="button" onclick="window.print()">Imprimir</button>
        <a href="{{ route('clients.card.pdf', $client->id) }}" class="btn">Exportar PDF</a>
        <a href="{{ $backToClientUrl }}" class="btn btn-secondary">Volver al cliente</a>
    </div>

    <div class="card-wrap">
        <div class="header">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo gimnasio" class="gym-logo">
            @else
                <div class="gym-logo" style="display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;">GYM</div>
            @endif
            <div class="gym-name">{{ $gym?->name ?? 'Gimnasio' }}</div>
        </div>

        <div class="content">
            <div class="meta">
                <p class="label">Cliente</p>
                <p class="value">{{ $client->full_name }}</p>
                <p class="label">Documento</p>
                <p class="value doc">{{ $client->document_number }}</p>
            </div>

            <div>
                <div class="qr-box">{!! $qrSvg !!}</div>
            </div>
        </div>

        <div class="qr-value">{{ $qrValue }}</div>
    </div>
</body>
</html>
