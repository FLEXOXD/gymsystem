# Production Operations (Oracle Free Tier)

Este proyecto se optimiza en produccion sin tocar tu `.env` local.

## 1) Preparar entorno

```bash
cp .env.production.example .env
php artisan key:generate --force
```

Configura en ese `.env` los valores reales de dominio, DB y credenciales.

## 2) Despliegue base

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
```

## 3) Cachés para produccion

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4) Scheduler y cola

```bash
php artisan schedule:run
php artisan queue:work --queue=default --sleep=2 --tries=1 --timeout=90
```

Para ejecucion continua:
- `schedule:run` via cron cada minuto.
- `queue:work` administrado por Supervisor o systemd.

## 5) Logs y disco

- En produccion usa `LOG_CHANNEL=daily`.
- Ajusta `LOG_DAILY_DAYS` segun el disco disponible.
- Se agenda limpieza diaria de archivos temporales:
  - `php artisan maintenance:cleanup-files --days=14`
- Puedes probar sin borrar con:

```bash
php artisan maintenance:cleanup-files --days=14 --dry-run
```

- Si necesitas liberar cache compilada:

```bash
php artisan optimize:clear
```

## 6) Nota local

No ejecutar `config:cache`/`route:cache`/`view:cache` durante desarrollo iterativo, salvo pruebas puntuales.
