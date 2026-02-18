<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
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
        $gymId = $this->user()?->gym_id;

        if (! $gymId) {
            return [
                'gym_context' => ['required'],
            ];
        }

        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'document_number' => [
                'required',
                'string',
                'max:120',
                Rule::unique('clients', 'document_number')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_context.required' => 'El usuario autenticado no tiene gym_id asignado.',
            'document_number.unique' => 'Ya existe un cliente con ese documento en este gimnasio.',
        ];
    }
}
