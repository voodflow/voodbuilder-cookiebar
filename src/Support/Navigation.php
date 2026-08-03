<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

/**
 * Filament navigation helpers owned by Vcookiebar (independent category).
 */
final class Navigation
{
    public static function group(): string
    {
        return (string) __('vcookiebar::admin.navigation.group');
    }
}
