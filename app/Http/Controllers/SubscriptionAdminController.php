<?php

namespace App\Http\Controllers;

use App\Models\Gym;
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
        ]);

        $months = (int) ($data['months'] ?? 1);
        $paymentMethod = (string) $data['payment_method'];

        $gymModel = Gym::query()->findOrFail($gym);
        $this->subscriptionService->renew((int) $gymModel->id, $months, $paymentMethod);

        return redirect()
            ->route('superadmin.gyms.index')
            ->with('status', 'Suscripcion renovada por '.$months.' mes(es) con pago por '.$paymentMethod.'.');
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
