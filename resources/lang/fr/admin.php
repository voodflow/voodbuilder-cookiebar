<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vcookiebar',
        'settings' => 'Paramètres',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Paramètres Vcookiebar',
    ],

    'sections' => [
        'general' => 'Général',
        'defaults' => 'Préférences par défaut',
        'defaults_help' => 'Appliquées avant le choix du visiteur. Les cookies nécessaires restent actifs.',
    ],

    'fields' => [
        'enabled' => 'Activer la barre de cookies',
        'enabled_help' => 'Désactive la bannière publique, l’endpoint de consentement et le runtime.',
        'privacy_policy_url' => 'URL de la politique de confidentialité',
        'consent_cookie' => 'Nom du cookie de consentement',
        'category_necessary' => 'Nécessaires',
        'category_preferences' => 'Préférences',
        'category_analytics' => 'Analytique',
        'category_marketing' => 'Marketing',
    ],

    'actions' => [
        'save' => 'Enregistrer',
    ],

    'notifications' => [
        'settings_saved' => 'Paramètres Vcookiebar enregistrés.',
    ],
];
