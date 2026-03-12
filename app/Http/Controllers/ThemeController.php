<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGymAvatarsRequest;
use App\Http\Requests\UpdateGymLogoRequest;
use App\Http\Requests\UpdateGymProfileRequest;
use App\Http\Requests\UpdateSuperAdminContactRequest;
use App\Http\Requests\UpdateSuperAdminTimezoneRequest;
use App\Http\Requests\UpdateThemeRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\LogoutOtherDevicesRequest;
use App\Models\Gym;
use App\Models\Subscription;
use App\Models\User;
use App\Support\Currency;
use App\Support\GymLocationCatalog;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ThemeController extends Controller
{
    /**
     * Available panel themes and display metadata.
     *
     * @return array<string, array<string, string>>
     */
    private function themes(): array
    {
        return [
            'iron_dark' => [
                'name' => 'IRON DARK',
                'bg' => '#0B0F19',
                'sidebar' => '#111827',
                'primary' => '#2563EB',
                'accent' => '#06B6D4',
            ],
            'power_red' => [
                'name' => 'POWER RED',
                'bg' => '#0F0F0F',
                'sidebar' => '#1A1A1A',
                'primary' => '#DC2626',
                'accent' => '#EF4444',
            ],
            'energy_green' => [
                'name' => 'ENERGY GREEN',
                'bg' => '#0B1215',
                'sidebar' => '#111827',
                'primary' => '#16A34A',
                'accent' => '#22C55E',
            ],
            'gold_elite' => [
                'name' => 'GOLD ELITE',
                'bg' => '#0A0A0A',
                'sidebar' => '#171717',
                'primary' => '#D4AF37',
                'accent' => '#F59E0B',
            ],
            'light_emerald' => [
                'name' => 'LIGHT EMERALD',
                'bg' => '#F7FBF8',
                'sidebar' => '#123524',
                'primary' => '#166534',
                'accent' => '#10B981',
            ],
            'light_coffee' => [
                'name' => 'LIGHT COFFEE',
                'bg' => '#FCF9F5',
                'sidebar' => '#3D2B1F',
                'primary' => '#6F4E37',
                'accent' => '#A16207',
            ],
            'light_navy' => [
                'name' => 'LIGHT NAVY',
                'bg' => '#F6F8FC',
                'sidebar' => '#102A43',
                'primary' => '#1D4ED8',
                'accent' => '#0EA5E9',
            ],
            'light_onyx' => [
                'name' => 'LIGHT ONYX',
                'bg' => '#F8FAFC',
                'sidebar' => '#111111',
                'primary' => '#1F2937',
                'accent' => '#111827',
            ],
        ];
    }

    /**
     * Show the settings screen with the theme selector.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $gym = $this->resolveGymForContext($request);
        $themes = $this->themes();
        $currentTheme = $user?->theme;

        if (! isset($themes[$currentTheme])) {
            $currentTheme = 'iron_dark';
        }

        return view('admin.settings.theme-selector', [
            'themes' => $themes,
            'currentTheme' => $currentTheme,
            'languageOptions' => $this->languageOptions(),
            'gym' => $gym,
            'currencyOptions' => Currency::options(),
            'locationCatalog' => GymLocationCatalog::catalog(),
            'timezoneOptions' => $this->timezoneOptions(),
        ]);
    }

    /**
     * Show the profile screen in a dedicated window.
     */
    public function profile(Request $request): View
    {
        $user = $request->user();
        $subscription = $user?->gym?->latestSubscription;
        $membershipSummary = $this->buildMembershipSummary($subscription);
        $membershipInvoices = $this->buildMembershipInvoices($subscription);

        return view('admin.settings.profile', [
            'userRoleLabel' => $this->userRoleLabel($user),
            'phoneCountryOptions' => $this->phoneCountryOptions(),
            'membershipSummary' => $membershipSummary,
            'membershipInvoices' => $membershipInvoices,
        ]);
    }

    /**
     * Show the support contact screen.
     */
    public function contact(Request $request): View
    {
        $viewer = $request->user();
        $contactOwner = $this->resolveSupportContactOwner($viewer);
        $contactData = $this->buildSupportContactData($contactOwner);

        return view('contact.index', [
            'contactOwner' => $contactOwner,
            'contactData' => $contactData,
            'isSuperAdminViewer' => $viewer?->gym_id === null,
        ]);
    }

    /**
     * Show suspended subscription page with SuperAdmin support branding.
     */
    public function subscriptionExpired(Request $request): View
    {
        $viewer = $request->user();
        $contactOwner = $this->resolveSupportContactOwner($viewer);
        $contactData = $this->buildSupportContactData($contactOwner);
        $gymSlug = trim((string) ($viewer?->gym?->slug ?? ''));
        $updateUrl = $gymSlug !== ''
            ? route('panel.index', ['contextGym' => $gymSlug])
            : route('superadmin.dashboard');
        $whatsappUrl = $this->buildWhatsappUrl((string) ($contactData['whatsapp'] ?? ''));

        return view('subscription.expired', [
            'contactData' => $contactData,
            'updateUrl' => $updateUrl,
            'gymName' => (string) ($viewer?->gym?->name ?? 'Gym'),
            'nowLabel' => now()->format('Y-m-d H:i'),
            'whatsappUrl' => $whatsappUrl,
        ]);
    }

    /**
     * Update the authenticated user's selected theme.
     */
    public function update(UpdateThemeRequest $request): JsonResponse
    {
        $user = $request->user();
        $theme = $request->validated('theme');

        $user->forceFill([
            'theme' => $theme,
        ])->save();

        return response()->json([
            'ok' => true,
            'theme' => $theme,
        ]);
    }

    /**
     * Update only the authenticated user's profile photo.
     */
    public function updateOwnProfilePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, __('messages.user_not_authenticated'));

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoPath = $request->file('profile_photo')?->store('users/profiles', 'public');
        if (! is_string($photoPath) || trim($photoPath) === '') {
            return back()->with('error', 'No se pudo guardar la imagen de perfil.');
        }

        $this->deletePublicAssetIfLocal($user->profile_photo_path);
        $user->forceFill([
            'profile_photo_path' => $photoPath,
        ])->save();

        return back()->with('status', 'Foto de perfil actualizada correctamente.');
    }

    /**
     * Update gym business details for the authenticated user.
     */
    public function updateGymProfile(UpdateGymProfileRequest $request): RedirectResponse
    {
        $gym = $this->resolveGymForContext($request);

        abort_if(! $gym, 403, __('messages.user_without_gym'));

        $data = $request->validated();
        $countryCode = strtolower(trim((string) ($data['address_country_code'] ?? '')));
        $state = trim((string) ($data['address_state'] ?? ''));
        $city = trim((string) ($data['address_city'] ?? ''));
        $line = trim((string) ($data['address_line'] ?? ''));

        $resolvedState = $state !== '' && $countryCode !== ''
            ? (GymLocationCatalog::resolveState($countryCode, $state) ?? $state)
            : null;
        $resolvedCity = $city !== '' && $countryCode !== '' && $resolvedState !== null
            ? (GymLocationCatalog::resolveCity($countryCode, $resolvedState, $city) ?? $city)
            : null;
        $countryName = $countryCode !== ''
            ? (string) (GymLocationCatalog::catalog()[$countryCode]['label'] ?? strtoupper($countryCode))
            : null;
        $addressLine = $line !== '' ? $line : null;

        $gym->update([
            'name' => (string) $data['name'],
            'phone' => $data['phone'] ?? null,
            'currency_code' => (string) $data['currency_code'],
            'language_code' => (string) $data['language_code'],
            'timezone' => (string) $data['timezone'],
            'address_country_code' => $countryCode !== '' ? $countryCode : null,
            'address_country_name' => $countryName,
            'address_state' => $resolvedState,
            'address_city' => $resolvedCity,
            'address_line' => $addressLine,
            'address' => $countryCode !== '' && $resolvedState !== null && $resolvedCity !== null
                ? GymLocationCatalog::buildAddress($countryCode, $resolvedState, $resolvedCity, $addressLine)
                : $addressLine,
        ]);

        return back()->with('status', __('messages.gym_profile_updated'));
    }

    /**
     * Update user profile details.
     */
    public function updateProfile(UpdateUserProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user, 403, __('messages.user_not_authenticated'));
        abort_if($user->gym_id !== null, 403, 'Solo SuperAdmin puede editar este perfil.');

        $data = $request->validated();
        $photoPath = $user->profile_photo_path;
        if ($request->hasFile('user_profile_photo')) {
            $photoPath = $request->file('user_profile_photo')->store('users/profiles', 'public');
            $this->deletePublicAssetIfLocal($user->profile_photo_path);
        }

        $user->forceFill([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'country_iso' => $data['user_country_iso'],
            'country_name' => $data['user_country_name'],
            'gender' => $data['user_gender'] ?? null,
            'birth_date' => $data['user_birth_date'] ?? null,
            'identification_type' => $data['user_identification_type'] ?? null,
            'identification_number' => $data['user_identification_number'] ?? null,
            'phone_country_iso' => $data['user_phone_country_iso'],
            'phone_country_dial' => $data['user_phone_country_dial'],
            'phone_number' => $data['user_phone_number'],
            'profile_photo_path' => $photoPath,
        ])->save();

        return back()->with('status', __('messages.profile_updated'));
    }

    /**
     * Update support contact data shown in "Contactarse".
     */
    public function updateSuperAdminContact(UpdateSuperAdminContactRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user || $user->gym_id !== null, 403, __('messages.user_not_authenticated'));
        if (! $this->supportContactColumnsExist()) {
            return back()->with('error', 'Falta actualizar base de datos. Ejecuta: php artisan migrate');
        }
        $data = $request->validated();
        unset($data['support_contact_logo_light'], $data['support_contact_logo_dark']);

        $hasLightLogo = $request->hasFile('support_contact_logo_light');
        $hasDarkLogo = $request->hasFile('support_contact_logo_dark');
        if ($hasLightLogo || $hasDarkLogo) {
            if (! $this->supportContactThemeLogoColumnsExist()) {
                return back()->with('error', 'Falta actualizar base de datos para los logos claro/oscuro. Ejecuta: php artisan migrate');
            }
        }

        if ($hasLightLogo) {
            $data['support_contact_logo_light_path'] = $request->file('support_contact_logo_light')->store('support/contact', 'public');
            $this->deletePublicAssetIfLocal((string) ($user->support_contact_logo_light_path ?? ''));
        }

        if ($hasDarkLogo) {
            $data['support_contact_logo_dark_path'] = $request->file('support_contact_logo_dark')->store('support/contact', 'public');
            $this->deletePublicAssetIfLocal((string) ($user->support_contact_logo_dark_path ?? ''));
        }

        $user->forceFill($data)->save();

        return back()->with('status', __('messages.superadmin_contact_updated'));
    }

    /**
     * Update preferred timezone for SuperAdmin screens.
     */
    public function updateSuperAdminTimezone(UpdateSuperAdminTimezoneRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user || $user->gym_id !== null, 403, __('messages.user_not_authenticated'));
        if (! Schema::hasColumn('users', 'timezone')) {
            return back()->with('error', 'Falta actualizar base de datos. Ejecuta: php artisan migrate');
        }

        $user->forceFill([
            'timezone' => (string) $request->validated('superadmin_timezone'),
        ])->save();

        return back()->with('status', 'Zona horaria de SuperAdmin actualizada.');
    }

    /**
     * Update user password.
     */
    public function updateProfilePassword(UpdateUserPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, __('messages.user_not_authenticated'));

        $user->forceFill([
            'password' => Hash::make((string) $request->validated('password')),
        ])->save();

        return back()->with('status', __('messages.password_updated'));
    }

    /**
     * Close all other active sessions for current user.
     */
    public function logoutOtherDevices(LogoutOtherDevicesRequest $request): RedirectResponse
    {
        $user = $request->user();
        abort_if(! $user, 403, __('messages.user_not_authenticated'));

        Auth::logoutOtherDevices((string) $request->validated('current_password'));

        if (config('session.driver') === 'database') {
            DB::table('sessions')
                ->where('user_id', (int) $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return back()->with('status', __('messages.other_sessions_closed'));
    }

    /**
     * Download membership invoice summary PDF for current gym.
     */
    public function membershipInvoicePdf(
        Request $request,
        string|int $contextGymOrSubscription,
        ?int $subscription = null
    ): Response|RedirectResponse
    {
        $user = $request->user();
        $gym = $this->resolveGymForContext($request);
        $gymId = (int) ($gym?->id ?? 0);
        abort_if($gymId === 0, 403, __('messages.user_without_gym'));

        if ($subscription === null) {
            $subscription = (int) $contextGymOrSubscription;
        }

        $subscriptionModel = Subscription::query()
            ->where('gym_id', $gymId)
            ->findOrFail($subscription);

        $startsAt = $subscriptionModel->starts_at?->copy();
        $endsAt = $subscriptionModel->ends_at?->copy();
        $periodLabel = __('ui.profile.membership_period_na');
        if ($startsAt && $endsAt) {
            $periodLabel = $startsAt->format('Y-m-d').' - '.$endsAt->format('Y-m-d');
        }

        $invoiceData = [
            'period' => $periodLabel,
            'amount' => (float) ($subscriptionModel->price ?? 0),
            'status' => in_array((string) $subscriptionModel->status, ['active', 'grace'], true) ? 'paid' : 'pending',
            'payment_method' => (string) ($subscriptionModel->last_payment_method ?? ''),
            'recorded_at' => $subscriptionModel->updated_at,
            'plan_name' => (string) ($subscriptionModel->plan_name ?? 'Plan'),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];

        $pdf = Pdf::loadView('admin.settings.membership-invoice-pdf', [
            'gymName' => (string) ($gym?->name ?? 'GymSystem'),
            'userName' => (string) ($user?->name ?? ''),
            'invoice' => $invoiceData,
            'currencyCode' => (string) ($gym?->currency_code ?? 'USD'),
        ])->setPaper('a4', 'portrait');

        $filename = 'membership_invoice_'.$subscriptionModel->id.'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Update gym logo for the authenticated user.
     */
    public function updateGymLogo(UpdateGymLogoRequest $request): RedirectResponse
    {
        $gym = $this->resolveGymForContext($request);

        abort_if(! $gym, 403, __('messages.user_without_gym'));

        $file = $request->file('logo');
        $newPath = $file->store('gyms/logos', 'public');

        $oldPath = $gym->logo_path;
        $this->deletePublicAssetIfLocal($oldPath);

        $gym->update([
            'logo_path' => $newPath,
        ]);

        return back()->with('status', __('messages.logo_updated'));
    }

    /**
     * Update gym fallback avatars by gender.
     */
    public function updateGymAvatars(UpdateGymAvatarsRequest $request): RedirectResponse
    {
        $gym = $this->resolveGymForContext($request);

        abort_if(! $gym, 403, __('messages.user_without_gym'));

        $inputs = [
            'avatar_male' => 'avatar_male_path',
            'avatar_female' => 'avatar_female_path',
            'avatar_neutral' => 'avatar_neutral_path',
        ];

        $updates = [];
        foreach ($inputs as $inputName => $columnName) {
            if (! $request->hasFile($inputName)) {
                continue;
            }

            $newPath = $request->file($inputName)->store('gyms/avatars', 'public');
            $this->deletePublicAssetIfLocal($gym->{$columnName});
            $updates[$columnName] = $newPath;
        }

        if ($updates !== []) {
            $gym->update($updates);
        }

        return back()->with('status', __('messages.avatars_updated'));
    }

    private function resolveGymForContext(Request $request): ?Gym
    {
        $contextGymSlug = trim((string) ($request->attributes->get('active_gym_slug') ?? $request->route('contextGym') ?? ''));
        if ($contextGymSlug !== '') {
            $contextGym = Gym::query()
                ->withoutDemoSessions()
                ->whereRaw('LOWER(slug) = ?', [mb_strtolower($contextGymSlug)])
                ->first();

            if ($contextGym instanceof Gym) {
                return $contextGym;
            }
        }

        $activeGym = $request->attributes->get('active_gym');
        if ($activeGym instanceof Gym) {
            return $activeGym;
        }

        return $request->user()?->gym;
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
     * @return array<string, string>
     */
    private function languageOptions(): array
    {
        return [
            'es' => '🇪🇸 Español',
            'en' => '🇺🇸 English',
            'pt' => '🇧🇷 Português (Brasil)',
        ];
    }

    /**
     * @return array<int, array{iso:string,dial:string,flag:string,name:string}>
     */
    private function phoneCountryOptions(): array
    {
        return [
            ['iso' => 'EC', 'dial' => '+593', 'flag' => '🇪🇨', 'name' => 'Ecuador'],
            ['iso' => 'CO', 'dial' => '+57', 'flag' => '🇨🇴', 'name' => 'Colombia'],
            ['iso' => 'PE', 'dial' => '+51', 'flag' => '🇵🇪', 'name' => 'Perú'],
            ['iso' => 'MX', 'dial' => '+52', 'flag' => '🇲🇽', 'name' => 'México'],
            ['iso' => 'US', 'dial' => '+1', 'flag' => '🇺🇸', 'name' => 'Estados Unidos'],
            ['iso' => 'ES', 'dial' => '+34', 'flag' => '🇪🇸', 'name' => 'España'],
            ['iso' => 'BR', 'dial' => '+55', 'flag' => '🇧🇷', 'name' => 'Brasil'],
            ['iso' => 'AR', 'dial' => '+54', 'flag' => '🇦🇷', 'name' => 'Argentina'],
            ['iso' => 'CL', 'dial' => '+56', 'flag' => '🇨🇱', 'name' => 'Chile'],
            ['iso' => 'BO', 'dial' => '+591', 'flag' => '🇧🇴', 'name' => 'Bolivia'],
            ['iso' => 'PA', 'dial' => '+507', 'flag' => '🇵🇦', 'name' => 'Panamá'],
            ['iso' => 'GT', 'dial' => '+502', 'flag' => '🇬🇹', 'name' => 'Guatemala'],
            ['iso' => 'PY', 'dial' => '+595', 'flag' => '🇵🇾', 'name' => 'Paraguay'],
            ['iso' => 'UY', 'dial' => '+598', 'flag' => '🇺🇾', 'name' => 'Uruguay'],
            ['iso' => 'VE', 'dial' => '+58', 'flag' => '🇻🇪', 'name' => 'Venezuela'],
            ['iso' => 'PT', 'dial' => '+351', 'flag' => '🇵🇹', 'name' => 'Portugal'],
            ['iso' => 'DO', 'dial' => '+1', 'flag' => '🇩🇴', 'name' => 'República Dominicana'],
        ];
    }

    private function userRoleLabel(?\App\Models\User $user): string
    {
        if (! $user) {
            return __('ui.guest');
        }

        if ($user->gym_id === null) {
            return 'SuperAdmin';
        }

        return __('ui.gym_admin');
    }

    private function resolveSupportContactOwner(?User $viewer): ?User
    {
        if ($viewer && $viewer->gym_id === null) {
            return $viewer;
        }

        $superAdminQuery = User::query()->whereNull('gym_id');
        if (! $this->supportContactColumnsExist()) {
            return $superAdminQuery->orderBy('id')->first();
        }

        $configured = (clone $superAdminQuery)
            ->where(function ($query): void {
                $query
                    ->whereNotNull('support_contact_email')
                    ->orWhereNotNull('support_contact_phone')
                    ->orWhereNotNull('support_contact_whatsapp')
                    ->orWhereNotNull('support_contact_link')
                    ->orWhereNotNull('support_contact_message');
                if ($this->supportContactThemeLogoColumnsExist()) {
                    $query
                        ->orWhereNotNull('support_contact_logo_light_path')
                        ->orWhereNotNull('support_contact_logo_dark_path');
                } elseif ($this->supportContactLogoColumnExists()) {
                    $query->orWhereNotNull('support_contact_logo_path');
                }
            })
            ->orderBy('id')
            ->first();

        return $configured ?? $superAdminQuery->orderBy('id')->first();
    }

    private function supportContactColumnsExist(): bool
    {
        return Schema::hasColumns('users', [
            'support_contact_label',
            'support_contact_email',
            'support_contact_phone',
            'support_contact_whatsapp',
            'support_contact_link',
            'support_contact_message',
        ]);
    }

    private function supportContactLogoColumnExists(): bool
    {
        return Schema::hasColumn('users', 'support_contact_logo_path');
    }

    private function supportContactThemeLogoColumnsExist(): bool
    {
        return Schema::hasColumns('users', [
            'support_contact_logo_light_path',
            'support_contact_logo_dark_path',
        ]);
    }

    /**
     * @return array{
     *   label:string,
     *   email:string,
     *   phone:string,
     *   whatsapp:string,
     *   link:string,
     *   message:string,
     *   logo_light_url:string,
     *   logo_dark_url:string
     * }
     */
    private function buildSupportContactData(?User $contactOwner): array
    {
        $fallbackPhone = '';
        if ($contactOwner) {
            $fallbackPhone = trim(((string) ($contactOwner->phone_country_dial ?? '')).' '.((string) ($contactOwner->phone_number ?? '')));
        }
        $logoLightUrl = '';
        $logoDarkUrl = '';
        if ($this->supportContactThemeLogoColumnsExist()) {
            $logoLightUrl = $this->resolvePublicAssetUrl((string) ($contactOwner?->support_contact_logo_light_path ?? ''));
            $logoDarkUrl = $this->resolvePublicAssetUrl((string) ($contactOwner?->support_contact_logo_dark_path ?? ''));
        }
        if ($logoLightUrl === '' && $logoDarkUrl === '' && $this->supportContactLogoColumnExists()) {
            $legacyLogoUrl = $this->resolvePublicAssetUrl((string) ($contactOwner?->support_contact_logo_path ?? ''));
            $logoLightUrl = $legacyLogoUrl;
            $logoDarkUrl = $legacyLogoUrl;
        }

        return [
            'label' => trim((string) ($contactOwner?->support_contact_label ?? '')) !== ''
                ? (string) $contactOwner?->support_contact_label
                : (string) ($contactOwner?->name ?? 'Soporte'),
            'email' => (string) ($contactOwner?->support_contact_email ?: $contactOwner?->email ?: 'soporte@gymsystem.app'),
            'phone' => (string) ($contactOwner?->support_contact_phone ?: $fallbackPhone),
            'whatsapp' => (string) ($contactOwner?->support_contact_whatsapp ?: $fallbackPhone),
            'link' => (string) ($contactOwner?->support_contact_link ?? ''),
            'message' => (string) ($contactOwner?->support_contact_message ?? 'Escríbenos y te ayudamos con tu consulta.'),
            'logo_light_url' => $logoLightUrl,
            'logo_dark_url' => $logoDarkUrl,
        ];
    }

    private function resolvePublicAssetUrl(?string $path): string
    {
        $assetPath = trim((string) $path);
        if ($assetPath === '') {
            return '';
        }
        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $assetPath) === 1) {
            return '';
        }
        if (str_starts_with($assetPath, '/tmp/') || str_starts_with($assetPath, 'tmp/')) {
            return '';
        }
        if (str_starts_with($assetPath, 'http://') || str_starts_with($assetPath, 'https://')) {
            return $assetPath;
        }

        $relativePath = ltrim($assetPath, '/');
        if (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, 8);
        }
        if ($relativePath === '' || str_contains($relativePath, '..')) {
            return '';
        }
        if (! Storage::disk('public')->exists($relativePath)) {
            return '';
        }

        return asset('storage/'.$relativePath);
    }

    private function buildWhatsappUrl(string $rawPhone): ?string
    {
        $digits = preg_replace('/\D+/', '', $rawPhone);
        if (! is_string($digits) || $digits === '') {
            return null;
        }

        return 'https://wa.me/'.$digits;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildMembershipSummary(?Subscription $subscription): ?array
    {
        if (! $subscription) {
            return null;
        }

        $today = now()->startOfDay();
        $endsAt = $subscription->ends_at?->copy()->startOfDay();
        $startsAt = $subscription->starts_at?->copy()->startOfDay();
        $remainingDays = $endsAt ? $today->diffInDays($endsAt, false) : null;

        return [
            'plan_name' => (string) ($subscription->plan_name ?? 'Plan Mensual'),
            'status' => (string) ($subscription->status ?? 'active'),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'remaining_days' => $remainingDays,
            'price' => (float) ($subscription->price ?? 0),
            'payment_method' => (string) ($subscription->last_payment_method ?? ''),
            'updated_at' => $subscription->updated_at,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildMembershipInvoices(?Subscription $subscription): array
    {
        if (! $subscription) {
            return [];
        }

        $startsAt = $subscription->starts_at?->copy();
        $endsAt = $subscription->ends_at?->copy();
        $periodLabel = __('ui.profile.membership_period_na');
        if ($startsAt && $endsAt) {
            $periodLabel = $startsAt->format('Y-m-d').' - '.$endsAt->format('Y-m-d');
        }

        return [[
            'id' => (int) $subscription->id,
            'period' => $periodLabel,
            'amount' => (float) ($subscription->price ?? 0),
            'status' => in_array((string) $subscription->status, ['active', 'grace'], true) ? 'paid' : 'pending',
            'payment_method' => (string) ($subscription->last_payment_method ?? ''),
            'recorded_at' => $subscription->updated_at,
        ]];
    }
}

