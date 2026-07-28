<?php

declare(strict_types=1);

namespace Voodflow\VoodbuilderCookiebar;

use Filament\Contracts\Plugin;
use Filament\Panel;

/**
 * FilamentPHP 5 plugin that extends VoodBuilder with Cookie Bar.
 *
 * Registering this plugin on a panel activates CookiebarModule (routes, editor UI, runtime).
 * Omitting it from ->plugins([...]) disables the feature even if the Composer package is installed.
 */
class VoodbuilderCookiebarPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'voodbuilder-cookiebar';
    }

    public function register(Panel $panel): void
    {
        VoodbuilderCookiebar::activate();

        if (! VoodbuilderCookiebar::moduleShouldBeEnabled()) {
            return;
        }

        // Filament resources will be registered here after extraction from core.
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
