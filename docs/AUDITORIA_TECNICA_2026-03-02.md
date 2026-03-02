# Auditoría Técnica Integral - GymSystem
Fecha: 2026-03-02

## Estado general
- Build frontend: OK (`npm run build`).
- Suite de pruebas: OK (`php artisan test`), 71 tests aprobados.
- Plataforma base: estable para seguir escalando sin romper operación actual.

## Hallazgos críticos (prioridad alta)
1. Codificación/consistencia de copy comercial
- Riesgo: textos con acentos inconsistentes afectan confianza y percepción de calidad.
- Impacto: marketing, onboarding y mensajes al cliente final.
- Acción aplicada: normalización UTF-8 y copy profesional en `app/Support/MarketingContent.php`.

2. Coherencia visual de marca en PWA
- Riesgo: `theme-color` azul no alineado con branding verde.
- Impacto: barra del navegador/PWA y percepción de identidad visual.
- Acción aplicada:
  - `public/manifest.webmanifest`
  - `resources/views/layouts/panel.blade.php`
  - `resources/views/auth/login.blade.php`
  - `public/offline.html`

3. Mantenimiento de archivos temporales `.tmp_*`
- Riesgo: ruido operativo, confusión del equipo, riesgo de despliegue accidental de scripts de diagnóstico.
- Impacto: mantenibilidad y limpieza del repo.
- Estado: se mantiene patrón de ignore (`.gitignore`), pero existen temporales históricos versionados en raíz.
- Recomendación: migrar diagnóstico a `scripts/debug/` y excluirlos formalmente del versionado productivo.

## Hallazgos medios (prioridad media)
1. Archivos Blade muy grandes (deuda de mantenibilidad)
- `resources/views/marketing/home.blade.php` (~2.8k líneas)
- `resources/views/layouts/panel.blade.php` (~1.6k líneas)
- `resources/views/layouts/partials/panel-inline-scripts.blade.php` (~1.8k líneas)
- Riesgo: más probabilidad de regresiones y tiempos altos de cambio.
- Recomendación: extraer componentes Blade + módulos JS por dominio (header, switcher de sucursales, push, tour demo, etc.).

2. Responsive avanzado pero con alto acoplamiento a estilos inline
- Riesgo: cambios visuales difíciles de probar de forma aislada.
- Recomendación: consolidar utilidades de layout en `resources/css/ui.css` y reducir CSS inline por vista.

3. PWA offline básica (correcta, pero mejorable)
- Estado actual: fallback y caché de assets funcionando.
- Recomendación: estrategia de caché por versión de API y precarga de rutas críticas por contexto autenticado.

## Hallazgos bajos (prioridad baja)
1. Terminología técnica mezclada (ES/EN)
- Ejemplos: "Mobile UX", "Desktop", "tenant".
- Recomendación: definir guía de estilo (español comercial + términos técnicos permitidos).

2. Comentarios TODO en vistas
- Ubicación: módulos de caja.
- Recomendación: mover TODO a issues/tareas y limpiar vistas productivas.

## Mejoras aplicadas en esta iteración
- Normalización de textos y tildes en contenido comercial:
  - `app/Support/MarketingContent.php`
- Ajuste de color de marca para experiencia PWA/login/panel:
  - `public/manifest.webmanifest`
  - `resources/views/layouts/panel.blade.php`
  - `resources/views/auth/login.blade.php`
  - `public/offline.html`
- Corrección ortográfica de comando de mantenimiento:
  - `app/Console/Commands/CleanupOperationalFiles.php`

## Plan recomendado de mejora (sin romper operación)
Fase 1 (rápida, 1-2 días)
- Limpiar temporales del repo y centralizar scripts de diagnóstico.
- Definir guía de copy y tono (acentos, mayúsculas, términos permitidos).
- Revisar y eliminar TODO visibles en vistas productivas.

Fase 2 (estructura, 3-5 días)
- Refactorizar `panel.blade.php` y `panel-inline-scripts.blade.php` en componentes/módulos.
- Crear pruebas de snapshot/feature para rutas críticas responsive (panel, clientes, recepción).

Fase 3 (PWA avanzada, 2-4 días)
- Optimizar cacheo por contexto autenticado.
- Añadir estrategia de actualización controlada (notificación de nueva versión al usuario).
- Medir e iterar con métricas de instalación/engagement PWA.
