<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Support;

use Illuminate\Support\Facades\Cache;
use Voodflow\Vookiebar\Vookiebar;

/**
 * Admin-editable settings layered over published config defaults.
 *
 * Persistence uses the application cache so the package stays DB-agnostic
 * until a dedicated settings store is introduced.
 */
final class SettingsStore
{
    public const CACHE_KEY = 'vookiebar.settings';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'enabled' => Vookiebar::isEnabled(),
            'privacy_policy_url' => config('vookiebar.privacy_policy_url'),
            'consent_cookie' => config('vookiebar.consent_cookie', 'vookiebar_consent'),
            'defaults' => Vookiebar::defaultPreferences(),
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

        return array_replace_recursive(self::defaults(), $stored);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $allowed = [
            'enabled' => (bool) ($data['enabled'] ?? true),
            'privacy_policy_url' => filled($data['privacy_policy_url'] ?? null)
                ? (string) $data['privacy_policy_url']
                : null,
            'consent_cookie' => (string) ($data['consent_cookie'] ?? 'vookiebar_consent'),
            'defaults' => ConsentPayload::normalize(
                is_array($data['defaults'] ?? null) ? $data['defaults'] : [],
            ),
        ];

        Cache::forever(self::CACHE_KEY, $allowed);

        // Mirror into runtime config for the current process.
        config([
            'vookiebar.enabled' => $allowed['enabled'],
            'vookiebar.privacy_policy_url' => $allowed['privacy_policy_url'],
            'vookiebar.consent_cookie' => $allowed['consent_cookie'],
            'vookiebar.defaults' => $allowed['defaults'],
        ]);
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
