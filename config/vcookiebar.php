<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Master switch
    |--------------------------------------------------------------------------
    |
    | When false, Filament admin pages stay registered but the public consent
    | runtime and consent endpoint refuse to run.
    |
    */
    'enabled' => env('VCOOKIEBAR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Public route prefix
    |--------------------------------------------------------------------------
    */
    'route_prefix' => env('VCOOKIEBAR_ROUTE_PREFIX', 'vcookiebar'),

    /*
    |--------------------------------------------------------------------------
    | Consent endpoint throttle (requests per minute)
    |--------------------------------------------------------------------------
    */
    'consent_throttle' => (int) env('VCOOKIEBAR_CONSENT_THROTTLE', 60),

    /*
    |--------------------------------------------------------------------------
    | Allowed preference category keys
    |--------------------------------------------------------------------------
    |
    | Only these keys may appear in consent payloads. Unknown keys are rejected.
    |
    */
    'categories' => [
        'necessary',
        'preferences',
        'analytics',
        'marketing',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default category enabled state
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'necessary' => true,
        'preferences' => false,
        'analytics' => false,
        'marketing' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Privacy / cookie policy URLs (optional; admin can override via links)
    |--------------------------------------------------------------------------
    */
    'privacy_policy_url' => env('VCOOKIEBAR_PRIVACY_POLICY_URL'),
    'cookie_policy_url' => env('VCOOKIEBAR_COOKIE_POLICY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Which categories appear in the customize UI (necessary always shown)
    |--------------------------------------------------------------------------
    */
    'visible' => [
        'necessary' => true,
        'preferences' => true,
        'analytics' => true,
        'marketing' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Appearance (placement, theme, colors, reopen icon)
    |--------------------------------------------------------------------------
    |
    | theme: voodflow (CSS vars from host theme) | auto (prefers-color-scheme)
    |        | custom (admin color pickers)
    |
    */
    'appearance' => [
        'placement' => env('VCOOKIEBAR_PLACEMENT', 'bottom'),
        'theme' => env('VCOOKIEBAR_THEME', 'auto'),
        'reopen_icon' => env('VCOOKIEBAR_REOPEN_ICON', true),
        'colors' => [
            'panel_bg' => null,
            'text' => null,
            'muted' => null,
            'border' => null,
            'button_bg' => null,
            'button_text' => null,
            'button_primary_bg' => null,
            'button_primary_text' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie name used to store the visitor consent snapshot
    |--------------------------------------------------------------------------
    */
    'consent_cookie' => env('VCOOKIEBAR_CONSENT_COOKIE', 'vcookiebar_consent'),

    /*
    |--------------------------------------------------------------------------
    | Consent cookie lifetime in minutes
    |--------------------------------------------------------------------------
    */
    'consent_lifetime_minutes' => (int) env('VCOOKIEBAR_CONSENT_LIFETIME', 60 * 24 * 365),

    /*
    |--------------------------------------------------------------------------
    | Public banner
    |--------------------------------------------------------------------------
    */
    'banner' => [
        'enabled' => env('VCOOKIEBAR_BANNER_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-inject banner into known public layouts
    |--------------------------------------------------------------------------
    |
    | When true, the package pushes <x-vcookiebar::banner /> onto the overlays
    | stack of the listed views (typically page-builder layouts). Standalone
    | hosts can set this to false and include the component manually.
    |
    */
    'auto_inject' => env('VCOOKIEBAR_AUTO_INJECT', true),

    'auto_inject_views' => [
        'voodbuilder::layouts.app',
        'voodbuilder::layouts.chrome-app',
        'voodbuilder::layouts.page',
        'voodbuilder::layouts.landing',
        'voodbuilder::layouts.home',
        'voodbuilder::layouts.doc',
        'voodbuilder::layouts.full-width',
        'vdocs::layouts.voodbuilder',
        'vtuts::layouts.voodbuilder',
        'vtuts::layouts.voodbuilder-page',
    ],
];
