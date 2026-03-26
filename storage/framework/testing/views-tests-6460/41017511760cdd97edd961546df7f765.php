<?php $__env->startSection('title', 'Cliente #'.$client->id); ?>
<?php $__env->startSection('page-title', 'Cliente: '.$client->full_name); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .client-hero-card {
            background:
                radial-gradient(circle at top right, rgb(14 165 233 / 0.14), transparent 34%),
                linear-gradient(180deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.98));
        }

        .theme-dark .client-hero-card {
            border-color: rgb(51 65 85 / 0.9);
            background:
                radial-gradient(circle at top right, rgb(34 211 238 / 0.18), transparent 34%),
                linear-gradient(180deg, rgb(2 6 23 / 0.96), rgb(15 23 42 / 0.9));
        }

        .client-hero-stat {
            border: 1px solid rgb(148 163 184 / 0.28);
            background: rgb(255 255 255 / 0.7);
            border-radius: 1rem;
            padding: 0.8rem 0.9rem;
        }

        .theme-dark .client-hero-stat {
            border-color: rgb(148 163 184 / 0.18);
            background: rgb(15 23 42 / 0.62);
        }

        .client-hero-stat-label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgb(100 116 139);
        }

        .theme-dark .client-hero-stat-label {
            color: rgb(148 163 184);
        }

        .client-hero-actions {
            display: grid;
            gap: 0.75rem;
            width: 100%;
            align-content: start;
        }

        .client-hero-layout {
            display: grid;
            gap: 1.25rem;
        }

        .client-hero-actions-full {
            grid-column: 1 / -1;
        }

        .client-action-popover {
            width: min(19rem, calc(100vw - 2rem));
        }

        .client-hero-status {
            display: flex;
            justify-content: flex-start;
        }

        .client-detail-shell {
            --client-sticky-offset: 5.7rem;
            position: relative;
            scroll-padding-top: calc(var(--client-sticky-offset) + 0.9rem);
        }

        .client-hero-card,
        .client-tab-panel,
        .client-tabs-wrap {
            scroll-margin-top: calc(var(--client-sticky-offset) + 0.8rem);
        }

        .client-tabs-wrap {
            position: sticky;
            top: calc(var(--client-sticky-offset) - 0.35rem);
            z-index: 18;
            border-radius: 1.05rem;
            border: 1px solid rgb(148 163 184 / 0.3);
            background: linear-gradient(145deg, rgb(248 250 252 / 0.95), rgb(241 245 249 / 0.92));
            box-shadow: 0 20px 32px -28px rgb(15 23 42 / 0.5);
            backdrop-filter: blur(7px);
        }

        .theme-dark .client-tabs-wrap {
            border-color: rgb(148 163 184 / 0.18);
            background: linear-gradient(150deg, rgb(15 23 42 / 0.9), rgb(2 6 23 / 0.8));
            box-shadow: 0 22px 36px -30px rgb(2 8 23 / 0.9);
        }

        .client-tabs-strip {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .client-tabs-strip::-webkit-scrollbar {
            display: none;
        }

        .client-tab-chip {
            min-height: 2.7rem;
            white-space: nowrap;
            border-radius: 0.95rem;
        }

        .client-tab-chip-active {
            box-shadow: 0 14px 24px -16px rgb(14 165 233 / 0.85);
        }

        .client-tab-chip-idle {
            opacity: 0.95;
        }

        .client-tab-chip-idle:hover {
            opacity: 1;
        }

        .client-tab-panel .ui-card {
            border: 1px solid rgb(148 163 184 / 0.28);
            background: linear-gradient(156deg, rgb(255 255 255 / 0.96), rgb(248 250 252 / 0.94));
            box-shadow: 0 22px 38px -34px rgb(15 23 42 / 0.56);
        }

        .theme-dark .client-tab-panel .ui-card {
            border-color: rgb(148 163 184 / 0.18);
            background: linear-gradient(160deg, rgb(2 6 23 / 0.86), rgb(15 23 42 / 0.7));
            box-shadow: 0 24px 40px -30px rgb(2 8 23 / 0.88);
        }

        .client-tab-panel .ui-card > header {
            margin-bottom: 0.95rem;
        }

        .client-tab-panel .ui-card > header .ui-heading {
            letter-spacing: -0.015em;
        }

        .client-tab-panel .ui-card > header .ui-muted {
            margin-top: 0.3rem;
            font-size: 0.86rem;
        }

        .client-tab-panel .ui-table thead tr {
            background: rgb(241 245 249 / 0.94);
            border-bottom-color: rgb(203 213 225 / 0.8);
        }

        .theme-dark .client-tab-panel .ui-table thead tr {
            background: rgb(51 65 85 / 0.88);
            border-bottom-color: rgb(71 85 105 / 0.9);
        }

        .client-tab-panel .ui-table th {
            font-size: 0.69rem;
            letter-spacing: 0.11em;
        }

        .client-tab-panel .ui-table th,
        .client-tab-panel .ui-table td {
            padding-top: 0.86rem;
            padding-bottom: 0.86rem;
        }

        .client-empty-state {
            border-style: dashed !important;
            border-color: rgb(148 163 184 / 0.52) !important;
            background: linear-gradient(160deg, rgb(248 250 252 / 0.95), rgb(241 245 249 / 0.84)) !important;
        }

        .theme-dark .client-empty-state {
            border-color: rgb(100 116 139 / 0.62) !important;
            background: linear-gradient(160deg, rgb(15 23 42 / 0.85), rgb(2 6 23 / 0.75)) !important;
        }

        .client-action-popover {
            border: 1px solid rgb(148 163 184 / 0.28);
            background: rgb(255 255 255 / 0.92);
            box-shadow: 0 20px 34px -24px rgb(15 23 42 / 0.62);
            backdrop-filter: blur(8px);
            padding: 0.38rem;
        }

        .theme-dark .client-action-popover {
            border-color: rgb(71 85 105 / 0.74);
            background: rgb(2 6 23 / 0.93);
            box-shadow: 0 22px 38px -26px rgb(2 8 23 / 0.92);
        }

        .client-action-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            border-radius: 0.78rem;
            border: 1px solid rgb(148 163 184 / 0.28);
            background: rgb(248 250 252 / 0.82);
            padding: 0.6rem 0.72rem;
            text-align: left;
            font-size: 0.79rem;
            font-weight: 700;
            color: rgb(15 23 42 / 0.96);
            transition: border-color 120ms ease, background-color 120ms ease, transform 120ms ease;
        }

        .client-action-item:hover {
            border-color: rgb(14 165 233 / 0.55);
            background: rgb(224 242 254 / 0.85);
            transform: translateY(-1px);
        }

        .theme-dark .client-action-item {
            border-color: rgb(71 85 105 / 0.72);
            background: rgb(15 23 42 / 0.8);
            color: rgb(226 232 240 / 0.94);
        }

        .theme-dark .client-action-item:hover {
            border-color: rgb(34 211 238 / 0.6);
            background: rgb(8 47 73 / 0.55);
        }

        .client-credentials-toolbar {
            margin-bottom: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .client-credentials-hero {
            border-radius: 1rem;
            border: 1px solid rgb(34 211 238 / 0.32);
            background: linear-gradient(155deg, rgb(6 182 212 / 0.13), rgb(14 116 144 / 0.06));
            padding: 0.95rem;
            gap: 0.9rem;
        }

        .theme-dark .client-credentials-hero {
            border-color: rgb(34 211 238 / 0.25);
            background: linear-gradient(165deg, rgb(6 182 212 / 0.16), rgb(8 47 73 / 0.22));
        }

        .client-credentials-actions {
            display: grid;
            gap: 0.45rem;
            grid-template-columns: repeat(auto-fit, minmax(9.8rem, 1fr));
        }

        .client-layout-wide {
            display: grid;
            gap: 1.4rem;
        }

        @media (min-width: 1536px) {
            .client-layout-wide {
                grid-template-columns: minmax(0, 1fr) minmax(18rem, 23rem);
                align-items: start;
            }
        }

        .client-tab-panel {
            animation: clientFade .16s ease-out;
        }

        @keyframes clientFade {
            from {
                opacity: 0;
                transform: translateY(3px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 640px) {
            .client-hero-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .client-detail-shell {
                --client-sticky-offset: 6.35rem;
            }

            .client-hero-layout {
                grid-template-columns: minmax(0, 1fr) 18rem;
                align-items: start;
            }

            .client-hero-actions {
                grid-template-columns: 1fr;
            }

            .client-hero-status {
                justify-content: flex-end;
            }
        }

        @media (max-width: 1279px) {
            .client-detail-shell {
                --client-sticky-offset: 7.1rem;
            }
        }

        @media (max-width: 900px) {
            .client-detail-shell {
                --client-sticky-offset: 7.7rem;
            }
        }

        @media (max-width: 639px) {
            .client-detail-shell {
                --client-sticky-offset: 8.2rem;
            }

            .client-action-popover {
                left: 0;
                right: 0;
                width: auto;
            }

            .client-tabs-wrap {
                position: static;
                top: auto;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $photoPath = trim((string) ($client->photo_path ?? ''));
        $photoUrl = null;
        if ($photoPath !== '') {
            if (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://')) {
                $photoUrl = $photoPath;
            } elseif (str_starts_with($photoPath, 'storage/') || str_starts_with($photoPath, '/storage/')) {
                $photoUrl = url('/'.ltrim($photoPath, '/'));
            } else {
                $photoUrl = url('/storage/'.ltrim($photoPath, '/'));
            }
        }

        $today = now()->startOfDay();
        $lastAttendance = $client->attendances->first();
        $lastAttendanceLabel = 'Sin asistencia';
        if ($lastAttendance?->date) {
            $attDate = $lastAttendance->date->copy()->startOfDay();
            $attTimeRaw = trim((string) ($lastAttendance->time ?? ''));
            $attTime = $attTimeRaw !== '' ? mb_substr($attTimeRaw, 0, 5) : '--:--';
            if ($attDate->isSameDay($today)) {
                $lastAttendanceLabel = 'Hoy '.$attTime;
            } else {
                $daysAgo = $attDate->diffInDays($today);
                if ($daysAgo <= 30) {
                    $lastAttendanceLabel = ($daysAgo === 1 ? 'Hace 1 día' : "Hace {$daysAgo} días").' '.$attTime;
                } else {
                    $lastAttendanceLabel = $attDate->translatedFormat('d M Y').' '.$attTime;
                }
            }
        }

        $statusLabels = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'expired' => 'Vencido',
            'cancelled' => 'Cancelado',
            'scheduled' => 'Programada',
        ];

        $adjustmentTypeLabels = [
            'reschedule_start' => 'Mover fecha de inicio',
            'extend_access' => 'Sumar días al final',
            'manual_window' => 'Corregir fechas manualmente',
        ];

        $adjustmentTypeHelp = [
            'reschedule_start' => 'Mueve la fecha de inicio y recalcula el fin con la duración normal del plan.',
            'extend_access' => 'Agrega días al final sin cambiar la fecha de inicio.',
            'manual_window' => 'Corrige inicio y fin manualmente solo para casos excepcionales o administrativos.',
        ];

        $adjustmentReasonLabels = [
            'payment_registered_late' => 'Pago recibido antes del registro',
            'future_start_requested' => 'Inicio acordado para otra fecha',
            'grace_period' => 'Prórroga o permiso temporal',
            'administrative_correction' => 'Corrección administrativa',
            'owner_exception' => 'Excepción autorizada por el dueño',
        ];

        $adjustmentReasonHelp = [
            'payment_registered_late' => 'Cuando el cliente pagó antes y recién lo estás registrando o moviendo.',
            'future_start_requested' => 'Cuando el cliente pidió empezar después de la fecha de cobro.',
            'grace_period' => 'Cuando vas a regalar o prorrogar días adicionales al final.',
            'administrative_correction' => 'Cuando corriges un error interno de fechas o registro.',
            'owner_exception' => 'Cuando el dueño autorizó una excepción fuera del flujo normal.',
        ];

        $adjustmentReasonMap = [
            'reschedule_start' => ['payment_registered_late', 'future_start_requested', 'administrative_correction'],
            'extend_access' => ['grace_period', 'administrative_correction', 'owner_exception'],
            'manual_window' => ['future_start_requested', 'administrative_correction', 'owner_exception'],
        ];

        $adjustmentReasonOptions = collect($adjustmentReasonLabels)
            ->map(fn (string $label, string $value): array => [
                'value' => $value,
                'label' => $label,
                'help' => $adjustmentReasonHelp[$value] ?? '',
            ])
            ->values()
            ->all();

        $latestMembershipStartsAt = $latestMembership?->starts_at?->copy()->startOfDay();
        $latestMembershipEndsAt = $latestMembership?->ends_at?->copy()->startOfDay();
        $daysLeft = $latestMembershipEndsAt
            ? $today->diffInDays($latestMembershipEndsAt, false)
            : null;
        $daysUntilStart = $latestMembershipStartsAt
            ? $today->diffInDays($latestMembershipStartsAt, false)
            : null;
        $isCancelledMembership = $latestMembership && (string) $latestMembership->status === 'cancelled';
        $isScheduledMembership = $latestMembershipStartsAt !== null
            && $latestMembershipStartsAt->greaterThan($today)
            && ! $isCancelledMembership;
        $isExpiredMembership = $latestMembership
            && ($isCancelledMembership || $latestMembershipEndsAt === null || $latestMembershipEndsAt->lt($today));

        $membershipBadgeVariant = 'muted';
        $membershipBadgeText = 'Sin membresía';
        $membershipLabel = 'Sin membresía';
        $membershipDateLabel = 'Vence';
        $membershipDateValue = 'N/A';
        $membershipCountdownLabel = 'Restan';
        $membershipCountdownValue = 'N/A';
        $membershipStartsLabel = $latestMembershipStartsAt?->translatedFormat('d M Y') ?? 'N/A';
        $membershipEndsLabel = $latestMembershipEndsAt?->translatedFormat('d M Y') ?? 'N/A';
        $remainingLabel = 'N/A';

        if ($latestMembership) {
            if ($isScheduledMembership) {
                $membershipBadgeVariant = 'info';
                $membershipBadgeText = 'Programada';
                $membershipLabel = 'Pendiente de iniciar';
                $membershipDateLabel = 'Inicia';
                $membershipDateValue = $membershipStartsLabel;
                $membershipCountdownLabel = 'Ventana';
                $membershipCountdownValue = $membershipStartsLabel.' -> '.$membershipEndsLabel;
                $remainingLabel = $daysUntilStart === null
                    ? 'Pendiente'
                    : ($daysUntilStart === 0 ? 'Inicia hoy' : 'Empieza en '.$daysUntilStart.' días');
            } elseif (! $isExpiredMembership && $daysLeft !== null && $daysLeft <= 7) {
                $membershipBadgeVariant = 'warning';
                $membershipBadgeText = 'Por vencer';
                $membershipLabel = 'Vigente';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = $daysLeft === 0 ? 'Hoy' : $daysLeft.' días';
                $remainingLabel = $membershipCountdownValue;
            } elseif (! $isExpiredMembership) {
                $membershipBadgeVariant = 'success';
                $membershipBadgeText = 'Vigente';
                $membershipLabel = 'Vigente';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = $daysLeft === null ? 'N/A' : ($daysLeft === 0 ? 'Hoy' : $daysLeft.' días');
                $remainingLabel = $membershipCountdownValue;
            } elseif ($isCancelledMembership) {
                $membershipBadgeVariant = 'danger';
                $membershipBadgeText = 'Cancelada';
                $membershipLabel = 'Cancelada';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = 'Sin acceso';
                $remainingLabel = $membershipCountdownValue;
            } else {
                $membershipBadgeVariant = 'danger';
                $membershipBadgeText = 'Vencida';
                $membershipLabel = 'Vencida';
                $membershipDateValue = $membershipEndsLabel;
                $membershipCountdownValue = $daysLeft === null
                    ? 'Vencida'
                    : (abs($daysLeft) === 0 ? 'Venció hoy' : 'Hace '.abs($daysLeft).' días');
                $remainingLabel = $membershipCountdownValue;
            }
        }

        $methodLabels = [
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ];

        $attendanceMethodLabels = [
            'document' => 'Documento',
            'rfid' => 'RFID',
            'qr' => 'QR',
        ];

        $membershipFormMode = trim((string) old('membership_form_mode', ''));
        $membershipErrorKeys = ['plan_id', 'starts_at', 'status', 'payment_method', 'payment_received_at', 'promotion_id', 'cash'];
        $membershipAdjustmentErrorKeys = ['membership_adjustment', 'adjustment_type', 'reason', 'notes', 'extra_days', 'starts_at', 'ends_at'];
        $rfidErrorKeys = ['rfid', 'value'];
        $appAccountErrorKeys = ['app_username', 'app_password', 'app_password_confirmation'];

        $openMembershipModal = (bool) old('_open_membership_modal', false)
            || $membershipFormMode === 'create'
            || ($errors->hasAny($membershipErrorKeys) && $membershipFormMode !== 'adjustment');
        $openAdjustmentModal = ! empty($canAdjustMemberships) && (
            (bool) old('_open_adjust_membership_modal', false)
            || $membershipFormMode === 'adjustment'
            || ($errors->hasAny($membershipAdjustmentErrorKeys) && $membershipFormMode === 'adjustment')
        );
        $openRfidModal = $errors->hasAny($rfidErrorKeys);
        $openAppAccountTab = ! empty($canManageClientAccounts) && (
            $errors->hasAny($appAccountErrorKeys) || old('active_tab') === 'app_access'
        );

        $progressTabUrl = null;
        if (! empty($canShowProgress)) {
            $progressTabUrlParams = [
                'client' => $client->id,
                'tab' => 'progress',
            ];
            $contextGym = trim((string) request()->route('contextGym'));
            if ($contextGym !== '') {
                $progressTabUrlParams['contextGym'] = $contextGym;
            }
            $pwaMode = strtolower(trim((string) request()->query('pwa_mode', '')));
            if ($pwaMode === 'standalone') {
                $progressTabUrlParams['pwa_mode'] = 'standalone';
            }
            $progressTabUrl = route('clients.show', $progressTabUrlParams);
        }

        $initialTab = 'summary';
        $allowedTabs = ['summary', 'membership', 'attendance', 'credentials'];
        if (! empty($canShowProgress)) {
            $allowedTabs[] = 'progress';
        }
        if (! empty($canManageClientAccounts)) {
            $allowedTabs[] = 'app_access';
        }

        $requestedTab = trim((string) old('active_tab', request()->query('tab', '')));
        if (in_array($requestedTab, $allowedTabs, true)) {
            $initialTab = $requestedTab;
        }

        if ($openMembershipModal || $openAdjustmentModal) {
            $initialTab = 'membership';
        }
        if ($openRfidModal) {
            $initialTab = 'credentials';
        }
        if ($openAppAccountTab) {
            $initialTab = 'app_access';
        }

        $attendancePreview = $client->attendances->take(4);
        $paymentsPreview = $recentMembershipPayments->take(4);

        $adjustmentMemberships = $client->memberships
            ->map(function ($membership): array {
                return [
                    'id' => (int) $membership->id,
                    'planName' => (string) ($membership->plan?->name ?? 'Sin plan'),
                    'status' => (string) ($membership->status ?? ''),
                    'startsAt' => $membership->starts_at?->toDateString(),
                    'endsAt' => $membership->ends_at?->toDateString(),
                    'bonusDays' => (int) ($membership->bonus_days ?? 0),
                    'durationUnit' => (string) ($membership->plan?->duration_unit ?? 'days'),
                    'durationDays' => (int) ($membership->plan?->duration_days ?? 30),
                    'durationMonths' => $membership->plan?->duration_months !== null
                        ? (int) $membership->plan->duration_months
                        : null,
                    'adjustUrl' => route('memberships.adjust', ['membership' => $membership->id]),
                ];
            })
            ->values();

        $initialAdjustmentMembershipId = old('adjust_membership_id');
        if ($initialAdjustmentMembershipId === null && $latestMembership) {
            $initialAdjustmentMembershipId = (int) $latestMembership->id;
        }

        $oldAdjustmentInput = [
            'adjustmentType' => (string) old('adjustment_type', 'reschedule_start'),
            'reason' => (string) old('reason', 'payment_registered_late'),
            'startsAt' => (string) old('starts_at', ''),
            'endsAt' => (string) old('ends_at', ''),
            'extraDays' => (string) old('extra_days', ''),
            'notes' => (string) old('notes', ''),
        ];

        $hasAdjustmentOldInput = $membershipFormMode === 'adjustment'
            && (
                old('adjustment_type') !== null
                || old('reason') !== null
                || old('starts_at') !== null
                || old('ends_at') !== null
                || old('extra_days') !== null
                || old('notes') !== null
            );
        $contextGym = trim((string) request()->route('contextGym'));
        $isGlobalScope = (bool) request()->attributes->get('active_gym_is_global', false);
        $clientRouteParams = ['contextGym' => $contextGym, 'client' => $client->id] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $moduleRouteParams = ['contextGym' => $contextGym] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $clientReportRouteParams = [
            'contextGym' => $contextGym,
            'search' => $client->document_number,
        ] + ($isGlobalScope ? ['scope' => 'global'] : []);
        $professionalClientDashboard = is_array($professionalClientDashboard ?? null) ? $professionalClientDashboard : null;
    ?>

    <div x-data="clientShowPage({
            initialTab: <?php echo \Illuminate\Support\Js::from($initialTab)->toHtml() ?>,
            openMembershipModal: <?php echo \Illuminate\Support\Js::from($openMembershipModal)->toHtml() ?>,
            openAdjustmentModal: <?php echo \Illuminate\Support\Js::from($openAdjustmentModal)->toHtml() ?>,
            openRfidModal: <?php echo \Illuminate\Support\Js::from($openRfidModal)->toHtml() ?>,
            adjustmentMemberships: <?php echo \Illuminate\Support\Js::from($adjustmentMemberships)->toHtml() ?>,
            adjustmentTypeHelp: <?php echo \Illuminate\Support\Js::from($adjustmentTypeHelp)->toHtml() ?>,
            adjustmentReasonOptions: <?php echo \Illuminate\Support\Js::from($adjustmentReasonOptions)->toHtml() ?>,
            adjustmentReasonMap: <?php echo \Illuminate\Support\Js::from($adjustmentReasonMap)->toHtml() ?>,
            initialAdjustmentMembershipId: <?php echo \Illuminate\Support\Js::from($initialAdjustmentMembershipId ? (int) $initialAdjustmentMembershipId : null)->toHtml() ?>,
            membershipDefaults: <?php echo \Illuminate\Support\Js::from([
                'currentPlanId' => $professionalClientDashboard['current_plan_id'] ?? ($latestMembership?->plan_id !== null ? (int) $latestMembership->plan_id : null),
                'suggestedPromotionId' => $professionalClientDashboard['suggested_promotion_id'] ?? null,
            ])->toHtml() ?>,
            oldAdjustmentInput: <?php echo \Illuminate\Support\Js::from($oldAdjustmentInput)->toHtml() ?>,
            hasAdjustmentOldInput: <?php echo \Illuminate\Support\Js::from($hasAdjustmentOldInput)->toHtml() ?>,
         })"
         x-init="init()"
         class="client-detail-shell space-y-4 sm:space-y-6">

        <?php echo $__env->make('clients.partials._header', [
            'client' => $client,
            'photoUrl' => $photoUrl,
            'membershipBadgeVariant' => $membershipBadgeVariant,
            'membershipBadgeText' => $membershipBadgeText,
            'membershipLabel' => $membershipLabel,
            'membershipDateLabel' => $membershipDateLabel,
            'membershipDateValue' => $membershipDateValue,
            'membershipCountdownLabel' => $membershipCountdownLabel,
            'membershipCountdownValue' => $membershipCountdownValue,
            'lastAttendanceLabel' => $lastAttendanceLabel,
            'canAdjustMemberships' => $canAdjustMemberships,
            'latestMembership' => $latestMembership,
            'progressTabUrl' => $progressTabUrl,
            'canShowProgress' => $canShowProgress,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if($professionalClientDashboard): ?>
            <?php
                $professionalClientAlerts = collect($professionalClientDashboard['alerts'] ?? [])->values();
            ?>
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Cliente: foco comercial','subtitle' => 'Renovacion, promo y ticket extra dentro del plan Profesional.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cliente: foco comercial','subtitle' => 'Renovacion, promo y ticket extra dentro del plan Profesional.']); ?>
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_320px]">
                    <div>
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="max-w-3xl">
                                <p class="text-sm font-black text-slate-900 dark:text-slate-100"><?php echo e($professionalClientDashboard['headline'] ?? 'Foco comercial del cliente'); ?></p>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300"><?php echo e($professionalClientDashboard['summary'] ?? 'Usa esta capa para cobrar, renovar y mover promo desde la ficha.'); ?></p>
                            </div>
                            <span class="inline-flex rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-cyan-800 dark:border-cyan-400/40 dark:bg-cyan-500/15 dark:text-cyan-100">
                                Profesional
                            </span>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-400/40 dark:bg-emerald-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-200">Ultimo cobro</p>
                                <p class="mt-2 text-2xl font-black text-emerald-800 dark:text-emerald-100"><?php echo e(\App\Support\Currency::format((float) ($professionalClientDashboard['last_payment_amount'] ?? 0), $appCurrencyCode)); ?></p>
                                <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-200"><?php echo e($professionalClientDashboard['last_payment_label'] ?? 'Sin cobro'); ?></p>
                            </article>

                            <article class="rounded-xl border border-cyan-200 bg-cyan-50 p-3 dark:border-cyan-400/40 dark:bg-cyan-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-200">Facturacion membresias</p>
                                <p class="mt-2 text-2xl font-black text-cyan-800 dark:text-cyan-100"><?php echo e(\App\Support\Currency::format((float) ($professionalClientDashboard['total_membership_revenue'] ?? 0), $appCurrencyCode)); ?></p>
                                <p class="mt-1 text-xs text-cyan-700 dark:text-cyan-200">Historial acumulado de pagos de membresia.</p>
                            </article>

                            <article class="rounded-xl border border-violet-200 bg-violet-50 p-3 dark:border-violet-400/40 dark:bg-violet-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-violet-700 dark:text-violet-200">Promo</p>
                                <p class="mt-2 text-base font-black text-violet-800 dark:text-violet-100"><?php echo e($professionalClientDashboard['promotion_title'] ?? 'Sin promo'); ?></p>
                                <p class="mt-1 text-xs text-violet-700 dark:text-violet-200"><?php echo e($professionalClientDashboard['promotion_subtitle'] ?? ''); ?></p>
                            </article>

                            <article class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-400/40 dark:bg-amber-500/15">
                                <p class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-200">Productos</p>
                                <p class="mt-2 text-2xl font-black text-amber-800 dark:text-amber-100"><?php echo e(\App\Support\Currency::format((float) ($professionalClientDashboard['product_sales_revenue'] ?? 0), $appCurrencyCode)); ?></p>
                                <p class="mt-1 text-xs text-amber-700 dark:text-amber-200"><?php echo e((int) ($professionalClientDashboard['product_sales_count'] ?? 0)); ?> ticket(s) | <?php echo e($professionalClientDashboard['last_product_sale_label'] ?? 'Sin ventas'); ?></p>
                            </article>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/75">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-300">Lectura rapida</p>
                            <p class="mt-2 text-xs font-semibold text-slate-700 dark:text-slate-200"><?php echo e($professionalClientDashboard['attendance_label'] ?? 'Sin asistencia registrada'); ?></p>
                            <div class="mt-3 space-y-2">
                                <?php $__currentLoopData = $professionalClientAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $alertTone = (string) ($alert['tone'] ?? 'info');
                                        $alertCardClass = match ($alertTone) {
                                            'warning' => 'border-amber-200 bg-amber-50 dark:border-amber-400/40 dark:bg-amber-500/15',
                                            'danger' => 'border-rose-200 bg-rose-50 dark:border-rose-400/40 dark:bg-rose-500/15',
                                            'success' => 'border-emerald-200 bg-emerald-50 dark:border-emerald-400/40 dark:bg-emerald-500/15',
                                            default => 'border-cyan-200 bg-cyan-50 dark:border-cyan-400/40 dark:bg-cyan-500/15',
                                        };
                                        $alertTextClass = match ($alertTone) {
                                            'warning' => 'text-amber-700 dark:text-amber-200',
                                            'danger' => 'text-rose-700 dark:text-rose-200',
                                            'success' => 'text-emerald-700 dark:text-emerald-200',
                                            default => 'text-cyan-700 dark:text-cyan-200',
                                        };
                                    ?>
                                    <article class="rounded-xl border p-3 <?php echo e($alertCardClass); ?>">
                                        <p class="text-xs font-bold uppercase tracking-wider <?php echo e($alertTextClass); ?>"><?php echo e($alert['title'] ?? 'Alerta'); ?></p>
                                        <p class="mt-1 text-xs <?php echo e($alertTextClass); ?>"><?php echo e($alert['description'] ?? ''); ?></p>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'primary','xOn:click' => 'openRenewalModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'primary','x-on:click' => 'openRenewalModal()']); ?>
                                <?php echo e(! empty($professionalClientDashboard['current_plan_id']) ? 'Renovar mismo plan' : 'Cobrar membresia'); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php if(! empty($professionalClientDashboard['suggested_promotion_id'])): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','xOn:click' => 'openPromotionRenewalModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','x-on:click' => 'openPromotionRenewalModal()']); ?>Renovar con promo <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php endif; ?>
                            <?php if(! empty($canUseSalesInventory)): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('sales.index', $moduleRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('sales.index', $moduleRouteParams)),'variant' => 'ghost']); ?>Registrar venta <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php endif; ?>
                            <?php if(! empty($canViewReports)): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('reports.client-earnings', $clientReportRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('reports.client-earnings', $clientReportRouteParams)),'variant' => 'ghost']); ?>Reporte del cliente <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php endif; ?>
                            <?php if(! empty($canManagePromotions)): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('plans.index', $moduleRouteParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('plans.index', $moduleRouteParams)),'variant' => 'ghost']); ?>Planes y promos <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php endif; ?>
                            <?php if(! empty($canShowProgress) && ! empty($progressTabUrl)): ?>
                                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $progressTabUrl,'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($progressTabUrl),'variant' => 'ghost']); ?>Ver progreso <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
        <?php endif; ?>

        <?php echo $__env->make('clients.partials._tabs', [
            'canShowProgress' => $canShowProgress,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <section x-cloak x-show="activeTab === 'summary'" x-transition.opacity class="client-tab-panel">
            <?php echo $__env->make('clients.partials._tab_summary', [
                'client' => $client,
                'photoUrl' => $photoUrl,
                'latestMembership' => $latestMembership,
                'membershipBadgeVariant' => $membershipBadgeVariant,
                'membershipBadgeText' => $membershipBadgeText,
                'membershipDateLabel' => $membershipDateLabel,
                'membershipDateValue' => $membershipDateValue,
                'membershipCountdownLabel' => $membershipCountdownLabel,
                'membershipCountdownValue' => $membershipCountdownValue,
                'membershipStartsLabel' => $membershipStartsLabel,
                'membershipEndsLabel' => $membershipEndsLabel,
                'remainingLabel' => $remainingLabel,
                'lastAttendanceLabel' => $lastAttendanceLabel,
                'attendancePreview' => $attendancePreview,
                'paymentsPreview' => $paymentsPreview,
                'methodLabels' => $methodLabels,
                'statusLabels' => $statusLabels,
                'canAdjustMemberships' => $canAdjustMemberships,
                'progressTabUrl' => $progressTabUrl,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

        <?php if(! empty($canShowProgress)): ?>
            <section x-cloak x-show="activeTab === 'progress'" x-transition.opacity class="client-tab-panel">
                <?php echo $__env->make('clients.partials._tab_progress', [
                    'client' => $client,
                    'progressOverview' => $progressOverview,
                    'canManageClientAccounts' => $canManageClientAccounts,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </section>
        <?php endif; ?>

        <section x-cloak x-show="activeTab === 'membership'" x-transition.opacity class="client-tab-panel">
            <?php echo $__env->make('clients.partials._tab_membership_payments', [
                'client' => $client,
                'latestMembership' => $latestMembership,
                'recentMembershipPayments' => $recentMembershipPayments,
                'membershipAdjustments' => $membershipAdjustments,
                'methodLabels' => $methodLabels,
                'statusLabels' => $statusLabels,
                'adjustmentTypeLabels' => $adjustmentTypeLabels,
                'adjustmentReasonLabels' => $adjustmentReasonLabels,
                'canAdjustMemberships' => $canAdjustMemberships,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

        <section x-cloak x-show="activeTab === 'attendance'" x-transition.opacity class="client-tab-panel">
            <?php echo $__env->make('clients.partials._tab_attendances', [
                'client' => $client,
                'attendanceMethodLabels' => $attendanceMethodLabels,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

        <section x-cloak x-show="activeTab === 'credentials'" x-transition.opacity class="client-tab-panel">
            <?php echo $__env->make('clients.partials._tab_credentials', [
                'client' => $client,
                'activeQrCredential' => $activeQrCredential,
                'activeQrSvg' => $activeQrSvg,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

        <?php if(! empty($canManageClientAccounts)): ?>
            <section x-cloak x-show="activeTab === 'app_access'" x-transition.opacity class="client-tab-panel">
                <?php echo $__env->make('clients.partials._tab_usuario_app', [
                    'client' => $client,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </section>
        <?php endif; ?>

        <?php echo $__env->make('clients.partials._modal_membership', [
            'client' => $client,
            'plans' => $plans,
            'promotions' => $promotions,
            'canManagePromotions' => $canManagePromotions ?? false,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(! empty($canAdjustMemberships)): ?>
            <?php echo $__env->make('clients.partials._modal_membership_adjustment', [
                'client' => $client,
                'adjustmentTypeLabels' => $adjustmentTypeLabels,
                'adjustmentReasonLabels' => $adjustmentReasonLabels,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        <?php echo $__env->make('clients.partials._modal_rfid', [
            'client' => $client,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('clients.partials._modal_confirm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        window.clientShowPage = function clientShowPage(config) {
            return {
                activeTab: config.initialTab || 'summary',
                actionsOpen: false,
                quickMoreOpen: false,
                membershipModalOpen: Boolean(config.openMembershipModal),
                adjustmentModalOpen: Boolean(config.openAdjustmentModal),
                rfidModalOpen: Boolean(config.openRfidModal),
                confirmOpen: false,
                confirmMessage: '',
                pendingDeactivateFormId: null,
                qrCopyFeedback: '',
                whatsappCopyFeedback: '',
                adjustmentMemberships: Array.isArray(config.adjustmentMemberships) ? config.adjustmentMemberships : [],
                adjustmentTypeHelp: config.adjustmentTypeHelp || {},
                adjustmentReasonOptions: Array.isArray(config.adjustmentReasonOptions) ? config.adjustmentReasonOptions : [],
                adjustmentReasonMap: config.adjustmentReasonMap || {},
                membershipDefaults: config.membershipDefaults || {},
                selectedAdjustmentMembershipId: config.initialAdjustmentMembershipId || null,
                hasAdjustmentOldInput: Boolean(config.hasAdjustmentOldInput),
                adjustmentForm: {
                    type: config.oldAdjustmentInput?.adjustmentType || 'reschedule_start',
                    reason: config.oldAdjustmentInput?.reason || 'payment_registered_late',
                    startsAt: config.oldAdjustmentInput?.startsAt || '',
                    endsAt: config.oldAdjustmentInput?.endsAt || '',
                    extraDays: config.oldAdjustmentInput?.extraDays || '',
                    notes: config.oldAdjustmentInput?.notes || '',
                },

                init() {
                    if (!this.selectedAdjustmentMembershipId && this.adjustmentMemberships.length > 0) {
                        this.selectedAdjustmentMembershipId = this.adjustmentMemberships[0].id;
                    }

                    if (this.adjustmentMemberships.length > 0) {
                        this.setSelectedAdjustmentMembership(
                            this.selectedAdjustmentMembershipId,
                            this.hasAdjustmentOldInput
                        );
                    }

                    this.syncAdjustmentReason();

                    if (this.membershipModalOpen) {
                        this.$nextTick(() => this.$refs.membershipPlanInput?.focus());
                    }

                    if (this.adjustmentModalOpen) {
                        this.$nextTick(() => this.$refs.adjustmentTypeInput?.focus());
                    }

                    if (this.rfidModalOpen) {
                        this.$nextTick(() => this.$refs.rfidValueInput?.focus());
                    }
                },

                setTab(tab) {
                    this.activeTab = tab;
                    this.actionsOpen = false;
                },

                openMembershipModal() {
                    this.membershipModalOpen = true;
                    this.actionsOpen = false;
                    this.activeTab = 'membership';
                    this.$nextTick(() => this.$refs.membershipPlanInput?.focus());
                },

                openRenewalModal() {
                    this.openMembershipModalWithDefaults({
                        planId: this.membershipDefaults.currentPlanId || null,
                        promotionId: '',
                    });
                },

                openPromotionRenewalModal() {
                    this.openMembershipModalWithDefaults({
                        planId: this.membershipDefaults.currentPlanId || null,
                        promotionId: this.membershipDefaults.suggestedPromotionId || '',
                    });
                },

                openMembershipModalWithDefaults(options = {}) {
                    this.membershipModalOpen = true;
                    this.actionsOpen = false;
                    this.activeTab = 'membership';
                    this.$nextTick(() => {
                        if (Object.prototype.hasOwnProperty.call(options, 'planId') && options.planId && this.$refs.membershipPlanInput) {
                            this.$refs.membershipPlanInput.value = String(options.planId);
                        }

                        if (this.$refs.membershipPromotionInput) {
                            const promotionValue = Object.prototype.hasOwnProperty.call(options, 'promotionId')
                                ? String(options.promotionId || '')
                                : '';
                            this.$refs.membershipPromotionInput.value = promotionValue;
                        }

                        this.$refs.membershipPlanInput?.focus();
                    });
                },

                closeMembershipModal() {
                    this.membershipModalOpen = false;
                },

                openMembershipAdjustmentModal(membershipId = null) {
                    if (membershipId !== null) {
                        this.setSelectedAdjustmentMembership(membershipId, false);
                    } else if (!this.selectedAdjustmentMembershipId && this.adjustmentMemberships.length > 0) {
                        this.setSelectedAdjustmentMembership(this.adjustmentMemberships[0].id, false);
                    }

                    this.adjustmentModalOpen = true;
                    this.actionsOpen = false;
                    this.activeTab = 'membership';
                    this.syncAdjustmentReason();
                    this.$nextTick(() => this.$refs.adjustmentTypeInput?.focus());
                },

                closeMembershipAdjustmentModal() {
                    this.adjustmentModalOpen = false;
                },

                setSelectedAdjustmentMembership(membershipId, preserveValues = false) {
                    if (membershipId === null || membershipId === undefined || membershipId === '') {
                        return;
                    }

                    const membership = this.adjustmentMemberships.find((item) => String(item.id) === String(membershipId));
                    if (!membership) {
                        return;
                    }

                    this.selectedAdjustmentMembershipId = membership.id;

                    if (!preserveValues) {
                        this.adjustmentForm.startsAt = membership.startsAt || '';
                        this.adjustmentForm.endsAt = membership.endsAt || '';
                        this.adjustmentForm.extraDays = '';
                        this.adjustmentForm.notes = '';
                        this.adjustmentForm.type = 'reschedule_start';
                    } else if (!this.adjustmentForm.startsAt) {
                        this.adjustmentForm.startsAt = membership.startsAt || '';
                    }

                    this.syncAdjustmentReason();
                },

                selectedAdjustmentMembership() {
                    return this.adjustmentMemberships.find((item) => String(item.id) === String(this.selectedAdjustmentMembershipId)) || null;
                },

                adjustmentFormAction() {
                    return this.selectedAdjustmentMembership()?.adjustUrl || '#';
                },

                allowedAdjustmentReasons() {
                    const type = String(this.adjustmentForm.type || '').trim();
                    const allowedValues = Array.isArray(this.adjustmentReasonMap[type]) ? this.adjustmentReasonMap[type] : [];

                    return this.adjustmentReasonOptions.filter((option) => allowedValues.includes(option.value));
                },

                currentAdjustmentTypeHelp() {
                    const type = String(this.adjustmentForm.type || '').trim();
                    return String(this.adjustmentTypeHelp[type] || '');
                },

                currentAdjustmentReasonHelp() {
                    const reason = String(this.adjustmentForm.reason || '').trim();
                    const option = this.adjustmentReasonOptions.find((item) => item.value === reason);

                    return option?.help || '';
                },

                syncAdjustmentReason() {
                    const allowedReasons = this.allowedAdjustmentReasons();
                    const currentReason = String(this.adjustmentForm.reason || '').trim();
                    if (allowedReasons.length === 0) {
                        this.adjustmentForm.reason = '';
                        return;
                    }

                    if (!allowedReasons.some((option) => option.value === currentReason)) {
                        this.adjustmentForm.reason = allowedReasons[0].value;
                    }
                },

                adjustmentPreview() {
                    const membership = this.selectedAdjustmentMembership();
                    if (!membership) {
                        return {
                            startsAt: '',
                            endsAt: '',
                            statusLabel: 'Sin membresía seleccionada',
                            deltaLabel: 'Sin cambios',
                            planLabel: 'Selecciona una membresía para continuar.',
                        };
                    }

                    let startsAt = membership.startsAt || '';
                    let endsAt = membership.endsAt || '';

                    if (this.adjustmentForm.type === 'reschedule_start') {
                        startsAt = this.adjustmentForm.startsAt || startsAt;
                        endsAt = startsAt ? this.calculatePlanEnd(startsAt, membership) : endsAt;
                    } else if (this.adjustmentForm.type === 'extend_access') {
                        const extraDays = Math.max(0, Number(this.adjustmentForm.extraDays || 0));
                        endsAt = this.addDaysToInput(endsAt, extraDays);
                    } else if (this.adjustmentForm.type === 'manual_window') {
                        startsAt = this.adjustmentForm.startsAt || startsAt;
                        endsAt = this.adjustmentForm.endsAt || endsAt;
                    }

                    return {
                        startsAt,
                        endsAt,
                        statusLabel: this.resolveWindowStatus(startsAt, endsAt, membership.status),
                        deltaLabel: this.buildDaysDeltaLabel(membership.endsAt || '', endsAt),
                        planLabel: membership.planName || 'Sin plan',
                    };
                },

                calculatePlanEnd(startsAt, membership) {
                    const startDate = this.parseDate(startsAt);
                    if (!startDate) {
                        return '';
                    }

                    const bonusDays = Math.max(0, Number(membership.bonusDays || 0));
                    if ((membership.durationUnit || 'days') === 'months') {
                        const months = Math.max(1, Number(membership.durationMonths || 1));
                        const endDate = this.addMonthsNoOverflow(startDate, months);
                        if (bonusDays > 0) {
                            endDate.setDate(endDate.getDate() + bonusDays);
                        }

                        return this.formatDateInput(endDate);
                    }

                    const durationDays = Math.max(1, Number(membership.durationDays || 1));
                    const totalDays = durationDays + bonusDays - 1;
                    const endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
                    endDate.setDate(endDate.getDate() + totalDays);

                    return this.formatDateInput(endDate);
                },

                resolveWindowStatus(startsAt, endsAt, rawStatus) {
                    if (rawStatus === 'cancelled') {
                        return 'Cancelada';
                    }

                    const today = this.todayInput();
                    if (startsAt && startsAt > today) {
                        return 'Programada';
                    }

                    if (!endsAt || endsAt < today) {
                        return 'Vencida';
                    }

                    return 'Vigente';
                },

                buildDaysDeltaLabel(previousEndsAt, nextEndsAt) {
                    const previous = this.parseDate(previousEndsAt);
                    const next = this.parseDate(nextEndsAt);
                    if (!previous || !next) {
                        return 'Sin cambios';
                    }

                    const millisecondsPerDay = 24 * 60 * 60 * 1000;
                    const delta = Math.round((next.getTime() - previous.getTime()) / millisecondsPerDay);
                    if (delta === 0) {
                        return 'Sin cambio neto';
                    }

                    return delta > 0 ? `+${delta} días` : `${delta} días`;
                },

                addDaysToInput(value, days) {
                    const baseDate = this.parseDate(value);
                    if (!baseDate) {
                        return '';
                    }

                    const nextDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate());
                    nextDate.setDate(nextDate.getDate() + days);

                    return this.formatDateInput(nextDate);
                },

                addMonthsNoOverflow(date, months) {
                    const baseYear = date.getFullYear();
                    const baseMonth = date.getMonth();
                    const baseDay = date.getDate();
                    const targetMonthIndex = baseMonth + months;
                    const targetYear = baseYear + Math.floor(targetMonthIndex / 12);
                    const targetMonth = ((targetMonthIndex % 12) + 12) % 12;
                    const lastDayOfTargetMonth = new Date(targetYear, targetMonth + 1, 0).getDate();
                    const safeDay = Math.min(baseDay, lastDayOfTargetMonth);

                    return new Date(targetYear, targetMonth, safeDay);
                },

                parseDate(value) {
                    const raw = String(value || '').trim();
                    if (!/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
                        return null;
                    }

                    const [year, month, day] = raw.split('-').map(Number);
                    return new Date(year, month - 1, day);
                },

                formatDateInput(date) {
                    if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
                        return '';
                    }

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
                },

                formatDateLabel(value) {
                    const parsed = this.parseDate(value);
                    if (!parsed) {
                        return '-';
                    }

                    return parsed.toLocaleDateString('es-EC', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    });
                },

                todayInput() {
                    return this.formatDateInput(new Date());
                },

                openRfidModal() {
                    this.rfidModalOpen = true;
                    this.actionsOpen = false;
                    this.$nextTick(() => this.$refs.rfidValueInput?.focus());
                },

                closeRfidModal() {
                    this.rfidModalOpen = false;
                },

                requestDeactivate(formId, label) {
                    this.pendingDeactivateFormId = formId;
                    this.confirmMessage = 'Se desactivará ' + label + '. Esta acción no se puede deshacer desde esta pantalla.';
                    this.confirmOpen = true;
                },

                closeConfirm() {
                    this.confirmOpen = false;
                    this.pendingDeactivateFormId = null;
                },

                confirmDeactivate() {
                    if (!this.pendingDeactivateFormId) {
                        return;
                    }

                    const form = document.getElementById(this.pendingDeactivateFormId);
                    if (form) {
                        form.submit();
                    }
                },

                async copyQr(value) {
                    if (!value) {
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(value);
                        this.qrCopyFeedback = 'Valor QR copiado.';
                    } catch (error) {
                        this.qrCopyFeedback = 'No se pudo copiar automáticamente.';
                    }
                },

                async copyWhatsappMessage(value) {
                    if (!value) {
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(value);
                        this.whatsappCopyFeedback = 'Mensaje de WhatsApp copiado.';
                    } catch (error) {
                        this.whatsappCopyFeedback = 'No se pudo copiar el mensaje automáticamente.';
                    }
                },
            };
        };
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/show.blade.php ENDPATH**/ ?>