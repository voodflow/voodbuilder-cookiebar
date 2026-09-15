<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

/**
 * Derive banner-copy translations the same way SitePage / Vtuts do:
 * one primary configuration, then explicit “Translate” clones for other locales.
 */
final class CopyTranslation
{
    /**
     * @return array<string, string> locale => label
     */
    public static function availableTargetLocales(?string $currentLocale = null): array
    {
        $settings = SettingsStore::all();
        $existing = SettingsStore::registeredCopyLocales($settings);
        $current = $currentLocale ?? SettingsStore::copyPrimaryLocale($settings);

        return collect(ContentLocales::options())
            ->reject(fn (string $label, string $code): bool => $code === $current || in_array($code, $existing, true))
            ->all();
    }

    public static function createFrom(string $sourceLocale, string $targetLocale): void
    {
        if (! in_array($targetLocale, ContentLocales::codes(), true)) {
            throw new \InvalidArgumentException("Unsupported locale [{$targetLocale}].");
        }

        if ($targetLocale === $sourceLocale) {
            throw new \InvalidArgumentException('Target locale must differ from the source copy.');
        }

        $settings = SettingsStore::all();
        $registered = SettingsStore::registeredCopyLocales($settings);

        if (in_array($targetLocale, $registered, true)) {
            throw new \InvalidArgumentException("A banner text translation for [{$targetLocale}] already exists.");
        }

        $source = is_array($settings['copy'][$sourceLocale] ?? null)
            ? $settings['copy'][$sourceLocale]
            : SettingsStore::emptyCopyFields();

        SettingsStore::upsertCopyLocale($targetLocale, $source, register: true);
    }

    public static function delete(string $locale): void
    {
        SettingsStore::removeCopyLocale($locale);
    }
}
