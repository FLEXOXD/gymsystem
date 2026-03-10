@extends('layouts.panel')

@section('title', 'Cliente #'.$client->id)
@section('page-title', 'Cliente: '.$client->full_name)

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
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
    </style>
@endpush

@section('content')
    @php
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

        $initialTab = 'summary';
        $allowedTabs = ['summary', 'progress', 'membership', 'attendance', 'credentials'];
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
    @endphp

    <div x-data="clientShowPage({
            initialTab: @js($initialTab),
            openMembershipModal: @js($openMembershipModal),
            openAdjustmentModal: @js($openAdjustmentModal),
            openRfidModal: @js($openRfidModal),
            adjustmentMemberships: @js($adjustmentMemberships),
            adjustmentTypeHelp: @js($adjustmentTypeHelp),
            adjustmentReasonOptions: @js($adjustmentReasonOptions),
            adjustmentReasonMap: @js($adjustmentReasonMap),
            initialAdjustmentMembershipId: @js($initialAdjustmentMembershipId ? (int) $initialAdjustmentMembershipId : null),
            oldAdjustmentInput: @js($oldAdjustmentInput),
            hasAdjustmentOldInput: @js($hasAdjustmentOldInput),
        })"
         x-init="init()"
         class="space-y-6">

        @include('clients.partials._header', [
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
        ])

        @include('clients.partials._tabs')

        <section x-cloak x-show="activeTab === 'summary'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_summary', [
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
            ])
        </section>

        <section x-cloak x-show="activeTab === 'progress'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_progress', [
                'client' => $client,
                'progressOverview' => $progressOverview,
            ])
        </section>

        <section x-cloak x-show="activeTab === 'membership'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_membership_payments', [
                'client' => $client,
                'latestMembership' => $latestMembership,
                'recentMembershipPayments' => $recentMembershipPayments,
                'membershipAdjustments' => $membershipAdjustments,
                'methodLabels' => $methodLabels,
                'statusLabels' => $statusLabels,
                'adjustmentTypeLabels' => $adjustmentTypeLabels,
                'adjustmentReasonLabels' => $adjustmentReasonLabels,
                'canAdjustMemberships' => $canAdjustMemberships,
            ])
        </section>

        <section x-cloak x-show="activeTab === 'attendance'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_attendances', [
                'client' => $client,
                'attendanceMethodLabels' => $attendanceMethodLabels,
            ])
        </section>

        <section x-cloak x-show="activeTab === 'credentials'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_credentials', [
                'client' => $client,
                'activeQrCredential' => $activeQrCredential,
                'activeQrSvg' => $activeQrSvg,
            ])
        </section>

        @if (! empty($canManageClientAccounts))
            <section x-cloak x-show="activeTab === 'app_access'" x-transition.opacity class="client-tab-panel">
                @include('clients.partials._tab_usuario_app', [
                    'client' => $client,
                ])
            </section>
        @endif

        @include('clients.partials._modal_membership', [
            'client' => $client,
            'plans' => $plans,
            'promotions' => $promotions,
            'canManagePromotions' => $canManagePromotions ?? false,
        ])

        @if (! empty($canAdjustMemberships))
            @include('clients.partials._modal_membership_adjustment', [
                'client' => $client,
                'adjustmentTypeLabels' => $adjustmentTypeLabels,
                'adjustmentReasonLabels' => $adjustmentReasonLabels,
            ])
        @endif

        @include('clients.partials._modal_rfid', [
            'client' => $client,
        ])

        @include('clients.partials._modal_confirm')
    </div>
@endsection

@push('scripts')
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
                    this.$nextTick(() => this.$refs.membershipPlanInput?.focus());
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
@endpush
