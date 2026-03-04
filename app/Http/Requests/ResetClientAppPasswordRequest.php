<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetClientAppPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'app_password' => ['required', 'string', 'min:8', 'max:120', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'app_password.required' => 'Ingresa una contraseña para el acceso cliente.',
            'app_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'app_password.max' => 'La contraseña no puede superar 120 caracteres.',
            'app_password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }
}
