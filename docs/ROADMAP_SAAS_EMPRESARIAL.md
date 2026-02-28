# Roadmap SaaS Empresarial

Fecha: 2026-02-27

## Norte arquitectónico
Multi-tenant por `gym_id`, aislamiento estricto de datos y operación por contexto (`{contextGym}`).

## 1) Multi-tenant y aislamiento
- Middleware de contexto obligatorio en módulos multi-gym.
- Scopes `forGym/forGyms` en modelos críticos.
- Auditoría de acceso cruzado con logging estructurado.
- Bloqueo explícito de acceso a gimnasios no vinculados.

## 2) Roles y permisos
Roles objetivo:
- superadmin
- admin gym
- reception
- cash

Acciones:
- Consolidar Policies/Gates por dominio.
- Evitar `authorize() => true` en requests sensibles sin policy/middleware específico.

## 3) Observabilidad
- Logging por eventos de seguridad y acceso.
- Correlation ID por request (siguiente fase).
- Métricas mínimas:
  - check-ins por hora
  - cierres con diferencia
  - fallos de autorización

## 4) Jobs/Queues
- PDFs y reportes pesados a cola.
- Notificaciones de suscripción asíncronas.
- Reintentos y DLQ para tareas críticas.

## 5) Operación empresarial
- Backups con prueba de restauración.
- Estrategia de migraciones forward-only.
- CI/CD con gates mínimos:
  - tests
  - build frontend
  - smoke routes

## Hardening básico aplicado en esta iteración
- `EnsureGymRouteContextMiddleware` ahora registra intentos inválidos/no autorizados con contexto (`user_id`, `requested_slug`, `ip`, `path`).
- Validación adicional de formato de slug antes de resolver gimnasio.
- Refactor de casos de uso de caja y clientes hacia Actions para reducir lógica crítica en controllers.

## Próximos 90 días
1. Policies por dominio con matriz rol-acción.
2. Auditoría persistente (tabla audit_logs) para caja/clientes/suscripciones.
3. Colas para exportaciones de reportes y contratos PDF.
4. Health checks + dashboards operativos.