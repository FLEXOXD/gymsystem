<?php

namespace App\Http\Requests;

use App\Support\Currency;
use App\Support\GymLocationCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGymRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()?->gym_id === null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'gym_name' => $this->normalizeText($this->input('gym_name')),
            'gym_phone' => $this->normalizeText($this->input('gym_phone')),
            'gym_address_country' => strtolower($this->normalizeText($this->input('gym_address_country')) ?? ''),
            'gym_address_state' => $this->normalizeText($this->input('gym_address_state')),
            'gym_address_city' => $this->normalizeText($this->input('gym_address_city')),
            'gym_address_line' => $this->normalizeText($this->input('gym_address_line')),
            'gym_timezone' => $this->normalizeText($this->input('gym_timezone')),
            'gym_currency_code' => strtoupper($this->normalizeText($this->input('gym_currency_code')) ?? ''),
            'gym_language_code' => strtolower($this->normalizeText($this->input('gym_language_code')) ?? ''),
            'admin_name' => $this->normalizeText($this->input('admin_name')),
            'admin_email' => strtolower($this->normalizeText($this->input('admin_email')) ?? ''),
            'admin_gender' => ($gender = strtolower($this->normalizeText($this->input('admin_gender')) ?? '')) !== '' ? $gender : null,
            'admin_identification_type' => ($idType = strtolower($this->normalizeText($this->input('admin_identification_type')) ?? '')) !== '' ? $idType : null,
            'admin_identification_number' => ($idNumber = strtoupper($this->normalizeText($this->input('admin_identification_number')) ?? '')) !== '' ? $idNumber : null,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'gym_name' => ['required', 'string', 'max:120'],
            'gym_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'gym_address_country' => ['required', Rule::in(array_keys(GymLocationCatalog::catalog()))],
            'gym_address_state' => ['required', 'string', 'max:120'],
            'gym_address_city' => [
                'required',
                'string',
                'max:120',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $country = (string) $this->input('gym_address_country', '');
                    $state = (string) $this->input('gym_address_state', '');
                    $city = (string) $value;
                    if (! GymLocationCatalog::isValid($country, $state, $city)) {
                        $fail('Selecciona una ciudad valida para el pais y provincia/estado elegidos.');
                    }
                },
            ],
            'gym_address_line' => ['nullable', 'string', 'max:120'],
            'gym_timezone' => [
                'required',
                'string',
                'max:64',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $timezone = (string) $value;
                    if (! in_array($timezone, timezone_identifiers_list(), true)) {
                        $fail('La zona horaria no es valida.');
                    }
                },
            ],
            'gym_currency_code' => ['required', 'string', Rule::in(array_keys(Currency::options()))],
            'gym_language_code' => ['required', 'string', Rule::in(['es', 'en', 'pt'])],
            'admin_name' => ['required', 'string', 'max:120'],
            'admin_email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'admin_gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer_not_say'])],
            'admin_identification_type' => ['nullable', 'string', Rule::in(['cedula', 'dni', 'passport'])],
            'admin_identification_number' => ['nullable', 'string', 'min:4', 'max:30', 'regex:/^[A-Z0-9\-]+$/'],
            'admin_password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_name.required' => 'Ingresa el nombre del gimnasio.',
            'gym_phone.regex' => 'El telefono solo puede contener numeros, espacios y + - ( ).',
            'gym_address_country.required' => 'Selecciona el pais del gimnasio.',
            'gym_address_country.in' => 'Selecciona un pais valido.',
            'gym_address_state.required' => 'Selecciona la provincia/estado.',
            'gym_address_city.required' => 'Selecciona la ciudad.',
            'gym_currency_code.in' => 'Selecciona una moneda valida.',
            'gym_language_code.in' => 'Selecciona un idioma valido.',
            'admin_email.unique' => 'Ese correo ya esta registrado en otro usuario.',
            'admin_gender.in' => 'Selecciona un genero valido.',
            'admin_identification_type.in' => 'Selecciona un tipo de identificacion valido.',
            'admin_identification_number.regex' => 'El numero de identificacion solo puede contener letras, numeros y guion.',
            'admin_password.confirmed' => 'La confirmacion de contrasena no coincide.',
            'admin_password.min' => 'La contrasena debe tener minimo 8 caracteres.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $identificationType = (string) ($this->input('admin_identification_type') ?? '');
            $identificationNumber = (string) ($this->input('admin_identification_number') ?? '');

            if ($identificationType !== '' && $identificationNumber === '') {
                $validator->errors()->add('admin_identification_number', 'Ingresa la cedula o identificacion.');
            }

            if ($identificationType === '' && $identificationNumber !== '') {
                $validator->errors()->add('admin_identification_type', 'Selecciona el tipo de identificacion.');
            }
        });
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
