# Fase 5: Índices Recomendados (No Aplicados)

Fecha: 2026-03-04

## Alcance
- Este documento **no aplica cambios en base de datos**.
- Resume índices sugeridos para validar con `EXPLAIN` en staging antes de migrar a producción.
- Objetivo: reducir costo en consultas de alto volumen detectadas en Fase 5.

## 1) `gym_branch_links` para hub y estado

### Índice sugerido
```sql
CREATE INDEX gym_branch_links_hub_status_id_idx
ON gym_branch_links (hub_gym_id, status, id);
```

### Justificación
- Consultas por sede principal con orden por `id` descendente y filtros por estado.
- Referencias:
  - `app/Http/Controllers/BranchController.php`
  - `app/Http/Controllers/SuperAdminBranchController.php`

## 2) `gym_branch_links` para resolver branch activa

### Índice sugerido
```sql
CREATE INDEX gym_branch_links_branch_status_hub_idx
ON gym_branch_links (branch_gym_id, status, hub_gym_id);
```

### Justificación
- Resolución frecuente de vínculo activo por `branch_gym_id` y `status`.
- Referencias:
  - `app/Http/Controllers/ReceptionCheckInController.php` (fallback de avatares y resolución de hub).

## 3) `users` para operador móvil

### Índice sugerido
```sql
CREATE INDEX users_gym_active_role_id_idx
ON users (gym_id, is_active, role, id);
```

### Justificación
- Búsqueda del operador para check-in móvil filtra por `gym_id`, `is_active`, `role` y ordena por `id`.
- Referencias:
  - `app/Http/Controllers/ClientMobileController.php` (`resolveMobileOperatorUserId`).

## 4) `pwa_events` para historiales y limpieza

### Índice sugerido
```sql
CREATE INDEX pwa_events_event_created_idx
ON pwa_events (event_name, created_at, id);
```

### Justificación
- Facilita paneles/event streams y futuros procesos de limpieza temporal por tipo + fecha.
- Referencias:
  - `app/Http/Controllers/PwaEventController.php`

## Validación recomendada antes de aplicar
1. Ejecutar `EXPLAIN` de consultas reales con datos de staging.
2. Medir impacto en lectura y en tiempo de escritura.
3. Aplicar migraciones en ventana de bajo tráfico.
4. Monitorear latencia y lock-time posterior al despliegue.
