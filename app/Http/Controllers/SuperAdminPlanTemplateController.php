<?php

namespace App\Http\Controllers;

use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use App\Support\PlanDuration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuperAdminPlanTemplateController extends Controller
{
    public function index(): View
    {
        $plans = SuperAdminPlanTemplate::query()
            ->orderByDesc('id')
            ->get();

        $promotions = SuperAdminPromotionTemplate::query()
            ->with(['planTemplate:id,name'])
            ->orderByDesc('id')
            ->get();

        return view('superadmin.plans', [
            'plans' => $plans,
            'promotions' => $promotions,
        ]);
    }

    public function storePlan(Request $request): RedirectResponse
    {
        $durationUnit = strtolower((string) $request->input('duration_unit', 'days'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'duration_unit' => ['nullable', Rule::in(['days', 'months'])],
            'duration_days' => [
                Rule::requiredIf($durationUnit !== 'months'),
                'nullable',
                'integer',
                'min:1',
                'max:3650',
            ],
            'duration_months' => [
                Rule::requiredIf($durationUnit === 'months'),
                'nullable',
                'integer',
                'min:1',
                'max:120',
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $data = PlanDuration::normalizeForPersistence($data);
        $data['status'] = $data['status'] ?? 'active';

        SuperAdminPlanTemplate::query()->create($data);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Plan base creado correctamente.');
    }

    public function togglePlan(Request $request, int $template): RedirectResponse
    {
        $plan = SuperAdminPlanTemplate::query()->findOrFail($template);

        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $plan->update(['status' => $data['status']]);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Estado de plan base actualizado.');
    }

    public function destroyPlan(int $template): RedirectResponse
    {
        $plan = SuperAdminPlanTemplate::query()->findOrFail($template);
        $plan->delete();

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Plan base eliminado.');
    }

    public function storePromotion(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'plan_template_id' => ['nullable', 'integer', Rule::exists('superadmin_plan_templates', 'id')],
            'type' => ['required', Rule::in(['percentage', 'fixed', 'final_price', 'bonus_days', 'two_for_one', 'bring_friend'])],
            'value' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:1000000'],
        ]);

        SuperAdminPromotionTemplate::query()->create($data);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Promocion base creada correctamente.');
    }

    public function togglePromotion(Request $request, int $promotion): RedirectResponse
    {
        $promotionTemplate = SuperAdminPromotionTemplate::query()->findOrFail($promotion);

        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $promotionTemplate->update(['status' => $data['status']]);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Estado de promocion base actualizado.');
    }

    public function destroyPromotion(int $promotion): RedirectResponse
    {
        $promotionTemplate = SuperAdminPromotionTemplate::query()->findOrFail($promotion);
        $promotionTemplate->delete();

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Promocion base eliminada.');
    }
}

