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
                'title' => 'Plan básico',
                'summary' => 'Ordena recepción, clientes, membresías y caja en una sola sede.',
                'features' => [
                    'Recepción y check-in (documento, QR y RFID)',
                    'Clientes, credenciales y membresías',
                    'Caja por turnos con ingresos y egresos',
                    'Acceso del administrador principal',
                    'Panel web operativo (sin PWA instalable)',
                ],
                'ideal_for' => 'Tienes una sola sede y quieres ordenar cobros, recepción y membresías.',
                'ops_focus' => 'Cobrar, registrar clientes y llevar la caja sin enredos.',
                'setup_note' => 'Empiezas rápido con lo esencial para operar bien.',
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'profesional' => [
                'title' => 'Plan profesional',
                'summary' => 'Suma reportes, promociones y operación móvil para trabajar con más control.',
                'features' => [
                    'Todo lo del Plan básico',
                    '1 cajero operativo (panel, recepción, clientes y cobros)',
                    'Activa, desactiva o elimina cajeros sin perder historial',
                    'Permisos por cajero: abrir caja, cerrar caja y registrar cobros',
                    'Por defecto el dueño abre/cierra caja; cajero cobra',
                    'Promociones en planes y ventas de membresías',
                    'Reportes base (ingresos, asistencias y membresías)',
                    'Exportacion de reportes CSV y PDF',
                    'PWA instalable en celular, tablet y escritorio',
                    'No incluye cuentas cliente con usuario y contraseña',
                ],
                'ideal_for' => 'Ya tienes movimiento diario y quieres más control, reportes y promociones.',
                'ops_focus' => 'Recepción, caja y reportes para trabajar con más orden.',
                'setup_note' => 'Subes de nivel sin cambiar tu forma de trabajo.',
                'featured' => true,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'premium' => [
                'title' => 'Plan premium',
                'summary' => 'Te da una operación más completa, con mejor experiencia para clientes y más control del equipo.',
                'features' => [
                    'Todo lo del Plan profesional',
                    '2 cajeros operativos incluidos',
                    'Control avanzado de permisos de caja por cada cajero',
                    'Eliminacion permanente de cajero con historial conservado',
                    'Reportes base y exportaciones CSV/PDF',
                    'Flujo completo de recepción, clientes, planes y caja',
                    'Preparado para más movimiento diario',
                    'Incluido: Cuentas cliente con usuario y contraseña para app móvil PWA',
                    'Incluido: Registro de asistencia desde app móvil PWA con QR dinámico temporal',
                ],
                'ideal_for' => 'Tu gimnasio mueve más clientes, más personal y quieres dar una mejor experiencia.',
                'ops_focus' => 'Control completo del negocio y acceso digital para tus clientes.',
                'setup_note' => 'Ideal si ya quieres automatizar más y tener más control.',
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'sucursales' => [
                'title' => 'Plan sucursales',
                'summary' => 'Controla varias sedes desde un solo sistema y revisa cada sucursal sin perder el orden.',
                'features' => [
                    'Todo lo del Plan premium',
                    '2 cajeros por cada sucursal activa',
                    'Permisos de caja por cajero en cada sucursal',
                    'Activar/desactivar/eliminar cajeros con historial protegido',
                    'Módulo de sucursales y enlaces sede principal/sucursal',
                    'Contexto por sede y vista global multi-gym',
                    'Reportes por sede o en alcance global',
                    'Incluido: Cuentas cliente con usuario y contraseña para app móvil PWA por sede',
                    'Incluido: Registro de asistencia desde app móvil PWA con QR dinámico temporal por sucursal',
                ],
                'ideal_for' => 'Tienes dos o más sedes y quieres ver todo sin perder el control.',
                'ops_focus' => 'Controlar varias sedes desde una sola administración.',
                'setup_note' => 'Se adapta a la cantidad de sedes y al tamaño de tu operación.',
                'featured' => false,
                'contact_mode' => true,
                'cta' => 'Solicita tu cotización',
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
            'summary' => 'Plan disponible para tu operación.',
            'features' => [],
            'ideal_for' => 'Operación en crecimiento.',
            'ops_focus' => 'Control operativo.',
            'setup_note' => 'Configuración según necesidad.',
            'featured' => false,
            'contact_mode' => false,
            'cta' => 'Demo gratis',
        ];
    }
}
