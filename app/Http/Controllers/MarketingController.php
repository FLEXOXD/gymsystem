<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLegalAcceptanceRequest;
use App\Models\DemoSession;
use App\Models\Gym;
use App\Models\LegalAcceptance;
use App\Models\LandingContactMessage;
use App\Models\LandingQuoteRequest;
use App\Models\Subscription;
use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use App\Models\User;
use App\Services\DemoSessionService;
use App\Services\SuperAdminCommercialPlanService;
use App\Support\LegalTerms;
use App\Support\MarketingContent;
use App\Support\SuperAdminPlanCatalog;
use App\Support\SuperAdminPlanPresentation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MarketingController extends Controller
{
    public function __construct(
        private readonly SuperAdminCommercialPlanService $commercialPlanService
    ) {
    }

    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        return $this->renderLandingPage($request, 'home');
    }

    public function about(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        return $this->renderLandingPage($request, 'about');
    }

    public function contact(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        return $this->renderLandingPage($request, 'contact');
    }

    public function demo(Request $request): View|RedirectResponse
    {
        $redirect = $this->redirectAuthenticatedUser($request);
        if ($redirect) {
            return $redirect;
        }

        $this->applyPublicTimezone();

        return view('marketing.demo', [
            'content' => MarketingContent::load(),
        ]);
    }

    public function demoGuide(Request $request): View|RedirectResponse
    {
        $redirect = $this->redirectAuthenticatedUser($request);
        if ($redirect) {
            return $redirect;
        }

        $this->applyPublicTimezone();

        return view('marketing.demo-guide', [
            'content' => MarketingContent::load(),
        ]);
    }

    public function storeContactMessage(Request $request): RedirectResponse
    {
        $this->applyPublicTimezone();

        $data = $request->validateWithBag('landingContact', [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        LandingContactMessage::query()->create([
            'first_name' => trim((string) $data['first_name']),
            'last_name' => trim((string) $data['last_name']),
            'email' => strtolower(trim((string) $data['email'])),
            'message' => trim((string) $data['message']),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        return redirect()
            ->to(route('landing.contact').'#contacto')
            ->with('contact_status', 'Recibimos tu mensaje. Nuestro equipo revisará tu caso y te responderá pronto con el siguiente paso recomendado para tu gimnasio.');
    }

    public function storeQuoteRequest(Request $request): RedirectResponse
    {
        $this->applyPublicTimezone();

        $data = $request->validateWithBag('landingQuote', [
            'quote_first_name' => ['required', 'string', 'max:80'],
            'quote_last_name' => ['required', 'string', 'max:80'],
            'quote_email' => ['required', 'email', 'max:150'],
            'quote_phone_country_code' => ['required', 'string', 'max:10', 'regex:/^\+\d{1,4}$/'],
            'quote_phone_number' => ['required', 'string', 'max:30', 'regex:/^[0-9\s\-]{6,20}$/'],
            'quote_country' => ['required', 'string', 'max:120'],
            'quote_professionals_count' => ['required', 'integer', 'min:1', 'max:5000'],
            'quote_requested_plan' => ['nullable', 'string', 'max:60'],
            'quote_source' => ['nullable', 'string', 'max:60'],
            'quote_notes' => ['nullable', 'string', 'max:1000'],
            'quote_privacy_accepted' => ['accepted'],
        ], [
            'quote_privacy_accepted.accepted' => 'Debes aceptar el tratamiento de datos para recibir tu cotización.',
            'quote_phone_country_code.regex' => 'El prefijo telefónico debe tener formato internacional, por ejemplo +593.',
            'quote_phone_number.regex' => 'Ingresa un número telefónico válido.',
        ]);

        $sanitizedPhoneNumber = preg_replace('/\D+/', '', (string) $data['quote_phone_number']) ?? '';

        LandingQuoteRequest::query()->create([
            'first_name' => trim((string) $data['quote_first_name']),
            'last_name' => trim((string) $data['quote_last_name']),
            'email' => strtolower(trim((string) $data['quote_email'])),
            'phone_country_code' => trim((string) $data['quote_phone_country_code']),
            'phone_number' => $sanitizedPhoneNumber !== '' ? $sanitizedPhoneNumber : trim((string) $data['quote_phone_number']),
            'country' => trim((string) $data['quote_country']),
            'professionals_count' => (int) $data['quote_professionals_count'],
            'requested_plan' => $this->normalizeQuoteMeta($data['quote_requested_plan'] ?? null),
            'source' => $this->normalizeQuoteMeta($data['quote_source'] ?? null),
            'notes' => $this->normalizeNullableText($data['quote_notes'] ?? null),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        return back()->with('quote_status', 'Gracias por solicitar tu cotización. En breve te contactaremos para preparar una propuesta ajustada a la operación de tu gimnasio.');
    }

    public function privacy(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        return $this->renderLandingPage($request, 'privacy');
    }

    public function serviceTerms(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        return $this->renderLandingPage($request, 'service');
    }

    public function commercialTerms(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        return $this->renderLandingPage($request, 'commercial');
    }

    public function acceptLegal(StoreLegalAcceptanceRequest $request): RedirectResponse
    {
        $documentKey = (string) $request->validated('document_key');
        $document = self::legalDocumentCatalog()[$documentKey];

        LegalAcceptance::query()->create([
            'full_name' => (string) $request->validated('full_name'),
            'email' => strtolower((string) $request->validated('email')),
            'document_key' => $documentKey,
            'document_label' => (string) $document['label'],
            'legal_version' => LegalTerms::VERSION,
            'accepted' => true,
            'accepted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
        ]);

        return back()->with('status', 'Aceptación registrada para '.$document['label'].' (versión '.LegalTerms::VERSION.').');
    }

    private function renderLandingPage(Request $request, string $pageMode): View|RedirectResponse
    {
        $redirect = $this->redirectAuthenticatedUser($request);
        if ($redirect) {
            return $redirect;
        }

        $this->applyPublicTimezone();

        return view('marketing.home', [
            'stats' => $this->resolveLandingStats(),
            'content' => MarketingContent::load(),
            'publicPlanCards' => $this->resolvePublicPlanCards(),
            'pageMode' => $pageMode,
            'legalVersion' => LegalTerms::VERSION,
        ]);
    }

    /**
     * @return array{gyms:int,active_sessions:int}
     */
    private function resolveLandingStats(): array
    {
        $stats = [
            'gyms' => 0,
            'active_sessions' => 0,
        ];

        try {
            $stats = [
                'gyms' => Gym::query()->withoutDemoSessions()->count(),
                'active_sessions' => Gym::query()
                    ->withoutDemoSessions()
                    ->whereHas('latestSubscription', fn ($query) => $query->where('status', 'active'))
                    ->count(),
            ];
        } catch (Throwable $exception) {
            Log::warning('Marketing stats unavailable, returning safe defaults.', [
                'exception' => $exception->getMessage(),
            ]);
        }

        return $stats;
    }

    public function requestDemo(Request $request, DemoSessionService $demoSessionService): RedirectResponse
    {
        $redirect = $this->redirectAuthenticatedUser($request);
        if ($redirect) {
            return $redirect;
        }

        $session = $this->resolveReusableDemoSession($request, $demoSessionService);
        $isNewDemo = false;
        if (! $session) {
            $session = $demoSessionService->startSession();
            $isNewDemo = true;
        }

        $this->storeDemoSessionReference($request, $session);

        Auth::login($session->user);
        $request->session()->regenerate();

        return redirect()
            ->route('panel.index', ['contextGym' => $session->gym->slug])
            ->with('status', $isNewDemo
                ? 'Registro temporal activo. Los cambios se eliminan automáticamente al expirar.'
                : 'Se reanudó tu registro temporal activo en este dispositivo.');
    }

    private function redirectAuthenticatedUser(Request $request): ?RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        if ($request->boolean('preview_guest') && ! $user->gym_id) {
            return null;
        }

        if (
            ! $user->gym_id
            && $request->routeIs('landing')
            && (bool) $request->session()->pull('allow_superadmin_landing_preview_once', false)
        ) {
            return null;
        }

        $gymSlug = trim((string) ($user->gym?->slug ?? ''));
        if ($gymSlug !== '') {
            return redirect()->route('panel.index', ['contextGym' => $gymSlug]);
        }

        return redirect()->route('superadmin.dashboard');
    }

    private function resolveReusableDemoSession(Request $request, DemoSessionService $demoSessionService): ?DemoSession
    {
        $tokensToTry = [];

        $sessionToken = trim((string) $request->session()->get('demo_session_token', ''));
        if ($sessionToken !== '') {
            $tokensToTry[] = $sessionToken;
        }

        $cacheToken = trim((string) Cache::get($this->demoFingerprintCacheKey($request), ''));
        if ($cacheToken !== '') {
            $tokensToTry[] = $cacheToken;
        }

        foreach (array_values(array_unique($tokensToTry)) as $token) {
            $demoSession = DemoSession::query()
                ->active()
                ->where('token', $token)
                ->first();

            if (! $demoSession) {
                continue;
            }

            if (! $demoSession->user || ! $demoSession->gym) {
                continue;
            }

            $demoSessionService->touchActivity($demoSession);

            return $demoSession->fresh(['user', 'gym']);
        }

        return null;
    }

    private function storeDemoSessionReference(Request $request, DemoSession $demoSession): void
    {
        $request->session()->put('demo_session_token', $demoSession->token);

        $ttlMinutes = max(1, now()->diffInMinutes($demoSession->expires_at, false));
        Cache::put(
            $this->demoFingerprintCacheKey($request),
            $demoSession->token,
            now()->addMinutes($ttlMinutes)
        );
    }

    private function demoFingerprintCacheKey(Request $request): string
    {
        $fingerprint = hash(
            'sha256',
            strtolower((string) $request->ip()).'|'.substr(strtolower((string) $request->userAgent()), 0, 180)
        );

        return 'demo:fingerprint:'.$fingerprint;
    }

    private function normalizeQuoteMeta(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? mb_substr($normalized, 0, 60) : null;
    }

    private function normalizeNullableText(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function applyPublicTimezone(): string
    {
        $timezone = $this->resolvePublicTimezone();

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $timezone;
    }

    private function resolvePublicTimezone(): string
    {
        $candidate = trim((string) User::query()
            ->whereNull('gym_id')
            ->whereNotNull('timezone')
            ->orderByDesc('id')
            ->value('timezone'));

        if (
            $candidate !== ''
            && $candidate !== 'UTC'
            && in_array($candidate, timezone_identifiers_list(), true)
        ) {
            return $candidate;
        }

        return 'America/Guayaquil';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function resolvePublicPlanCards(): array
    {
        $defaultsByKey = collect(SuperAdminPlanCatalog::defaults())
            ->keyBy(static fn (array $row): string => (string) $row['plan_key']);
        $cards = [];

        try {
            if ($this->commercialPlanService->supportsCommercialCatalog()) {
                foreach ($this->commercialPlanService->basePlans() as $basePlan) {
                    $planKey = trim((string) ($basePlan->plan_key ?? ''));
                    if ($planKey === '') {
                        continue;
                    }

                    $promotion = $this->resolveLandingPromotionTemplate($basePlan);
                    $promotionSnapshot = $this->resolveLandingPromotionSnapshot($basePlan, $promotion);
                    $manualOfferText = trim((string) ($basePlan->offer_text ?? ''));
                    $resolvedOfferText = trim((string) ($promotionSnapshot['offer_text'] ?? '')) !== ''
                        ? trim((string) $promotionSnapshot['offer_text'])
                        : ($manualOfferText !== '' ? $manualOfferText : null);

                    $cards[] = $this->makePublicPlanCard(
                        $planKey,
                        (string) ($basePlan->name ?? SuperAdminPlanPresentation::for($planKey)['title']),
                        (float) ($basePlan->price ?? 0),
                        $promotionSnapshot['discount_price'],
                        $resolvedOfferText
                    );
                }
            }
        } catch (Throwable $exception) {
            Log::warning('Marketing plan catalog unavailable, using defaults.', [
                'exception' => $exception->getMessage(),
            ]);
        }

        if ($cards !== []) {
            return $this->applyDynamicFeaturedPlan($cards);
        }

        foreach (SuperAdminPlanCatalog::keys() as $planKey) {
            $default = (array) ($defaultsByKey->get($planKey) ?? []);
            $cards[] = $this->makePublicPlanCard(
                $planKey,
                (string) ($default['name'] ?? SuperAdminPlanPresentation::for($planKey)['title']),
                (float) ($default['price'] ?? 0),
                null,
                null
            );
        }

        return $this->applyDynamicFeaturedPlan($cards);
    }

    /**
     * @param  array<int, array<string, mixed>>  $cards
     * @return array<int, array<string, mixed>>
     */
    private function applyDynamicFeaturedPlan(array $cards): array
    {
        $availablePlanKeys = collect($cards)
            ->map(static fn (array $card): string => mb_strtolower(trim((string) ($card['plan_key'] ?? ''))))
            ->filter(static fn (string $planKey): bool => $planKey !== '')
            ->unique()
            ->values()
            ->all();

        $featuredPlanKey = $this->resolveMostUsedPublicPlanKey($availablePlanKeys);
        if ($featuredPlanKey === null) {
            return $cards;
        }

        return collect($cards)
            ->map(static function (array $card) use ($featuredPlanKey): array {
                $planKey = mb_strtolower(trim((string) ($card['plan_key'] ?? '')));
                $card['featured'] = $planKey === $featuredPlanKey;

                return $card;
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $availablePlanKeys
     */
    private function resolveMostUsedPublicPlanKey(array $availablePlanKeys): ?string
    {
        $normalizedKeys = collect($availablePlanKeys)
            ->map(static fn (string $key): string => mb_strtolower(trim($key)))
            ->filter(static fn (string $key): bool => $key !== '')
            ->unique()
            ->values();

        if ($normalizedKeys->isEmpty()) {
            return null;
        }

        try {
            if (! Schema::hasTable('subscriptions') || ! Schema::hasTable('gyms') || ! Schema::hasColumn('subscriptions', 'plan_key')) {
                return null;
            }

            $supportsBranchManagedFlag = Schema::hasColumn('subscriptions', 'is_branch_managed');
            $planCountQuery = Subscription::query()
                ->whereHas('gym', fn ($query) => $query->withoutDemoSessions());

            if ($supportsBranchManagedFlag) {
                $planCountQuery->where(function ($query): void {
                    $query
                        ->where('is_branch_managed', false)
                        ->orWhereNull('is_branch_managed');
                });
            }

            $planCountRows = $planCountQuery
                ->selectRaw('LOWER(COALESCE(plan_key, "")) as plan_key, COUNT(*) as total')
                ->groupBy('plan_key')
                ->pluck('total', 'plan_key');

            $countsByKey = $normalizedKeys->mapWithKeys(
                static fn (string $key): array => [$key => (int) ($planCountRows[$key] ?? 0)]
            );

            $maxCount = (int) ($countsByKey->max() ?? 0);
            if ($maxCount <= 0) {
                return null;
            }

            $candidates = $countsByKey
                ->filter(static fn (int $count): bool => $count === $maxCount)
                ->keys()
                ->values();

            if ($candidates->count() === 1) {
                return (string) $candidates->first();
            }

            $candidateLookup = $candidates->flip();
            $staticFeatured = collect(SuperAdminPlanPresentation::metadata())
                ->filter(static fn (array $meta): bool => (bool) ($meta['featured'] ?? false))
                ->keys()
                ->map(static fn (string $key): string => mb_strtolower(trim($key)))
                ->values();

            foreach ($staticFeatured as $planKey) {
                if ($candidateLookup->has($planKey)) {
                    return (string) $planKey;
                }
            }

            foreach (SuperAdminPlanCatalog::keys() as $planKey) {
                $normalized = mb_strtolower(trim((string) $planKey));
                if ($candidateLookup->has($normalized)) {
                    return $normalized;
                }
            }

            $fallbackCandidate = $candidates->sort()->first();

            return is_string($fallbackCandidate) && trim($fallbackCandidate) !== ''
                ? trim($fallbackCandidate)
                : null;
        } catch (Throwable $exception) {
            Log::warning('No se pudo resolver el plan destacado dinámico para la landing.', [
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function makePublicPlanCard(string $planKey, string $name, float $price, ?float $discountPrice, ?string $offerText = null): array
    {
        $meta = SuperAdminPlanPresentation::for($planKey);
        $discountPercent = null;
        if ($discountPrice !== null && $price > 0 && $discountPrice < $price) {
            $discountPercent = (int) round((($price - $discountPrice) / $price) * 100);
        }

        return [
            'plan_key' => $planKey,
            'name' => trim($name) !== '' ? $name : (string) $meta['title'],
            'price' => $price,
            'discount_price' => $discountPrice,
            'offer_text' => trim((string) ($offerText ?? '')) !== '' ? trim((string) $offerText) : null,
            'discount_percent' => $discountPercent,
            'summary' => (string) ($meta['summary'] ?? ''),
            'features' => array_values((array) ($meta['features'] ?? [])),
            'ideal_for' => (string) ($meta['ideal_for'] ?? ''),
            'ops_focus' => (string) ($meta['ops_focus'] ?? ''),
            'setup_note' => (string) ($meta['setup_note'] ?? ''),
            'featured' => (bool) ($meta['featured'] ?? false),
            'contact_mode' => (bool) ($meta['contact_mode'] ?? false),
            'cta' => (string) ($meta['cta'] ?? 'Demo gratis'),
        ];
    }

    /**
     * @return array{discount_price:?float,offer_text:?string}
     */
    private function resolveLandingPromotionSnapshot(SuperAdminPlanTemplate $planTemplate, ?SuperAdminPromotionTemplate $promotion): array
    {
        if (! $promotion) {
            return [
                'discount_price' => null,
                'offer_text' => null,
            ];
        }

        $basePrice = round((float) ($planTemplate->price ?? 0), 2);
        $offerText = trim((string) ($promotion->description ?? $promotion->name ?? ''));
        $discountPrice = null;
        $value = $promotion->value !== null ? (float) $promotion->value : null;
        $durationUnit = method_exists($promotion, 'resolvedDurationUnit')
            ? (string) $promotion->resolvedDurationUnit()
            : 'months';

        switch ((string) $promotion->type) {
            case 'percentage':
                if ($value !== null) {
                    $percent = min(max($value, 0), 100);
                    $discountPrice = round(max(0, $basePrice - ($basePrice * ($percent / 100))), 2);
                }
                break;

            case 'fixed':
                if ($value !== null) {
                    $discountPrice = round(max(0, $basePrice - min($basePrice, max(0, $value))), 2);
                }
                break;

            case 'final_price':
                if ($value !== null) {
                    if ($durationUnit === 'months') {
                        $durationMonths = max(1, (int) ($promotion->duration_months ?? 1));
                        $discountPrice = $durationMonths > 1
                            ? round(max(0, $value / $durationMonths), 2)
                            : round(max(0, $value), 2);
                    }
                }
                break;
        }

        return [
            'discount_price' => $discountPrice !== null && $discountPrice < $basePrice ? $discountPrice : null,
            'offer_text' => $offerText !== '' ? $offerText : null,
        ];
    }

    private function resolveLandingPromotionTemplate(SuperAdminPlanTemplate $planTemplate): ?SuperAdminPromotionTemplate
    {
        if (! Schema::hasTable('superadmin_promotion_templates')) {
            return null;
        }

        $today = now()->toDateString();
        $supportsDurationUnit = $this->supportsPromotionDurationUnitColumns();
        $query = SuperAdminPromotionTemplate::query()
            ->where('plan_template_id', (int) $planTemplate->id)
            ->where('status', 'active')
            ->where(function ($query) use ($today): void {
                $query->whereNull('starts_at')
                    ->orWhereDate('starts_at', '<=', $today);
            })
            ->where(function ($query) use ($today): void {
                $query->whereNull('ends_at')
                    ->orWhereDate('ends_at', '>=', $today);
            });

        if ($supportsDurationUnit) {
            $query
                ->where(function ($query): void {
                    $query
                        ->where(function ($monthQuery): void {
                            $monthQuery->where(function ($unitQuery): void {
                                $unitQuery->whereNull('duration_unit')
                                    ->orWhere('duration_unit', 'months');
                            })->where(function ($durationQuery): void {
                                $durationQuery->whereNull('duration_months')
                                    ->orWhere('duration_months', 1);
                            });
                        })
                        ->orWhere(function ($dayQuery): void {
                            $dayQuery->where('duration_unit', 'days')
                                ->whereNotNull('duration_days');
                        });
                })
                ->orderByRaw("CASE WHEN duration_unit = 'days' THEN 0 WHEN duration_months = 1 THEN 1 ELSE 2 END")
                ->orderByDesc('id');
        } else {
            $query
                ->where(function ($query): void {
                    $query->whereNull('duration_months')
                        ->orWhere('duration_months', 1);
                })
                ->orderByRaw('CASE WHEN duration_months = 1 THEN 0 ELSE 1 END')
                ->orderByDesc('id');
        }

        return $query->first();
    }

    private function supportsPromotionDurationUnitColumns(): bool
    {
        return Schema::hasTable('superadmin_promotion_templates')
            && Schema::hasColumns('superadmin_promotion_templates', ['duration_unit', 'duration_days']);
    }

    /**
     * @return array<string, array{label:string}>
     */
    private static function legalDocumentCatalog(): array
    {
        $documents = LegalTerms::documents();

        return [
            'privacy_policy' => ['label' => (string) ($documents['privacy_policy']['label'] ?? 'Política de privacidad')],
            'service_terms' => ['label' => (string) ($documents['service_terms']['label'] ?? 'Condiciones de servicio')],
            'commercial_terms' => ['label' => (string) ($documents['commercial_terms']['label'] ?? 'Términos comerciales')],
        ];
    }
}
