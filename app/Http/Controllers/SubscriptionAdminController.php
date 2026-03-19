<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use App\Services\SuperAdminCommercialPlanService;
use App\Services\SuperAdminPlanPricingService;
use App\Services\SubscriptionService;
use App\Support\SuperAdminPlanCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class SubscriptionAdminController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly SuperAdminPlanPricingService $planPricingService,
        private readonly SuperAdminCommercialPlanService $commercialPlanService
    ) {
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('superadmin.gyms.index');
    }

    /**
     * Renew a gym subscription.
     */
    public function renew(Request $request, int $gym): RedirectResponse
    {
        try {
            SuperAdminPlanTemplate::ensureDefaultCatalog();
            $supportsCommercialPlanCatalog = $this->supportsCommercialPlanCatalog();
            $planTemplateRules = ['nullable', 'integer'];
            if ($supportsCommercialPlanCatalog) {
                $planTemplateRules[] = Rule::exists('superadmin_plan_templates', 'id')->where(
                    fn ($query) => $query->where('status', 'active')->whereIn('plan_key', SuperAdminPlanCatalog::keys())
                );
            }

            $data = $request->validate([
                'months' => ['nullable', 'integer', 'min:1', 'max:12'],
                'payment_method' => ['required', 'string', 'in:'.implode(',', SubscriptionService::PAYMENT_METHODS)],
                'plan_template_id' => $planTemplateRules,
                'promotion_template_id' => ['nullable', 'integer', Rule::exists('superadmin_promotion_templates', 'id')->where(fn ($query) => $query->where('status', 'active'))],
                'custom_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            ], [
                'custom_price.numeric' => 'El precio personalizado debe ser numérico.',
                'custom_price.min' => 'El precio personalizado no puede ser negativo.',
                'custom_price.max' => 'El precio personalizado supera el límite permitido.',
            ]);

            $gymModel = Gym::query()
                ->withoutDemoSessions()
                ->findOrFail($gym);
            $this->assertGymCanBeRenewedOrSuspended((int) $gymModel->id);

            $months = (int) ($data['months'] ?? 1);
            $months = max(1, $months);
            $pricingBillingCycles = $months;
            $paymentMethod = (string) $data['payment_method'];
            $planTemplateId = isset($data['plan_template_id']) ? (int) $data['plan_template_id'] : 0;
            $selectedPromotionTemplateId = isset($data['promotion_template_id']) ? (int) $data['promotion_template_id'] : 0;
            $promotionDurationUnit = null;
            $promotionDurationMonths = null;
            $promotionDurationDays = null;
            if ($planTemplateId > 0 && ! $supportsCommercialPlanCatalog) {
                return redirect()
                    ->route('superadmin.gyms.index')
                    ->withErrors(['subscription' => 'El catalogo de planes base aun no esta listo en la base de datos. Ejecuta las migraciones pendientes antes de cambiar el plan.']);
            }

            $customPrice = array_key_exists('custom_price', $data) && $data['custom_price'] !== null
                ? (float) $data['custom_price']
                : null;
            $selectedPlanName = '';
            $planTemplatePayload = null;
            $planTemplate = null;
            $currentSubscription = Subscription::query()
                ->where('gym_id', (int) $gymModel->id)
                ->first();

            if ($supportsCommercialPlanCatalog && $planTemplateId > 0) {
                $planTemplate = SuperAdminPlanTemplate::query()
                    ->where('status', 'active')
                    ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
                    ->findOrFail($planTemplateId);
            } elseif ($supportsCommercialPlanCatalog) {
                $currentPlanTemplateId = $currentSubscription?->plan_template_id !== null
                    ? (int) $currentSubscription->plan_template_id
                    : 0;
                if ($currentPlanTemplateId > 0) {
                    $planTemplate = SuperAdminPlanTemplate::query()
                        ->where('status', 'active')
                        ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
                        ->find($currentPlanTemplateId);
                }

                if (! $planTemplate) {
                    $currentPlanKey = strtolower(trim((string) ($currentSubscription?->plan_key ?? '')));
                    if ($currentPlanKey !== '') {
                        $planTemplate = $this->commercialPlanService
                            ->basePlans()
                            ->where('status', 'active')
                            ->first(function (SuperAdminPlanTemplate $template) use ($currentPlanKey): bool {
                                $templatePlanKey = strtolower(trim((string) ($template->plan_key ?? '')));
                                $templateFeaturePlanKey = strtolower(trim((string) $template->resolvedFeaturePlanKey()));

                                return $templatePlanKey === $currentPlanKey || $templateFeaturePlanKey === $currentPlanKey;
                            });
                    }
                }
            }

            $selectedPromotion = null;
            if ($selectedPromotionTemplateId > 0) {
                $selectedPromotion = SuperAdminPromotionTemplate::query()
                    ->where('status', 'active')
                    ->find($selectedPromotionTemplateId);

                if (! $selectedPromotion) {
                    return redirect()
                        ->route('superadmin.gyms.index')
                        ->withErrors(['subscription' => 'La promocion seleccionada ya no esta disponible.']);
                }

                $today = now()->toDateString();
                if ($selectedPromotion->starts_at && $selectedPromotion->starts_at->toDateString() > $today) {
                    return redirect()
                        ->route('superadmin.gyms.index')
                        ->withErrors(['subscription' => 'La promocion seleccionada aun no inicia.']);
                }

                if ($selectedPromotion->ends_at && $selectedPromotion->ends_at->toDateString() < $today) {
                    return redirect()
                        ->route('superadmin.gyms.index')
                        ->withErrors(['subscription' => 'La promocion seleccionada ya vencio.']);
                }

                $promotionCoverage = $this->resolvePromotionCoverage($selectedPromotion);
                if ($promotionCoverage['unit'] === 'days' && $promotionCoverage['days'] !== null) {
                    $months = 1;
                    $pricingBillingCycles = 1;
                    $promotionDurationUnit = 'days';
                    $promotionDurationDays = (int) $promotionCoverage['days'];
                    $promotionDurationMonths = null;
                } elseif ($promotionCoverage['months'] !== null) {
                    $months = (int) $promotionCoverage['months'];
                    $pricingBillingCycles = $months;
                    $promotionDurationUnit = 'months';
                    $promotionDurationMonths = $months;
                    $promotionDurationDays = null;
                }
            }

            if ($selectedPromotion && ! $planTemplate) {
                return redirect()
                    ->route('superadmin.gyms.index')
                    ->withErrors(['subscription' => 'Selecciona un plan base para aplicar esa promocion.']);
            }

            if ($planTemplate) {
                $selectedPlanName = (string) $planTemplate->name;
                $selectedFeaturePlanKey = $planTemplate->resolvedFeaturePlanKey();
                $pricingCustomPrice = $customPrice;
                if (
                    $pricingCustomPrice === null
                    && $selectedFeaturePlanKey === 'sucursales'
                    && $currentSubscription !== null
                ) {
                    $pricingCustomPrice = $currentSubscription->sucursales_base_price !== null
                        ? (float) $currentSubscription->sucursales_base_price
                        : (float) ($currentSubscription->price ?? 0);
                }
                $pricing = $selectedPromotion
                    ? $this->planPricingService->resolveSelectionWithPromotion(
                        planTemplate: $planTemplate,
                        promotionTemplate: $selectedPromotion,
                        billingCycles: $pricingBillingCycles,
                        customMonthlyPrice: $pricingCustomPrice
                    )
                    : $this->planPricingService->resolveSelection(
                        planTemplate: $planTemplate,
                        billingCycles: $pricingBillingCycles,
                        customMonthlyPrice: $pricingCustomPrice,
                        applyPromotions: true
                    );
                $appliedPromotionTemplateId = $pricing['promotion']?->id;
                $templateDurationUnit = (string) ($planTemplate->duration_unit ?? 'days');
                $templateDurationDays = (int) $planTemplate->duration_days;
                $templateDurationMonths = $planTemplate->duration_months !== null ? (int) $planTemplate->duration_months : null;
                $templateBillingCycles = $months;
                $chargeBillingCycles = $months;
                if ($promotionDurationUnit === 'days' && $promotionDurationDays !== null) {
                    $templateDurationUnit = 'days';
                    $templateDurationDays = max(1, $promotionDurationDays);
                    $templateDurationMonths = null;
                    $templateBillingCycles = 1;
                    $chargeBillingCycles = 1;
                }

                $planTemplatePayload = [
                    'template_id' => (int) $planTemplate->id,
                    'plan_key' => $selectedFeaturePlanKey,
                    'feature_version' => (string) config('plan_features.default_feature_version', 'v1'),
                    'name' => (string) $planTemplate->name,
                    'price' => (float) $pricing['effective_monthly_price'],
                    'duration_unit' => $templateDurationUnit,
                    'duration_days' => $templateDurationDays,
                    'duration_months' => $templateDurationMonths,
                    'billing_cycles' => $templateBillingCycles,
                    'bonus_days' => (int) ($pricing['bonus_days'] ?? 0),
                    'billing_event' => [
                        'plan_template_id' => (int) $planTemplate->id,
                        'promotion_template_id' => $appliedPromotionTemplateId !== null ? (int) $appliedPromotionTemplateId : null,
                        'plan_key' => $selectedFeaturePlanKey,
                        'plan_name' => (string) $planTemplate->name,
                        'event_type' => 'renewal',
                        'payment_method' => $paymentMethod,
                        'billing_cycles' => $chargeBillingCycles,
                        'base_monthly_price' => (float) ($pricing['base_monthly_price'] ?? 0),
                        'effective_monthly_price' => (float) ($pricing['effective_monthly_price'] ?? 0),
                        'base_total' => (float) ($pricing['base_total'] ?? 0),
                        'discount_amount' => (float) ($pricing['discount_amount'] ?? 0),
                        'final_total' => (float) ($pricing['final_total'] ?? 0),
                        'bonus_days' => (int) ($pricing['bonus_days'] ?? 0),
                        'charged_at' => now(),
                    ],
                ];
            }

            $this->subscriptionService->renew((int) $gymModel->id, $months, $paymentMethod, $planTemplatePayload);

            return redirect()
                ->route('superadmin.gyms.index')
                ->with('status', $selectedPlanName !== ''
                    ? 'Suscripción renovada con plan "'.$selectedPlanName.'" y pago por '.$paymentMethod.'.'
                    : 'Suscripción renovada por '.$months.' mes(es) con pago por '.$paymentMethod.'.');
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('superadmin.gyms.index')
                ->withErrors(['subscription' => $exception->getMessage()]);
        }
    }

    /**
     * Suspend subscription for a gym.
     */
    public function suspend(int $gym): RedirectResponse
    {
        try {
            $gymModel = Gym::query()
                ->withoutDemoSessions()
                ->findOrFail($gym);
            $this->assertGymCanBeRenewedOrSuspended((int) $gymModel->id);

            $this->subscriptionService->suspend((int) $gymModel->id);

            return redirect()
                ->route('superadmin.gyms.index')
                ->with('status', 'Suscripción suspendida.');
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->route('superadmin.gyms.index')
                ->withErrors(['subscription' => $exception->getMessage()]);
        }
    }

    private function assertGymCanBeRenewedOrSuspended(int $gymId): void
    {
        $isBranchManaged = (bool) Subscription::query()
            ->where('gym_id', $gymId)
            ->value('is_branch_managed');

        if (! $isBranchManaged) {
            return;
        }

        throw new InvalidArgumentException('Esta sucursal está gestionada por su sede principal y no se renueva ni suspende de forma directa.');
    }

    private function supportsCommercialPlanCatalog(): bool
    {
        return $this->commercialPlanService->supportsCommercialCatalog();
    }

    /**
     * @return array{unit:string,months:?int,days:?int}
     */
    private function resolvePromotionCoverage(SuperAdminPromotionTemplate $promotion): array
    {
        $supportsDurationUnit = $this->supportsPromotionDurationUnitColumns();
        $durationUnit = 'months';
        if ($supportsDurationUnit) {
            $candidateUnit = strtolower(trim((string) ($promotion->duration_unit ?? '')));
            if (in_array($candidateUnit, ['days', 'months'], true)) {
                $durationUnit = $candidateUnit;
            } elseif ($promotion->duration_days !== null && (int) $promotion->duration_days > 0) {
                $durationUnit = 'days';
            }
        }

        if ($durationUnit === 'days') {
            $durationDays = $promotion->duration_days !== null ? (int) $promotion->duration_days : null;
            if ($durationDays !== null && $durationDays > 0) {
                return [
                    'unit' => 'days',
                    'months' => null,
                    'days' => $durationDays,
                ];
            }
        }

        $durationMonths = $promotion->duration_months !== null ? (int) $promotion->duration_months : null;
        $durationMonths = $durationMonths !== null && $durationMonths > 0 ? $durationMonths : null;

        return [
            'unit' => 'months',
            'months' => $durationMonths,
            'days' => null,
        ];
    }

    private function supportsPromotionDurationUnitColumns(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasTable('superadmin_promotion_templates')
            && \Illuminate\Support\Facades\Schema::hasColumns('superadmin_promotion_templates', ['duration_unit', 'duration_days']);
    }
}


