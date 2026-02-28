# Arquitectura Ideal (Laravel 12.52 / PHP 8.3)

Fecha: 2026-02-27

## Objetivo
Estandarizar GymSystem en una arquitectura por capas, incremental y compatible con el código actual.

## Capas propuestas

### 1) HTTP/UI
- Controllers: solo orquestan flujo, autorizaciones de alto nivel y respuestas.
- FormRequests: validación y mensajes de error.
- Blade/Componentes: rendering y UX.

### 2) Aplicación (Use Cases)
- `Actions` por caso de uso:
  - Registrar cliente
  - Abrir turno de caja
  - Registrar movimiento de caja
  - Cerrar turno de caja
- Los controllers llaman Actions, no contienen reglas de negocio pesadas.

### 3) Dominio (DDD Light)
- Servicios de dominio para reglas puras:
  - Vigencia de membresía
  - Estado de membresía (active/expired)
  - Construcción de descripciones de movimientos de cobro
- Sin dependencia de HTTP.

### 4) Infraestructura
- Eloquent Models + Query Builder
- Integraciones (PDF, mail, storage)
- Middlewares y logging operacional

## Patrón objetivo incremental
`Controller -> Action -> Domain Service -> Model/Service`

## Aplicación inicial realizada
- `ClientController::store` ahora delega en `App\Modules\Clients\Actions\RegisterClientAction`.
- `CashController` delega casos de uso a Actions de módulo Cash.
- Lecturas de resumen/totales de caja pasan a `CashSessionReadService`.

## Convenciones recomendadas
- Un Action por caso de uso transaccional.
- FormRequests para toda entrada mutante.
- Modelos sin lógica de presentación.
- Mensajes de validación en español profesional.

## Próximos pasos
1. Extraer `ClientController::index/show` a Actions de lectura.
2. Mover reglas de promociones a un Domain Service dedicado.
3. Aplicar el mismo patrón en Reception y Reports.