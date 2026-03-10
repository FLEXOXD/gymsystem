<?php

namespace App\Console\Commands;

use App\Services\CashSessionService;
use Illuminate\Console\Command;

class AutoCloseCashSessionsAtMidnightCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cash:auto-close-midnight
        {--gym= : Cerrar automaticamente solo las cajas abiertas de un gym especifico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra automaticamente las cajas que llegaron a medianoche sin cierre manual.';

    /**
     * Execute the console command.
     */
    public function handle(CashSessionService $cashSessionService): int
    {
        $gymId = max(0, (int) $this->option('gym'));
        $closedCount = $cashSessionService->autoCloseExpiredSessions($gymId > 0 ? $gymId : null);

        $scopeLabel = $gymId > 0
            ? 'gym '.$gymId
            : 'todos los gimnasios';

        $this->info("Cierres automaticos aplicados: {$closedCount} ({$scopeLabel}).");

        return self::SUCCESS;
    }
}
