<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

use Illuminate\Support\Facades\Cache;
use Voodflow\Vcookiebar\Vcookiebar;

/**
 * Admin-editable settings layered over published config defaults.
 *
 * Persistence uses the application cache so the package stays DB-agnostic
 * until a dedicated settings store is introduced.
 */
final class SettingsStore
{
    public const CACHE_KEY = 'vcookiebar.settings';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $visible = [];
        foreach (Vcookiebar::allowedCategories() as $category) {
            $visible[$category] = true;
        }

        return [
            'enabled' => Vcookiebar::isEnabled(),
            'privacy_policy_url' => config('vcookiebar.privacy_policy_url'),
            'consent_cookie' => config('vcookiebar.consent_cookie', 'vcookiebar_consent'),
            'defaults' => Vcookiebar::defaultPreferences(),
            'visible' => $visible,
            'copy_primary_locale' => ContentLocales::default(),
            'copy_locales' => [ContentLocales::default()],
            'copy' => [
                ContentLocales::default() => self::emptyCopyFields(),
            ],
            'privacy_link_type' => 'url',
            'privacy_link' => null,
            'privacy_open_in' => '',
            'cookie_policy_link_type' => 'url',
            'cookie_policy_link' => null,
            'cookie_policy_open_in' => '',
            'appearance' => Appearance::defaults(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        /** @var array<string, mixed> $stored */
        $stored = Cache::get(self::CACHE_KEY, []);

        if (! is_array($stored)) {
            $stored = [];
        }

        $merged = array_replace_recursive(self::defaults(), $stored);

        // Expand legacy privacy_policy_url into link fields for the form.
        if (blank($merged['privacy_link'] ?? null) && filled($merged['privacy_policy_url'] ?? null)) {
            $merged['privacy_link_type'] = 'url';
            $merged['privacy_link'] = (string) $merged['privacy_policy_url'];
        }

        $merged['appearance'] = Appearance::normalize(
            is_array($merged['appearance'] ?? null) ? $merged['appearance'] : [],
        );
        $merged['visible'] = self::normalizeVisible(
            is_array($merged['visible'] ?? null) ? $merged['visible'] : [],
        );
        $merged['defaults'] = self::optInDefaults(
            is_array($merged['defaults'] ?? null) ? $merged['defaults'] : [],
        );
        $merged = self::normalizeCopyBag($merged);

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $privacy = PolicyLink::extract($data, 'privacy');
        $cookiePolicy = PolicyLink::extract($data, 'cookie_policy');
        $normalizedCopyBag = self::normalizeCopyBag($data);
        $primary = (string) $normalizedCopyBag['copy_primary_locale'];
        $primaryFields = is_array($normalizedCopyBag['copy'][$primary] ?? null)
            ? $normalizedCopyBag['copy'][$primary]
            : self::emptyCopyFields();

        // Prefer policy links stored on the primary translation bag.
        if (filled($primaryFields['privacy_link'] ?? null) || blank($privacy['privacy_link'] ?? null)) {
            $privacy = PolicyLink::extract($primaryFields, 'privacy');
        }

        if (filled($primaryFields['cookie_policy_link'] ?? null) || blank($cookiePolicy['cookie_policy_link'] ?? null)) {
            $cookiePolicy = PolicyLink::extract($primaryFields, 'cookie_policy');
        }

        $mergedForResolve = array_merge($data, $privacy, $cookiePolicy);
        $resolvedPrivacy = PolicyLink::resolve($mergedForResolve, 'privacy', 'privacy_policy_url');

        $allowed = array_merge(
            [
                'enabled' => (bool) ($data['enabled'] ?? true),
                'privacy_policy_url' => $resolvedPrivacy,
                'consent_cookie' => (string) ($data['consent_cookie'] ?? 'vcookiebar_consent') ?: 'vcookiebar_consent',
                'defaults' => self::optInDefaults(
                    is_array($data['defaults'] ?? null) ? $data['defaults'] : [],
                ),
                'visible' => self::normalizeVisible(
                    is_array($data['visible'] ?? null) ? $data['visible'] : [],
                ),
                'appearance' => Appearance::normalize(
                    is_array($data['appearance'] ?? null) ? $data['appearance'] : [],
                ),
                'copy_primary_locale' => $normalizedCopyBag['copy_primary_locale'],
                'copy_locales' => $normalizedCopyBag['copy_locales'],
                'copy' => $normalizedCopyBag['copy'],
            ],
            $privacy,
            $cookiePolicy,
        );

        Cache::forever(self::CACHE_KEY, $allowed);
        self::mirrorConfig($allowed);
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Apply cached admin settings onto runtime config for this request.
     */
    public static function hydrateRuntimeConfig(): void
    {
        /** @var array<string, mixed> $stored */
        $stored = Cache::get(self::CACHE_KEY, []);

        if (! is_array($stored) || $stored === []) {
            return;
        }

        $merged = array_replace_recursive(self::defaults(), $stored);
        $merged['appearance'] = Appearance::normalize(
            is_array($merged['appearance'] ?? null) ? $merged['appearance'] : [],
        );
        $merged['visible'] = self::normalizeVisible(
            is_array($merged['visible'] ?? null) ? $merged['visible'] : [],
        );
        $merged['defaults'] = self::optInDefaults(
            is_array($merged['defaults'] ?? null) ? $merged['defaults'] : [],
        );
        $merged = self::normalizeCopyBag($merged);

        self::mirrorConfig($merged);
    }

    /**
     * Locales available on the host site (candidates for “Translate”).
     *
     * @return list<string>
     */
    public static function contentLocales(): array
    {
        return ContentLocales::codes();
    }

    /**
     * @return array<string, string>
     */
    public static function contentLocaleOptions(): array
    {
        return ContentLocales::options();
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public static function copyPrimaryLocale(array $settings): string
    {
        $primary = trim((string) ($settings['copy_primary_locale'] ?? ''));
        $siteDefault = ContentLocales::default();

        if ($primary !== '' && in_array($primary, ContentLocales::codes(), true)) {
            return $primary;
        }

        return $siteDefault;
    }

    /**
     * Locales that already have a derived banner-copy configuration.
     *
     * @param  array<string, mixed>  $settings
     * @return list<string>
     */
    public static function registeredCopyLocales(array $settings): array
    {
        $primary = self::copyPrimaryLocale($settings);
        /** @var list<string>|mixed $registered */
        $registered = $settings['copy_locales'] ?? [$primary];

        if (! is_array($registered) || $registered === []) {
            $registered = [$primary];
        }

        $allowed = ContentLocales::codes();
        $out = [];

        foreach ($registered as $locale) {
            if (! is_string($locale) || $locale === '') {
                continue;
            }

            if ($allowed !== [] && ! in_array($locale, $allowed, true)) {
                continue;
            }

            $out[] = $locale;
        }

        if (! in_array($primary, $out, true)) {
            array_unshift($out, $primary);
        }

        return array_values(array_unique($out));
    }

    /**
     * Resolve banner chrome copy for the current app locale.
     *
     * @param  array<string, mixed>  $settings
     * @return array<string, string>
     */
    public static function resolveCopy(array $settings): array
    {
        $fields = self::resolveCopyFields($settings);

        $map = [
            'title' => 'title',
            'message' => 'message',
            'acceptAll' => 'accept_all',
            'rejectOptional' => 'reject_optional',
            'customize' => 'customize',
            'save' => 'save',
            'privacy' => 'privacy',
            'cookiePolicy' => 'cookie_policy',
            'error' => 'error',
            'reopen' => 'reopen',
        ];

        $resolved = [];

        foreach ($map as $runtimeKey => $storageKey) {
            $override = $fields[$storageKey] ?? null;
            $resolved[$runtimeKey] = filled($override)
                ? (string) $override
                : (string) __('vcookiebar::runtime.banner.' . $storageKey);
        }

        $resolved['hideDetails'] = (string) __('vcookiebar::runtime.banner.hide_details');

        return $resolved;
    }

    /**
     * Raw copy bag for the visitor locale (falls back to primary field-by-field).
     *
     * @param  array<string, mixed>  $settings
     * @return array<string, string>
     */
    public static function resolveCopyFields(array $settings): array
    {
        $locale = app()->getLocale();
        $primary = self::copyPrimaryLocale($settings);
        $registered = self::registeredCopyLocales($settings);
        $copyBag = is_array($settings['copy'] ?? null) ? $settings['copy'] : [];

        $localeFields = in_array($locale, $registered, true) && is_array($copyBag[$locale] ?? null)
            ? self::normalizeCopyFields($copyBag[$locale])
            : self::emptyCopyFields();
        $primaryFields = is_array($copyBag[$primary] ?? null)
            ? self::normalizeCopyFields($copyBag[$primary])
            : self::emptyCopyFields();

        $merged = self::emptyCopyFields();

        foreach (self::copyKeys() as $key) {
            $value = $localeFields[$key] ?? '';

            if (! filled($value)) {
                $value = $primaryFields[$key] ?? '';
            }

            $merged[$key] = is_string($value) ? $value : '';
        }

        return $merged;
    }

    /**
     * Policy link settings for the current locale (with primary + legacy fallback).
     *
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function resolvePolicySettings(array $settings): array
    {
        $fields = self::resolveCopyFields($settings);

        return [
            'privacy_link_type' => $fields['privacy_link_type'] ?: 'url',
            'privacy_link' => $fields['privacy_link'] !== '' ? $fields['privacy_link'] : null,
            'privacy_open_in' => $fields['privacy_open_in'],
            'cookie_policy_link_type' => $fields['cookie_policy_link_type'] ?: 'url',
            'cookie_policy_link' => $fields['cookie_policy_link'] !== '' ? $fields['cookie_policy_link'] : null,
            'cookie_policy_open_in' => $fields['cookie_policy_open_in'],
            'privacy_policy_url' => $settings['privacy_policy_url'] ?? null,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function emptyCopyFields(): array
    {
        return array_merge(
            array_fill_keys(self::copyTextKeys(), ''),
            [
                'privacy_link_type' => 'url',
                'privacy_link' => '',
                'privacy_open_in' => '',
                'cookie_policy_link_type' => 'url',
                'cookie_policy_link' => '',
                'cookie_policy_open_in' => '',
            ],
        );
    }

    /**
     * Text keys shown as banner chrome (not policy links).
     *
     * @return list<string>
     */
    public static function copyTextKeys(): array
    {
        return [
            'title',
            'message',
            'accept_all',
            'reject_optional',
            'customize',
            'save',
            'privacy',
            'cookie_policy',
            'error',
            'reopen',
        ];
    }

    /**
     * @return list<string>
     */
    public static function copyKeys(): array
    {
        return array_keys(self::emptyCopyFields());
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, string>
     */
    public static function normalizeCopyFields(array $fields): array
    {
        $normalized = self::emptyCopyFields();

        foreach (self::copyTextKeys() as $key) {
            $value = $fields[$key] ?? '';
            $normalized[$key] = is_string($value) ? trim($value) : '';
        }

        foreach (['privacy', 'cookie_policy'] as $prefix) {
            $extracted = PolicyLink::extract($fields, $prefix);
            $normalized["{$prefix}_link_type"] = (string) ($extracted["{$prefix}_link_type"] ?? 'url');
            $normalized["{$prefix}_link"] = (string) ($extracted["{$prefix}_link"] ?? '');
            $normalized["{$prefix}_open_in"] = (string) ($extracted["{$prefix}_open_in"] ?? '');
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function normalizeCopyBag(array $settings): array
    {
        $primary = self::copyPrimaryLocale($settings);
        $copyRaw = is_array($settings['copy'] ?? null) ? $settings['copy'] : [];

        /** @var list<string>|null $explicit */
        $explicit = is_array($settings['copy_locales'] ?? null)
            ? array_values(array_filter(
                $settings['copy_locales'],
                static fn (mixed $locale): bool => is_string($locale) && $locale !== '',
            ))
            : null;

        if ($explicit === null || $explicit === []) {
            // Legacy: tabs for every site locale with empty fields → keep only primary
            // plus locales that actually have custom text.
            $explicit = [$primary];

            foreach ($copyRaw as $locale => $fields) {
                if (! is_string($locale) || $locale === $primary || ! is_array($fields)) {
                    continue;
                }

                if (self::copyFieldsHaveContent($fields)) {
                    $explicit[] = $locale;
                }
            }
        }

        $settings['copy_primary_locale'] = $primary;
        $settings['copy_locales'] = array_values(array_unique(array_merge([$primary], $explicit)));

        $normalizedCopy = [];

        foreach ($settings['copy_locales'] as $locale) {
            $normalizedCopy[$locale] = self::normalizeCopyFields(
                is_array($copyRaw[$locale] ?? null) ? $copyRaw[$locale] : [],
            );
        }

        // Legacy global policy links → primary locale bag when still empty.
        if (($normalizedCopy[$primary]['privacy_link'] ?? '') === ''
            && filled($settings['privacy_link'] ?? null)) {
            $normalizedCopy[$primary] = self::normalizeCopyFields(array_merge(
                $normalizedCopy[$primary],
                PolicyLink::extract($settings, 'privacy'),
            ));
        }

        if (($normalizedCopy[$primary]['cookie_policy_link'] ?? '') === ''
            && filled($settings['cookie_policy_link'] ?? null)) {
            $normalizedCopy[$primary] = self::normalizeCopyFields(array_merge(
                $normalizedCopy[$primary],
                PolicyLink::extract($settings, 'cookie_policy'),
            ));
        }

        if (($normalizedCopy[$primary]['privacy_link'] ?? '') === ''
            && filled($settings['privacy_policy_url'] ?? null)) {
            $normalizedCopy[$primary]['privacy_link_type'] = 'url';
            $normalizedCopy[$primary]['privacy_link'] = (string) $settings['privacy_policy_url'];
        }

        $settings['copy'] = $normalizedCopy;

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    public static function copyFieldsHaveContent(array $fields): bool
    {
        foreach (self::copyTextKeys() as $key) {
            if (filled($fields[$key] ?? null)) {
                return true;
            }
        }

        foreach (['privacy_link', 'cookie_policy_link'] as $key) {
            if (filled($fields[$key] ?? null)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    public static function upsertCopyLocale(string $locale, array $fields, bool $register = true): void
    {
        $settings = self::all();
        $settings['copy'][$locale] = self::normalizeCopyFields($fields);

        if ($register) {
            $registered = self::registeredCopyLocales($settings);

            if (! in_array($locale, $registered, true)) {
                $registered[] = $locale;
            }

            $settings['copy_locales'] = $registered;
        }

        self::save($settings);
    }

    public static function removeCopyLocale(string $locale): void
    {
        $settings = self::all();
        $primary = self::copyPrimaryLocale($settings);

        if ($locale === $primary) {
            throw new \InvalidArgumentException('The primary banner text locale cannot be deleted.');
        }

        $registered = array_values(array_filter(
            self::registeredCopyLocales($settings),
            static fn (string $code): bool => $code !== $locale,
        ));

        unset($settings['copy'][$locale]);
        $settings['copy_locales'] = $registered;

        self::save($settings);
    }

    /**
     * @deprecated Use emptyCopyFields() + registered locales.
     *
     * @return array<string, array<string, string>>
     */
    public static function emptyCopy(): array
    {
        return [
            ContentLocales::default() => self::emptyCopyFields(),
        ];
    }

    /**
     * @deprecated Use normalizeCopyBag().
     *
     * @param  array<string, mixed>  $copy
     * @return array<string, array<string, string>>
     */
    public static function normalizeCopy(array $copy): array
    {
        return self::normalizeCopyBag(['copy' => $copy])['copy'];
    }

    /**
     * Normalize defaults for GDPR/ePrivacy opt-in: optional categories stay off
     * until the visitor explicitly accepts them (no pre-ticked consent).
     *
     * @param  array<string, mixed>  $defaults
     * @return array<string, bool>
     */
    public static function optInDefaults(array $defaults): array
    {
        $normalized = ConsentPayload::normalize($defaults);

        foreach ($normalized as $category => $enabled) {
            if ($category !== 'necessary') {
                $normalized[$category] = false;
            }
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $visible
     * @return array<string, bool>
     */
    public static function normalizeVisible(array $visible): array
    {
        $out = [];
        foreach (Vcookiebar::allowedCategories() as $category) {
            if ($category === 'necessary') {
                $out[$category] = true;

                continue;
            }
            $out[$category] = array_key_exists($category, $visible)
                ? (bool) $visible[$category]
                : true;
        }

        return $out;
    }

    /**
     * Categories shown in the customize UI (necessary always included).
     *
     * @return list<string>
     */
    public static function visibleCategories(): array
    {
        $visible = self::normalizeVisible(
            is_array(config('vcookiebar.visible')) ? config('vcookiebar.visible') : [],
        );

        $keys = [];
        foreach (Vcookiebar::allowedCategories() as $category) {
            if ($category === 'necessary' || ($visible[$category] ?? false)) {
                $keys[] = $category;
            }
        }

        return $keys;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private static function mirrorConfig(array $settings): void
    {
        $privacyUrl = PolicyLink::resolve($settings, 'privacy', 'privacy_policy_url');
        $cookiePolicyUrl = PolicyLink::resolve($settings, 'cookie_policy');

        config([
            'vcookiebar.enabled' => (bool) ($settings['enabled'] ?? true),
            'vcookiebar.privacy_policy_url' => $privacyUrl,
            'vcookiebar.cookie_policy_url' => $cookiePolicyUrl,
            'vcookiebar.consent_cookie' => (string) ($settings['consent_cookie'] ?? 'vcookiebar_consent'),
            'vcookiebar.defaults' => is_array($settings['defaults'] ?? null)
                ? self::optInDefaults($settings['defaults'])
                : Vcookiebar::defaultPreferences(),
            'vcookiebar.visible' => self::normalizeVisible(
                is_array($settings['visible'] ?? null) ? $settings['visible'] : [],
            ),
            'vcookiebar.appearance' => Appearance::normalize(
                is_array($settings['appearance'] ?? null) ? $settings['appearance'] : [],
            ),
        ]);
    }
}
