<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPasswordRequest extends FormRequest
{
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
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('validation_custom.password.current_password_required'),
            'current_password.current_password' => __('validation_custom.password.current_password_invalid'),
            'password.required' => __('validation_custom.password.password_required'),
            'password.min' => __('validation_custom.password.password_min'),
            'password.confirmed' => __('validation_custom.password.password_confirmed'),
        ];
    }
}
