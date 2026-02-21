<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $durationUnit = strtolower((string) $this->input('duration_unit', 'days'));

        return [
            'name' => ['required', 'string', 'max:120'],
            'duration_unit' => ['nullable', Rule::in(['days', 'months'])],
            'duration_days' => [
                Rule::requiredIf($durationUnit !== 'months'),
                'nullable',
                'integer',
                'min:1',
                'max:3650',
            ],
            'duration_months' => [
                Rule::requiredIf($durationUnit === 'months'),
                'nullable',
                'integer',
                'min:1',
                'max:120',
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'duration_days.required' => 'La duración en días es obligatoria para planes por días.',
            'duration_months.required' => 'La duración en meses es obligatoria para planes mensuales.',
        ];
    }
}
