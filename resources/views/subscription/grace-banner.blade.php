@if (!empty($subscription_grace))
    <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: #fee2e2; color: #991b1b; font-weight: 700;">
        Su suscripcion ha vencido. Tiene {{ (int) ($subscription_grace_days ?? 3) }} dias de gracia para renovar.
    </div>
@endif
