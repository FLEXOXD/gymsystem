<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripcion suspendida</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", sans-serif; background: #f5f7fa; color: #1b2430; }
        .wrap { min-height: 100vh; display: grid; place-items: center; padding: 20px; }
        .card {
            width: min(560px, 100%);
            background: #fff;
            border: 1px solid #f0c9c9;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .title { margin: 0 0 10px; color: #9d2323; }
        .msg { margin: 0 0 14px; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn {
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            text-decoration: none;
            color: #fff;
            background: #0f5fbb;
            font-weight: 600;
        }
        .btn-ghost { background: #4b5563; }
    </style>
</head>
<body>
<div class="wrap">
    <section class="card">
        <h1 class="title">Suscripcion suspendida</h1>
        <p class="msg">Su suscripcion ha sido suspendida por falta de pago. Contacte al administrador.</p>
        <div class="actions">
            <a class="btn" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Cerrar sesion
            </a>
            <a class="btn btn-ghost" href="{{ route('subscription.expired') }}">Actualizar</a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </section>
</div>
</body>
</html>
