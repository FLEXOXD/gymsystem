<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Models\Plan;
use App\Models\Promotion;
use App\Services\PlanAccessService;
use App\Support\ActiveGymContext;
use App\Support\PlanDuration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PlanController extends Controller
{
    /**
     * List plans for current gym.
     */
    public function index(Request $request, PlanAccessService $planAccessService): View
    {
        $gymId = $this->resolveGymId($request);
        $gymIds = ActiveGymContext::ids($request);
        $activePlanKey = $planAccessService->currentPlanKeyForGym($gymId);
        $isPlanControl = $activePlanKey === 'basico';

        $plans = Plan::query()
            ->forGyms($gymIds)
            ->select(['id', 'gym_id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price', 'status'])
            ->withCount('memberships')
            ->with(['gym:id,name'])
            ->orderByDesc('id')
            ->get();

        $promotions = Promotion::query()
            ->forGyms($gymIds)
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
            ->with(['plan:id,name', 'gym:id,name'])
            ->orderByDesc('id')
            ->get();

        return view('plans.index', [
            'plans' => $plans,
            'promotions' => $promotions,
            'planControlPlansDashboard' => $this->buildPlanControlPlansDashboard($isPlanControl, $plans),
        ]);
    }

    /**
     * Store a plan for current gym.
     */
    public function store(StorePlanRequest $request): RedirectResponse
    {
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar planes.']);
        }

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
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar planes.']);
        }

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
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar planes.']);
        }

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
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar planes.']);
        }

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
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar promociones.']);
        }

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
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar promociones.']);
        }

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
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar promociones.']);
        }

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
        if (ActiveGymContext::isGlobal($request)) {
            return redirect()->route('plans.index')
                ->withErrors(['plans' => 'Selecciona una sucursal específica para gestionar promociones.']);
        }

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
        $gymId = ActiveGymContext::id($request);
        abort_if(! $gymId, 403, 'El usuario autenticado no tiene gym_id asignado.');

        return (int) $gymId;
    }

    /**
     * @param  Collection<int, Plan>  $plans
     * @return array<string, mixed>|null
     */
    private function buildPlanControlPlansDashboard(bool $isPlanControl, Collection $plans): ?array
    {
        if (! $isPlanControl) {
            return null;
        }

        $totalPlans = (int) $plans->count();
        $activePlans = (int) $plans->where('status', 'active')->count();
        $hiddenPlans = max(0, $totalPlans - $activePlans);
        $basePrice = $plans
            ->where('status', 'active')
            ->pluck('price')
            ->filter(static fn ($value): bool => $value !== null)
            ->map(static fn ($value): float => (float) $value)
            ->min();

        if ($basePrice === null) {
            $basePrice = $plans
                ->pluck('price')
                ->filter(static fn ($value): bool => $value !== null)
                ->map(static fn ($value): float => (float) $value)
                ->min();
        }

        $topPlan = $plans
            ->sortByDesc(static fn (Plan $plan): string => sprintf(
                '%010d-%010d',
                (int) ($plan->memberships_count ?? 0),
                (int) $plan->id
            ))
            ->first();
        $topPlanMemberships = (int) ($topPlan?->memberships_count ?? 0);

        $headline = $totalPlans === 0
            ? 'Todavia no has creado el primer plan de esta sede'
            : ($activePlans === 0
                ? 'Tu catalogo existe, pero aun no hay un plan visible para vender'
                : 'Catalogo claro y listo para vender desde una sola sede');

        $summary = $totalPlans === 0
            ? 'Crea el primer plan para que recepcion, clientes y caja puedan trabajar con una oferta clara desde el inicio.'
            : ($activePlans === 0
                ? 'Activa al menos un plan para que recepcion y clientes puedan renovar o vender sin vueltas.'
                : 'Aqui lees lo esencial del catalogo: cuantos planes estan listos, cual es tu precio base y cual ya mueve membresias.');

        return [
            'headline' => $headline,
            'summary' => $summary,
            'total_plans' => $totalPlans,
            'active_plans' => $activePlans,
            'hidden_plans' => $hiddenPlans,
            'base_price' => $basePrice !== null ? round((float) $basePrice, 2) : null,
            'top_plan_name' => $topPlan?->name ?: 'Sin ventas aun',
            'top_plan_memberships' => $topPlanMemberships,
        ];
    }
}
