<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vcookiebar',
        'settings' => 'Einstellungen',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Vcookiebar-Einstellungen',
    ],

    'sections' => [
        'general' => 'Allgemein',
        'policies' => 'Policy links',
        'policies_help' => 'Optional.',
        'visible' => 'Categories to show',
        'visible_help' => 'Optional categories in Customize.',
        'appearance' => 'Appearance',
        'appearance_help' => 'Placement and colors.',
        'defaults' => 'Standard-Einstellungen',
        'defaults_help' => 'Gilt bevor der Besucher wählt. Notwendige Cookies bleiben aktiv.',
    ],

    'fields' => [
        'enabled' => 'Cookie-Bar aktivieren',
        'enabled_help' => 'Deaktiviert Banner, öffentlichen Consent-Endpunkt und Runtime.',
        'privacy_policy_url' => 'URL der Datenschutzerklärung',
        'consent_cookie' => 'Name des Consent-Cookies',
        'category_necessary' => 'Notwendig',
        'category_preferences' => 'Präferenzen',
        'category_analytics' => 'Analyse',
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
        'save' => 'Einstellungen speichern',
    ],

    'notifications' => [
        'settings_saved' => 'Vcookiebar-Einstellungen gespeichert.',
    ],
];
