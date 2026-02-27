<?php

namespace App\Support;

class SuperAdminPlanPresentation
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function metadata(): array
    {
        return [
            'basico' => [
                'title' => 'Plan basico',
                'summary' => 'Para gimnasios que necesitan control diario esencial y cobros sin complicaciones.',
                'features' => [
                    'Recepcion y asistencias',
                    'Clientes y membresias basicas',
                    'Cobros y POS basico',
                    'Caja diaria simple',
                ],
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'profesional' => [
                'title' => 'Plan profesional',
                'summary' => 'Ideal para gimnasios que empiezan y quieren un control diario claro.',
                'features' => [
                    'Recepcion, clientes y asistencias',
                    'Membresias con control de vencimientos',
                    'Caja diaria y reportes operativos base',
                    'PWA instalable en celular, tablet y escritorio',
                    'Soporte comercial por WhatsApp',
                ],
                'featured' => true,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'premium' => [
                'title' => 'Plan premium',
                'summary' => 'Para gimnasios que buscan mas control, analitica y mejor experiencia operativa.',
                'features' => [
                    'Todo lo del Plan profesional',
                    'Reportes avanzados',
                    'Aplicativo movil PWA en celulares para clientes',
                    'Experiencia PWA en celular, tablet y escritorio',
                    'Soporte prioritario',
                ],
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'sucursales' => [
                'title' => 'Plan sucursales',
                'summary' => 'Pensado para administrar varios gimnasios desde un control central en un solo sistema.',
                'features' => [
                    'Tambien todo lo del Plan premium',
                    'Panel para multiples sucursales',
                    'Control independiente por cada gimnasio',
                    'Reportes consolidados por sede y globales',
                    'Acompanamiento para expansion multi sede',
                ],
                'featured' => false,
                'contact_mode' => true,
                'cta' => 'Hablar por WhatsApp',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function for(string $planKey): array
    {
        $meta = self::metadata();

        return $meta[$planKey] ?? [
            'title' => 'Plan',
            'summary' => 'Plan disponible para tu operacion.',
            'features' => [],
            'featured' => false,
            'contact_mode' => false,
            'cta' => 'Demo gratis',
        ];
    }
}
