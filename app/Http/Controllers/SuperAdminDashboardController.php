<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGymRequest;
use App\Http\Requests\UpdateGymAdminPasswordRequest;
use App\Http\Requests\UpdateGymAdminUserRequest;
use App\Models\Gym;
use App\Models\SuperAdminPlanTemplate;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Services\SuperAdminDashboardService;
use App\Support\Currency;
use App\Support\GymLocationCatalog;
use App\Support\SuperAdminPlanCatalog;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuperAdminDashboardController extends Controller
{
    public function __construct(
        private readonly SuperAdminDashboardService $dashboardService,
        private readonly SubscriptionService $subscriptionService
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
        SuperAdminPlanTemplate::ensureDefaultCatalog();

        return view('superadmin.gyms', [
            'gyms' => $this->dashboardService->getGymsTable(),
            'paymentMethods' => SubscriptionService::PAYMENT_METHODS,
            'planTemplates' => SuperAdminPlanTemplate::query()
                ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
                ->where('status', 'active')
                ->select(['id', 'plan_key', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price', 'discount_price'])
                ->orderByRaw(SuperAdminPlanCatalog::orderCaseSql('plan_key'))
                ->get(),
        ]);
    }

    /**
     * Create gym page.
     */
    public function gym(): View
    {
        return view('superadmin.gym', $this->gymFormViewData());
    }

    /**
     * Gym listing page for SuperAdmin.
     */
    public function gymListing(): View
    {
        return view('superadmin.gym-listing', [
            'gymsWithAdmins' => $this->gymsWithAdmins(),
            'locationCatalog' => GymLocationCatalog::catalog(),
        ]);
    }

    /**
     * Create a gym and its first admin user.
     */
    public function storeGym(StoreGymRequest $request): RedirectResponse
    {
        $data = $request->validated();
        SuperAdminPlanTemplate::ensureDefaultCatalog();

        $selectedPlanTemplate = SuperAdminPlanTemplate::query()
            ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
            ->where('status', 'active')
            ->findOrFail((int) ($data['subscription_plan_template_id'] ?? 0));
        $customPrice = array_key_exists('subscription_custom_price', $data) && $data['subscription_custom_price'] !== null
            ? (float) $data['subscription_custom_price']
            : null;
        $applyIntroDiscount50 = (bool) ($data['subscription_apply_intro_50'] ?? false);
        $resolvedTemplatePrice = (float) $selectedPlanTemplate->price;
        if ((string) ($selectedPlanTemplate->plan_key ?? '') === 'sucursales' && $customPrice !== null) {
            $resolvedTemplatePrice = $customPrice;
        }
        $profilePhotoPath = $request->hasFile('admin_profile_photo')
            ? $request->file('admin_profile_photo')->store('users/profiles', 'public')
            : null;

        $gym = DB::transaction(function () use ($data, $profilePhotoPath): Gym {
            $slug = $this->generateUniqueGymSlug((string) $data['gym_name']);
            $address = GymLocationCatalog::buildAddress(
                country: (string) $data['gym_address_country'],
                state: (string) $data['gym_address_state'],
                city: (string) $data['gym_address_city'],
                line: $data['gym_address_line'] ?? null
            );

            $gym = Gym::query()->create([
                'name' => (string) $data['gym_name'],
                'slug' => $slug,
                'phone' => $data['gym_phone'] ?? null,
                'address' => $address !== '' ? $address : null,
                'address_country_code' => strtolower((string) $data['gym_address_country']),
                'address_country_name' => (string) (GymLocationCatalog::catalog()[strtolower((string) $data['gym_address_country'])]['label'] ?? (string) $data['gym_address_country']),
                'address_state' => (string) $data['gym_address_state'],
                'address_city' => (string) $data['gym_address_city'],
                'address_line' => $data['gym_address_line'] ?? null,
                'timezone' => (string) $data['gym_timezone'],
                'currency_code' => (string) $data['gym_currency_code'],
                'language_code' => (string) $data['gym_language_code'],
            ]);

            User::query()->create([
                'gym_id' => (int) $gym->id,
                'name' => (string) $data['admin_name'],
                'email' => (string) $data['admin_email'],
                'country_iso' => strtoupper((string) $data['gym_address_country']),
                'country_name' => (string) (GymLocationCatalog::catalog()[strtolower((string) $data['gym_address_country'])]['label'] ?? (string) $data['gym_address_country']),
                'address_state' => (string) $data['gym_address_state'],
                'address_city' => (string) $data['gym_address_city'],
                'address_line' => $data['gym_address_line'] ?? null,
                'phone_country_iso' => strtoupper((string) $data['gym_address_country']),
                'phone_country_dial' => $data['admin_phone_country_dial'] ?? null,
                'phone_number' => $data['admin_phone_number'] ?? null,
                'gender' => $data['admin_gender'] ?? null,
                'birth_date' => $data['admin_birth_date'] ?? null,
                'identification_type' => $data['admin_identification_type'] ?? null,
                'identification_number' => $data['admin_identification_number'] ?? null,
                'profile_photo_path' => $profilePhotoPath,
                'role' => User::ROLE_OWNER,
                'password' => Hash::make((string) $data['admin_password']),
            ]);

            return $gym;
        });

        $this->subscriptionService->applyPlanTemplate(
            gymId: (int) $gym->id,
            planTemplate: [
                'template_id' => (int) $selectedPlanTemplate->id,
                'plan_key' => (string) ($selectedPlanTemplate->plan_key ?? ''),
                'feature_version' => (string) config('plan_features.default_feature_version', 'v1'),
                'name' => (string) $selectedPlanTemplate->name,
                'price' => $resolvedTemplatePrice,
                'duration_unit' => (string) ($selectedPlanTemplate->duration_unit ?? 'days'),
                'duration_days' => (int) $selectedPlanTemplate->duration_days,
                'duration_months' => $selectedPlanTemplate->duration_months !== null ? (int) $selectedPlanTemplate->duration_months : null,
                'intro_discount_first_cycle' => (string) ($selectedPlanTemplate->plan_key ?? '') === 'sucursales' && $applyIntroDiscount50,
                'intro_discount_percent' => 50,
            ],
            paymentMethod: null
        );

        return redirect()
            ->route('superadmin.gyms.index')
            ->with('status', __('messages.gym_created', [
                'gym' => $gym->name,
                'slug' => $gym->slug,
            ]));
    }

    /**
     * Update the main admin user data for a gym.
     */
    public function updateGymAdminUser(UpdateGymAdminUserRequest $request, int $gym): RedirectResponse
    {
        $data = $request->validated();

        $gymModel = Gym::query()
            ->withoutDemoSessions()
            ->findOrFail($gym);
        $adminUserId = (int) $data['admin_user_id'];

        $adminUser = User::query()
            ->where('id', $adminUserId)
            ->where('gym_id', (int) $gymModel->id)
            ->firstOrFail();

        $countryCode = strtolower((string) ($data['admin_country_iso'] ?? ($adminUser->country_iso ? strtolower((string) $adminUser->country_iso) : '')));
        $countryLabel = null;
        if ($countryCode !== '') {
            $countryLabel = (string) (GymLocationCatalog::catalog()[$countryCode]['label'] ?? strtoupper($countryCode));
        }
        $resolvedState = null;
        $resolvedCity = null;
        if ($countryCode !== '' && ! empty($data['admin_address_state'])) {
            $resolvedState = GymLocationCatalog::resolveState($countryCode, (string) $data['admin_address_state']) ?? (string) $data['admin_address_state'];
        } elseif (! empty($data['admin_address_state'])) {
            $resolvedState = (string) $data['admin_address_state'];
        }
        if ($countryCode !== '' && $resolvedState !== null && ! empty($data['admin_address_city'])) {
            $resolvedCity = GymLocationCatalog::resolveCity($countryCode, $resolvedState, (string) $data['admin_address_city']) ?? (string) $data['admin_address_city'];
        } elseif (! empty($data['admin_address_city'])) {
            $resolvedCity = (string) $data['admin_address_city'];
        }

        $photoPath = $adminUser->profile_photo_path;
        if ($request->hasFile('admin_profile_photo')) {
            $photoPath = $request->file('admin_profile_photo')->store('users/profiles', 'public');
            $this->deletePublicAssetIfLocal((string) $adminUser->profile_photo_path);
        }

        $adminUser->forceFill([
            'name' => (string) $data['admin_name'],
            'email' => (string) $data['admin_email'],
            'gender' => $data['admin_gender'] ?? null,
            'birth_date' => $data['admin_birth_date'] ?? null,
            'identification_type' => $data['admin_identification_type'] ?? null,
            'identification_number' => $data['admin_identification_number'] ?? null,
            'country_iso' => $countryCode !== '' ? strtoupper($countryCode) : null,
            'country_name' => $countryLabel,
            'address_state' => $resolvedState,
            'address_city' => $resolvedCity,
            'address_line' => $data['admin_address_line'] ?? null,
            'phone_country_iso' => $countryCode !== '' ? strtoupper($countryCode) : null,
            'phone_country_dial' => $data['admin_phone_country_dial'] ?? null,
            'phone_number' => $data['admin_phone_number'] ?? null,
            'profile_photo_path' => $photoPath,
        ])->save();

        return back()->with('status', 'Usuario del gimnasio actualizado correctamente.');
    }

    /**
     * Reset gym admin password from SuperAdmin.
     */
    public function updateGymAdminPassword(UpdateGymAdminPasswordRequest $request, int $gym): RedirectResponse
    {
        $data = $request->validated();

        $gymModel = Gym::query()
            ->withoutDemoSessions()
            ->findOrFail($gym);
        $adminUserId = (int) $data['reset_password_user_id'];

        $adminUser = User::query()
            ->where('id', $adminUserId)
            ->where('gym_id', (int) $gymModel->id)
            ->firstOrFail();

        $adminUser->forceFill([
            'password' => Hash::make((string) $data['reset_password']),
            'remember_token' => null,
        ])->save();

        return back()->with('status', 'Contrasena del admin actualizada correctamente.');
    }

    /**
     * Delete a gym and all of its data.
     */
    public function destroyGym(int $gym): RedirectResponse
    {
        $gymModel = Gym::query()
            ->withoutDemoSessions()
            ->findOrFail($gym);

        $gymUserIds = User::query()
            ->where('gym_id', (int) $gymModel->id)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        DB::transaction(function () use ($gymModel, $gymUserIds): void {
            $gymModel->delete();

            if ($gymUserIds !== []) {
                User::query()
                    ->whereIn('id', $gymUserIds)
                    ->delete();
            }
        });

        return redirect()
            ->route('superadmin.gym-list.index')
            ->with('status', 'Gimnasio eliminado con todos sus datos.');
    }

    private function generateUniqueGymSlug(string $gymName): string
    {
        $baseSlug = Str::slug($gymName);
        if ($baseSlug === '') {
            $baseSlug = 'gym';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (Gym::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * @return array<string, string>
     */
    private function timezoneOptions(): array
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $identifiers = DateTimeZone::listIdentifiers();
        $options = [];

        foreach ($identifiers as $identifier) {
            $timezone = new DateTimeZone($identifier);
            $offset = $timezone->getOffset($now);
            $sign = $offset >= 0 ? '+' : '-';
            $absOffset = abs($offset);
            $hours = intdiv($absOffset, 3600);
            $minutes = intdiv($absOffset % 3600, 60);
            $labelOffset = sprintf('UTC%s%02d:%02d', $sign, $hours, $minutes);
            $options[$identifier] = sprintf('(%s) %s', $labelOffset, str_replace('_', ' ', $identifier));
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    private function gymFormViewData(): array
    {
        SuperAdminPlanTemplate::ensureDefaultCatalog();

        return [
            'currencyOptions' => Currency::options(),
            'languageOptions' => [
                'es' => 'Espanol',
                'en' => 'English',
                'pt' => 'Portugues',
            ],
            'timezoneOptions' => $this->timezoneOptions(),
            'locationCatalog' => GymLocationCatalog::catalog(),
            'defaultTimezone' => 'America/Guayaquil',
            'planTemplates' => SuperAdminPlanTemplate::query()
                ->whereIn('plan_key', SuperAdminPlanCatalog::keys())
                ->where('status', 'active')
                ->select(['id', 'plan_key', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price', 'discount_price'])
                ->orderByRaw(SuperAdminPlanCatalog::orderCaseSql('plan_key'))
                ->get(),
        ];
    }

    private function deletePublicAssetIfLocal(string $path): void
    {
        $trimmed = trim($path);
        if ($trimmed === '') {
            return;
        }
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return;
        }
        $normalized = ltrim($trimmed, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        if ($normalized !== '' && Storage::disk('public')->exists($normalized)) {
            Storage::disk('public')->delete($normalized);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Gym>
     */
    private function gymsWithAdmins()
    {
        return Gym::query()
            ->withoutDemoSessions()
            ->with([
                'users' => static function ($query): void {
                    $query->orderBy('id');
                },
                'branchLinks',
                'parentHubLinks.hubGym',
            ])
            ->orderByDesc('id')
            ->get();
    }
}
