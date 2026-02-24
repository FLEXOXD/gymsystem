# Manual de Mantenimiento - GymSystem (Produccion)

Fecha de referencia: 24 de febrero de 2026
Servidor: Google Cloud VM Ubuntu 22.04
Stack: Laravel 12, PHP 8.2, Apache, Redis, MySQL, systemd workers

## 1) Objetivo

Este manual te permite mantener tu sistema estable, rapido y seguro, con tareas claras para:

- Monitoreo
- Respaldos
- Seguridad
- Rendimiento
- Respuesta a incidentes

## 2) Estado actual confirmado

- VM: e2-medium (2 vCPU, 4 GB RAM)
- RAM observada: 3.8 GiB + 2 GiB swap
- Disco root: 9.6 GB, uso aproximado 71%
- PHP: 8.2.30
- Laravel: 12.52.0
- Redis activo
- Apache activo
- Workers de cola activos: laravel-worker@1, @2, @3, @4
- Queue/cache/session en Redis

## 3) Alertas de Monitoring ya configuradas

### 3.1 CPU

- Nombre: VM CPU > 70% (10m)
- Metrica: VM Instance - CPU utilization
- Trigger: Above threshold
- Umbral: 70
- Duracion: 10 min
- Notificacion: correo

### 3.2 RAM

- Nombre: VM RAM > 85% (10m)
- Metrica: VM Instance - Memory utilization
- Trigger: Above threshold
- Umbral: 85
- Duracion: 10 min
- Notificacion: correo

### 3.3 Disco

- Nombre: VM DISK > 85% (10m)
- Metrica: VM Instance - Disk utilization
- Trigger: Above threshold
- Umbral: 85
- Duracion: 10 min
- Filtro recomendado: state=used y device del root (ejemplo /dev/sda1)
- Notificacion: correo

## 4) Comandos de chequeo rapido (salud)

Ejecutar desde SSH:

```bash
free -h
df -h
php -v
sudo systemctl is-active apache2 redis-server
sudo systemctl is-active laravel-worker@1 laravel-worker@2 laravel-worker@3 laravel-worker@4
```

Resultado esperado:

- Servicios en `active`
- Disco menor a 85%
- RAM con margen libre

## 5) Cola y workers (produccion)

### 5.1 Validar workers

```bash
sudo systemctl status laravel-worker@1 laravel-worker@2 laravel-worker@3 laravel-worker@4 --no-pager
```

### 5.2 Reiniciar workers (despues de deploy o cambios de config)

```bash
sudo systemctl restart laravel-worker@1 laravel-worker@2 laravel-worker@3 laravel-worker@4
```

### 5.3 Reinicio limpio de colas Laravel

```bash
cd /var/www/gymsystem/current
sudo -u www-data php artisan queue:restart
```

## 6) Cache, config y optimizacion Laravel

Despues de cambios en `.env` o `config/*`:

```bash
cd /var/www/gymsystem/current
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
```

Verificaciones:

```bash
sudo -u www-data php artisan config:show queue.default
sudo -u www-data php artisan config:show cache.default
sudo -u www-data php artisan config:show session.driver
```

Esperado:

- queue.default = redis
- cache.default = redis
- session.driver = redis

## 7) Respaldos (backup) y restauracion

## 7.1 Backup rapido

```bash
set -a
source /var/www/gymsystem/shared/.env
set +a

TS="$(date +%F-%H%M%S)"
BACKUP_DIR="/var/backups/gymsystem"
mkdir -p "$BACKUP_DIR"

cp /var/www/gymsystem/shared/.env "$BACKUP_DIR/env-$TS.bak"
mysqldump --no-tablespaces -h "${DB_HOST:-localhost}" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" | gzip > "$BACKUP_DIR/db-$TS.sql.gz"
tar -czf "$BACKUP_DIR/storage-$TS.tar.gz" -C /var/www/gymsystem/shared storage
ls -lh "$BACKUP_DIR" | tail -n 5
```

## 7.2 Restore test (recomendado mensual)

- Restaurar en base temporal, validar que abre y luego borrar base temporal.
- Nunca restaurar encima de produccion sin backup previo.

## 8) Seguridad base ya aplicada

- UFW activo con reglas minimas (OpenSSH + Apache Full)
- Fail2ban activo para SSH
- SSH hardening:
  - PermitRootLogin no
  - PasswordAuthentication no
  - PubkeyAuthentication yes

Comandos de validacion:

```bash
sudo ufw status numbered
sudo fail2ban-client status
sudo fail2ban-client status sshd
sudo grep -E '^(PermitRootLogin|PasswordAuthentication|PubkeyAuthentication)' /etc/ssh/sshd_config
```

## 9) OPcache (PHP 8.2)

Configuracion recomendada ya aplicada:

- opcache.enable=1
- opcache.enable_cli=1
- opcache.memory_consumption=256
- opcache.interned_strings_buffer=32
- opcache.max_accelerated_files=50000
- opcache.validate_timestamps=0
- opcache.revalidate_freq=0

Verificar:

```bash
php -i | grep -E "opcache.enable =>|opcache.enable_cli =>|opcache.memory_consumption =>|opcache.max_accelerated_files =>|opcache.validate_timestamps =>|opcache.revalidate_freq =>"
```

## 10) Limpieza de disco

## 10.1 Diagnostico

```bash
sudo du -xhd1 / | sort -h
sudo du -xhd1 /var | sort -h
sudo du -xhd1 /var/lib | sort -h
```

## 10.2 Limpieza segura

```bash
sudo apt clean
sudo apt autoremove -y
sudo apt autoclean
sudo journalctl --vacuum-time=7d
```

## 10.3 Releases viejos (si aplica)

```bash
ls -1dt /var/www/gymsystem/releases/* | tail -n +6
```

Si muestra rutas, puedes borrar solo esos releases viejos y mantener los 5 mas recientes.

## 11) Respuesta a alertas (playbook)

## 11.1 Si alerta CPU > 70% (10m)

1. Revisar procesos:

```bash
top
ps aux --sort=-%cpu | head
```

2. Revisar workers y cola.
3. Revisar endpoint pesado o cron.
4. Si se repite en horas pico por varios dias: subir VM (mas vCPU).

## 11.2 Si alerta RAM > 85% (10m)

1. Revisar memoria:

```bash
free -h
ps aux --sort=-%mem | head
```

2. Revisar consumo de Apache y workers.
3. Reinicio controlado de workers.
4. Si persiste: aumentar RAM de VM.

## 11.3 Si alerta DISK > 85% (10m)

1. Revisar:

```bash
df -h
sudo du -xhd1 /var | sort -h
```

2. Limpiar logs/cache/paquetes.
3. Limpiar releases viejos.
4. Si sigue alto: ampliar disco en GCP.

## 12) Rutina operativa recomendada

### Diario (5 min)

- Revisar alertas GCP
- Ver `df -h` y estado de servicios

### Diario rapido (2 min)

Puedes ejecutar este script para validacion rapida:

```bash
bash /var/www/gymsystem/current/scripts/server_2min_check.sh
```

Si trabajas desde otra ruta:

```bash
APP_DIR=/var/www/gymsystem/current bash /var/www/gymsystem/current/scripts/server_2min_check.sh
```

Interpretacion:

- `Summary: X OK / 0 FAIL` => estado estable
- Cualquier `FAIL` => revisar ese punto antes de abrir horario de trabajo

### Semanal (15-20 min)

- Ejecutar backup completo
- Revisar logs de errores Laravel y Apache
- Revisar fallos de jobs

### Mensual (30 min)

- Restore test de backup
- Revisar costos GCP
- Revisar uso pico (CPU/RAM/disco) para decidir escalado

## 13) Umbrales para escalar VM

Escalar cuando ocurra al menos una condicion de forma repetida (semanas):

- CPU > 70% sostenido en horas pico
- RAM > 85% frecuente
- Disco > 80% con tendencia de subida
- Tiempo de respuesta del sistema sube de forma visible

## 14) Comando unico de diagnostico rapido

```bash
echo "=== FECHA ===" && date
echo "=== CPU/RAM ===" && nproc && free -h
echo "=== DISCO ===" && df -h
echo "=== SERVICIOS ===" && sudo systemctl is-active apache2 redis-server laravel-worker@1 laravel-worker@2 laravel-worker@3 laravel-worker@4
echo "=== VERSIONES ===" && php -v | head -n 2 && redis-server --version | head -n 1
```

## 15) Recomendacion final

Tu servidor quedo bien optimizado para etapa inicial/comercial. Manteniendo este runbook, alertas y backups, reduces mucho riesgo operativo mientras creces clientes.
