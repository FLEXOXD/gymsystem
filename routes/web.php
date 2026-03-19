<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientCardController;
use App\Http\Controllers\ClientCredentialController;
use App\Http\Controllers\ClientMobileController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ContactSuggestionController;
use App\Http\Controllers\LegalAcceptanceController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\GymPanelController;
use App\Http\Controllers\GymStaffController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReceptionCheckInController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdminBranchController;
use App\Http\Controllers\SuperAdminInboxController;
use App\Http\Controllers\SuperAdminSiteContentController;
use App\Http\Controllers\SuperAdminNotificationsController;
use App\Http\Controllers\SuperAdminPlanTemplateController;
use App\Http\Controllers\SuperAdminQuotationController;
use App\Http\Controllers\SuperAdminSupportChatController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\SubscriptionAdminController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\PwaEventController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RemoteScanController;
use App\Http\Controllers\SalesInventoryController;
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
Route::post('/cotizacion/solicitar', [MarketingController::class, 'storeQuoteRequest'])
    ->middleware(['throttle:20,1'])
    ->name('landing.quote.store');
Route::prefix('support-chat')->name('support-chat.landing.')->group(function (): void {
    Route::get('/state', [SupportChatController::class, 'landingState'])
        ->middleware(['throttle:120,1'])
        ->name('state');
    Route::post('/restart', [SupportChatController::class, 'landingRestart'])
        ->middleware(['throttle:40,1'])
        ->name('restart');
    Route::post('/quick-reply', [SupportChatController::class, 'landingQuickReply'])
        ->middleware(['throttle:60,1'])
        ->name('quick-reply');
    Route::post('/message', [SupportChatController::class, 'landingSendMessage'])
        ->middleware(['throttle:60,1'])
        ->name('message');
});
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
Route::get('/client-qr/{client}', [ClientCardController::class, 'publicCard'])
    ->middleware('signed')
    ->name('clients.card.public');
Route::get('/client-qr/{client}/image', [ClientCardController::class, 'publicQrImage'])
    ->middleware('signed')
    ->name('clients.card.public-qr-image');
Route::get('/client-qr/{client}/download', [ClientCardController::class, 'publicQrDownload'])
    ->middleware('signed')
    ->name('clients.card.public-download');
Route::get('/scan/{contextGym}/{channel}', [RemoteScanController::class, 'mobile'])
    ->middleware(['gym.timezone', 'signed:relative', 'throttle:240,1'])
    ->name('remote-scanner.mobile');
Route::post('/scan/{contextGym}/{channel}/capture', [RemoteScanController::class, 'capture'])
    ->middleware(['gym.timezone', 'signed:relative', 'throttle:240,1'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('remote-scanner.capture');
Route::get('/demo', [MarketingController::class, 'demo'])->name('demo');
Route::get('/demo/guia', [MarketingController::class, 'demoGuide'])->name('demo.guide');
Route::post('/demo/solicitar', [MarketingController::class, 'requestDemo'])
    ->middleware(['guest', 'throttle:demo-request'])
    ->name('demo.request');

Route::prefix('cliente/{gymSlug}')
    ->where(['gymSlug' => '[A-Za-z0-9\-]+'])
    ->middleware('gym.timezone')
    ->name('client-mobile.')
    ->group(function (): void {
        Route::get('/manifest.webmanifest', [ClientMobileController::class, 'manifest'])
            ->name('manifest');
        Route::get('/login', [ClientMobileController::class, 'login'])->name('login');
        Route::post('/login', [ClientMobileController::class, 'authenticate'])
            ->middleware('throttle:60,1')
            ->name('authenticate');

        Route::middleware('client.mobile.session')->group(function (): void {
            Route::get('/app', [ClientMobileController::class, 'app'])->name('app');
            Route::get('/progress', [ClientMobileController::class, 'progress'])
                ->middleware('throttle:120,1')
                ->name('progress');
            Route::post('/profile', [ClientMobileController::class, 'updateProfile'])
                ->middleware('throttle:20,1')
                ->name('profile.update');
            Route::post('/fitness-profile', [ClientMobileController::class, 'saveFitnessProfile'])
                ->middleware('throttle:20,1')
                ->name('fitness-profile.save');
            Route::post('/weekly-goal', [ClientMobileController::class, 'updateWeeklyGoal'])
                ->middleware('throttle:20,1')
                ->name('weekly-goal.update');
            Route::get('/push/status', [ClientMobileController::class, 'pushStatus'])
                ->middleware('throttle:120,1')
                ->name('push.status');
            Route::post('/push/subscribe', [ClientMobileController::class, 'pushSubscribe'])
                ->middleware('throttle:30,1')
                ->name('push.subscribe');
            Route::post('/push/unsubscribe', [ClientMobileController::class, 'pushUnsubscribe'])
                ->middleware('throttle:30,1')
                ->name('push.unsubscribe');
            Route::post('/check-in', [ClientMobileController::class, 'checkIn'])
                ->middleware('throttle:120,1')
                ->name('check-in');
            Route::post('/training/start', [ClientMobileController::class, 'startTraining'])
                ->middleware('throttle:60,1')
                ->name('training.start');
            Route::post('/training/finish', [ClientMobileController::class, 'finishTraining'])
                ->middleware('throttle:60,1')
                ->name('training.finish');
            Route::match(['GET', 'POST'], '/logout', [ClientMobileController::class, 'logout'])->name('logout');
        });
    });

Route::middleware(['guest', 'no.history'])->group(function (): void {
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
    ->middleware(['auth', 'no.history'])
    ->name('logout');

Route::get('/logout', [AuthenticatedSessionController::class, 'redirectAfterLogout'])
    ->middleware(['guest', 'no.history']);

Route::middleware(['auth', 'demo.session', 'gym.timezone', 'no.history'])->group(function (): void {
    Route::get('/subscription/expired', [ThemeController::class, 'subscriptionExpired'])->name('subscription.expired');
    Route::post('/subscription/reactivation-request', [ThemeController::class, 'requestSubscriptionReactivation'])
        ->middleware('throttle:10,1')
        ->name('subscription.reactivation.request');
    Route::post('/demo/finalizar', [AuthenticatedSessionController::class, 'endDemo'])->name('demo.end');
    Route::post('/legal/modal-acceptance', [LegalAcceptanceController::class, 'storeModal'])
        ->name('legal.modal-acceptance.store');
    Route::post('/pwa/events', [PwaEventController::class, 'store'])
        ->middleware('throttle:120,1')
        ->name('pwa.events.store');

    Route::middleware('check.subscription')->group(function (): void {
        Route::prefix('notifications/push')->name('notifications.push.')->group(function (): void {
            Route::get('/status', [PushSubscriptionController::class, 'status'])
                ->name('status');
            Route::post('/subscribe', [PushSubscriptionController::class, 'subscribe'])
                ->name('subscribe');
            Route::post('/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])
                ->name('unsubscribe');
            Route::post('/test', [PushSubscriptionController::class, 'test'])
                ->middleware('throttle:10,1')
                ->name('test');
        });

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
            Route::get('/', function () {
                return redirect()->route('superadmin.dashboard');
            })->name('index');

            Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])
                ->name('dashboard');
            Route::get('/quotations', [SuperAdminQuotationController::class, 'index'])
                ->name('quotations.index');
            Route::get('/quotations/{quote}', [SuperAdminQuotationController::class, 'show'])
                ->name('quotations.show');
            Route::post('/quotations/{quote}/read', [SuperAdminQuotationController::class, 'markRead'])
                ->name('quotations.read');
            Route::get('/gym-list', [SuperAdminDashboardController::class, 'gymListing'])
                ->name('gym-list.index');
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
            Route::patch('/gyms/{gym}/admin-user/password', [SuperAdminDashboardController::class, 'updateGymAdminPassword'])
                ->name('gyms.admin-user.password.update');
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
            Route::post('/support-chat/heartbeat', [SuperAdminSupportChatController::class, 'heartbeat'])
                ->middleware('throttle:120,1')
                ->name('support-chat.heartbeat');
            Route::get('/support-chat/unread-count', [SuperAdminSupportChatController::class, 'unreadCountJson'])
                ->middleware('throttle:120,1')
                ->name('support-chat.unread-count');
            Route::get('/support-chat/{conversation}/state', [SuperAdminSupportChatController::class, 'state'])
                ->middleware('throttle:120,1')
                ->name('support-chat.state');
            Route::post('/support-chat/{conversation}/reply', [SuperAdminSupportChatController::class, 'reply'])
                ->middleware('throttle:80,1')
                ->name('support-chat.reply');
            Route::post('/support-chat/{conversation}/status', [SuperAdminSupportChatController::class, 'updateStatus'])
                ->middleware('throttle:80,1')
                ->name('support-chat.status');
            Route::post('/support-chat/{conversation}/read', [SuperAdminSupportChatController::class, 'markRead'])
                ->middleware('throttle:80,1')
                ->name('support-chat.read');
            Route::post('/support-chat/{conversation}/finalize', [SuperAdminSupportChatController::class, 'finalize'])
                ->middleware('throttle:80,1')
                ->name('support-chat.finalize');
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
            Route::post('/notifications/push-campaigns', [SuperAdminNotificationsController::class, 'sendPushCampaign'])
                ->middleware('throttle:20,1')
                ->name('notifications.push-campaigns.send');
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
            ->middleware(['gym.route', 'pwa.standalone.access', 'owner.activity'])
            ->group(function (): void {
                Route::get('/panel', [GymPanelController::class, 'index'])
                    ->middleware('role:owner,cashier')
                    ->name('panel.index');
                Route::get('/panel/live-clients', [GymPanelController::class, 'liveClients'])
                    ->middleware('role:owner,cashier')
                    ->name('panel.live-clients');
                Route::post('/panel/owner-activity', [GymPanelController::class, 'ownerActivityHeartbeat'])
                    ->middleware('role:owner,cashier')
                    ->name('panel.owner-activity.heartbeat');

                Route::prefix('support-chat')
                    ->middleware('role:owner,cashier')
                    ->name('support-chat.gym.')
                    ->group(function (): void {
                        Route::get('/state', [SupportChatController::class, 'gymState'])
                            ->middleware('throttle:120,1')
                            ->name('state');
                        Route::post('/restart', [SupportChatController::class, 'gymRestart'])
                            ->middleware('throttle:40,1')
                            ->name('restart');
                        Route::post('/quick-reply', [SupportChatController::class, 'gymQuickReply'])
                            ->middleware('throttle:60,1')
                            ->name('quick-reply');
                        Route::post('/message', [SupportChatController::class, 'gymSendMessage'])
                            ->middleware('throttle:60,1')
                            ->name('message');
                    });

                Route::get('/clients', [ClientController::class, 'index'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.index');
                Route::post('/clients', [ClientController::class, 'store'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.store');
                Route::get('/clients/check-document', [ClientController::class, 'checkDocument'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.check-document');
                Route::get('/clients/{client}', [ClientController::class, 'show'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.show');
                Route::patch('/clients/{client}/basic', [ClientController::class, 'updateBasic'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.basic.update');
                Route::delete('/clients/{client}', [ClientController::class, 'destroy'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.destroy');
                Route::patch('/clients/{client}/photo', [ClientController::class, 'updatePhoto'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.photo.update');
                Route::patch('/clients/{client}/app-account', [ClientController::class, 'updateAppAccount'])
                    ->middleware(['role:owner,cashier', 'plan.feature:client_accounts'])
                    ->name('clients.app-account.update');
                Route::patch('/clients/{client}/app-password', [ClientController::class, 'resetAppPassword'])
                    ->middleware(['role:owner,cashier', 'plan.feature:client_accounts'])
                    ->name('clients.app-password.reset');
                Route::get('/clients/{client}/card', [ClientCardController::class, 'show'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.card');
                Route::get('/clients/{client}/card.pdf', [ClientCardController::class, 'pdf'])
                    ->middleware('role:owner,cashier')
                    ->name('clients.card.pdf');

                Route::get('/staff', [GymStaffController::class, 'index'])
                    ->middleware('role:owner')
                    ->name('staff.index');
                Route::post('/staff/cashiers', [GymStaffController::class, 'storeCashier'])
                    ->middleware('role:owner')
                    ->name('staff.cashiers.store');
                Route::patch('/staff/cashiers/{cashier}/permissions', [GymStaffController::class, 'updateCashierPermissions'])
                    ->middleware('role:owner')
                    ->name('staff.cashiers.permissions.update');
                Route::patch('/staff/cashiers/{cashier}/password', [GymStaffController::class, 'updateCashierPassword'])
                    ->middleware('role:owner')
                    ->name('staff.cashiers.password.update');
                Route::patch('/staff/cashiers/{cashier}/disable', [GymStaffController::class, 'disableCashier'])
                    ->middleware('role:owner')
                    ->name('staff.cashiers.disable');
                Route::patch('/staff/cashiers/{cashier}/activate', [GymStaffController::class, 'activateCashier'])
                    ->middleware('role:owner')
                    ->name('staff.cashiers.activate');
                Route::delete('/staff/cashiers/{cashier}', [GymStaffController::class, 'destroyCashier'])
                    ->middleware('role:owner')
                    ->name('staff.cashiers.destroy');

                Route::get('/plans', [PlanController::class, 'index'])
                    ->middleware('role:owner')
                    ->name('plans.index');
                Route::post('/plans', [PlanController::class, 'store'])
                    ->middleware(['role:owner', 'not.branch:manage_plans'])
                    ->name('plans.store');
                Route::put('/plans/{plan}', [PlanController::class, 'update'])
                    ->middleware(['role:owner', 'not.branch:manage_plans'])
                    ->name('plans.update');
                Route::patch('/plans/{plan}/toggle', [PlanController::class, 'toggle'])
                    ->middleware(['role:owner', 'not.branch:manage_plans'])
                    ->name('plans.toggle');
                Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])
                    ->middleware(['role:owner', 'not.branch:manage_plans'])
                    ->name('plans.destroy');
                Route::post('/plans/promotions', [PlanController::class, 'storePromotion'])
                    ->middleware(['role:owner', 'plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.store');
                Route::put('/plans/promotions/{promotion}', [PlanController::class, 'updatePromotion'])
                    ->middleware(['role:owner', 'plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.update');
                Route::patch('/plans/promotions/{promotion}/toggle', [PlanController::class, 'togglePromotion'])
                    ->middleware(['role:owner', 'plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.toggle');
                Route::delete('/plans/promotions/{promotion}', [PlanController::class, 'destroyPromotion'])
                    ->middleware(['role:owner', 'plan.feature:promotions', 'not.branch:manage_promotions'])
                    ->name('plans.promotions.destroy');

                Route::post('/memberships', [MembershipController::class, 'store'])
                    ->middleware('role:owner,cashier')
                    ->name('memberships.store');
                Route::patch('/memberships/{membership}/adjust', [MembershipController::class, 'adjust'])
                    ->middleware('role:owner')
                    ->name('memberships.adjust');
                Route::post('/clients/{client}/credentials/rfid', [ClientCredentialController::class, 'storeRfid'])
                    ->middleware('role:owner,cashier')
                    ->name('client-credentials.store-rfid');
                Route::post('/clients/{client}/credentials/qr', [ClientCredentialController::class, 'generateQr'])
                    ->middleware('role:owner,cashier')
                    ->name('client-credentials.generate-qr');
                Route::patch('/client-credentials/{credential}/deactivate', [ClientCredentialController::class, 'deactivate'])
                    ->middleware('role:owner,cashier')
                    ->name('client-credentials.deactivate');

                Route::get('/reception', [ReceptionCheckInController::class, 'index'])
                    ->middleware('role:owner,cashier')
                    ->name('reception.index');
                Route::get('/reception/display', [ReceptionCheckInController::class, 'display'])
                    ->middleware('role:owner,cashier')
                    ->name('reception.display');
                Route::get('/reception/mobile-display', [ReceptionCheckInController::class, 'mobileDisplay'])
                    ->middleware('role:owner,cashier')
                    ->name('reception.mobile-display');
                Route::get('/reception/sync/latest', [ReceptionCheckInController::class, 'syncLatest'])
                    ->middleware('role:owner,cashier')
                    ->name('reception.sync.latest');
                Route::get('/reception/mobile-qr', [ReceptionCheckInController::class, 'mobileQr'])
                    ->middleware(['role:owner,cashier', 'plan.feature:client_accounts', 'throttle:600,1'])
                    ->name('reception.mobile-qr');
                Route::get('/reception/mobile-qr/status', [ReceptionCheckInController::class, 'mobileQrStatus'])
                    ->middleware(['role:owner,cashier', 'plan.feature:client_accounts', 'throttle:600,1'])
                    ->name('reception.mobile-qr.status');
                Route::post('/reception/check-in', [ReceptionCheckInController::class, 'store'])
                    ->middleware(['role:owner,cashier', 'throttle:checkin'])
                    ->name('reception.check-in');
                Route::post('/reception/check-out', [ReceptionCheckInController::class, 'checkOut'])
                    ->middleware(['role:owner,cashier', 'throttle:checkin'])
                    ->name('reception.check-out');

                Route::get('/cash', [CashController::class, 'index'])
                    ->middleware('role:owner,cashier')
                    ->name('cash.index');
                Route::post('/cash/open', [CashController::class, 'open'])
                    ->middleware(['role:owner,cashier', 'not.branch:manage_cash'])
                    ->name('cash.open');
                Route::post('/cash/movements', [CashController::class, 'addMovement'])
                    ->middleware(['role:owner,cashier', 'not.branch:manage_cash'])
                    ->name('cash.movements.store');
                Route::post('/cash/close', [CashController::class, 'close'])
                    ->middleware(['role:owner,cashier', 'not.branch:manage_cash'])
                    ->name('cash.close');
                Route::get('/cash/movements/monthly', [CashController::class, 'monthlyMovements'])
                    ->middleware('role:owner,cashier')
                    ->name('cash.movements.monthly');
                Route::get('/cash/sessions', [CashController::class, 'sessions'])
                    ->middleware('role:owner,cashier')
                    ->name('cash.sessions.index');
                Route::get('/cash/sessions/{session}', [CashController::class, 'show'])
                    ->middleware('role:owner,cashier')
                    ->name('cash.sessions.show');

                Route::get('/sales', [SalesInventoryController::class, 'index'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('sales.index');
                Route::post('/sales', [SalesInventoryController::class, 'storeSale'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('sales.store');
                Route::post('/remote-scanner/sessions', [RemoteScanController::class, 'createSession'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('remote-scanner.sessions.store');
                Route::get('/remote-scanner/{channel}/stream', [RemoteScanController::class, 'stream'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('remote-scanner.stream');
                Route::delete('/remote-scanner/{channel}', [RemoteScanController::class, 'close'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('remote-scanner.close');

                Route::get('/products', [ProductController::class, 'index'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('products.index');
                Route::post('/products', [ProductController::class, 'store'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('products.store');
                Route::put('/products/{product}', [ProductController::class, 'update'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('products.update');
                Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('products.toggle');
                Route::post('/products/{product}/stock', [ProductController::class, 'adjustStock'])
                    ->middleware(['role:owner,cashier', 'plan.feature:sales_inventory'])
                    ->name('products.stock');

                Route::get('/sucursales', [BranchController::class, 'index'])
                    ->middleware(['role:owner', 'not.branch:manage_branches', 'plan.feature:multi_branch'])
                    ->name('branches.index');

                Route::get('/reports', [ReportController::class, 'index'])
                    ->middleware(['role:owner', 'plan.feature:reports_base'])
                    ->name('reports.index');
                Route::get('/reports/income', [ReportController::class, 'income'])
                    ->middleware(['role:owner', 'plan.feature:reports_base'])
                    ->name('reports.income');
                Route::get('/reports/attendance', [ReportController::class, 'attendance'])
                    ->middleware(['role:owner', 'plan.feature:reports_base'])
                    ->name('reports.attendance');
                Route::get('/reports/memberships', [ReportController::class, 'memberships'])
                    ->middleware(['role:owner', 'plan.feature:reports_base'])
                    ->name('reports.memberships');
                Route::get('/reports/client-earnings', [ReportController::class, 'clientEarnings'])
                    ->middleware(['role:owner', 'plan.feature:reports_base'])
                    ->name('reports.client-earnings');
                Route::get('/reports/sales-inventory', [ReportController::class, 'salesInventory'])
                    ->middleware(['role:owner', 'plan.feature:sales_inventory_reports'])
                    ->name('reports.sales-inventory');
                Route::get('/reports/sales-inventory/export/csv', [ReportController::class, 'exportSalesInventoryCsv'])
                    ->middleware(['role:owner', 'plan.feature:reports_export', 'plan.feature:sales_inventory_reports', 'not.branch:export_reports'])
                    ->name('reports.sales-inventory.export.csv');
                Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])
                    ->middleware(['role:owner', 'plan.feature:reports_export', 'not.branch:export_reports'])
                    ->name('reports.export.csv');
                Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
                    ->middleware(['role:owner', 'plan.feature:reports_export', 'not.branch:export_reports'])
                    ->name('reports.export.pdf');

                Route::get('/portal-cliente', [ClientPortalController::class, 'index'])
                    ->middleware(['role:owner,cashier', 'plan.feature:client_accounts'])
                    ->name('client-portal.index');

                // Context-aware profile/settings/contact routes for gym users.
                Route::get('/profile', [ThemeController::class, 'profile'])->middleware('role:owner')->name('gym.profile.index');
                Route::get('/contact', [ThemeController::class, 'contact'])->middleware('role:owner')->name('gym.contact.index');
                Route::post('/contact/suggestions', [ContactSuggestionController::class, 'store'])->middleware('role:owner')->name('gym.contact.suggestions.store');
                Route::get('/profile/membership-invoices/{subscription}/pdf', [ThemeController::class, 'membershipInvoicePdf'])
                    ->middleware('role:owner')
                    ->name('gym.profile.membership-invoice.pdf');
                Route::get('/config', [ThemeController::class, 'index'])->middleware('role:owner,cashier')->name('gym.settings.index');
                Route::post('/config/theme', [ThemeController::class, 'update'])->middleware('role:owner,cashier')->name('gym.settings.theme.update');
                Route::post('/config/profile-photo', [ThemeController::class, 'updateOwnProfilePhoto'])->middleware('role:owner,cashier')->name('gym.settings.profile-photo.update');
                Route::post('/config/profile', [ThemeController::class, 'updateProfile'])->middleware('role:owner')->name('gym.settings.profile.update');
                Route::post('/config/gym-profile', [ThemeController::class, 'updateGymProfile'])->middleware('role:owner')->name('gym.settings.gym-profile.update');
                Route::post('/config/gym-logo', [ThemeController::class, 'updateGymLogo'])->middleware('role:owner')->name('gym.settings.gym-logo.update');
                Route::post('/config/gym-avatars', [ThemeController::class, 'updateGymAvatars'])->middleware('role:owner')->name('gym.settings.gym-avatars.update');
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
        Route::post('/config/profile-photo', [ThemeController::class, 'updateOwnProfilePhoto'])->name('settings.profile-photo.update');
        Route::post('/config/profile', [ThemeController::class, 'updateProfile'])->name('settings.profile.update');
        Route::post('/config/superadmin-timezone', [ThemeController::class, 'updateSuperAdminTimezone'])->name('settings.superadmin-timezone.update');
        Route::post('/config/superadmin-contact', [ThemeController::class, 'updateSuperAdminContact'])->name('settings.superadmin-contact.update');
        Route::post('/config/profile/password', [ThemeController::class, 'updateProfilePassword'])->name('settings.profile.password.update');
        Route::post('/config/profile/logout-others', [ThemeController::class, 'logoutOtherDevices'])->name('settings.profile.logout-others');
        Route::post('/config/gym-profile', [ThemeController::class, 'updateGymProfile'])->name('settings.gym-profile.update');
        Route::post('/config/gym-logo', [ThemeController::class, 'updateGymLogo'])->name('settings.gym-logo.update');
        Route::post('/config/gym-avatars', [ThemeController::class, 'updateGymAvatars'])->name('settings.gym-avatars.update');

    });
});

