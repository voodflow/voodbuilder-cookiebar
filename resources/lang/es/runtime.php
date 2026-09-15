<?php

declare(strict_types=1);

return [
    'consent' => [
        'unknown_categories' => 'Una o más categorías de preferencia no están permitidas.',
        'invalid_preference' => 'Cada preferencia debe ser un valor booleano.',
    ],

    'banner' => [
        'title' => 'Preferencias de cookies',
        'message' => 'Usamos cookies para que el sitio funcione y, con tu consentimiento, para medirlo y mejorarlo. Puedes aceptar todas, rechazar las opcionales o elegir categorías.',
        'accept_all' => 'Aceptar todas',
        'reject_optional' => 'Rechazar opcionales',
        'customize' => 'Personalizar',
        'hide_details' => 'Ocultar',
        'save' => 'Guardar preferencias',
        'privacy' => 'Política de privacidad',
        'cookie_policy' => 'Cookie policy',
        'reopen' => 'Cookie preferences',
        'error' => 'No se pudieron guardar las preferencias. Inténtalo de nuevo.',
        'categories' => [
            'necessary' => [
                'label' => 'Necesarias',
                'description' => 'Requeridas para seguridad, almacenamiento del consentimiento y funciones básicas. Siempre activas.',
            ],
            'preferences' => [
                'label' => 'Preferencias',
                'description' => 'Recuerdan opciones como el idioma o la visualización.',
            ],
            'analytics' => [
                'label' => 'Analítica',
                'description' => 'Ayudan a entender cómo usan el sitio los visitantes para mejorarlo.',
            ],
            'marketing' => [
                'label' => 'Marketing',
                'description' => 'Se usan para contenido personalizado y medición de campañas.',
            ],
        ],
    ],
];
