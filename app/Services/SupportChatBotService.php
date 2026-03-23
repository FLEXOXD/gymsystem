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

        if ($this->containsTrigger($text, 'representative')) {
            return [
                'text' => 'Perfecto, estoy pasando tu solicitud a un representante ahora mismo.',
                'escalate' => true,
            ];
        }

        if ($this->containsTrigger($text, 'schedule')) {
            return [
                'text' => 'Para atención rápida, puedes dejar aquí tu consulta y también pedir que te escalemos a llamada o WhatsApp con un representante.',
                'escalate' => false,
            ];
        }

        if ($context === 'landing') {
            if ($this->containsTrigger($text, 'plans')) {
                return [
                    'text' => 'Te orientamos con planes según operación: sedes, recepción, caja, ventas/inventario, reportes y portal cliente. Si quieres, te ayudo a elegir el ideal.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'demo')) {
                return [
                    'text' => 'La demo te permite probar un flujo real: recepción, clientes, membresías, caja, ventas/inventario y reportes en un entorno temporal.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'implementation') || $this->containsTrigger($text, 'migration')) {
                return [
                    'text' => 'Podemos guiarte en implementación y migración: carga inicial de clientes, planes, usuarios y configuración operativa para arrancar sin fricción.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'reception')) {
                return [
                    'text' => 'Sí, el sistema contempla recepción y control de acceso con seguimiento de asistencias para la operación diaria.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'clients') || $this->containsTrigger($text, 'memberships')) {
                return [
                    'text' => 'FlexGym incluye gestión de clientes y membresías: altas, renovaciones, vencimientos y control de estado.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'cash')) {
                return [
                    'text' => 'Sí, incluye caja con apertura/cierre por turno y registro de movimientos para control operativo.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'sales_inventory')) {
                return [
                    'text' => 'También incluye ventas e inventario: productos, stock, entradas/salidas y reportes comerciales.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'reports')) {
                return [
                    'text' => 'Tendrás reportes de ingresos, asistencias y desempeño operativo, con exportación según el plan habilitado.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'branches')) {
                return [
                    'text' => 'Puedes trabajar por sede y también en contexto global para administrar sucursales cuando el plan multi-sucursal esté activo.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'portal_pwa')) {
                return [
                    'text' => 'Sí, existe portal cliente y opción de experiencia móvil tipo app (PWA), sujeto a las funciones del plan.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'billing')) {
                return [
                    'text' => 'Te ayudo con facturación y suscripción. Si prefieres, te conecto con un representante para revisión comercial directa.',
                    'escalate' => false,
                ];
            }
            if ($this->containsTrigger($text, 'technical')) {
                return [
                    'text' => 'Si deseas, te comparto una guía inicial por módulo. También puedo escalar tu consulta a soporte humano.',
                    'escalate' => false,
                ];
            }

            return [
                'text' => $this->fallbackMessage($context),
                'escalate' => false,
            ];
        }

        if ($this->containsTrigger($text, 'reception')) {
            return [
                'text' => 'Para recepción, indícame la acción exacta (check-in/check-out), sede y mensaje mostrado. Con eso te doy pasos concretos de solución.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'clients') || $this->containsTrigger($text, 'memberships')) {
            return [
                'text' => 'En clientes/membresías dime el cliente y la acción que falla (crear, editar, renovar, ajustar) para guiarte paso a paso.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'cash')) {
            return [
                'text' => 'En caja revisamos apertura, movimientos o cierre. Comparte fecha, turno y en qué paso se detiene para ayudarte con precisión.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'sales_inventory')) {
            return [
                'text' => 'Para ventas/inventario, indícame producto, movimiento y pantalla exacta. También confirma si la caja estaba abierta.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'reports')) {
            return [
                'text' => 'Para reportes, comparte el rango de fechas y el módulo (ingresos, asistencias o ventas) para validar filtros y permisos.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'staff')) {
            return [
                'text' => 'Si es de usuarios/permisos, dime qué rol (owner o cajero) y qué sección debería ver para orientarte en la configuración correcta.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'branches')) {
            return [
                'text' => 'Si es por sucursales, te ayudo a revisar contexto de sede/global, permisos y plan multi-sucursal.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'portal_pwa')) {
            return [
                'text' => 'Para portal cliente o PWA, comparte dispositivo, navegador y pantalla donde falla; así te damos solución más rápida.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'billing')) {
            return [
                'text' => 'Te ayudo con suscripción, renovación o facturación. Si quieres revisión directa, también puedo escalarte con un representante.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'training')) {
            return [
                'text' => 'Podemos coordinar capacitación por módulo (recepción, clientes, caja, ventas e inventario, reportes).',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'technical')) {
            return [
                'text' => 'Para soporte técnico, envíame módulo, paso exacto y texto del error. Con esos datos aceleramos la solución.',
                'escalate' => false,
            ];
        }
        if ($this->containsTrigger($text, 'plans')) {
            return [
                'text' => 'Si deseas ampliar funciones, también te puedo orientar sobre ajuste de plan según tus módulos activos.',
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

    private function containsTrigger(string $haystack, string $triggerKey): bool
    {
        return $this->containsAny(
            $haystack,
            (array) config('support_chat.keyword_triggers.'.$triggerKey, [])
        );
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
