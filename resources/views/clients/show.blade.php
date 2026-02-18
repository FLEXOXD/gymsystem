@extends('layouts.panel')

@section('title', 'Cliente #'.$client->id)
@section('page-title', 'Cliente: '.$client->full_name)

@section('content')
    @php
        $membershipVariant = match ($membershipState) {
            'active' => 'success',
            'expired' => 'danger',
            default => 'muted',
        };
        $membershipLabel = match ($membershipState) {
            'active' => 'Vigente',
            'expired' => 'Vencida',
            default => 'Sin membresia',
        };
        $activeCredentials = $client->credentials->where('status', 'active');
        $photoUrl = null;
        if ($client->photo_path) {
            $photoUrl = str_starts_with($client->photo_path, 'http://')
                || str_starts_with($client->photo_path, 'https://')
                ? $client->photo_path
                : asset($client->photo_path);
        }
        $daysLeft = null;
        if ($latestMembership?->ends_at) {
            $daysLeft = now()->startOfDay()->diffInDays($latestMembership->ends_at->copy()->startOfDay(), false);
        }
    @endphp

    <x-ui.card>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="ui-heading text-2xl">{{ $client->full_name }}</h2>
                <p class="ui-muted mt-1 text-sm">Documento: {{ $client->document_number }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <x-ui.badge :variant="$client->status === 'active' ? 'success' : 'danger'">{{ $client->status }}</x-ui.badge>
                    <x-ui.badge :variant="$membershipVariant">{{ $membershipLabel }}</x-ui.badge>
                    @if ($daysLeft !== null)
                        <x-ui.badge :variant="$daysLeft >= 0 ? 'info' : 'danger'">{{ $daysLeft >= 0 ? $daysLeft.' dias restantes' : 'Vencida' }}</x-ui.badge>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-ui.button href="#membership-form" variant="success" size="sm">Cobrar/Renovar</x-ui.button>
                <form method="POST" action="{{ route('client-credentials.generate-qr', $client->id) }}">
                    @csrf
                    <x-ui.button type="submit" size="sm" variant="secondary">Generar QR</x-ui.button>
                </form>
                <x-ui.button type="button" id="open-rfid-modal-header" size="sm">Asignar RFID</x-ui.button>
                <x-ui.button :href="route('clients.card', $client->id)" target="_blank" rel="noopener" variant="ghost" size="sm">Imprimir tarjeta</x-ui.button>
                <x-ui.button :href="route('reception.index')" variant="ghost" size="sm">Ir a recepcion</x-ui.button>
            </div>
        </div>
    </x-ui.card>

    <section class="grid gap-4 xl:grid-cols-3">
        <x-ui.card title="Estado de membresia" class="xl:col-span-1">
            <div class="space-y-3 text-sm ui-text">
                <x-ui.badge :variant="$membershipVariant">{{ $membershipLabel }}</x-ui.badge>

                <p>
                    <span class="font-semibold text-slate-900">Plan:</span>
                    {{ $latestMembership?->plan?->name ?? 'Sin plan activo' }}
                </p>
                <p>
                    <span class="font-semibold text-slate-900">Fin:</span>
                    {{ $latestMembership?->ends_at?->toDateString() ?? '-' }}
                </p>

                <x-ui.button href="#membership-form" variant="success" class="w-full">Cobrar / Renovar</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card title="Credenciales" class="xl:col-span-1">
            <div class="space-y-3 text-sm ui-text">
                <p>
                    <span class="font-semibold text-slate-900">Activas:</span>
                    {{ $activeCredentials->count() }}
                </p>
                <p>
                    <span class="font-semibold text-slate-900">RFID activas:</span>
                    {{ $activeCredentials->where('type', 'rfid')->count() }}
                </p>
                <p>
                    <span class="font-semibold text-slate-900">QR activas:</span>
                    {{ $activeCredentials->where('type', 'qr')->count() }}
                </p>

                <div class="flex flex-wrap gap-2">
                    <x-ui.button type="button" id="open-rfid-modal" size="sm">Asignar RFID</x-ui.button>
                    <form method="POST" action="{{ route('client-credentials.generate-qr', $client->id) }}">
                        @csrf
                        <x-ui.button type="submit" size="sm" variant="secondary">Generar QR</x-ui.button>
                    </form>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Accesos rapidos" class="xl:col-span-1">
            <div class="grid gap-2">
                <x-ui.button :href="route('reception.index')" variant="secondary" class="w-full justify-start">Ir a recepcion</x-ui.button>
                <x-ui.button href="#attendance-section" variant="ghost" class="w-full justify-start">Ver asistencias del cliente</x-ui.button>
                <x-ui.button href="#payments-section" variant="ghost" class="w-full justify-start">Ver pagos relacionados</x-ui.button>
                <x-ui.button :href="route('cash.index')" variant="ghost" class="w-full justify-start">Ir a caja</x-ui.button>
            </div>
        </x-ui.card>
    </section>

    <x-ui.card title="Perfil del cliente" subtitle="Informacion base para recepcion y control.">
        <div class="grid gap-4 md:grid-cols-[140px_1fr] md:items-center">
            <div class="h-32 w-32 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Foto cliente" class="h-full w-full object-cover"
                         onerror="this.style.display='none'; this.parentElement.classList.add('flex','items-center','justify-center'); this.parentElement.innerHTML='<span class=\'text-xs text-slate-400\'>Sin foto</span>'">
                @else
                    <div class="flex h-full w-full items-center justify-center text-xs text-slate-400">Sin foto</div>
                @endif
            </div>
            <div class="grid gap-2 text-sm ui-text md:grid-cols-2">
                <p><span class="font-semibold text-slate-900">Nombre:</span> {{ $client->full_name }}</p>
                <p><span class="font-semibold text-slate-900">Documento:</span> {{ $client->document_number }}</p>
                <p><span class="font-semibold text-slate-900">Estado:</span> {{ $client->status }}</p>
                <p><span class="font-semibold text-slate-900">Telefono:</span> {{ $client->phone ?: '-' }}</p>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card title="Credenciales" subtitle="Gestion de QR/RFID con acciones directas.">
        @if (session('generated_qr_value'))
            <x-ui.alert type="success" class="mb-4">
                QR generado: <span class="font-mono text-xs">{{ session('generated_qr_value') }}</span>
            </x-ui.alert>
        @endif

        @if ($activeQrCredential)
            <div class="mb-5 grid gap-4 rounded-xl border border-cyan-100 bg-cyan-50 p-4 md:grid-cols-[220px_1fr] md:items-center">
                <div class="rounded-lg border border-slate-200 bg-white p-3 text-center">
                    {!! $activeQrSvg !!}
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-semibold ui-text">QR activo para acceso</p>
                    <p id="active-qr-value" class="break-all rounded-lg border border-cyan-200 bg-white p-2 font-mono text-xs ui-text">{{ $activeQrCredential->value }}</p>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button type="button" id="copy-qr-btn" data-copy-text="{{ $activeQrCredential->value }}" variant="muted" size="sm">Copiar valor</x-ui.button>
                        <x-ui.button :href="route('clients.card', $client->id)" target="_blank" rel="noopener" variant="secondary" size="sm">Imprimir tarjeta</x-ui.button>
                        <x-ui.button :href="route('clients.card.pdf', $client->id)" target="_blank" rel="noopener" variant="primary" size="sm">Exportar PDF</x-ui.button>
                    </div>
                    <small id="copy-feedback" class="block text-xs text-cyan-700"></small>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[860px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Tipo</th>
                    <th class="px-3 py-3">Valor</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Creado</th>
                    <th class="px-3 py-3">Accion</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($client->credentials as $credential)
                    @php
                        $displayValue = $credential->value;
                        if ($credential->type === 'rfid' && strlen($credential->value) > 6) {
                            $displayValue = str_repeat('*', 6).' '.substr($credential->value, -6);
                        }
                    @endphp
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $credential->id }}</td>
                        <td class="px-3 py-3 font-semibold">{{ strtoupper($credential->type) }}</td>
                        <td class="px-3 py-3 font-mono text-xs">{{ $displayValue }}</td>
                        <td class="px-3 py-3">
                            <x-ui.badge :variant="$credential->status === 'active' ? 'success' : 'muted'">{{ $credential->status }}</x-ui.badge>
                        </td>
                        <td class="px-3 py-3">{{ $credential->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3">
                            @if ($credential->status === 'active')
                                <form method="POST"
                                      action="{{ route('client-credentials.deactivate', $credential->id) }}"
                                      class="js-deactivate-credential inline"
                                      data-label="{{ strtoupper($credential->type) }} #{{ $credential->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-ui.button type="submit" variant="danger" size="sm">Desactivar</x-ui.button>
                                </form>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-sm text-slate-500">Sin credenciales registradas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card id="membership-form" title="Cobrar / Renovar membresia" subtitle="Crea membresia y registra cobro en caja automaticamente.">
        <form method="POST" action="{{ route('memberships.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Plan</span>
                    <select name="plan_id" required class="ui-input">
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">
                                {{ $plan->name }} ({{ $plan->duration_days }} dias, ${{ number_format((float) $plan->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Inicio</span>
                    <input type="date" name="starts_at" value="{{ now()->toDateString() }}" required class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Estado</span>
                    <select name="status" class="ui-input">
                        <option value="active">active</option>
                        <option value="expired">expired</option>
                        <option value="cancelled">cancelled</option>
                    </select>
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Metodo de pago</span>
                    <select name="payment_method" required class="ui-input">
                        <option value="">Seleccione</option>
                        <option value="cash" @selected(old('payment_method') === 'cash')>cash</option>
                        <option value="card" @selected(old('payment_method') === 'card')>card</option>
                        <option value="transfer" @selected(old('payment_method') === 'transfer')>transfer</option>
                    </select>
                </label>
            </div>

            <x-ui.button type="submit" variant="success">Cobrar y guardar membresia</x-ui.button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="ui-table min-w-[760px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Inicio</th>
                    <th class="px-3 py-3">Fin</th>
                    <th class="px-3 py-3">Estado</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($client->memberships as $membership)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $membership->id }}</td>
                        <td class="px-3 py-3">{{ $membership->plan?->name ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $membership->starts_at?->toDateString() ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $membership->ends_at?->toDateString() ?? '-' }}</td>
                        <td class="px-3 py-3">
                            @php
                                $membershipBadge = match ($membership->status) {
                                    'active' => 'success',
                                    'expired' => 'danger',
                                    default => 'muted',
                                };
                            @endphp
                            <x-ui.badge :variant="$membershipBadge">{{ $membership->status }}</x-ui.badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500">Sin membresias registradas.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card id="attendance-section" title="Asistencias recientes" subtitle="Ultimos ingresos del cliente.">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[720px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Hora</th>
                    <th class="px-3 py-3">Metodo</th>
                    <th class="px-3 py-3">Credencial</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($client->attendances as $attendance)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $attendance->date?->toDateString() ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $attendance->time ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $attendance->credential?->type ?? 'document' }}</td>
                        <td class="px-3 py-3 font-mono text-xs">{{ $attendance->credential?->value ? substr($attendance->credential->value, -10) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-sm text-slate-500">Sin asistencias recientes.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <x-ui.card id="payments-section" title="Pagos relacionados en caja" subtitle="Ultimos ingresos vinculados a membresias del cliente.">
        <div class="overflow-x-auto">
            <table class="ui-table min-w-[860px]">
                <thead>
                <tr class="border-b border-slate-200 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-3 py-3">Fecha</th>
                    <th class="px-3 py-3">Membresia</th>
                    <th class="px-3 py-3">Plan</th>
                    <th class="px-3 py-3">Metodo</th>
                    <th class="px-3 py-3">Monto</th>
                    <th class="px-3 py-3">Registrado por</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($recentMembershipPayments as $movement)
                    <tr class="border-b border-slate-100 text-sm">
                        <td class="px-3 py-3">{{ $movement->occurred_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-3 py-3">#{{ $movement->membership_id }}</td>
                        <td class="px-3 py-3">{{ $movement->membership?->plan?->name ?? '-' }}</td>
                        <td class="px-3 py-3">{{ $movement->method }}</td>
                        <td class="px-3 py-3 font-semibold text-emerald-700">${{ number_format((float) $movement->amount, 2) }}</td>
                        <td class="px-3 py-3">{{ $movement->createdBy?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-sm text-slate-500">Sin pagos relacionados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <div id="rfid-modal" class="ui-modal-backdrop hidden">
        <div class="ui-modal-panel">
            <h3 class="ui-heading text-lg">Asignar RFID</h3>
            <p class="ui-muted mt-1 text-sm">Pase la pulsera por el lector o pegue el UID.</p>

            <form method="POST" action="{{ route('client-credentials.store-rfid', $client->id) }}" id="rfid-form" class="mt-4 space-y-4">
                @csrf
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>UID RFID</span>
                    <input type="text" name="value" id="rfid-value" value="{{ old('value') }}" required
                           class="ui-input font-mono">
                </label>

                <div class="flex flex-wrap justify-end gap-2">
                    <x-ui.button type="button" id="close-rfid-modal" variant="muted">Cancelar</x-ui.button>
                    <x-ui.button type="submit" variant="success">Guardar RFID</x-ui.button>
                </div>
            </form>
        </div>
    </div>

    <div id="confirm-modal" class="ui-modal-backdrop hidden">
        <div class="ui-modal-panel max-w-md">
            <h3 class="ui-heading text-lg">Confirmar accion</h3>
            <p id="confirm-message" class="ui-text mt-2 text-sm">Desea continuar?</p>
            <div class="mt-4 flex justify-end gap-2">
                <x-ui.button type="button" id="confirm-cancel" variant="muted">Cancelar</x-ui.button>
                <x-ui.button type="button" id="confirm-accept" variant="danger">Desactivar</x-ui.button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const rfidModal = document.getElementById('rfid-modal');
        const openRfidBtn = document.getElementById('open-rfid-modal');
        const openRfidBtnHeader = document.getElementById('open-rfid-modal-header');
        const closeRfidBtn = document.getElementById('close-rfid-modal');
        const rfidInput = document.getElementById('rfid-value');

        const confirmModal = document.getElementById('confirm-modal');
        const confirmMessage = document.getElementById('confirm-message');
        const confirmAccept = document.getElementById('confirm-accept');
        const confirmCancel = document.getElementById('confirm-cancel');
        let pendingForm = null;

        const copyBtn = document.getElementById('copy-qr-btn');
        const copyFeedback = document.getElementById('copy-feedback');

        function showModal(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideModal(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        openRfidBtn?.addEventListener('click', function () {
            showModal(rfidModal);
            setTimeout(() => rfidInput?.focus(), 80);
        });
        openRfidBtnHeader?.addEventListener('click', function () {
            showModal(rfidModal);
            setTimeout(() => rfidInput?.focus(), 80);
        });

        closeRfidBtn?.addEventListener('click', function () {
            hideModal(rfidModal);
        });

        rfidModal?.addEventListener('click', function (event) {
            if (event.target === rfidModal) {
                hideModal(rfidModal);
            }
        });

        document.querySelectorAll('.js-deactivate-credential').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                pendingForm = form;
                const label = form.getAttribute('data-label') || 'credencial';
                confirmMessage.textContent = 'Se desactivara ' + label + '. Esta accion no se puede deshacer desde esta pantalla.';
                showModal(confirmModal);
            });
        });

        confirmCancel?.addEventListener('click', function () {
            hideModal(confirmModal);
            pendingForm = null;
        });

        confirmAccept?.addEventListener('click', function () {
            if (pendingForm) {
                pendingForm.submit();
            }
        });

        confirmModal?.addEventListener('click', function (event) {
            if (event.target === confirmModal) {
                hideModal(confirmModal);
                pendingForm = null;
            }
        });

        copyBtn?.addEventListener('click', async function () {
            const text = copyBtn.getAttribute('data-copy-text') || '';
            if (!text) return;

            try {
                await navigator.clipboard.writeText(text);
                if (copyFeedback) copyFeedback.textContent = 'Valor QR copiado.';
            } catch (error) {
                if (copyFeedback) copyFeedback.textContent = 'No se pudo copiar automaticamente.';
            }
        });

        @if ($errors->has('rfid') || $errors->has('value'))
            showModal(rfidModal);
            setTimeout(() => rfidInput?.focus(), 80);
        @endif
    })();
</script>
@endpush
