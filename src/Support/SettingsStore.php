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
            'privacy_link_type' => 'url',
            'privacy_link' => null,
            'cookie_policy_link_type' => 'url',
            'cookie_policy_link' => null,
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

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $privacy = PolicyLink::extract($data, 'privacy');
        $cookiePolicy = PolicyLink::extract($data, 'cookie_policy');

        $mergedForResolve = array_merge($data, $privacy, $cookiePolicy);
        $resolvedPrivacy = PolicyLink::resolve($mergedForResolve, 'privacy', 'privacy_policy_url');

        $allowed = array_merge(
            [
                'enabled' => (bool) ($data['enabled'] ?? true),
                'privacy_policy_url' => $resolvedPrivacy,
                'consent_cookie' => (string) ($data['consent_cookie'] ?? 'vcookiebar_consent') ?: 'vcookiebar_consent',
                'defaults' => ConsentPayload::normalize(
                    is_array($data['defaults'] ?? null) ? $data['defaults'] : [],
                ),
                'visible' => self::normalizeVisible(
                    is_array($data['visible'] ?? null) ? $data['visible'] : [],
                ),
                'appearance' => Appearance::normalize(
                    is_array($data['appearance'] ?? null) ? $data['appearance'] : [],
                ),
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

        self::mirrorConfig($merged);
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
                ? ConsentPayload::normalize($settings['defaults'])
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
