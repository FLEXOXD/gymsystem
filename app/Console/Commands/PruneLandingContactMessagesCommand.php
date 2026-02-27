<?php

namespace App\Console\Commands;

use App\Models\LandingContactMessage;
use Illuminate\Console\Command;

class PruneLandingContactMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'landing-messages:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina mensajes de contacto web vencidos (24 horas).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = LandingContactMessage::query()
            ->where('created_at', '<=', now()->subHours(LandingContactMessage::PRUNE_HOURS))
            ->delete();

        $this->info('Mensajes eliminados: '.$deleted);

        return self::SUCCESS;
    }
}
