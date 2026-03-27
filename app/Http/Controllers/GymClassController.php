<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\GymClassReservation;
use App\Services\GymClassBookingService;
use App\Support\ActiveGymContext;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GymClassController extends Controller
{
    public function __construct(
        private readonly GymClassBookingService $gymClassBookingService
    ) {
    }

    public function index(Request $request): View
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $routeParams = $this->routeParams($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $search = trim((string) $request->query('search', ''));
        $selectedDate = $this->resolveSelectedDate($request);
        $windowStart = $selectedDate->copy()->startOfDay();
        $windowEnd = $selectedDate->copy()->addDays(13)->endOfDay();
        $todayStart = now()->copy()->startOfDay();
        $todayEnd = now()->copy()->endOfDay();

        $classes = GymClass::query()
            ->forGyms($gymIds)
            ->with(['gym:id,name,slug'])
            ->withCount([
                'activeReservations as reserved_count',
                'waitlistReservations as waitlist_count',
            ])
            ->search($search)
            ->where('starts_at', '<=', $windowEnd)
            ->where('ends_at', '>=', $windowStart)
            ->get()
            ->filter(static function (GymClass $classModel) use ($windowStart, $windowEnd): bool {
                return $classModel->occursWithinRange($windowStart, $windowEnd);
            })
            ->sortBy(static function (GymClass $classModel) use ($windowStart): int {
                $window = $classModel->nextOccurrenceWindow($windowStart);

                return $window ? $window['start']->getTimestamp() : PHP_INT_MAX;
            })
            ->values();

        $todayClassesCount = GymClass::query()
            ->forGyms($gymIds)
            ->where('status', GymClass::STATUS_SCHEDULED)
            ->where('starts_at', '<=', $todayEnd)
            ->where('ends_at', '>=', $todayStart)
            ->get()
            ->filter(static function (GymClass $classModel) use ($todayStart): bool {
                return $classModel->occursOnDate($todayStart);
            })
            ->count();

        $todayReservationsCount = GymClassReservation::query()
            ->whereIn('gym_id', $gymIds)
            ->whereIn('status', [
                GymClassReservation::STATUS_RESERVED,
                GymClassReservation::STATUS_ATTENDED,
            ])
            ->with(['gymClass:id,gym_id,active_weekdays,starts_at,ends_at,status'])
            ->whereHas('gymClass', function ($query) use ($todayStart, $todayEnd): void {
                $query->where('starts_at', '<=', $todayEnd)
                    ->where('ends_at', '>=', $todayStart);
            })
            ->get()
            ->filter(static function (GymClassReservation $reservation) use ($todayStart): bool {
                return $reservation->gymClass instanceof GymClass
                    && $reservation->gymClass->occursOnDate($todayStart);
            })
            ->count();

        $waitlistCount = GymClassReservation::query()
            ->whereIn('gym_id', $gymIds)
            ->where('status', GymClassReservation::STATUS_WAITLIST)
            ->with(['gymClass:id,gym_id,active_weekdays,starts_at,ends_at,status'])
            ->whereHas('gymClass', function ($query): void {
                $query->where('ends_at', '>=', now());
            })
            ->get()
            ->filter(static function (GymClassReservation $reservation): bool {
                return $reservation->gymClass instanceof GymClass
                    && $reservation->gymClass->nextOccurrenceWindow(now()) !== null;
            })
            ->count();

        $nextClass = GymClass::query()
            ->forGyms($gymIds)
            ->with(['gym:id,name,slug'])
            ->withCount([
                'activeReservations as reserved_count',
                'waitlistReservations as waitlist_count',
            ])
            ->where('status', GymClass::STATUS_SCHEDULED)
            ->where('ends_at', '>=', now())
            ->get()
            ->filter(static function (GymClass $classModel): bool {
                return $classModel->nextOccurrenceWindow(now()) !== null;
            })
            ->sortBy(static function (GymClass $classModel): int {
                $window = $classModel->nextOccurrenceWindow(now());

                return $window ? $window['start']->getTimestamp() : PHP_INT_MAX;
            })
            ->first();

        $canManageClasses = ! ActiveGymContext::isGlobal($request) && ! (bool) $request->user()?->isCashier();

        return view('classes.index', [
            'classes' => $classes,
            'search' => $search,
            'selectedDate' => $selectedDate->toDateString(),
            'isGlobalScope' => $isGlobalScope,
            'routeParams' => $routeParams,
            'canCreateClasses' => $canManageClasses,
            'canManageClasses' => $canManageClasses,
            'todayClassesCount' => $todayClassesCount,
            'todayReservationsCount' => $todayReservationsCount,
            'waitlistCount' => $waitlistCount,
            'nextClass' => $nextClass,
        ]);
    }

    public function show(Request $request, string $contextGym, int $gymClass): View
    {
        $routeParams = $this->routeParams($request, $contextGym);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);

        $classModel = GymClass::query()
            ->forGyms($gymIds)
            ->with(['gym:id,name,slug'])
            ->withCount([
                'activeReservations as reserved_count',
                'waitlistReservations as waitlist_count',
            ])
            ->with([
                'reservations' => static function ($query): void {
                    $query->with([
                        'client:id,gym_id,first_name,last_name,document_number,phone,status',
                    ])
                        ->orderByRaw(
                            "CASE status
                                WHEN '".GymClassReservation::STATUS_RESERVED."' THEN 0
                                WHEN '".GymClassReservation::STATUS_ATTENDED."' THEN 1
                                WHEN '".GymClassReservation::STATUS_WAITLIST."' THEN 2
                                ELSE 3
                             END"
                        )
                        ->orderBy('reserved_at')
                        ->orderBy('waitlisted_at')
                        ->orderBy('id');
                },
            ])
            ->findOrFail($gymClass);

        return view('classes.show', [
            'classModel' => $classModel,
            'isGlobalScope' => $isGlobalScope,
            'routeParams' => $routeParams,
            'canManageReservations' => true,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('classes.index', $this->routeParams($request))
                ->withErrors(['classes' => 'Selecciona una sede especifica para crear clases.']);
        }

        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $payload = $this->validatedPayload($request);
        $payload['gym_id'] = $gymId;
        $payload['created_by'] = (int) ($request->user()?->id ?? 0) ?: null;
        $payload['updated_by'] = $payload['created_by'];

        GymClass::query()->create($payload);

        return redirect()
            ->route('classes.index', $this->routeParams($request))
            ->with('status', 'Clase creada correctamente.');
    }

    public function update(Request $request, string $contextGym, int $gymClass): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('classes.index', $this->routeParams($request, $contextGym))
                ->withErrors(['classes' => 'Selecciona una sede especifica para editar clases.']);
        }

        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        $classModel = GymClass::query()
            ->forGym($gymId)
            ->with(['gym:id,name,slug'])
            ->withCount(['activeReservations as reserved_count'])
            ->findOrFail($gymClass);

        $previousStatus = (string) $classModel->status;
        $previousStartsAt = $classModel->starts_at?->copy();
        $previousRoom = trim((string) ($classModel->room_name ?? ''));

        $payload = $this->validatedPayload($request);
        $payload['updated_by'] = (int) ($request->user()?->id ?? 0) ?: null;

        if ((int) $payload['capacity'] < (int) ($classModel->reserved_count ?? 0)) {
            throw ValidationException::withMessages([
                'capacity' => 'La capacidad no puede quedar por debajo de las reservas activas.',
            ]);
        }

        $classModel->update($payload);
        $classModel->refresh();
        $classModel->loadMissing('gym:id,name,slug');

        if ($previousStatus !== GymClass::STATUS_CANCELLED && (string) $classModel->status === GymClass::STATUS_CANCELLED) {
            $this->gymClassBookingService->notifyParticipants(
                $classModel,
                'La clase '.$classModel->name.' fue cancelada. Revisa la agenda para elegir otra opcion.'
            );
        } elseif (
            (string) $classModel->status === GymClass::STATUS_SCHEDULED
            && (
                ($previousStartsAt && $classModel->starts_at && ! $classModel->starts_at->equalTo($previousStartsAt))
                || trim((string) ($classModel->room_name ?? '')) !== $previousRoom
            )
        ) {
            $this->gymClassBookingService->notifyParticipants(
                $classModel,
                'La clase '.$classModel->name.' fue actualizada. Nueva hora: '.$classModel->starts_at?->format('d/m H:i').'.'
            );
        }

        return back()->with('status', 'Clase actualizada correctamente.');
    }

    public function notifyParticipants(Request $request, string $contextGym, int $gymClass): RedirectResponse
    {
        $gymIds = ActiveGymContext::ids($request);
        $classModel = GymClass::query()
            ->forGyms($gymIds)
            ->with('gym:id,name,slug')
            ->findOrFail($gymClass);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:180'],
        ]);

        $summary = $this->gymClassBookingService->notifyParticipants($classModel, (string) $data['message']);

        return back()->with(
            'status',
            'Aviso procesado. Enviadas: '.(int) $summary['sent'].' | Fallidas: '.(int) $summary['failed'].' | Omitidas: '.(int) $summary['skipped']
        );
    }

    public function updateReservation(Request $request, string $contextGym, int $reservation): RedirectResponse
    {
        $gymIds = ActiveGymContext::ids($request);
        $reservationModel = GymClassReservation::query()
            ->with(['gymClass:id,gym_id'])
            ->whereHas('gymClass', function ($query) use ($gymIds): void {
                $query->forGyms($gymIds);
            })
            ->findOrFail($reservation);

        $data = $request->validate([
            'action' => ['required', 'in:attended,cancel'],
        ]);

        $result = (string) $data['action'] === 'attended'
            ? $this->gymClassBookingService->markAttendance($reservationModel)
            : $this->gymClassBookingService->cancelReservationByAdmin($reservationModel);

        if (! (bool) ($result['ok'] ?? false)) {
            return back()->withErrors(['reservation' => (string) ($result['message'] ?? 'No se pudo actualizar la reserva.')]);
        }

        return back()->with('status', (string) ($result['message'] ?? 'Reserva actualizada.'));
    }

    private function resolveSelectedDate(Request $request): Carbon
    {
        $rawDate = trim((string) $request->query('date', ''));
        if ($rawDate === '') {
            return now();
        }

        try {
            return Carbon::parse($rawDate);
        } catch (\Throwable) {
            return now();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:80'],
            'level' => ['nullable', 'string', 'max:40'],
            'instructor_name' => ['nullable', 'string', 'max:120'],
            'room_name' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1200'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'active_weekdays' => ['nullable', 'array'],
            'active_weekdays.*' => ['integer', 'between:1,7'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'capacity' => ['required', 'integer', 'min:1', 'max:300'],
            'allow_waitlist' => ['nullable', 'boolean'],
            'status' => ['required', 'in:scheduled,cancelled'],
        ]);

        $startsAt = $this->resolveMomentInput($request, 'starts_at', 'start_date', 'start_time', 'inicio');
        $endsAt = $this->resolveMomentInput($request, 'ends_at', 'end_date', 'end_time', 'fin');

        if ($endsAt->lte($startsAt)) {
            throw ValidationException::withMessages([
                'end_date' => 'La fecha y hora de fin debe ser mayor que inicio.',
                'end_time' => 'La fecha y hora de fin debe ser mayor que inicio.',
            ]);
        }

        unset($data['start_date'], $data['start_time'], $data['end_date'], $data['end_time']);

        $data['starts_at'] = $startsAt;
        $data['ends_at'] = $endsAt;
        $data['price'] = round((float) ($data['price'] ?? 0), 2);
        $data['active_weekdays'] = GymClass::normalizeWeekdaySelection($data['active_weekdays'] ?? []);
        $data['allow_waitlist'] = $request->boolean('allow_waitlist');

        return $data;
    }

    private function resolveMomentInput(
        Request $request,
        string $legacyField,
        string $dateField,
        string $timeField,
        string $label
    ): Carbon {
        $legacyValue = trim((string) $request->input($legacyField, ''));
        $dateValue = trim((string) $request->input($dateField, ''));
        $timeValue = trim((string) $request->input($timeField, ''));

        if ($dateValue !== '' || $timeValue !== '') {
            $messages = [];

            if ($dateValue === '') {
                $messages[$dateField] = 'Selecciona la fecha de '.$label.'.';
            }

            if ($timeValue === '') {
                $messages[$timeField] = 'Selecciona la hora de '.$label.'.';
            }

            if ($messages !== []) {
                throw ValidationException::withMessages($messages);
            }

            try {
                return Carbon::createFromFormat('Y-m-d H:i', $dateValue.' '.$timeValue);
            } catch (\Throwable) {
                throw ValidationException::withMessages([
                    $dateField => 'La fecha u hora de '.$label.' no es valida.',
                ]);
            }
        }

        if ($legacyValue !== '') {
            try {
                return Carbon::parse($legacyValue);
            } catch (\Throwable) {
                throw ValidationException::withMessages([
                    $legacyField => 'La fecha u hora de '.$label.' no es valida.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            $dateField => 'Selecciona la fecha de '.$label.'.',
            $timeField => 'Selecciona la hora de '.$label.'.',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function routeParams(Request $request, ?string $contextGym = null): array
    {
        $params = [];
        $gymSlug = trim((string) ($contextGym ?? $request->route('contextGym') ?? ''));
        if ($gymSlug !== '') {
            $params['contextGym'] = $gymSlug;
        }

        if (ActiveGymContext::isGlobal($request)) {
            $params['scope'] = 'global';
        }

        return $params;
    }
}
