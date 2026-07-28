<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Cookie Bar plugin features
    |--------------------------------------------------------------------------
    |
    | Master switch for the package. Also gated by voodbuilder.modules.cookiebar
    | and entitlements.
    |
    */
    'enabled' => env('COOKIEBAR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Auto-register CookiebarModule without Filament plugin
    |--------------------------------------------------------------------------
    |
    | When false (default), the module activates only after
    | VoodbuilderCookiebarPlugin is registered on a Filament panel.
    |
    | Enable for Testbench or rare headless installs that never use Filament.
    |
    */
    'auto_register_module' => env('COOKIEBAR_AUTO_REGISTER', false),
];
