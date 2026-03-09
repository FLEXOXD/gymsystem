<?php

namespace App\Http\Requests;

use App\Support\GymLocationCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()?->gym_id !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'branch_name' => $this->normalizeText($this->input('branch_name')),
            'branch_phone' => $this->normalizeText($this->input('branch_phone')),
            'branch_country' => strtolower($this->normalizeText($this->input('branch_country')) ?? ''),
            'branch_state' => $this->normalizeText($this->input('branch_state')),
            'branch_city' => $this->normalizeText($this->input('branch_city')),
            'branch_address_line' => $this->normalizeText($this->input('branch_address_line')),
            'branch_plan_key' => strtolower($this->normalizeText($this->input('branch_plan_key')) ?? ''),
            'branch_admin_name' => $this->normalizeText($this->input('branch_admin_name')),
            'branch_admin_email' => strtolower($this->normalizeText($this->input('branch_admin_email')) ?? ''),
            'cash_managed_by_hub' => $this->boolean('cash_managed_by_hub'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'branch_name' => ['required', 'string', 'max:120'],
            'branch_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'branch_country' => ['required', Rule::in(array_keys(GymLocationCatalog::catalog()))],
            'branch_state' => ['required', 'string', 'max:120'],
            'branch_city' => [
                'required',
                'string',
                'max:120',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $country = (string) $this->input('branch_country', '');
                    $state = (string) $this->input('branch_state', '');
                    $city = (string) $value;
                    if (! GymLocationCatalog::isValid($country, $state, $city)) {
                        $fail('Selecciona una ciudad válida para el país y provincia/estado elegidos.');
                    }
                },
            ],
            'branch_address_line' => ['nullable', 'string', 'max:180'],
            'branch_plan_key' => ['required', Rule::in(['basico', 'profesional', 'premium'])],
            'branch_admin_name' => ['required', 'string', 'max:120'],
            'branch_admin_email' => ['nullable', 'email', 'max:120', 'unique:users,email'],
            'branch_admin_password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'cash_managed_by_hub' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'branch_name.required' => 'Ingresa el nombre de la sucursal.',
            'branch_phone.regex' => 'El teléfono solo puede contener números, espacios y + - ( ).',
            'branch_country.required' => 'Selecciona el país de la sucursal.',
            'branch_country.in' => 'Selecciona un país válido.',
            'branch_state.required' => 'Selecciona la provincia/estado.',
            'branch_city.required' => 'Selecciona la ciudad.',
            'branch_plan_key.required' => 'Selecciona el plan operativo de la sucursal.',
            'branch_plan_key.in' => 'Plan operativo no válido.',
            'branch_admin_name.required' => 'Ingresa el nombre del usuario de la sucursal.',
            'branch_admin_email.email' => 'Ingresa un correo de usuario válido.',
            'branch_admin_email.unique' => 'Ese correo ya existe en otro usuario.',
            'branch_admin_password.required' => 'Ingresa una contraseña para la sucursal.',
            'branch_admin_password.confirmed' => 'La confirmación de contraseña no coincide.',
            'branch_admin_password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
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

