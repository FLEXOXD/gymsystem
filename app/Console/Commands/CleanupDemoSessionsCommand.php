<?php

namespace App\Console\Commands;

use App\Services\DemoSessionService;
use Illuminate\Console\Command;

class CleanupDemoSessionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina sesiones demo temporales expiradas y sus datos asociados.';

    public function __construct(
        private readonly DemoSessionService $demoSessionService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->demoSessionService->cleanupExpired();
        $this->info('Sesiones demo expiradas limpiadas correctamente.');

        return self::SUCCESS;
    }
}

