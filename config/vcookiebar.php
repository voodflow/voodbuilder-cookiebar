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
    |
    | Optional categories MUST stay false for GDPR/ePrivacy opt-in (no
    | pre-ticked boxes). Admin settings force the same rule on save.
    |
    */
    'defaults' => [
        'necessary' => true,
        'preferences' => false,
        'analytics' => false,
        'marketing' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cookie names to delete when a category is declined
    |--------------------------------------------------------------------------
    |
    | Client-readable cookies only (HttpOnly cookies cannot be cleared from JS).
    | Use a trailing * for prefix match (e.g. "_ga*"). Empty lists are fine —
    | script gating is the primary control; cleanup is best-effort hygiene.
    |
    */
    'cleanup_cookies' => [
        'preferences' => [],
        'analytics' => [
            '_ga',
            '_ga_*',
            '_gid',
            '_gat',
            '_gat_*',
            '_gcl_au',
            '_gac_*',
        ],
        'marketing' => [
            '_fbp',
            '_fbc',
        ],
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
    | theme: base (packaged light/dark) | voodbuilder (page CSS tokens)
    |        | custom (color pickers). Legacy: auto→base, voodflow→voodbuilder.
    |        | custom (admin color pickers)
    |
    */
    'appearance' => [
        'placement' => env('VCOOKIEBAR_PLACEMENT', 'bottom'),
        'theme' => env('VCOOKIEBAR_THEME', 'base'),
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
