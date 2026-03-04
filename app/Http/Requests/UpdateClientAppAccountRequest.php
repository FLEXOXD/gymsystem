<?php

namespace App\Http\Requests;

use App\Support\ActiveGymContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientAppAccountRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $appUsername = mb_strtolower(trim((string) $this->input('app_username', '')));
        $appUsername = preg_replace('/\s+/u', '', $appUsername) ?? '';

        $this->merge([
            'app_username' => $appUsername,
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $gymId = ActiveGymContext::id($this);
        $clientRoute = $this->route('client');
        $clientId = is_numeric($clientRoute) ? (int) $clientRoute : (int) ($clientRoute?->id ?? 0);

        if (! $gymId) {
            return [
                'gym_context' => ['required'],
            ];
        }

        return [
            'app_username' => [
                'required',
                'string',
                'min:4',
                'max:80',
                'regex:/^[a-z0-9._-]+$/',
                Rule::unique('clients', 'app_username')
                    ->where(fn ($query) => $query->where('gym_id', $gymId))
                    ->ignore($clientId),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_context.required' => 'El usuario autenticado no tiene gym_id asignado.',
            'app_username.required' => 'Ingresa un usuario para el acceso cliente.',
            'app_username.min' => 'El usuario debe tener al menos 4 caracteres.',
            'app_username.max' => 'El usuario no puede superar 80 caracteres.',
            'app_username.regex' => 'El usuario solo puede usar letras minusculas, numeros, punto, guion y guion bajo.',
            'app_username.unique' => 'Este usuario ya existe en este gimnasio.',
        ];
    }
}
