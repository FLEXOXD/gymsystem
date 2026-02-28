# Plan de Refactor por Fases

Fecha: 2026-02-27

## Objetivo general
Mejorar estabilidad, limpieza, deduplicación, españolización y performance sin romper contratos públicos (rutas, payloads y comportamiento funcional).

## Fase 1: Inventario y auditoría
- [x] Medir archivos grandes por líneas y tamaño.
- [x] Detectar duplicaciones de validación/lógica.
- [x] Levantar riesgos de seguridad/performance.
- [x] Crear `docs/AUDITORIA_PROYECTO.md`.

## Fase 2: Limpieza segura
- [ ] Eliminar `.tmp_*.php` sin referencias.
- [ ] Retirar comentarios temporales (`TODO backend minimo`) en vistas productivas.
- [ ] Ejecutar Pint para normalizar estilo e imports no usados donde aplique.

## Fase 3: Deduplicación y refactor incremental
- [ ] Crear concern compartido para resolver `gym_id` en controladores.
- [ ] Extraer venta de membresía a servicio reutilizable (`ClientController` y `MembershipController`).
- [ ] Pasar validación inline de alta de membresía a `FormRequest`.
- [ ] Reducir duplicación de validación de estado (active/inactive) mediante `FormRequest` compartido.

## Fase 4: Españolización segura
- [ ] Corregir textos visibles en archivos tocados.
- [ ] Centralizar mensajes nuevos en `resources/lang/es/messages.php`.
- [ ] Mantener intactos nombres técnicos (clases, tablas, rutas, claves JSON de frontend).

## Fase 5: Performance y DB (sin romper)
- [ ] Evitar consultas repetitivas en evaluaciones de acceso por plan (batch/caché segura).
- [ ] Mantener o introducir paginación en listados de alto volumen.
- [ ] Documentar índices recomendados (sin aplicar sin validación).

## Fase 6: Seguridad
- [ ] Confirmar `authorize()` en nuevos FormRequest.
- [ ] Evitar validación inline en cambios nuevos.
- [ ] No usar `request()->all()` para persistencia.

## Fase 7: Verificación automática
- [ ] `composer dump-autoload`
- [ ] `php artisan config:clear`
- [ ] `php artisan route:clear`
- [ ] `php artisan view:clear`
- [ ] `php artisan test`
- [ ] `./vendor/bin/pint` (si está disponible)

## Criterio de cierre
- Controladores objetivo más delgados.
- Menor duplicación directa de lógica crítica.
- Sin regresiones funcionales en pruebas.
- Reporte final en `docs/RESUMEN_CAMBIOS.md`.

