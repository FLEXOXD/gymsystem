<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientCardController;
use App\Http\Controllers\ClientCredentialController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ContactSuggestionController;
use App\Http\Controllers\LegalAcceptanceController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\GymPanelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReceptionCheckInController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdminBranchController;
use App\Http\Controllers\SuperAdminInboxController;
use App\Http\Controllers\SuperAdminSiteContentController;
use App\Http\Controllers\SuperAdminNotificationsController;
use App\Http\Controllers\SuperAdminPlanTemplateController;
use App\Http\Controllers\SubscriptionAdminController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'index'])->name('landing');
Route::get('/nosotros', [MarketingController::class, 'about'])->name('landing.about');
Route::get('/contactanos', [MarketingController::class, 'contact'])->name('landing.contact');
Route::post('/contactanos/mensaje', [MarketingController::class, 'storeContactMessage'])
    ->middleware(['throttle:20,1'])
    ->name('landing.contact.store');
Route::get('/politica-de-privacidad', [MarketingController::class, 'privacy'])->name('landing.legal.privacy');
Route::get('/condiciones-de-servicio', [MarketingController::class, 'serviceTerms'])->name('landing.legal.service');
Route::get('/terminos-comerciales', [MarketingController::class, 'commercialTerms'])->name('landing.legal.commercial');
Route::post('/legal/aceptar', [MarketingController::class, 'acceptLegal'])
    ->middleware(['guest', 'throttle:20,1'])
    ->name('landing.legal.accept');

Route::get('/app', function (Request $request) {
    $user = $request->user();
    if (!$user) {
        return redirect()->route('login');
    }

    $isStandalonePwa = strtolower(trim((string) $request->query('pwa_mode', (string) $request->cookie('gym_pwa_mode', '')))) === 'standalone';

    $gymSlug = trim((string) ($user->gym?->slug ?? ''));
    if ($gymSlug !== '') {
        return redirect()->route('panel.index', [
            'contextGym' => $gymSlug,
        ] + ($isStandalonePwa ? ['pwa_mode' => 'standalone'] : []));
    }

    return redirect()->route('superadmin.dashboard');
})->name('app.entry');

Route::get('/public', fn () => redirect('/'))->name('public.root.redirect');
Route::get('/demo', [MarketingController::class, 'demo'])->name('demo');
Route::get('/demo/guia', [MarketingController::class, 'demoGuide'])->name('demo.guide');
Route::post('/demo/solicitar', [MarketingController::class, 'requestDemo'])
    ->middleware(['guest', 'throttle:demo-request'])
    ->name('demo.request');

Route::middleware('guest')->group(function (): void {
    // Legacy login endpoint used by some deployments that submit to "/".
    Route::post('/', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.root');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');

    // Compatibility routes when app is exposed with "/public" in the URL.
    Route::post('/public', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.public.root');
    Route::get('/public/login', [AuthenticatedSessionController::class, 'create'])->name('login.public');
    Route::post('/public/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.public.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'demo.session', 'gym.timezone'])->group(function (): void {
    Route::get('/subscription/expired', [ThemeController::class, 'subscriptionExpired'])->name('subscription.expired');
    Route::post('/legal/modal-acceptance', [LegalAcceptanceController::class, 'storeModal'])
        ->name('legal.modal-acceptance.store');

    Route::middleware('check.subscription')->group(function (): void {
        Route::get('/panel', function (Request $request) {
            $gymSlug = trim((string) ($request->user()?->gym?->slug ?? ''));
            if ($gymSlug === '') {
                return redirect()->route('superadmin.dashboard');
            }

            $isStandalonePwa = strtolower(trim((string) $request->query('pwa_mode', (string) $request->cookie('gym_pwa_mode', '')))) === 'standalone';

            return redirect()->route('panel.index', [
                'contextGym' => $gymSlug,
            ] + ($isStandalonePwa ? ['pwa_mode' => 'standalone'] : []));
        })->name('panel.legacy');

        Route::middleware('superadmin')->prefix('superadmin')->name('superadmin.')->group(function (): void {
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])
                ->name('dashboard');
            Route::get('/gyms', [SuperAdminDashboardController::class, 'gyms'])
                ->name('gyms.index');
            Route::get('/sucursales', [SuperAdminBranchController::class, 'index'])
                ->name('branches.index');
            Route::post('/sucursales', [SuperAdminBranchController::class, 'store'])
                ->name('branches.store');
            Route::delete('/sucursales/{link}', [SuperAdminBranchController::class, 'destroy'])
                ->name('branches.destroy');
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
            Route::get('/inbox', [SuperAdminInboxController::class, 'index'])
                ->name('inbox.index');
            Route::get('/inbox/{message}', [SuperAdminInboxController::class, 'show'])
                ->name('inbox.show');
            Route::post('/inbox/{message}/read', [SuperAdminInboxController::class, 'markRead'])
                ->name('inbox.read');
            Route::get('/plans', [SuperAdminPlanTemplateController::class, 'index'])
                ->name('plan-templates.index');
            Route::post('/plans', [SuperAdminPlanTemplateController::class, 'storePlan'])
                ->name('plan-templates.store');
            Route::patch('/plans/{template}/pricing', [SuperAdminPlanTemplateController::class, 'updatePlanPricing'])
                ->name('plan-templates.pricing.update');
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
            Route::get('/web-page', [SuperAdminSiteContentController::class, 'edit'])
                ->name('web-page.edit');
            Route::post('/web-page', [SuperAdminSiteContentController::class, 'update'])
                ->name('web-page.update');
            Route::get('/legal-acceptances', [LegalAcceptanceController::class, 'index'])
                ->name('legal-acceptances.index');
            Route::get('/legal-acceptances/{acceptance}/contract.pdf', [LegalAcceptanceController::class, 'contractPdf'])
                ->name('legal-acceptances.contract.pdf');
        });

        Route::prefix('{contextGym}')
            ->where(['contextGym' => '(?!superadmin$)[A-Za-z0-9\-]+'])
            ->middleware(['gym.route', 'pwa.standalone.access'])
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
                Route::post('/plans', [PlanController::class, 'store'])
                    ->middleware('not.branch:manage_plans')
                    ->name('plans.store');
                Route::put('/plans/{plan}', [PlanController::class, 'update'])
                    ->middleware('not.branch:manage_plans')
                    ->name('plans.update');
                Route::patch('/plans/{plan}/toggle', [PlanController::class, 'toggle'])
                    ->middleware('not.branch:manage_plans')
                    ->name('plans.toggle');
                Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])
                    ->middleware('not.branch:manage_plans')
                    ->name('plans.destroy');
                Route::post('/plans/promotions', [PlanController::class, 'storePromotion'])
                    ->middleware(['plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.store');
                Route::put('/plans/promotions/{promotion}', [PlanController::class, 'updatePromotion'])
                    ->middleware(['plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.update');
                Route::patch('/plans/promotions/{promotion}/toggle', [PlanController::class, 'togglePromotion'])
                    ->middleware(['plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.toggle');
                Route::delete('/plans/promotions/{promotion}', [PlanController::class, 'destroyPromotion'])
                    ->middleware(['plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.destroy');

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
                Route::post('/cash/open', [CashController::class, 'open'])
                    ->middleware('not.branch:manage_cash')
                    ->name('cash.open');
                Route::post('/cash/movements', [CashController::class, 'addMovement'])
                    ->middleware('not.branch:manage_cash')
                    ->name('cash.movements.store');
                Route::post('/cash/close', [CashController::class, 'close'])
                    ->middleware('not.branch:manage_cash')
                    ->name('cash.close');
                Route::get('/cash/sessions', [CashController::class, 'sessions'])->name('cash.sessions.index');
                Route::get('/cash/sessions/{session}', [CashController::class, 'show'])->name('cash.sessions.show');

                Route::get('/sucursales', [BranchController::class, 'index'])
                    ->middleware(['not.branch:manage_branches', 'plan.feature:multi_branch'])
                    ->name('branches.index');

                Route::get('/reports', [ReportController::class, 'index'])
                    ->middleware('plan.feature:reports_base')
                    ->name('reports.index');
                Route::get('/reports/income', [ReportController::class, 'income'])
                    ->middleware('plan.feature:reports_base')
                    ->name('reports.income');
                Route::get('/reports/attendance', [ReportController::class, 'attendance'])
                    ->middleware('plan.feature:reports_base')
                    ->name('reports.attendance');
                Route::get('/reports/memberships', [ReportController::class, 'memberships'])
                    ->middleware('plan.feature:reports_base')
                    ->name('reports.memberships');
                Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])
                    ->middleware(['plan.feature:reports_export', 'not.branch:export_reports'])
                    ->name('reports.export.csv');
                Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
                    ->middleware(['plan.feature:reports_export', 'not.branch:export_reports'])
                    ->name('reports.export.pdf');

                // Context-aware profile/settings/contact routes for gym users.
                Route::get('/profile', [ThemeController::class, 'profile'])->name('gym.profile.index');
                Route::get('/contact', [ThemeController::class, 'contact'])->name('gym.contact.index');
                Route::post('/contact/suggestions', [ContactSuggestionController::class, 'store'])->name('gym.contact.suggestions.store');
                Route::get('/profile/membership-invoices/{subscription}/pdf', [ThemeController::class, 'membershipInvoicePdf'])
                    ->name('gym.profile.membership-invoice.pdf');
                Route::get('/config', [ThemeController::class, 'index'])->name('gym.settings.index');
                Route::post('/config/theme', [ThemeController::class, 'update'])->name('gym.settings.theme.update');
                Route::post('/config/profile', [ThemeController::class, 'updateProfile'])->name('gym.settings.profile.update');
                Route::post('/config/gym-profile', [ThemeController::class, 'updateGymProfile'])->name('gym.settings.gym-profile.update');
                Route::post('/config/gym-logo', [ThemeController::class, 'updateGymLogo'])->name('gym.settings.gym-logo.update');
                Route::post('/config/gym-avatars', [ThemeController::class, 'updateGymAvatars'])->name('gym.settings.gym-avatars.update');
            });

        $redirectGymUserToContextRoute = function (Request $request, string $routeName, array $params = []) {
            $user = $request->user();
            if (! $user || ! $user->gym_id) {
                return null;
            }

            $gymSlug = trim((string) ($user->gym?->slug ?? ''));
            if ($gymSlug === '') {
                return redirect()->route('panel.legacy');
            }

            return redirect()->route($routeName, array_merge(['contextGym' => $gymSlug], $params));
        };

        Route::get('/profile', function (Request $request) use ($redirectGymUserToContextRoute) {
            $redirect = $redirectGymUserToContextRoute($request, 'gym.profile.index');
            if ($redirect) {
                return $redirect;
            }

            return app(ThemeController::class)->profile($request);
        })->name('profile.index');
        Route::get('/contact', function (Request $request) use ($redirectGymUserToContextRoute) {
            $redirect = $redirectGymUserToContextRoute($request, 'gym.contact.index');
            if ($redirect) {
                return $redirect;
            }

            return app(ThemeController::class)->contact($request);
        })->name('contact.index');
        Route::post('/contact/suggestions', [ContactSuggestionController::class, 'store'])->name('contact.suggestions.store');
        Route::get('/profile/membership-invoices/{subscription}/pdf', function (Request $request, int $subscription) use ($redirectGymUserToContextRoute) {
            $redirect = $redirectGymUserToContextRoute($request, 'gym.profile.membership-invoice.pdf', [
                'subscription' => $subscription,
            ]);
            if ($redirect) {
                return $redirect;
            }

            return app(ThemeController::class)->membershipInvoicePdf($request, $subscription);
        })->name('profile.membership-invoice.pdf');
        Route::get('/config', function (Request $request) use ($redirectGymUserToContextRoute) {
            $redirect = $redirectGymUserToContextRoute($request, 'gym.settings.index');
            if ($redirect) {
                return $redirect;
            }

            return app(ThemeController::class)->index($request);
        })->name('settings.index');
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
