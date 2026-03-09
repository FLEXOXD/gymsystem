<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Services\PlanAccessService;
use App\Support\ActiveGymContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $phone = trim((string) $this->input('phone', ''));
        $phone = preg_replace('/\s+/u', ' ', $phone) ?? '';
        $appUsername = mb_strtolower(trim((string) $this->input('app_username', '')));
        $appUsername = preg_replace('/\s+/u', '', $appUsername) ?? '';

        $this->merge([
            'first_name' => $this->formatPersonName($this->input('first_name')),
            'last_name' => $this->formatPersonName($this->input('last_name')),
            'document_number' => Client::normalizeDocumentNumber($this->input('document_number')),
            'phone' => $phone,
            'create_app_account' => $this->boolean('create_app_account'),
            'app_username' => $appUsername,
        ]);
    }

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
        $startsMembership = $this->boolean('start_membership');
        $canManageClientAccounts = $gymId
            ? app(PlanAccessService::class)->canForGym((int) $gymId, 'client_accounts')
            : false;
        $creatingAppAccount = $canManageClientAccounts && $this->boolean('create_app_account');

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
                'max:30',
                function (string $attribute, mixed $value, \Closure $fail) use ($gymId): void {
                    $document = Client::normalizeDocumentNumber((string) $value);
                    $canonical = Client::canonicalDocumentNumber($document);

                    if ($canonical === '') {
                        $fail('El documento es obligatorio.');
                        return;
                    }

                    if (! preg_match('/^[A-Z0-9\- ]+$/', $document)) {
                        $fail('El documento solo puede contener letras, números, espacios y guion.');
                        return;
                    }

                    $length = mb_strlen($canonical);
                    if ($length < 6 || $length > 20) {
                        $fail('El documento debe tener entre 6 y 20 caracteres utiles.');
                        return;
                    }

                    if (! preg_match('/\d/', $canonical)) {
                        $fail('El documento debe incluir al menos un número.');
                        return;
                    }

                    if (preg_match('/^(.)\1+$/', $canonical) === 1) {
                        $fail('El documento ingresado no parece válido.');
                        return;
                    }

                    if (preg_match('/^\d+$/', $canonical) === 1 && $this->isSequentialDigits($canonical)) {
                        $fail('El documento ingresado no parece válido.');
                        return;
                    }

                    if ($this->isKnownGarbageDocument($canonical)) {
                        $fail('El documento ingresado no parece válido.');
                        return;
                    }

                    $exists = Client::query()
                        ->where('gym_id', $gymId)
                        ->whereRaw("REPLACE(REPLACE(UPPER(document_number), '-', ''), ' ', '') = ?", [$canonical])
                        ->exists();

                    if ($exists) {
                        $fail('Ya existe un cliente con ese documento en este gimnasio.');
                    }
                },
            ],
            'phone' => [
                'bail',
                'required',
                'string',
                'max:25',
                'regex:/^[0-9+\-\s()]+$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

                    if ($digits === '') {
                        $fail('Ingresa el teléfono del cliente.');
                        return;
                    }

                    $length = strlen($digits);
                    if ($length < 7 || $length > 15) {
                        $fail('El teléfono debe tener entre 7 y 15 dígitos.');
                        return;
                    }

                    if (preg_match('/^(\d)\1+$/', $digits) === 1) {
                        $fail('El teléfono ingresado no parece válido.');
                        return;
                    }

                    if ($this->isSequentialDigits($digits)) {
                        $fail('El teléfono ingresado no parece válido.');
                    }
                },
            ],
            'create_app_account' => ['nullable', 'boolean'],
            'app_username' => [
                'nullable',
                Rule::requiredIf($creatingAppAccount),
                'string',
                'min:4',
                'max:80',
                'regex:/^[a-z0-9._-]+$/',
                Rule::unique('clients', 'app_username')->where(
                    fn ($query) => $query->where('gym_id', $gymId)
                ),
            ],
            'app_password' => [
                'nullable',
                Rule::requiredIf($creatingAppAccount),
                'string',
                'min:8',
                'max:120',
                'confirmed',
            ],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'neutral'])],
            'start_membership' => ['nullable', 'boolean'],
            'plan_id' => [
                'nullable',
                Rule::requiredIf($startsMembership),
                'integer',
                Rule::exists('plans', 'id')->where(
                    fn ($query) => $query
                        ->where('gym_id', $gymId)
                        ->where('status', 'active')
                ),
            ],
            'membership_starts_at' => ['nullable', Rule::requiredIf($startsMembership), 'date'],
            'membership_price' => ['nullable', Rule::requiredIf($startsMembership), 'numeric', 'min:0'],
            'promotion_id' => [
                'nullable',
                'integer',
                Rule::exists('promotions', 'id')->where(
                    fn ($query) => $query
                        ->where('gym_id', $gymId)
                        ->where('status', 'active')
                ),
            ],
            'payment_method' => ['nullable', Rule::requiredIf($startsMembership), Rule::in(['cash', 'transfer', 'card'])],
            'amount_paid' => ['nullable', Rule::requiredIf($startsMembership), 'numeric', 'min:0'],
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
            'first_name.required' => 'Ingresa el nombre del cliente.',
            'first_name.max' => 'El nombre no puede superar 120 caracteres.',
            'last_name.required' => 'Ingresa el apellido del cliente.',
            'last_name.max' => 'El apellido no puede superar 120 caracteres.',
            'document_number.required' => 'Ingresa el documento del cliente.',
            'phone.required' => 'Ingresa el teléfono del cliente.',
            'phone.regex' => 'El teléfono solo puede contener números y los símbolos + - ( ).',
            'phone.max' => 'El teléfono no puede superar 25 caracteres.',
            'app_username.required' => 'Ingresa un usuario para el acceso cliente.',
            'app_username.min' => 'El usuario debe tener al menos 4 caracteres.',
            'app_username.max' => 'El usuario no puede superar 80 caracteres.',
            'app_username.regex' => 'El usuario solo puede usar letras minúsculas, números, punto, guion y guion bajo.',
            'app_username.unique' => 'Este usuario ya existe en este gimnasio.',
            'app_password.required' => 'Ingresa una contraseña para el acceso cliente.',
            'app_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'app_password.confirmed' => 'La confirmación de contraseña no coincide.',
            'photo.image' => 'La foto debe ser una imagen válida.',
            'photo.max' => 'La foto no puede superar 2MB.',
            'plan_id.required' => 'Selecciona un plan para iniciar la membresía.',
            'membership_starts_at.required' => 'La fecha de inicio es obligatoria.',
            'membership_price.required' => 'El precio de la membresía es obligatorio.',
            'promotion_id.exists' => 'La promoción seleccionada no es válida o no está disponible.',
            'payment_method.required' => 'Selecciona el método de pago.',
            'amount_paid.required' => 'Ingresa el monto pagado.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'nombre',
            'last_name' => 'apellido',
            'document_number' => 'documento',
            'phone' => 'teléfono',
            'create_app_account' => 'crear cuenta app',
            'app_username' => 'usuario app',
            'app_password' => 'contraseña app',
            'app_password_confirmation' => 'confirmación de contraseña app',
            'gender' => 'género',
            'photo' => 'foto',
            'plan_id' => 'plan',
            'membership_starts_at' => 'fecha de inicio',
            'membership_price' => 'precio de membresía',
            'promotion_id' => 'promoción',
            'payment_method' => 'método de pago',
            'amount_paid' => 'monto pagado',
        ];
    }

    private function formatPersonName(mixed $value): string
    {
        $name = trim((string) $value);
        if ($name === '') {
            return '';
        }

        $name = preg_replace('/\s+/u', ' ', $name) ?? '';
        if ($name === '') {
            return '';
        }

        $segments = preg_split('/(\s+|-|\')/u', $name, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if (! is_array($segments)) {
            return $name;
        }

        foreach ($segments as $index => $segment) {
            if (preg_match('/^(\s+|-|\')$/u', $segment) === 1) {
                continue;
            }

            $first = mb_substr($segment, 0, 1);
            $rest = mb_substr($segment, 1);
            $segments[$index] = mb_strtoupper($first).mb_strtolower($rest);
        }

        return implode('', $segments);
    }

    private function isSequentialDigits(string $value): bool
    {
        $ascending = '01234567890';
        $descending = '9876543210';

        return str_contains($ascending, $value) || str_contains($descending, $value);
    }

    private function isKnownGarbageDocument(string $canonical): bool
    {
        $blocked = [
            '000000',
            '111111',
            '222222',
            '333333',
            '444444',
            '555555',
            '666666',
            '777777',
            '888888',
            '999999',
            '123456',
            '1234567',
            '12345678',
            '123456789',
            '1234567890',
            '987654',
            '9876543',
            '98765432',
            '987654321',
            '0123456',
            '01234567',
            '012345678',
            '0123456789',
        ];

        return in_array($canonical, $blocked, true);
    }
}
