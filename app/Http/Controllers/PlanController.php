<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Models\Plan;
use App\Models\Promotion;
use App\Support\PlanDuration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->select(['id', 'gym_id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price', 'status'])
            ->orderByDesc('id')
            ->get();

        $promotions = Promotion::query()
            ->forGym($gymId)
            ->select([
                'id',
                'gym_id',
                'plan_id',
                'name',
                'description',
                'type',
                'value',
                'starts_at',
                'ends_at',
                'status',
                'max_uses',
                'times_used',
            ])
            ->with(['plan:id,name'])
            ->orderByDesc('id')
            ->get();

        return view('plans.index', [
            'plans' => $plans,
            'promotions' => $promotions,
        ]);
    }

    /**
     * Store a plan for current gym.
     */
    public function store(StorePlanRequest $request): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $data = $request->validated();
        $data = PlanDuration::normalizeForPersistence($data);
        $data['gym_id'] = $gymId;
        $data['status'] = $data['status'] ?? 'active';

        Plan::query()->create($data);

        return redirect()
            ->route('plans.index')
            ->with('status', 'Plan creado correctamente.');
    }

    /**
     * Update a plan.
     */
    public function update(UpdatePlanRequest $request, string $contextGym, int $plan): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $planModel = Plan::query()->forGym($gymId)->findOrFail($plan);
        $data = PlanDuration::normalizeForPersistence($request->validated());
        $planModel->update($data);

        return redirect()
            ->route('plans.index')
            ->with('status', 'Plan actualizado correctamente.');
    }

    /**
     * Toggle plan status active/inactive.
     */
    public function toggle(Request $request, string $contextGym, int $plan): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $planModel = Plan::query()->forGym($gymId)->findOrFail($plan);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $planModel->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('plans.index')
            ->with('status', 'Estado del plan actualizado.');
    }

    /**
     * Delete a plan if it has no memberships.
     */
    public function destroy(Request $request, string $contextGym, int $plan): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $planModel = Plan::query()
            ->forGym($gymId)
            ->withCount('memberships')
            ->findOrFail($plan);

        if ((int) $planModel->memberships_count > 0) {
            return redirect()
                ->route('plans.index')
                ->withErrors([
                    'plan' => 'No se puede eliminar un plan con membresías registradas. Puede desactivarlo.',
                ]);
        }

        $planModel->delete();

        return redirect()
            ->route('plans.index')
            ->with('status', 'Plan eliminado correctamente.');
    }

    /**
     * Store promotion for current gym.
     */
    public function storePromotion(StorePromotionRequest $request): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $data = $request->validated();
        $data['gym_id'] = $gymId;
        $data['times_used'] = 0;
        $data['value'] = $data['value'] ?? 0;

        Promotion::query()->create($data);

        return redirect()
            ->route('plans.index')
            ->with('status', 'Promoción creada correctamente.');
    }

    /**
     * Update promotion.
     */
    public function updatePromotion(UpdatePromotionRequest $request, string $contextGym, int $promotion): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $promotionModel = Promotion::query()->forGym($gymId)->findOrFail($promotion);

        $data = $request->validated();
        $data['value'] = $data['value'] ?? 0;

        $promotionModel->update($data);

        return redirect()
            ->route('plans.index')
            ->with('status', 'Promoción actualizada correctamente.');
    }

    /**
     * Toggle promotion status.
     */
    public function togglePromotion(Request $request, string $contextGym, int $promotion): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $promotionModel = Promotion::query()->forGym($gymId)->findOrFail($promotion);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $promotionModel->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('plans.index')
            ->with('status', 'Estado de promoción actualizado.');
    }

    /**
     * Delete promotion.
     */
    public function destroyPromotion(Request $request, string $contextGym, int $promotion): RedirectResponse
    {
        $gymId = $this->resolveGymId($request);
        $promotionModel = Promotion::query()->forGym($gymId)->findOrFail($promotion);

        DB::transaction(function () use ($promotionModel): void {
            $promotionModel->memberships()->update([
                'promotion_id' => null,
            ]);
            $promotionModel->delete();
        });

        return redirect()
            ->route('plans.index')
            ->with('status', 'Promoción eliminada correctamente.');
    }

    private function resolveGymId(Request $request): int
    {
        $gymId = $request->user()?->gym_id;
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }
}
