<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupOperationalFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:cleanup-files
        {--days=14 : Eliminar archivos con antiguedad mayor a N dias}
        {--dry-run : Solo mostrar lo que se eliminaria}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia archivos temporales de reportes/exports para proteger disco.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $dryRun = (bool) $this->option('dry-run');
        $cutoffTimestamp = now()->subDays($days)->getTimestamp();

        $paths = [
            storage_path('app/public/reports'),
            storage_path('app/private/reports'),
            storage_path('app/tmp'),
        ];

        $allowedExtensions = ['pdf', 'csv', 'txt', 'tmp', 'json', 'html'];

        $scanned = 0;
        $deleted = 0;

        foreach ($paths as $path) {
            if (! File::isDirectory($path)) {
                continue;
            }

            foreach (File::allFiles($path) as $file) {
                $scanned++;

                if ($file->getMTime() >= $cutoffTimestamp) {
                    continue;
                }

                $extension = strtolower((string) $file->getExtension());
                if (! in_array($extension, $allowedExtensions, true)) {
                    continue;
                }

                if ($dryRun) {
                    $this->line('[DRY-RUN] '.$file->getPathname());
                    $deleted++;
                    continue;
                }

                File::delete($file->getPathname());
                $deleted++;
            }
        }

        $mode = $dryRun ? 'simulados' : 'eliminados';
        $this->info("Archivos escaneados: {$scanned}. Archivos {$mode}: {$deleted}. Dias: {$days}.");

        return self::SUCCESS;
    }
}
