<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vookiebar',
        'settings' => 'Settings',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Vookiebar settings',
    ],

    'sections' => [
        'general' => 'General',
        'defaults' => 'Default preferences',
        'defaults_help' => 'Applied before the visitor chooses. Necessary cookies stay enabled.',
    ],

    'fields' => [
        'enabled' => 'Enable cookie bar',
        'enabled_help' => 'Disables the public consent endpoint and runtime when off.',
        'privacy_policy_url' => 'Privacy policy URL',
        'consent_cookie' => 'Consent cookie name',
        'category_necessary' => 'Necessary',
        'category_preferences' => 'Preferences',
        'category_analytics' => 'Analytics',
        'category_marketing' => 'Marketing',
    ],

    'actions' => [
        'save' => 'Save settings',
    ],

    'notifications' => [
        'settings_saved' => 'Vookiebar settings saved.',
    ],
];
