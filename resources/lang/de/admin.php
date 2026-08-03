<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vookiebar',
        'settings' => 'Einstellungen',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Vookiebar-Einstellungen',
    ],

    'sections' => [
        'general' => 'Allgemein',
        'defaults' => 'Standard-Einstellungen',
        'defaults_help' => 'Gilt bevor der Besucher wählt. Notwendige Cookies bleiben aktiv.',
    ],

    'fields' => [
        'enabled' => 'Cookie-Bar aktivieren',
        'enabled_help' => 'Deaktiviert den öffentlichen Consent-Endpunkt und die Runtime.',
        'privacy_policy_url' => 'URL der Datenschutzerklärung',
        'consent_cookie' => 'Name des Consent-Cookies',
        'category_necessary' => 'Notwendig',
        'category_preferences' => 'Präferenzen',
        'category_analytics' => 'Analyse',
        'category_marketing' => 'Marketing',
    ],

    'actions' => [
        'save' => 'Einstellungen speichern',
    ],

    'notifications' => [
        'settings_saved' => 'Vookiebar-Einstellungen gespeichert.',
    ],
];
