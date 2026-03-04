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

        $lastAttendance = $client->attendances->first();
        $lastAttendanceLabel = 'Sin asistencia';
        if ($lastAttendance?->date) {
            $attDate = $lastAttendance->date->copy()->startOfDay();
            $attTimeRaw = trim((string) ($lastAttendance->time ?? ''));
            $attTime = $attTimeRaw !== '' ? mb_substr($attTimeRaw, 0, 5) : '--:--';
            if ($attDate->isSameDay(now())) {
                $lastAttendanceLabel = 'Hoy '.$attTime;
            } else {
                $daysAgo = $attDate->diffInDays(now()->startOfDay());
                if ($daysAgo <= 30) {
                    $lastAttendanceLabel = ($daysAgo === 1 ? 'Hace 1 día' : "Hace {$daysAgo} días").' '.$attTime;
                } else {
                    $lastAttendanceLabel = $attDate->translatedFormat('d M Y').' '.$attTime;
                }
            }
        }

        $daysLeft = null;
        if ($latestMembership?->ends_at) {
            $daysLeft = now()->startOfDay()->diffInDays($latestMembership->ends_at->copy()->startOfDay(), false);
        }

        $statusLabels = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'expired' => 'Vencido',
            'cancelled' => 'Cancelado',
        ];

        $membershipBadgeVariant = 'muted';
        $membershipBadgeText = 'Sin membresía';
        if ($latestMembership) {
            $isActiveMembership = $latestMembership->status === 'active' && $daysLeft !== null && $daysLeft >= 0;
            if ($isActiveMembership && $daysLeft <= 7) {
                $membershipBadgeVariant = 'warning';
                $membershipBadgeText = 'Por vencer';
            } elseif ($isActiveMembership) {
                $membershipBadgeVariant = 'success';
                $membershipBadgeText = 'Vigente';
            } else {
                $membershipBadgeVariant = 'danger';
                $membershipBadgeText = 'Vencida';
            }
        }

        $membershipLabel = $membershipBadgeText;
        $membershipEndsLabel = $latestMembership?->ends_at?->translatedFormat('d M Y') ?? 'N/A';
        $remainingLabel = $daysLeft === null ? 'N/A' : ($daysLeft >= 0 ? $daysLeft.' días' : 'Vencida');

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

        $membershipErrorKeys = ['plan_id', 'starts_at', 'status', 'payment_method', 'promotion_id', 'cash'];
        $rfidErrorKeys = ['rfid', 'value'];
        $appAccountErrorKeys = ['app_username', 'app_password', 'app_password_confirmation'];

        $openMembershipModal = $errors->hasAny($membershipErrorKeys);
        $openRfidModal = $errors->hasAny($rfidErrorKeys);
        $openAppAccountTab = ! empty($canManageClientAccounts) && (
            $errors->hasAny($appAccountErrorKeys) || old('active_tab') === 'app_access'
        );

        $initialTab = 'summary';
        $requestedTab = trim((string) old('active_tab', ''));
        $allowedTabs = ['summary', 'membership', 'attendance', 'credentials'];
        if (! empty($canManageClientAccounts)) {
            $allowedTabs[] = 'app_access';
        }
        if (in_array($requestedTab, $allowedTabs, true)) {
            $initialTab = $requestedTab;
        }

        if ($openMembershipModal) {
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
    @endphp

    <div x-data="clientShowPage({
            initialTab: @js($initialTab),
            openMembershipModal: @js($openMembershipModal),
            openRfidModal: @js($openRfidModal),
        })"
         x-init="init()"
         class="space-y-6">

        @include('clients.partials._header', [
            'client' => $client,
            'photoUrl' => $photoUrl,
            'membershipBadgeVariant' => $membershipBadgeVariant,
            'membershipBadgeText' => $membershipBadgeText,
            'membershipLabel' => $membershipLabel,
            'membershipEndsLabel' => $membershipEndsLabel,
            'remainingLabel' => $remainingLabel,
            'lastAttendanceLabel' => $lastAttendanceLabel,
        ])

        @include('clients.partials._tabs')

        <section x-cloak x-show="activeTab === 'summary'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_summary', [
                'client' => $client,
                'photoUrl' => $photoUrl,
                'latestMembership' => $latestMembership,
                'membershipBadgeVariant' => $membershipBadgeVariant,
                'membershipBadgeText' => $membershipBadgeText,
                'membershipEndsLabel' => $membershipEndsLabel,
                'remainingLabel' => $remainingLabel,
                'lastAttendanceLabel' => $lastAttendanceLabel,
                'attendancePreview' => $attendancePreview,
                'paymentsPreview' => $paymentsPreview,
                'methodLabels' => $methodLabels,
                'statusLabels' => $statusLabels,
            ])
        </section>

        <section x-cloak x-show="activeTab === 'membership'" x-transition.opacity class="client-tab-panel">
            @include('clients.partials._tab_membership_payments', [
                'client' => $client,
                'recentMembershipPayments' => $recentMembershipPayments,
                'methodLabels' => $methodLabels,
                'statusLabels' => $statusLabels,
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
                rfidModalOpen: Boolean(config.openRfidModal),
                confirmOpen: false,
                confirmMessage: '',
                pendingDeactivateFormId: null,
                qrCopyFeedback: '',
                whatsappCopyFeedback: '',

                init() {
                    if (this.membershipModalOpen) {
                        this.$nextTick(() => this.$refs.membershipPlanInput?.focus());
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
                        this.whatsappCopyFeedback = 'No se pudo copiar el mensaje automaticamente.';
                    }
                },
            };
        };
    </script>
@endpush
