<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGymAvatarsRequest extends FormRequest
{
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
            'avatar_male' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'avatar_female' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'avatar_neutral' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (
                ! $this->hasFile('avatar_male')
                && ! $this->hasFile('avatar_female')
                && ! $this->hasFile('avatar_neutral')
            ) {
                $validator->errors()->add('avatar_files', 'Debes subir al menos un avatar.');
            }
        });
    }
}
