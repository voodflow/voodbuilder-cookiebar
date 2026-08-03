<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vcookiebar',
        'settings' => 'Ajustes',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Ajustes de Vcookiebar',
    ],

    'sections' => [
        'general' => 'General',
        'defaults' => 'Preferencias predeterminadas',
        'defaults_help' => 'Se aplican antes de la elección del visitante. Las cookies necesarias permanecen activas.',
    ],

    'fields' => [
        'enabled' => 'Activar cookie bar',
        'enabled_help' => 'Desactiva el banner público, el endpoint de consentimiento y el runtime.',
        'privacy_policy_url' => 'URL de política de privacidad',
        'consent_cookie' => 'Nombre de la cookie de consentimiento',
        'category_necessary' => 'Necesarias',
        'category_preferences' => 'Preferencias',
        'category_analytics' => 'Analítica',
        'category_marketing' => 'Marketing',
    ],

    'actions' => [
        'save' => 'Guardar ajustes',
    ],

    'notifications' => [
        'settings_saved' => 'Ajustes de Vcookiebar guardados.',
    ],
];
