<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

/**
 * Translation helper with fallback when published lang lags behind the package.
 */
final class Lang
{
    public static function get(string $key, ?string $fallback = null, array $replace = []): string
    {
        $full = 'vcookiebar::' . $key;
        $translated = __($full, $replace);

        if ($translated === $full || $translated === $key) {
            return $fallback ?? $key;
        }

        return $translated;
    }
}
