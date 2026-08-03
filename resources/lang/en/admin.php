<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Vcookiebar',
        'settings' => 'Settings',
        'label' => 'Cookie Bar',
    ],

    'pages' => [
        'settings_title' => 'Vcookiebar settings',
    ],

    'sections' => [
        'general' => 'General',
        'policies' => 'Policy links',
        'policies_help' => 'Optional. Use an internal page, a site path, or an external URL (same options as Voodbuilder buttons when available).',
        'visible' => 'Categories to show',
        'visible_help' => 'Choose which optional categories appear in Customize. Necessary is always shown.',
        'defaults' => 'Default preferences',
        'defaults_help' => 'Applied before the visitor chooses. Necessary cookies stay enabled.',
        'appearance' => 'Appearance',
        'appearance_help' => 'Placement, theme, and colors. With Voodbuilder, “Voodflow theme” follows the site light/dark palette automatically.',
    ],

    'fields' => [
        'enabled' => 'Enable cookie bar',
        'enabled_help' => 'Disables the public consent banner, endpoint, and runtime when off.',
        'privacy_policy_url' => 'Privacy policy URL',
        'privacy_policy' => 'Privacy policy',
        'cookie_policy' => 'Cookie policy',
        'consent_cookie' => 'Consent cookie name',
        'category_necessary' => 'Necessary',
        'category_preferences' => 'Preferences',
        'category_analytics' => 'Analytics',
        'category_marketing' => 'Marketing',
        'link_type_url' => 'External URL',
        'link_type_path' => 'Site path',
        'link_target' => 'Link',
        'link_target_url_help' => 'Absolute URL (https://…).',
        'link_target_path_help' => 'Path on this site, e.g. /privacy.',
        'placement' => 'Banner placement',
        'placement_bottom' => 'Bottom (full width)',
        'placement_bottom_right' => 'Bottom right (floating)',
        'placement_bottom_left' => 'Bottom left (floating)',
        'placement_top' => 'Top (full width)',
        'theme' => 'Color theme',
        'theme_voodflow' => 'Voodflow theme (auto light/dark)',
        'theme_auto' => 'System auto (prefers-color-scheme)',
        'theme_custom' => 'Custom colors',
        'theme_help' => 'Custom colors apply only when “Custom colors” is selected.',
        'reopen_icon' => 'Show reopen preferences icon',
        'reopen_icon_help' => 'Subtle floating control after consent so visitors can change preferences.',
        'color_panel_bg' => 'Panel background',
        'color_text' => 'Text',
        'color_muted' => 'Muted text',
        'color_button_primary_bg' => 'Primary button background',
        'color_button_primary_text' => 'Primary button text',
        'color_button_bg' => 'Secondary button background',
        'color_button_text' => 'Secondary button text',
    ],

    'actions' => [
        'save' => 'Save settings',
    ],

    'notifications' => [
        'settings_saved' => 'Vcookiebar settings saved.',
    ],
];
