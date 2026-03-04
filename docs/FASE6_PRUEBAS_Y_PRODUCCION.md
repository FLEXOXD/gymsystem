# Fase 6: Pruebas y salida a produccion

Fecha: 2026-03-04

## Objetivo
- Validar los 4 metodos de ingreso (RFID, QR, documento y QR dinamico).
- Asegurar aislamiento por plan y por gimnasio/sucursal.
- Definir un checklist de despliegue con rollback claro.

## Cobertura de pruebas feature implementada
- `tests/Feature/ReceptionPhase6ProductionTest.php`
  - Check-in por documento.
  - Check-in por RFID.
  - Check-in por QR.
  - Check-in por QR dinamico (flujo `reception.mobile-qr` + `client-mobile.check-in`).
  - Bloqueo por plan sin `client_accounts`.
  - Aislamiento tenant: no usar credenciales de otro gimnasio.
  - Aislamiento hub/sucursal en contexto de check-in.
  - Rechazo de QR dinamico emitido para otro gimnasio.

## Checklist de despliegue (Go-Live)
1. Previo al despliegue
- Confirmar backup de base de datos y punto de restauracion.
- Confirmar ventana de despliegue y responsable on-call.
- Ejecutar en staging:
  - `php artisan test tests/Feature/ReceptionPhase6ProductionTest.php`
  - `php artisan test`

2. Despliegue
- Publicar codigo de la release.
- Instalar dependencias de backend:
  - `composer install --no-dev --optimize-autoloader`
- Construir frontend:
  - `npm ci`
  - `npm run build`
- Aplicar migraciones:
  - `php artisan migrate --force`
- Refrescar caches:
  - `php artisan config:clear`
  - `php artisan route:clear`
  - `php artisan view:clear`
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`

3. Validacion post-deploy
- Login de owner/cashier funcional.
- Check-in en recepcion:
  - documento
  - RFID
  - QR
- Flujo QR dinamico:
  - generar token en recepcion
  - check-in desde cliente movil
  - reutilizacion del token bloqueada
- Verificar que no exista fuga entre gimnasios/sucursales.
- Monitorear logs de errores durante al menos 15 minutos.

## Plan de rollback
1. Disparadores de rollback
- Errores 5xx sostenidos en rutas de check-in.
- Falla de login operativa.
- Falla de aislamiento tenant detectada.
- Falla critica en flujo QR dinamico.

2. Pasos de rollback
- Poner la aplicacion en mantenimiento:
  - `php artisan down`
- Volver al release/tag estable previo.
- Reinstalar dependencias y reconstruir assets del release estable.
- Si el despliegue incluyo migraciones incompatibles:
  - restaurar backup de base de datos
  - o ejecutar rollback de migraciones validadas para ese release
- Limpiar y regenerar caches:
  - `php artisan optimize:clear`
- Levantar aplicacion:
  - `php artisan up`

3. Verificacion posterior al rollback
- Confirmar login, recepcion y caja operativos.
- Confirmar que check-in por documento vuelve a funcionar.
- Revisar logs y registrar incidente con causa raiz.
