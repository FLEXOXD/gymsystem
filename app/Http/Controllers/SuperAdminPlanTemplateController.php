<?php

namespace App\Http\Controllers;

use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use App\Support\PlanDuration;
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
        $schemaReady = $this->supportsCommercialPlanCatalog();
        $promotionSchemaReady = $this->supportsPromotionCatalog();

        if (! $schemaReady) {
            return view('superadmin.plans', [
                'plans' => collect(),
                'basePlans' => collect(),
                'internalPlans' => collect(),
                'promotions' => collect(),
                'planPresentation' => SuperAdminPlanPresentation::metadata(),
                'schemaReady' => false,
            ]);
        }

        SuperAdminPlanTemplate::ensureDefaultCatalog();

        $basePlans = SuperAdminPlanTemplate::query()
            ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
            ->orderByRaw(SuperAdminPlanCatalog::orderCaseSql('plan_key'))
            ->get();

        $internalPlans = SuperAdminPlanTemplate::query()
            ->where(function ($query): void {
                $query
                    ->whereNull('plan_key')
                    ->orWhereNotIn('plan_key', SuperAdminPlanCatalog::keys());
            })
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->orderByDesc('id')
            ->get();

        $promotions = $promotionSchemaReady
            ? SuperAdminPromotionTemplate::query()
                ->with(['planTemplate:id,name,plan_key'])
                ->whereHas('planTemplate', function ($query): void {
                    $query->whereIn('plan_key', SuperAdminPlanCatalog::keys());
                })
                ->orderByDesc('id')
                ->get()
            : collect();

        return view('superadmin.plans', [
            'plans' => $basePlans->values(),
            'basePlans' => $basePlans,
            'internalPlans' => $internalPlans,
            'promotions' => $promotions,
            'planPresentation' => SuperAdminPlanPresentation::metadata(),
            'schemaReady' => true,
        ]);
    }

    public function storePlan(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectWhenSchemaNotReady()) {
            return $redirect;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'feature_plan_key' => ['required', Rule::in(SuperAdminPlanCatalog::keys())],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'lte:price'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $featurePlanKey = strtolower(trim((string) $data['feature_plan_key']));
        $durationPayload = $this->resolveBaseDurationPayload($featurePlanKey);

        SuperAdminPlanTemplate::query()->create([
            'plan_key' => null,
            'feature_plan_key' => $featurePlanKey,
            'name' => trim((string) $data['name']),
            'duration_unit' => $durationPayload['duration_unit'],
            'duration_days' => $durationPayload['duration_days'],
            'duration_months' => $durationPayload['duration_months'],
            'price' => (float) $data['price'],
            'discount_price' => array_key_exists('discount_price', $data) && $data['discount_price'] !== null
                ? (float) $data['discount_price']
                : null,
            'status' => (string) $data['status'],
        ]);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Plantilla interna creada correctamente.');
    }

    public function updatePlanPricing(Request $request, int $template): RedirectResponse
    {
        if ($redirect = $this->redirectWhenSchemaNotReady()) {
            return $redirect;
        }

        $plan = SuperAdminPlanTemplate::query()->findOrFail($template);

        $rules = [
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'lte:price'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];

        if (! $plan->isBaseCatalog()) {
            $rules['name'] = ['required', 'string', 'max:120'];
            $rules['feature_plan_key'] = ['required', Rule::in(SuperAdminPlanCatalog::keys())];
        }

        $data = $request->validate($rules);

        $payload = [
            'price' => (float) $data['price'],
            'discount_price' => array_key_exists('discount_price', $data) && $data['discount_price'] !== null
                ? (float) $data['discount_price']
                : null,
            'status' => (string) ($data['status'] ?? $plan->status),
        ];

        if ($plan->isBaseCatalog()) {
            $payload['feature_plan_key'] = (string) $plan->plan_key;
        } else {
            $payload['name'] = trim((string) $data['name']);
            $payload['feature_plan_key'] = strtolower(trim((string) $data['feature_plan_key']));
        }

        $plan->update($payload);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', $plan->isBaseCatalog() ? 'Plan base actualizado.' : 'Plantilla interna actualizada.');
    }

    public function togglePlan(int $template): RedirectResponse
    {
        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Usa la edicion de cada plan para actualizar su estado.');
    }

    public function destroyPlan(int $template): RedirectResponse
    {
        if ($redirect = $this->redirectWhenSchemaNotReady()) {
            return $redirect;
        }

        $plan = SuperAdminPlanTemplate::query()->findOrFail($template);

        if ($plan->isBaseCatalog()) {
            return redirect()
                ->route('superadmin.plan-templates.index')
                ->with('status', 'No se puede eliminar planes base del catalogo fijo.');
        }

        $plan->delete();

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Plantilla interna eliminada.');
    }

    public function storePromotion(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectWhenPromotionSchemaNotReady()) {
            return $redirect;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'plan_template_id' => [
                'required',
                'integer',
                Rule::exists('superadmin_plan_templates', 'id')->where(function ($query): void {
                    $query->whereIn('plan_key', SuperAdminPlanCatalog::keys());
                }),
            ],
            'type' => ['required', Rule::in(['percentage', 'fixed', 'final_price', 'bonus_days', 'two_for_one', 'bring_friend'])],
            'value' => [
                Rule::requiredIf(function () use ($request): bool {
                    return in_array((string) $request->input('type'), ['percentage', 'fixed', 'final_price', 'bonus_days'], true);
                }),
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
        ], [
            'value.required' => 'Indica el valor comercial de la promocion para poder calcular el total.',
        ]);

        SuperAdminPromotionTemplate::query()->create($data);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Promocion base creada correctamente.');
    }

    public function togglePromotion(Request $request, int $promotion): RedirectResponse
    {
        if ($redirect = $this->redirectWhenPromotionSchemaNotReady()) {
            return $redirect;
        }

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
        if ($redirect = $this->redirectWhenPromotionSchemaNotReady()) {
            return $redirect;
        }

        $promotionTemplate = SuperAdminPromotionTemplate::query()->findOrFail($promotion);
        $promotionTemplate->delete();

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Promocion base eliminada.');
    }

    /**
     * @return array{duration_unit:string,duration_days:int,duration_months:?int}
     */
    private function resolveBaseDurationPayload(string $featurePlanKey): array
    {
        $default = collect(SuperAdminPlanCatalog::defaults())
            ->firstWhere('plan_key', $featurePlanKey);

        return PlanDuration::normalizeForPersistence([
            'duration_unit' => (string) ($default['duration_unit'] ?? 'days'),
            'duration_days' => (int) ($default['duration_days'] ?? 30),
            'duration_months' => isset($default['duration_months']) ? (int) $default['duration_months'] : 1,
        ]);
    }

    private function redirectWhenSchemaNotReady(): ?RedirectResponse
    {
        if ($this->supportsCommercialPlanCatalog()) {
            return null;
        }

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Ejecuta php artisan migrate antes de editar el catalogo comercial.');
    }

    private function redirectWhenPromotionSchemaNotReady(): ?RedirectResponse
    {
        if ($this->supportsCommercialPlanCatalog() && $this->supportsPromotionCatalog()) {
            return null;
        }

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Ejecuta php artisan migrate antes de editar promociones del catalogo comercial.');
    }

    private function supportsCommercialPlanCatalog(): bool
    {
        return Schema::hasTable('superadmin_plan_templates')
            && Schema::hasColumns('superadmin_plan_templates', ['id', 'plan_key', 'feature_plan_key', 'discount_price', 'status']);
    }

    private function supportsPromotionCatalog(): bool
    {
        return Schema::hasTable('superadmin_promotion_templates')
            && Schema::hasColumns('superadmin_promotion_templates', ['plan_template_id', 'name', 'type', 'status']);
    }
}
