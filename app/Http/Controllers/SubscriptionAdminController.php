<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\SuperAdminPlanTemplate;
use App\Services\SuperAdminPlanPricingService;
use App\Services\SubscriptionService;
use App\Support\SuperAdminPlanCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class SubscriptionAdminController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly SuperAdminPlanPricingService $planPricingService
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
            $paymentMethod = (string) $data['payment_method'];
            $planTemplateId = isset($data['plan_template_id']) ? (int) $data['plan_template_id'] : 0;
            if ($planTemplateId > 0 && ! $supportsCommercialPlanCatalog) {
                return redirect()
                    ->route('superadmin.gyms.index')
                    ->withErrors(['subscription' => 'El catalogo comercial aun no esta listo en la base de datos. Ejecuta las migraciones pendientes antes de cambiar el plan comercial.']);
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
                $currentPlanKey = strtolower(trim((string) ($currentSubscription?->plan_key ?? '')));
                if (in_array($currentPlanKey, SuperAdminPlanCatalog::keys(), true)) {
                    $planTemplate = SuperAdminPlanTemplate::query()
                        ->where('status', 'active')
                        ->where('plan_key', $currentPlanKey)
                        ->first();
                }
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
                $pricing = $this->planPricingService->resolveSelection(
                    planTemplate: $planTemplate,
                    billingCycles: $months,
                    customMonthlyPrice: $pricingCustomPrice
                );
                $promotionTemplateId = $pricing['promotion']?->id;

                $planTemplatePayload = [
                    'template_id' => (int) $planTemplate->id,
                    'plan_key' => $selectedFeaturePlanKey,
                    'feature_version' => (string) config('plan_features.default_feature_version', 'v1'),
                    'name' => (string) $planTemplate->name,
                    'price' => (float) $pricing['effective_monthly_price'],
                    'duration_unit' => (string) ($planTemplate->duration_unit ?? 'days'),
                    'duration_days' => (int) $planTemplate->duration_days,
                    'duration_months' => $planTemplate->duration_months !== null ? (int) $planTemplate->duration_months : null,
                    'billing_cycles' => $months,
                    'bonus_days' => (int) ($pricing['bonus_days'] ?? 0),
                    'billing_event' => [
                        'plan_template_id' => (int) $planTemplate->id,
                        'promotion_template_id' => $promotionTemplateId !== null ? (int) $promotionTemplateId : null,
                        'plan_key' => $selectedFeaturePlanKey,
                        'plan_name' => (string) $planTemplate->name,
                        'event_type' => 'renewal',
                        'payment_method' => $paymentMethod,
                        'billing_cycles' => $months,
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
        return Schema::hasTable('superadmin_plan_templates')
            && Schema::hasColumns('superadmin_plan_templates', ['id', 'plan_key', 'status', 'price']);
    }
}


