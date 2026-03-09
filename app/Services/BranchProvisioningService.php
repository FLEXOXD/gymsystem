<?php

namespace App\Services;

use App\Models\Gym;
use App\Models\GymBranchLink;
use App\Models\Subscription;
use App\Models\User;
use App\Support\GymLocationCatalog;
use App\Support\SuperAdminPlanCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BranchProvisioningService
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_BRANCH_PLAN_KEYS = [
        'basico',
        'profesional',
        'premium',
    ];

    public function __construct(
        private readonly PlanAccessService $planAccessService,
        private readonly SubscriptionService $subscriptionService
    ) {
    }

    /**
     * Create a branch gym + branch admin + managed subscription + link in one transaction.
     */
    public function createBranchForHub(
        int $hubGymId,
        string $branchName,
        ?string $branchPhone,
        ?string $countryCode,
        ?string $state,
        ?string $city,
        ?string $addressLine,
        string $branchPlanKey,
        string $branchAdminName,
        string $branchAdminPassword,
        ?string $branchAdminEmail = null,
        bool $cashManagedByHub = true,
        ?int $createdByUserId = null
    ): GymBranchLink {
        $hubGym = Gym::query()
            ->withoutDemoSessions()
            ->findOrFail($hubGymId);

        if (! $this->planAccessService->canForGym($hubGymId, 'multi_branch')) {
            throw new InvalidArgumentException('La sede principal no tiene habilitado plan sucursales.');
        }

        $normalizedBranchName = trim($branchName);
        if ($normalizedBranchName === '') {
            throw new InvalidArgumentException('El nombre de la sucursal es obligatorio.');
        }

        $resolvedPlanKey = strtolower(trim($branchPlanKey));
        if (! in_array($resolvedPlanKey, self::ALLOWED_BRANCH_PLAN_KEYS, true)) {
            throw new InvalidArgumentException('Plan de sucursal no válido. Usa basico, profesional o premium.');
        }

        $resolvedCountryCode = strtolower(trim((string) ($countryCode ?? '')));
        if ($resolvedCountryCode === '') {
            $resolvedCountryCode = strtolower(trim((string) ($hubGym->address_country_code ?? '')));
        }
        if ($resolvedCountryCode === '' || ! GymLocationCatalog::hasCountry($resolvedCountryCode)) {
            $resolvedCountryCode = 'ec';
        }

        $resolvedState = trim((string) ($state ?? ''));
        if ($resolvedState === '') {
            $resolvedState = trim((string) ($hubGym->address_state ?? ''));
        }

        $resolvedCity = trim((string) ($city ?? ''));
        if ($resolvedCity === '') {
            $resolvedCity = trim((string) ($hubGym->address_city ?? ''));
        }

        if ($resolvedState === '' || $resolvedCity === '') {
            throw new InvalidArgumentException('Provincia/estado y ciudad son obligatorios para crear la sucursal.');
        }

        $normalizedState = GymLocationCatalog::resolveState($resolvedCountryCode, $resolvedState) ?? $resolvedState;
        $normalizedCity = GymLocationCatalog::resolveCity($resolvedCountryCode, $normalizedState, $resolvedCity) ?? $resolvedCity;

        $resolvedAddressLine = trim((string) ($addressLine ?? ''));
        $resolvedBranchPhone = trim((string) ($branchPhone ?? ''));
        if ($resolvedBranchPhone === '') {
            $resolvedBranchPhone = trim((string) ($hubGym->phone ?? ''));
        }
        $resolvedBranchPhone = $resolvedBranchPhone !== '' ? $resolvedBranchPhone : null;

        $normalizedAdminName = trim($branchAdminName);
        if ($normalizedAdminName === '') {
            throw new InvalidArgumentException('El nombre del usuario administrador de la sucursal es obligatorio.');
        }

        $normalizedAdminPassword = trim($branchAdminPassword);
        if (mb_strlen($normalizedAdminPassword) < 8) {
            throw new InvalidArgumentException('La contraseña de la sucursal debe tener al menos 8 caracteres.');
        }

        $slug = $this->generateUniqueGymSlug($normalizedBranchName);
        $customAdminEmail = strtolower(trim((string) ($branchAdminEmail ?? '')));
        $resolvedAdminEmail = $customAdminEmail !== ''
            ? $this->validateCustomBranchAdminEmail($customAdminEmail)
            : $this->resolveBranchAdminEmail(
                hubGym: $hubGym,
                branchName: $normalizedBranchName
            );

        $countryMeta = GymLocationCatalog::catalog()[$resolvedCountryCode] ?? ['label' => strtoupper($resolvedCountryCode)];
        $countryLabel = (string) ($countryMeta['label'] ?? strtoupper($resolvedCountryCode));

        $branchAddress = GymLocationCatalog::buildAddress(
            country: $resolvedCountryCode,
            state: $normalizedState,
            city: $normalizedCity,
            line: $resolvedAddressLine !== '' ? $resolvedAddressLine : null
        );

        $defaults = collect(SuperAdminPlanCatalog::defaults())
            ->firstWhere('plan_key', $resolvedPlanKey);

        if (! is_array($defaults)) {
            throw new InvalidArgumentException('No se pudo resolver el catálogo base para el plan de sucursal.');
        }

        return DB::transaction(function () use (
            $hubGymId,
            $hubGym,
            $normalizedBranchName,
            $slug,
            $resolvedBranchPhone,
            $branchAddress,
            $resolvedCountryCode,
            $countryLabel,
            $normalizedState,
            $normalizedCity,
            $resolvedAddressLine,
            $normalizedAdminName,
            $resolvedAdminEmail,
            $normalizedAdminPassword,
            $resolvedPlanKey,
            $defaults,
            $cashManagedByHub,
            $createdByUserId
        ): GymBranchLink {
            $branchGym = Gym::query()->create([
                'name' => $normalizedBranchName,
                'slug' => $slug,
                'phone' => $resolvedBranchPhone,
                'address' => $branchAddress !== '' ? $branchAddress : null,
                'address_country_code' => $resolvedCountryCode,
                'address_country_name' => $countryLabel,
                'address_state' => $normalizedState,
                'address_city' => $normalizedCity,
                'address_line' => $resolvedAddressLine !== '' ? $resolvedAddressLine : null,
                'timezone' => (string) ($hubGym->timezone ?? config('app.timezone', 'UTC')),
                'currency_code' => (string) ($hubGym->currency_code ?? 'USD'),
                'language_code' => (string) ($hubGym->language_code ?? 'es'),
                'logo_path' => null,
                'avatar_male_path' => $hubGym->avatar_male_path,
                'avatar_female_path' => $hubGym->avatar_female_path,
                'avatar_neutral_path' => $hubGym->avatar_neutral_path,
            ]);

            User::query()->create([
                'gym_id' => (int) $branchGym->id,
                'name' => $normalizedAdminName,
                'email' => $resolvedAdminEmail,
                'country_iso' => strtoupper($resolvedCountryCode),
                'country_name' => $countryLabel,
                'address_state' => $normalizedState,
                'address_city' => $normalizedCity,
                'address_line' => $resolvedAddressLine !== '' ? $resolvedAddressLine : null,
                'phone_country_iso' => strtoupper($resolvedCountryCode),
                'phone_country_dial' => null,
                'phone_number' => null,
                'role' => User::ROLE_OWNER,
                'password' => $normalizedAdminPassword,
            ]);

            $this->subscriptionService->applyPlanTemplate(
                gymId: (int) $branchGym->id,
                planTemplate: [
                    'template_id' => null,
                    'plan_key' => $resolvedPlanKey,
                    'feature_version' => (string) config('plan_features.default_feature_version', 'v1'),
                    'name' => (string) ($defaults['name'] ?? 'Plan '.$resolvedPlanKey),
                    'price' => 0.0,
                    'duration_unit' => (string) ($defaults['duration_unit'] ?? 'days'),
                    'duration_days' => (int) ($defaults['duration_days'] ?? 30),
                    'duration_months' => isset($defaults['duration_months']) ? (int) $defaults['duration_months'] : null,
                ],
                paymentMethod: null
            );

            Subscription::query()
                ->where('gym_id', (int) $branchGym->id)
                ->update([
                    'billing_owner_gym_id' => $hubGymId,
                    'is_branch_managed' => true,
                ]);

            $this->subscriptionService->syncManagedSubscriptionWithOwner((int) $branchGym->id);

            return GymBranchLink::query()->create([
                'hub_gym_id' => $hubGymId,
                'branch_gym_id' => (int) $branchGym->id,
                'branch_plan_key' => $resolvedPlanKey,
                'cash_managed_by_hub' => $cashManagedByHub,
                'status' => 'active',
                'created_by' => $createdByUserId,
            ]);
        });
    }

    private function generateUniqueGymSlug(string $branchName): string
    {
        $baseSlug = Str::slug($branchName);
        if ($baseSlug === '') {
            $baseSlug = 'sucursal';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (Gym::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function resolveBranchAdminEmail(Gym $hubGym, string $branchName): string
    {
        $hubUser = User::query()
            ->where('gym_id', (int) $hubGym->id)
            ->orderBy('id')
            ->first(['email']);

        $hubEmail = strtolower(trim((string) ($hubUser?->email ?? '')));
        if ($hubEmail === '' || ! str_contains($hubEmail, '@')) {
            $hubEmail = 'admin@gymsystem.app';
        }

        [, $domain] = explode('@', $hubEmail, 2);
        $hubToken = trim(Str::slug((string) ($hubGym->name ?? '')));
        $branchToken = trim(Str::slug($branchName));
        $hubToken = $hubToken !== '' ? str_replace('-', '', $hubToken) : 'gym';
        $branchToken = $branchToken !== '' ? str_replace('-', '', $branchToken) : 'sucursal';
        $candidate = strtolower($hubToken.'.'.$branchToken.'@'.$domain);

        if (User::query()->whereRaw('LOWER(email) = ?', [$candidate])->exists()) {
            throw new InvalidArgumentException('Ese correo ya está en uso, revísalo por favor.');
        }

        return $candidate;
    }

    private function validateCustomBranchAdminEmail(string $email): string
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El correo del administrador de sucursal no es válido.');
        }

        if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw new InvalidArgumentException('Ese correo ya está en uso, revísalo por favor.');
        }

        return $email;
    }
}
