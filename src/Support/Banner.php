<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

use Voodflow\Vcookiebar\Vcookiebar;

/**
 * Public consent banner helpers (agnostic of any page builder).
 */
final class Banner
{
    public static function shouldRender(): bool
    {
        if (! Vcookiebar::isEnabled()) {
            return false;
        }

        if (! (bool) config('vcookiebar.banner.enabled', true)) {
            return false;
        }

        $cookieName = (string) config('vcookiebar.consent_cookie', 'vcookiebar_consent');

        return ConsentPayload::decode(request()->cookie($cookieName)) === null;
    }

    /**
     * Payload embedded into the banner for the public runtime script.
     *
     * @return array{
     *     endpoint: string,
     *     csrf: string,
     *     preferences: array<string, bool>,
     *     categories: list<array{key: string, label: string, description: string, locked: bool}>,
     *     privacyPolicyUrl: string|null,
     *     copy: array<string, string>
     * }
     */
    public static function runtimeConfig(): array
    {
        $preferences = Vcookiebar::defaultPreferences();
        $categories = [];

        foreach (Vcookiebar::allowedCategories() as $key) {
            $categories[] = [
                'key' => $key,
                'label' => (string) __('vcookiebar::runtime.banner.categories.'.$key.'.label'),
                'description' => (string) __('vcookiebar::runtime.banner.categories.'.$key.'.description'),
                'locked' => $key === 'necessary',
            ];
        }

        return [
            'endpoint' => route('vcookiebar.consent.store'),
            'csrf' => csrf_token(),
            'preferences' => $preferences,
            'categories' => $categories,
            'privacyPolicyUrl' => filled(config('vcookiebar.privacy_policy_url'))
                ? (string) config('vcookiebar.privacy_policy_url')
                : null,
            'copy' => [
                'title' => (string) __('vcookiebar::runtime.banner.title'),
                'message' => (string) __('vcookiebar::runtime.banner.message'),
                'acceptAll' => (string) __('vcookiebar::runtime.banner.accept_all'),
                'rejectOptional' => (string) __('vcookiebar::runtime.banner.reject_optional'),
                'customize' => (string) __('vcookiebar::runtime.banner.customize'),
                'save' => (string) __('vcookiebar::runtime.banner.save'),
                'privacy' => (string) __('vcookiebar::runtime.banner.privacy'),
                'error' => (string) __('vcookiebar::runtime.banner.error'),
            ],
        ];
    }
}
