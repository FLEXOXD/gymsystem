<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientFitnessProfile;
use App\Models\ClientProgressSnapshot;
use App\Models\Membership;
use App\Models\PresenceSession;
use App\Support\FitnessGoalSupport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ClientProgressOverviewService
{
    public function build(Client $client, ?Membership $latestMembership = null, string $gymTimezone = ''): array
    {
        $gymId = (int) $client->gym_id;
        $timezone = $this->resolveTimezone($gymTimezone);
        $nowAtGym = Carbon::now($timezone);
        $today = $nowAtGym->copy()->startOfDay();
        $monthStart = $nowAtGym->copy()->startOfMonth();
        $monthEnd = $nowAtGym->copy()->endOfMonth();

        $fitnessProfile = ClientFitnessProfile::query()
            ->forGym($gymId)
            ->where('client_id', (int) $client->id)
            ->first([
                'id',
                'gym_id',
                'client_id',
                'height_cm',
                'weight_kg',
                'goal',
                'secondary_goal',
                'experience_level',
                'days_per_week',
                'session_minutes',
                'limitations',
                'onboarding_completed_at',
                'updated_at',
            ]);

        $monthAttendances = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', (int) $client->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->get(['date', 'time']);

        $recentAttendances = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', (int) $client->id)
            ->whereBetween('date', [$nowAtGym->copy()->subDays(45)->toDateString(), $today->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->get(['date', 'time']);

        $lastAttendance = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', (int) $client->id)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first(['date', 'time']);

        $monthVisits = (int) $monthAttendances->count();
        $totalVisits = (int) Attendance::query()
            ->forGym($gymId)
            ->where('client_id', (int) $client->id)
            ->count();

        [$membershipMeta, $periodVisits] = $this->buildMembershipMeta(
            latestMembership: $latestMembership,
            gymId: $gymId,
            clientId: (int) $client->id,
            today: $today,
            timezone: $timezone
        );

        $snapshotSections = [];
        $snapshotSourceLabel = null;
        if (Schema::hasTable('client_progress_snapshots')) {
            $snapshot = ClientProgressSnapshot::query()
                ->forGym($gymId)
                ->where('client_id', (int) $client->id)
                ->first(['id', 'snapshot_for_date', 'source_attendance_date', 'sections']);

            $snapshotSections = is_array($snapshot?->sections ?? null)
                ? $snapshot->sections
                : [];

            if ($snapshot?->source_attendance_date) {
                $snapshotSourceLabel = 'Lectura basada en progreso confirmado hasta '.$snapshot->source_attendance_date->format('Y-m-d').'.';
            }
        }

        $prediction = $this->resolvePrediction(
            fitnessProfile: $fitnessProfile,
            monthVisits: $monthVisits,
            periodVisits: $periodVisits,
            totalVisits: $totalVisits,
            snapshotSections: $snapshotSections
        );

        $bodyState = $this->resolveBodyState(
            fitnessProfile: $fitnessProfile,
            recentAttendances: $recentAttendances,
            monthVisits: $monthVisits,
            nowAtGym: $nowAtGym,
            snapshotSections: $snapshotSections
        );

        $membershipStartDate = trim((string) ($latestMembership?->starts_at?->toDateString() ?? ''));
        $weeklyGoal = $this->resolveWeeklyGoal(
            fitnessProfile: $fitnessProfile,
            recentAttendances: $recentAttendances,
            nowAtGym: $nowAtGym,
            membershipStartDate: $membershipStartDate,
            bodyState: $bodyState,
            snapshotSections: $snapshotSections
        );

        $timeline = $this->buildMonthTimeline(
            recentAttendances: $recentAttendances,
            nowAtGym: $nowAtGym,
            daysPerWeek: max(3, min(7, (int) ($fitnessProfile?->days_per_week ?? 3))),
            visibleFromDate: $membershipStartDate
        );

        $training = $this->resolveTrainingSummary(
            fitnessProfile: $fitnessProfile,
            weeklyGoal: $weeklyGoal,
            snapshotSections: $snapshotSections
        );

        $daysSinceLast = isset($bodyState['days_since_last']) && $bodyState['days_since_last'] !== null
            ? (int) $bodyState['days_since_last']
            : null;

        $performance = $this->buildPerformanceSummary(
            membershipMeta: $membershipMeta,
            prediction: $prediction,
            weeklyGoal: $weeklyGoal,
            daysSinceLast: $daysSinceLast
        );

        $alerts = $this->buildAlerts(
            membershipMeta: $membershipMeta,
            fitnessProfile: $fitnessProfile,
            weeklyGoal: $weeklyGoal,
            bodyState: $bodyState,
            prediction: $prediction
        );

        $liveClientsCount = (int) PresenceSession::query()
            ->forGym($gymId)
            ->open()
            ->count();

        $stats = [
            [
                'label' => 'Estado general',
                'value' => $performance['label'],
                'meta' => $performance['score'].'/100',
                'tone' => $performance['tone'],
            ],
            [
                'label' => 'Visitas del mes',
                'value' => (string) $monthVisits,
                'meta' => 'Total historico: '.$totalVisits,
                'tone' => 'info',
            ],
            [
                'label' => 'Meta semanal',
                'value' => (int) ($weeklyGoal['visits'] ?? 0).' / '.(int) ($weeklyGoal['target'] ?? 0),
                'meta' => 'Cumplimiento: '.(int) ($weeklyGoal['completion_percent'] ?? 0).'%',
                'tone' => (int) ($weeklyGoal['completion_percent'] ?? 0) >= 100 ? 'success' : 'warning',
            ],
            [
                'label' => 'Dias restantes',
                'value' => (string) ($membershipMeta['days_remaining_label'] ?? 'Sin membresia'),
                'meta' => 'Ultima asistencia: '.$this->formatLastAttendanceLabel($lastAttendance, $today),
                'tone' => (string) ($membershipMeta['tone'] ?? 'muted'),
            ],
        ];

        return [
            'stats' => $stats,
            'performance' => $performance,
            'alerts' => $alerts,
            'membership' => $membershipMeta,
            'prediction' => $prediction,
            'weekly_goal' => $weeklyGoal,
            'body_state' => $bodyState,
            'timeline' => [
                'month_label' => $this->formatMonthLabel($nowAtGym),
                'entries' => $timeline,
            ],
            'training' => $training,
            'profile' => $this->buildProfileMeta($fitnessProfile),
            'live_clients_count' => $liveClientsCount,
            'last_attendance_label' => $this->formatLastAttendanceLabel($lastAttendance, $today),
            'snapshot_source_label' => $snapshotSourceLabel,
        ];
    }

    private function resolveTimezone(string $gymTimezone): string
    {
        $timezone = trim($gymTimezone);
        if ($timezone === '') {
            return config('app.timezone', 'UTC');
        }

        try {
            new \DateTimeZone($timezone);

            return $timezone;
        } catch (\Throwable) {
            return config('app.timezone', 'UTC');
        }
    }

    /**
     * @return array{0: array<string, mixed>, 1: int}
     */
    private function buildMembershipMeta(?Membership $latestMembership, int $gymId, int $clientId, Carbon $today, string $timezone): array
    {
        if (! $latestMembership) {
            return [[
                'status_label' => 'Sin membresia',
                'status_line' => 'No tiene membresia registrada.',
                'days_remaining' => null,
                'days_remaining_label' => 'Sin membresia',
                'ends_at_label' => 'N/A',
                'period_window_label' => 'Sin membresia activa',
                'tone' => 'muted',
                'is_active' => false,
            ], 0];
        }

        $startsAt = $latestMembership->starts_at
            ? Carbon::parse((string) $latestMembership->starts_at, $timezone)->startOfDay()
            : null;
        $endsAt = $latestMembership->ends_at
            ? Carbon::parse((string) $latestMembership->ends_at, $timezone)->startOfDay()
            : null;

        $status = trim((string) ($latestMembership->status ?? ''));
        $isCancelled = $status === 'cancelled';
        $isScheduled = ! $isCancelled && $startsAt instanceof Carbon && $startsAt->greaterThan($today);
        $isExpired = $isCancelled || ! ($endsAt instanceof Carbon) || $endsAt->lt($today);
        $daysRemaining = $endsAt instanceof Carbon
            ? (int) $today->diffInDays($endsAt, false)
            : null;

        $tone = 'success';
        $statusLabel = 'Vigente';
        $statusLine = 'Membresia activa y operativa.';
        if ($isScheduled) {
            $tone = 'info';
            $statusLabel = 'Programada';
            $statusLine = 'La membresia aun no inicia.';
        } elseif ($isCancelled) {
            $tone = 'danger';
            $statusLabel = 'Cancelada';
            $statusLine = 'La membresia fue cancelada.';
        } elseif ($isExpired) {
            $tone = 'danger';
            $statusLabel = 'Vencida';
            $statusLine = 'El cliente requiere renovacion.';
        } elseif ($daysRemaining !== null && $daysRemaining <= 7) {
            $tone = 'warning';
            $statusLabel = 'Por vencer';
            $statusLine = 'La membresia esta por vencer.';
        }

        $periodVisits = 0;
        $periodWindowLabel = 'Sin membresia activa';
        if ($startsAt instanceof Carbon && $endsAt instanceof Carbon) {
            $periodWindowLabel = $startsAt->toDateString().' al '.$endsAt->toDateString();

            if (! $isScheduled && ! $isExpired) {
                $periodVisits = (int) Attendance::query()
                    ->forGym($gymId)
                    ->where('client_id', $clientId)
                    ->whereBetween('date', [$startsAt->toDateString(), $today->toDateString()])
                    ->count();
            }
        }

        $daysRemainingLabel = 'N/A';
        if ($daysRemaining !== null) {
            if ($daysRemaining > 0) {
                $daysRemainingLabel = $daysRemaining.' dias';
            } elseif ($daysRemaining === 0) {
                $daysRemainingLabel = 'Hoy';
            } else {
                $daysRemainingLabel = 'Hace '.abs($daysRemaining).' dias';
            }
        }

        return [[
            'status_label' => $statusLabel,
            'status_line' => $statusLine,
            'days_remaining' => $daysRemaining,
            'days_remaining_label' => $daysRemainingLabel,
            'ends_at_label' => $endsAt?->format('Y-m-d') ?? 'N/A',
            'period_window_label' => $periodWindowLabel,
            'tone' => $tone,
            'is_active' => ! $isScheduled && ! $isExpired,
        ], $periodVisits];
    }

    private function resolvePrediction(
        ?ClientFitnessProfile $fitnessProfile,
        int $monthVisits,
        int $periodVisits,
        int $totalVisits,
        array $snapshotSections
    ): array {
        $snapshot = $snapshotSections['prediction'] ?? null;
        if (is_array($snapshot) && ($snapshot['ready'] ?? false)) {
            return $snapshot;
        }

        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'rhythm_label' => 'Sin datos',
                'consistency_percent' => 0,
                'primary_line' => 'Completa el perfil fisico del cliente para activar la prediccion.',
                'secondary_line' => 'Mientras tanto puedes seguir visitas, ultima asistencia y renovacion.',
                'context_line' => 'No existe un perfil fisico cargado en la app cliente.',
            ];
        }

        [$primaryGoal, $secondaryGoal] = FitnessGoalSupport::pair(
            (string) ($fitnessProfile->goal ?? ''),
            isset($fitnessProfile->secondary_goal) ? (string) $fitnessProfile->secondary_goal : null
        );
        $goal = $primaryGoal;
        $experienceLevel = trim((string) ($fitnessProfile->experience_level ?? ''));
        $daysPerWeek = max(1, min(7, (int) ($fitnessProfile->days_per_week ?? 3)));

        $expectedVisitsMonth = max(1, (int) round($daysPerWeek * 4.3));
        $adherenceRatio = max(0.0, min(1.4, $monthVisits / $expectedVisitsMonth));
        $consistencyPercent = (int) round(min(100, $adherenceRatio * 100));

        $experienceMultiplier = match ($experienceLevel) {
            'principiante' => 1.10,
            'intermedio' => 1.0,
            'avanzado' => 0.88,
            default => 1.0,
        };

        $strengthGainPct = (4 + ($adherenceRatio * 6.5) + (($daysPerWeek - 3) * 0.7)) * $experienceMultiplier;
        $strengthGainPct = max(2.5, min(14.5, $strengthGainPct));
        $resistanceGainPct = 5 + ($adherenceRatio * 7.4) + (($daysPerWeek - 3) * 0.5);
        $resistanceGainPct = max(3.0, min(16.0, $resistanceGainPct));

        $rhythmLabel = 'Ritmo bajo';
        if ($adherenceRatio >= 0.95) {
            $rhythmLabel = 'Ritmo alto';
        } elseif ($adherenceRatio >= 0.7) {
            $rhythmLabel = 'Ritmo medio';
        }

        $primaryLine = 'Manteniendo este ritmo puede mejorar su condicion en 30 dias.';
        $secondaryLine = 'La fuerza podria subir alrededor de +'.(int) round($strengthGainPct).'%';

        switch ($goal) {
            case 'perder_grasa':
                $fatLossKg = max(0.5, min(3.6, 0.7 + ($adherenceRatio * 2.0)));
                $primaryLine = 'Podria perder '.number_format($fatLossKg, 1, '.', '').' kg de grasa en 30 dias.';
                $secondaryLine = 'Tambien podria mejorar +'.(int) round($strengthGainPct).'% su fuerza base.';
                break;
            case 'ganar_musculo':
                $muscleGainKg = max(0.2, min(1.4, 0.25 + ($adherenceRatio * 0.75 * $experienceMultiplier)));
                $primaryLine = 'Podria ganar '.number_format($muscleGainKg, 1, '.', '').' kg de masa muscular en 30 dias.';
                $secondaryLine = 'Su fuerza podria subir +'.(int) round($strengthGainPct).'% en ejercicios base.';
                break;
            case 'mantener_forma':
                $variationKg = max(0.3, min(0.8, 0.9 - ($adherenceRatio * 0.45)));
                $primaryLine = 'Podria mantenerse dentro de +/-'.number_format($variationKg, 1, '.', '').' kg en 30 dias.';
                $secondaryLine = 'La resistencia podria mejorar +'.(int) round($resistanceGainPct).'% con esta constancia.';
                break;
            case 'definir':
                $fatLossKg = max(0.4, min(2.4, 0.5 + ($adherenceRatio * 1.3)));
                $primaryLine = 'Podria bajar '.number_format($fatLossKg, 1, '.', '').' kg de grasa manteniendo masa muscular.';
                $secondaryLine = 'Se proyecta +'.(int) round($strengthGainPct * 0.8).'% en fuerza y control.';
                break;
            case 'aumentar_fuerza':
                $primaryLine = 'Podria aumentar +'.(int) round($strengthGainPct + 1.5).'% su fuerza en 30 dias.';
                $secondaryLine = 'Su capacidad de trabajo podria subir +'.(int) round($resistanceGainPct * 0.7).'%.';
                break;
            case 'mejorar_resistencia':
                $primaryLine = 'Podria mejorar +'.(int) round($resistanceGainPct + 1.2).'% su resistencia en 30 dias.';
                $secondaryLine = 'Con eso podria sostener +'.(int) round($strengthGainPct * 0.6).'% mas volumen efectivo.';
                break;
        }

        $primaryLine = $this->buildPredictionGoalLine(
            $primaryGoal,
            $adherenceRatio,
            $experienceMultiplier,
            $strengthGainPct,
            $resistanceGainPct,
            true,
            $primaryGoal,
            $secondaryGoal
        );
        $secondaryLine = $secondaryGoal !== null
            ? 'Apoyo secundario: '.$this->buildPredictionGoalLine(
                $secondaryGoal,
                $adherenceRatio,
                $experienceMultiplier,
                $strengthGainPct,
                $resistanceGainPct,
                false,
                $primaryGoal,
                $secondaryGoal
            )
            : $this->buildPredictionGoalLine(
                $primaryGoal,
                $adherenceRatio,
                $experienceMultiplier,
                $strengthGainPct,
                $resistanceGainPct,
                false,
                $primaryGoal,
                $secondaryGoal
            );

        return [
            'ready' => true,
            'rhythm_label' => $rhythmLabel,
            'consistency_percent' => $consistencyPercent,
            'primary_line' => $primaryLine,
            'secondary_line' => $secondaryLine,
            'context_line' => 'Objetivo: '.FitnessGoalSupport::summaryLabel($primaryGoal, $secondaryGoal, 'General').' | Visitas del mes: '.$monthVisits.' de '.$expectedVisitsMonth.' esperadas.',
            'month_visits' => $monthVisits,
            'period_visits' => $periodVisits,
            'total_visits' => $totalVisits,
        ];
    }

    private function buildPredictionGoalLine(
        ?string $goal,
        float $adherenceRatio,
        float $experienceMultiplier,
        float $strengthGainPct,
        float $resistanceGainPct,
        bool $isPrimaryLine,
        ?string $primaryGoal,
        ?string $secondaryGoal
    ): string {
        $goal = FitnessGoalSupport::normalize($goal);
        if ($goal === null) {
            return $isPrimaryLine
                ? 'Manteniendo este ritmo puede mejorar su condicion en 30 dias.'
                : 'La fuerza podria subir alrededor de +'.(int) round($strengthGainPct).'%.';
        }

        $muscleBias = max(0.55, FitnessGoalSupport::predictionFactor('muscle_gain', $primaryGoal, $secondaryGoal));
        $fatLossBias = max(0.55, FitnessGoalSupport::predictionFactor('fat_loss', $primaryGoal, $secondaryGoal));
        $strengthBias = max(0.50, FitnessGoalSupport::predictionFactor('strength', $primaryGoal, $secondaryGoal));
        $resistanceBias = max(0.50, FitnessGoalSupport::predictionFactor('resistance', $primaryGoal, $secondaryGoal));

        return match ($goal) {
            'perder_grasa' => $isPrimaryLine
                ? 'Podria perder '.number_format(max(0.5, min(3.6, (0.7 + ($adherenceRatio * 2.0)) * $fatLossBias)), 1, '.', '').' kg de grasa en 30 dias.'
                : 'Tambien podria mejorar +'.(int) round($strengthGainPct * $strengthBias).'% su fuerza base.',
            'ganar_musculo' => $isPrimaryLine
                ? 'Podria ganar '.number_format(max(0.2, min(1.6, (0.25 + ($adherenceRatio * 0.75 * $experienceMultiplier)) * $muscleBias)), 1, '.', '').' kg de masa muscular en 30 dias.'
                : 'Su fuerza podria subir +'.(int) round($strengthGainPct * $strengthBias).'% en ejercicios base.',
            'mantener_forma' => $isPrimaryLine
                ? 'Podria mantenerse dentro de +/-'.number_format(max(0.3, min(0.9, 0.95 - ($adherenceRatio * 0.42))), 1, '.', '').' kg en 30 dias.'
                : 'La resistencia podria mejorar +'.(int) round($resistanceGainPct * $resistanceBias).'% con esta constancia.',
            'definir' => $isPrimaryLine
                ? 'Podria bajar '.number_format(max(0.4, min(2.6, (0.5 + ($adherenceRatio * 1.3)) * $fatLossBias)), 1, '.', '').' kg de grasa manteniendo masa muscular.'
                : 'Se proyecta +'.(int) round(($strengthGainPct * 0.8) * $strengthBias).'% en fuerza y control.',
            'aumentar_fuerza' => $isPrimaryLine
                ? 'Podria aumentar +'.(int) round(($strengthGainPct + 1.5) * $strengthBias).'% su fuerza en 30 dias.'
                : 'Su capacidad de trabajo podria subir +'.(int) round(($resistanceGainPct * 0.7) * $resistanceBias).'%.',
            'mejorar_resistencia' => $isPrimaryLine
                ? 'Podria mejorar +'.(int) round(($resistanceGainPct + 1.2) * $resistanceBias).'% su resistencia en 30 dias.'
                : 'Con eso podria sostener +'.(int) round(($strengthGainPct * 0.6) * $strengthBias).'% mas volumen efectivo.',
            default => $isPrimaryLine
                ? 'Manteniendo este ritmo puede mejorar su condicion en 30 dias.'
                : 'La fuerza podria subir alrededor de +'.(int) round($strengthGainPct).'%.',
        };
    }

    private function resolveBodyState(
        ?ClientFitnessProfile $fitnessProfile,
        Collection $recentAttendances,
        int $monthVisits,
        Carbon $nowAtGym,
        array $snapshotSections
    ): array {
        $snapshot = $snapshotSections['body_state'] ?? null;
        if (is_array($snapshot) && ($snapshot['ready'] ?? false)) {
            return $snapshot;
        }

        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'force' => 0,
                'resistance' => 0,
                'discipline' => 0,
                'recovery' => 0,
                'streak_days' => 0,
                'week_visits' => 0,
                'days_since_last' => null,
                'summary_line' => 'Sin perfil fisico completo.',
                'context_line' => 'Completa los datos fisicos en la PWA para activar este analisis.',
            ];
        }

        $attendanceDates = $recentAttendances
            ->map(fn ($attendance): string => $this->extractDateValue($attendance))
            ->filter(fn (string $date): bool => $date !== '')
            ->unique()
            ->sort()
            ->values();

        $attendanceDateSet = $attendanceDates->flip();
        $today = $nowAtGym->copy()->startOfDay();
        $todayKey = $today->toDateString();
        $yesterdayKey = $today->copy()->subDay()->toDateString();

        $streakCursor = null;
        if ($attendanceDateSet->has($todayKey)) {
            $streakCursor = $today->copy();
        } elseif ($attendanceDateSet->has($yesterdayKey)) {
            $streakCursor = $today->copy()->subDay();
        }

        $streakDays = 0;
        if ($streakCursor instanceof Carbon) {
            while ($attendanceDateSet->has($streakCursor->toDateString())) {
                $streakDays++;
                $streakCursor->subDay();
            }
        }

        $lastDateString = (string) ($attendanceDates->last() ?? '');
        $daysSinceLast = null;
        if ($lastDateString !== '') {
            $daysSinceLast = max(0, (int) Carbon::parse($lastDateString, $nowAtGym->getTimezone())
                ->startOfDay()
                ->diffInDays($today, false));
        }

        $weekStart = $today->copy()->subDays(6)->toDateString();
        $weekVisits = (int) $attendanceDates
            ->filter(fn (string $date): bool => $date >= $weekStart)
            ->count();

        $daysPerWeek = max(1, min(7, (int) ($fitnessProfile->days_per_week ?? 3)));
        $sessionMinutes = max(30, min(180, (int) ($fitnessProfile->session_minutes ?? 60)));
        [$primaryGoal, $secondaryGoal] = FitnessGoalSupport::pair(
            (string) ($fitnessProfile->goal ?? ''),
            isset($fitnessProfile->secondary_goal) ? (string) $fitnessProfile->secondary_goal : null
        );
        $experienceLevel = trim((string) ($fitnessProfile->experience_level ?? ''));

        $expectedMonthVisits = max(1, (int) round($daysPerWeek * 4.3));
        $monthAdherence = max(0.0, min(1.4, $monthVisits / $expectedMonthVisits));
        $weekConsistency = max(0.0, min(1.5, $weekVisits / max(1, $daysPerWeek)));

        $intensityBase = match ($sessionMinutes) {
            90 => 74,
            60 => 58,
            default => 45,
        };
        $goalIntensityBonus = FitnessGoalSupport::intensityBonus($primaryGoal, $secondaryGoal);
        $trainingLoad = (int) round(max(15, min(98, $intensityBase + ($weekVisits * 6) + $goalIntensityBonus)));

        $experienceBonus = match ($experienceLevel) {
            'principiante' => 4,
            'intermedio' => 6,
            'avanzado' => 8,
            default => 5,
        };
        $goalStrengthBonus = FitnessGoalSupport::strengthBonus($primaryGoal, $secondaryGoal);
        $goalResistanceBonus = FitnessGoalSupport::resistanceBonus($primaryGoal, $secondaryGoal);

        $force = (int) round(max(12, min(98, 30 + ($trainingLoad * 0.35) + ($monthAdherence * 22) + ($streakDays * 2) + $goalStrengthBonus + $experienceBonus)));
        $resistance = (int) round(max(12, min(98, 26 + ($weekVisits * 8) + ($sessionMinutes * 0.24) + ($monthAdherence * 16) + $goalResistanceBonus)));
        $discipline = (int) round(max(8, min(99, 24 + ($streakDays * 8) + ($weekConsistency * 34) + ($monthAdherence * 20))));

        $restDaysWeek = max(0, 7 - $weekVisits);
        $overloadDays = max(0, $weekVisits - $daysPerWeek);
        $recovery = 52 + ($restDaysWeek * 6) - ($overloadDays * 8) - (max(0, $trainingLoad - 75) * 0.45);
        if ($daysSinceLast !== null && $daysSinceLast >= 2) {
            $recovery += 8;
        }
        if ($daysSinceLast === 0 && $weekVisits >= ($daysPerWeek + 1)) {
            $recovery -= 6;
        }
        $recovery = (int) round(max(10, min(98, $recovery)));

        return [
            'ready' => true,
            'force' => $force,
            'resistance' => $resistance,
            'discipline' => $discipline,
            'recovery' => $recovery,
            'streak_days' => $streakDays,
            'week_visits' => $weekVisits,
            'days_since_last' => $daysSinceLast,
            'summary_line' => 'Racha actual: '.$streakDays.' dias | Semana: '.$weekVisits.' entrenamientos.',
            'context_line' => 'Objetivo: '.FitnessGoalSupport::summaryLabel($primaryGoal, $secondaryGoal, 'General').'. Calculado con descanso, intensidad y constancia de los ultimos 45 dias.',
        ];
    }

    private function resolveWeeklyGoal(
        ?ClientFitnessProfile $fitnessProfile,
        Collection $recentAttendances,
        Carbon $nowAtGym,
        string $membershipStartDate,
        array $bodyState,
        array $snapshotSections
    ): array {
        $snapshot = $snapshotSections['weekly_goal'] ?? null;
        if (is_array($snapshot) && isset($snapshot['target'], $snapshot['visits'])) {
            return $snapshot;
        }

        $attendanceDates = $recentAttendances
            ->map(fn ($attendance): string => $this->extractDateValue($attendance))
            ->filter(fn (string $date): bool => $date !== '')
            ->unique()
            ->values();

        $weekStart = $nowAtGym->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = $nowAtGym->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();
        $membershipStartDate = $this->normalizeDateString($membershipStartDate);
        $evaluationStart = $weekStart;
        if ($membershipStartDate !== '' && $membershipStartDate > $evaluationStart) {
            $evaluationStart = $membershipStartDate;
        }

        $evaluableDays = 0;
        if ($evaluationStart <= $weekEnd) {
            $evaluableDays = Carbon::parse($evaluationStart)->diffInDays(Carbon::parse($weekEnd)) + 1;
        }

        $configuredWeeklyGoal = max(3, min(7, (int) ($fitnessProfile?->days_per_week ?? 3)));
        $effectiveWeeklyGoal = $evaluableDays > 0 ? min($configuredWeeklyGoal, $evaluableDays) : 0;
        $weekVisits = (int) $attendanceDates
            ->filter(fn (string $date): bool => $date >= $evaluationStart && $date <= $weekEnd)
            ->count();

        $remaining = max(0, $effectiveWeeklyGoal - $weekVisits);
        $completionPercent = (int) round(min(100, ($weekVisits / max(1, $effectiveWeeklyGoal)) * 100));
        $daysLeftWeek = (int) $nowAtGym->copy()->startOfDay()->diffInDays($nowAtGym->copy()->endOfWeek(Carbon::SUNDAY)->startOfDay());
        $isSundayClose = (int) $nowAtGym->format('N') === 7;

        $alerts = [];
        if (! $isSundayClose) {
            $alerts[] = ['type' => 'info', 'text' => 'El resumen semanal final se calcula al cierre del domingo.'];
        } elseif ($effectiveWeeklyGoal <= 0) {
            $alerts[] = ['type' => 'info', 'text' => 'No hubo dias evaluables esta semana.'];
        } elseif ($weekVisits >= $effectiveWeeklyGoal) {
            $alerts[] = ['type' => 'success', 'text' => 'Meta semanal completada. Buena consistencia.'];
        } elseif ($remaining === 1) {
            $alerts[] = ['type' => 'warning', 'text' => 'Le falta 1 sesion para cumplir la meta semanal.'];
        } else {
            $alerts[] = ['type' => 'info', 'text' => 'Aun faltan '.$remaining.' sesiones para completar la semana.'];
        }

        $streakDays = max(0, (int) ($bodyState['streak_days'] ?? 0));
        $daysSinceLast = isset($bodyState['days_since_last']) ? (int) $bodyState['days_since_last'] : null;
        if ($streakDays >= 2 && $daysSinceLast !== null && $daysSinceLast >= 2) {
            $alerts[] = ['type' => 'danger', 'text' => 'Racha en riesgo: lleva '.$daysSinceLast.' dias sin entrenar.'];
        }

        $recoveryScore = max(0, min(100, (int) ($bodyState['recovery'] ?? 0)));
        if ($recoveryScore < 45) {
            $alerts[] = ['type' => 'warning', 'text' => 'Recuperacion baja. Conviene revisar descanso y carga.'];
        }

        return [
            'target' => $effectiveWeeklyGoal,
            'configured_target' => $configuredWeeklyGoal,
            'visits' => $weekVisits,
            'remaining' => $remaining,
            'completion_percent' => $completionPercent,
            'days_left_week' => $daysLeftWeek,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'evaluation_start' => $evaluationStart,
            'evaluable_days' => $evaluableDays,
            'is_week_closed' => $isSundayClose,
            'commitment_line' => $isSundayClose
                ? 'Semana cerrada: '.$weekVisits.' de '.$effectiveWeeklyGoal.' sesiones.'
                : 'Avance actual: '.$weekVisits.' de '.$effectiveWeeklyGoal.' sesiones.',
            'rest_line' => 'Dias evaluables esta semana: '.$evaluableDays.'.',
            'alerts' => array_slice($alerts, 0, 3),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildMonthTimeline(Collection $recentAttendances, Carbon $nowAtGym, int $daysPerWeek, string $visibleFromDate = ''): array
    {
        $attendanceDateSet = $recentAttendances
            ->map(fn ($attendance): string => $this->extractDateValue($attendance))
            ->filter(fn (string $date): bool => $date !== '')
            ->unique()
            ->flip();

        $timeline = [];
        $visibleFromDate = $this->normalizeDateString($visibleFromDate);
        $hasVisibleStart = $visibleFromDate !== '';
        $todayDate = $nowAtGym->copy()->startOfDay()->toDateString();
        $daysPerWeek = max(3, min(7, $daysPerWeek));
        $placeholderCell = [
            'date' => '',
            'label' => '',
            'weekday_short' => '',
            'status' => 'pending',
            'attended' => false,
            'expected' => false,
            'is_today' => false,
            'is_placeholder' => true,
        ];

        $rangeStart = $nowAtGym->copy()->startOfMonth()->toDateString();
        $rangeEnd = $nowAtGym->copy()->endOfMonth()->toDateString();
        $expectedTrainingDateSet = [];

        $cursorWeekStart = Carbon::parse($rangeStart)->startOfWeek(Carbon::MONDAY);
        $lastWeekStart = Carbon::parse($rangeEnd)->startOfWeek(Carbon::MONDAY);
        while ($cursorWeekStart->lte($lastWeekStart)) {
            $weekStartDate = $cursorWeekStart->toDateString();
            $weekEndDate = $cursorWeekStart->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

            $evaluationStart = $weekStartDate;
            if ($hasVisibleStart && $visibleFromDate > $evaluationStart) {
                $evaluationStart = $visibleFromDate;
            }

            $evaluationWindowEnd = min($weekEndDate, $todayDate);
            if ($evaluationStart <= $evaluationWindowEnd) {
                $evaluationDates = [];
                $dayCursor = Carbon::parse($evaluationStart)->startOfDay();
                $weekEndCarbon = Carbon::parse($evaluationWindowEnd)->startOfDay();
                while ($dayCursor->lte($weekEndCarbon)) {
                    $currentDate = $dayCursor->toDateString();
                    if ($currentDate >= $rangeStart && $currentDate <= $rangeEnd) {
                        $evaluationDates[] = $currentDate;
                    }
                    $dayCursor->addDay();
                }

                $effectiveGoal = min($daysPerWeek, count($evaluationDates));
                for ($index = 0; $index < $effectiveGoal; $index++) {
                    $expectedDate = $evaluationDates[$index] ?? '';
                    if ($expectedDate !== '') {
                        $expectedTrainingDateSet[$expectedDate] = true;
                    }
                }
            }

            $cursorWeekStart->addWeek();
        }

        $startDate = Carbon::parse($rangeStart)->startOfDay();
        $startDayOfWeek = (int) $startDate->format('N');
        for ($padding = 1; $padding < $startDayOfWeek; $padding++) {
            $timeline[] = $placeholderCell;
        }

        $totalDays = (int) Carbon::parse($rangeStart)->diffInDays(Carbon::parse($rangeEnd));
        $dayLabels = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];

        for ($offset = 0; $offset <= $totalDays; $offset++) {
            $date = $startDate->copy()->addDays($offset);
            $dateString = $date->toDateString();
            $dayOfWeek = (int) $date->format('N');
            $isToday = $dateString === $todayDate;
            $trained = $attendanceDateSet->has($dateString);
            $isExpectedTrainingDay = isset($expectedTrainingDateSet[$dateString]);
            $isBeforeMembership = $hasVisibleStart && $dateString < $visibleFromDate;
            $isFutureDate = $dateString > $todayDate;
            $status = 'pending';
            if (! $isBeforeMembership && ! $isFutureDate) {
                $status = 'rest';
                if ($trained) {
                    $status = 'trained';
                } elseif (! $isToday && $isExpectedTrainingDay) {
                    $status = 'missed';
                }
            }

            $timeline[] = [
                'date' => $dateString,
                'label' => (string) $date->format('j'),
                'weekday_short' => $dayLabels[$dayOfWeek] ?? '',
                'status' => $status,
                'attended' => $trained,
                'expected' => $isExpectedTrainingDay,
                'is_today' => $isToday,
                'is_placeholder' => false,
            ];
        }

        while (count($timeline) % 7 !== 0) {
            $timeline[] = $placeholderCell;
        }

        return $timeline;
    }

    private function resolveTrainingSummary(?ClientFitnessProfile $fitnessProfile, array $weeklyGoal, array $snapshotSections): array
    {
        $snapshot = $snapshotSections['training_plan'] ?? null;
        if (is_array($snapshot) && ($snapshot['ready'] ?? false)) {
            return $snapshot;
        }

        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'title' => 'Orientacion de entrenamiento',
                'objective_line' => 'Sin perfil fisico cargado.',
                'focus_line' => 'Aun no hay enfoque disponible.',
                'rhythm_line' => 'Completa el perfil del cliente desde la app PWA.',
                'adaptation_line' => 'Despues podras ver una recomendacion orientativa.',
                'context_line' => 'Este bloque resume objetivo, frecuencia y alertas de continuidad.',
                'exercises' => [],
            ];
        }

        [$primaryGoal, $secondaryGoal] = FitnessGoalSupport::pair(
            (string) ($fitnessProfile->goal ?? ''),
            isset($fitnessProfile->secondary_goal) ? (string) $fitnessProfile->secondary_goal : null
        );
        $daysPerWeek = max(1, min(7, (int) ($fitnessProfile->days_per_week ?? 3)));
        $sessionMinutes = max(30, min(180, (int) ($fitnessProfile->session_minutes ?? 60)));
        $limitations = is_array($fitnessProfile->limitations ?? null)
            ? array_values(array_filter(array_map(
                static fn ($item): string => trim((string) $item),
                $fitnessProfile->limitations
            )))
            : [];

        $completionPercent = (int) ($weeklyGoal['completion_percent'] ?? 0);
        $adaptationLine = 'Ritmo estable. Mantener la frecuencia actual.';
        if ($completionPercent < 50) {
            $adaptationLine = 'Conviene reforzar adherencia antes de subir intensidad.';
        } elseif ($completionPercent >= 100) {
            $adaptationLine = 'Buena adherencia. Puede sostener una progresion gradual.';
        }

        return [
            'ready' => true,
            'title' => 'Orientacion de entrenamiento',
            'objective_line' => 'Objetivo: '.FitnessGoalSupport::summaryLabel($primaryGoal, $secondaryGoal, 'General'),
            'focus_line' => 'Enfoque sugerido: '.FitnessGoalSupport::focusLabel($primaryGoal, $secondaryGoal, 'Acondicionamiento general'),
            'rhythm_line' => 'Frecuencia configurada: '.$daysPerWeek.' dias/semana | Sesion base: '.$sessionMinutes.' min.',
            'adaptation_line' => $adaptationLine,
            'context_line' => $limitations === []
                ? FitnessGoalSupport::trackLine($primaryGoal, $secondaryGoal)
                : 'Limitaciones reportadas: '.implode(', ', $limitations).'. '.FitnessGoalSupport::trackLine($primaryGoal, $secondaryGoal),
            'exercises' => [],
        ];
    }

    private function buildPerformanceSummary(array $membershipMeta, array $prediction, array $weeklyGoal, ?int $daysSinceLast): array
    {
        $consistency = max(0, min(100, (int) ($prediction['consistency_percent'] ?? 0)));
        $weeklyCompletion = max(0, min(100, (int) ($weeklyGoal['completion_percent'] ?? 0)));
        $recencyScore = $daysSinceLast === null ? 20 : max(0, 100 - min(100, $daysSinceLast * 12));

        $score = (int) round(($consistency * 0.45) + ($weeklyCompletion * 0.35) + ($recencyScore * 0.20));

        $membershipTone = (string) ($membershipMeta['tone'] ?? 'muted');
        if ($membershipTone === 'danger') {
            $score -= 25;
        } elseif ($membershipTone === 'warning') {
            $score -= 10;
        }
        $score = max(0, min(100, $score));

        $label = 'Seguimiento bajo';
        $tone = 'danger';
        $summary = 'El cliente necesita seguimiento cercano para recuperar ritmo.';
        if ($score >= 80) {
            $label = 'Alto rendimiento';
            $tone = 'success';
            $summary = 'El cliente mantiene consistencia alta y buen ritmo semanal.';
        } elseif ($score >= 60) {
            $label = 'Buen ritmo';
            $tone = 'info';
            $summary = 'El avance es estable y puede sostener renovacion y continuidad.';
        } elseif ($score >= 40) {
            $label = 'Seguimiento necesario';
            $tone = 'warning';
            $summary = 'Hay senales de baja adherencia o riesgo de pausa.';
        }

        return [
            'score' => $score,
            'label' => $label,
            'tone' => $tone,
            'summary' => $summary,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function buildAlerts(array $membershipMeta, ?ClientFitnessProfile $fitnessProfile, array $weeklyGoal, array $bodyState, array $prediction): array
    {
        $alerts = [];

        $membershipTone = (string) ($membershipMeta['tone'] ?? 'muted');
        if ($membershipTone === 'danger') {
            $alerts[] = [
                'tone' => 'danger',
                'title' => 'Renovacion pendiente',
                'text' => (string) ($membershipMeta['status_line'] ?? 'El cliente requiere una accion sobre su membresia.'),
            ];
        } elseif ($membershipTone === 'warning') {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Renovar pronto',
                'text' => 'La membresia esta por vencer. Conviene contactarlo antes de la fecha limite.',
            ];
        }

        if (! $fitnessProfile) {
            $alerts[] = [
                'tone' => 'info',
                'title' => 'Sin perfil fisico',
                'text' => 'La PWA aun no tiene datos fisicos suficientes para una lectura completa.',
            ];
        }

        if ((int) ($weeklyGoal['completion_percent'] ?? 0) >= 100) {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Meta cumplida',
                'text' => 'El cliente completo su objetivo semanal actual.',
            ];
        } elseif ((int) ($weeklyGoal['remaining'] ?? 0) > 0 && (int) ($weeklyGoal['days_left_week'] ?? 7) <= 1) {
            $alerts[] = [
                'tone' => 'warning',
                'title' => 'Meta en riesgo',
                'text' => 'Le faltan '.(int) ($weeklyGoal['remaining'] ?? 0).' sesiones y queda poco tiempo esta semana.',
            ];
        }

        if (isset($bodyState['days_since_last']) && $bodyState['days_since_last'] !== null && (int) $bodyState['days_since_last'] >= 5) {
            $alerts[] = [
                'tone' => 'danger',
                'title' => 'Inactividad',
                'text' => 'Lleva '.(int) $bodyState['days_since_last'].' dias sin entrenar.',
            ];
        }

        if ((int) ($prediction['consistency_percent'] ?? 0) >= 85) {
            $alerts[] = [
                'tone' => 'success',
                'title' => 'Constancia alta',
                'text' => 'Su adherencia del mes es fuerte y vale la pena reforzar seguimiento positivo.',
            ];
        }

        return array_slice($alerts, 0, 4);
    }

    private function buildProfileMeta(?ClientFitnessProfile $fitnessProfile): array
    {
        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'goal_label' => 'Sin objetivo registrado',
                'primary_goal_label' => 'Sin objetivo registrado',
                'secondary_goal_label' => null,
                'experience_label' => 'Sin nivel',
                'days_per_week' => null,
                'session_minutes' => null,
                'weight_label' => 'N/A',
                'height_label' => 'N/A',
                'limitations_label' => 'Sin datos',
                'updated_label' => null,
            ];
        }

        $limitations = is_array($fitnessProfile->limitations ?? null)
            ? array_values(array_filter(array_map(
                static fn ($item): string => trim((string) $item),
                $fitnessProfile->limitations
            )))
            : [];

        [$primaryGoal, $secondaryGoal] = FitnessGoalSupport::pair(
            (string) ($fitnessProfile->goal ?? ''),
            isset($fitnessProfile->secondary_goal) ? (string) $fitnessProfile->secondary_goal : null
        );

        return [
            'ready' => true,
            'goal_label' => FitnessGoalSupport::summaryLabel($primaryGoal, $secondaryGoal, 'Sin objetivo registrado'),
            'primary_goal_label' => FitnessGoalSupport::label($primaryGoal, 'Sin objetivo registrado'),
            'secondary_goal_label' => $secondaryGoal !== null ? FitnessGoalSupport::label($secondaryGoal, '') : null,
            'experience_label' => $this->experienceLabel((string) ($fitnessProfile->experience_level ?? '')),
            'days_per_week' => (int) ($fitnessProfile->days_per_week ?? 0),
            'session_minutes' => (int) ($fitnessProfile->session_minutes ?? 0),
            'weight_label' => $fitnessProfile->weight_kg !== null ? number_format((float) $fitnessProfile->weight_kg, 1, '.', '').' kg' : 'N/A',
            'height_label' => $fitnessProfile->height_cm !== null ? (string) ((int) $fitnessProfile->height_cm).' cm' : 'N/A',
            'limitations_label' => $limitations === [] ? 'Sin limitaciones' : implode(', ', $limitations),
            'updated_label' => $fitnessProfile->updated_at?->format('Y-m-d H:i'),
        ];
    }

    private function formatLastAttendanceLabel(mixed $lastAttendance, Carbon $today): string
    {
        $dateValue = trim((string) ($lastAttendance?->date?->toDateString() ?? ''));
        if ($dateValue === '') {
            return 'Sin asistencia';
        }

        $date = Carbon::parse($dateValue)->startOfDay();
        $timeValue = trim((string) ($lastAttendance?->time ?? ''));
        $timeLabel = $timeValue !== '' ? mb_substr($timeValue, 0, 5) : '--:--';

        if ($date->equalTo($today)) {
            return 'Hoy '.$timeLabel;
        }

        $daysAgo = (int) $date->diffInDays($today);
        if ($daysAgo <= 30) {
            return ($daysAgo === 1 ? 'Hace 1 dia' : 'Hace '.$daysAgo.' dias').' '.$timeLabel;
        }

        return $date->format('Y-m-d').' '.$timeLabel;
    }

    private function goalLabel(string $goal): string
    {
        return FitnessGoalSupport::label($goal, 'General');
    }

    private function experienceLabel(string $level): string
    {
        return match (trim($level)) {
            'principiante' => 'Principiante',
            'intermedio' => 'Intermedio',
            'avanzado' => 'Avanzado',
            default => 'Sin nivel',
        };
    }

    private function formatMonthLabel(Carbon $nowAtGym): string
    {
        $monthNames = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return ($monthNames[(int) $nowAtGym->format('n')] ?? $nowAtGym->format('F')).' '.$nowAtGym->format('Y');
    }

    private function normalizeDateString(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return '';
        }
    }

    private function extractDateValue(mixed $attendance): string
    {
        if (is_array($attendance)) {
            $dateValue = $attendance['date'] ?? '';

            return $this->normalizeDateString((string) $dateValue);
        }

        return $this->normalizeDateString((string) ($attendance?->date?->toDateString() ?? ''));
    }
}
