<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\GymLocationCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGymAdminUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'admin_user_id' => (int) $this->input('admin_user_id'),
            'admin_name' => trim((string) $this->input('admin_name')),
            'admin_email' => strtolower(trim((string) $this->input('admin_email'))),
            'admin_gender' => ($gender = strtolower(trim((string) $this->input('admin_gender')))) !== '' ? $gender : null,
            'admin_birth_date' => ($birthDate = trim((string) $this->input('admin_birth_date'))) !== '' ? $birthDate : null,
            'admin_identification_type' => ($idType = strtolower(trim((string) $this->input('admin_identification_type')))) !== '' ? $idType : null,
            'admin_identification_number' => ($idNumber = strtoupper(trim((string) $this->input('admin_identification_number')))) !== '' ? $idNumber : null,
            'admin_country_iso' => ($countryIso = strtolower(trim((string) $this->input('admin_country_iso')))) !== '' ? $countryIso : null,
            'admin_address_state' => ($state = trim((string) $this->input('admin_address_state'))) !== '' ? $state : null,
            'admin_address_city' => ($city = trim((string) $this->input('admin_address_city'))) !== '' ? $city : null,
            'admin_address_line' => ($line = trim((string) $this->input('admin_address_line'))) !== '' ? $line : null,
            'admin_phone_country_dial' => ($dial = trim((string) $this->input('admin_phone_country_dial'))) !== '' ? $dial : null,
            'admin_phone_number' => ($phone = preg_replace('/\D+/', '', (string) $this->input('admin_phone_number'))) !== '' ? $phone : null,
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()?->gym_id === null;
    }

    /**
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        $adminUserId = (int) $this->input('admin_user_id');

        return [
            'admin_user_id' => ['required', 'integer', 'exists:users,id'],
            'admin_name' => ['required', 'string', 'min:2', 'max:120'],
            'admin_email' => [
                'required',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($adminUserId),
            ],
            'admin_gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer_not_say'])],
            'admin_birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'admin_identification_type' => ['nullable', 'string', Rule::in(['cedula', 'dni', 'passport'])],
            'admin_identification_number' => ['nullable', 'string', 'min:4', 'max:30', 'regex:/^[A-Z0-9\-]+$/'],
            'admin_country_iso' => ['nullable', 'string', Rule::in(array_keys(GymLocationCatalog::catalog()))],
            'admin_address_state' => ['nullable', 'string', 'max:120'],
            'admin_address_city' => ['nullable', 'string', 'max:120'],
            'admin_address_line' => ['nullable', 'string', 'max:180'],
            'admin_phone_country_dial' => ['nullable', 'string', 'max:8', 'regex:/^\+\d{1,4}$/'],
            'admin_phone_number' => ['nullable', 'string', 'min:6', 'max:15', 'regex:/^\d+$/'],
            'admin_profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $adminUserId = (int) $this->input('admin_user_id');
            $routeGymId = (int) $this->route('gym');
            $adminUser = User::query()->find($adminUserId);

            if (! $adminUser || (int) ($adminUser->gym_id ?? 0) !== $routeGymId) {
                $validator->errors()->add('admin_user_id', 'El usuario no pertenece al gimnasio seleccionado.');
            }

            $identificationType = (string) ($this->input('admin_identification_type') ?? '');
            $identificationNumber = (string) ($this->input('admin_identification_number') ?? '');
            if ($identificationType !== '' && $identificationNumber === '') {
                $validator->errors()->add('admin_identification_number', 'Ingresa el número de identificación.');
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

            $countryIso = strtolower((string) ($this->input('admin_country_iso') ?? ''));
            $state = (string) ($this->input('admin_address_state') ?? '');
            $city = (string) ($this->input('admin_address_city') ?? '');
            if ($countryIso === '' && ($state !== '' || $city !== '')) {
                $validator->errors()->add('admin_country_iso', 'Selecciona país para la ubicación.');
            }
            if ($countryIso !== '' && $state !== '' && GymLocationCatalog::resolveState($countryIso, $state) === null) {
                $validator->errors()->add('admin_address_state', 'Selecciona una provincia/estado válido.');
            }
            if ($state === '' && $city !== '') {
                $validator->errors()->add('admin_address_state', 'Selecciona provincia/estado.');
            }
            if ($countryIso !== '' && $state !== '' && $city !== '' && GymLocationCatalog::resolveCity($countryIso, $state, $city) === null) {
                $validator->errors()->add('admin_address_city', 'Selecciona una ciudad válida para la provincia/estado.');
            }
        });
    }
}
