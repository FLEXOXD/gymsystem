<?php

return [
    'agent_presence_ttl_seconds' => 90,
    'polling_interval_seconds' => 7,
    'max_messages_per_minute' => 35,
    'contexts' => [
        'landing' => [
            'assistant_name' => 'Asistente GymSystem',
            'assistant_subtitle' => 'Soporte comercial para gimnasios',
            'welcome_message' => 'Hola, soy tu asistente. Te ayudo a resolver dudas de implementacion y soporte inicial para tu gimnasio.',
            'fallback_message' => 'Entendido. Puedo ayudarte con planes, implementacion y soporte. Si prefieres, te paso con un representante.',
            'quick_replies' => [
                [
                    'key' => 'know_plans',
                    'label' => 'Quiero conocer planes',
                    'response' => 'Te ayudo con eso. Tenemos planes por nivel operativo del gimnasio y podemos recomendarte el mas adecuado segun sedes, recepcion y reportes.',
                    'escalate' => false,
                ],
                [
                    'key' => 'implementation_help',
                    'label' => 'Ayuda para implementar',
                    'response' => 'Perfecto. Podemos guiarte con carga inicial, usuarios del equipo, caja, recepcion y flujo diario para arrancar sin friccion.',
                    'escalate' => false,
                ],
                [
                    'key' => 'demo_request',
                    'label' => 'Solicitar una demo',
                    'response' => 'Excelente decision. Te recomiendo compartir nombre del gimnasio, ciudad y cantidad de sedes para prepararte una demo ajustada.',
                    'escalate' => false,
                ],
                [
                    'key' => 'billing_question',
                    'label' => 'Duda de precios o cobro',
                    'response' => 'Con gusto. El valor depende del plan activo y del alcance operativo. Si quieres, te conecto con un representante para una cotizacion exacta.',
                    'escalate' => false,
                ],
                [
                    'key' => 'contact_representative',
                    'label' => 'Contactar representante',
                    'response' => 'Listo, ya estoy notificando a soporte SuperAdmin para que te atienda por este chat.',
                    'escalate' => true,
                ],
            ],
        ],
        'gym_panel' => [
            'assistant_name' => 'Soporte operativo',
            'assistant_subtitle' => 'Asistencia para gimnasios activos',
            'welcome_message' => 'Hola equipo. Este canal es para soporte operativo del gimnasio con SuperAdmin.',
            'fallback_message' => 'Recibido. Puedo ayudarte con errores tecnicos, configuracion y suscripcion. Si lo necesitas, escalo a un representante.',
            'quick_replies' => [
                [
                    'key' => 'technical_issue',
                    'label' => 'Tengo un problema tecnico',
                    'response' => 'Vamos paso a paso: indica modulo, accion exacta y mensaje de error. Asi soporte puede resolverlo mas rapido.',
                    'escalate' => false,
                ],
                [
                    'key' => 'subscription_billing',
                    'label' => 'Ayuda con suscripcion',
                    'response' => 'Claro. Puedo registrar tu caso de pagos, renovacion o reactivacion para que SuperAdmin lo revise en prioridad.',
                    'escalate' => false,
                ],
                [
                    'key' => 'users_permissions',
                    'label' => 'Usuarios y permisos',
                    'response' => 'Perfecto. Puedes indicar si el ajuste es para owner, cajero o accesos por modulo y te guiamos con la configuracion correcta.',
                    'escalate' => false,
                ],
                [
                    'key' => 'training_help',
                    'label' => 'Capacitacion del equipo',
                    'response' => 'Excelente. Podemos coordinar una sesion enfocada en recepcion, clientes, caja y reportes de tu gimnasio.',
                    'escalate' => false,
                ],
                [
                    'key' => 'contact_representative',
                    'label' => 'Contactar representante',
                    'response' => 'Listo, estoy pasando tu conversacion a soporte SuperAdmin.',
                    'escalate' => true,
                ],
            ],
        ],
    ],
    'keyword_triggers' => [
        'representative' => ['representante', 'agente', 'humano', 'asesor', 'soporte', 'admin'],
        'technical' => ['error', 'falla', 'bug', 'no funciona', 'problema tecnico'],
        'billing' => ['factura', 'facturacion', 'pago', 'cobro', 'suscripcion', 'renovacion'],
        'training' => ['capacitacion', 'entrenamiento', 'equipo', 'induccion'],
        'plans' => ['plan', 'precio', 'cotizacion', 'demo'],
    ],
    'agent_online_message' => 'SuperAdmin esta conectado. Ya puedes continuar la conversacion en vivo por este chat.',
    'agent_offline_message' => 'SuperAdmin no esta conectado en este momento. El bot seguira ayudandote y dejaremos tu caso en cola para respuesta humana.',
];

