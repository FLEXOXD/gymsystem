<?php ($content = $content ?? \App\Support\MarketingContent::defaults()); ?>
<?php ($demoCtaLabel = 'Demo gratis'); ?>
<?php ($brandName = trim((string) ($content['brand_name'] ?? 'FlexGym'))); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b1020">
    <title><?php echo e($brandName); ?> | Demo guiada</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        body {
            margin: 0;
            font-family: "Space Grotesk", "Segoe UI", system-ui, sans-serif;
            background: radial-gradient(circle at top left, rgba(15, 60, 201, 0.2), transparent 48%), #0b1020;
            color: #e2e8f0;
        }
        .guide-wrap {
            width: min(980px, 100% - 2rem);
            margin: 0 auto;
            padding: 1rem 0 2.2rem;
        }
        .top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
        }
        .top a {
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 700;
            font-size: .86rem;
        }
        .hero {
            margin-top: 1rem;
            border: 1px solid #1e3a8a;
            border-radius: 1rem;
            background: linear-gradient(160deg, rgba(15, 23, 42, 0.95), rgba(15, 35, 78, 0.94));
            padding: 1.1rem;
        }
        .hero h1 {
            margin: 0;
            font-size: clamp(1.45rem, 2.8vw, 2rem);
            line-height: 1.1;
            letter-spacing: -.02em;
        }
        .hero p {
            margin: .7rem 0 0;
            color: #94a3b8;
            line-height: 1.52;
        }
        .cta {
            margin-top: .85rem;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }
        .cta form {
            margin: 0;
        }
        .btn {
            border: 0;
            border-radius: .8rem;
            padding: .6rem .9rem;
            font-size: .8rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-main {
            color: #fff;
            background: linear-gradient(140deg, #0f3cc9, #0fb5ff);
        }
        .btn-soft {
            color: #e2e8f0;
            border: 1px solid #334155;
            background: rgba(15, 23, 42, 0.75);
        }
        .steps {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }
        .step {
            border: 1px solid #334155;
            border-radius: .95rem;
            background: rgba(15, 23, 42, 0.72);
            padding: .85rem;
        }
        .step-index {
            width: 1.7rem;
            height: 1.7rem;
            border-radius: 999px;
            background: linear-gradient(140deg, #0f3cc9, #0fb5ff);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .76rem;
            font-weight: 800;
        }
        .step h2 {
            margin: .55rem 0 .25rem;
            font-size: 1.02rem;
            line-height: 1.2;
        }
        .step p {
            margin: 0;
            color: #94a3b8;
            line-height: 1.45;
            font-size: .9rem;
        }
        .footer-note {
            margin-top: 1rem;
            border: 1px solid #1e3a8a;
            border-radius: .9rem;
            padding: .85rem;
            color: #cbd5e1;
            font-size: .9rem;
            background: rgba(15, 23, 42, 0.8);
        }
        @media (max-width: 780px) {
            .steps { grid-template-columns: 1fr; }
        }
        @media (max-width: 620px) {
            .guide-wrap { width: min(980px, 100% - 1rem); }
        }
    </style>
</head>
<body>
    <main class="guide-wrap">
        <div class="top">
            <a href="<?php echo e(route('landing')); ?>">Volver al inicio</a>
            <a href="<?php echo e(route('demo')); ?>">Volver a información demo</a>
        </div>

        <section class="hero">
            <h1><?php echo e($content['guide_title'] ?? 'Demo guiada'); ?></h1>
            <p><?php echo e($content['guide_subtitle'] ?? 'Te acompanas paso a paso para conocer el flujo operativo.'); ?></p>
            <div class="cta">
                <form method="POST" action="<?php echo e(route('demo.request')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-main"><?php echo e($demoCtaLabel); ?></button>
                </form>
                <a class="btn btn-soft" href="<?php echo e(route('landing.contact')); ?>">Contáctanos</a>
            </div>
        </section>

        <section class="steps" aria-label="Pasos de demo guiada">
            <article class="step">
                <span class="step-index">1</span>
                <h2><?php echo e($content['guide_step_1_title'] ?? 'Paso 1'); ?></h2>
                <p><?php echo e($content['guide_step_1_text'] ?? 'Configura tu sede principal y datos base.'); ?></p>
            </article>
            <article class="step">
                <span class="step-index">2</span>
                <h2><?php echo e($content['guide_step_2_title'] ?? 'Paso 2'); ?></h2>
                <p><?php echo e($content['guide_step_2_text'] ?? 'Carga clientes y planes iniciales.'); ?></p>
            </article>
            <article class="step">
                <span class="step-index">3</span>
                <h2><?php echo e($content['guide_step_3_title'] ?? 'Paso 3'); ?></h2>
                <p><?php echo e($content['guide_step_3_text'] ?? 'Activa membresías y prueba recepción.'); ?></p>
            </article>
            <article class="step">
                <span class="step-index">4</span>
                <h2><?php echo e($content['guide_step_4_title'] ?? 'Paso 4'); ?></h2>
                <p><?php echo e($content['guide_step_4_text'] ?? 'Válida caja, cobros y reportes.'); ?></p>
            </article>
            <article class="step" style="grid-column: 1 / -1;">
                <span class="step-index">5</span>
                <h2><?php echo e($content['guide_step_5_title'] ?? 'Paso 5'); ?></h2>
                <p><?php echo e($content['guide_step_5_text'] ?? 'Confirma configuración final antes de operar.'); ?></p>
            </article>
        </section>

        <div class="footer-note">
            Esta guía muestra el orden recomendado. Con "<?php echo e($demoCtaLabel); ?>" entras al sistema real en modo temporal.
        </div>
    </main>
</body>
</html>



<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/demo-guide.blade.php ENDPATH**/ ?>