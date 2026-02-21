<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $firstName = trim((string) $this->input('user_first_name'));
        $lastName = trim((string) $this->input('user_last_name'));
        $gender = strtolower(trim((string) $this->input('user_gender')));
        $birthDate = trim((string) $this->input('user_birth_date'));
        $identificationType = strtolower(trim((string) $this->input('user_identification_type')));
        $identificationNumber = strtoupper(trim((string) $this->input('user_identification_number')));
        $fullName = trim($firstName.' '.$lastName);

        $this->merge([
            'user_first_name' => $firstName,
            'user_last_name' => $lastName,
            'user_name' => $fullName,
            'user_email' => strtolower(trim((string) $this->input('user_email'))),
            'user_country_iso' => strtoupper(trim((string) $this->input('user_country_iso'))),
            'user_country_name' => trim((string) $this->input('user_country_name')),
            'user_gender' => $gender !== '' ? $gender : null,
            'user_birth_date' => $birthDate !== '' ? $birthDate : null,
            'user_identification_type' => $identificationType !== '' ? $identificationType : null,
            'user_identification_number' => $identificationNumber !== '' ? $identificationNumber : null,
            'user_phone_country_iso' => strtoupper(trim((string) $this->input('user_phone_country_iso'))),
            'user_phone_country_dial' => trim((string) $this->input('user_phone_country_dial')),
            'user_phone_number' => preg_replace('/\D+/', '', (string) $this->input('user_phone_number')),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = (int) ($this->user()?->id ?? 0);

        return [
            'user_first_name' => ['required', 'string', 'min:2', 'max:80'],
            'user_last_name' => ['required', 'string', 'min:2', 'max:80'],
            'user_name' => ['required', 'string', 'min:3', 'max:120'],
            'user_email' => ['required', 'email', 'max:120', 'unique:users,email,'.$userId],
            'user_country_iso' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'user_country_name' => ['required', 'string', 'min:2', 'max:80'],
            'user_gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer_not_say'])],
            'user_birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'user_identification_type' => ['nullable', 'string', Rule::in(['cedula', 'dni', 'passport'])],
            'user_identification_number' => ['nullable', 'string', 'min:4', 'max:30', 'regex:/^[A-Z0-9\-]+$/'],
            'user_phone_country_iso' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'user_phone_country_dial' => ['required', 'string', 'max:8', 'regex:/^\+\d{1,4}$/'],
            'user_phone_number' => ['required', 'string', 'min:6', 'max:15', 'regex:/^\d+$/'],
            'user_profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_first_name.required' => __('validation_custom.profile.user_first_name_required'),
            'user_first_name.min' => __('validation_custom.profile.user_first_name_min'),
            'user_last_name.required' => __('validation_custom.profile.user_last_name_required'),
            'user_last_name.min' => __('validation_custom.profile.user_last_name_min'),
            'user_name.required' => __('validation_custom.profile.user_name_required'),
            'user_name.min' => __('validation_custom.profile.user_name_min'),
            'user_email.required' => __('validation_custom.profile.user_email_required'),
            'user_email.email' => __('validation_custom.profile.user_email_email'),
            'user_email.unique' => __('validation_custom.profile.user_email_unique'),
            'user_country_iso.required' => __('validation_custom.profile.user_country_required'),
            'user_country_name.required' => __('validation_custom.profile.user_country_required'),
            'user_country_iso.regex' => __('validation_custom.profile.user_country_iso_regex'),
            'user_gender.in' => __('validation_custom.profile.user_gender_in'),
            'user_birth_date.date' => __('validation_custom.profile.user_birth_date_date'),
            'user_birth_date.before_or_equal' => __('validation_custom.profile.user_birth_date_before_or_equal'),
            'user_identification_type.in' => __('validation_custom.profile.user_identification_type_in'),
            'user_identification_number.min' => __('validation_custom.profile.user_identification_number_min'),
            'user_identification_number.max' => __('validation_custom.profile.user_identification_number_max'),
            'user_identification_number.regex' => __('validation_custom.profile.user_identification_number_regex'),
            'user_phone_country_iso.required' => __('validation_custom.profile.user_phone_country_iso_required'),
            'user_phone_country_dial.required' => __('validation_custom.profile.user_phone_country_dial_required'),
            'user_phone_country_dial.regex' => __('validation_custom.profile.user_phone_country_dial_regex'),
            'user_phone_number.required' => __('validation_custom.profile.user_phone_number_required'),
            'user_phone_number.regex' => __('validation_custom.profile.user_phone_number_regex'),
            'user_profile_photo.image' => __('validation_custom.profile.user_profile_photo_image'),
            'user_profile_photo.mimes' => __('validation_custom.profile.user_profile_photo_mimes'),
            'user_profile_photo.max' => __('validation_custom.profile.user_profile_photo_max'),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $countryIso = (string) $this->input('user_country_iso');
            $phoneCountryIso = (string) $this->input('user_phone_country_iso');
            $identificationType = (string) $this->input('user_identification_type');
            $identificationNumber = (string) $this->input('user_identification_number');
            if ($countryIso !== '' && $phoneCountryIso !== '' && $countryIso !== $phoneCountryIso) {
                $validator->errors()->add('user_phone_country_iso', __('validation_custom.profile.country_phone_mismatch'));
            }

            if ($identificationType !== '' && $identificationNumber === '') {
                $validator->errors()->add('user_identification_number', __('validation_custom.profile.identification_number_required_with_type'));
            }

            if ($identificationType === '' && $identificationNumber !== '') {
                $validator->errors()->add('user_identification_type', __('validation_custom.profile.identification_type_required_with_number'));
            }
        });
    }
}
