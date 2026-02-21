<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGymRequest;
use App\Http\Requests\UpdateGymAdminUserRequest;
use App\Models\Gym;
use App\Models\Plan;
use App\Models\Promotion;
use App\Models\SuperAdminPlanTemplate;
use App\Models\SuperAdminPromotionTemplate;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Services\SuperAdminDashboardService;
use App\Support\Currency;
use App\Support\GymLocationCatalog;
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
            'planTemplates' => SuperAdminPlanTemplate::query()
                ->where('status', 'active')
                ->select(['id', 'name', 'duration_days', 'duration_unit', 'duration_months', 'price'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Create gym page.
     */
    public function gym(): View
    {
        $viewData = $this->gymFormViewData();
        $viewData['gymsWithAdmins'] = Gym::query()
            ->with(['users' => static function ($query): void {
                $query->orderBy('id');
            }])
            ->orderByDesc('id')
            ->get();

        return view('superadmin.gym', $viewData);
    }

    /**
     * Create a gym and its first admin user.
     */
    public function storeGym(StoreGymRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $gym = DB::transaction(function () use ($data): Gym {
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
                'gender' => $data['admin_gender'] ?? null,
                'identification_type' => $data['admin_identification_type'] ?? null,
                'identification_number' => $data['admin_identification_number'] ?? null,
                'password' => Hash::make((string) $data['admin_password']),
            ]);

            $this->copySuperAdminPlanTemplatesToGym((int) $gym->id);

            return $gym;
        });

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

        $gymModel = Gym::query()->findOrFail($gym);
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
     * Delete a gym and all of its data.
     */
    public function destroyGym(int $gym): RedirectResponse
    {
        $gymModel = Gym::query()->findOrFail($gym);

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
            ->route('superadmin.gym.index')
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

    private function copySuperAdminPlanTemplatesToGym(int $gymId): void
    {
        if ($gymId <= 0) {
            return;
        }

        $planTemplates = SuperAdminPlanTemplate::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        if ($planTemplates->isEmpty()) {
            return;
        }

        $createdPlansByTemplateId = [];

        foreach ($planTemplates as $template) {
            $plan = Plan::query()->create([
                'gym_id' => $gymId,
                'name' => (string) $template->name,
                'duration_days' => (int) $template->duration_days,
                'duration_unit' => (string) ($template->duration_unit ?: 'days'),
                'duration_months' => $template->duration_months !== null ? (int) $template->duration_months : null,
                'price' => (float) $template->price,
                'status' => 'active',
            ]);

            $createdPlansByTemplateId[(int) $template->id] = (int) $plan->id;
        }

        $promotionTemplates = SuperAdminPromotionTemplate::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        foreach ($promotionTemplates as $template) {
            $targetPlanId = null;
            if ($template->plan_template_id !== null) {
                $targetPlanId = $createdPlansByTemplateId[(int) $template->plan_template_id] ?? null;
            }

            Promotion::query()->create([
                'gym_id' => $gymId,
                'plan_id' => $targetPlanId,
                'name' => (string) $template->name,
                'description' => $template->description !== null ? (string) $template->description : null,
                'type' => (string) $template->type,
                'value' => $template->value !== null ? (float) $template->value : null,
                'starts_at' => $template->starts_at?->toDateString(),
                'ends_at' => $template->ends_at?->toDateString(),
                'status' => 'active',
                'max_uses' => $template->max_uses !== null ? (int) $template->max_uses : null,
                'times_used' => 0,
            ]);
        }
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
}
