<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\SuperAdminPlanTemplate;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $data = $request->validate([
            'months' => ['nullable', 'integer', 'min:1', 'max:24'],
            'payment_method' => ['required', 'string', 'in:'.implode(',', SubscriptionService::PAYMENT_METHODS)],
            'plan_template_id' => ['nullable', 'integer', 'exists:superadmin_plan_templates,id'],
        ]);

        $months = (int) ($data['months'] ?? 1);
        $paymentMethod = (string) $data['payment_method'];
        $planTemplateId = isset($data['plan_template_id']) ? (int) $data['plan_template_id'] : 0;
        $selectedPlanName = '';
        $planTemplatePayload = null;

        if ($planTemplateId > 0) {
            $planTemplate = SuperAdminPlanTemplate::query()->findOrFail($planTemplateId);
            $selectedPlanName = (string) $planTemplate->name;
            $planTemplatePayload = [
                'name' => (string) $planTemplate->name,
                'price' => (float) $planTemplate->price,
                'duration_unit' => (string) ($planTemplate->duration_unit ?? 'days'),
                'duration_days' => (int) $planTemplate->duration_days,
                'duration_months' => $planTemplate->duration_months !== null ? (int) $planTemplate->duration_months : null,
            ];
        }

        $gymModel = Gym::query()->findOrFail($gym);
        $this->subscriptionService->renew((int) $gymModel->id, $months, $paymentMethod, $planTemplatePayload);

        return redirect()
            ->route('superadmin.gyms.index')
            ->with('status', $selectedPlanName !== ''
                ? 'Suscripcion renovada con plan "'.$selectedPlanName.'" y pago por '.$paymentMethod.'.'
                : 'Suscripcion renovada por '.$months.' mes(es) con pago por '.$paymentMethod.'.');
    }

    /**
     * Suspend subscription for a gym.
     */
    public function suspend(int $gym): RedirectResponse
    {
        $gymModel = Gym::query()->findOrFail($gym);

        $this->subscriptionService->suspend((int) $gymModel->id);

        return redirect()
            ->route('superadmin.gyms.index')
            ->with('status', 'Suscripcion suspendida.');
    }
}

