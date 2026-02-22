<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientCardController;
use App\Http\Controllers\ClientCredentialController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ContactSuggestionController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\GymPanelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReceptionCheckInController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdminNotificationsController;
use App\Http\Controllers\SuperAdminPlanTemplateController;
use App\Http\Controllers\SubscriptionAdminController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();
    if (!$user) {
        return redirect()->route('login');
    }

    $gymSlug = trim((string) ($user->gym?->slug ?? ''));
    if ($gymSlug !== '') {
        return redirect()->route('panel.index', ['contextGym' => $gymSlug]);
    }

    return redirect()->route('superadmin.dashboard');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'gym.timezone'])->group(function (): void {
    Route::get('/subscription/expired', [ThemeController::class, 'subscriptionExpired'])->name('subscription.expired');

    Route::middleware('check.subscription')->group(function (): void {
        Route::get('/panel', function (Request $request) {
            $gymSlug = trim((string) ($request->user()?->gym?->slug ?? ''));
            if ($gymSlug === '') {
                return redirect()->route('superadmin.dashboard');
            }

            return redirect()->route('panel.index', ['contextGym' => $gymSlug]);
        })->name('panel.legacy');

        Route::middleware('superadmin')->prefix('superadmin')->name('superadmin.')->group(function (): void {
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])
                ->name('dashboard');
            Route::get('/gyms', [SuperAdminDashboardController::class, 'gyms'])
                ->name('gyms.index');
            Route::get('/gym', [SuperAdminDashboardController::class, 'gym'])
                ->name('gym.index');
            Route::post('/gyms', [SuperAdminDashboardController::class, 'storeGym'])
                ->name('gyms.store');
            Route::patch('/gyms/{gym}/admin-user', [SuperAdminDashboardController::class, 'updateGymAdminUser'])
                ->name('gyms.admin-user.update');
            Route::delete('/gyms/{gym}', [SuperAdminDashboardController::class, 'destroyGym'])
                ->name('gyms.destroy');
            Route::get('/notifications', [SuperAdminNotificationsController::class, 'index'])
                ->name('notifications.index');
            Route::get('/notifications/history', [SuperAdminNotificationsController::class, 'history'])
                ->name('notifications.history');
            Route::get('/plans', [SuperAdminPlanTemplateController::class, 'index'])
                ->name('plan-templates.index');
            Route::post('/plans', [SuperAdminPlanTemplateController::class, 'storePlan'])
                ->name('plan-templates.store');
            Route::patch('/plans/{template}/toggle', [SuperAdminPlanTemplateController::class, 'togglePlan'])
                ->name('plan-templates.toggle');
            Route::delete('/plans/{template}', [SuperAdminPlanTemplateController::class, 'destroyPlan'])
                ->name('plan-templates.destroy');
            Route::post('/plans/promotions', [SuperAdminPlanTemplateController::class, 'storePromotion'])
                ->name('plan-templates.promotions.store');
            Route::patch('/plans/promotions/{promotion}/toggle', [SuperAdminPlanTemplateController::class, 'togglePromotion'])
                ->name('plan-templates.promotions.toggle');
            Route::delete('/plans/promotions/{promotion}', [SuperAdminPlanTemplateController::class, 'destroyPromotion'])
                ->name('plan-templates.promotions.destroy');
            Route::get('/suggestions', [ContactSuggestionController::class, 'index'])
                ->name('suggestions.index');
            Route::post('/suggestions/{suggestion}/reviewed', [ContactSuggestionController::class, 'markReviewed'])
                ->name('suggestions.reviewed');
            Route::post('/notifications/{notification}/sent', [SuperAdminNotificationsController::class, 'markSent'])
                ->name('notifications.sent');
            Route::post('/notifications/{notification}/skipped', [SuperAdminNotificationsController::class, 'markSkipped'])
                ->name('notifications.skipped');
            Route::get('/subscriptions', [SubscriptionAdminController::class, 'index'])
                ->name('subscriptions.index');
            Route::post('/subscriptions/{gym}/renew', [SubscriptionAdminController::class, 'renew'])
                ->name('subscriptions.renew');
            Route::post('/subscriptions/{gym}/suspend', [SubscriptionAdminController::class, 'suspend'])
                ->name('subscriptions.suspend');
        });

        Route::prefix('{contextGym}')
            ->where(['contextGym' => '(?!superadmin$)[A-Za-z0-9\-]+'])
            ->middleware('gym.route')
            ->group(function (): void {
                Route::get('/panel', [GymPanelController::class, 'index'])->name('panel.index');

                Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
                Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
                Route::get('/clients/check-document', [ClientController::class, 'checkDocument'])->name('clients.check-document');
                Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
                Route::patch('/clients/{client}/photo', [ClientController::class, 'updatePhoto'])->name('clients.photo.update');
                Route::get('/clients/{client}/card', [ClientCardController::class, 'show'])->name('clients.card');
                Route::get('/clients/{client}/card.pdf', [ClientCardController::class, 'pdf'])->name('clients.card.pdf');

                Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
                Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
                Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
                Route::patch('/plans/{plan}/toggle', [PlanController::class, 'toggle'])->name('plans.toggle');
                Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
                Route::post('/plans/promotions', [PlanController::class, 'storePromotion'])->name('plans.promotions.store');
                Route::put('/plans/promotions/{promotion}', [PlanController::class, 'updatePromotion'])->name('plans.promotions.update');
                Route::patch('/plans/promotions/{promotion}/toggle', [PlanController::class, 'togglePromotion'])->name('plans.promotions.toggle');
                Route::delete('/plans/promotions/{promotion}', [PlanController::class, 'destroyPromotion'])->name('plans.promotions.destroy');

                Route::post('/memberships', [MembershipController::class, 'store'])->name('memberships.store');
                Route::post('/clients/{client}/credentials/rfid', [ClientCredentialController::class, 'storeRfid'])
                    ->name('client-credentials.store-rfid');
                Route::post('/clients/{client}/credentials/qr', [ClientCredentialController::class, 'generateQr'])
                    ->name('client-credentials.generate-qr');
                Route::patch('/client-credentials/{credential}/deactivate', [ClientCredentialController::class, 'deactivate'])
                    ->name('client-credentials.deactivate');

                Route::get('/reception', [ReceptionCheckInController::class, 'index'])->name('reception.index');
                Route::get('/reception/display', [ReceptionCheckInController::class, 'display'])->name('reception.display');
                Route::get('/reception/sync/latest', [ReceptionCheckInController::class, 'syncLatest'])->name('reception.sync.latest');
                Route::post('/reception/check-in', [ReceptionCheckInController::class, 'store'])
                    ->middleware('throttle:checkin')
                    ->name('reception.check-in');

                Route::get('/cash', [CashController::class, 'index'])->name('cash.index');
                Route::post('/cash/open', [CashController::class, 'open'])->name('cash.open');
                Route::post('/cash/movements', [CashController::class, 'addMovement'])->name('cash.movements.store');
                Route::post('/cash/close', [CashController::class, 'close'])->name('cash.close');
                Route::get('/cash/sessions', [CashController::class, 'sessions'])->name('cash.sessions.index');
                Route::get('/cash/sessions/{session}', [CashController::class, 'show'])->name('cash.sessions.show');

                Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
                Route::get('/reports/income', [ReportController::class, 'income'])->name('reports.income');
                Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
                Route::get('/reports/memberships', [ReportController::class, 'memberships'])->name('reports.memberships');
                Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
                Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
            });

        Route::get('/profile', [ThemeController::class, 'profile'])->name('profile.index');
        Route::get('/contact', [ThemeController::class, 'contact'])->name('contact.index');
        Route::post('/contact/suggestions', [ContactSuggestionController::class, 'store'])->name('contact.suggestions.store');
        Route::get('/profile/membership-invoices/{subscription}/pdf', [ThemeController::class, 'membershipInvoicePdf'])
            ->name('profile.membership-invoice.pdf');
        Route::get('/config', [ThemeController::class, 'index'])->name('settings.index');
        Route::post('/config/theme', [ThemeController::class, 'update'])->name('settings.theme.update');
        Route::post('/config/profile', [ThemeController::class, 'updateProfile'])->name('settings.profile.update');
        Route::post('/config/superadmin-contact', [ThemeController::class, 'updateSuperAdminContact'])->name('settings.superadmin-contact.update');
        Route::post('/config/profile/password', [ThemeController::class, 'updateProfilePassword'])->name('settings.profile.password.update');
        Route::post('/config/profile/logout-others', [ThemeController::class, 'logoutOtherDevices'])->name('settings.profile.logout-others');
        Route::post('/config/gym-profile', [ThemeController::class, 'updateGymProfile'])->name('settings.gym-profile.update');
        Route::post('/config/gym-logo', [ThemeController::class, 'updateGymLogo'])->name('settings.gym-logo.update');
        Route::post('/config/gym-avatars', [ThemeController::class, 'updateGymAvatars'])->name('settings.gym-avatars.update');

    });
});
