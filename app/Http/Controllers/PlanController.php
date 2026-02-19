<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Models\Plan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * List plans for current gym.
     */
    public function index(Request $request): View
    {
        $gymId = $this->resolveGymId($request);

        $plans = Plan::query()
            ->forGym($gymId)
            ->select(['id', 'gym_id', 'name', 'duration_days', 'price', 'status'])
            ->orderByDesc('id')
            ->get();

        return view('plans.index', [
            'plans' => $plans,
        ]);
    }

    /**
     * Store a plan for current gym.
     */
    public function store(StorePlanRequest $request): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $data = $request->validated();
        $data['gym_id'] = $gymId;
        $data['status'] = $data['status'] ?? 'active';

        Plan::query()->create($data);

        return redirect()
            ->route('plans.index')
            ->with('status', 'Plan creado correctamente.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }
}
