# GymSystem SaaS

Sistema SaaS multi-tenant para gimnasios (Laravel 12) con:
- recepcion/check-in (RFID/QR/documento),
- clientes/credenciales/membresias,
- caja por turnos,
- reportes (CSV/PDF),
- suscripciones SaaS con panel SuperAdmin.

## Requisitos

- PHP 8.2+
- Composer
- Node.js 20+
- MySQL 8+

## Instalacion Local

```bash
cp .env.local.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
php artisan serve
```

Credenciales demo del seeder:
- Gym admin: `admin@ironwill.test` / `password`
- SuperAdmin: `superadmin@gymsaas.test` / `password`

## Tests

```bash
php artisan test
```

## Deploy Oracle Cloud Free Tier (1 VM)

Arquitectura objetivo:
- 1 VM Ubuntu Always Free
- Nginx
- PHP-FPM + OPcache
- MySQL local
- Laravel monolito

### 1) Provision inicial de la VM

```bash
sudo apt update && sudo apt -y upgrade
sudo apt -y install nginx mysql-server unzip git curl
sudo apt -y install php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-intl
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Instalar Node (LTS) para build frontend.

### 2) Clonar y preparar aplicacion

```bash
git clone <tu-repo> /var/www/gymsystem
cd /var/www/gymsystem
cp .env.production.example .env
php artisan key:generate --force
```

Configura `.env` de produccion (DB, APP_URL, etc.).

### 3) Build y migraciones

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

### 4) Cache de produccion

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5) Nginx virtual host

Archivo sugerido: `/etc/nginx/sites-available/gymsystem`

```nginx
server {
    listen 80;
    server_name tu-dominio.com;

    root /var/www/gymsystem/public;
    index index.php index.html;

    client_max_body_size 2M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Activar:

```bash
sudo ln -s /etc/nginx/sites-available/gymsystem /etc/nginx/sites-enabled/gymsystem
sudo nginx -t
sudo systemctl restart nginx
```

### 6) OPcache recomendado (Free Tier)

Editar `php.ini` de FPM:

```ini
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
```

Aplicar:

```bash
sudo systemctl restart php8.2-fpm
```

### 7) SSL (Let's Encrypt)

```bash
sudo apt -y install certbot python3-certbot-nginx
sudo certbot --nginx -d tu-dominio.com
```

### 8) Scheduler y Queue Worker

Cron (scheduler cada minuto):

```bash
* * * * * cd /var/www/gymsystem && php artisan schedule:run >> /dev/null 2>&1
```

Queue worker (Supervisor o systemd):

```bash
php artisan queue:work --queue=default --sleep=2 --tries=1 --timeout=90
```

## Notificaciones Push PWA (movil)

Esta version incluye soporte Web Push para la app instalada en celular.

### 1) Instalar dependencia PHP

```bash
composer update minishlink/web-push
# luego puedes volver a usar composer install normalmente
```

### 2) Generar llaves VAPID

```bash
php artisan notifications:webpush-keys
```

### 3) Configurar `.env`

```bash
WEBPUSH_ENABLED=true
WEBPUSH_VAPID_SUBJECT=mailto:soporte@gymsystem.app
WEBPUSH_VAPID_PUBLIC_KEY=...
WEBPUSH_VAPID_PRIVATE_KEY=...
```

### 4) Migrar y levantar worker

```bash
php artisan migrate
php artisan queue:work --queue=default --sleep=2 --tries=1 --timeout=90
```

### 5) Activar desde panel

- Instalar la PWA en el celular.
- Ingresar al panel y usar el boton `Activar alertas`.
- El sistema envia una notificacion de prueba inmediata.

## Operacion y disco

- Usa `LOG_CHANNEL=daily` y define `LOG_DAILY_DAYS`.
- Limpieza de archivos temporales:

```bash
php artisan maintenance:cleanup-files --days=14
php artisan maintenance:cleanup-files --days=14 --dry-run
```

## Checklist de Verificacion

Local:
- [ ] `php artisan test` pasa.
- [ ] Login admin/superadmin funciona.
- [ ] Check-in valida membresia y evita duplicados.
- [ ] Caja impide cobros sin sesion abierta.

Produccion:
- [ ] `APP_ENV=production`, `APP_DEBUG=false`.
- [ ] `config/route/view cache` aplicado.
- [ ] `storage:link` activo.
- [ ] Cron scheduler configurado.
- [ ] Worker de cola activo.
- [ ] SSL valido.
- [ ] Logs rotando y limpieza temporal programada.
