<?php

namespace App\Http\Requests;

use App\Support\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGymProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency_code' => strtoupper(trim((string) $this->input('currency_code', 'USD'))),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->gym_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'size:3', Rule::in(array_keys(Currency::options()))],
            'timezone' => [
                'required',
                'string',
                'max:64',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value) || ! in_array($value, timezone_identifiers_list(), true)) {
                        $fail('La zona horaria seleccionada no es valida.');
                    }
                },
            ],
        ];
    }
}
