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
    /**
     * Request attribute a page builder sets when it has taken over the viewport.
     *
     * Kept as a literal string so this package keeps booting with no page builder
     * installed — see the `package_boots_without_page_builder_classes` test.
     */
    private const EDITOR_CONTEXT_ATTRIBUTE = 'voodbuilder.editor_active';

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
     * Should the banner stay out of this response?
     *
     * A visual editor absorbs host page markup into its authoring canvas, so an injected
     * banner stops being an overlay and becomes editable content pinned over the author's
     * footer. The editor announces itself per request; we never inspect the `?edit=1`
     * query flag ourselves, because any visitor can append it and a banner that vanishes
     * for visitors is a consent failure rather than a cosmetic one.
     */
    public static function shouldStandDownForEditor(): bool
    {
        return request()->attributes->getBoolean(self::EDITOR_CONTEXT_ATTRIBUTE);
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
