<?php

namespace App\Http\Requests;

use App\Support\Currency;
use App\Support\GymLocationCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGymProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name', '')),
            'phone' => ($phone = trim((string) $this->input('phone', ''))) !== '' ? $phone : null,
            'address_country_code' => ($country = strtolower(trim((string) $this->input('address_country_code', '')))) !== '' ? $country : null,
            'address_state' => ($state = trim((string) $this->input('address_state', ''))) !== '' ? $state : null,
            'address_city' => ($city = trim((string) $this->input('address_city', ''))) !== '' ? $city : null,
            'address_line' => ($line = trim((string) $this->input('address_line', ''))) !== '' ? $line : null,
            'currency_code' => strtoupper(trim((string) $this->input('currency_code', 'USD'))),
            'language_code' => strtolower(trim((string) $this->input('language_code', 'es'))),
            'timezone' => trim((string) $this->input('timezone', 'UTC')),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->gym_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address_country_code' => ['nullable', 'string', Rule::in(array_keys(GymLocationCatalog::catalog()))],
            'address_state' => ['nullable', 'string', 'max:120'],
            'address_city' => ['nullable', 'string', 'max:120'],
            'address_line' => ['nullable', 'string', 'max:180'],
            'currency_code' => ['required', 'string', 'size:3', Rule::in(array_keys(Currency::options()))],
            'language_code' => ['required', 'string', Rule::in(['es', 'en', 'pt'])],
            'timezone' => [
                'required',
                'string',
                'max:64',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! in_array($value, timezone_identifiers_list(), true)) {
                        $fail(__('validation_custom.invalid_timezone'));
                    }
                },
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $country = (string) ($this->input('address_country_code') ?? '');
            $state = (string) ($this->input('address_state') ?? '');
            $city = (string) ($this->input('address_city') ?? '');

            $hasAnyLocation = $country !== '' || $state !== '' || $city !== '';

            if (! $hasAnyLocation) {
                return;
            }

            if ($country === '') {
                $validator->errors()->add('address_country_code', 'Selecciona el país.');
            }

            if ($state === '') {
                $validator->errors()->add('address_state', 'Selecciona la provincia/estado.');
            }

            if ($city === '') {
                $validator->errors()->add('address_city', 'Selecciona la ciudad.');
            }

            if ($country === '' || $state === '' || $city === '') {
                return;
            }

            if (GymLocationCatalog::resolveState($country, $state) === null) {
                $validator->errors()->add('address_state', 'Selecciona una provincia/estado válido.');
            }

            if (GymLocationCatalog::resolveCity($country, $state, $city) === null) {
                $validator->errors()->add('address_city', 'Selecciona una ciudad válida para la provincia/estado elegidos.');
            }
        });
    }
}
