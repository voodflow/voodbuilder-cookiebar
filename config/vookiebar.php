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
    'enabled' => env('VOOKIEBAR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Public route prefix
    |--------------------------------------------------------------------------
    */
    'route_prefix' => env('VOOKIEBAR_ROUTE_PREFIX', 'vookiebar'),

    /*
    |--------------------------------------------------------------------------
    | Consent endpoint throttle (requests per minute)
    |--------------------------------------------------------------------------
    */
    'consent_throttle' => (int) env('VOOKIEBAR_CONSENT_THROTTLE', 60),

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
    | Privacy policy URL (optional, shown in the bar when set)
    |--------------------------------------------------------------------------
    */
    'privacy_policy_url' => env('VOOKIEBAR_PRIVACY_POLICY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cookie name used to store the visitor consent snapshot
    |--------------------------------------------------------------------------
    */
    'consent_cookie' => env('VOOKIEBAR_CONSENT_COOKIE', 'vookiebar_consent'),

    /*
    |--------------------------------------------------------------------------
    | Consent cookie lifetime in minutes
    |--------------------------------------------------------------------------
    */
    'consent_lifetime_minutes' => (int) env('VOOKIEBAR_CONSENT_LIFETIME', 60 * 24 * 365),
];
