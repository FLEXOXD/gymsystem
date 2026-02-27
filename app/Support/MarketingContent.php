<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MarketingContent
{
    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'brand_kicker' => 'Sistema Operativo',
            'brand_name' => 'GymSystem',
            'brand_logo_path' => '',
            'login_button_label' => 'Iniciar sesión',
            'hero_kicker' => 'Software para gimnasios',
            'hero_title' => 'Administra tu gimnasio en un solo sistema, rápido y sin problemas.',
            'hero_subtitle' => 'Gestiona recepción, clientes, membresías, caja y reportes desde una plataforma estable para escritorio y móvil. Ideal para gimnasios individuales o multisucursal.',
            'demo_button_label' => 'Demo gratis',
            'whatsapp_phone' => '593991066303',
            'whatsapp_message' => 'Hola, quiero más información de GymSystem para controlar mi gimnasio.',
            'final_cta_title' => 'Convierte tu operación diaria en un flujo simple y medible.',
            'final_cta_text' => 'Solicita una demo temporal y prueba el sistema real con datos de ejemplo que luego se eliminan.',
            'final_cta_image_path' => '',
            'footer_text' => 'GymSystem {year} | Control operativo para gimnasios',
            'footer_contact_email' => 'soporte@gymsystem.local',
            'hero_panel_left_title' => 'Panel operativo en vivo',
            'hero_panel_right_title' => 'Modo recepción',
            'hero_metric_1_label' => 'Clientes',
            'hero_metric_1_value' => '124',
            'hero_metric_2_label' => 'Activos',
            'hero_metric_2_value' => '87',
            'hero_metric_3_label' => 'Vencen 7 dias',
            'hero_metric_3_value' => '12',
            'hero_metric_4_label' => 'Caja hoy',
            'hero_metric_4_value' => '$ 1,430',
            'hero_media_tag' => 'Marca - Web - Crecimiento',
            'hero_media_note' => 'Carrusel visual en vivo de tu operación',
            'hero_slide_1_path' => '',
            'hero_slide_2_path' => '',
            'hero_slide_3_path' => '',
            'section_1_title' => 'Operación diaria clara para recepción y administración',
            'section_1_text' => 'Todo lo que tu equipo necesita en una sola vista: alta rápida de cliente, cobro inmediato, renovación de membresía y control de caja por turno.',
            'section_1_item_1' => 'Cobros de membresía con control de método de pago',
            'section_1_item_2' => 'Alertas de vencimiento y seguimiento de clientes',
            'section_1_item_3' => 'Reportes de ingresos y asistencia en tiempo real',
            'section_1_image_path' => '',
            'section_1_metric_1_label' => 'Por vencer',
            'section_1_metric_1_value' => '18',
            'section_1_metric_2_label' => 'Vencidos',
            'section_1_metric_2_value' => '6',
            'section_1_metric_3_label' => 'Ingresos hoy',
            'section_1_metric_3_value' => '$ 320',
            'section_1_metric_4_label' => 'Check-ins',
            'section_1_metric_4_value' => '54',
            'section_2_title' => 'Datos separados por gimnasio para evitar cruces',
            'section_2_text' => 'GymSystem trabaja con contexto de gimnasio en cada modulo para garantizar aislamiento de datos, seguridad y control operativo para SuperAdmin.',
            'section_2_item_1' => 'Rutas con contexto de gimnasio y control de acceso',
            'section_2_item_2' => 'Filtros por gym_id en operaciones críticas',
            'section_2_item_3' => 'Auditoría y pruebas de aislamiento multi-tenant',
            'section_2_image_path' => '',
            'section_2_metric_1_label' => 'Control tenant',
            'section_2_metric_1_value' => 'Activo',
            'section_2_metric_2_label' => 'Integridad',
            'section_2_metric_2_value' => 'OK',
            'section_2_metric_3_label' => 'Permisos',
            'section_2_metric_3_value' => 'Por rol',
            'section_2_metric_4_label' => 'Escalabilidad',
            'section_2_metric_4_value' => 'Lista',
            'section_3_title' => 'Experiencia móvil tipo app (PWA) para operar desde cualquier dispositivo',
            'section_3_text' => 'Instalable en celular y escritorio, con interfaz operativa adaptada para recepción y administración diaria.',
            'section_3_item_1' => 'Pantalla de inicio tipo aplicación',
            'section_3_item_2' => 'Caché de assets para carga rápida',
            'section_3_item_3' => 'Base lista para notificaciones y recordatorios',
            'section_3_image_path' => '',
            'section_3_metric_1_label' => 'PWA',
            'section_3_metric_1_value' => 'Instalable',
            'section_3_metric_2_label' => 'Offline base',
            'section_3_metric_2_value' => 'Activo',
            'section_3_metric_3_label' => 'Mobile UX',
            'section_3_metric_3_value' => 'Optimizada',
            'section_3_metric_4_label' => 'Desktop',
            'section_3_metric_4_value' => 'Soportado',
            'marquee_item_1_text' => 'Marcas creativas',
            'marquee_item_1_logo_path' => '',
            'marquee_item_2_text' => 'Empresas emergentes',
            'marquee_item_2_logo_path' => '',
            'marquee_item_3_text' => 'Ampliaciones de escala',
            'marquee_item_3_logo_path' => '',
            'marquee_item_4_text' => 'Fundadores',
            'marquee_item_4_logo_path' => '',
            'marquee_item_5_text' => 'Equipos globales',
            'marquee_item_5_logo_path' => '',
            'marquee_item_6_text' => 'Gym owners',
            'marquee_item_6_logo_path' => '',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function load(): array
    {
        $template = self::defaults();
        $defaults = $template;
        if (! Schema::hasTable('site_settings')) {
            return self::withComputed($defaults);
        }

        $keys = array_keys($defaults);
        $storageKeys = array_map(static fn (string $key): string => 'marketing.'.$key, $keys);
        $stored = SiteSetting::query()
            ->whereIn('key', $storageKeys)
            ->pluck('value', 'key');

        foreach ($keys as $key) {
            $storedValue = $stored->get('marketing.'.$key);
            if ($storedValue !== null && trim((string) $storedValue) !== '') {
                $defaults[$key] = (string) $storedValue;
            }
        }

        $legacyWhatsappUrl = trim((string) SiteSetting::query()
            ->where('key', 'marketing.whatsapp_url')
            ->value('value'));
        if ($legacyWhatsappUrl !== '') {
            $parsed = parse_url($legacyWhatsappUrl);
            $legacyPath = (string) ($parsed['path'] ?? '');
            $legacyPhone = trim((string) preg_replace('/\D+/', '', $legacyPath));
            $legacyMessage = '';
            $legacyQuery = (string) ($parsed['query'] ?? '');
            if ($legacyQuery !== '') {
                parse_str($legacyQuery, $queryParts);
                $legacyMessage = trim((string) ($queryParts['text'] ?? ''));
            }

            $templatePhone = trim((string) ($template['whatsapp_phone'] ?? ''));
            $templateMessage = trim((string) ($template['whatsapp_message'] ?? ''));
            if (trim((string) ($defaults['whatsapp_phone'] ?? '')) === $templatePhone && $legacyPhone !== '') {
                $defaults['whatsapp_phone'] = $legacyPhone;
            }
            if (trim((string) ($defaults['whatsapp_message'] ?? '')) === $templateMessage && $legacyMessage !== '') {
                $defaults['whatsapp_message'] = $legacyMessage;
            }
        }

        return self::withComputed($defaults);
    }

    /**
     * @param array<string, string> $data
     */
    public static function save(array $data): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $allowedKeys = array_fill_keys(array_keys(self::defaults()), true);
        foreach ($data as $key => $value) {
            if (! isset($allowedKeys[$key])) {
                continue;
            }

            SiteSetting::query()->updateOrCreate(
                ['key' => 'marketing.'.$key],
                ['value' => trim((string) $value)]
            );
        }
    }

    /**
     * @param array<string, string> $content
     * @return array<string, string>
     */
    private static function withComputed(array $content): array
    {
        $content['whatsapp_url'] = self::buildWhatsappUrl(
            (string) ($content['whatsapp_phone'] ?? ''),
            (string) ($content['whatsapp_message'] ?? '')
        );
        $content['brand_logo_url'] = self::publicStorageUrl((string) ($content['brand_logo_path'] ?? ''));
        $content['final_cta_image_url'] = self::publicStorageUrl((string) ($content['final_cta_image_path'] ?? ''));
        $content['hero_slide_1_url'] = self::publicStorageUrl((string) ($content['hero_slide_1_path'] ?? ''));
        $content['hero_slide_2_url'] = self::publicStorageUrl((string) ($content['hero_slide_2_path'] ?? ''));
        $content['hero_slide_3_url'] = self::publicStorageUrl((string) ($content['hero_slide_3_path'] ?? ''));
        $content['section_1_image_url'] = self::publicStorageUrl((string) ($content['section_1_image_path'] ?? ''));
        $content['section_2_image_url'] = self::publicStorageUrl((string) ($content['section_2_image_path'] ?? ''));
        $content['section_3_image_url'] = self::publicStorageUrl((string) ($content['section_3_image_path'] ?? ''));
        for ($i = 1; $i <= 6; $i++) {
            $content['marquee_item_'.$i.'_logo_url'] = self::publicStorageUrl((string) ($content['marquee_item_'.$i.'_logo_path'] ?? ''));
        }
        $content['footer_text_resolved'] = str_replace('{year}', now()->format('Y'), (string) ($content['footer_text'] ?? ''));
        $content['brand_initials'] = self::initials((string) ($content['brand_name'] ?? 'GymSystem'));

        return $content;
    }

    private static function buildWhatsappUrl(string $phone, string $message): string
    {
        $normalizedPhone = preg_replace('/\D+/', '', $phone) ?? '';
        $normalizedPhone = trim($normalizedPhone);
        if ($normalizedPhone === '') {
            return '#';
        }

        $base = 'https://wa.me/'.$normalizedPhone;
        $text = trim($message);
        if ($text === '') {
            return $base;
        }

        return $base.'?text='.rawurlencode($text);
    }

    private static function publicStorageUrl(string $path): string
    {
        $normalized = trim($path);
        if ($normalized === '') {
            return '';
        }
        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            return $normalized;
        }
        $normalized = str_replace('\\', '/', ltrim($normalized, '/'));
        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }
        if (str_starts_with($normalized, 'public/')) {
            $normalized = substr($normalized, 7);
        }

        return asset('storage/'.ltrim($normalized, '/'));
    }

    private static function initials(string $brandName): string
    {
        $initials = Str::of($brandName)
            ->trim()
            ->explode(' ')
            ->filter()
            ->map(fn (string $word): string => mb_substr($word, 0, 1))
            ->take(2)
            ->implode('');

        $initials = mb_strtoupper($initials);

        return $initials !== '' ? $initials : 'GS';
    }
}

