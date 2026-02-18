<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCashMovementRequest extends FormRequest
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
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', Rule::in(['cash', 'card', 'transfer'])],
            'membership_id' => [
                'nullable',
                'integer',
                Rule::exists('memberships', 'id')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_context.required' => 'El usuario autenticado no tiene gym_id asignado.',
        ];
    }
}
