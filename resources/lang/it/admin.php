<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vookiebar',
        'settings' => 'Impostazioni',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Impostazioni Vookiebar',
    ],

    'sections' => [
        'general' => 'Generale',
        'defaults' => 'Preferenze predefinite',
        'defaults_help' => 'Applicate prima della scelta del visitatore. I cookie necessari restano attivi.',
    ],

    'fields' => [
        'enabled' => 'Abilita cookie bar',
        'enabled_help' => 'Se disattivato, endpoint pubblico e runtime non rispondono.',
        'privacy_policy_url' => 'URL privacy policy',
        'consent_cookie' => 'Nome cookie di consenso',
        'category_necessary' => 'Necessari',
        'category_preferences' => 'Preferenze',
        'category_analytics' => 'Analitici',
        'category_marketing' => 'Marketing',
    ],

    'actions' => [
        'save' => 'Salva impostazioni',
    ],

    'notifications' => [
        'settings_saved' => 'Impostazioni Vookiebar salvate.',
    ],
];
