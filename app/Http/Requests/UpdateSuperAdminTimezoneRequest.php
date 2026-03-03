<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuperAdminTimezoneRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'superadmin_timezone' => trim((string) $this->input('superadmin_timezone')),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->gym_id === null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'superadmin_timezone' => [
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
}

