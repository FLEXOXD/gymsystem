<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use App\Services\SuperAdminDashboardService;
use Illuminate\Contracts\View\View;

class SuperAdminDashboardController extends Controller
{
    public function __construct(
        private readonly SuperAdminDashboardService $dashboardService
    ) {
    }

    /**
     * Global dashboard for SuperAdmin.
     */
    public function dashboard(): View
    {
        return view('superadmin.dashboard', [
            'kpis' => $this->dashboardService->getKpis(),
        ]);
    }

    /**
     * Global gym list with subscription state.
     */
    public function gyms(): View
    {
        return view('superadmin.gyms', [
            'gyms' => $this->dashboardService->getGymsTable(),
            'paymentMethods' => SubscriptionService::PAYMENT_METHODS,
        ]);
    }
}
