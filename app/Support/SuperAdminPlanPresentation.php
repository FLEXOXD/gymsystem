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
                'summary' => 'Ordena recepcion, clientes, membresias y caja en una sola sede.',
                'features' => [
                    'Recepcion y check-in (documento, QR y RFID)',
                    'Clientes, credenciales y membresias',
                    'Caja por turnos con ingresos y egresos',
                    'Acceso del administrador principal',
                    'Panel web operativo (sin PWA instalable)',
                ],
                'ideal_for' => 'Tienes una sola sede y quieres ordenar cobros, recepcion y membresias.',
                'ops_focus' => 'Cobrar, registrar clientes y llevar la caja sin enredos.',
                'setup_note' => 'Empiezas rapido con lo esencial para operar bien.',
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'profesional' => [
                'title' => 'Plan profesional',
                'summary' => 'Suma reportes, promociones, PWA y ventas de productos para trabajar con mas control.',
                'features' => [
                    'Todo lo del Plan basico',
                    '1 cajero operativo (panel, recepcion, clientes y cobros)',
                    'Activa, desactiva o elimina cajeros sin perder historial',
                    'Permisos por cajero: abrir caja, cerrar caja y registrar cobros',
                    'Por defecto el dueno abre/cierra caja; cajero cobra',
                    'Promociones en planes y ventas de membresias',
                    'Reportes base (ingresos, asistencias y membresias)',
                    'Exportacion de reportes CSV y PDF',
                    'Ventas e inventario con productos, stock y alertas',
                    'Reporte diario, semanal y mensual de productos',
                    'PWA instalable en celular, tablet y escritorio',
                    'No incluye cuentas cliente con usuario y contrasena',
                ],
                'ideal_for' => 'Ya tienes movimiento diario y quieres mas control, reportes y nuevas ventas dentro del gym.',
                'ops_focus' => 'Recepcion, caja, productos y reportes para trabajar con mas orden.',
                'setup_note' => 'Subes de nivel sin cambiar tu forma de trabajo.',
                'featured' => true,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'premium' => [
                'title' => 'Plan premium',
                'summary' => 'Te da una operacion mas completa, con mejor experiencia para clientes y mas control comercial.',
                'features' => [
                    'Todo lo del Plan profesional',
                    '2 cajeros operativos incluidos',
                    'Control avanzado de permisos de caja por cada cajero',
                    'Eliminacion permanente de cajero con historial conservado',
                    'Reportes base y exportaciones CSV/PDF',
                    'Flujo completo de recepcion, clientes, planes y caja',
                    'Preparado para mas movimiento diario',
                    'Panel de ventas e inventario con mas control comercial',
                    'Catalogo de productos con utilidad estimada y stock bajo',
                    'Incluido: Cuentas cliente con usuario y contrasena para app movil PWA',
                    'Incluido: Registro de asistencia desde app movil PWA con QR dinamico temporal',
                ],
                'ideal_for' => 'Tu gimnasio mueve mas clientes, mas personal y quieres vender mejor sin perder control.',
                'ops_focus' => 'Control completo del negocio, clientes y ventas del gimnasio.',
                'setup_note' => 'Ideal si ya quieres automatizar mas y tener mas control.',
                'featured' => false,
                'contact_mode' => false,
                'cta' => 'Demo gratis',
            ],
            'sucursales' => [
                'title' => 'Plan sucursales',
                'summary' => 'Controla varias sedes desde un solo sistema y revisa clientes, caja y productos sin perder el orden.',
                'features' => [
                    'Todo lo del Plan premium',
                    '2 cajeros por cada sucursal activa',
                    'Permisos de caja por cajero en cada sucursal',
                    'Activar/desactivar/eliminar cajeros con historial protegido',
                    'Modulo de sucursales y enlaces sede principal/sucursal',
                    'Contexto por sede y vista global multi-gym',
                    'Reportes por sede o en alcance global',
                    'Ventas e inventario por sucursal con lectura global',
                    'Incluido: Cuentas cliente con usuario y contrasena para app movil PWA por sede',
                    'Incluido: Registro de asistencia desde app movil PWA con QR dinamico temporal por sucursal',
                ],
                'ideal_for' => 'Tienes dos o mas sedes y quieres ver todo sin perder el control.',
                'ops_focus' => 'Controlar varias sedes desde una sola administracion.',
                'setup_note' => 'Se adapta a la cantidad de sedes y al tamano de tu operacion.',
                'featured' => false,
                'contact_mode' => true,
                'cta' => 'Solicita tu cotizacion',
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
            'ideal_for' => 'Operacion en crecimiento.',
            'ops_focus' => 'Control operativo.',
            'setup_note' => 'Configuracion segun necesidad.',
            'featured' => false,
            'contact_mode' => false,
            'cta' => 'Demo gratis',
        ];
    }
}
