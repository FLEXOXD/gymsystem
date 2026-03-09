<?php

namespace App\Http\Requests;

use App\Support\Currency;
use App\Support\GymLocationCatalog;
use App\Support\SuperAdminPlanCatalog;
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
            'admin_birth_date' => ($birthDate = trim((string) $this->input('admin_birth_date'))) !== '' ? $birthDate : null,
            'admin_identification_type' => ($idType = strtolower($this->normalizeText($this->input('admin_identification_type')) ?? '')) !== '' ? $idType : null,
            'admin_identification_number' => ($idNumber = strtoupper($this->normalizeText($this->input('admin_identification_number')) ?? '')) !== '' ? $idNumber : null,
            'admin_phone_country_dial' => ($dial = trim((string) $this->input('admin_phone_country_dial'))) !== '' ? $dial : null,
            'admin_phone_number' => ($phone = preg_replace('/\D+/', '', (string) $this->input('admin_phone_number'))) !== '' ? $phone : null,
            'subscription_plan_template_id' => ($templateId = trim((string) $this->input('subscription_plan_template_id'))) !== '' ? (int) $templateId : null,
            'subscription_custom_price' => ($customPrice = trim((string) $this->input('subscription_custom_price'))) !== '' ? (float) $customPrice : null,
            'subscription_apply_intro_50' => filter_var($this->input('subscription_apply_intro_50'), FILTER_VALIDATE_BOOLEAN),
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
                        $fail('Selecciona una ciudad válida para el país y provincia/estado elegidos.');
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
                        $fail('La zona horaria no es válida.');
                    }
                },
            ],
            'gym_currency_code' => ['required', 'string', Rule::in(array_keys(Currency::options()))],
            'gym_language_code' => ['required', 'string', Rule::in(['es', 'en', 'pt'])],
            'admin_name' => ['required', 'string', 'max:120'],
            'admin_email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'admin_gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer_not_say'])],
            'admin_birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'admin_identification_type' => ['nullable', 'string', Rule::in(['cédula', 'dni', 'passport'])],
            'admin_identification_number' => ['nullable', 'string', 'min:4', 'max:30', 'regex:/^[A-Z0-9\-]+$/'],
            'admin_phone_country_dial' => ['nullable', 'string', 'max:8', 'regex:/^\+\d{1,4}$/'],
            'admin_phone_number' => ['nullable', 'string', 'min:6', 'max:15', 'regex:/^\d+$/'],
            'admin_profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
            'admin_password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'subscription_plan_template_id' => [
                'required',
                'integer',
                Rule::exists('superadmin_plan_templates', 'id')->where(function ($query): void {
                    $query
                        ->where('status', 'active')
                        ->whereIn('plan_key', SuperAdminPlanCatalog::keys());
                }),
            ],
            'subscription_custom_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'subscription_apply_intro_50' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_name.required' => 'Ingresa el nombre del gimnasio.',
            'gym_phone.regex' => 'El teléfono solo puede contener números, espacios y + - ( ).',
            'gym_address_country.required' => 'Selecciona el país del gimnasio.',
            'gym_address_country.in' => 'Selecciona un país válido.',
            'gym_address_state.required' => 'Selecciona la provincia/estado.',
            'gym_address_city.required' => 'Selecciona la ciudad.',
            'gym_currency_code.in' => 'Selecciona una moneda válida.',
            'gym_language_code.in' => 'Selecciona un idioma válido.',
            'admin_email.unique' => 'Ese correo ya está en uso, revísalo por favor.',
            'admin_gender.in' => 'Selecciona un género válido.',
            'admin_birth_date.date' => 'Selecciona una fecha de nacimiento válida.',
            'admin_birth_date.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'admin_identification_type.in' => 'Selecciona un tipo de identificación válido.',
            'admin_identification_number.regex' => 'El número de identificación solo puede contener letras, números y guion.',
            'admin_phone_country_dial.regex' => 'El código de teléfono debe tener formato internacional, por ejemplo +593.',
            'admin_phone_number.regex' => 'El teléfono solo puede contener números.',
            'admin_profile_photo.image' => 'La foto del admin debe ser una imagen válida.',
            'admin_profile_photo.mimes' => 'La foto del admin debe ser JPG, JPEG, PNG o WEBP.',
            'admin_profile_photo.max' => 'La foto del admin no puede superar 15MB.',
            'admin_password.confirmed' => 'La confirmación de contraseña no coincide.',
            'admin_password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'subscription_plan_template_id.required' => 'Selecciona un plan base para el nuevo gimnasio.',
            'subscription_plan_template_id.exists' => 'El plan base seleccionado no está disponible.',
            'subscription_custom_price.numeric' => 'El precio personalizado debe ser numerico.',
            'subscription_custom_price.min' => 'El precio personalizado no puede ser negativo.',
            'subscription_custom_price.max' => 'El precio personalizado supera el límite permitido.',
            'subscription_apply_intro_50.boolean' => 'El indicador de descuento de introducción no es válido.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $identificationType = (string) ($this->input('admin_identification_type') ?? '');
            $identificationNumber = (string) ($this->input('admin_identification_number') ?? '');

            if ($identificationType !== '' && $identificationNumber === '') {
                $validator->errors()->add('admin_identification_number', 'Ingresa la cédula o identificación.');
            }

            if ($identificationType === '' && $identificationNumber !== '') {
                $validator->errors()->add('admin_identification_type', 'Selecciona el tipo de identificación.');
            }

            $phoneDial = (string) ($this->input('admin_phone_country_dial') ?? '');
            $phoneNumber = (string) ($this->input('admin_phone_number') ?? '');
            if ($phoneDial !== '' && $phoneNumber === '') {
                $validator->errors()->add('admin_phone_number', 'Ingresa el teléfono.');
            }
            if ($phoneDial === '' && $phoneNumber !== '') {
                $validator->errors()->add('admin_phone_country_dial', 'Ingresa el código de teléfono.');
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
