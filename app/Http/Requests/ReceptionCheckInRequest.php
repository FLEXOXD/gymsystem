<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceptionCheckInRequest extends FormRequest
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
        return [
            'value' => ['required', 'string', 'max:191'],
        ];
    }

    /**
     * Prepare request data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'value' => $this->normalizeScanValue((string) $this->input('value', '')),
        ]);
    }

    /**
     * Normalize scanner input (RFID/QR) to reduce hardware-specific noise.
     */
    private function normalizeScanValue(string $raw): string
    {
        $value = trim($raw);

        if ($value === '') {
            return '';
        }

        // Remove control characters added by some readers.
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value) ?? $value;
        $value = trim($value);

        // Common prefix patterns from scanners (e.g. "UID: 12345").
        if (preg_match('/^(?:uid|rfid|qr|code|codigo)\s*[:\-]\s*(.+)$/i', $value, $matches) === 1) {
            $value = trim((string) ($matches[1] ?? ''));
        }

        // If scanner sends a URL, try common query keys.
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $query = parse_url($value, PHP_URL_QUERY);
            if (is_string($query) && $query !== '') {
                parse_str($query, $params);
                foreach (['value', 'code', 'uid', 'rfid', 'qr', 'token'] as $key) {
                    $candidate = isset($params[$key]) ? trim((string) $params[$key]) : '';
                    if ($candidate !== '') {
                        $value = $candidate;
                        break;
                    }
                }
            }
        }

        return trim($value);
    }
}
