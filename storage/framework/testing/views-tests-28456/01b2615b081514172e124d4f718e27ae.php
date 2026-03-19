<?php
    $currencyFormatter = \App\Support\Currency::class;
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo e(__('ui.profile.invoice_pdf_title')); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        .header { margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: 700; margin: 0; }
        .subtitle { margin: 4px 0 0; color: #475569; }
        .box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; margin-bottom: 12px; }
        .label { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px; }
        .value { font-size: 13px; font-weight: 600; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background: #f1f5f9; font-size: 11px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title"><?php echo e(__('ui.profile.invoice_pdf_title')); ?></h1>
        <p class="subtitle"><?php echo e($gymName); ?></p>
    </div>

    <div class="box">
        <div class="label"><?php echo e(__('ui.profile.invoice_pdf_user')); ?></div>
        <div class="value"><?php echo e($userName !== '' ? $userName : '-'); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th><?php echo e(__('ui.profile.invoice_period')); ?></th>
                <th><?php echo e(__('ui.profile.invoice_amount')); ?></th>
                <th><?php echo e(__('ui.profile.invoice_method')); ?></th>
                <th><?php echo e(__('ui.profile.invoice_status')); ?></th>
                <th><?php echo e(__('ui.profile.invoice_date')); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo e($invoice['period']); ?></td>
                <td><?php echo e($currencyFormatter::format((float) ($invoice['amount'] ?? 0), $currencyCode)); ?></td>
                <td><?php echo e($invoice['payment_method'] !== '' ? $invoice['payment_method'] : '-'); ?></td>
                <td><?php echo e($invoice['status'] === 'paid' ? __('ui.profile.invoice_paid') : __('ui.profile.invoice_pending')); ?></td>
                <td><?php echo e($invoice['recorded_at']?->format('Y-m-d H:i') ?? '-'); ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>

<?php /**PATH C:\laragon\www\gymsystem\resources\views/admin/settings/membership-invoice-pdf.blade.php ENDPATH**/ ?>