<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Voodflow\Vookiebar\Http\Controllers\ConsentController;
use Voodflow\Vookiebar\Vookiebar;

/*
|--------------------------------------------------------------------------
| Vookiebar public routes
|--------------------------------------------------------------------------
|
| Consent endpoints are independent of any page builder. They load whenever
| the package is installed and enabled. Admin UI requires VookiebarPlugin.
|
*/

if (! Vookiebar::isEnabled()) {
    return;
}

$prefix = (string) config('vookiebar.route_prefix', 'vookiebar');
$throttle = max(1, (int) config('vookiebar.consent_throttle', 60));

Route::middleware(['web', "throttle:{$throttle},1"])
    ->prefix($prefix)
    ->name('vookiebar.')
    ->group(function (): void {
        Route::post('consent', [ConsentController::class, 'store'])
            ->name('consent.store');
    });
