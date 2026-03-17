<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\Subscription;
use App\Models\SuperAdminPlanTemplate;
use App\Services\SubscriptionService;
use App\Support\SuperAdminPlanCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SubscriptionAdminController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService
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

            $data = $request->validate([
                'months' => ['nullable', 'integer', 'min:1', 'max:24'],
                'payment_method' => ['required', 'string', 'in:'.implode(',', SubscriptionService::PAYMENT_METHODS)],
                'plan_template_id' => ['nullable', 'integer', 'exists:superadmin_plan_templates,id'],
                'custom_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
                'intro_discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            ], [
                'custom_price.numeric' => 'El precio personalizado debe ser numérico.',
                'custom_price.min' => 'El precio personalizado no puede ser negativo.',
                'custom_price.max' => 'El precio personalizado supera el límite permitido.',
                'intro_discount_percent.integer' => 'El descuento del primer mes debe ser un número entero.',
                'intro_discount_percent.min' => 'El descuento del primer mes no puede ser menor a 0%.',
                'intro_discount_percent.max' => 'El descuento del primer mes no puede superar 100%.',
            ]);

            $gymModel = Gym::query()
                ->withoutDemoSessions()
                ->findOrFail($gym);
            $this->assertGymCanBeRenewedOrSuspended((int) $gymModel->id);

            $months = (int) ($data['months'] ?? 1);
            $paymentMethod = (string) $data['payment_method'];
            $planTemplateId = isset($data['plan_template_id']) ? (int) $data['plan_template_id'] : 0;
            $customPrice = array_key_exists('custom_price', $data) && $data['custom_price'] !== null
                ? (float) $data['custom_price']
                : null;
            $introDiscountPercent = max(0, min(100, (int) ($data['intro_discount_percent'] ?? 0)));
            $selectedPlanName = '';
            $planTemplatePayload = null;

            if ($planTemplateId > 0) {
                $planTemplate = SuperAdminPlanTemplate::query()
                    ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
                    ->findOrFail($planTemplateId);
                $selectedPlanName = (string) $planTemplate->name;
                $resolvedPrice = (float) $planTemplate->price;
                if ((string) ($planTemplate->plan_key ?? '') === 'sucursales' && $customPrice !== null) {
                    $resolvedPrice = $customPrice;
                }
                $planTemplatePayload = [
                    'template_id' => (int) $planTemplate->id,
                    'plan_key' => (string) ($planTemplate->plan_key ?? ''),
                    'feature_version' => (string) config('plan_features.default_feature_version', 'v1'),
                    'name' => (string) $planTemplate->name,
                    'price' => $resolvedPrice,
                    'duration_unit' => (string) ($planTemplate->duration_unit ?? 'days'),
                    'duration_days' => (int) $planTemplate->duration_days,
                    'duration_months' => $planTemplate->duration_months !== null ? (int) $planTemplate->duration_months : null,
                    'intro_discount_first_cycle' => $introDiscountPercent > 0,
                    'intro_discount_percent' => $introDiscountPercent,
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
}


