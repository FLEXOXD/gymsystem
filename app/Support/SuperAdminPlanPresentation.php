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
                'summary' => 'Operacion diaria esencial para controlar asistencia, clientes, membresias y caja en una sola sede.',
                'features' => [
                    'Recepcion y check-in (documento, QR y RFID)',
                    'Clientes, credenciales y membresias',
                    'Caja por turnos con ingresos y egresos',
                    'Sin cupo de cajeros (solo usuario administrador)',
                    'Panel web operativo (sin PWA instalable)',
                ],
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'profesional' => [
                'title' => 'Plan profesional',
                'summary' => 'Incluye todo lo esencial y habilita promociones, reportes y exportaciones en modo app instalable.',
                'features' => [
                    'Todo lo del Plan basico',
                    '1 cajero operativo (panel, recepcion, clientes y cobros)',
                    'Activa, desactiva o elimina cajeros sin perder historial',
                    'Permisos por cajero: abrir caja, cerrar caja y registrar cobros',
                    'Por defecto el dueno abre/cierra caja; cajero cobra',
                    'Promociones en planes y ventas de membresias',
                    'Reportes base (ingresos, asistencias y membresias)',
                    'Exportacion de reportes CSV y PDF',
                    'PWA instalable en celular, tablet y escritorio',
                ],
                'featured' => true,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'premium' => [
                'title' => 'Plan premium',
                'summary' => 'Pack premium con stack completo para operacion diaria, analitica y exportaciones.',
                'features' => [
                    'Todo lo del Plan profesional',
                    '2 cajeros operativos incluidos',
                    'Control avanzado de permisos de caja por cada cajero',
                    'Eliminacion permanente de cajero con historial conservado',
                    'Reportes base y exportaciones CSV/PDF',
                    'Flujo completo de recepcion, clientes, planes y caja',
                    'Ideal para operaciones con mayor volumen diario',
                    'Muy pronto: aplicativo movil PWA del gimnasio para cada cliente',
                ],
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'sucursales' => [
                'title' => 'Plan sucursales',
                'summary' => 'Gestion multi-sede para operar varias sucursales con control central desde la sede principal.',
                'features' => [
                    'Todo lo del Plan premium',
                    '2 cajeros por cada sucursal activa',
                    'Permisos de caja por cajero en cada sucursal',
                    'Activar/desactivar/eliminar cajeros con historial protegido',
                    'Modulo de sucursales y enlaces sede principal/sucursal',
                    'Contexto por sede y vista global multi-gym',
                    'Reportes por sede o en alcance global',
                    'Muy pronto: aplicativo movil PWA del gimnasio para cada cliente',
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
