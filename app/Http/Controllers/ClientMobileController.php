<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientFitnessProfile;
use App\Models\ClientPushSubscription;
use App\Models\Gym;
use App\Models\Membership;
use App\Models\PresenceSession;
use App\Models\User;
use App\Services\AttendanceCheckinService;
use App\Services\ClientPushNotificationService;
use App\Services\MobileCheckInTokenService;
use App\Services\PlanAccessService;
use App\Services\WebPushService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientMobileController extends Controller
{
    public function __construct(
        private readonly PlanAccessService $planAccessService,
        private readonly AttendanceCheckinService $attendanceCheckinService,
        private readonly MobileCheckInTokenService $mobileCheckInTokenService,
        private readonly ClientPushNotificationService $clientPushNotificationService
    ) {
    }

    public function login(Request $request, string $gymSlug): View|RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $session = (array) $request->session()->get('client_mobile', []);
        if ((int) ($session['client_id'] ?? 0) > 0 && (int) ($session['gym_id'] ?? 0) === (int) $gym->id) {
            return redirect()->route('client-mobile.app', ['gymSlug' => $gym->slug]);
        }

        return view('client-mobile.login', ['gym' => $gym]);
    }

    public function authenticate(Request $request, string $gymSlug): RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $data = $request->validate([
            'username' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'max:120'],
        ]);

        $username = mb_strtolower(trim((string) $data['username']));
        $client = Client::query()
            ->forGym((int) $gym->id)
            ->whereRaw('LOWER(app_username) = ?', [$username])
            ->first(['id', 'gym_id', 'status', 'app_password']);

        if (! $client || trim((string) ($client->app_password ?? '')) === '' || ! Hash::check((string) $data['password'], (string) $client->app_password)) {
            return back()->withErrors([
                'mobile_login' => __('messages.client_mobile.invalid_credentials'),
            ])->withInput($request->only('username'));
        }

        if ((string) ($client->status ?? 'inactive') !== 'active') {
            return back()->withErrors([
                'mobile_login' => __('messages.client_mobile.inactive_profile'),
            ])->withInput($request->only('username'));
        }

        $request->session()->regenerate();
        $request->session()->put('client_mobile', [
            'client_id' => (int) $client->id,
            'gym_id' => (int) $gym->id,
            'login_at' => now()->toIso8601String(),
        ]);

        return redirect()->route('client-mobile.app', ['gymSlug' => $gym->slug]);
    }

    public function logout(Request $request, string $gymSlug): RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);

        $request->session()->forget('client_mobile');
        $request->session()->regenerateToken();

        return redirect()->route('client-mobile.login', ['gymSlug' => $gym->slug]);
    }

    public function app(Request $request, string $gymSlug): View|RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return redirect()->route('client-mobile.login', ['gymSlug' => $gym->slug]);
        }

        $screen = mb_strtolower(trim((string) $request->query('screen', 'home')));
        if (! in_array($screen, ['home', 'checkin', 'progress', 'profile', 'physical'], true)) {
            $screen = 'home';
        }

        $fitnessProfile = $this->resolveFitnessProfile((int) $gym->id, (int) $client->id);
        $fitnessProfileCompleted = $this->isFitnessProfileCompleted($fitnessProfile);
        $openFitnessModal = false;

        if ($screen === 'progress' && ! $fitnessProfileCompleted) {
            // Progress screen requires initial physical profile to be completed first.
            $screen = 'home';
            $openFitnessModal = true;
        }

        if (! $fitnessProfileCompleted) {
            $oldFitnessModal = (string) $request->session()->getOldInput('_fitness_modal', '');
            if ($request->boolean('open_fitness_modal') || $oldFitnessModal === '1') {
                $openFitnessModal = true;
            }
        }

        return view('client-mobile.app', [
            'gym' => $gym,
            'client' => $client,
            'screen' => $screen,
            'progress' => $this->progressPayload((int) $gym->id, (int) $client->id, (string) ($gym->timezone ?? ''), (string) ($gym->slug ?? '')),
            'fitnessProfile' => $fitnessProfile,
            'fitnessProfileCompleted' => $fitnessProfileCompleted,
            'openFitnessModal' => $openFitnessModal,
            'webPushPublicKey' => trim((string) config('services.webpush.vapid.public_key', '')),
        ]);
    }

    public function progress(Request $request, string $gymSlug): JsonResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.client_mobile.invalid_session'),
            ], 401);
        }

        return response()->json([
            'ok' => true,
            'progress' => $this->progressPayload((int) $gym->id, (int) $client->id, (string) ($gym->timezone ?? ''), (string) ($gym->slug ?? '')),
        ]);
    }

    public function checkIn(Request $request, string $gymSlug): JsonResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.client_mobile.invalid_session'),
            ], 401);
        }

        $data = $request->validate([
            'token' => ['required', 'string', 'max:300'],
        ]);

        $token = $this->mobileCheckInTokenService->extractToken((string) $data['token']);
        $consumed = $this->mobileCheckInTokenService->consume($token, (int) $gym->id);
        if (! (bool) ($consumed['ok'] ?? false)) {
            return response()->json([
                'ok' => false,
                'reason' => (string) ($consumed['reason'] ?? 'token_invalid'),
                'message' => (string) ($consumed['message'] ?? __('messages.client_mobile.invalid_qr')),
            ], 422);
        }

        $operatorId = $this->resolveMobileOperatorUserId((int) $gym->id);
        if ($operatorId <= 0) {
            return response()->json([
                'ok' => false,
                'reason' => 'operator_missing',
                'message' => __('messages.client_mobile.operator_missing'),
            ], 422);
        }

        $result = $this->attendanceCheckinService->checkInByValue(
            (int) $gym->id,
            $operatorId,
            (string) $client->document_number
        );

        $result['method'] = 'mobile_proximity';
        $this->publishReceptionSync((int) $gym->id, $result);

        return response()->json([
            ...$result,
            'progress' => $this->progressPayload((int) $gym->id, (int) $client->id, (string) ($gym->timezone ?? ''), (string) ($gym->slug ?? '')),
        ], (bool) ($result['ok'] ?? false) ? 200 : 422);
    }

    public function updateProfile(Request $request, string $gymSlug): RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $sessionClient = $this->resolveSessionClient($request, $gym);
        if (! $sessionClient) {
            return redirect()->route('client-mobile.login', ['gymSlug' => $gym->slug]);
        }

        $phoneInput = trim((string) $request->input('phone', ''));
        $request->merge([
            'phone' => $phoneInput !== '' ? $phoneInput : null,
        ]);

        $data = $request->validate([
            'current_password' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+\-\s()]+$/'],
            'new_password' => ['nullable', 'string', 'min:8', 'max:120', 'confirmed'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'current_password.required' => 'Ingresa tu contraseña actual.',
            'current_password.max' => 'La contraseña actual no puede superar 120 caracteres.',
            'phone.regex' => 'El teléfono solo puede contener números y los símbolos + - ( ).',
            'phone.max' => 'El teléfono no puede superar 25 caracteres.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.max' => 'La nueva contraseña no puede superar 120 caracteres.',
            'new_password.confirmed' => 'La confirmación de nueva contraseña no coincide.',
            'photo.image' => 'La foto debe ser una imagen válida.',
            'photo.mimes' => 'La foto debe ser JPG, JPEG, PNG o WEBP.',
            'photo.max' => 'La foto no puede superar 4 MB.',
        ]);

        $client = Client::query()
            ->forGym((int) $gym->id)
            ->findOrFail((int) $sessionClient->id, ['id', 'gym_id', 'app_password', 'phone', 'photo_path']);

        if (
            trim((string) ($client->app_password ?? '')) === ''
            || ! Hash::check((string) $data['current_password'], (string) $client->app_password)
        ) {
            return back()
                ->withErrors(['current_password' => 'La contraseña actual no es correcta.'])
                ->withInput($request->except(['current_password', 'new_password', 'new_password_confirmation']));
        }

        $phoneNormalized = trim((string) ($data['phone'] ?? ''));
        $phoneNormalized = preg_replace('/\s+/u', ' ', $phoneNormalized) ?? '';
        $phoneChanged = $phoneNormalized !== trim((string) ($client->phone ?? ''));
        $passwordChanged = trim((string) ($data['new_password'] ?? '')) !== '';
        $photoChanged = $request->hasFile('photo');

        if (! $phoneChanged && ! $passwordChanged && ! $photoChanged) {
            return back()
                ->withErrors(['profile' => 'No detectamos cambios para guardar.'])
                ->withInput($request->except(['current_password', 'new_password', 'new_password_confirmation']));
        }

        $updates = [];
        if ($phoneChanged) {
            $updates['phone'] = $phoneNormalized !== '' ? $phoneNormalized : null;
        }

        if ($passwordChanged) {
            $updates['app_password'] = Hash::make((string) $data['new_password']);
        }

        $oldPhotoPath = trim((string) ($client->photo_path ?? ''));
        if ($photoChanged) {
            $updates['photo_path'] = $request->file('photo')->store('clients', 'public');
        }

        if ($updates !== []) {
            $client->update($updates);
        }

        if ($photoChanged) {
            $this->deletePublicAssetIfLocal($oldPhotoPath);
        }

        return redirect()
            ->route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'profile'])
            ->with('profile_status', 'Perfil actualizado correctamente.');
    }

    public function saveFitnessProfile(Request $request, string $gymSlug): RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $sessionClient = $this->resolveSessionClient($request, $gym);
        if (! $sessionClient) {
            return redirect()->route('client-mobile.login', ['gymSlug' => $gym->slug]);
        }

        $data = $request->validate([
            'age' => ['required', 'integer', 'min:12', 'max:90'],
            'sex' => ['required', 'string', 'in:masculino,femenino,otro'],
            'height_cm' => ['nullable', 'numeric', 'min:120', 'max:250'],
            'weight_kg' => ['nullable', 'numeric', 'min:30', 'max:400'],
            'goal' => ['required', 'string', 'in:ganar_musculo,perder_grasa,mantener_forma,definir,aumentar_fuerza,mejorar_resistencia'],
            'experience_level' => ['required', 'string', 'in:principiante,intermedio,avanzado'],
            'days_per_week' => ['required', 'integer', 'in:3,4,5,6,7'],
            'session_minutes' => ['required', 'integer', 'in:45,60,90'],
            'limitations' => ['nullable', 'array', 'max:5'],
            'limitations.*' => ['string', 'in:ninguna,rodilla,espalda,hombro,codo,cuello,tobillo'],
            'next_screen' => ['nullable', 'string', 'in:home,progress,physical'],
        ], [
            'age.required' => 'Ingresa tu edad.',
            'age.integer' => 'La edad debe ser un numero entero.',
            'age.min' => 'Edad minima permitida: 12.',
            'age.max' => 'Edad maxima permitida: 90.',
            'sex.required' => 'Selecciona tu sexo.',
            'sex.in' => 'Selecciona una opcion valida de sexo.',
            'height_cm.numeric' => 'La altura debe ser numerica.',
            'height_cm.min' => 'La altura minima es 120 cm.',
            'height_cm.max' => 'La altura maxima es 250 cm.',
            'weight_kg.numeric' => 'El peso debe ser numerico.',
            'weight_kg.min' => 'El peso minimo es 30 kg.',
            'weight_kg.max' => 'El peso maximo es 400 kg.',
            'goal.required' => 'Selecciona tu objetivo del gimnasio.',
            'goal.in' => 'Selecciona un objetivo valido.',
            'experience_level.required' => 'Selecciona tu nivel.',
            'experience_level.in' => 'Selecciona un nivel valido.',
            'days_per_week.required' => 'Selecciona tus dias por semana.',
            'days_per_week.in' => 'Selecciona 3, 4, 5, 6 o 7 dias.',
            'session_minutes.required' => 'Selecciona el tiempo por entrenamiento.',
            'session_minutes.in' => 'Selecciona 45, 60 o 90 minutos.',
            'limitations.array' => 'Las limitaciones deben enviarse como lista.',
            'limitations.max' => 'Puedes seleccionar maximo 5 limitaciones.',
            'limitations.*.in' => 'Hay una limitacion no valida.',
            'next_screen.in' => 'Pantalla de retorno no valida.',
        ]);

        $limitations = collect($data['limitations'] ?? [])
            ->map(static fn ($value): string => mb_strtolower(trim((string) $value)))
            ->filter(static fn (string $value): bool => $value !== '')
            ->unique()
            ->values();

        if ($limitations->contains('ninguna') || $limitations->isEmpty()) {
            $limitations = collect(['ninguna']);
        }

        $heightCm = $this->normalizeNumericInput($data['height_cm'] ?? null);
        $weightKg = $this->normalizeNumericInput($data['weight_kg'] ?? null);
        $bodyMetrics = $this->buildBodyMetrics(
            age: (int) $data['age'],
            sex: (string) $data['sex'],
            heightCm: $heightCm,
            weightKg: $weightKg,
            goal: (string) $data['goal'],
            daysPerWeek: (int) $data['days_per_week'],
            sessionMinutes: (int) $data['session_minutes'],
        );

        ClientFitnessProfile::query()->updateOrCreate(
            [
                'gym_id' => (int) $gym->id,
                'client_id' => (int) $sessionClient->id,
            ],
            [
                'age' => (int) $data['age'],
                'sex' => (string) $data['sex'],
                'height_cm' => $heightCm,
                'weight_kg' => $weightKg,
                'goal' => (string) $data['goal'],
                'experience_level' => (string) $data['experience_level'],
                'days_per_week' => (int) $data['days_per_week'],
                'session_minutes' => (int) $data['session_minutes'],
                'limitations' => $limitations->all(),
                'body_metrics' => $bodyMetrics,
                'onboarding_completed_at' => now(),
            ]
        );

        $nextScreen = mb_strtolower(trim((string) ($data['next_screen'] ?? 'progress')));
        if (! in_array($nextScreen, ['home', 'progress', 'physical'], true)) {
            $nextScreen = 'progress';
        }

        return redirect()
            ->route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => $nextScreen])
            ->with('fitness_status', 'Datos fisicos guardados correctamente.');
    }

    public function updateWeeklyGoal(Request $request, string $gymSlug): RedirectResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $sessionClient = $this->resolveSessionClient($request, $gym);
        if (! $sessionClient) {
            return redirect()->route('client-mobile.login', ['gymSlug' => $gym->slug]);
        }

        $data = $request->validate([
            'weekly_goal' => ['required', 'integer', 'in:3,4,5,6,7'],
        ], [
            'weekly_goal.required' => 'Selecciona una meta semanal.',
            'weekly_goal.integer' => 'La meta semanal debe ser numerica.',
            'weekly_goal.in' => 'Selecciona entre 3 y 7 dias por semana.',
        ]);

        $fitnessProfile = $this->resolveFitnessProfile((int) $gym->id, (int) $sessionClient->id);
        if (! $fitnessProfile) {
            return back()
                ->withErrors(['weekly_goal_profile' => 'Primero completa tus datos fisicos para ajustar la meta semanal.'])
                ->withInput([
                    '_weekly_goal_form' => '1',
                    'weekly_goal' => (int) $data['weekly_goal'],
                ]);
        }

        $weeklyGoal = (int) $data['weekly_goal'];
        $currentBodyMetrics = is_array($fitnessProfile->body_metrics ?? null) ? $fitnessProfile->body_metrics : [];
        $recalculatedBodyMetrics = $this->buildBodyMetrics(
            age: (int) $fitnessProfile->age,
            sex: (string) $fitnessProfile->sex,
            heightCm: $this->normalizeNumericInput($fitnessProfile->height_cm),
            weightKg: $this->normalizeNumericInput($fitnessProfile->weight_kg),
            goal: (string) $fitnessProfile->goal,
            daysPerWeek: $weeklyGoal,
            sessionMinutes: (int) $fitnessProfile->session_minutes,
        );
        if ($currentBodyMetrics !== []) {
            $recalculatedBodyMetrics = array_merge($currentBodyMetrics, $recalculatedBodyMetrics);
        }

        $fitnessProfile->update([
            'days_per_week' => $weeklyGoal,
            'body_metrics' => $recalculatedBodyMetrics,
        ]);

        return redirect()
            ->route('client-mobile.app', ['gymSlug' => $gym->slug, 'screen' => 'progress'])
            ->with('goal_status', 'Meta semanal actualizada correctamente.');
    }

    public function pushStatus(Request $request, string $gymSlug, WebPushService $webPushService): JsonResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.client_mobile.invalid_session'),
            ], 401);
        }

        $activeCount = ClientPushSubscription::query()
            ->active()
            ->where('gym_id', (int) $gym->id)
            ->where('client_id', (int) $client->id)
            ->count();

        return response()->json([
            'ok' => true,
            'active_subscriptions' => $activeCount,
            'webpush_ready' => $webPushService->isConfigured(),
            'has_vapid_public_key' => trim((string) config('services.webpush.vapid.public_key', '')) !== '',
        ]);
    }

    public function pushSubscribe(Request $request, string $gymSlug, WebPushService $webPushService): JsonResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.client_mobile.invalid_session'),
            ], 401);
        }

        $data = $request->validate([
            'subscription' => ['required', 'array'],
            'subscription.endpoint' => ['required', 'string', 'max:4096'],
            'subscription.keys' => ['required', 'array'],
            'subscription.keys.p256dh' => ['required', 'string', 'max:2048'],
            'subscription.keys.auth' => ['required', 'string', 'max:1024'],
            'encoding' => ['nullable', 'string', 'max:32'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $subscriptionData = (array) $data['subscription'];
        $endpoint = trim((string) ($subscriptionData['endpoint'] ?? ''));
        if ($endpoint === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Endpoint de push invalido.',
            ], 422);
        }

        $encoding = mb_strtolower(trim((string) ($data['encoding'] ?? '')));
        if (! in_array($encoding, ['aesgcm', 'aes128gcm'], true)) {
            $encoding = 'aesgcm';
        }

        $subscription = ClientPushSubscription::query()->updateOrCreate(
            ['endpoint_hash' => hash('sha256', $endpoint)],
            [
                'gym_id' => (int) $gym->id,
                'client_id' => (int) $client->id,
                'endpoint' => $endpoint,
                'public_key' => (string) (($subscriptionData['keys']['p256dh'] ?? '')),
                'auth_token' => (string) (($subscriptionData['keys']['auth'] ?? '')),
                'content_encoding' => $encoding,
                'user_agent' => mb_substr((string) ($request->userAgent() ?? ''), 0, 1024),
                'device_name' => isset($data['device_name']) && trim((string) $data['device_name']) !== ''
                    ? trim((string) $data['device_name'])
                    : null,
                'revoked_at' => null,
            ]
        );

        return response()->json([
            'ok' => true,
            'subscription_id' => (int) $subscription->id,
            'active_subscriptions' => ClientPushSubscription::query()
                ->active()
                ->where('gym_id', (int) $gym->id)
                ->where('client_id', (int) $client->id)
                ->count(),
            'webpush_ready' => $webPushService->isConfigured(),
            'message' => 'Notificaciones activadas para este dispositivo.',
        ]);
    }

    public function pushUnsubscribe(Request $request, string $gymSlug): JsonResponse
    {
        $gym = $this->resolveGymBySlug($gymSlug);
        $this->abortIfFeatureUnavailable((int) $gym->id);

        $client = $this->resolveSessionClient($request, $gym);
        if (! $client) {
            return response()->json([
                'ok' => false,
                'message' => __('messages.client_mobile.invalid_session'),
            ], 401);
        }

        $data = $request->validate([
            'endpoint' => ['nullable', 'string', 'max:4096'],
        ]);

        $query = ClientPushSubscription::query()
            ->active()
            ->where('gym_id', (int) $gym->id)
            ->where('client_id', (int) $client->id);

        $endpoint = trim((string) ($data['endpoint'] ?? ''));
        if ($endpoint !== '') {
            $query->where('endpoint_hash', hash('sha256', $endpoint));
        }

        $revoked = $query->update([
            'revoked_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'revoked' => $revoked,
            'active_subscriptions' => ClientPushSubscription::query()
                ->active()
                ->where('gym_id', (int) $gym->id)
                ->where('client_id', (int) $client->id)
                ->count(),
            'message' => $revoked > 0
                ? 'Notificaciones desactivadas para este dispositivo.'
                : 'No se encontro una suscripcion activa para revocar.',
        ]);
    }

    private function isFitnessProfileCompleted(?ClientFitnessProfile $profile): bool
    {
        if (! $profile) {
            return false;
        }

        return $profile->onboarding_completed_at !== null;
    }

    private function resolveGymBySlug(string $gymSlug): Gym
    {
        return Gym::query()
            ->withoutDemoSessions()
            ->whereRaw('LOWER(slug) = ?', [mb_strtolower(trim($gymSlug))])
            ->firstOrFail(['id', 'name', 'slug', 'timezone']);
    }

    private function abortIfFeatureUnavailable(int $gymId): void
    {
        abort_if(! $this->planAccessService->canForGym($gymId, 'client_accounts'), 403, 'Tu plan actual no incluye acceso cliente PWA.');
    }

    private function resolveSessionClient(Request $request, Gym $gym): ?Client
    {
        $session = (array) $request->session()->get('client_mobile', []);
        $clientId = (int) ($session['client_id'] ?? 0);
        $sessionGymId = (int) ($session['gym_id'] ?? 0);

        if ($clientId <= 0 || $sessionGymId !== (int) $gym->id) {
            return null;
        }

        return Client::query()
            ->forGym((int) $gym->id)
            ->where('status', 'active')
            ->find($clientId, ['id', 'gym_id', 'first_name', 'last_name', 'document_number', 'app_username', 'phone', 'photo_path', 'status']);
    }

    private function resolveFitnessProfile(int $gymId, int $clientId): ?ClientFitnessProfile
    {
        return ClientFitnessProfile::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->first([
                'id',
                'gym_id',
                'client_id',
                'age',
                'sex',
                'height_cm',
                'weight_kg',
                'goal',
                'experience_level',
                'days_per_week',
                'session_minutes',
                'limitations',
                'body_metrics',
                'onboarding_completed_at',
                'updated_at',
            ]);
    }

    private function normalizeNumericInput(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $raw = str_replace(',', '.', $raw);
        if (! is_numeric($raw)) {
            return null;
        }

        return round((float) $raw, 2);
    }

    private function buildBodyMetrics(
        int $age,
        string $sex,
        ?float $heightCm,
        ?float $weightKg,
        string $goal,
        int $daysPerWeek,
        int $sessionMinutes
    ): array {
        $bmi = null;
        if ($heightCm !== null && $heightCm > 0.0 && $weightKg !== null && $weightKg > 0.0) {
            $heightMeters = $heightCm / 100;
            $bmi = round($weightKg / ($heightMeters * $heightMeters), 2);
        }

        $bmr = null;
        if ($heightCm !== null && $weightKg !== null) {
            $sexOffset = match ($sex) {
                'masculino' => 5,
                'femenino' => -161,
                default => -78,
            };

            $bmr = (int) round((10 * $weightKg) + (6.25 * $heightCm) - (5 * $age) + $sexOffset);
        }

        $activityFactor = match ($daysPerWeek) {
            3 => 1.37,
            4 => 1.44,
            5 => 1.52,
            6 => 1.62,
            7 => 1.72,
            default => 1.37,
        };
        if ($sessionMinutes >= 90) {
            $activityFactor += 0.04;
        } elseif ($sessionMinutes >= 60) {
            $activityFactor += 0.02;
        }

        $maintenanceKcal = $bmr !== null ? (int) round($bmr * $activityFactor) : null;

        $goalAdjustment = match ($goal) {
            'ganar_musculo' => 280,
            'perder_grasa' => -320,
            'mantener_forma' => 0,
            'definir' => -180,
            'aumentar_fuerza' => 160,
            'mejorar_resistencia' => 80,
            default => 0,
        };

        $targetKcal = null;
        if ($maintenanceKcal !== null) {
            $targetKcal = max(1100, $maintenanceKcal + $goalAdjustment);
        }

        $estimatedBodyFatPct = null;
        if ($bmi !== null && in_array($sex, ['masculino', 'femenino'], true)) {
            $sexBinary = $sex === 'masculino' ? 1 : 0;
            $estimatedBodyFatPct = round((1.20 * $bmi) + (0.23 * $age) - (10.8 * $sexBinary) - 5.4, 1);
            $estimatedBodyFatPct = max(3, min(55, $estimatedBodyFatPct));
        }

        return [
            'bmi' => $bmi,
            'bmi_category' => $this->resolveBmiCategory($bmi),
            'bmr_kcal' => $bmr,
            'maintenance_kcal' => $maintenanceKcal,
            'target_kcal' => $targetKcal,
            'estimated_body_fat_pct' => $estimatedBodyFatPct,
            'goal_track' => $this->resolveGoalTrack($goal),
            'calculated_at' => now()->toIso8601String(),
        ];
    }

    private function resolveBmiCategory(?float $bmi): ?string
    {
        if ($bmi === null || $bmi <= 0) {
            return null;
        }

        if ($bmi < 18.5) {
            return 'Bajo peso';
        }

        if ($bmi < 25) {
            return 'Rango saludable';
        }

        if ($bmi < 30) {
            return 'Sobrepeso';
        }

        if ($bmi < 35) {
            return 'Obesidad grado I';
        }

        if ($bmi < 40) {
            return 'Obesidad grado II';
        }

        return 'Obesidad grado III';
    }

    private function resolveGoalTrack(string $goal): string
    {
        return match ($goal) {
            'ganar_musculo' => 'Enfoque en superavit calorico y progresion de fuerza.',
            'perder_grasa' => 'Enfoque en deficit calorico con mantenimiento muscular.',
            'mantener_forma' => 'Enfoque en balance calorico y constancia semanal.',
            'definir' => 'Enfoque en recomposicion corporal y volumen de entrenamiento.',
            'aumentar_fuerza' => 'Enfoque en ejercicios compuestos y cargas progresivas.',
            'mejorar_resistencia' => 'Enfoque en volumen semanal y capacidad cardiovascular.',
            default => 'Enfoque general de acondicionamiento fisico.',
        };
    }

    private function resolveMobileOperatorUserId(int $gymId): int
    {
        $user = User::query()
            ->where('gym_id', $gymId)
            ->where(function ($query): void {
                $query->whereNull('role')
                    ->orWhereIn('role', [User::ROLE_OWNER, User::ROLE_CASHIER]);
            })
            ->where(function ($query): void {
                $query->whereNull('is_active')
                    ->orWhere('is_active', true);
            })
            ->orderByRaw("CASE WHEN role = 'owner' OR role IS NULL THEN 0 WHEN role = 'cashier' THEN 1 ELSE 2 END")
            ->orderBy('id')
            ->first(['id']);

        return (int) ($user?->id ?? 0);
    }

    private function progressPayload(int $gymId, int $clientId, string $gymTimezone = '', string $gymSlug = ''): array
    {
        $timezone = $this->resolveTimezone($gymTimezone);
        $nowAtGym = Carbon::now($timezone);
        $today = $nowAtGym->toDateString();
        $monthStart = $nowAtGym->copy()->startOfMonth()->toDateString();
        $monthEnd = $nowAtGym->copy()->endOfMonth()->toDateString();

        $latestMembership = Membership::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('ends_at')
            ->orderByDesc('id')
            ->first(['id', 'starts_at', 'ends_at', 'status']);

        $monthVisits = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();

        $monthAttendances = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->get(['date', 'time']);

        $recentAttendances = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->whereBetween('date', [$nowAtGym->copy()->subDays(45)->toDateString(), $today])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->get(['date', 'time']);

        $totalVisits = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->count();

        $liveClientsCount = PresenceSession::query()
            ->where('gym_id', $gymId)
            ->open()
            ->count();

        $lastAttendance = Attendance::query()
            ->forGym($gymId)
            ->where('client_id', $clientId)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first(['date', 'time']);

        $periodVisits = 0;
        $periodWindowLabel = 'Sin membresía activa';
        if (
            $latestMembership?->starts_at
            && $latestMembership?->ends_at
            && (string) ($latestMembership->status ?? '') === 'active'
        ) {
            $periodStart = Carbon::parse((string) $latestMembership->starts_at, $timezone)->startOfDay();
            $periodEnd = Carbon::parse((string) $latestMembership->ends_at, $timezone)->endOfDay();

            if ($nowAtGym->between($periodStart, $periodEnd, true)) {
                $periodVisits = Attendance::query()
                    ->forGym($gymId)
                    ->where('client_id', $clientId)
                    ->whereBetween('date', [$periodStart->toDateString(), $nowAtGym->toDateString()])
                    ->count();
            }

            $periodWindowLabel = $periodStart->toDateString().' al '.$periodEnd->toDateString();
        }

        $daysRemaining = null;
        if ($latestMembership?->ends_at) {
            $daysRemaining = max(0, Carbon::parse((string) $latestMembership->ends_at, $timezone)->startOfDay()->diffInDays($nowAtGym->copy()->startOfDay(), false) * -1);
        }

        $monthNameMap = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        $monthIndex = (int) $nowAtGym->format('n');
        $monthLabel = ucfirst((string) ($monthNameMap[$monthIndex] ?? $nowAtGym->format('F'))).' '.$nowAtGym->format('Y');

        $monthEntries = [];
        foreach ($monthAttendances as $attendance) {
            $dateValue = $attendance->date?->toDateString();
            if (! is_string($dateValue) || trim($dateValue) === '') {
                continue;
            }

            $monthEntries[] = [
                'date' => $dateValue,
                'time' => trim((string) ($attendance->time ?? '')),
            ];
        }

        $fitnessProfile = $this->resolveFitnessProfile($gymId, $clientId);
        $progressPrediction = $this->buildProgressPrediction(
            fitnessProfile: $fitnessProfile,
            monthVisits: (int) $monthVisits,
            periodVisits: (int) $periodVisits,
            totalVisits: (int) $totalVisits,
        );
        $bodyState = $this->buildBodyState(
            fitnessProfile: $fitnessProfile,
            recentAttendances: $recentAttendances->all(),
            monthVisits: (int) $monthVisits,
            nowAtGym: $nowAtGym,
        );
        $trainingPlan = $this->buildAutoTrainingPlan(
            fitnessProfile: $fitnessProfile,
            recentAttendances: $recentAttendances->all(),
            nowAtGym: $nowAtGym,
        );
        $personalMessage = $this->buildPersonalMessage(
            fitnessProfile: $fitnessProfile,
            bodyState: $bodyState,
            monthVisits: (int) $monthVisits,
            nowAtGym: $nowAtGym,
        );
        $weeklyGoalSummary = $this->buildWeeklyGoalSummary(
            fitnessProfile: $fitnessProfile,
            recentAttendances: $recentAttendances->all(),
            nowAtGym: $nowAtGym,
            bodyState: $bodyState,
        );
        $last30Timeline = $this->buildLast30Timeline(
            recentAttendances: $recentAttendances->all(),
            nowAtGym: $nowAtGym,
        );

        $this->dispatchWeeklyGoalPushNotifications(
            gymId: $gymId,
            clientId: $clientId,
            gymSlug: $gymSlug,
            weeklyGoalSummary: $weeklyGoalSummary,
            nowAtGym: $nowAtGym
        );

        return [
            'membership_status' => (string) ($latestMembership?->status ?? 'inactive'),
            'membership_ends_at' => $latestMembership?->ends_at?->toDateString(),
            'days_remaining' => $daysRemaining,
            'month_visits' => (int) $monthVisits,
            'month_label' => $monthLabel,
            'month_entries' => $monthEntries,
            'total_visits' => (int) $totalVisits,
            'period_visits' => (int) $periodVisits,
            'period_window_label' => $periodWindowLabel,
            'live_clients_count' => (int) $liveClientsCount,
            'live_window_label' => 'En vivo',
            'last_attendance_date' => $lastAttendance?->date?->toDateString(),
            'last_attendance_time' => (string) ($lastAttendance?->time ?? ''),
            'prediction' => $progressPrediction,
            'body_state' => $bodyState,
            'training_plan' => $trainingPlan,
            'personal_message' => $personalMessage,
            'weekly_goal' => $weeklyGoalSummary,
            'last30_timeline' => $last30Timeline,
            'today' => $today,
        ];
    }

    private function buildProgressPrediction(
        ?ClientFitnessProfile $fitnessProfile,
        int $monthVisits,
        int $periodVisits,
        int $totalVisits
    ): array {
        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'rhythm_label' => 'Sin datos',
                'consistency_percent' => 0,
                'primary_line' => 'Completa tus datos fisicos para activar tu prediccion.',
                'secondary_line' => 'Cuando registres asistencias veremos tu proyeccion de 30 dias.',
                'context_line' => 'Aun no existe perfil fisico asociado.',
            ];
        }

        $goal = trim((string) ($fitnessProfile->goal ?? ''));
        $experienceLevel = trim((string) ($fitnessProfile->experience_level ?? ''));
        $daysPerWeek = max(1, min(7, (int) ($fitnessProfile->days_per_week ?? 3)));

        $expectedVisitsMonth = max(1, (int) round($daysPerWeek * 4.3));
        $adherenceRatio = $monthVisits / $expectedVisitsMonth;
        $adherenceRatio = max(0.0, min(1.4, $adherenceRatio));
        $consistencyPercent = (int) round(min(100, $adherenceRatio * 100));

        $experienceMultiplier = match ($experienceLevel) {
            'principiante' => 1.10,
            'intermedio' => 1.0,
            'avanzado' => 0.88,
            default => 1.0,
        };

        $strengthGainPct = (4 + ($adherenceRatio * 6.5) + (($daysPerWeek - 3) * 0.7)) * $experienceMultiplier;
        $strengthGainPct = max(2.5, min(14.5, $strengthGainPct));

        $resistanceGainPct = 5 + ($adherenceRatio * 7.4) + (($daysPerWeek - 3) * 0.5);
        $resistanceGainPct = max(3.0, min(16.0, $resistanceGainPct));

        $rhythmLabel = 'Ritmo bajo';
        if ($adherenceRatio >= 0.95) {
            $rhythmLabel = 'Ritmo alto';
        } elseif ($adherenceRatio >= 0.7) {
            $rhythmLabel = 'Ritmo medio';
        }

        $primaryLine = '';
        $secondaryLine = '';

        switch ($goal) {
            case 'perder_grasa':
                $fatLossKg = 0.7 + ($adherenceRatio * 2.0);
                $fatLossKg = max(0.5, min(3.6, $fatLossKg));

                $primaryLine = 'Podrias perder '.number_format($fatLossKg, 1, '.', '').' kg de grasa en 30 dias.';
                $secondaryLine = 'Tambien podrias mejorar +'.(int) round($strengthGainPct).'% tu fuerza base.';
                break;
            case 'ganar_musculo':
                $muscleGainKg = 0.25 + ($adherenceRatio * 0.75 * $experienceMultiplier);
                $muscleGainKg = max(0.2, min(1.4, $muscleGainKg));

                $primaryLine = 'Podrias ganar '.number_format($muscleGainKg, 1, '.', '').' kg de masa muscular en 30 dias.';
                $secondaryLine = 'Tu fuerza podria subir +'.(int) round($strengthGainPct).'% en ejercicios principales.';
                break;
            case 'mantener_forma':
                $variationKg = max(0.3, min(0.8, 0.9 - ($adherenceRatio * 0.45)));

                $primaryLine = 'Podrias mantener tu peso dentro de ±'.number_format($variationKg, 1, '.', '').' kg en 30 dias.';
                $secondaryLine = 'Tu resistencia podria mejorar +'.(int) round($resistanceGainPct).'% con este ritmo.';
                break;
            case 'definir':
                $fatLossKg = 0.5 + ($adherenceRatio * 1.3);
                $fatLossKg = max(0.4, min(2.4, $fatLossKg));

                $primaryLine = 'Podrias bajar '.number_format($fatLossKg, 1, '.', '').' kg de grasa manteniendo masa muscular.';
                $secondaryLine = 'Se proyecta +'.(int) round($strengthGainPct * 0.8).'% en fuerza y control tecnico.';
                break;
            case 'aumentar_fuerza':
                $primaryLine = 'Podrias aumentar +'.(int) round($strengthGainPct + 1.5).'% tu fuerza en 30 dias.';
                $secondaryLine = 'Tu capacidad de trabajo podria subir +'.(int) round($resistanceGainPct * 0.7).'%.';
                break;
            case 'mejorar_resistencia':
                $primaryLine = 'Podrias mejorar +'.(int) round($resistanceGainPct + 1.2).'% tu resistencia en 30 dias.';
                $secondaryLine = 'Con eso podrias sostener +'.(int) round($strengthGainPct * 0.6).'% de volumen efectivo.';
                break;
            default:
                $primaryLine = 'Manteniendo este ritmo, tu condicion fisica puede mejorar en 30 dias.';
                $secondaryLine = 'Tu fuerza podria subir alrededor de +'.(int) round($strengthGainPct).'%';
                break;
        }

        $goalLabels = [
            'ganar_musculo' => 'Ganar musculo',
            'perder_grasa' => 'Perder grasa',
            'mantener_forma' => 'Mantener forma',
            'definir' => 'Definir',
            'aumentar_fuerza' => 'Aumentar fuerza',
            'mejorar_resistencia' => 'Mejorar resistencia',
        ];
        $goalLabel = $goalLabels[$goal] ?? 'General';

        return [
            'ready' => true,
            'rhythm_label' => $rhythmLabel,
            'consistency_percent' => $consistencyPercent,
            'primary_line' => $primaryLine,
            'secondary_line' => $secondaryLine,
            'context_line' => 'Objetivo: '.$goalLabel.' | Visitas del mes: '.$monthVisits.' de '.$expectedVisitsMonth.' esperadas.',
            'month_visits' => $monthVisits,
            'period_visits' => $periodVisits,
            'total_visits' => $totalVisits,
        ];
    }

    /**
     * @param array<int, mixed> $recentAttendances
     * @return array<string, mixed>
     */
    private function buildBodyState(
        ?ClientFitnessProfile $fitnessProfile,
        array $recentAttendances,
        int $monthVisits,
        Carbon $nowAtGym
    ): array {
        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'force' => 0,
                'resistance' => 0,
                'discipline' => 0,
                'recovery' => 0,
                'streak_days' => 0,
                'week_visits' => 0,
                'days_since_last' => null,
                'summary_line' => 'Completa tus datos fisicos para activar este estado.',
                'context_line' => 'Se calcula con racha, descanso e intensidad semanal.',
            ];
        }

        $attendanceDates = collect($recentAttendances)
            ->map(static function ($attendance): string {
                $dateValue = '';
                if (is_object($attendance) && isset($attendance->date) && $attendance->date !== null) {
                    $rawDate = $attendance->date;
                    if (is_object($rawDate) && method_exists($rawDate, 'toDateString')) {
                        $dateValue = trim((string) $rawDate->toDateString());
                    } else {
                        $dateValue = trim((string) $rawDate);
                    }
                } elseif (is_array($attendance)) {
                    $dateValue = trim((string) ($attendance['date'] ?? ''));
                }

                return $dateValue;
            })
            ->filter(static fn (string $date): bool => $date !== '')
            ->unique()
            ->sort()
            ->values();

        $attendanceDateSet = $attendanceDates->flip();
        $today = $nowAtGym->copy()->startOfDay();
        $todayKey = $today->toDateString();
        $yesterdayKey = $today->copy()->subDay()->toDateString();

        $streakCursor = null;
        if ($attendanceDateSet->has($todayKey)) {
            $streakCursor = $today->copy();
        } elseif ($attendanceDateSet->has($yesterdayKey)) {
            $streakCursor = $today->copy()->subDay();
        }

        $streakDays = 0;
        if ($streakCursor instanceof Carbon) {
            while ($attendanceDateSet->has($streakCursor->toDateString())) {
                $streakDays++;
                $streakCursor->subDay();
            }
        }

        $lastDateString = (string) ($attendanceDates->last() ?? '');
        $daysSinceLast = null;
        if ($lastDateString !== '') {
            $daysSinceLast = Carbon::parse($lastDateString, $nowAtGym->getTimezone())
                ->startOfDay()
                ->diffInDays($today, false);
            $daysSinceLast = max(0, (int) $daysSinceLast);
        }

        $weekStart = $today->copy()->subDays(6)->toDateString();
        $weekVisits = (int) $attendanceDates
            ->filter(static fn (string $date): bool => $date >= $weekStart)
            ->count();

        $daysPerWeek = max(1, min(7, (int) ($fitnessProfile->days_per_week ?? 3)));
        $sessionMinutes = max(30, min(180, (int) ($fitnessProfile->session_minutes ?? 60)));
        $goal = trim((string) ($fitnessProfile->goal ?? ''));
        $experienceLevel = trim((string) ($fitnessProfile->experience_level ?? ''));

        $expectedMonthVisits = max(1, (int) round($daysPerWeek * 4.3));
        $monthAdherence = max(0.0, min(1.4, $monthVisits / $expectedMonthVisits));
        $weekConsistency = max(0.0, min(1.5, $weekVisits / $daysPerWeek));

        $intensityBase = match ($sessionMinutes) {
            90 => 74,
            60 => 58,
            default => 45,
        };
        $goalIntensityBonus = match ($goal) {
            'ganar_musculo', 'aumentar_fuerza' => 6,
            'mejorar_resistencia' => 5,
            'perder_grasa', 'definir' => 4,
            default => 2,
        };
        $trainingLoad = $intensityBase + ($weekVisits * 6) + $goalIntensityBonus;
        $trainingLoad = (int) round(max(15, min(98, $trainingLoad)));

        $experienceBonus = match ($experienceLevel) {
            'principiante' => 4,
            'intermedio' => 6,
            'avanzado' => 8,
            default => 5,
        };
        $goalStrengthBonus = match ($goal) {
            'aumentar_fuerza' => 12,
            'ganar_musculo' => 10,
            'definir' => 6,
            'perder_grasa' => 4,
            'mejorar_resistencia' => 3,
            default => 4,
        };

        $force = 30 + ($trainingLoad * 0.35) + ($monthAdherence * 22) + ($streakDays * 2) + $goalStrengthBonus + $experienceBonus;
        $force = (int) round(max(12, min(98, $force)));

        $goalResistanceBonus = match ($goal) {
            'mejorar_resistencia' => 12,
            'perder_grasa', 'definir' => 8,
            'mantener_forma' => 6,
            default => 4,
        };
        $resistance = 26 + ($weekVisits * 8) + ($sessionMinutes * 0.24) + ($monthAdherence * 16) + $goalResistanceBonus;
        $resistance = (int) round(max(12, min(98, $resistance)));

        $discipline = 24 + ($streakDays * 8) + ($weekConsistency * 34) + ($monthAdherence * 20);
        $discipline = (int) round(max(8, min(99, $discipline)));

        $restDaysWeek = max(0, 7 - $weekVisits);
        $overloadDays = max(0, $weekVisits - $daysPerWeek);
        $recovery = 52 + ($restDaysWeek * 6) - ($overloadDays * 8) - (max(0, $trainingLoad - 75) * 0.45);
        if ($daysSinceLast !== null && $daysSinceLast >= 2) {
            $recovery += 8;
        }
        if ($daysSinceLast === 0 && $weekVisits >= ($daysPerWeek + 1)) {
            $recovery -= 6;
        }
        $recovery = (int) round(max(10, min(98, $recovery)));

        $summaryLine = 'Racha actual: '.$streakDays.' dias | Semana: '.$weekVisits.' entrenamientos.';
        $contextLine = 'Calculado con descanso, intensidad y constancia de los ultimos 45 dias.';
        if ($daysSinceLast !== null) {
            $contextLine = $contextLine.' Ultimo entrenamiento: hace '.$daysSinceLast.' dias.';
        }

        return [
            'ready' => true,
            'force' => $force,
            'resistance' => $resistance,
            'discipline' => $discipline,
            'recovery' => $recovery,
            'streak_days' => $streakDays,
            'week_visits' => $weekVisits,
            'days_since_last' => $daysSinceLast,
            'summary_line' => $summaryLine,
            'context_line' => $contextLine,
        ];
    }

    /**
     * @param array<int, mixed> $recentAttendances
     * @return array<string, mixed>
     */
    private function buildAutoTrainingPlan(
        ?ClientFitnessProfile $fitnessProfile,
        array $recentAttendances,
        Carbon $nowAtGym
    ): array {
        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'title' => 'Entrenamiento de hoy',
                'objective_line' => 'Completa tus datos fisicos para crear tu rutina.',
                'focus_line' => 'Aun no hay enfoque disponible.',
                'rhythm_line' => 'Sin frecuencia configurada.',
                'adaptation_line' => 'Cuando completes tu perfil activaremos tu rutina automatica.',
                'context_line' => 'Plan basado en objetivo, nivel y frecuencia semanal.',
                'exercises' => [],
            ];
        }

        $goal = trim((string) ($fitnessProfile->goal ?? ''));
        $experienceLevel = trim((string) ($fitnessProfile->experience_level ?? ''));
        $daysPerWeek = max(1, min(7, (int) ($fitnessProfile->days_per_week ?? 3)));
        $sessionMinutes = max(45, min(90, (int) ($fitnessProfile->session_minutes ?? 60)));
        $limitations = is_array($fitnessProfile->limitations ?? null)
            ? array_values(array_unique(array_filter(array_map(
                static fn ($item): string => mb_strtolower(trim((string) $item)),
                $fitnessProfile->limitations
            ), static fn (string $value): bool => $value !== '' && $value !== 'ninguna')))
            : [];

        $today = $nowAtGym->copy()->startOfDay();
        $weekStart = $today->copy()->subDays(6)->toDateString();
        $attendanceDates = collect($recentAttendances)
            ->map(static function ($attendance): string {
                if (is_object($attendance) && isset($attendance->date) && $attendance->date !== null) {
                    $rawDate = $attendance->date;
                    if (is_object($rawDate) && method_exists($rawDate, 'toDateString')) {
                        return trim((string) $rawDate->toDateString());
                    }

                    return trim((string) $rawDate);
                }
                if (is_array($attendance)) {
                    return trim((string) ($attendance['date'] ?? ''));
                }

                return '';
            })
            ->filter(static fn (string $date): bool => $date !== '')
            ->unique()
            ->values();
        $weekVisits = (int) $attendanceDates
            ->filter(static fn (string $date): bool => $date >= $weekStart)
            ->count();

        $routineMap = [
            'ganar_musculo' => [
                ['focus' => 'Pecho y triceps', 'exercises' => [
                    ['name' => 'Press banca', 'prescription' => '4 x 8'],
                    ['name' => 'Aperturas en maquina', 'prescription' => '3 x 12'],
                    ['name' => 'Fondos asistidos', 'prescription' => '3 x 10'],
                    ['name' => 'Extension de triceps en polea', 'prescription' => '3 x 12'],
                ]],
                ['focus' => 'Espalda y biceps', 'exercises' => [
                    ['name' => 'Jalon al pecho', 'prescription' => '4 x 10'],
                    ['name' => 'Remo con barra', 'prescription' => '3 x 10'],
                    ['name' => 'Curl con barra', 'prescription' => '3 x 12'],
                    ['name' => 'Curl martillo', 'prescription' => '3 x 10'],
                ]],
                ['focus' => 'Piernas y core', 'exercises' => [
                    ['name' => 'Sentadilla goblet', 'prescription' => '4 x 10'],
                    ['name' => 'Prensa inclinada', 'prescription' => '3 x 12'],
                    ['name' => 'Peso muerto rumano', 'prescription' => '3 x 10'],
                    ['name' => 'Plancha abdominal', 'prescription' => '3 x 40 seg'],
                ]],
            ],
            'perder_grasa' => [
                ['focus' => 'Circuito metabolico A', 'exercises' => [
                    ['name' => 'Sentadilla goblet', 'prescription' => '4 x 12'],
                    ['name' => 'Remo en polea', 'prescription' => '4 x 12'],
                    ['name' => 'Press inclinado con mancuernas', 'prescription' => '3 x 12'],
                    ['name' => 'Bicicleta estatica', 'prescription' => '12 min'],
                ]],
                ['focus' => 'Circuito metabolico B', 'exercises' => [
                    ['name' => 'Peso muerto rumano', 'prescription' => '4 x 10'],
                    ['name' => 'Desplantes alternos', 'prescription' => '3 x 12'],
                    ['name' => 'Press militar', 'prescription' => '3 x 10'],
                    ['name' => 'Caminata inclinada', 'prescription' => '15 min'],
                ]],
                ['focus' => 'Full body controlado', 'exercises' => [
                    ['name' => 'Prensa inclinada', 'prescription' => '4 x 12'],
                    ['name' => 'Jalon al pecho', 'prescription' => '4 x 12'],
                    ['name' => 'Fondos asistidos', 'prescription' => '3 x 10'],
                    ['name' => 'Plancha abdominal', 'prescription' => '3 x 45 seg'],
                ]],
            ],
            'mantener_forma' => [
                ['focus' => 'Fuerza general', 'exercises' => [
                    ['name' => 'Prensa inclinada', 'prescription' => '3 x 10'],
                    ['name' => 'Remo en polea', 'prescription' => '3 x 10'],
                    ['name' => 'Press plano con mancuernas', 'prescription' => '3 x 10'],
                    ['name' => 'Caminata inclinada', 'prescription' => '12 min'],
                ]],
                ['focus' => 'Control y movilidad', 'exercises' => [
                    ['name' => 'Sentadilla goblet', 'prescription' => '3 x 12'],
                    ['name' => 'Jalon al pecho', 'prescription' => '3 x 12'],
                    ['name' => 'Elevaciones laterales', 'prescription' => '3 x 15'],
                    ['name' => 'Plancha abdominal', 'prescription' => '3 x 40 seg'],
                ]],
                ['focus' => 'Resistencia moderada', 'exercises' => [
                    ['name' => 'Peso muerto rumano', 'prescription' => '3 x 10'],
                    ['name' => 'Press inclinado con mancuernas', 'prescription' => '3 x 12'],
                    ['name' => 'Curl martillo', 'prescription' => '3 x 12'],
                    ['name' => 'Bicicleta estatica', 'prescription' => '15 min'],
                ]],
            ],
            'definir' => [
                ['focus' => 'Tono superior', 'exercises' => [
                    ['name' => 'Press banca', 'prescription' => '4 x 10'],
                    ['name' => 'Remo con barra', 'prescription' => '4 x 10'],
                    ['name' => 'Elevaciones laterales', 'prescription' => '3 x 15'],
                    ['name' => 'Ab wheel o crunch', 'prescription' => '3 x 15'],
                ]],
                ['focus' => 'Pierna y core', 'exercises' => [
                    ['name' => 'Prensa inclinada', 'prescription' => '4 x 12'],
                    ['name' => 'Peso muerto rumano', 'prescription' => '3 x 12'],
                    ['name' => 'Desplantes alternos', 'prescription' => '3 x 12'],
                    ['name' => 'Plancha abdominal', 'prescription' => '4 x 40 seg'],
                ]],
                ['focus' => 'Mixto de volumen', 'exercises' => [
                    ['name' => 'Jalon al pecho', 'prescription' => '4 x 12'],
                    ['name' => 'Press inclinado con mancuernas', 'prescription' => '4 x 10'],
                    ['name' => 'Fondos asistidos', 'prescription' => '3 x 12'],
                    ['name' => 'Caminata inclinada', 'prescription' => '12 min'],
                ]],
            ],
            'aumentar_fuerza' => [
                ['focus' => 'Empuje pesado', 'exercises' => [
                    ['name' => 'Press banca', 'prescription' => '5 x 5'],
                    ['name' => 'Press militar', 'prescription' => '4 x 6'],
                    ['name' => 'Fondos asistidos', 'prescription' => '4 x 8'],
                    ['name' => 'Plancha abdominal', 'prescription' => '3 x 50 seg'],
                ]],
                ['focus' => 'Tiron pesado', 'exercises' => [
                    ['name' => 'Peso muerto rumano', 'prescription' => '5 x 5'],
                    ['name' => 'Remo con barra', 'prescription' => '4 x 6'],
                    ['name' => 'Jalon al pecho', 'prescription' => '4 x 8'],
                    ['name' => 'Curl con barra', 'prescription' => '3 x 8'],
                ]],
                ['focus' => 'Pierna fuerte', 'exercises' => [
                    ['name' => 'Sentadilla goblet', 'prescription' => '5 x 6'],
                    ['name' => 'Prensa inclinada', 'prescription' => '4 x 8'],
                    ['name' => 'Hip thrust', 'prescription' => '4 x 8'],
                    ['name' => 'Farmer walk', 'prescription' => '4 x 40 m'],
                ]],
            ],
            'mejorar_resistencia' => [
                ['focus' => 'Capacidad aerobica', 'exercises' => [
                    ['name' => 'Bicicleta estatica', 'prescription' => '18 min'],
                    ['name' => 'Remo en polea', 'prescription' => '4 x 15'],
                    ['name' => 'Sentadilla goblet', 'prescription' => '4 x 15'],
                    ['name' => 'Plancha abdominal', 'prescription' => '3 x 45 seg'],
                ]],
                ['focus' => 'Circuito continuo', 'exercises' => [
                    ['name' => 'Caminata inclinada', 'prescription' => '20 min'],
                    ['name' => 'Press inclinado con mancuernas', 'prescription' => '4 x 12'],
                    ['name' => 'Jalon al pecho', 'prescription' => '4 x 12'],
                    ['name' => 'Desplantes alternos', 'prescription' => '3 x 14'],
                ]],
                ['focus' => 'Base de trabajo', 'exercises' => [
                    ['name' => 'Eliptica', 'prescription' => '16 min'],
                    ['name' => 'Prensa inclinada', 'prescription' => '4 x 14'],
                    ['name' => 'Press militar', 'prescription' => '3 x 12'],
                    ['name' => 'Ab wheel o crunch', 'prescription' => '3 x 15'],
                ]],
            ],
        ];

        $goalKey = array_key_exists($goal, $routineMap) ? $goal : 'mantener_forma';
        $goalTemplates = $routineMap[$goalKey];
        $templateIndex = ((int) $nowAtGym->format('z') + $weekVisits) % max(1, count($goalTemplates));
        $selectedTemplate = $goalTemplates[$templateIndex] ?? $goalTemplates[0];

        $rawExercises = is_array($selectedTemplate['exercises'] ?? null) ? $selectedTemplate['exercises'] : [];
        $adjustedExercises = [];
        foreach ($rawExercises as $exercise) {
            $name = trim((string) ($exercise['name'] ?? 'Ejercicio'));
            $prescription = trim((string) ($exercise['prescription'] ?? '3 x 10'));

            if (in_array('rodilla', $limitations, true)) {
                if ($name === 'Sentadilla goblet') {
                    $name = 'Prensa inclinada';
                } elseif ($name === 'Desplantes alternos') {
                    $name = 'Curl femoral en maquina';
                }
            }
            if (in_array('espalda', $limitations, true)) {
                if ($name === 'Peso muerto rumano') {
                    $name = 'Hip thrust en maquina';
                } elseif ($name === 'Remo con barra') {
                    $name = 'Remo en polea sentado';
                }
            }
            if (in_array('hombro', $limitations, true) && $name === 'Press militar') {
                $name = 'Elevaciones laterales';
            }
            if (in_array('codo', $limitations, true) && $name === 'Fondos asistidos') {
                $name = 'Extension de triceps con cuerda';
            }
            if (in_array('tobillo', $limitations, true) && $name === 'Farmer walk') {
                $name = 'Caminata en banda';
            }

            if (preg_match('/^(\d+)\s*x\s*(\d+)(.*)$/', $prescription, $matches) === 1) {
                $sets = (int) $matches[1];
                $reps = (int) $matches[2];
                $suffix = trim((string) ($matches[3] ?? ''));

                if ($experienceLevel === 'principiante') {
                    $sets = max(2, $sets - 1);
                    $reps = max(6, $reps - 1);
                } elseif ($experienceLevel === 'avanzado') {
                    $sets = min(6, $sets + 1);
                    $reps = min(18, $reps + 1);
                }

                $prescription = $sets.' x '.$reps.($suffix !== '' ? ' '.$suffix : '');
            }

            $adjustedExercises[] = [
                'name' => $name,
                'prescription' => $prescription,
            ];
        }

        $goalLabels = [
            'ganar_musculo' => 'Ganar musculo',
            'perder_grasa' => 'Perder grasa',
            'mantener_forma' => 'Mantener forma',
            'definir' => 'Definir',
            'aumentar_fuerza' => 'Aumentar fuerza',
            'mejorar_resistencia' => 'Mejorar resistencia',
        ];
        $goalLabel = $goalLabels[$goalKey] ?? 'Acondicionamiento general';

        $adaptationLine = 'Semana equilibrada. Mantiene este volumen.';
        if ($weekVisits < max(1, $daysPerWeek - 1)) {
            $adaptationLine = 'Vas por debajo de tu frecuencia. Intenta sumar una sesion extra.';
        } elseif ($weekVisits > $daysPerWeek) {
            $adaptationLine = 'Semana intensa. Prioriza descanso y tecnica limpia.';
        }

        $limitationsLine = $limitations === []
            ? 'Sin limitaciones reportadas.'
            : 'Ajustada por limitaciones: '.implode(', ', $limitations).'.';

        $estimatedMinutes = min(95, max(35, 10 + (count($adjustedExercises) * 11)));

        return [
            'ready' => true,
            'title' => 'Entrenamiento de hoy',
            'objective_line' => 'Objetivo: '.$goalLabel,
            'focus_line' => 'Enfoque: '.(string) ($selectedTemplate['focus'] ?? 'General'),
            'rhythm_line' => 'Frecuencia: '.$daysPerWeek.' dias/semana | Sesion sugerida: '.$sessionMinutes.' min | Estimada: '.$estimatedMinutes.' min',
            'adaptation_line' => $adaptationLine,
            'context_line' => $limitationsLine,
            'exercises' => $adjustedExercises,
            'week_visits' => $weekVisits,
        ];
    }

    /**
     * @param array<string, mixed> $bodyState
     * @return array<string, mixed>
     */
    private function buildPersonalMessage(
        ?ClientFitnessProfile $fitnessProfile,
        array $bodyState,
        int $monthVisits,
        Carbon $nowAtGym
    ): array {
        if (! $fitnessProfile) {
            return [
                'ready' => false,
                'tag' => 'Mensaje personal',
                'line_1' => 'Completa tus datos fisicos para activar tus recomendaciones.',
                'line_2' => 'Con tu perfil listo veremos metas, rachas y alertas utiles.',
                'context_line' => 'Se personaliza segun objetivo, ritmo y recuperacion.',
            ];
        }

        $goal = trim((string) ($fitnessProfile->goal ?? ''));
        $daysPerWeek = max(1, min(7, (int) ($fitnessProfile->days_per_week ?? 3)));
        $streakDays = max(0, (int) ($bodyState['streak_days'] ?? 0));
        $weekVisits = max(0, (int) ($bodyState['week_visits'] ?? 0));
        $daysSinceLast = isset($bodyState['days_since_last']) ? (int) $bodyState['days_since_last'] : null;
        $recoveryScore = max(0, min(100, (int) ($bodyState['recovery'] ?? 0)));

        $goalLabels = [
            'ganar_musculo' => 'ganar musculo',
            'perder_grasa' => 'perder grasa',
            'mantener_forma' => 'mantener forma',
            'definir' => 'definir',
            'aumentar_fuerza' => 'aumentar fuerza',
            'mejorar_resistencia' => 'mejorar resistencia',
        ];
        $goalLabel = $goalLabels[$goal] ?? 'progresar en el gimnasio';

        $line1 = '';
        $line2 = '';

        if ($streakDays >= 3 && $daysSinceLast !== null && $daysSinceLast <= 1) {
            $line1 = 'Si entrenas hoy, mantendras tu racha de '.$streakDays.' dias.';
            $line2 = 'No la rompas. Estas cerca de un nuevo record personal.';
        } elseif ($weekVisits < $daysPerWeek) {
            $missing = max(0, $daysPerWeek - $weekVisits);
            $line1 = 'Te faltan '.$missing.' entrenamientos para cumplir tu meta semanal.';
            $line2 = 'Tu objetivo es '.$goalLabel.'. Este es buen momento para avanzar.';
        } elseif ($daysSinceLast !== null && $daysSinceLast >= 3) {
            $line1 = 'Llevas '.$daysSinceLast.' dias sin entrenar.';
            $line2 = 'Vuelve hoy con una sesion corta para retomar el ritmo.';
        } else {
            $line1 = 'Vas bien: '.$weekVisits.' sesiones esta semana para '.$goalLabel.'.';
            $line2 = 'Manten constancia para consolidar resultados en los proximos dias.';
        }

        $contextLine = 'Mes actual: '.$monthVisits.' visitas | Recuperacion estimada: '.$recoveryScore.'/100.';
        if ((int) $nowAtGym->format('N') >= 6) {
            $contextLine .= ' Fin de semana ideal para tecnica y movilidad.';
        }

        return [
            'ready' => true,
            'tag' => 'Mensaje personal',
            'line_1' => $line1,
            'line_2' => $line2,
            'context_line' => $contextLine,
        ];
    }

    /**
     * @param array<int, mixed> $recentAttendances
     * @param array<string, mixed> $bodyState
     * @return array<string, mixed>
     */
    private function buildWeeklyGoalSummary(
        ?ClientFitnessProfile $fitnessProfile,
        array $recentAttendances,
        Carbon $nowAtGym,
        array $bodyState = []
    ): array {
        $attendanceDates = collect($recentAttendances)
            ->map(static function ($attendance): string {
                if (is_object($attendance) && isset($attendance->date) && $attendance->date !== null) {
                    $rawDate = $attendance->date;
                    if (is_object($rawDate) && method_exists($rawDate, 'toDateString')) {
                        return trim((string) $rawDate->toDateString());
                    }

                    return trim((string) $rawDate);
                }

                if (is_array($attendance)) {
                    return trim((string) ($attendance['date'] ?? ''));
                }

                return '';
            })
            ->filter(static fn (string $date): bool => $date !== '')
            ->unique()
            ->values();

        $weekStart = $nowAtGym->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = $nowAtGym->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();
        $weekVisits = (int) $attendanceDates
            ->filter(static fn (string $date): bool => $date >= $weekStart && $date <= $weekEnd)
            ->count();

        $weeklyGoal = max(3, min(7, (int) ($fitnessProfile?->days_per_week ?? 3)));
        $remaining = max(0, $weeklyGoal - $weekVisits);
        $completionPercent = (int) round(min(100, ($weekVisits / max(1, $weeklyGoal)) * 100));
        $daysLeftWeek = (int) $nowAtGym->copy()->startOfDay()->diffInDays($nowAtGym->copy()->endOfWeek(Carbon::SUNDAY)->startOfDay());

        $alerts = [];
        if ($weekVisits >= $weeklyGoal) {
            $alerts[] = [
                'type' => 'success',
                'text' => 'Meta semanal completada. Excelente consistencia.',
            ];
        } elseif ($remaining === 1) {
            $alerts[] = [
                'type' => 'warning',
                'text' => 'Te falta 1 sesion para cumplir tu meta semanal.',
            ];
        } else {
            $alerts[] = [
                'type' => 'info',
                'text' => 'Aun te faltan '.$remaining.' sesiones para completar la semana.',
            ];
        }

        $streakDays = max(0, (int) ($bodyState['streak_days'] ?? 0));
        $daysSinceLast = isset($bodyState['days_since_last']) ? (int) $bodyState['days_since_last'] : null;
        if ($streakDays >= 2 && $daysSinceLast !== null && $daysSinceLast >= 2) {
            $alerts[] = [
                'type' => 'danger',
                'text' => 'Racha en riesgo: llevas '.$daysSinceLast.' dias sin entrenar.',
            ];
        }

        $recoveryScore = max(0, min(100, (int) ($bodyState['recovery'] ?? 0)));
        if ($recoveryScore < 45) {
            $alerts[] = [
                'type' => 'warning',
                'text' => 'Recuperacion baja. Prioriza descanso activo y tecnica.',
            ];
        }

        if (count($alerts) > 3) {
            $alerts = array_slice($alerts, 0, 3);
        }

        return [
            'target' => $weeklyGoal,
            'visits' => $weekVisits,
            'remaining' => $remaining,
            'completion_percent' => $completionPercent,
            'days_left_week' => $daysLeftWeek,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'alerts' => $alerts,
        ];
    }

    /**
     * @param array<int, mixed> $recentAttendances
     * @return array<int, array<string, mixed>>
     */
    private function buildLast30Timeline(array $recentAttendances, Carbon $nowAtGym): array
    {
        $attendanceDateSet = collect($recentAttendances)
            ->map(static function ($attendance): string {
                if (is_object($attendance) && isset($attendance->date) && $attendance->date !== null) {
                    $rawDate = $attendance->date;
                    if (is_object($rawDate) && method_exists($rawDate, 'toDateString')) {
                        return trim((string) $rawDate->toDateString());
                    }

                    return trim((string) $rawDate);
                }

                if (is_array($attendance)) {
                    return trim((string) ($attendance['date'] ?? ''));
                }

                return '';
            })
            ->filter(static fn (string $date): bool => $date !== '')
            ->unique()
            ->flip();

        $timeline = [];
        $dayLabels = [
            1 => 'Lun',
            2 => 'Mar',
            3 => 'Mie',
            4 => 'Jue',
            5 => 'Vie',
            6 => 'Sab',
            7 => 'Dom',
        ];

        for ($offset = 29; $offset >= 0; $offset--) {
            $date = $nowAtGym->copy()->subDays($offset)->startOfDay();
            $dateString = $date->toDateString();
            $dayOfWeek = (int) $date->format('N');

            $timeline[] = [
                'date' => $dateString,
                'label' => (string) $date->format('j'),
                'weekday_short' => $dayLabels[$dayOfWeek] ?? '',
                'attended' => $attendanceDateSet->has($dateString),
                'is_today' => $offset === 0,
            ];
        }

        return $timeline;
    }

    /**
     * @param array<string, mixed> $weeklyGoalSummary
     */
    private function dispatchWeeklyGoalPushNotifications(
        int $gymId,
        int $clientId,
        string $gymSlug,
        array $weeklyGoalSummary,
        Carbon $nowAtGym
    ): void {
        if ($gymId <= 0 || $clientId <= 0) {
            return;
        }

        $gymSlug = trim($gymSlug);
        if ($gymSlug === '') {
            return;
        }

        $hasActiveSubscription = ClientPushSubscription::query()
            ->active()
            ->where('gym_id', $gymId)
            ->where('client_id', $clientId)
            ->exists();
        if (! $hasActiveSubscription) {
            return;
        }

        $target = max(3, min(7, (int) ($weeklyGoalSummary['target'] ?? 3)));
        $visits = max(0, (int) ($weeklyGoalSummary['visits'] ?? 0));
        $remaining = max(0, (int) ($weeklyGoalSummary['remaining'] ?? max(0, $target - $visits)));
        $completionPercent = max(0, min(100, (int) ($weeklyGoalSummary['completion_percent'] ?? 0)));
        $daysLeftWeek = max(0, (int) ($weeklyGoalSummary['days_left_week'] ?? 0));
        $weekStart = trim((string) ($weeklyGoalSummary['week_start'] ?? ''));
        if ($weekStart === '') {
            $weekStart = $nowAtGym->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        }
        $weekEnd = trim((string) ($weeklyGoalSummary['week_end'] ?? ''));
        if ($weekEnd === '') {
            $weekEnd = $nowAtGym->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();
        }

        $progressUrl = route('client-mobile.app', [
            'gymSlug' => $gymSlug,
            'screen' => 'progress',
        ]);
        $baseData = [
            'kind' => 'weekly_goal',
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'target' => $target,
            'visits' => $visits,
            'completion_percent' => $completionPercent,
            'sent_at' => now()->toIso8601String(),
        ];

        $completed = $visits >= $target || $completionPercent >= 100;
        if ($completed) {
            $completedCacheKey = 'client-mobile:push:weekly-goal-completed:g'.$gymId.':c'.$clientId.':w'.$weekStart;
            if (Cache::add($completedCacheKey, 1, now()->addDays(10))) {
                $this->clientPushNotificationService->sendToClient($gymId, $clientId, [
                    'title' => 'Meta semanal completada',
                    'body' => 'Cumpliste '.$visits.' de '.$target.' sesiones. Excelente trabajo.',
                    'tag' => 'client-weekly-goal-completed-'.$clientId.'-'.$weekStart,
                    'url' => $progressUrl,
                    'data' => $baseData + [
                        'event' => 'weekly_goal_completed',
                    ],
                    'renotify' => false,
                    'requireInteraction' => false,
                ]);
            }

            return;
        }

        $alerts = is_array($weeklyGoalSummary['alerts'] ?? null) ? $weeklyGoalSummary['alerts'] : [];
        $hasRiskAlert = collect($alerts)->contains(static function ($alert): bool {
            if (! is_array($alert)) {
                return false;
            }

            $type = mb_strtolower(trim((string) ($alert['type'] ?? '')));
            return $type === 'danger';
        });
        $goalAtRisk = $remaining > 0 && ($daysLeftWeek <= 1 || $hasRiskAlert);
        if (! $goalAtRisk) {
            return;
        }

        $today = $nowAtGym->toDateString();
        $riskCacheKey = 'client-mobile:push:weekly-goal-risk:g'.$gymId.':c'.$clientId.':d'.$today;
        if (! Cache::add($riskCacheKey, 1, now()->addHours(30))) {
            return;
        }

        $this->clientPushNotificationService->sendToClient($gymId, $clientId, [
            'title' => 'Tu meta semanal esta en riesgo',
            'body' => $this->buildWeeklyGoalRiskPushMessage($remaining, $daysLeftWeek),
            'tag' => 'client-weekly-goal-risk-'.$clientId.'-'.$today,
            'url' => $progressUrl,
            'data' => $baseData + [
                'event' => 'weekly_goal_risk',
                'remaining' => $remaining,
                'days_left_week' => $daysLeftWeek,
            ],
            'renotify' => true,
            'requireInteraction' => false,
        ]);
    }

    private function buildWeeklyGoalRiskPushMessage(int $remaining, int $daysLeftWeek): string
    {
        $remaining = max(1, $remaining);
        $daysLeftWeek = max(0, $daysLeftWeek);

        if ($daysLeftWeek <= 0) {
            return 'Hoy cierra la semana y aun faltan '.$remaining.' sesiones. Revisa tu plan.';
        }

        if ($daysLeftWeek === 1) {
            return 'Solo queda 1 dia y aun faltan '.$remaining.' sesiones para tu meta semanal.';
        }

        return 'Faltan '.$remaining.' sesiones y '.$daysLeftWeek.' dias para cerrar tu meta semanal.';
    }

    private function resolveTimezone(string $timezone): string
    {
        $candidate = trim($timezone);
        if ($candidate !== '' && in_array($candidate, timezone_identifiers_list(), true)) {
            return $candidate;
        }

        return config('app.timezone', 'UTC');
    }

    private function publishReceptionSync(int $gymId, array $payload): void
    {
        if ($gymId <= 0) {
            return;
        }

        $eventType = strtolower(trim((string) ($payload['event_type'] ?? 'checkin')));
        if ($eventType === '') {
            $eventType = 'checkin';
        }

        Cache::put('reception:sync:gym:'.$gymId.':latest', [
            'id' => (string) Str::ulid(),
            'type' => $eventType,
            'source' => 'mobile-client',
            'payload' => $payload,
            'published_at_ms' => (int) round(microtime(true) * 1000),
        ], now()->addHours(12));
    }

    private function deletePublicAssetIfLocal(?string $path): void
    {
        $assetPath = trim((string) $path);
        if (
            $assetPath === ''
            || str_starts_with($assetPath, 'http://')
            || str_starts_with($assetPath, 'https://')
        ) {
            return;
        }

        Storage::disk('public')->delete(ltrim($assetPath, '/'));
    }
}
