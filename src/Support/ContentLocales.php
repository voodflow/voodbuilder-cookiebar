<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

/**
 * Discovers which locales the host site actually uses.
 *
 * Priority (no plugin classes):
 * 1. explicit `vcookiebar.content_locales`
 * 2. `config('app.locales')`
 * 3. `config('cosmolab.locales')` (host app config, if present)
 * 4. `vcookiebar.site_locales` / `APP_LOCALES` (comma-separated)
 * 5. `app.locale` alone
 *
 * Banner text translations are derived on demand (like SitePage), not
 * pre-created for every site locale.
 */
final class ContentLocales
{
    /**
     * @return list<string>
     */
    public static function codes(): array
    {
        $configured = config('vcookiebar.content_locales');

        if (is_array($configured) && $configured !== []) {
            return self::normalizeList($configured);
        }

        return self::discover();
    }

    /**
     * Default / primary locale for the host site.
     */
    public static function default(): string
    {
        $codes = self::codes();

        foreach ([
            config('app.locale'),
            config('cosmolab.default_locale'),
        ] as $candidate) {
            $locale = trim((string) $candidate);

            if ($locale !== '' && in_array($locale, $codes, true)) {
                return $locale;
            }
        }

        return $codes[0] ?? 'en';
    }

    /**
     * Locale code => human label for Filament UI.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $labels = self::discoverLabels();
        $options = [];

        foreach (self::codes() as $code) {
            $options[$code] = $labels[$code] ?? self::fallbackLabel($code);
        }

        return $options;
    }

    public static function label(string $locale): string
    {
        return self::options()[$locale] ?? self::fallbackLabel($locale);
    }

    /**
     * @return list<string>
     */
    private static function discover(): array
    {
        foreach ([
            config('app.locales'),
            config('cosmolab.locales'),
        ] as $candidate) {
            $map = self::normalizeMap($candidate);

            if ($map !== []) {
                return array_keys($map);
            }
        }

        $fromEnv = self::normalizeEnvList(config('vcookiebar.site_locales'));

        if ($fromEnv !== []) {
            return $fromEnv;
        }

        $locale = trim((string) config('app.locale', 'en'));

        return [$locale !== '' ? $locale : 'en'];
    }

    /**
     * @return array<string, string>
     */
    private static function discoverLabels(): array
    {
        foreach ([
            config('app.locales'),
            config('cosmolab.locales'),
        ] as $candidate) {
            $map = self::normalizeMap($candidate);

            if ($map !== []) {
                return $map;
            }
        }

        $options = [];

        foreach (self::codes() as $code) {
            $options[$code] = self::fallbackLabel($code);
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    private static function normalizeList(mixed $configured): array
    {
        if (! is_array($configured)) {
            return [];
        }

        return array_values(array_filter(
            $configured,
            static fn (mixed $value): bool => is_string($value) && $value !== '',
        ));
    }

    /**
     * @return list<string>
     */
    private static function normalizeEnvList(mixed $value): array
    {
        if (is_array($value)) {
            return self::normalizeList($value);
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $part): string => trim($part),
            explode(',', $value),
        )));
    }

    /**
     * @return array<string, string>
     */
    private static function normalizeMap(mixed $configured): array
    {
        if (! is_array($configured) || $configured === []) {
            return [];
        }

        $locales = [];

        foreach ($configured as $code => $label) {
            if (is_int($code) && is_string($label) && $label !== '') {
                $locales[$label] = self::fallbackLabel($label);

                continue;
            }

            if (is_string($code) && $code !== '' && is_string($label) && $label !== '') {
                $locales[$code] = $label;
            }
        }

        return $locales;
    }

    private static function fallbackLabel(string $locale): string
    {
        return match ($locale) {
            'en' => 'English',
            'it' => 'Italiano',
            'de' => 'Deutsch',
            'es' => 'Español',
            'fr' => 'Français',
            default => strtoupper($locale),
        };
    }
}
