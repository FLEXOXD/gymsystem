<?php

namespace App\Http\Requests;

use App\Support\ActiveGymContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'description' => trim((string) $this->input('description')),
            'status' => (string) $this->input('status', 'active'),
            'type' => (string) $this->input('type', 'percentage'),
        ]);
    }

    /**
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
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'plan_id' => [
                'nullable',
                'integer',
                Rule::exists('plans', 'id')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'type' => ['required', Rule::in(['percentage', 'fixed', 'final_price', 'bonus_days', 'two_for_one', 'bring_friend'])],
            'value' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:1000000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym_context.required' => 'El usuario autenticado no tiene gym_id asignado.',
        ];
    }
}
