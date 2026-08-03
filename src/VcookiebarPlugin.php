<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Voodflow\Vcookiebar\Filament\Pages\VcookiebarSettingsPage;

/**
 * Filament 5 plugin for Vcookiebar admin (own navigation group, own settings).
 *
 * Registering this plugin on a panel activates admin pages. Omitting it from
 * ->plugins([...]) hides admin UI even if the Composer package is installed.
 */
class VcookiebarPlugin implements Plugin
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
        return 'vcookiebar';
    }

    public function register(Panel $panel): void
    {
        Vcookiebar::activate();

        $panel->pages([
            VcookiebarSettingsPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
