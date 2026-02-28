# DDD Light - Mapa de Dominios

Fecha: 2026-02-27

## Dominios definidos
- Identidad/Acceso
- Clientes
- Membresías
- Caja
- Recepción
- Reportes
- Suscripciones
- Marketing
- SuperAdmin

## Límites de contexto (sugeridos)
- Contexto Operación Gym: Clientes, Membresías, Caja, Recepción, Reportes
- Contexto Comercial: Marketing, Suscripciones
- Contexto Plataforma: Identidad/Acceso, SuperAdmin

## Aplicación inicial DDD Light (dominio: Clientes)

### Use Case (Application Layer)
- `App\Modules\Clients\Actions\RegisterClientAction`
  - Crea cliente
  - Inicia membresía opcional
  - Registra cobro en caja

### Domain Service
- `App\Modules\Clients\Services\ClientMembershipDomainService`
  - Calcula ventana de membresía (`starts_at` / `ends_at`)
  - Determina estado de membresía
  - Construye descripción de cobro

### Infraestructura
- Eloquent: `Client`, `Plan`, `Membership`, `CashMovement`
- Servicios existentes reutilizados: `PromotionService`, `CashSessionService`

## Regla de adopción
DDD Light, no reescritura total:
- Se conserva Eloquent e infraestructura actual.
- Se encapsulan reglas nuevas en servicios de dominio.
- Controllers actúan como adaptadores HTTP.

## Próximo dominio a migrar
- Caja: extraer reglas de cierre con diferencia y aprobación supervisor a Domain Service dedicado.