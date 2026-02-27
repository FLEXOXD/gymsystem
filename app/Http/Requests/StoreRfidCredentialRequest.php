<?php

namespace App\Http\Requests;

use App\Support\ActiveGymContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRfidCredentialRequest extends FormRequest
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
        $gymId = ActiveGymContext::id($this);

        if (! $gymId) {
            return [
                'gym_context' => ['required'],
            ];
        }

        return [
            'client' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'value' => [
                'required',
                'string',
                'max:191',
                Rule::unique('client_credentials', 'value')->where(
                    fn ($query) => $query
                        ->where('gym_id', $gymId)
                        ->where('type', 'rfid')
                ),
            ],
        ];
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'client' => $this->route('client'),
            'value' => trim((string) $this->input('value', '')),
        ]);
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_context.required' => 'El usuario autenticado no tiene gym_id asignado.',
            'client.exists' => 'El cliente no pertenece al gimnasio actual.',
            'value.unique' => 'Este UID RFID ya existe en este gimnasio.',
        ];
    }
}
