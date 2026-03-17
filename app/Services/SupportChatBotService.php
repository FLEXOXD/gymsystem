<?php

namespace App\Services;

class SupportChatBotService
{
    /**
     * @return array<int, array{key:string,label:string}>
     */
    public function quickReplies(string $context): array
    {
        $items = (array) data_get($this->contextConfig($context), 'quick_replies', []);

        return collect($items)
            ->map(static function (array $item): array {
                return [
                    'key' => trim((string) ($item['key'] ?? '')),
                    'label' => trim((string) ($item['label'] ?? '')),
                ];
            })
            ->filter(static fn (array $item): bool => $item['key'] !== '' && $item['label'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return array{assistant_name:string,assistant_subtitle:string,welcome_message:string}
     */
    public function assistantProfile(string $context): array
    {
        $config = $this->contextConfig($context);

        return [
            'assistant_name' => trim((string) ($config['assistant_name'] ?? 'Asistente')),
            'assistant_subtitle' => trim((string) ($config['assistant_subtitle'] ?? 'Soporte')),
            'welcome_message' => trim((string) ($config['welcome_message'] ?? 'Hola, te puedo ayudar.')),
        ];
    }

    /**
     * @return array{text:string,escalate:bool}
     */
    public function quickReplyResponse(string $context, string $actionKey): array
    {
        $normalizedAction = trim($actionKey);
        $items = (array) data_get($this->contextConfig($context), 'quick_replies', []);
        foreach ($items as $item) {
            if (trim((string) ($item['key'] ?? '')) !== $normalizedAction) {
                continue;
            }

            return [
                'text' => trim((string) ($item['response'] ?? $this->fallbackMessage($context))),
                'escalate' => (bool) ($item['escalate'] ?? false),
            ];
        }

        return [
            'text' => $this->fallbackMessage($context),
            'escalate' => false,
        ];
    }

    /**
     * @return array{text:string,escalate:bool}
     */
    public function messageResponse(string $context, string $message): array
    {
        $text = mb_strtolower(trim($message));
        if ($text === '') {
            return [
                'text' => $this->fallbackMessage($context),
                'escalate' => false,
            ];
        }

        if ($this->containsAny($text, (array) config('support_chat.keyword_triggers.representative', []))) {
            return [
                'text' => 'Perfecto, estoy pasando tu solicitud a un representante ahora mismo.',
                'escalate' => true,
            ];
        }

        if ($this->containsAny($text, (array) config('support_chat.keyword_triggers.technical', []))) {
            return [
                'text' => 'Para soporte tecnico, comparte modulo, paso exacto y mensaje mostrado. Con eso aceleramos la solucion.',
                'escalate' => false,
            ];
        }

        if ($this->containsAny($text, (array) config('support_chat.keyword_triggers.billing', []))) {
            return [
                'text' => 'Te ayudo con facturacion y suscripcion. Si quieres revision directa, tambien puedo escalarlo a representante.',
                'escalate' => false,
            ];
        }

        if ($this->containsAny($text, (array) config('support_chat.keyword_triggers.training', []))) {
            return [
                'text' => 'Podemos coordinar capacitacion por modulo operativo. Indica que area necesita refuerzo y lo agendamos.',
                'escalate' => false,
            ];
        }

        if ($context === 'landing' && $this->containsAny($text, (array) config('support_chat.keyword_triggers.plans', []))) {
            return [
                'text' => 'Te orientamos con planes y demo segun tu operacion. Si prefieres atencion personalizada, te conecto con un representante.',
                'escalate' => false,
            ];
        }

        return [
            'text' => $this->fallbackMessage($context),
            'escalate' => false,
        ];
    }

    public function agentOnlineMessage(): string
    {
        return trim((string) config('support_chat.agent_online_message', 'Representante conectado.'));
    }

    public function agentOfflineMessage(): string
    {
        return trim((string) config('support_chat.agent_offline_message', 'Representante no disponible por ahora.'));
    }

    private function fallbackMessage(string $context): string
    {
        return trim((string) data_get($this->contextConfig($context), 'fallback_message', 'Te ayudamos en seguida.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function contextConfig(string $context): array
    {
        $normalized = trim($context) !== '' ? trim($context) : 'landing';
        $contextConfig = config('support_chat.contexts.'.$normalized);
        if (is_array($contextConfig)) {
            return $contextConfig;
        }

        return (array) config('support_chat.contexts.landing', []);
    }

    /**
     * @param  array<int, string>  $needles
     */
    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            $term = mb_strtolower(trim((string) $needle));
            if ($term === '') {
                continue;
            }

            if (mb_stripos($haystack, $term) !== false) {
                return true;
            }
        }

        return false;
    }
}

