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
        'policies' => 'Policy links',
        'policies_help' => 'Optional.',
        'visible' => 'Categories to show',
        'visible_help' => 'Optional categories in Customize.',
        'appearance' => 'Appearance',
        'appearance_help' => 'Placement and colors.',
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
        'privacy_policy' => 'Privacy policy',
        'cookie_policy' => 'Cookie policy',
        'link_type_url' => 'External URL',
        'link_type_path' => 'Site path',
        'link_target' => 'Link',
        'link_target_url_help' => 'Absolute URL.',
        'link_target_path_help' => 'Site path.',
        'placement' => 'Banner placement',
        'placement_bottom' => 'Bottom',
        'placement_bottom_right' => 'Bottom right',
        'placement_bottom_left' => 'Bottom left',
        'placement_top' => 'Top',
        'theme' => 'Color theme',
        'theme_base' => 'Base (built-in light / dark)',
        'theme_voodbuilder' => 'VoodBuilder (page theme)',
        'theme_custom' => 'Custom colors',
        'theme_help' => 'Base / VoodBuilder / Custom.',
        'reopen_icon' => 'Show reopen icon',
        'reopen_icon_help' => 'After consent.',
        'color_panel_bg' => 'Panel background',
        'color_text' => 'Text',
        'color_muted' => 'Muted text',
        'color_button_primary_bg' => 'Primary button background',
        'color_button_primary_text' => 'Primary button text',
        'color_button_bg' => 'Secondary button background',
        'color_button_text' => 'Secondary button text',
    ],

    'actions' => [
        'save' => 'Enregistrer',
    ],

    'notifications' => [
        'settings_saved' => 'Paramètres Vcookiebar enregistrés.',
    ],
];
