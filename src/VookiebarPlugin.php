<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Voodflow\Vookiebar\Filament\Pages\VookiebarSettingsPage;

/**
 * Filament 5 plugin for Vookiebar admin (own navigation group, own settings).
 *
 * Registering this plugin on a panel activates admin pages. Omitting it from
 * ->plugins([...]) hides admin UI even if the Composer package is installed.
 */
class VookiebarPlugin implements Plugin
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
        return 'vookiebar';
    }

    public function register(Panel $panel): void
    {
        Vookiebar::activate();

        $panel->pages([
            VookiebarSettingsPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
