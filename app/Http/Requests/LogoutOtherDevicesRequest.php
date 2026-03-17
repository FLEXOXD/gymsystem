<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoutOtherDevicesRequest extends FormRequest
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
            'confirm_current_password' => ['required', 'current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirm_current_password.required' => __('validation_custom.logout_other_devices.current_password_required'),
            'confirm_current_password.current_password' => __('validation_custom.logout_other_devices.current_password_invalid'),
        ];
    }
}
