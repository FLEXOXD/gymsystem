<?php ($content = $content ?? \App\Support\MarketingContent::defaults()); ?>
<?php ($demoCtaLabel = 'Demo gratis'); ?>
<?php ($brandName = trim((string) ($content['brand_name'] ?? 'GymSystem'))); ?>
<?php ($brandLogoUrl = trim((string) ($content['brand_logo_url'] ?? ''))); ?>
<?php ($brandInitials = trim((string) ($content['brand_initials'] ?? 'GS'))); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b1020">
    <title>GymSystem Demo | Flujo de ejemplo</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        body {
            margin: 0;
            font-family: "Space Grotesk", "Segoe UI", system-ui, sans-serif;
            background: radial-gradient(circle at top right, rgba(15, 181, 255, 0.18), transparent 42%), #eaf0fb;
            color: #0f172a;
        }
        .demo-wrap {
            width: min(980px, 100% - 2rem);
            margin: 0 auto;
            padding: 1rem 0 2.4rem;
        }
        .demo-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }
        .demo-brand {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            text-decoration: none;
            color: inherit;
            font-weight: 800;
            letter-spacing: -.01em;
        }
        .demo-badge {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: .75rem;
            background: linear-gradient(140deg, #0f3cc9, #0fb5ff);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
        }
        .demo-brand-logo {
            width: 2.9rem;
            height: 2.9rem;
            border-radius: 0;
            object-fit: contain;
            border: 0;
            background: transparent;
        }
        .demo-actions {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }
        .demo-actions form {
            margin: 0;
        }
        .btn {
            border: 0;
            border-radius: .8rem;
            padding: .62rem .95rem;
            font-size: .8rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-outline { border: 1px solid #ccd7eb; background: #fff; color: #0f172a; }
        .btn-main { color: #fff; background: linear-gradient(140deg, #0f3cc9, #0fb5ff); }
        .hero {
            margin-top: 1rem;
            background: #ffffff;
            border: 1px solid #d5deec;
            border-radius: 1.1rem;
            padding: 1.2rem;
        }
        .hero h1 {
            margin: 0;
            font-size: clamp(1.5rem, 2.8vw, 2.15rem);
            line-height: 1.1;
            letter-spacing: -.02em;
        }
        .hero p {
            margin: .7rem 0 0;
            color: #475569;
            line-height: 1.55;
        }
        .note {
            margin-top: .8rem;
            border-radius: .8rem;
            border: 1px solid #d5deec;
            background: #f8fbff;
            padding: .65rem .8rem;
            font-size: .85rem;
            color: #1e293b;
        }
        .grid {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }
        .card {
            border: 1px solid #d5deec;
            border-radius: .95rem;
            background: #fff;
            padding: .9rem;
        }
        .card small {
            display: block;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
            font-size: .68rem;
        }
        .card b {
            display: block;
            margin-top: .35rem;
            font-size: 1.2rem;
        }
        .stack {
            margin-top: 1rem;
            display: grid;
            gap: .65rem;
        }
        .step {
            border: 1px solid #d5deec;
            border-radius: .85rem;
            background: #fff;
            padding: .75rem .85rem;
        }
        .step strong {
            display: block;
            font-size: .92rem;
        }
        .step span {
            display: block;
            margin-top: .2rem;
            color: #64748b;
            font-size: .85rem;
        }
        @media (max-width: 860px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 620px) {
            .demo-wrap { width: min(980px, 100% - 1rem); }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="demo-wrap">
        <nav class="demo-nav">
            <a class="demo-brand" href="<?php echo e(route('landing')); ?>">
                <?php if($brandLogoUrl !== ''): ?>
                    <img src="<?php echo e($brandLogoUrl); ?>" alt="<?php echo e($brandName); ?>" class="demo-brand-logo">
                <?php else: ?>
                    <span class="demo-badge"><?php echo e($brandInitials); ?></span>
                <?php endif; ?>
                <span><?php echo e($brandName); ?> Demo</span>
            </a>
            <div class="demo-actions">
                <a class="btn btn-outline" href="<?php echo e(route('landing')); ?>">Volver al inicio</a>
                <a class="btn btn-outline" href="<?php echo e(route('login')); ?>">Iniciar sesión</a>
            </div>
        </nav>

        <section class="hero">
            <h1><?php echo e($demoCtaLabel); ?>: entra al sistema real con cuenta temporal</h1>
            <p>Inicias con un gimnasio de prueba aislado. Puedes navegar clientes, planes, caja y reportes sin afectar cuentas reales.</p>
            <div class="note">
                Los datos demo se eliminan automáticamente al expirar la sesión temporal.
            </div>
            <div class="demo-actions" style="margin-top:.85rem;">
                <form method="POST" action="<?php echo e(route('demo.request')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-main"><?php echo e($demoCtaLabel); ?></button>
                </form>
                <a class="btn btn-outline" href="<?php echo e($content['whatsapp_url']); ?>" target="_blank" rel="noreferrer">Hablar por WhatsApp</a>
            </div>
        </section>

        <section class="grid" aria-label="Kpis demo">
            <article class="card"><small>Clientes</small><b>124</b></article>
            <article class="card"><small>Membresías activas</small><b>87</b></article>
            <article class="card"><small>Por vencer</small><b>12</b></article>
            <article class="card"><small>Check-ins hoy</small><b>54</b></article>
            <article class="card"><small>Ingresos hoy</small><b>$ 1,430</b></article>
            <article class="card"><small>Turno de caja</small><b>Abierto</b></article>
        </section>

        <section class="stack" aria-label="Flujo demo">
            <article class="step">
                <strong>1) Alta rápida del cliente</strong>
                <span>Se registra foto, documento y teléfono en una sola pantalla.</span>
            </article>
            <article class="step">
                <strong>2) Activación de membresía + cobro</strong>
                <span>Un solo flujo para no duplicar trabajo en recepción.</span>
            </article>
            <article class="step">
                <strong>3) Seguimiento operativo diario</strong>
                <span>Reportes y alertas de vencimiento para tomar decisiones rápido.</span>
            </article>
        </section>
    </main>
</body>
</html>


<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/demo.blade.php ENDPATH**/ ?>