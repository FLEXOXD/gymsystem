<?php

namespace App\Console\Commands;

use App\Services\SubscriptionNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class GenerateSubscriptionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:notify {--date= : Fecha a procesar (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera recordatorios de vencimiento de suscripciones (7,3,1 y gracia 1,2,3).';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionNotificationService $notificationService): int
    {
        $dateOption = $this->option('date');

        try {
            $targetDate = $dateOption
                ? Carbon::parse((string) $dateOption)->toDateString()
                : Carbon::today()->toDateString();
        } catch (Throwable $exception) {
            $this->error('Fecha invalida. Use formato YYYY-MM-DD.');

            return self::FAILURE;
        }

        $created = $notificationService->generateDueNotifications($targetDate);

        $this->info("Notificaciones generadas para {$targetDate}: {$created}");

        return self::SUCCESS;
    }
}
