<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuperAdminContactRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'support_contact_label' => $this->normalizeText($this->input('support_contact_label')),
            'support_contact_email' => strtolower($this->normalizeText($this->input('support_contact_email'))),
            'support_contact_phone' => $this->normalizeText($this->input('support_contact_phone')),
            'support_contact_whatsapp' => $this->normalizeText($this->input('support_contact_whatsapp')),
            'support_contact_link' => $this->normalizeText($this->input('support_contact_link')),
            'support_contact_message' => $this->normalizeText($this->input('support_contact_message')),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->gym_id === null;
    }

    public function rules(): array
    {
        return [
            'support_contact_label' => ['nullable', 'string', 'max:120'],
            'support_contact_email' => ['nullable', 'email', 'max:120'],
            'support_contact_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'support_contact_whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'support_contact_link' => ['nullable', 'url', 'max:255'],
            'support_contact_message' => ['nullable', 'string', 'max:500'],
            'support_contact_logo_light' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'support_contact_logo_dark' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'support_contact_phone.regex' => 'El formato del telefono no es valido.',
            'support_contact_whatsapp.regex' => 'El formato del WhatsApp no es valido.',
            'support_contact_link.url' => 'El enlace debe ser una URL valida.',
            'support_contact_logo_light.image' => 'El logo para tema claro debe ser una imagen valida.',
            'support_contact_logo_light.mimes' => 'El logo para tema claro debe ser JPG, PNG o WEBP.',
            'support_contact_logo_light.max' => 'El logo para tema claro no puede superar 4MB.',
            'support_contact_logo_dark.image' => 'El logo para tema oscuro debe ser una imagen valida.',
            'support_contact_logo_dark.mimes' => 'El logo para tema oscuro debe ser JPG, PNG o WEBP.',
            'support_contact_logo_dark.max' => 'El logo para tema oscuro no puede superar 4MB.',
        ];
    }

    private function normalizeText(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }
}
