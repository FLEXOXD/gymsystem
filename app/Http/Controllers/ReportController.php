<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const PDF_MAX_DETAIL_ROWS = 1200;

    public function __construct(
        private readonly ReportService $reportService
    ) {
    }

    /**
     * Reports dashboard with summaries and charts.
     */
    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymId, $from, $to);
        $incomeByMethod = $this->reportService->getIncomeByMethod($gymId, $from, $to);
        $attendanceSummary = $this->reportService->getAttendanceSummary($gymId, $from, $to);
        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymId);

        $methodLabels = $incomeByMethod
            ->pluck('method')
            ->map(fn (string $method) => match ($method) {
                'cash' => 'Efectivo',
                'card' => 'Tarjeta',
                'transfer' => 'Transferencia',
                default => $method,
            })
            ->values();
        $methodIncomeData = $incomeByMethod->map(fn ($row) => (float) $row->income_total)->values();
        $methodExpenseData = $incomeByMethod->map(fn ($row) => (float) $row->expense_total)->values();

        $attendanceByDay = collect($attendanceSummary['by_day']);
        $attendanceLabels = $attendanceByDay
            ->map(fn ($row) => Carbon::parse($row->date)->format('Y-m-d'))
            ->values();
        $attendanceData = $attendanceByDay
            ->map(fn ($row) => (int) $row->attendances_count)
            ->values();

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'incomeSummary' => $incomeSummary,
            'attendanceSummary' => $attendanceSummary,
            'membershipSummary' => $membershipSummary,
            'methodLabels' => $methodLabels,
            'methodIncomeData' => $methodIncomeData,
            'methodExpenseData' => $methodExpenseData,
            'attendanceLabels' => $attendanceLabels,
            'attendanceData' => $attendanceData,
        ]);
    }

    /**
     * Income report detail table.
     */
    public function income(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymId, $from, $to);

        $movements = $this->movementsBaseQuery($gymId, $from, $to)
            ->select([
                'id',
                'gym_id',
                'cash_session_id',
                'membership_id',
                'created_by',
                'type',
                'amount',
                'method',
                'description',
                'occurred_at',
            ])
            ->with([
                'createdBy:id,name',
                'membership:id,client_id',
                'membership.client:id,first_name,last_name',
            ])
            ->orderByDesc('occurred_at')
            ->paginate(50)
            ->withQueryString();

        return view('reports.income', [
            'from' => $from,
            'to' => $to,
            'incomeSummary' => $incomeSummary,
            'movements' => $movements,
        ]);
    }

    /**
     * Attendance report by day.
     */
    public function attendance(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $attendanceSummary = $this->reportService->getAttendanceSummary($gymId, $from, $to);
        $attendanceByDay = collect($attendanceSummary['by_day']);

        return view('reports.attendance', [
            'from' => $from,
            'to' => $to,
            'attendanceSummary' => $attendanceSummary,
            'attendanceByDay' => $attendanceByDay,
        ]);
    }

    /**
     * Membership report (active vs expired clients).
     */
    public function memberships(Request $request): View
    {
        $gymId = $this->resolveGymId($request);

        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymId);
        $membershipLists = $this->reportService->getMembershipClientLists($gymId);

        return view('reports.memberships', [
            'membershipSummary' => $membershipSummary,
            'activeClients' => $membershipLists['active_clients'],
            'expiredClients' => $membershipLists['expired_clients'],
        ]);
    }

    /**
     * Export reports data to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $gymId = $this->resolveGymId($request);
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymId, $from, $to);
        $incomeByMethod = $this->reportService->getIncomeByMethod($gymId, $from, $to);
        $attendanceSummary = $this->reportService->getAttendanceSummary($gymId, $from, $to);
        $attendanceByDay = collect($attendanceSummary['by_day']);
        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymId);

        $filename = 'reports_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.csv';

        return response()->streamDownload(function () use (
            $gymId,
            $from,
            $to,
            $incomeSummary,
            $incomeByMethod,
            $attendanceSummary,
            $attendanceByDay,
            $membershipSummary
        ): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Reportes Profesionales - Gimnasio']);
            fputcsv($handle, ['Desde', $from->toDateString(), 'Hasta', $to->toDateString()]);
            fputcsv($handle, []);

            fputcsv($handle, ['RESUMEN INGRESOS']);
            fputcsv($handle, ['Total ingresos', number_format((float) $incomeSummary['total_income'], 2, '.', '')]);
            fputcsv($handle, ['Total egresos', number_format((float) $incomeSummary['total_expense'], 2, '.', '')]);
            fputcsv($handle, ['Balance', number_format((float) $incomeSummary['balance'], 2, '.', '')]);
            fputcsv($handle, ['Total movimientos', (int) $incomeSummary['total_movements']]);
            fputcsv($handle, []);

            fputcsv($handle, ['INGRESOS POR METODO']);
            fputcsv($handle, ['Metodo', 'Ingresos', 'Egresos', 'Balance', 'Movimientos']);
            foreach ($incomeByMethod as $row) {
                fputcsv($handle, [
                    match ($row->method) {
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        default => $row->method,
                    },
                    number_format((float) $row->income_total, 2, '.', ''),
                    number_format((float) $row->expense_total, 2, '.', ''),
                    number_format((float) $row->balance, 2, '.', ''),
                    (int) $row->movements_count,
                ]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['ASISTENCIAS']);
            fputcsv($handle, ['Total asistencias', (int) $attendanceSummary['total_attendances']]);
            fputcsv($handle, ['Fecha', 'Cantidad']);
            foreach ($attendanceByDay as $row) {
                fputcsv($handle, [$row->date, (int) $row->attendances_count]);
            }
            fputcsv($handle, []);

            fputcsv($handle, ['MEMBRESIAS']);
            fputcsv($handle, ['Activos', (int) $membershipSummary['active']]);
            fputcsv($handle, ['Vencidos', (int) $membershipSummary['expired']]);
            fputcsv($handle, ['Total clientes', (int) $membershipSummary['total_clients']]);
            fputcsv($handle, []);

            fputcsv($handle, ['DETALLE MOVIMIENTOS']);
            fputcsv($handle, ['ID', 'Fecha', 'Tipo', 'Metodo', 'Monto', 'Cliente', 'Usuario', 'Descripcion']);
            $movementsQuery = CashMovement::query()
                ->forGym($gymId)
                ->betweenOccurredAt($from, $to)
                ->leftJoin('users as users', 'users.id', '=', 'cash_movements.created_by')
                ->leftJoin('memberships as memberships', 'memberships.id', '=', 'cash_movements.membership_id')
                ->leftJoin('clients as clients', function ($join): void {
                    $join->on('clients.id', '=', 'memberships.client_id')
                        ->on('clients.gym_id', '=', 'cash_movements.gym_id');
                })
                ->select([
                    'cash_movements.id as movement_id',
                    'cash_movements.occurred_at',
                    'cash_movements.type',
                    'cash_movements.method',
                    'cash_movements.amount',
                    'cash_movements.description',
                    'users.name as user_name',
                    'clients.first_name as client_first_name',
                    'clients.last_name as client_last_name',
                ])
                ->orderBy('cash_movements.id');

            $movementsQuery->chunkById(1000, function ($rows) use ($handle): void {
                foreach ($rows as $row) {
                    $clientName = trim(((string) ($row->client_first_name ?? '')).' '.((string) ($row->client_last_name ?? '')));

                    fputcsv($handle, [
                        $row->movement_id,
                        $row->occurred_at,
                        match ($row->type) {
                            'income' => 'Ingreso',
                            'expense' => 'Egreso',
                            default => $row->type,
                        },
                        match ($row->method) {
                            'cash' => 'Efectivo',
                            'card' => 'Tarjeta',
                            'transfer' => 'Transferencia',
                            default => $row->method,
                        },
                        number_format((float) $row->amount, 2, '.', ''),
                        $clientName,
                        (string) ($row->user_name ?? ''),
                        (string) ($row->description ?? ''),
                    ]);
                }
            }, 'cash_movements.id', 'movement_id');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export reports data to PDF.
     */
    public function exportPdf(Request $request)
    {
        $gymId = $this->resolveGymId($request);
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymId, $from, $to);
        $incomeByMethod = $this->reportService->getIncomeByMethod($gymId, $from, $to);
        $attendanceSummary = $this->reportService->getAttendanceSummary($gymId, $from, $to);
        $attendanceByDay = collect($attendanceSummary['by_day']);
        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymId);

        $movementsQuery = $this->movementsBaseQuery($gymId, $from, $to);
        $movementsCount = (clone $movementsQuery)->count();
        $isPdfDetailTruncated = $movementsCount > self::PDF_MAX_DETAIL_ROWS;

        $movements = $movementsQuery
            ->select([
                'id',
                'gym_id',
                'cash_session_id',
                'membership_id',
                'created_by',
                'type',
                'amount',
                'method',
                'description',
                'occurred_at',
            ])
            ->with([
                'createdBy:id,name',
                'membership:id,client_id',
                'membership.client:id,first_name,last_name',
            ])
            ->orderByDesc('occurred_at')
            ->limit(self::PDF_MAX_DETAIL_ROWS)
            ->get();

        $pdf = Pdf::loadView('reports.export-pdf', [
            'from' => $from,
            'to' => $to,
            'incomeSummary' => $incomeSummary,
            'incomeByMethod' => $incomeByMethod,
            'attendanceSummary' => $attendanceSummary,
            'attendanceByDay' => $attendanceByDay,
            'membershipSummary' => $membershipSummary,
            'movementsCount' => $movementsCount,
            'isPdfDetailTruncated' => $isPdfDetailTruncated,
            'pdfMaxDetailRows' => self::PDF_MAX_DETAIL_ROWS,
            'movements' => $movements,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('reports_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.pdf');
    }

    /**
     * Resolve safe date range from query string.
     *
     * @return array{from: Carbon, to: Carbon}
     */
    private function resolveDateRange(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = isset($data['from'])
            ? Carbon::parse($data['from'])->startOfDay()
            : Carbon::today()->subDays(29)->startOfDay();

        $to = isset($data['to'])
            ? Carbon::parse($data['to'])->endOfDay()
            : Carbon::today()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [
            'from' => $from,
            'to' => $to,
        ];
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function movementsBaseQuery(int $gymId, Carbon $from, Carbon $to)
    {
        return CashMovement::query()
            ->forGym($gymId)
            ->betweenOccurredAt($from, $to);
    }
}
