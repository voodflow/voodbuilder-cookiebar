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

    public const THEME_VOODFLOW = 'voodflow';

    public const THEME_CUSTOM = 'custom';

    public const THEME_AUTO = 'auto';

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
        // Do not autoload optional Voodbuilder classes during package boot
        // (keeps the package isolatable in its own testbench).
        $theme = (
            class_exists(\Voodflow\Voodbuilder\Voodbuilder::class, false)
            || class_exists(\Voodflow\Voodbuilder\Support\ThemePalette::class, false)
        ) ? self::THEME_VOODFLOW : self::THEME_AUTO;

        return [
            'placement' => 'bottom',
            'theme' => $theme,
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

        $theme = strtolower(trim((string) ($raw['theme'] ?? $defaults['theme'])));
        if (! in_array($theme, [self::THEME_VOODFLOW, self::THEME_CUSTOM, self::THEME_AUTO], true)) {
            $theme = $defaults['theme'];
        }

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
