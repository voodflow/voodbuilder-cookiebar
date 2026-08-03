<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Voodflow\Vcookiebar\Http\Controllers\ConsentController;
use Voodflow\Vcookiebar\Vcookiebar;

/*
|--------------------------------------------------------------------------
| Vcookiebar public routes
|--------------------------------------------------------------------------
|
| Consent endpoints are independent of any page builder. They load whenever
| the package is installed and enabled. Admin UI requires VcookiebarPlugin.
|
*/

if (! Vcookiebar::isEnabled()) {
    return;
}

$prefix = (string) config('vcookiebar.route_prefix', 'vcookiebar');
$throttle = max(1, (int) config('vcookiebar.consent_throttle', 60));

Route::middleware(['web', "throttle:{$throttle},1"])
    ->prefix($prefix)
    ->name('vcookiebar.')
    ->group(function (): void {
        Route::post('consent', [ConsentController::class, 'store'])
            ->name('consent.store');
    });
