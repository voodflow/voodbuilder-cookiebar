<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

/**
 * Banner placement and color tokens for public runtime.
 */
final class Appearance
{
    public const PLACEMENTS = [
        'bottom',
        'bottom-right',
        'bottom-left',
        'top',
    ];

    /** Packaged light/dark presets (prefers-color-scheme + html.dark). */
    public const THEME_BASE = 'base';

    /** Inherit VoodBuilder page theme tokens (--color-vp-*). */
    public const THEME_VOODBUILDER = 'voodbuilder';

    public const THEME_CUSTOM = 'custom';

    /** @deprecated Use THEME_BASE */
    public const THEME_AUTO = 'auto';

    /** @deprecated Use THEME_VOODBUILDER */
    public const THEME_VOODFLOW = 'voodflow';

    /**
     * @return array{
     *     placement: string,
     *     theme: string,
     *     reopen_icon: bool,
     *     colors: array<string, string|null>
     * }
     */
    public static function defaults(): array
    {
        return [
            'placement' => 'bottom',
            'theme' => self::hasVoodbuilderLoaded() ? self::THEME_VOODBUILDER : self::THEME_BASE,
            'reopen_icon' => true,
            'colors' => [
                'panel_bg' => null,
                'text' => null,
                'muted' => null,
                'border' => null,
                'button_bg' => null,
                'button_text' => null,
                'button_primary_bg' => null,
                'button_primary_text' => null,
            ],
        ];
    }

    /**
     * True when VoodBuilder is available (autoload allowed — Filament / runtime).
     */
    public static function hasVoodbuilder(): bool
    {
        return class_exists(\Voodflow\Voodbuilder\Voodbuilder::class)
            || class_exists(\Voodflow\Voodbuilder\Support\ThemePalette::class);
    }

    /**
     * Soft detection that does not autoload (safe during package boot / testbench).
     */
    public static function hasVoodbuilderLoaded(): bool
    {
        return class_exists(\Voodflow\Voodbuilder\Voodbuilder::class, false)
            || class_exists(\Voodflow\Voodbuilder\Support\ThemePalette::class, false);
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array{
     *     placement: string,
     *     theme: string,
     *     reopen_icon: bool,
     *     colors: array<string, string|null>
     * }
     */
    public static function normalize(array $raw): array
    {
        $defaults = self::defaults();
        $placement = strtolower(trim((string) ($raw['placement'] ?? $defaults['placement'])));
        if (! in_array($placement, self::PLACEMENTS, true)) {
            $placement = $defaults['placement'];
        }

        $theme = self::normalizeTheme((string) ($raw['theme'] ?? $defaults['theme']));

        $colorsIn = is_array($raw['colors'] ?? null) ? $raw['colors'] : [];
        $colors = [];
        foreach ($defaults['colors'] as $key => $fallback) {
            $colors[$key] = self::sanitizeHex($colorsIn[$key] ?? null) ?? $fallback;
        }

        return [
            'placement' => $placement,
            'theme' => $theme,
            'reopen_icon' => filter_var($raw['reopen_icon'] ?? $defaults['reopen_icon'], FILTER_VALIDATE_BOOLEAN),
            'colors' => $colors,
        ];
    }

    /**
     * Map legacy theme ids and fall back when VoodBuilder is missing.
     */
    public static function normalizeTheme(string $theme): string
    {
        $theme = strtolower(trim($theme));

        $theme = match ($theme) {
            self::THEME_AUTO, 'system' => self::THEME_BASE,
            self::THEME_VOODFLOW => self::THEME_VOODBUILDER,
            default => $theme,
        };

        if (! in_array($theme, [self::THEME_BASE, self::THEME_VOODBUILDER, self::THEME_CUSTOM], true)) {
            return self::THEME_BASE;
        }

        return $theme;
    }

    public static function sanitizeHex(mixed $raw): ?string
    {
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $color = strtolower(trim($raw));
        if ($color[0] !== '#') {
            $color = '#'.$color;
        }

        if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/', $color) !== 1) {
            return null;
        }

        if (strlen($color) === 4) {
            return '#'.$color[1].$color[1].$color[2].$color[2].$color[3].$color[3];
        }

        return $color;
    }

    /**
     * CSS custom properties for the banner root.
     *
     * @param  array{
     *     placement: string,
     *     theme: string,
     *     reopen_icon: bool,
     *     colors: array<string, string|null>
     * }  $appearance
     * @return array<string, string>
     */
    public static function cssVariables(array $appearance): array
    {
        $vars = [];
        $c = $appearance['colors'];

        $map = [
            'panel_bg' => '--vcb-panel-bg',
            'text' => '--vcb-text',
            'muted' => '--vcb-muted',
            'border' => '--vcb-border',
            'button_bg' => '--vcb-btn-bg',
            'button_text' => '--vcb-btn-text',
            'button_primary_bg' => '--vcb-btn-primary-bg',
            'button_primary_text' => '--vcb-btn-primary-text',
        ];

        if ($appearance['theme'] === self::THEME_CUSTOM) {
            foreach ($map as $key => $cssVar) {
                if (filled($c[$key] ?? null)) {
                    $vars[$cssVar] = (string) $c[$key];
                }
            }
        }

        return $vars;
    }
}
