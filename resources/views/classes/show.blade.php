@extends('layouts.panel')

@section('title', 'Detalle de clase')
@section('page-title', 'Detalle de clase')

@push('styles')
<style>
    .class-detail-page {
        display: grid;
        gap: 1rem;
    }

    .class-detail-summary {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .class-detail-stat {
        border: 1px solid rgb(148 163 184 / 0.24);
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        background: rgb(255 255 255 / 0.84);
    }

    .theme-dark .class-detail-stat,
    .dark .class-detail-stat {
        border-color: rgb(71 85 105 / 0.7);
        background: rgb(15 23 42 / 0.62);
    }

    .class-detail-stat-label {
        font-size: 0.7rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .class-detail-stat-label,
    .dark .class-detail-stat-label {
        color: rgb(148 163 184 / 0.9);
    }

    .class-detail-stat-value {
        margin-top: 0.45rem;
        font-size: 1.4rem;
        font-weight: 900;
        letter-spacing: -0.04em;
        color: rgb(15 23 42 / 0.98);
    }

    .theme-dark .class-detail-stat-value,
    .dark .class-detail-stat-value {
        color: rgb(248 250 252 / 0.98);
    }

    .class-detail-grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 1200px) {
        .class-detail-grid {
            grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
            align-items: start;
        }
    }

    .class-detail-table-wrap {
        overflow: auto;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / 0.24);
    }

    .theme-dark .class-detail-table-wrap,
    .dark .class-detail-table-wrap {
        border-color: rgb(71 85 105 / 0.62);
    }
</style>
@endpush

@section('content')
    @php
        $routeParams = is_array($routeParams ?? null) ? $routeParams : [];
        $statusMeta = match ((string) $classModel->status) {
            'cancelled' => ['label' => 'Cancelada', 'variant' => 'danger'],
            default => ['label' => 'Programada', 'variant' => 'success'],
        };

        $reservationStatusMeta = static function (?string $status): array {
            return match (trim((string) $status)) {
                'attended' => ['label' => 'Asistio', 'variant' => 'info'],
                'waitlist' => ['label' => 'Espera', 'variant' => 'warning'],
                'cancelled' => ['label' => 'Cancelada', 'variant' => 'danger'],
                default => ['label' => 'Reservada', 'variant' => 'success'],
            };
        };
        $priceMeta = (float) ($classModel->price ?? 0) <= 0
            ? ['label' => 'Gratis', 'classes' => 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100']
            : ['label' => '$'.number_format((float) $classModel->price, 2, '.', ','), 'classes' => 'border-amber-300/45 bg-amber-400/15 text-amber-100'];
    @endphp

    <div class="class-detail-page">
        <div class="space-y-2">
            <a href="{{ route('classes.index', $routeParams) }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-300">Volver a clases</a>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-2xl font-black tracking-tight text-slate-950 dark:text-white">{{ $classModel->name }}</h2>
                <x-ui.badge :variant="$statusMeta['variant']">{{ $statusMeta['label'] }}</x-ui.badge>
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-black uppercase tracking-[0.14em] {{ $priceMeta['classes'] }}">{{ $priceMeta['label'] }}</span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300">
                {{ $classModel->gym?->name ?? 'Sede actual' }}
                | {{ optional($classModel->starts_at)->format('d/m/Y H:i') }}
                | {{ $classModel->instructor_name ?: 'Instructor por definir' }}
            </p>
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">
                La edicion de la clase ahora se hace desde la agenda con el boton Abrir.
            </p>
        </div>

        @if ($isGlobalScope)
            <x-ui.alert type="warning" title="Vista global">
                Estas consultando la clase desde una vista multisede. Puedes revisar reservas, pero la edicion se habilita entrando a la sede de esta clase.
            </x-ui.alert>
        @endif

        <section class="class-detail-summary">
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Reservas</p>
                <p class="class-detail-stat-value">{{ (int) $classModel->reserved_count }}/{{ (int) $classModel->capacity }}</p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Lista de espera</p>
                <p class="class-detail-stat-value">{{ (int) $classModel->waitlist_count }}</p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Sala</p>
                <p class="class-detail-stat-value text-lg !tracking-normal">{{ $classModel->room_name ?: '-' }}</p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Categoria</p>
                <p class="class-detail-stat-value text-lg !tracking-normal">{{ $classModel->category ?: 'General' }}</p>
            </article>
            <article class="class-detail-stat">
                <p class="class-detail-stat-label">Precio</p>
                <p class="class-detail-stat-value text-lg !tracking-normal">{{ $priceMeta['label'] }}</p>
            </article>
        </section>

        <section class="class-detail-grid">
            <div class="space-y-4">
                <x-ui.card title="Informacion de la clase" subtitle="Resumen operativo de la sesion.">
                    <dl class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Inicio</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ optional($classModel->starts_at)->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Fin</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ optional($classModel->ends_at)->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Instructor</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $classModel->instructor_name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Nivel</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $classModel->level ?: '-' }}</dd>
                        </div>
                    </dl>

                    @if ($classModel->description)
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm leading-6 text-slate-600 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-300">
                            {{ $classModel->description }}
                        </div>
                    @endif
                </x-ui.card>

                <x-ui.card title="Edicion desde agenda" subtitle="Si quieres cambiar horario, cupos o estado, vuelve a la agenda y usa el boton Abrir de esa clase.">
                    <div class="flex flex-wrap gap-3">
                        <x-ui.button :href="route('classes.index', $routeParams)" variant="secondary">Ir a agenda</x-ui.button>
                    </div>
                </x-ui.card>
            </div>

            <x-ui.card title="Reservas del cliente" subtitle="Controla cupos confirmados, lista de espera y asistencia.">
                @if ($classModel->reservations->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-600 dark:border-slate-700 dark:text-slate-300">
                        Todavia no hay reservas registradas para esta clase.
                    </div>
                @else
                    <div class="class-detail-table-wrap">
                        <table class="ui-table min-w-[980px] text-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Reserva</th>
                                    <th>Contacto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classModel->reservations as $reservation)
                                    @php
                                        $reservationMeta = $reservationStatusMeta($reservation->status);
                                        $clientName = trim((string) ($reservation->client?->full_name ?? 'Cliente'));
                                        $reserveMoment = $reservation->promoted_at
                                            ? 'Promovido '.optional($reservation->promoted_at)->format('d/m H:i')
                                            : ($reservation->reserved_at
                                                ? 'Reservo '.optional($reservation->reserved_at)->format('d/m H:i')
                                                : ($reservation->waitlisted_at ? 'Espera '.optional($reservation->waitlisted_at)->format('d/m H:i') : '-'));
                                    @endphp
                                    <tr>
                                        <td class="font-semibold text-slate-900 dark:text-slate-100">{{ $clientName }}</td>
                                        <td>{{ $reservation->client?->document_number ?? '-' }}</td>
                                        <td>
                                            <x-ui.badge :variant="$reservationMeta['variant']">{{ $reservationMeta['label'] }}</x-ui.badge>
                                        </td>
                                        <td>{{ $reserveMoment }}</td>
                                        <td>{{ $reservation->client?->phone ?: '-' }}</td>
                                        <td>
                                            @if ($canManageReservations)
                                                <div class="flex flex-wrap gap-2">
                                                    @if ((string) $reservation->status === 'reserved')
                                                        <form method="POST" action="{{ route('classes.reservations.update', $routeParams + ['reservation' => $reservation->id]) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="action" value="attended">
                                                            <x-ui.button type="submit" variant="primary" size="sm">Asistio</x-ui.button>
                                                        </form>
                                                    @endif
                                                    @if (in_array((string) $reservation->status, ['reserved', 'waitlist'], true))
                                                        <form method="POST" action="{{ route('classes.reservations.update', $routeParams + ['reservation' => $reservation->id]) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="action" value="cancel">
                                                            <x-ui.button type="submit" variant="ghost" size="sm">Cancelar</x-ui.button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-500 dark:text-slate-400">Solo lectura</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>
        </section>
    </div>
@endsection
