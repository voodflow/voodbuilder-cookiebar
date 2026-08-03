<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vcookiebar',
        'settings' => 'Impostazioni',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Impostazioni Vcookiebar',
    ],

    'sections' => [
        'general' => 'Generale',
        'defaults' => 'Preferenze predefinite',
        'defaults_help' => 'Applicate prima della scelta del visitatore. I cookie necessari restano attivi.',
    ],

    'fields' => [
        'enabled' => 'Abilita cookie bar',
        'enabled_help' => 'Se disattivato, banner pubblico, endpoint e runtime non rispondono.',
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
        'settings_saved' => 'Impostazioni Vcookiebar salvate.',
    ],
];
