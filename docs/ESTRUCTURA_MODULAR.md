# Estructura Modular (sin romper Laravel)

Fecha: 2026-02-27

## Estructura objetivo

```text
app/
  Modules/
    Clients/
      Actions/
      Services/
      Http/
        Controllers/
        Requests/
    Cash/
      Actions/
      Services/
      Http/
        Controllers/
        Requests/
    Plans/
    Reception/
    Reports/
    SuperAdmin/
```

## Implementación incremental aplicada

### Módulo Clients
- `app/Modules/Clients/Actions/RegisterClientAction.php`
- `app/Modules/Clients/Services/ClientMembershipDomainService.php`
- Integrado en `ClientController::store`.

### Módulo Cash
- `app/Modules/Cash/Actions/OpenCashSessionAction.php`
- `app/Modules/Cash/Actions/AddCashMovementAction.php`
- `app/Modules/Cash/Actions/CloseCashSessionAction.php`
- `app/Modules/Cash/Services/CashSessionReadService.php`
- Integrado en `CashController`.

## Compatibilidad preservada
- No se cambiaron rutas públicas.
- No se cambiaron contratos JSON.
- No se renombraron tablas/columnas.
- No se renombraron modelos existentes.

## Plan de migración por fases
1. Fase 1 (hecha): Clients + Cash (Actions/Services).
2. Fase 2: Reception + Reports (acciones de lectura y casos críticos).
3. Fase 3: Plans + Memberships (promociones y pricing como dominio).
4. Fase 4: SuperAdmin (plantillas, notificaciones, sucursales).

## Criterios de migración segura
- Migrar por caso de uso, no por carpeta completa.
- Mantener controllers legacy como adaptadores temporales.
- Probar cada refactor con tests y build antes del siguiente módulo.