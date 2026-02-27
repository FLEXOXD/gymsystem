<?php

namespace App\Http\Controllers;

use App\Support\MarketingContent;
use Illuminate\Support\Arr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuperAdminSiteContentController extends Controller
{
    public function edit(): View
    {
        return view('superadmin.site-content', [
            'content' => MarketingContent::load(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $textRules = [
            'brand_kicker' => ['nullable', 'string', 'max:120'],
            'brand_name' => ['nullable', 'string', 'max:120'],
            'login_button_label' => ['nullable', 'string', 'max:80'],
            'hero_kicker' => ['required', 'string', 'max:120'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['required', 'string', 'max:900'],
            'demo_button_label' => ['required', 'string', 'max:80'],
            'whatsapp_phone' => ['nullable', 'string', 'max:30'],
            'whatsapp_message' => ['nullable', 'string', 'max:500'],
            'hero_media_tag' => ['nullable', 'string', 'max:120'],
            'hero_media_note' => ['nullable', 'string', 'max:180'],
            'section_1_title' => ['nullable', 'string', 'max:255'],
            'section_1_text' => ['nullable', 'string', 'max:900'],
            'section_1_item_1' => ['nullable', 'string', 'max:220'],
            'section_1_item_2' => ['nullable', 'string', 'max:220'],
            'section_1_item_3' => ['nullable', 'string', 'max:220'],
            'section_2_title' => ['nullable', 'string', 'max:255'],
            'section_2_text' => ['nullable', 'string', 'max:900'],
            'section_2_item_1' => ['nullable', 'string', 'max:220'],
            'section_2_item_2' => ['nullable', 'string', 'max:220'],
            'section_2_item_3' => ['nullable', 'string', 'max:220'],
            'section_3_title' => ['nullable', 'string', 'max:255'],
            'section_3_text' => ['nullable', 'string', 'max:900'],
            'section_3_item_1' => ['nullable', 'string', 'max:220'],
            'section_3_item_2' => ['nullable', 'string', 'max:220'],
            'section_3_item_3' => ['nullable', 'string', 'max:220'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'footer_contact_email' => ['nullable', 'string', 'email', 'max:190'],
            'marquee_item_1_text' => ['nullable', 'string', 'max:90'],
            'marquee_item_2_text' => ['nullable', 'string', 'max:90'],
            'marquee_item_3_text' => ['nullable', 'string', 'max:90'],
            'marquee_item_4_text' => ['nullable', 'string', 'max:90'],
            'marquee_item_5_text' => ['nullable', 'string', 'max:90'],
            'marquee_item_6_text' => ['nullable', 'string', 'max:90'],
        ];
        $imageFieldToSetting = [
            'brand_logo_file' => 'brand_logo_path',
            'hero_slide_1_file' => 'hero_slide_1_path',
            'hero_slide_2_file' => 'hero_slide_2_path',
            'hero_slide_3_file' => 'hero_slide_3_path',
            'marquee_item_1_logo_file' => 'marquee_item_1_logo_path',
            'marquee_item_2_logo_file' => 'marquee_item_2_logo_path',
            'marquee_item_3_logo_file' => 'marquee_item_3_logo_path',
            'marquee_item_4_logo_file' => 'marquee_item_4_logo_path',
            'marquee_item_5_logo_file' => 'marquee_item_5_logo_path',
            'marquee_item_6_logo_file' => 'marquee_item_6_logo_path',
            'section_1_image_file' => 'section_1_image_path',
            'section_2_image_file' => 'section_2_image_path',
            'section_3_image_file' => 'section_3_image_path',
        ];

        $rules = $textRules;
        foreach (array_keys($imageFieldToSetting) as $fileField) {
            $rules[$fileField] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'];
        }
        foreach (array_values($imageFieldToSetting) as $settingField) {
            $rules['remove_'.$settingField] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules);
        $payload = Arr::only($validated, array_keys($textRules));
        $currentContent = MarketingContent::load();

        foreach ($imageFieldToSetting as $fileField => $settingField) {
            $removeFlag = $request->boolean('remove_'.$settingField);
            $currentPath = trim((string) ($currentContent[$settingField] ?? ''));
            $hasNewFile = $request->hasFile($fileField);

            if ($removeFlag && ! $hasNewFile) {
                $this->deleteIfLocalPublicFile($currentPath);
                $payload[$settingField] = '';
                continue;
            }

            if (! $hasNewFile) {
                continue;
            }

            $newPath = (string) $request->file($fileField)->store('marketing/site', 'public');
            $payload[$settingField] = $newPath;
            if ($currentPath !== '' && $currentPath !== $newPath) {
                $this->deleteIfLocalPublicFile($currentPath);
            }
        }

        MarketingContent::save($payload);

        return redirect()
            ->route('superadmin.web-page.edit')
            ->with('status', 'Contenido web actualizado correctamente.')
            ->with('allow_superadmin_landing_preview_once', true);
    }

    private function deleteIfLocalPublicFile(string $path): void
    {
        $normalized = trim($path);
        if (
            $normalized === ''
            || str_starts_with($normalized, 'http://')
            || str_starts_with($normalized, 'https://')
            || str_contains($normalized, '..')
        ) {
            return;
        }

        $normalized = str_replace('\\', '/', ltrim($normalized, '/'));
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }
        if (str_starts_with($normalized, 'public/')) {
            $normalized = substr($normalized, 7);
        }
        if ($normalized === '') {
            return;
        }

        Storage::disk('public')->delete($normalized);
    }
}
