<?php

namespace App\Support;

class LegalTerms
{
    public const Versión = '2026-02-26';

    /**
     * @return array<string, array{label:string,summary:string,points:list<string>}>
     */
    public static function documents(): array
    {
        return [
            'privacy_policy' => [
                'label' => 'Política de privacidad',
                'summary' => 'Tratamiento de datos personales y operativos para brindar el servicio.',
                'points' => [
                    'Se recopilan datos necesarios para operación, soporte y seguridad.',
                    'Se registran evidencias técnicas (IP, navegador, fecha/hora) para auditoría legal.',
                    'El titular puede solicitar actualización o eliminación según normativa aplicable.',
                ],
            ],
            'service_terms' => [
                'label' => 'Condiciones de servicio',
                'summary' => 'Reglas de uso de la plataforma, responsabilidades y alcance técnico.',
                'points' => [
                    'El uso debe cumplir leyes y políticas internas del gimnasio.',
                    'Cada usuario es responsable de proteger sus credenciales.',
                    'Puede haber mantenimientos y límites razonables de disponibilidad.',
                ],
            ],
            'commercial_terms' => [
                'label' => 'Términos comerciales',
                'summary' => 'Condiciones de planes, pagos, renovaciones y suspensión del servicio.',
                'points' => [
                    'La continuidad del servicio depende del plan y pagos vigentes.',
                    'La renovación y períodos de gracia siguen política comercial definida.',
                    'La facturación y comprobantes se emiten con los datos suministrados.',
                ],
            ],
        ];
    }

    /**
     * @return list<array{key:string,label:string,summary:string,points:list<string>}>
     */
    public static function orderedDocuments(): array
    {
        $orderedKeys = ['privacy_policy', 'service_terms', 'commercial_terms'];
        $catalog = self::documents();
        $rows = [];
        foreach ($orderedKeys as $key) {
            $item = $catalog[$key] ?? null;
            if (! is_array($item)) {
                continue;
            }
            $rows[] = [
                'key' => $key,
                'label' => (string) ($item['label'] ?? $key),
                'summary' => (string) ($item['summary'] ?? ''),
                'points' => array_values(array_map('strval', (array) ($item['points'] ?? []))),
            ];
        }

        return $rows;
    }
}
