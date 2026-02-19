<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGymAvatarsRequest;
use App\Http\Requests\UpdateGymLogoRequest;
use App\Http\Requests\UpdateGymProfileRequest;
use App\Http\Requests\UpdateThemeRequest;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
        ];
    }

    /**
     * Show the settings screen with the theme selector.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $themes = $this->themes();
        $currentTheme = $user?->theme;

        if (! isset($themes[$currentTheme])) {
            $currentTheme = 'iron_dark';
        }

        return view('admin.settings.theme-selector', [
            'themes' => $themes,
            'currentTheme' => $currentTheme,
            'gym' => $user?->gym,
            'timezoneOptions' => $this->timezoneOptions(),
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
     * Update gym business details for the authenticated user.
     */
    public function updateGymProfile(UpdateGymProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $gym = $user?->gym;

        abort_if(! $gym, 403, 'Este usuario no tiene gimnasio asignado.');

        $gym->update($request->validated());

        return back()->with('status', 'Datos del gym actualizados correctamente.');
    }

    /**
     * Update gym logo for the authenticated user.
     */
    public function updateGymLogo(UpdateGymLogoRequest $request): RedirectResponse
    {
        $user = $request->user();
        $gym = $user?->gym;

        abort_if(! $gym, 403, 'Este usuario no tiene gimnasio asignado.');

        $file = $request->file('logo');
        $newPath = $file->store('gyms/logos', 'public');

        $oldPath = $gym->logo_path;
        $this->deletePublicAssetIfLocal($oldPath);

        $gym->update([
            'logo_path' => $newPath,
        ]);

        return back()->with('status', 'Logo actualizado correctamente.');
    }

    /**
     * Update gym fallback avatars by gender.
     */
    public function updateGymAvatars(UpdateGymAvatarsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $gym = $user?->gym;

        abort_if(! $gym, 403, 'Este usuario no tiene gimnasio asignado.');

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

        return back()->with('status', 'Avatares actualizados correctamente.');
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
}
