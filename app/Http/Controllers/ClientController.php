<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\CashMovement;
use App\Models\Client;
use App\Models\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClientController extends Controller
{
    /**
     * Display all clients for current gym.
     */
    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);
        $search = trim((string) $request->query('q', ''));

        $clientsQuery = Client::query()
            ->where('gym_id', $gymId)
            ->orderByDesc('id');

        if ($search !== '') {
            $clientsQuery->where(function ($query) use ($search): void {
                $query->where('document_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $clients = $clientsQuery->paginate(20)->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    /**
     * Store a new client for current gym.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $data = $request->validated();
        $data['gym_id'] = $gymId;
        $data['status'] = $data['status'] ?? 'active';

        $client = Client::query()->create($data);

        return redirect()
            ->route('clients.show', $client->id)
            ->with('status', 'Cliente creado correctamente.');
    }

    /**
     * Show one client scoped by gym.
     */
    public function show(Request $request, int $client): View
    {
        $gymId = $this->resolveGymId($request);

        $clientModel = Client::query()
            ->where('gym_id', $gymId)
            ->with([
                'credentials' => fn ($query) => $query->orderByDesc('id'),
                'memberships' => fn ($query) => $query
                    ->with('plan')
                    ->orderByDesc('ends_at')
                    ->orderByDesc('id'),
                'attendances' => fn ($query) => $query
                    ->with('credential')
                    ->orderByDesc('date')
                    ->orderByDesc('time')
                    ->limit(10),
            ])
            ->findOrFail($client);

        $plans = Plan::query()
            ->where('gym_id', $gymId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $activeQrCredential = $clientModel->credentials
            ->where('type', 'qr')
            ->where('status', 'active')
            ->first();

        $activeQrSvg = null;
        if ($activeQrCredential) {
            $activeQrSvg = QrCode::format('svg')
                ->size(210)
                ->margin(1)
                ->generate($activeQrCredential->value);
        }

        $latestMembership = $clientModel->memberships->first();
        $membershipState = 'none';
        $today = now()->toDateString();

        if ($latestMembership) {
            $isMembershipActive = $latestMembership->status === 'active'
                && $latestMembership->ends_at !== null
                && $latestMembership->ends_at->toDateString() >= $today;

            $membershipState = $isMembershipActive ? 'active' : 'expired';
        }

        $recentMembershipPayments = collect();
        $membershipIds = $clientModel->memberships->pluck('id')->filter()->values();
        if ($membershipIds->isNotEmpty()) {
            $recentMembershipPayments = CashMovement::query()
                ->where('gym_id', $gymId)
                ->whereIn('membership_id', $membershipIds)
                ->where('type', 'income')
                ->with(['createdBy', 'membership.plan'])
                ->orderByDesc('occurred_at')
                ->limit(10)
                ->get();
        }

        return view('clients.show', [
            'client' => $clientModel,
            'plans' => $plans,
            'activeQrCredential' => $activeQrCredential,
            'activeQrSvg' => $activeQrSvg,
            'latestMembership' => $latestMembership,
            'membershipState' => $membershipState,
            'recentMembershipPayments' => $recentMembershipPayments,
        ]);
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }
}
