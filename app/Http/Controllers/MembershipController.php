<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Plan;
use App\Services\CashSessionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class MembershipController extends Controller
{
    public function __construct(
        private readonly CashSessionService $cashSessionService
    ) {
    }

    /**
     * Store membership for a client in current gym.
     */
    public function store(Request $request): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);

        $data = $request->validate([
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('plans', 'id')->where(
                    fn ($query) => $query
                        ->where('gym_id', $gymId)
                        ->where('status', 'active')
                ),
            ],
            'starts_at' => ['required', 'date'],
            'status' => ['nullable', Rule::in(['active', 'expired', 'cancelled'])],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'transfer'])],
        ]);

        $plan = Plan::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'name', 'duration_days', 'price', 'status'])
            ->findOrFail($data['plan_id']);

        $startsAt = Carbon::parse($data['starts_at'])->startOfDay();
        $endsAt = (clone $startsAt)->addDays($plan->duration_days - 1);

        $openSession = $this->cashSessionService->getOpenSession($gymId);
        if (! $openSession) {
            return redirect()
                ->route('clients.show', $data['client_id'])
                ->withErrors([
                    'cash' => 'Debe abrir caja para cobrar.',
                ])
                ->withInput();
        }

        $membership = null;
        try {
            DB::transaction(function () use (
                $gymId,
                $data,
                $plan,
                $startsAt,
                $endsAt,
                $request,
                &$membership
            ): void {
                $membership = Membership::query()->create([
                    'gym_id' => $gymId,
                    'client_id' => $data['client_id'],
                    'plan_id' => $plan->id,
                    'starts_at' => $startsAt->toDateString(),
                    'ends_at' => $endsAt->toDateString(),
                    'status' => $data['status'] ?? 'active',
                ]);

                $this->cashSessionService->addMovement(
                    gymId: $gymId,
                    userId: (int) $request->user()->id,
                    type: 'income',
                    amount: (float) $plan->price,
                    method: $data['payment_method'],
                    membershipId: $membership->id,
                    description: 'Cobro membresia #'.$membership->id.' - Plan '.$plan->name
                );
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('clients.show', $data['client_id'])
                ->withErrors([
                    'cash' => $exception->getMessage(),
                ])
                ->withInput();
        }

        return redirect()
            ->route('clients.show', $data['client_id'])
            ->with('status', 'Membresia creada y cobrada correctamente en caja.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }
}
