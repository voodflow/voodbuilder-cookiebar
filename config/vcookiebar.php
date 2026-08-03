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
    | Privacy policy URL (optional, shown in the bar when set)
    |--------------------------------------------------------------------------
    */
    'privacy_policy_url' => env('VCOOKIEBAR_PRIVACY_POLICY_URL'),

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
];
