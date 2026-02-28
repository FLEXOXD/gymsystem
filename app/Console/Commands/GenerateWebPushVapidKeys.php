<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateWebPushVapidKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:webpush-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera llaves VAPID para notificaciones Web Push.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! class_exists(\Minishlink\WebPush\VAPID::class)) {
            $this->error('Falta la libreria minishlink/web-push. Ejecuta composer install/update.');

            return self::FAILURE;
        }

        $keys = \Minishlink\WebPush\VAPID::createVapidKeys();

        $this->line('');
        $this->info('Copia estas variables en tu .env:');
        $this->line('WEBPUSH_ENABLED=true');
        $this->line('WEBPUSH_VAPID_SUBJECT=mailto:soporte@gymsystem.app');
        $this->line('WEBPUSH_VAPID_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('WEBPUSH_VAPID_PRIVATE_KEY='.$keys['privateKey']);
        $this->line('');

        return self::SUCCESS;
    }
}

