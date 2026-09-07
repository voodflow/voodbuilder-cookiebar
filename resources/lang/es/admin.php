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
        'policies' => 'Policy links',
        'policies_help' => 'Optional.',
        'visible' => 'Categories to show',
        'visible_help' => 'Optional categories in Customize.',
        'appearance' => 'Appearance',
        'appearance_help' => 'Placement and colors.',
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
        'theme_voodflow' => 'Voodflow theme',
        'theme_auto' => 'System auto',
        'theme_custom' => 'Custom colors',
        'theme_help' => 'Custom colors when selected.',
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
        'save' => 'Guardar ajustes',
    ],

    'notifications' => [
        'settings_saved' => 'Ajustes de Vcookiebar guardados.',
    ],
];
