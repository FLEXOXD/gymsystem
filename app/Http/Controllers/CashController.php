<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCashMovementRequest;
use App\Http\Requests\CloseCashSessionRequest;
use App\Http\Requests\OpenCashSessionRequest;
use App\Models\CashMovement;
use App\Models\CashSession;
use App\Models\GymBranchLink;
use App\Services\CashSessionService;
use App\Support\ActiveGymContext;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CashController extends Controller
{
    public function __construct(
        private readonly CashSessionService $cashSessionService
    ) {
    }

    /**
     * Cash main screen (open/operate/close current session).
     */
    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $isGlobalScope = ActiveGymContext::isGlobal($request);
        $cashGuard = $this->resolveCashGuard($request, $gymId);

        if ($isGlobalScope) {
            $sessions = CashSession::query()
                ->forGyms($gymIds)
                ->select([
                    'id',
                    'gym_id',
                    'opened_by',
                    'closed_by',
                    'opened_at',
                    'closed_at',
                    'opening_balance',
                    'closing_balance',
                    'expected_balance',
                    'difference',
                    'status',
                ])
                ->with(['gym:id,name,slug', 'openedBy:id,name', 'closedBy:id,name'])
                ->orderByDesc('opened_at')
                ->paginate(20)
                ->withQueryString();

            return view('cash.index', [
                'sessions' => $sessions,
            ]);
        }

        $openSession = $this->cashSessionService->getOpenSession($gymId);

        $summary = [
            'income_total' => 0.0,
            'expense_total' => 0.0,
            'expected_balance' => 0.0,
            'movements_count' => 0,
            'income_count' => 0,
            'expense_count' => 0,
        ];
        $methodTotals = collect();
        $latestMovements = collect();

        if ($openSession) {
            $summary = $this->buildSessionSummary($gymId, $openSession->id, (float) $openSession->opening_balance);
            $methodTotals = $this->buildMethodTotals($gymId, $openSession->id);
            $latestMovements = CashMovement::query()
                ->forGym($gymId)
                ->where('cash_session_id', $openSession->id)
                ->select([
                    'id',
                    'gym_id',
                    'cash_session_id',
                    'type',
                    'amount',
                    'method',
                    'membership_id',
                    'created_by',
                    'description',
                    'occurred_at',
                ])
                ->with([
                    'createdBy:id,name',
                    'membership:id,client_id',
                    'membership.client:id,first_name,last_name',
                ])
                ->orderByDesc('occurred_at')
                ->limit(10)
                ->get();
        }

        return view('cash.index', [
            'openSession' => $openSession,
            'summary' => $summary,
            'methodTotals' => $methodTotals,
            'latestMovements' => $latestMovements,
            'cashWriteBlocked' => (bool) ($cashGuard['blocked'] ?? false),
            'cashWriteBlockedReason' => (string) ($cashGuard['reason'] ?? ''),
        ]);
    }

    /**
     * Open a new session for current gym.
     */
    public function open(OpenCashSessionRequest $request): RedirectResponse
    {
        try {
            $gymId = $this->resolveGymId($request);
            $this->assertCashWriteAccess($request, $gymId);
            $data = $request->validated();

            $this->cashSessionService->openSession(
                gymId: $gymId,
                userId: (int) $request->user()->id,
                openingBalance: (float) $data['opening_balance'],
                notes: $data['notes'] ?? null
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cash.index')
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('cash.index')
            ->with('status', 'Turno de caja abierto correctamente.');
    }

    /**
     * Add movement to current open session.
     */
    public function addMovement(AddCashMovementRequest $request): RedirectResponse
    {
        try {
            $gymId = $this->resolveGymId($request);
            $this->assertCashWriteAccess($request, $gymId);
            $data = $request->validated();

            $this->cashSessionService->addMovement(
                gymId: $gymId,
                userId: (int) $request->user()->id,
                type: $data['type'],
                amount: (float) $data['amount'],
                method: $data['method'],
                membershipId: isset($data['membership_id']) ? (int) $data['membership_id'] : null,
                description: $data['description'] ?? null
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cash.index')
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('cash.index')
            ->with('status', 'Movimiento registrado correctamente.');
    }

    /**
     * Close current open session.
     */
    public function close(CloseCashSessionRequest $request): RedirectResponse
    {
        try {
            $gymId = $this->resolveGymId($request);
            $this->assertCashWriteAccess($request, $gymId);
            $data = $request->validated();

            $session = $this->cashSessionService->closeSession(
                gymId: $gymId,
                userId: (int) $request->user()->id,
                closingBalance: (float) $data['closing_balance'],
                notes: $data['notes'] ?? null
            );
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cash.index')
                ->withErrors(['cash' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('cash.sessions.show', $session->id)
            ->with('status', 'Turno de caja cerrado correctamente.');
    }

    /**
     * Show session history for current gym.
     */
    public function sessions(Request $request): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);

        $sessions = CashSession::query()
            ->forGyms($gymIds)
            ->select([
                'id',
                'gym_id',
                'opened_by',
                'closed_by',
                'opened_at',
                'closed_at',
                'opening_balance',
                'closing_balance',
                'expected_balance',
                'difference',
                'status',
            ])
            ->with(['gym:id,name,slug', 'openedBy:id,name', 'closedBy:id,name'])
            ->orderByDesc('opened_at')
            ->paginate(20);

        return view('cash.sessions', [
            'sessions' => $sessions,
        ]);
    }

    /**
     * Show one session detail with movements and totals.
     */
    public function show(Request $request, string $contextGym, int $session): View
    {
        $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);

        $sessionModel = CashSession::query()
            ->forGyms($gymIds)
            ->select([
                'id',
                'gym_id',
                'opened_by',
                'closed_by',
                'opened_at',
                'closed_at',
                'opening_balance',
                'closing_balance',
                'expected_balance',
                'difference',
                'status',
                'notes',
            ])
            ->with([
                'openedBy:id,name',
                'closedBy:id,name',
                'movements' => fn ($query) => $query
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
                    ->orderByDesc('occurred_at')
                    ->with([
                        'createdBy:id,name',
                        'membership:id,client_id',
                        'membership.client:id,first_name,last_name',
                    ]),
            ])
            ->findOrFail($session);

        $summary = $this->buildSessionSummary((int) $sessionModel->gym_id, $sessionModel->id, (float) $sessionModel->opening_balance);
        $methodTotals = $this->buildMethodTotals((int) $sessionModel->gym_id, $sessionModel->id);

        return view('cash.show', [
            'session' => $sessionModel,
            'summary' => $summary,
            'methodTotals' => $methodTotals,
        ]);
    }

    /**
     * Build totals for one session.
     *
     * @return array<string, float|int>
     */
    private function buildSessionSummary(int $gymId, int $sessionId, float $openingBalance): array
    {
        $totals = CashMovement::query()
            ->forGym($gymId)
            ->where('cash_session_id', $sessionId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
            ->selectRaw('COUNT(*) as movements_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN 1 ELSE 0 END), 0) as income_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN 1 ELSE 0 END), 0) as expense_count")
            ->first();

        $incomeTotal = (float) ($totals->income_total ?? 0);
        $expenseTotal = (float) ($totals->expense_total ?? 0);

        return [
            'income_total' => $incomeTotal,
            'expense_total' => $expenseTotal,
            'expected_balance' => round($openingBalance + $incomeTotal - $expenseTotal, 2),
            'movements_count' => (int) ($totals->movements_count ?? 0),
            'income_count' => (int) ($totals->income_count ?? 0),
            'expense_count' => (int) ($totals->expense_count ?? 0),
        ];
    }

    /**
     * Build totals grouped by method.
     */
    private function buildMethodTotals(int $gymId, int $sessionId)
    {
        return CashMovement::query()
            ->forGym($gymId)
            ->where('cash_session_id', $sessionId)
            ->selectRaw('method')
            ->selectRaw('COUNT(*) as movements_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense_total")
            ->groupBy('method')
            ->orderBy('method')
            ->get();
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    /**
     * @return array{blocked:bool,reason:string}
     */
    private function resolveCashGuard(Request $request, int $activeGymId): array
    {
        if (ActiveGymContext::isGlobal($request)) {
            return [
                'blocked' => true,
                'reason' => 'Selecciona una sucursal especifica para operar caja.',
            ];
        }

        if ((bool) $request->attributes->get('gym_context_is_branch', false)) {
            return [
                'blocked' => true,
                'reason' => 'La caja de esta sucursal la gestiona la sede principal.',
            ];
        }

        $link = GymBranchLink::query()
            ->where('branch_gym_id', $activeGymId)
            ->where('status', 'active')
            ->first(['id', 'hub_gym_id', 'cash_managed_by_hub']);

        if (! $link || ! (bool) $link->cash_managed_by_hub) {
            return [
                'blocked' => false,
                'reason' => '',
            ];
        }

        $operatorGymId = (int) ($request->user()?->gym_id ?? 0);
        if ($operatorGymId === (int) $link->hub_gym_id) {
            return [
                'blocked' => false,
                'reason' => '',
            ];
        }

        return [
            'blocked' => true,
            'reason' => 'La caja de esta sucursal la gestiona la sede principal.',
        ];
    }

    private function assertCashWriteAccess(Request $request, int $activeGymId): void
    {
        $guard = $this->resolveCashGuard($request, $activeGymId);
        if ((bool) ($guard['blocked'] ?? false)) {
            throw new RuntimeException((string) ($guard['reason'] ?? 'No autorizado para operar caja en esta sucursal.'));
        }
    }
}
