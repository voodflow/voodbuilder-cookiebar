<?php

declare(strict_types=1);

namespace Voodflow\VoodbuilderCookiebar;

use Voodflow\Voodbuilder\Modules\Cookiebar\CookiebarModule;
use Voodflow\Voodbuilder\Support\GrapesJs\GrapesJsEditorGate;
use Voodflow\Voodbuilder\Voodbuilder;
use Voodflow\VoodbuilderCookiebar\Support\VoodbuilderCookiebarEditorLabels;

/**
 * Runtime activation for the Filament plugin.
 *
 * Installing the Composer package alone is not enough: the host must register
 * VoodbuilderCookiebarPlugin on a Filament panel (unless auto_register_module is on).
 */
final class VoodbuilderCookiebar
{
    private static bool $activated = false;

    private static bool $labelsRegistered = false;

    public static function reset(): void
    {
        self::$activated = false;
        self::$labelsRegistered = false;
    }

    public static function activate(): void
    {
        self::$activated = true;
        self::ensureModuleRegistered();
        self::ensureEditorLabelsRegistered();
    }

    public static function isActivated(): bool
    {
        return self::$activated;
    }

    public static function ensureModuleRegistered(): void
    {
        if (! class_exists(Voodbuilder::class)) {
            return;
        }

        if (Voodbuilder::modules()->has(CookiebarModule::ID)) {
            return;
        }

        Voodbuilder::registerModule(
            new CookiebarModule,
            enabled: self::moduleShouldBeEnabled(),
        );
    }

    public static function ensureEditorLabelsRegistered(): void
    {
        if (self::$labelsRegistered) {
            return;
        }

        GrapesJsEditorGate::registerLabelProvider(
            static fn (): array => VoodbuilderCookiebarEditorLabels::grapesJsLabels(),
        );

        self::$labelsRegistered = true;
    }

    public static function moduleShouldBeEnabled(): bool
    {
        return (bool) config('voodbuilder.modules.cookiebar.enabled', true)
            && (bool) config('voodbuilder-cookiebar.enabled', true)
            && Voodbuilder::can('cookiebar.runtime');
    }
}
