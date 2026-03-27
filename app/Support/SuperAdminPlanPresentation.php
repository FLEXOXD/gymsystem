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
                'title' => 'Plan control',
                'summary' => 'Ordena recepción, clientes, membresías, caja y reportes base en una sola sede.',
                'features' => [
                    'Recepción y registro de acceso (documento, QR y RFID)',
                    'Clientes, credenciales y membresías',
                    'Caja por turnos con ingresos y egresos',
                    'Reportes base de ingresos, asistencias y membresías',
                    'Acceso del administrador principal',
                    'Panel web operativo para la sede principal',
                ],
                'ideal_for' => 'Tienes una sola sede y quieres ordenar cobros, recepción, membresías y reportes básicos.',
                'ops_focus' => 'Cobrar, registrar clientes y llevar la caja con una lectura clara del día.',
                'setup_note' => 'Empiezas rápido con lo esencial para operar bien desde una sola sede.',
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'profesional' => [
                'title' => 'Plan crecimiento',
                'summary' => 'Suma cajero, promociones, reportes, PWA y ventas con inventario para trabajar con más control.',
                'features' => [
                    'Todo lo del Plan control',
                    '1 cajero operativo con panel, recepción, clientes y cobros',
                    'Promociones, ventas e inventario con productos, stock y alertas',
                    'Reportes con exportación CSV y PDF, más PWA instalable',
                    'Permisos por cajero: abrir caja, cerrar caja y registrar cobros',
                    'Activa, desactiva o elimina cajeros sin perder historial',
                    'Por defecto el dueño abre o cierra caja; el cajero cobra',
                    'Reporte diario, semanal y mensual de productos',
                    'No incluye cuentas de cliente con usuario y contraseña',
                ],
                'ideal_for' => 'Ya tienes movimiento diario y quieres más control, reportes y nuevas ventas dentro del gimnasio.',
                'ops_focus' => 'Recepción, caja, productos y reportes para trabajar con más orden.',
                'setup_note' => 'Subes de nivel sin cambiar tu forma de trabajo ni salirte de una sola sede.',
                'featured' => true,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'premium' => [
                'title' => 'Plan elite',
                'summary' => 'Te da una operación más completa con 2 cajeros, acceso cliente y clases para vender mejor sin perder control.',
                'features' => [
                    'Todo lo del Plan crecimiento',
                    '2 cajeros operativos incluidos',
                    'Acceso cliente en app móvil PWA con usuario, contraseña y QR dinámico temporal',
                    'Creación y gestión de clases, cupos y reservas desde panel y app del cliente',
                    'Control avanzado de permisos de caja por cada cajero',
                    'Eliminación permanente de cajero con historial conservado',
                    'Panel de ventas e inventario con más control comercial',
                    'Catálogo de productos con utilidad estimada y stock bajo',
                    'Flujo completo de recepción, clientes, planes y caja',
                    'Preparado para más movimiento diario',
                    'Reportes base y exportaciones CSV y PDF',
                ],
                'ideal_for' => 'Tu gimnasio mueve más clientes, más personal y quieres vender mejor sin perder control.',
                'ops_focus' => 'Control completo del negocio, acceso cliente, clases y ventas del gimnasio.',
                'setup_note' => 'Ideal si ya quieres automatizar más áreas y dar mejor experiencia al cliente.',
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'sucursales' => [
                'title' => 'Plan sucursales',
                'summary' => 'Controla varias sedes desde un solo sistema con vista global, 2 cajeros por sucursal y clases por cada sede.',
                'features' => [
                    'Todo lo del Plan elite',
                    'Módulo de sucursales y vista global multisede',
                    '2 cajeros por cada sucursal activa',
                    'Acceso cliente, clases y reservas por cada sucursal',
                    'Permisos de caja por cajero en cada sucursal',
                    'Activar, desactivar o eliminar cajeros con historial protegido',
                    'Contexto por sede y vista global multisede',
                    'Reportes por sede o en alcance global',
                    'Ventas e inventario por sucursal con lectura global',
                    'Cuentas de cliente con usuario y contraseña para la app móvil PWA por sede',
                    'Registro de asistencia desde la app móvil PWA con QR dinámico temporal por sucursal',
                ],
                'ideal_for' => 'Tienes dos o más sedes y quieres ver agenda, caja, clientes y ventas sin perder el control.',
                'ops_focus' => 'Controlar varias sedes desde una sola administración con lectura global y por sede.',
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
