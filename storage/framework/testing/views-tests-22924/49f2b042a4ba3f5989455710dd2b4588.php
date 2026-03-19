<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato legal <?php echo e($contractCode); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; line-height: 1.45; }
        .header { margin-bottom: 16px; }
        .title { margin: 0; font-size: 20px; font-weight: 700; }
        .meta { margin-top: 6px; color: #334155; font-size: 11px; }
        .section { border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        .section h2 { margin: 0 0 8px; font-size: 14px; }
        .row { margin-bottom: 4px; }
        .label { font-weight: 700; color: #334155; }
        ul { margin: 8px 0 0 18px; padding: 0; }
        li { margin-bottom: 4px; }
        .signature { margin-top: 16px; border-top: 1px dashed #94a3b8; padding-top: 12px; }
        .mono { font-family: DejaVu Sans Mono, monospace; font-size: 11px; }
        .footer-note { margin-top: 12px; font-size: 10px; color: #475569; }
    </style>
</head>
<body>
    <header class="header">
        <h1 class="title">Contrato de aceptación digital</h1>
        <p class="meta">Código de contrato: <span class="mono"><?php echo e($contractCode); ?></span></p>
        <p class="meta">Generado el: <?php echo e($generatedAt->format('Y-m-d H:i:s')); ?></p>
    </header>

    <section class="section">
        <h2>Datos del aceptante</h2>
        <p class="row"><span class="label">Nombre:</span> <?php echo e($acceptance->full_name); ?></p>
        <p class="row"><span class="label">Correo:</span> <?php echo e($acceptance->email); ?></p>
        <p class="row"><span class="label">ID usuario:</span> <?php echo e($acceptance->user_id ?? 'N/D'); ?></p>
        <p class="row"><span class="label">Gimnasio:</span> <?php echo e($acceptance->gym?->name ?? 'N/D'); ?></p>
    </section>

    <section class="section">
        <h2>Evidencia técnica de aceptación</h2>
        <p class="row"><span class="label">Documento:</span> <?php echo e($acceptance->document_label); ?></p>
        <p class="row"><span class="label">Versión legal:</span> <?php echo e($acceptance->legal_version); ?></p>
        <p class="row"><span class="label">Aceptado el:</span> <?php echo e($acceptance->accepted_at?->format('Y-m-d H:i:s') ?? '-'); ?></p>
        <p class="row"><span class="label">IP:</span> <?php echo e($acceptance->ip_address ?? '-'); ?></p>
        <p class="row"><span class="label">User-Agent:</span> <?php echo e($acceptance->user_agent ?? '-'); ?></p>
        <p class="row"><span class="label">Sesión:</span> <?php echo e($acceptance->session_id ?: '-'); ?></p>
        <p class="row"><span class="label">Permiso de ubicación:</span> <?php echo e($acceptance->location_permission ?? 'skipped'); ?></p>
        <p class="row">
            <span class="label">Coordenadas:</span>
            <?php if($acceptance->latitude !== null && $acceptance->longitude !== null): ?>
                <?php echo e(number_format((float) $acceptance->latitude, 6)); ?>, <?php echo e(number_format((float) $acceptance->longitude, 6)); ?>

                <?php if($acceptance->location_accuracy_m !== null): ?>
                    (precisión <?php echo e(number_format((float) $acceptance->location_accuracy_m, 2)); ?> m)
                <?php endif; ?>
            <?php else: ?>
                No registradas
            <?php endif; ?>
        </p>
    </section>

    <section class="section">
        <h2>Términos aceptados</h2>
        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="row"><span class="label"><?php echo e($term['label']); ?>:</span> <?php echo e($term['summary']); ?></p>
            <ul>
                <?php $__currentLoopData = $term['points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($point); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <section class="signature">
        <p><strong>Firma digital de aceptación:</strong> registrada al presionar "Aceptar condiciones" en el primer ingreso autenticado al sistema.</p>
        <p class="mono">HASH/REF: <?php echo e($contractCode); ?></p>
    </section>

    <p class="footer-note">
        Este documento es un comprobante digital emitido por GymSystem con fines de respaldo legal y auditoría interna.
    </p>
</body>
</html>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/legal-acceptances/contract-pdf.blade.php ENDPATH**/ ?>