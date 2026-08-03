<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar;

/**
 * Runtime helpers for the Vcookiebar package.
 *
 * Installing the Composer package alone is not enough for Filament admin UI:
 * the host must register VcookiebarPlugin on a panel. Public consent routes
 * load from the service provider whenever the package is enabled.
 */
final class Vcookiebar
{
    private static bool $activated = false;

    public static function reset(): void
    {
        self::$activated = false;
    }

    public static function activate(): void
    {
        self::$activated = true;
    }

    public static function isActivated(): bool
    {
        return self::$activated;
    }

    public static function isEnabled(): bool
    {
        return (bool) config('vcookiebar.enabled', true);
    }

    /**
     * @return list<string>
     */
    public static function allowedCategories(): array
    {
        /** @var list<string>|mixed $categories */
        $categories = config('vcookiebar.categories', []);

        if (! is_array($categories)) {
            return [];
        }

        return array_values(array_filter(
            $categories,
            static fn (mixed $value): bool => is_string($value) && $value !== '',
        ));
    }

    /**
     * @return array<string, bool>
     */
    public static function defaultPreferences(): array
    {
        /** @var array<string, mixed>|mixed $defaults */
        $defaults = config('vcookiebar.defaults', []);

        if (! is_array($defaults)) {
            return [];
        }

        $normalized = [];

        foreach (self::allowedCategories() as $category) {
            $normalized[$category] = (bool) ($defaults[$category] ?? false);
        }

        // Necessary cookies cannot be declined.
        if (array_key_exists('necessary', $normalized)) {
            $normalized['necessary'] = true;
        }

        return $normalized;
    }
}
