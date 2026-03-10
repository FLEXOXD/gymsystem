<?php

namespace App\Support;

final class FitnessGoalSupport
{
    public const GOAL_OPTIONS = [
        'ganar_musculo' => 'Ganar musculo',
        'perder_grasa' => 'Perder grasa',
        'mantener_forma' => 'Mantener forma',
        'definir' => 'Definir',
        'aumentar_fuerza' => 'Aumentar fuerza',
        'mejorar_resistencia' => 'Mejorar resistencia',
    ];

    private const TRACK_MAP = [
        'ganar_musculo' => 'superavit calorico y progresion de fuerza',
        'perder_grasa' => 'deficit calorico con mantenimiento muscular',
        'mantener_forma' => 'balance calorico y constancia semanal',
        'definir' => 'recomposicion corporal y volumen de entrenamiento',
        'aumentar_fuerza' => 'ejercicios compuestos y cargas progresivas',
        'mejorar_resistencia' => 'volumen semanal y capacidad cardiovascular',
    ];

    private const FOCUS_MAP = [
        'ganar_musculo' => 'Hipertrofia y volumen controlado',
        'perder_grasa' => 'Circuitos metabolicos y trabajo global',
        'mantener_forma' => 'Acondicionamiento general',
        'definir' => 'Tono muscular y gasto energetico',
        'aumentar_fuerza' => 'Basicos pesados y tecnica',
        'mejorar_resistencia' => 'Capacidad aerobica y tolerancia al esfuerzo',
    ];

    private const CALORIE_ADJUSTMENT_MAP = [
        'ganar_musculo' => 280,
        'perder_grasa' => -320,
        'mantener_forma' => 0,
        'definir' => -180,
        'aumentar_fuerza' => 160,
        'mejorar_resistencia' => 80,
    ];

    private const INTENSITY_BONUS_MAP = [
        'ganar_musculo' => 6,
        'perder_grasa' => 4,
        'mantener_forma' => 2,
        'definir' => 4,
        'aumentar_fuerza' => 6,
        'mejorar_resistencia' => 5,
    ];

    private const STRENGTH_BONUS_MAP = [
        'ganar_musculo' => 10,
        'perder_grasa' => 4,
        'mantener_forma' => 4,
        'definir' => 6,
        'aumentar_fuerza' => 12,
        'mejorar_resistencia' => 3,
    ];

    private const RESISTANCE_BONUS_MAP = [
        'ganar_musculo' => 4,
        'perder_grasa' => 8,
        'mantener_forma' => 6,
        'definir' => 8,
        'aumentar_fuerza' => 4,
        'mejorar_resistencia' => 12,
    ];

    private const PREDICTION_FACTOR_MAPS = [
        'muscle_gain' => [
            'ganar_musculo' => 1.0,
            'perder_grasa' => 0.22,
            'mantener_forma' => 0.40,
            'definir' => 0.52,
            'aumentar_fuerza' => 0.82,
            'mejorar_resistencia' => 0.28,
        ],
        'fat_loss' => [
            'ganar_musculo' => 0.18,
            'perder_grasa' => 1.0,
            'mantener_forma' => 0.30,
            'definir' => 0.82,
            'aumentar_fuerza' => 0.20,
            'mejorar_resistencia' => 0.42,
        ],
        'strength' => [
            'ganar_musculo' => 0.86,
            'perder_grasa' => 0.36,
            'mantener_forma' => 0.46,
            'definir' => 0.58,
            'aumentar_fuerza' => 1.0,
            'mejorar_resistencia' => 0.42,
        ],
        'resistance' => [
            'ganar_musculo' => 0.32,
            'perder_grasa' => 0.72,
            'mantener_forma' => 0.54,
            'definir' => 0.64,
            'aumentar_fuerza' => 0.28,
            'mejorar_resistencia' => 1.0,
        ],
    ];

    public static function options(): array
    {
        return self::GOAL_OPTIONS;
    }

    public static function normalize(?string $goal): ?string
    {
        $goal = mb_strtolower(trim((string) $goal));
        if ($goal === '') {
            return null;
        }

        return array_key_exists($goal, self::GOAL_OPTIONS) ? $goal : null;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    public static function pair(?string $primaryGoal, ?string $secondaryGoal): array
    {
        $primaryGoal = self::normalize($primaryGoal);
        $secondaryGoal = self::normalize($secondaryGoal);

        if ($primaryGoal === null) {
            return [null, null];
        }

        if ($secondaryGoal === null || $secondaryGoal === $primaryGoal) {
            return [$primaryGoal, null];
        }

        return [$primaryGoal, $secondaryGoal];
    }

    /**
     * @return array<string, float>
     */
    public static function weights(?string $primaryGoal, ?string $secondaryGoal): array
    {
        [$primaryGoal, $secondaryGoal] = self::pair($primaryGoal, $secondaryGoal);

        if ($primaryGoal === null) {
            return [];
        }

        if ($secondaryGoal === null) {
            return [$primaryGoal => 1.0];
        }

        return [
            $primaryGoal => 0.70,
            $secondaryGoal => 0.30,
        ];
    }

    public static function weightedValue(?string $primaryGoal, ?string $secondaryGoal, array $map, float $default = 0.0): float
    {
        $weights = self::weights($primaryGoal, $secondaryGoal);
        if ($weights === []) {
            return $default;
        }

        $total = 0.0;
        foreach ($weights as $goal => $weight) {
            $total += ((float) ($map[$goal] ?? $default)) * $weight;
        }

        return $total;
    }

    public static function calorieAdjustment(?string $primaryGoal, ?string $secondaryGoal): int
    {
        return (int) round(self::weightedValue($primaryGoal, $secondaryGoal, self::CALORIE_ADJUSTMENT_MAP, 0.0));
    }

    public static function intensityBonus(?string $primaryGoal, ?string $secondaryGoal): int
    {
        return (int) round(self::weightedValue($primaryGoal, $secondaryGoal, self::INTENSITY_BONUS_MAP, 2.0));
    }

    public static function strengthBonus(?string $primaryGoal, ?string $secondaryGoal): int
    {
        return (int) round(self::weightedValue($primaryGoal, $secondaryGoal, self::STRENGTH_BONUS_MAP, 4.0));
    }

    public static function resistanceBonus(?string $primaryGoal, ?string $secondaryGoal): int
    {
        return (int) round(self::weightedValue($primaryGoal, $secondaryGoal, self::RESISTANCE_BONUS_MAP, 4.0));
    }

    public static function predictionFactor(string $metric, ?string $primaryGoal, ?string $secondaryGoal): float
    {
        $map = self::PREDICTION_FACTOR_MAPS[$metric] ?? [];

        return self::weightedValue($primaryGoal, $secondaryGoal, $map, 0.40);
    }

    public static function label(?string $goal, string $default = 'General'): string
    {
        $goal = self::normalize($goal);

        return $goal !== null ? (self::GOAL_OPTIONS[$goal] ?? $default) : $default;
    }

    public static function lowerLabel(?string $goal, string $default = 'general'): string
    {
        return mb_strtolower(self::label($goal, $default));
    }

    public static function summaryLabel(?string $primaryGoal, ?string $secondaryGoal, string $default = 'General'): string
    {
        [$primaryGoal, $secondaryGoal] = self::pair($primaryGoal, $secondaryGoal);

        if ($primaryGoal === null) {
            return $default;
        }

        if ($secondaryGoal === null) {
            return self::label($primaryGoal, $default);
        }

        return self::label($primaryGoal, $default).' + '.self::label($secondaryGoal, $default);
    }

    public static function focusLabel(?string $primaryGoal, ?string $secondaryGoal, string $default = 'Acondicionamiento general'): string
    {
        [$primaryGoal, $secondaryGoal] = self::pair($primaryGoal, $secondaryGoal);

        if ($primaryGoal === null) {
            return $default;
        }

        $primaryFocus = self::FOCUS_MAP[$primaryGoal] ?? $default;
        if ($secondaryGoal === null) {
            return $primaryFocus;
        }

        $secondaryFocus = self::FOCUS_MAP[$secondaryGoal] ?? $default;
        if ($secondaryFocus === $primaryFocus) {
            return $primaryFocus;
        }

        return $primaryFocus.' + '.$secondaryFocus;
    }

    public static function trackLine(?string $primaryGoal, ?string $secondaryGoal): string
    {
        [$primaryGoal, $secondaryGoal] = self::pair($primaryGoal, $secondaryGoal);

        if ($primaryGoal === null) {
            return 'Enfoque general de acondicionamiento fisico.';
        }

        if ($secondaryGoal === null) {
            return 'Enfoque en '.(self::TRACK_MAP[$primaryGoal] ?? 'acondicionamiento fisico general').'.';
        }

        $recompositionGoals = ['ganar_musculo', 'perder_grasa', 'definir'];
        if (in_array($primaryGoal, $recompositionGoals, true) && in_array($secondaryGoal, $recompositionGoals, true)) {
            return 'Enfoque en recomposicion corporal: '.self::label($primaryGoal).' como prioridad y '.self::lowerLabel($secondaryGoal).' como apoyo.';
        }

        return 'Objetivo mixto: '.self::label($primaryGoal).' como prioridad, con apoyo en '.self::lowerLabel($secondaryGoal).'.';
    }
}
