<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientCardController;
use App\Http\Controllers\ClientCredentialController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReceptionCheckInController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdminNotificationsController;
use App\Http\Controllers\SubscriptionAdminController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::view('/subscription/expired', 'subscription.expired')->name('subscription.expired');

    Route::middleware('check.subscription')->group(function (): void {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/card', [ClientCardController::class, 'show'])->name('clients.card');
        Route::get('/clients/{client}/card.pdf', [ClientCardController::class, 'pdf'])->name('clients.card.pdf');

        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');

        Route::post('/memberships', [MembershipController::class, 'store'])->name('memberships.store');
        Route::post('/clients/{client}/credentials/rfid', [ClientCredentialController::class, 'storeRfid'])
            ->name('client-credentials.store-rfid');
        Route::post('/clients/{client}/credentials/qr', [ClientCredentialController::class, 'generateQr'])
            ->name('client-credentials.generate-qr');
        Route::patch('/client-credentials/{credential}/deactivate', [ClientCredentialController::class, 'deactivate'])
            ->name('client-credentials.deactivate');

        Route::get('/reception', [ReceptionCheckInController::class, 'index'])->name('reception.index');
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

        Route::get('/config', [ThemeController::class, 'index'])->name('settings.index');
        Route::post('/config/theme', [ThemeController::class, 'update'])->name('settings.theme.update');
        Route::post('/config/gym-profile', [ThemeController::class, 'updateGymProfile'])->name('settings.gym-profile.update');
        Route::post('/config/gym-logo', [ThemeController::class, 'updateGymLogo'])->name('settings.gym-logo.update');

        Route::middleware('superadmin')->prefix('superadmin')->name('superadmin.')->group(function (): void {
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'dashboard'])
                ->name('dashboard');
            Route::get('/gyms', [SuperAdminDashboardController::class, 'gyms'])
                ->name('gyms.index');
            Route::get('/notifications', [SuperAdminNotificationsController::class, 'index'])
                ->name('notifications.index');
            Route::get('/notifications/history', [SuperAdminNotificationsController::class, 'history'])
                ->name('notifications.history');
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
    });
});
