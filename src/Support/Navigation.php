<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Support;

/**
 * Filament navigation helpers owned by Vookiebar (independent category).
 */
final class Navigation
{
    public static function group(): string
    {
        return (string) __('vookiebar::admin.navigation.group');
    }
}
