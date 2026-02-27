<?php

namespace App\Http\Controllers;

use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use App\Support\SuperAdminPlanCatalog;
use App\Support\SuperAdminPlanPresentation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SuperAdminPlanTemplateController extends Controller
{
    public function index(): View
    {
        $schemaReady = Schema::hasTable('superadmin_plan_templates')
            && Schema::hasColumns('superadmin_plan_templates', ['plan_key', 'discount_price']);

        if (! $schemaReady) {
            return view('superadmin.plans', [
                'plans' => SuperAdminPlanTemplate::query()->orderByDesc('id')->get(),
                'promotions' => SuperAdminPromotionTemplate::query()
                    ->with(['planTemplate:id,name'])
                    ->orderByDesc('id')
                    ->get(),
                'planPresentation' => SuperAdminPlanPresentation::metadata(),
                'schemaReady' => false,
            ]);
        }

        SuperAdminPlanTemplate::ensureDefaultCatalog();

        $plans = SuperAdminPlanTemplate::query()
            ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
            ->orderByRaw(SuperAdminPlanCatalog::orderCaseSql('plan_key'))
            ->get();

        $promotions = SuperAdminPromotionTemplate::query()
            ->with(['planTemplate:id,name'])
            ->orderByDesc('id')
            ->get();

        return view('superadmin.plans', [
            'plans' => $plans,
            'promotions' => $promotions,
            'planPresentation' => SuperAdminPlanPresentation::metadata(),
            'schemaReady' => true,
        ]);
    }

    public function storePlan(): RedirectResponse
    {
        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Los planes base son fijos. Solo puedes editar precio y descuento.');
    }

    public function updatePlanPricing(Request $request, int $template): RedirectResponse
    {
        $plan = SuperAdminPlanTemplate::query()
            ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
            ->findOrFail($template);

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'lte:price'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $plan->update([
            'price' => (float) $data['price'],
            'discount_price' => array_key_exists('discount_price', $data) && $data['discount_price'] !== null
                ? (float) $data['discount_price']
                : null,
            'status' => (string) ($data['status'] ?? $plan->status),
        ]);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Plan base actualizado.');
    }

    public function togglePlan(int $template): RedirectResponse
    {
        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Usa la edición de cada plan para actualizar su estado.');
    }

    public function destroyPlan(int $template): RedirectResponse
    {
        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'No se puede eliminar planes base del catalogo fijo.');
    }

    public function storePromotion(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'plan_template_id' => [
                'nullable',
                'integer',
                Rule::exists('superadmin_plan_templates', 'id')->where(function ($query): void {
                    $query->whereIn('plan_key', SuperAdminPlanCatalog::keys());
                }),
            ],
            'type' => ['required', Rule::in(['percentage', 'fixed', 'final_price', 'bonus_days', 'two_for_one', 'bring_friend'])],
            'value' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'duration_months' => ['nullable', 'integer', 'min:1', 'max:60'],
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
