<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLegalAcceptanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => $this->normalizeText($this->input('full_name')),
            'email' => strtolower((string) $this->normalizeText($this->input('email'))),
            'document_key' => $this->normalizeText($this->input('document_key')),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'document_key' => ['required', 'in:privacy_policy,service_terms,commercial_terms'],
            'accepted' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Ingresa tu nombre completo para registrar la aceptacion.',
            'email.required' => 'Ingresa tu correo para registrar la aceptacion.',
            'email.email' => 'Ingresa un correo valido.',
            'accepted.accepted' => 'Debes aceptar el documento legal para continuar.',
            'document_key.in' => 'El documento legal seleccionado no es valido.',
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

