<?php

namespace App\Http\Requests;

use App\Support\LegalTerms;
use Illuminate\Foundation\Http\FormRequest;

class StoreLegalModalAcceptanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'terms_version' => $this->normalizeText($this->input('terms_version')),
            'location_permission' => $this->normalizeText($this->input('location_permission')) ?? 'skipped',
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'accepted' => ['accepted'],
            'terms_version' => ['required', 'string', 'max:30', 'in:'.LegalTerms::VERSION],
            'location_permission' => ['nullable', 'string', 'in:granted,denied,prompt,unavailable,error,skipped'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_accuracy_m' => ['nullable', 'numeric', 'min:0', 'max:100000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'accepted.accepted' => 'Debes aceptar las condiciones legales para continuar.',
            'terms_version.in' => 'La versión legal no coincide con la vigente.',
        ];
    }

    private function normalizeText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
