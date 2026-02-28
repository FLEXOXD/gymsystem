# Candidatos a Eliminar (pendiente de confirmación)

Fecha: 2026-02-27

Regla aplicada: no borrar sin confirmar referencias globales (rutas, vistas, imports, tests).

## 1) resources/views/marketing/home.tmp
- Estado: SIN referencias detectadas por búsqueda global.
- Búsqueda: `rg -n "home\.tmp" app resources routes tests config database docs`
- Riesgo: bajo, pero requiere confirmación final antes de borrar.

## 2) tmp/test_delete_me.txt
- Estado: SIN referencias detectadas por búsqueda global.
- Búsqueda: `rg -n "test_delete_me\.txt" app resources routes tests config database docs`
- Riesgo: bajo, archivo temporal aparente.

## 3) docs/AUDITORIA_PROYECTO.md
- Estado: documento legacy paralelo a `docs/AUDITORIA_FINAL.md`.
- Riesgo: bajo, pero puede conservar contexto histórico.

## 4) docs/PLAN_REFACTOR.md
- Estado: documento legacy paralelo a entregables nuevos.
- Riesgo: bajo, pero puede ser referencia de seguimiento previo.

No se eliminó ninguno en esta iteración para evitar ruptura por dependencia no detectada fuera del repositorio.