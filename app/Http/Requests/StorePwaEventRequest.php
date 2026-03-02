<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePwaEventRequest extends FormRequest
{
    /**
     * @var list<string>
     */
    private const ALLOWED_EVENTS = [
        'sw_registered',
        'sw_update_ready',
        'sw_update_apply_clicked',
        'sw_update_applied',
        'install_prompt_available',
        'install_manual_hint_shown',
        'app_installed',
        'standalone_launch',
        'browser_launch',
    ];

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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'event_name' => ['required', 'string', 'max:64', Rule::in(self::ALLOWED_EVENTS)],
            'event_source' => ['nullable', 'string', 'max:32'],
            'mode' => ['nullable', 'string', Rule::in(['standalone', 'browser', 'unknown'])],
            'context_gym_slug' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9\-]+$/'],
            'payload' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'event_name.in' => 'Evento PWA no permitido.',
            'context_gym_slug.regex' => 'El contexto enviado no es válido.',
        ];
    }
}

