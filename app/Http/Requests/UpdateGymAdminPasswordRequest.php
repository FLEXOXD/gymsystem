<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGymAdminPasswordRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reset_password_gym_id' => (int) $this->input('reset_password_gym_id'),
            'reset_password_user_id' => (int) $this->input('reset_password_user_id'),
            'reset_password' => (string) $this->input('reset_password'),
            'reset_password_confirmation' => (string) $this->input('reset_password_confirmation'),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()?->gym_id === null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'reset_password_gym_id' => ['required', 'integer'],
            'reset_password_user_id' => ['required', 'integer', 'exists:users,id'],
            'reset_password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'reset_password_confirmation' => ['required', 'string', 'min:8', 'max:72'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reset_password_gym_id.required' => 'No se pudo identificar el gimnasio.',
            'reset_password_user_id.required' => 'No se pudo identificar el usuario administrador.',
            'reset_password_user_id.exists' => 'El usuario administrador no existe.',
            'reset_password.required' => 'Ingresa la nueva contrasena.',
            'reset_password.min' => 'La contrasena debe tener minimo 8 caracteres.',
            'reset_password.max' => 'La contrasena no puede superar 72 caracteres.',
            'reset_password.confirmed' => 'La confirmacion de contrasena no coincide.',
            'reset_password_confirmation.required' => 'Confirma la nueva contrasena.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $routeGymId = (int) $this->route('gym');
            $submittedGymId = (int) $this->input('reset_password_gym_id');
            $adminUserId = (int) $this->input('reset_password_user_id');

            if ($routeGymId <= 0 || $submittedGymId <= 0 || $routeGymId !== $submittedGymId) {
                $validator->errors()->add('reset_password_gym_id', 'No se pudo validar el gimnasio para este cambio.');
                return;
            }

            $adminUser = User::query()->find($adminUserId);
            if (! $adminUser || (int) ($adminUser->gym_id ?? 0) !== $routeGymId) {
                $validator->errors()->add('reset_password_user_id', 'El usuario no pertenece al gimnasio seleccionado.');
            }
        });
    }
}
