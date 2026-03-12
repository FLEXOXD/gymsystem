<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Models\ProductSale;
use App\Services\ReportService;
use App\Support\ActiveGymContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymIds, $from, $to);
        $incomeByMethod = $this->reportService->getIncomeByMethod($gymIds, $from, $to);
        $attendanceSummary = $this->reportService->getAttendanceSummary($gymIds, $from, $to);
        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymIds);
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
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $showGymColumn = $isGlobalScope && count($gymIds) > 1;
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymIds, $from, $to);

        $movements = $this->movementsBaseQuery($gymIds, $from, $to)
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
                'gym:id,name',
                'createdBy:id,name',
                'membership:id,client_id',
                'membership.client:id,first_name,last_name,created_by_name_snapshot,created_by_role_snapshot',
            ])
            ->orderByDesc('occurred_at')
            ->paginate(50)
            ->withQueryString();

        return view('reports.income', [
            'from' => $from,
            'to' => $to,
            'incomeSummary' => $incomeSummary,
            'movements' => $movements,
            'showGymColumn' => $showGymColumn,
        ]);
    }

    /**
     * Attendance report by day.
     */
    public function attendance(Request $request): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $attendanceSummary = $this->reportService->getAttendanceSummary($gymIds, $from, $to);
        $attendanceByDay = collect($attendanceSummary['by_day']);
        $rangeDays = max(1, $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) + 1);

        $previousTo = $from->copy()->subDay()->endOfDay();
        $previousFrom = $previousTo->copy()->subDays($rangeDays - 1)->startOfDay();
        $previousAttendanceSummary = $this->reportService->getAttendanceSummary($gymIds, $previousFrom, $previousTo);
        $previousByDay = collect($previousAttendanceSummary['by_day']);

        $currentTotal = (int) $attendanceSummary['total_attendances'];
        $previousTotal = (int) $previousAttendanceSummary['total_attendances'];
        $attendanceDiff = $currentTotal - $previousTotal;
        $attendanceDiffPct = null;
        if ($previousTotal > 0) {
            $attendanceDiffPct = round(($attendanceDiff / $previousTotal) * 100, 1);
        }

        $currentByDate = $attendanceByDay
            ->mapWithKeys(fn ($row) => [Carbon::parse((string) $row->date)->toDateString() => (int) $row->attendances_count]);
        $previousByDate = $previousByDay
            ->mapWithKeys(fn ($row) => [Carbon::parse((string) $row->date)->toDateString() => (int) $row->attendances_count]);

        $comparisonLabels = collect(range(0, $rangeDays - 1))
            ->map(fn (int $offset) => $from->copy()->addDays($offset)->format('d/m'))
            ->values();
        $attendanceCurrentSeries = collect(range(0, $rangeDays - 1))
            ->map(fn (int $offset) => (int) ($currentByDate[$from->copy()->addDays($offset)->toDateString()] ?? 0))
            ->values();
        $attendancePreviousSeries = collect(range(0, $rangeDays - 1))
            ->map(fn (int $offset) => (int) ($previousByDate[$previousFrom->copy()->addDays($offset)->toDateString()] ?? 0))
            ->values();

        return view('reports.attendance', [
            'from' => $from,
            'to' => $to,
            'attendanceSummary' => $attendanceSummary,
            'attendanceByDay' => $attendanceByDay,
            'attendanceComparison' => [
                'current_total' => $currentTotal,
                'previous_total' => $previousTotal,
                'diff' => $attendanceDiff,
                'diff_pct' => $attendanceDiffPct,
                'previous_from' => $previousFrom->copy()->startOfDay(),
                'previous_to' => $previousTo->copy()->endOfDay(),
                'range_days' => $rangeDays,
            ],
            'attendanceComparisonLabels' => $comparisonLabels,
            'attendanceCurrentSeries' => $attendanceCurrentSeries,
            'attendancePreviousSeries' => $attendancePreviousSeries,
        ]);
    }

    /**
     * Membership report (active vs expired clients).
     */
    public function memberships(Request $request): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);

        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymIds);
        $membershipLists = $this->reportService->getMembershipClientLists($gymIds);

        return view('reports.memberships', [
            'membershipSummary' => $membershipSummary,
            'activeClients' => $membershipLists['active_clients'],
            'expiredClients' => $membershipLists['expired_clients'],
        ]);
    }

    /**
     * Sales and inventory report detail.
     */
    public function salesInventory(Request $request): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $showGymColumn = $isGlobalScope && count($gymIds) > 1;
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $schemaReady = $this->salesInventorySchemaReady();

        $salesSummary = [
            'total_sales' => 0,
            'units_sold' => 0,
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'average_ticket' => 0,
        ];
        $inventorySummary = [
            'movement_count' => 0,
            'units_in' => 0,
            'units_out' => 0,
            'manual_adjustments' => 0,
        ];
        $salesByDay = collect();
        $topProducts = collect();
        $lowStockProducts = collect();
        $recentSales = null;

        if ($schemaReady) {
            $salesSummary = $this->reportService->getProductSalesSummary($gymIds, $from, $to);
            $inventorySummary = $this->reportService->getInventoryMovementSummary($gymIds, $from, $to);
            $salesByDay = $this->reportService->getProductSalesByDay($gymIds, $from, $to);
            $topProducts = $this->reportService->getTopSellingProducts($gymIds, $from, $to, 10);
            $lowStockProducts = $this->reportService->getLowStockProducts($gymIds, 10);
            $recentSales = ProductSale::query()
                ->forGyms($gymIds)
                ->betweenSoldAt($from, $to)
                ->with(['product:id,name,category', 'soldBy:id,name', 'client:id,first_name,last_name', 'gym:id,name'])
                ->orderByDesc('sold_at')
                ->paginate(40)
                ->withQueryString();
        }

        return view('reports.sales-inventory', [
            'from' => $from,
            'to' => $to,
            'schemaReady' => $schemaReady,
            'showGymColumn' => $showGymColumn,
            'salesSummary' => $salesSummary,
            'inventorySummary' => $inventorySummary,
            'salesByDay' => $salesByDay,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'recentSales' => $recentSales,
        ]);
    }

    /**
     * Client billing report.
     */
    public function clientEarnings(Request $request): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $showGymColumn = $isGlobalScope && count($gymIds) > 1;
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'source' => ['nullable', 'in:all,membership,sale,mixed'],
            'order' => ['nullable', 'in:amount_desc,amount_asc,last_desc,last_asc,name_asc,name_desc'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $source = (string) ($filters['source'] ?? 'all');
        $order = (string) ($filters['order'] ?? 'amount_desc');
        $userId = isset($filters['user_id']) ? (int) $filters['user_id'] : null;

        $entries = $this->clientBillingEntriesBaseQuery($gymIds, $from, $to);
        $users = (clone $entries)
            ->whereNotNull('entries.source_user_id')
            ->selectRaw('entries.source_user_id as id')
            ->selectRaw('MAX(entries.source_user_name) as name')
            ->groupBy('entries.source_user_id')
            ->orderBy('name')
            ->get();

        if (($userId ?? 0) > 0) {
            $entries->where('entries.source_user_id', $userId);
        }

        if (in_array($source, ['membership', 'sale'], true)) {
            $entries->where('entries.source', $source);
        }

        $clientTotalsQuery = DB::query()
            ->fromSub($entries, 'entries')
            ->selectRaw('entries.client_id')
            ->selectRaw('entries.gym_id')
            ->selectRaw('entries.client_name')
            ->selectRaw('entries.document_number')
            ->selectRaw('entries.gym_name')
            ->selectRaw('COALESCE(SUM(entries.amount), 0) as total_billed')
            ->selectRaw("COALESCE(SUM(CASE WHEN entries.source = 'membership' THEN entries.amount ELSE 0 END), 0) as memberships_billed")
            ->selectRaw("COALESCE(SUM(CASE WHEN entries.source = 'sale' THEN entries.amount ELSE 0 END), 0) as sales_billed")
            ->selectRaw('COUNT(*) as operations_count')
            ->selectRaw('MAX(entries.billed_at) as last_billed_at')
            ->groupBy('entries.client_id', 'entries.gym_id', 'entries.client_name', 'entries.document_number', 'entries.gym_name');

        if ($search !== '') {
            $normalizedSearch = mb_strtolower($search);
            $clientTotalsQuery->where(function ($query) use ($normalizedSearch): void {
                $query
                    ->whereRaw('LOWER(entries.client_name) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereRaw('LOWER(entries.document_number) LIKE ?', ["%{$normalizedSearch}%"]);
            });
        }

        if ($source === 'mixed') {
            $clientTotalsQuery
                ->havingRaw("COALESCE(SUM(CASE WHEN entries.source = 'membership' THEN entries.amount ELSE 0 END), 0) > 0")
                ->havingRaw("COALESCE(SUM(CASE WHEN entries.source = 'sale' THEN entries.amount ELSE 0 END), 0) > 0");
        }

        $summary = DB::query()
            ->fromSub((clone $clientTotalsQuery), 'client_totals')
            ->selectRaw('COUNT(*) as billed_clients')
            ->selectRaw('COALESCE(SUM(client_totals.total_billed), 0) as total_billed')
            ->selectRaw('COALESCE(SUM(client_totals.operations_count), 0) as operations_count')
            ->first();

        $topClient = (clone $clientTotalsQuery)
            ->orderByDesc('total_billed')
            ->orderByDesc('last_billed_at')
            ->first();

        $orderedClientTotals = clone $clientTotalsQuery;
        match ($order) {
            'amount_asc' => $orderedClientTotals->orderBy('total_billed')->orderBy('client_name'),
            'last_desc' => $orderedClientTotals->orderByDesc('last_billed_at')->orderBy('client_name'),
            'last_asc' => $orderedClientTotals->orderBy('last_billed_at')->orderBy('client_name'),
            'name_desc' => $orderedClientTotals->orderByDesc('client_name'),
            'name_asc' => $orderedClientTotals->orderBy('client_name'),
            default => $orderedClientTotals->orderByDesc('total_billed')->orderBy('client_name'),
        };

        $clients = $orderedClientTotals
            ->paginate(50)
            ->withQueryString();

        return view('reports.client-earnings', [
            'from' => $from,
            'to' => $to,
            'showGymColumn' => $showGymColumn,
            'clients' => $clients,
            'summary' => [
                'billed_clients' => (int) ($summary->billed_clients ?? 0),
                'total_billed' => (float) ($summary->total_billed ?? 0),
                'operations_count' => (int) ($summary->operations_count ?? 0),
                'average_per_client' => (int) ($summary->billed_clients ?? 0) > 0
                    ? round(((float) ($summary->total_billed ?? 0)) / max(1, (int) ($summary->billed_clients ?? 0)), 2)
                    : 0,
            ],
            'topClient' => $topClient,
            'users' => $users,
            'filters' => [
                'search' => $search,
                'user_id' => $userId,
                'source' => $source,
                'order' => $order,
            ],
        ]);
    }

    /**
     * Export sales and inventory data to CSV.
     */
    public function exportSalesInventoryCsv(Request $request): Response
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $showGymColumn = $isGlobalScope && count($gymIds) > 1;
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        if (! $this->salesInventorySchemaReady()) {
            abort(422, 'El modulo de ventas e inventario no esta habilitado en esta sede.');
        }

        $salesSummary = $this->reportService->getProductSalesSummary($gymIds, $from, $to);
        $inventorySummary = $this->reportService->getInventoryMovementSummary($gymIds, $from, $to);
        $salesByDay = $this->reportService->getProductSalesByDay($gymIds, $from, $to);
        $topProducts = $this->reportService->getTopSellingProducts($gymIds, $from, $to, 50);

        $filename = 'sales_inventory_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.csv';
        $handle = fopen('php://temp', 'w+');
        if ($handle === false) {
            abort(500, 'No se pudo generar el archivo CSV.');
        }

        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, ['Reporte de ventas e inventario']);
        fputcsv($handle, ['Desde', $from->toDateString(), 'Hasta', $to->toDateString()]);
        fputcsv($handle, ['Alcance', $showGymColumn ? 'Global multi-sede' : 'Sede actual']);
        fputcsv($handle, []);

        fputcsv($handle, ['RESUMEN COMERCIAL']);
        fputcsv($handle, ['Ventas', (int) ($salesSummary['total_sales'] ?? 0)]);
        fputcsv($handle, ['Unidades vendidas', (int) ($salesSummary['units_sold'] ?? 0)]);
        fputcsv($handle, ['Ingreso total', number_format((float) ($salesSummary['total_revenue'] ?? 0), 2, '.', '')]);
        fputcsv($handle, ['Costo total', number_format((float) ($salesSummary['total_cost'] ?? 0), 2, '.', '')]);
        fputcsv($handle, ['Utilidad total', number_format((float) ($salesSummary['total_profit'] ?? 0), 2, '.', '')]);
        fputcsv($handle, ['Ticket promedio', number_format((float) ($salesSummary['average_ticket'] ?? 0), 2, '.', '')]);
        fputcsv($handle, []);

        fputcsv($handle, ['RESUMEN INVENTARIO']);
        fputcsv($handle, ['Movimientos', (int) ($inventorySummary['movement_count'] ?? 0)]);
        fputcsv($handle, ['Unidades que entraron', (int) ($inventorySummary['units_in'] ?? 0)]);
        fputcsv($handle, ['Unidades que salieron', (int) ($inventorySummary['units_out'] ?? 0)]);
        fputcsv($handle, ['Ajustes manuales', (int) ($inventorySummary['manual_adjustments'] ?? 0)]);
        fputcsv($handle, []);

        fputcsv($handle, ['VENTAS POR DIA']);
        fputcsv($handle, ['Fecha', 'Ventas', 'Unidades', 'Ingreso', 'Utilidad']);
        foreach ($salesByDay as $row) {
            fputcsv($handle, [
                (string) $row->date,
                (int) ($row->sales_count ?? 0),
                (int) ($row->units_sold ?? 0),
                number_format((float) ($row->total_revenue ?? 0), 2, '.', ''),
                number_format((float) ($row->total_profit ?? 0), 2, '.', ''),
            ]);
        }
        fputcsv($handle, []);

        $topHeaders = ['Producto'];
        if ($showGymColumn) {
            $topHeaders[] = 'Sede';
        }
        $topHeaders = array_merge($topHeaders, ['Categoria', 'Unidades', 'Ingreso', 'Utilidad']);
        fputcsv($handle, ['TOP PRODUCTOS']);
        fputcsv($handle, $topHeaders);
        foreach ($topProducts as $product) {
            $row = [(string) ($product->product_name ?? '-')];
            if ($showGymColumn) {
                $row[] = (string) ($product->gym_name ?? '-');
            }
            $row[] = (string) ($product->product_category ?? '-');
            $row[] = (int) ($product->units_sold ?? 0);
            $row[] = number_format((float) ($product->total_revenue ?? 0), 2, '.', '');
            $row[] = number_format((float) ($product->total_profit ?? 0), 2, '.', '');
            fputcsv($handle, $row);
        }
        fputcsv($handle, []);

        $detailHeaders = ['Fecha'];
        if ($showGymColumn) {
            $detailHeaders[] = 'Sede';
        }
        $detailHeaders = array_merge($detailHeaders, ['Producto', 'Cliente', 'Usuario', 'Metodo', 'Cantidad', 'Total', 'Costo', 'Utilidad', 'Notas']);
        fputcsv($handle, ['DETALLE DE VENTAS']);
        fputcsv($handle, $detailHeaders);

        ProductSale::query()
            ->forGyms($gymIds)
            ->betweenSoldAt($from, $to)
            ->with(['product:id,name,category', 'soldBy:id,name', 'client:id,first_name,last_name', 'gym:id,name'])
            ->orderBy('id')
            ->chunkById(1000, function ($sales) use ($handle, $showGymColumn): void {
                foreach ($sales as $sale) {
                    $row = [
                        $sale->sold_at?->format('Y-m-d H:i') ?? '',
                    ];
                    if ($showGymColumn) {
                        $row[] = (string) ($sale->gym?->name ?? '-');
                    }
                    $row[] = (string) ($sale->product?->name ?? '-');
                    $row[] = (string) ($sale->client?->full_name ?? 'Venta sin cliente');
                    $row[] = (string) ($sale->soldBy?->name ?? '-');
                    $row[] = match ((string) $sale->payment_method) {
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        default => (string) $sale->payment_method,
                    };
                    $row[] = (int) ($sale->quantity ?? 0);
                    $row[] = number_format((float) ($sale->total_amount ?? 0), 2, '.', '');
                    $row[] = number_format((float) ($sale->total_cost ?? 0), 2, '.', '');
                    $row[] = number_format((float) ($sale->total_profit ?? 0), 2, '.', '');
                    $row[] = (string) ($sale->notes ?? '');
                    fputcsv($handle, $row);
                }
            });

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent !== false ? $csvContent : '', 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Export reports data to CSV.
     */
    public function exportCsv(Request $request): Response
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $showGymColumn = $isGlobalScope && count($gymIds) > 1;
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymIds, $from, $to);
        $incomeByMethod = $this->reportService->getIncomeByMethod($gymIds, $from, $to);
        $attendanceSummary = $this->reportService->getAttendanceSummary($gymIds, $from, $to);
        $attendanceByDay = collect($attendanceSummary['by_day']);
        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymIds);

        $filename = 'reports_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.csv';
        $handle = fopen('php://temp', 'w+');
        if ($handle === false) {
            abort(500, 'No se pudo generar el archivo CSV.');
        }

        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($handle, ['Reportes Profesionales - Gimnasio']);
        fputcsv($handle, ['Desde', $from->toDateString(), 'Hasta', $to->toDateString()]);
        fputcsv($handle, ['Alcance', $showGymColumn ? 'Global multi-sede' : 'Sede actual']);
        fputcsv($handle, []);

        fputcsv($handle, ['RESUMEN INGRESOS']);
        fputcsv($handle, ['Total ingresos', number_format((float) $incomeSummary['total_income'], 2, '.', '')]);
        fputcsv($handle, ['Total egresos', number_format((float) $incomeSummary['total_expense'], 2, '.', '')]);
        fputcsv($handle, ['Balance', number_format((float) $incomeSummary['balance'], 2, '.', '')]);
        fputcsv($handle, ['Total movimientos', (int) $incomeSummary['total_movements']]);
        fputcsv($handle, []);

        fputcsv($handle, ['INGRESOS POR Método']);
        fputcsv($handle, ['Método', 'Ingresos', 'Egresos', 'Balance', 'Movimientos']);
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

        fputcsv($handle, ['Membresías']);
        fputcsv($handle, ['Activos', (int) $membershipSummary['active']]);
        fputcsv($handle, ['Vencidos', (int) $membershipSummary['expired']]);
        fputcsv($handle, ['Total clientes', (int) $membershipSummary['total_clients']]);
        fputcsv($handle, []);

        fputcsv($handle, ['DETALLE MOVIMIENTOS']);
        $detailHeaders = ['ID', 'Fecha', 'Tipo', 'Metodo', 'Monto'];
        if ($showGymColumn) {
            $detailHeaders[] = 'Sede';
        }
        $detailHeaders = array_merge($detailHeaders, ['Cliente', 'Alta cliente', 'Usuario', 'Descripcion']);
        fputcsv($handle, ['ID', 'Fecha', 'Tipo', 'Método', 'Monto', 'Cliente', 'Alta cliente', 'Usuario', 'Descripción']);
        if ($showGymColumn) {
            fputcsv($handle, $detailHeaders);
        }

        $resolvedGymIds = collect($gymIds)
            ->map(static fn ($id): int => (int) $id)
            ->filter(static fn (int $id): bool => $id > 0)
            ->values()
            ->all();

        $movementsQuery = CashMovement::query()
            ->whereIn('cash_movements.gym_id', $resolvedGymIds)
            ->whereBetween('cash_movements.occurred_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->leftJoin('users as users', 'users.id', '=', 'cash_movements.created_by')
            ->leftJoin('gyms as gyms', 'gyms.id', '=', 'cash_movements.gym_id')
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
                'gyms.name as gym_name',
                'clients.first_name as client_first_name',
                'clients.last_name as client_last_name',
                'clients.created_by_name_snapshot as client_created_by_name_snapshot',
                'clients.created_by_role_snapshot as client_created_by_role_snapshot',
            ])
            ->orderBy('cash_movements.id');

        $movementsQuery->chunkById(1000, function ($rows) use ($handle, $showGymColumn): void {
            foreach ($rows as $row) {
                $clientName = trim(((string) ($row->client_first_name ?? '')).' '.((string) ($row->client_last_name ?? '')));
                $clientCreator = \App\Support\ClientAudit::actorDisplay(
                    (string) ($row->client_created_by_name_snapshot ?? ''),
                    (string) ($row->client_created_by_role_snapshot ?? '')
                );

                $detailRow = [
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
                ];

                if ($showGymColumn) {
                    $detailRow[] = (string) ($row->gym_name ?? '');
                }

                $detailRow[] = $clientName;
                $detailRow[] = $clientCreator;
                $detailRow[] = (string) ($row->user_name ?? '');
                $detailRow[] = (string) ($row->description ?? '');

                fputcsv($handle, $detailRow);
            }
        }, 'cash_movements.id', 'movement_id');

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent !== false ? $csvContent : '', 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Export reports data to PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $showGymColumn = $isGlobalScope && count($gymIds) > 1;
        ['from' => $from, 'to' => $to] = $this->resolveDateRange($request);

        $incomeSummary = $this->reportService->getIncomeSummary($gymIds, $from, $to);
        $incomeByMethod = $this->reportService->getIncomeByMethod($gymIds, $from, $to);
        $attendanceSummary = $this->reportService->getAttendanceSummary($gymIds, $from, $to);
        $attendanceByDay = collect($attendanceSummary['by_day']);
        $membershipSummary = $this->reportService->getMembershipStatusSummary($gymIds);

        $movementsQuery = $this->movementsBaseQuery($gymIds, $from, $to);
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
                'gym:id,name',
                'createdBy:id,name',
                'membership:id,client_id',
                'membership.client:id,first_name,last_name,created_by_name_snapshot,created_by_role_snapshot',
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
            'showGymColumn' => $showGymColumn,
            'movements' => $movements,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('reports_'.$from->format('Ymd').'_to_'.$to->format('Ymd').'.pdf');
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
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    private function movementsBaseQuery(array $gymIds, Carbon $from, Carbon $to)
    {
        return CashMovement::query()
            ->forGyms($gymIds)
            ->betweenOccurredAt($from, $to);
    }

    private function salesInventorySchemaReady(): bool
    {
        return Schema::hasTable('products')
            && Schema::hasTable('product_sales')
            && Schema::hasTable('product_stock_movements');
    }

    private function clientBillingEntriesBaseQuery(array $gymIds, Carbon $from, Carbon $to)
    {
        $membershipEntries = DB::table('cash_movements as cm')
            ->join('memberships as m', function ($join): void {
                $join->on('m.id', '=', 'cm.membership_id')
                    ->on('m.gym_id', '=', 'cm.gym_id');
            })
            ->join('clients as c', function ($join): void {
                $join->on('c.id', '=', 'm.client_id')
                    ->on('c.gym_id', '=', 'cm.gym_id');
            })
            ->leftJoin('users as u', 'u.id', '=', 'cm.created_by')
            ->leftJoin('gyms as g', 'g.id', '=', 'cm.gym_id')
            ->whereIn('cm.gym_id', $gymIds)
            ->where('cm.type', 'income')
            ->whereBetween('cm.occurred_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('cm.gym_id as gym_id')
            ->selectRaw('m.client_id as client_id')
            ->selectRaw("TRIM(CONCAT(COALESCE(c.first_name, ''), ' ', COALESCE(c.last_name, ''))) as client_name")
            ->selectRaw('c.document_number as document_number')
            ->selectRaw('g.name as gym_name')
            ->selectRaw('cm.created_by as source_user_id')
            ->selectRaw('u.name as source_user_name')
            ->selectRaw('cm.amount as amount')
            ->selectRaw('cm.occurred_at as billed_at')
            ->selectRaw("'membership' as source");

        if (! Schema::hasTable('product_sales')) {
            return DB::query()->fromSub($membershipEntries, 'entries');
        }

        $salesEntries = DB::table('product_sales as ps')
            ->join('clients as c', function ($join): void {
                $join->on('c.id', '=', 'ps.client_id')
                    ->on('c.gym_id', '=', 'ps.gym_id');
            })
            ->leftJoin('users as u', 'u.id', '=', 'ps.sold_by')
            ->leftJoin('gyms as g', 'g.id', '=', 'ps.gym_id')
            ->whereIn('ps.gym_id', $gymIds)
            ->whereNotNull('ps.client_id')
            ->whereBetween('ps.sold_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('ps.gym_id as gym_id')
            ->selectRaw('ps.client_id as client_id')
            ->selectRaw("TRIM(CONCAT(COALESCE(c.first_name, ''), ' ', COALESCE(c.last_name, ''))) as client_name")
            ->selectRaw('c.document_number as document_number')
            ->selectRaw('g.name as gym_name')
            ->selectRaw('ps.sold_by as source_user_id')
            ->selectRaw('u.name as source_user_name')
            ->selectRaw('ps.total_amount as amount')
            ->selectRaw('ps.sold_at as billed_at')
            ->selectRaw("'sale' as source");

        return DB::query()->fromSub($membershipEntries->unionAll($salesEntries), 'entries');
    }
}

