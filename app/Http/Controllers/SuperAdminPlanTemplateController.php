<?php

namespace App\Http\Controllers;

use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use App\Services\SuperAdminCommercialPlanService;
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
    public function __construct(
        private readonly SuperAdminCommercialPlanService $commercialPlanService
    ) {
    }

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

        $basePlans = $this->commercialPlanService->basePlans();
        $internalPlans = $this->commercialPlanService->commercialPlans();
        $promotionPlans = $internalPlans
            ->where('status', 'active')
            ->values();

        if ($promotionPlans->isEmpty()) {
            $promotionPlans = $this->commercialPlanService->publicSelections()
                ->where('status', 'active')
                ->values();
        }

        $promotions = $promotionSchemaReady
            ? SuperAdminPromotionTemplate::query()
                ->with(['planTemplate:id,name,plan_key,feature_plan_key'])
                ->orderByDesc('id')
                ->get()
            : collect();

        return view('superadmin.plans', [
            'plans' => $basePlans->values(),
            'basePlans' => $basePlans,
            'internalPlans' => $internalPlans,
            'promotionPlans' => $promotionPlans,
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
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'lte:price'],
            'offer_text' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $durationPayload = $this->resolveBaseDurationPayload('basico');

        SuperAdminPlanTemplate::query()->create([
            'plan_key' => null,
            'feature_plan_key' => null,
            'assigned_plan_template_id' => null,
            'name' => trim((string) $data['name']),
            'duration_unit' => $durationPayload['duration_unit'],
            'duration_days' => $durationPayload['duration_days'],
            'duration_months' => $durationPayload['duration_months'],
            'price' => (float) $data['price'],
            'discount_price' => array_key_exists('discount_price', $data) && $data['discount_price'] !== null
                ? (float) $data['discount_price']
                : null,
            'offer_text' => trim((string) ($data['offer_text'] ?? '')) !== '' ? trim((string) $data['offer_text']) : null,
            'status' => (string) $data['status'],
        ]);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Plan comercial creado correctamente.');
    }

    public function updatePlanPricing(Request $request, int $template): RedirectResponse
    {
        if ($redirect = $this->redirectWhenSchemaNotReady()) {
            return $redirect;
        }

        $plan = SuperAdminPlanTemplate::query()->findOrFail($template);

        if ($plan->isBaseCatalog()) {
            $data = $request->validate([
                'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
                'offer_text' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', Rule::in(['active', 'inactive'])],
            ]);

            $plan->update([
                'price' => (float) $data['price'],
                'offer_text' => trim((string) ($data['offer_text'] ?? '')) !== '' ? trim((string) $data['offer_text']) : null,
                'status' => (string) ($data['status'] ?? $plan->status),
                'feature_plan_key' => (string) $plan->plan_key,
            ]);

            return redirect()
                ->route('superadmin.plan-templates.index')
                ->with('status', 'Plan base actualizado.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'lte:price'],
            'offer_text' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];

        $data = $request->validate($rules);

        $payload = [
            'name' => trim((string) $data['name']),
            'price' => (float) $data['price'],
            'discount_price' => array_key_exists('discount_price', $data) && $data['discount_price'] !== null
                ? (float) $data['discount_price']
                : null,
            'offer_text' => trim((string) ($data['offer_text'] ?? '')) !== '' ? trim((string) $data['offer_text']) : null,
            'status' => (string) ($data['status'] ?? $plan->status),
        ];

        $plan->update($payload);

        return redirect()
            ->route('superadmin.plan-templates.index')
            ->with('status', 'Plan comercial actualizado.');
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
            ->with('status', 'Plan comercial eliminado.');
    }

    public function storePromotion(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectWhenPromotionSchemaNotReady()) {
            return $redirect;
        }

        $supportsDurationUnit = $this->supportsPromotionDurationUnitColumns();

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'plan_template_id' => [
                'nullable',
                'integer',
                Rule::exists('superadmin_plan_templates', 'id')->where(function ($query): void {
                    $query->where('status', 'active');
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
        ];

        if ($supportsDurationUnit) {
            $rules['duration_unit'] = ['required', Rule::in(['days', 'months'])];
            $rules['duration_months'] = [
                Rule::requiredIf(fn () => strtolower(trim((string) $request->input('duration_unit', 'months'))) === 'months'),
                'nullable',
                'integer',
                'min:1',
                'max:60',
            ];
            $rules['duration_days'] = [
                Rule::requiredIf(fn () => strtolower(trim((string) $request->input('duration_unit', 'months'))) === 'days'),
                'nullable',
                'integer',
                'min:1',
                'max:365',
            ];
        } else {
            $rules['duration_months'] = ['required', 'integer', 'min:1', 'max:60'];
        }

        $data = $request->validate($rules, [
            'value.required' => 'Indica el valor comercial de la promocion para poder calcular el total.',
            'duration_months.required' => 'Indica cuantos meses dura la promocion.',
            'duration_days.required' => 'Indica cuantos dias dura la promocion.',
        ]);

        $payload = [
            'name' => trim((string) $data['name']),
            'description' => trim((string) ($data['description'] ?? '')) !== '' ? trim((string) $data['description']) : null,
            'plan_template_id' => array_key_exists('plan_template_id', $data) && $data['plan_template_id'] !== null
                ? (int) $data['plan_template_id']
                : null,
            'type' => (string) $data['type'],
            'value' => array_key_exists('value', $data) && $data['value'] !== null ? (float) $data['value'] : null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'status' => (string) $data['status'],
            'max_uses' => array_key_exists('max_uses', $data) && $data['max_uses'] !== null ? (int) $data['max_uses'] : null,
        ];

        if ($supportsDurationUnit) {
            $durationUnit = strtolower(trim((string) ($data['duration_unit'] ?? 'months')));
            if ($durationUnit === 'days') {
                $payload['duration_unit'] = 'days';
                $payload['duration_days'] = max(1, (int) ($data['duration_days'] ?? 1));
                $payload['duration_months'] = null;
            } else {
                $payload['duration_unit'] = 'months';
                $payload['duration_months'] = max(1, (int) ($data['duration_months'] ?? 1));
                $payload['duration_days'] = null;
            }
        } else {
            $payload['duration_months'] = max(1, (int) ($data['duration_months'] ?? 1));
        }

        SuperAdminPromotionTemplate::query()->create($payload);

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
        return $this->commercialPlanService->supportsCommercialCatalog();
    }

    private function supportsPromotionCatalog(): bool
    {
        return Schema::hasTable('superadmin_promotion_templates')
            && Schema::hasColumns('superadmin_promotion_templates', ['plan_template_id', 'name', 'type', 'status']);
    }

    private function supportsPromotionDurationUnitColumns(): bool
    {
        return Schema::hasTable('superadmin_promotion_templates')
            && Schema::hasColumns('superadmin_promotion_templates', ['duration_unit', 'duration_days']);
    }
}
