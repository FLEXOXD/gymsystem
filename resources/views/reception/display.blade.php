@php
    $user = auth()->user();
    $theme = $user?->theme ?? 'iron_dark';
    $darkThemes = ['iron_dark', 'power_red', 'energy_green', 'gold_elite'];
    $themeClass = in_array($theme, $darkThemes, true) ? 'dark theme-dark' : 'theme-light';
    $gymName = trim((string) ($syncGymName ?? ($user?->gym?->name ?? 'Gym')));
@endphp
<!DOCTYPE html>
<html lang="es" class="h-full antialiased {{ $themeClass }}" data-theme="{{ $theme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pantalla recepción - {{ config('app.name', 'GymSystem') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="theme-body min-h-screen ui-text">
<main class="w-full max-w-none space-y-4 px-3 py-3 md:px-6 md:py-6">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-700/40 bg-slate-900/40 px-4 py-3">
        <div>
            <p class="ui-muted text-xs font-bold uppercase tracking-widest">Pantalla espejo</p>
            <h1 class="ui-heading text-xl font-black md:text-2xl">Recepción - {{ $gymName }}</h1>
        </div>
        <span class="rounded-full border border-cyan-400/40 bg-cyan-500/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-cyan-200">Live</span>
    </header>

    <section class="grid gap-4 2xl:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
        <x-ui.card id="result-panel" class="relative border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900" title="Resultado en vivo">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-transparent to-amber-400/10"></div>
            <div class="relative grid gap-4 md:grid-cols-[minmax(220px,280px)_minmax(0,1fr)] md:items-start xl:grid-cols-[minmax(260px,320px)_minmax(0,1fr)] 2xl:grid-cols-[360px_minmax(0,1fr)]">
                <div class="w-full">
                    <div class="relative overflow-hidden rounded-[1.9rem] border border-slate-300/70 bg-slate-900/20 shadow-2xl dark:border-slate-700/80">
                        <img id="result-photo" src="" alt="Foto del cliente" class="hidden h-64 w-full object-cover object-top sm:h-72 md:h-[22rem] xl:h-[28rem] 2xl:h-[33rem]">
                        <div id="result-photo-placeholder" class="flex h-64 w-full flex-col items-center justify-center gap-2 bg-slate-50/80 text-sm font-medium text-slate-500 dark:bg-slate-800/70 dark:text-slate-300 sm:h-72 md:h-[22rem] xl:h-[28rem] 2xl:h-[33rem]">
                            <span id="result-avatar-initials" class="text-6xl font-black tracking-wider text-slate-400 dark:text-slate-300">--</span>
                            <span class="text-xs uppercase tracking-[0.22em]">Avatar gym</span>
                        </div>
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950/45 to-transparent"></div>
                    </div>
                </div>

                <div class="min-w-0 space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <p id="result-method" class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-slate-700 dark:bg-slate-700 dark:text-slate-100">Método: -</p>
                        <span id="result-days-pill" class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-100">Días restantes: -</span>
                        <span id="result-month-pill" class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-100">Visitas mes: -</span>
                    </div>

                    <p id="result-message" class="pr-1 text-[clamp(1.7rem,4.6vw,3.2rem)] font-black leading-tight break-words [overflow-wrap:anywhere] text-slate-800 dark:text-slate-100">Esperando lectura...</p>
                    <p id="result-name" class="text-[clamp(1.35rem,3.8vw,2.4rem)] font-bold leading-tight text-slate-900 dark:text-slate-100">-</p>

                    <div class="grid gap-3 [grid-template-columns:repeat(auto-fit,minmax(150px,1fr))]">
                        <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Fin membresía</p>
                            <p id="result-membership" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Fecha check-in</p>
                            <p id="result-checkin-date" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Hora check-in</p>
                            <p id="result-checkin-time" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                        </div>
                        <div class="rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/60">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-600 dark:text-slate-400">Visitas del mes</p>
                            <p id="result-month-visits" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">-</p>
                        </div>
                    </div>

                    <div id="result-motivation" class="rounded-xl border border-cyan-200 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-900 dark:border-cyan-700/60 dark:bg-cyan-900/20 dark:text-cyan-100">
                        Listo para recibir al cliente.
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card class="border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900" title="Últimos 10 ingresos">
            <div class="overflow-x-auto">
                <table class="ui-table min-w-[620px]">
                    <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Hora</th>
                        <th class="px-3 py-3">Cliente</th>
                        <th class="px-3 py-3">Método</th>
                    </tr>
                    </thead>
                    <tbody id="recent-attendances-body">
                    @forelse ($recentAttendances as $attendance)
                        @php
                            $attendanceMethod = $attendance->credential?->type ?? 'document';
                            $attendanceMethodLabel = match ($attendanceMethod) {
                                'rfid' => 'RFID',
                                'qr' => 'QR',
                                'document' => 'Documento',
                                default => strtoupper((string) $attendanceMethod),
                            };
                        @endphp
                        <tr data-role="recent-attendance-row" data-attendance-id="{{ (int) $attendance->id }}" class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                            <td class="px-3 py-3 dark:text-slate-200">{{ $attendance->date?->toDateString() ?? '-' }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $attendance->time ?? '-' }}</td>
                            <td class="px-3 py-3 font-semibold dark:text-slate-100">{{ $attendance->client?->full_name ?? '-' }}</td>
                            <td class="px-3 py-3 dark:text-slate-200">{{ $attendanceMethodLabel }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td id="recent-attendances-empty" colspan="4" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">Sin ingresos recientes.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </section>
</main>

<script>
    (function () {
        const AUTO_RESET_MS = 15000;
        const syncGymName = String(@json((string) $syncGymName));
        const syncGymId = Number(@json((int) $syncGymId));
        const gymAvatarUrls = @json($gymAvatarUrls);
        const syncPollUrl = @json(route('reception.sync.latest'));
        const latestSyncEventId = String(@json((string) ($latestSyncEventId ?? '')));
        const latestSyncEventPublishedAt = Number(@json((int) ($latestSyncEventPublishedAt ?? 0))) || 0;
        const SYNC_POLL_INTERVAL_MS = 2500;
        const syncChannelName = 'reception.checkin.' + String(syncGymId || 0);
        const syncStorageKey = syncChannelName + '.event';
        const syncSourceId = 'display-' + Math.random().toString(36).slice(2);
        const syncChannel = typeof BroadcastChannel !== 'undefined'
            ? new BroadcastChannel(syncChannelName)
            : null;

        const panel = document.getElementById('result-panel');
        const photo = document.getElementById('result-photo');
        const photoPlaceholder = document.getElementById('result-photo-placeholder');
        const method = document.getElementById('result-method');
        const message = document.getElementById('result-message');
        const name = document.getElementById('result-name');
        const membership = document.getElementById('result-membership');
        const daysPill = document.getElementById('result-days-pill');
        const monthPill = document.getElementById('result-month-pill');
        const checkinDate = document.getElementById('result-checkin-date');
        const checkinTime = document.getElementById('result-checkin-time');
        const monthVisitsValue = document.getElementById('result-month-visits');
        const motivation = document.getElementById('result-motivation');
        const avatarInitials = document.getElementById('result-avatar-initials');
        const recentAttendancesBody = document.getElementById('recent-attendances-body');
        const methodLabels = {
            rfid: 'RFID',
            qr: 'QR',
            document: 'Documento',
        };

        let resetTimer = null;
        let lastRenderedAttendanceId = readTopAttendanceId();
        let lastHandledSyncEventId = latestSyncEventId !== '' ? latestSyncEventId : null;
        let lastHandledSyncPublishedAt = latestSyncEventPublishedAt > 0 ? latestSyncEventPublishedAt : 0;
        let syncPollTimer = null;
        let syncPollInFlight = false;

        function basePanelClasses() {
            return 'relative overflow-hidden rounded-2xl border p-5 shadow-sm';
        }

        function readTopAttendanceId() {
            if (!recentAttendancesBody) return null;

            const row = recentAttendancesBody.querySelector('tr[data-role="recent-attendance-row"]');
            if (!row) return null;

            const parsed = Number(row.getAttribute('data-attendance-id'));

            return Number.isFinite(parsed) ? parsed : null;
        }

        function payloadAttendanceId(payload) {
            const parsed = Number(payload && payload.attendance ? payload.attendance.id : null);

            return Number.isFinite(parsed) ? parsed : null;
        }

        function payloadAttempt(payload) {
            if (!payload) return null;
            if (payload.attendance && payload.attendance.date && payload.attendance.time) {
                return {
                    date: String(payload.attendance.date),
                    time: String(payload.attendance.time),
                };
            }
            if (payload.attempt && payload.attempt.date && payload.attempt.time) {
                return {
                    date: String(payload.attempt.date),
                    time: String(payload.attempt.time),
                };
            }

            return null;
        }

        function safePayload(payload) {
            return {
                ok: Boolean(payload && payload.ok),
                reason: payload && payload.reason ? String(payload.reason) : null,
                message: payload && payload.message ? payload.message : 'Respuesta invalida del servidor.',
                method: payload && Object.prototype.hasOwnProperty.call(payload, 'method') ? payload.method : null,
                client: payload && Object.prototype.hasOwnProperty.call(payload, 'client') ? payload.client : null,
                attendance: payload && Object.prototype.hasOwnProperty.call(payload, 'attendance') ? payload.attendance : null,
                attempt: payload && Object.prototype.hasOwnProperty.call(payload, 'attempt') ? payload.attempt : null,
            };
        }

        function payloadReason(payload) {
            return payload && payload.reason ? String(payload.reason) : '';
        }

        function clientInitials(fullName) {
            const normalized = String(fullName || '').trim();
            if (normalized === '') return '--';

            const initials = normalized
                .split(/\s+/)
                .filter(Boolean)
                .slice(0, 2)
                .map(function (part) {
                    return part.charAt(0).toUpperCase();
                })
                .join('');

            return initials || '--';
        }

        function normalizeGender(rawGender) {
            const value = String(rawGender || '').trim().toLowerCase();

            return value === 'male' || value === 'female' ? value : 'neutral';
        }

        function avatarLabelByGender(gender) {
            if (gender === 'male') return 'HOMBRE';
            if (gender === 'female') return 'MUJER';

            return 'NEUTRAL';
        }

        function customAvatarByGender(gender) {
            const normalizedGender = normalizeGender(gender);
            if (!gymAvatarUrls || typeof gymAvatarUrls !== 'object') return '';
            const value = gymAvatarUrls[normalizedGender];
            const normalizedValue = typeof value === 'string' ? value.trim() : '';
            if (normalizedValue !== '') {
                return normalizedValue;
            }

            const neutralValue = gymAvatarUrls.neutral;

            return typeof neutralValue === 'string' ? neutralValue.trim() : '';
        }

        function generatedAvatarDataUrl(gender, fullName) {
            const normalizedGender = normalizeGender(gender);
            const initials = clientInitials(fullName);
            const gymLabel = String(syncGymName || 'GYM').toUpperCase();
            const genderLabel = avatarLabelByGender(normalizedGender);

            let gradientFrom = '#0f172a';
            let gradientTo = '#1d4ed8';
            let accent = '#22d3ee';

            if (normalizedGender === 'female') {
                gradientFrom = '#3b0a45';
                gradientTo = '#9d174d';
                accent = '#f472b6';
            } else if (normalizedGender === 'neutral') {
                gradientFrom = '#1f2937';
                gradientTo = '#0f766e';
                accent = '#34d399';
            }

            const escapedGym = gymLabel.replace(/&/g, '&amp;').replace(/</g, '&lt;');
            const escapedGender = genderLabel.replace(/&/g, '&amp;').replace(/</g, '&lt;');
            const escapedInitials = initials.replace(/&/g, '&amp;').replace(/</g, '&lt;');

            const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="640" height="760" viewBox="0 0 640 760">
                <defs>
                    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="${gradientFrom}"/>
                        <stop offset="100%" stop-color="${gradientTo}"/>
                    </linearGradient>
                </defs>
                <rect width="640" height="760" rx="36" fill="url(#bg)"/>
                <circle cx="320" cy="250" r="128" fill="rgba(255,255,255,0.08)"/>
                <rect x="180" y="360" width="280" height="260" rx="42" fill="rgba(255,255,255,0.08)"/>
                <text x="320" y="285" font-family="Arial, sans-serif" font-size="116" font-weight="900" text-anchor="middle" fill="${accent}">${escapedInitials}</text>
                <text x="320" y="668" font-family="Arial, sans-serif" font-size="36" font-weight="700" text-anchor="middle" fill="rgba(255,255,255,0.95)">${escapedGym}</text>
                <text x="320" y="712" font-family="Arial, sans-serif" font-size="28" font-weight="700" text-anchor="middle" fill="${accent}">${escapedGender}</text>
            </svg>`;

            return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
        }

        function showClientVisual(client) {
            const fullName = client && client.full_name ? client.full_name : '';
            avatarInitials.textContent = clientInitials(fullName);

            const directPhoto = client && client.photo_url ? String(client.photo_url) : '';
            const avatarPhoto = customAvatarByGender(client ? client.gender : 'neutral')
                || generatedAvatarDataUrl(client ? client.gender : 'neutral', fullName);
            const candidates = Array.from(new Set([directPhoto, avatarPhoto].filter(function (value) {
                return typeof value === 'string' && value.trim() !== '';
            })));
            let index = 0;

            const loadNextCandidate = function () {
                if (index >= candidates.length) {
                    photo.classList.add('hidden');
                    photoPlaceholder.classList.remove('hidden');
                    return;
                }

                photo.src = candidates[index];
                index += 1;
            };

            photo.onerror = function () {
                loadNextCandidate();
            };
            photo.onload = function () {
                photo.classList.remove('hidden');
                photoPlaceholder.classList.add('hidden');
            };

            loadNextCandidate();
        }

        function formatDateDisplay(dateValue) {
            if (!dateValue) return '-';
            const parsed = new Date(String(dateValue) + 'T00:00:00');
            if (Number.isNaN(parsed.getTime())) return String(dateValue);

            return parsed.toLocaleDateString('es-EC', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            });
        }

        function daysRemainingInfo(membershipDate) {
            if (!membershipDate) {
                return { label: 'Días restantes: -', tone: 'neutral', days: null };
            }

            const endDate = new Date(String(membershipDate) + 'T00:00:00');
            if (Number.isNaN(endDate.getTime())) {
                return { label: 'Días restantes: -', tone: 'neutral', days: null };
            }

            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const diffMs = endDate.getTime() - today.getTime();
            const days = Math.floor(diffMs / 86400000);

            if (days < 0) {
                return {
                    label: 'Membresía vencida',
                    tone: 'danger',
                    days: days,
                };
            }

            if (days < 5) {
                return {
                    label: 'Días restantes: ' + String(days) + ' días',
                    tone: 'danger',
                    days: days,
                };
            }

            if (days <= 10) {
                return {
                    label: 'Días restantes: ' + String(days) + ' días',
                    tone: 'warn',
                    days: days,
                };
            }

            return {
                label: 'Días restantes: ' + String(days) + ' días',
                tone: 'ok',
                days: days,
            };
        }

        function monthVisitsValueOf(payload) {
            const reason = payloadReason(payload);
            const sourceValue = reason === 'membership_inactive'
                ? (payload && payload.client ? payload.client.total_visits : NaN)
                : (payload && payload.client ? payload.client.month_visits : NaN);
            const value = Number(sourceValue);

            return Number.isFinite(value) ? Math.max(0, Math.floor(value)) : null;
        }

        function motivationText(payload, daysInfo, monthVisits) {
            const reason = payloadReason(payload);

            if (reason === 'duplicate_attendance') {
                return 'Bienvenido de vuelta, ya te registraste hoy. Sigue nomas.';
            }

            if (reason === 'membership_inactive') {
                return 'Membresía no vigente. Renueva para habilitar ingreso.';
            }

            if (!payload.ok) {
                return 'Reintenta con otro método o verifica estado del cliente.';
            }

            if (monthVisits !== null && monthVisits >= 20) {
                return 'Ritmo top: constancia excelente este mes.';
            }

            if (monthVisits !== null && monthVisits >= 12) {
                return 'Muy buen progreso mensual. Sigue asi.';
            }

            if (daysInfo.days !== null && daysInfo.days >= 0 && daysInfo.days <= 5) {
                return 'Renovación cercana: oportunidad perfecta para venta.';
            }

            return 'Buen trabajo. Check-in registrado y flujo estable.';
        }

        function checkinDisplay(payload) {
            const reason = payloadReason(payload);
            if (reason === 'membership_inactive') {
                const lastTime = payload && payload.client && payload.client.last_attendance_time
                    ? String(payload.client.last_attendance_time)
                    : '-';

                return {
                    date: '-',
                    time: lastTime,
                };
            }

            const attempt = payloadAttempt(payload);
            return {
                date: attempt ? formatDateDisplay(attempt.date) : '-',
                time: attempt ? String(attempt.time) : '-',
            };
        }

        function setPillTone(element, tone) {
            if (!element) return;

            if (tone === 'ok') {
                element.className = 'inline-flex rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200';
                return;
            }

            if (tone === 'warn') {
                element.className = 'inline-flex rounded-full bg-amber-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-amber-800 dark:bg-amber-900/30 dark:text-amber-200';
                return;
            }

            if (tone === 'danger') {
                element.className = 'inline-flex rounded-full bg-rose-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-rose-800 dark:bg-rose-900/30 dark:text-rose-200';
                return;
            }

            element.className = 'inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-700 dark:text-slate-100';
        }

        function buildAttendanceRowCell(text, className) {
            const td = document.createElement('td');
            td.className = className;
            td.textContent = text;

            return td;
        }

        function removeRecentAttendancesEmptyState() {
            const emptyCell = document.getElementById('recent-attendances-empty');
            if (!emptyCell) return;

            const emptyRow = emptyCell.closest('tr');
            if (emptyRow) {
                emptyRow.remove();
            }
        }

        function buildRecentAttendanceRow(id, dateValue, timeValue, clientName, methodText) {
            const row = document.createElement('tr');
            row.setAttribute('data-role', 'recent-attendance-row');
            if (id !== null) {
                row.setAttribute('data-attendance-id', String(id));
            }
            row.className = 'border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50';
            row.appendChild(buildAttendanceRowCell(dateValue, 'px-3 py-3 dark:text-slate-200'));
            row.appendChild(buildAttendanceRowCell(timeValue, 'px-3 py-3 dark:text-slate-200'));
            row.appendChild(buildAttendanceRowCell(clientName, 'px-3 py-3 font-semibold dark:text-slate-100'));
            row.appendChild(buildAttendanceRowCell(methodText, 'px-3 py-3 dark:text-slate-200'));

            return row;
        }

        function prependRecentAttendance(payload) {
            if (!payload.ok || !payload.attendance || !recentAttendancesBody) return;

            const attendanceId = payloadAttendanceId(payload);
            const methodText = payload.method ? (methodLabels[payload.method] || String(payload.method).toUpperCase()) : '-';
            const clientName = payload.client && payload.client.full_name ? payload.client.full_name : '-';
            const dateValue = payload.attendance.date ? String(payload.attendance.date) : '-';
            const timeValue = payload.attendance.time ? String(payload.attendance.time) : '-';

            if (attendanceId !== null) {
                const existing = recentAttendancesBody.querySelector(`tr[data-attendance-id="${attendanceId}"]`);
                if (existing) {
                    existing.remove();
                }
            }

            removeRecentAttendancesEmptyState();
            recentAttendancesBody.prepend(
                buildRecentAttendanceRow(attendanceId, dateValue, timeValue, clientName, methodText)
            );

            const rows = recentAttendancesBody.querySelectorAll('tr[data-role="recent-attendance-row"]');
            if (rows.length > 10) {
                for (let i = 10; i < rows.length; i += 1) {
                    rows[i].remove();
                }
            }
        }

        function render(payload) {
            const attendanceId = payloadAttendanceId(payload);
            const reason = payloadReason(payload);
            if (attendanceId !== null) {
                lastRenderedAttendanceId = attendanceId;
            }

            if (payload.ok) {
                panel.className = basePanelClasses() + ' border-emerald-300 bg-emerald-50 dark:border-emerald-700 dark:bg-emerald-900/30';
                method.className = 'inline-flex rounded-full bg-emerald-200 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-emerald-800 dark:bg-emerald-800/40 dark:text-emerald-200';
                message.className = 'pr-1 text-[clamp(1.7rem,4.6vw,3.2rem)] font-black leading-tight break-words [overflow-wrap:anywhere] text-emerald-700 dark:text-emerald-200';
            } else if (reason === 'duplicate_attendance') {
                panel.className = basePanelClasses() + ' border-amber-300 bg-amber-50 dark:border-amber-700 dark:bg-amber-900/25';
                method.className = 'inline-flex rounded-full bg-amber-200 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-amber-800 dark:bg-amber-800/40 dark:text-amber-200';
                message.className = 'pr-1 text-[clamp(1.7rem,4.6vw,3.2rem)] font-black leading-tight break-words [overflow-wrap:anywhere] text-amber-700 dark:text-amber-200';
            } else {
                panel.className = basePanelClasses() + ' border-rose-300 bg-rose-50 dark:border-rose-700 dark:bg-rose-900/30';
                method.className = 'inline-flex rounded-full bg-rose-200 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-rose-800 dark:bg-rose-800/40 dark:text-rose-200';
                message.className = 'pr-1 text-[clamp(1.7rem,4.6vw,3.2rem)] font-black leading-tight break-words [overflow-wrap:anywhere] text-rose-700 dark:text-rose-200';
            }

            message.textContent = payload.message;
            const methodText = payload.method ? (methodLabels[payload.method] || String(payload.method).toUpperCase()) : '-';
            method.textContent = 'Método: ' + methodText;

            const membershipDate = payload.client && payload.client.membership_ends_at
                ? String(payload.client.membership_ends_at)
                : null;
            const daysInfo = daysRemainingInfo(membershipDate);
            setPillTone(daysPill, daysInfo.tone);
            daysPill.textContent = daysInfo.label;

            const monthVisits = monthVisitsValueOf(payload);
            monthPill.textContent = 'Visitas mes: ' + (monthVisits !== null ? String(monthVisits) : '-');
            setPillTone(monthPill, payload.ok ? 'ok' : (reason === 'duplicate_attendance' ? 'warn' : 'neutral'));

            const checkin = checkinDisplay(payload);
            checkinDate.textContent = checkin.date;
            checkinTime.textContent = checkin.time;
            monthVisitsValue.textContent = monthVisits !== null ? String(monthVisits) : '-';
            motivation.textContent = motivationText(payload, daysInfo, monthVisits);

            if (payload.client) {
                name.textContent = payload.client.full_name || '-';
                membership.textContent = formatDateDisplay(payload.client.membership_ends_at || null);
                showClientVisual(payload.client);
            } else {
                name.textContent = '-';
                membership.textContent = '-';
                avatarInitials.textContent = '--';
                photo.classList.add('hidden');
                photoPlaceholder.classList.remove('hidden');
            }
        }

        function renderIdle() {
            panel.className = basePanelClasses() + ' border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900';
            method.className = 'inline-flex rounded-full bg-slate-200 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-slate-700 dark:bg-slate-700 dark:text-slate-100';
            method.textContent = 'Método: -';
            message.className = 'pr-1 text-[clamp(1.7rem,4.6vw,3.2rem)] font-black leading-tight break-words [overflow-wrap:anywhere] text-slate-800 dark:text-slate-100';
            message.textContent = 'Esperando lectura...';
            name.textContent = '-';
            membership.textContent = '-';
            checkinDate.textContent = '-';
            checkinTime.textContent = '-';
            monthVisitsValue.textContent = '-';
            motivation.textContent = 'Listo para recibir al cliente.';
            daysPill.textContent = 'Días restantes: -';
            monthPill.textContent = 'Visitas mes: -';
            avatarInitials.textContent = '--';
            setPillTone(daysPill, 'neutral');
            setPillTone(monthPill, 'neutral');
            photo.classList.add('hidden');
            photoPlaceholder.classList.remove('hidden');
        }

        function scheduleReset() {
            if (resetTimer) clearTimeout(resetTimer);
            resetTimer = setTimeout(function () {
                renderIdle();
            }, AUTO_RESET_MS);
        }

        function applySyncedPayload(rawPayload) {
            const payload = safePayload(rawPayload);
            if (!payload.message) return;

            const attendanceId = payloadAttendanceId(payload);
            if (payload.ok && attendanceId !== null && attendanceId === lastRenderedAttendanceId) return;

            render(payload);
            prependRecentAttendance(payload);
            scheduleReset();
        }

        function handleSyncEvent(eventData) {
            if (!eventData || eventData.type !== 'checkin') return;
            if (eventData.source === syncSourceId) return;
            if (eventData.id && eventData.id === lastHandledSyncEventId) return;
            const eventPublishedAt = Number(eventData.published_at_ms || eventData.timestamp || 0);
            if (eventPublishedAt > 0 && eventPublishedAt <= lastHandledSyncPublishedAt) return;
            if (eventData.id) {
                lastHandledSyncEventId = eventData.id;
            }
            if (eventPublishedAt > 0) {
                lastHandledSyncPublishedAt = eventPublishedAt;
            }
            applySyncedPayload(eventData.payload);
        }

        async function pollLatestSyncEvent() {
            if (syncPollInFlight) return;

            syncPollInFlight = true;

            try {
                const query = lastHandledSyncPublishedAt > 0
                    ? '?after=' + encodeURIComponent(String(lastHandledSyncPublishedAt))
                    : '';
                const response = await fetch(syncPollUrl + query, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                if (data && data.event) {
                    handleSyncEvent(data.event);
                }
            } catch (error) {
                // Ignore intermittent network errors while polling.
            } finally {
                syncPollInFlight = false;
            }
        }

        function startSyncPolling() {
            pollLatestSyncEvent();
            syncPollTimer = window.setInterval(pollLatestSyncEvent, SYNC_POLL_INTERVAL_MS);
        }

        if (syncChannel) {
            syncChannel.onmessage = function (event) {
                handleSyncEvent(event.data);
            };
        }

        window.addEventListener('storage', function (event) {
            if (event.key !== syncStorageKey || !event.newValue) return;
            try {
                handleSyncEvent(JSON.parse(event.newValue));
            } catch (error) {
                // Ignore malformed storage payloads.
            }
        });

        window.addEventListener('beforeunload', function () {
            if (syncChannel) {
                syncChannel.close();
            }
            if (syncPollTimer) {
                clearInterval(syncPollTimer);
            }
        });

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                pollLatestSyncEvent();
            }
        });

        const initialResult = @json($latestResult);
        if (initialResult) {
            const payload = safePayload(initialResult);
            render(payload);
            prependRecentAttendance(payload);
            scheduleReset();
        } else {
            renderIdle();
        }

        startSyncPolling();
    })();
</script>
</body>
</html>
