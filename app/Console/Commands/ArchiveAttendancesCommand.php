<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AttendanceDailySummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveAttendancesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendances:archive {--months=2 : Meses de detalle a conservar antes de archivar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archiva asistencias antiguas por día y limpia detalle histórico para mantener rendimiento.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $monthsToKeep = max(1, (int) $this->option('months'));
        $cutoffDate = now()->subMonthsNoOverflow($monthsToKeep)->toDateString();

        $oldAttendancesQuery = Attendance::query()->whereDate('date', '<', $cutoffDate);
        $oldRowsCount = (clone $oldAttendancesQuery)->count();

        if ($oldRowsCount === 0) {
            $this->info("No hay asistencias para archivar. Corte: {$cutoffDate}");

            return self::SUCCESS;
        }

        $aggregatedRows = (clone $oldAttendancesQuery)
            ->selectRaw('gym_id, date, COUNT(*) as attendances_count')
            ->groupBy('gym_id', 'date')
            ->orderBy('gym_id')
            ->orderBy('date')
            ->get();

        DB::transaction(function () use ($aggregatedRows, $cutoffDate): void {
            foreach ($aggregatedRows as $row) {
                $gymId = (int) $row->gym_id;
                $date = (string) $row->date;
                $count = (int) $row->attendances_count;

                $summary = AttendanceDailySummary::query()
                    ->where('gym_id', $gymId)
                    ->whereDate('date', $date)
                    ->lockForUpdate()
                    ->first();

                if ($summary) {
                    $summary->attendances_count = (int) $summary->attendances_count + $count;
                    $summary->save();
                    continue;
                }

                AttendanceDailySummary::query()->create([
                    'gym_id' => $gymId,
                    'date' => $date,
                    'attendances_count' => $count,
                ]);
            }

            Attendance::query()
                ->whereDate('date', '<', $cutoffDate)
                ->delete();
        });

        $this->info("Asistencias archivadas: {$oldRowsCount} | Corte: {$cutoffDate}");

        return self::SUCCESS;
    }
}
