<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Support;

use Voodflow\Vcookiebar\Vcookiebar;

/**
 * Public consent banner + shared runtime helpers (agnostic of any page builder).
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

        return self::currentPreferences() === null;
    }

    /**
     * Runtime shell (script gate + reopen) whenever the package is enabled.
     */
    public static function shouldRenderRuntime(): bool
    {
        return Vcookiebar::isEnabled();
    }

    /**
     * @return array<string, bool>|null
     */
    public static function currentPreferences(): ?array
    {
        $cookieName = (string) config('vcookiebar.consent_cookie', 'vcookiebar_consent');

        return ConsentPayload::decode(request()->cookie($cookieName));
    }

    /**
     * Payload embedded into the banner / runtime for the public scripts.
     *
     * @return array<string, mixed>
     */
    public static function runtimeConfig(): array
    {
        $preferences = Vcookiebar::defaultPreferences();
        $visibleKeys = SettingsStore::visibleCategories();
        $categories = [];

        foreach ($visibleKeys as $key) {
            $categories[] = [
                'key' => $key,
                'label' => (string) __('vcookiebar::runtime.banner.categories.'.$key.'.label'),
                'description' => (string) __('vcookiebar::runtime.banner.categories.'.$key.'.description'),
                'locked' => $key === 'necessary',
            ];
        }

        $appearance = Appearance::normalize(
            is_array(config('vcookiebar.appearance')) ? config('vcookiebar.appearance') : [],
        );

        $settings = SettingsStore::all();
        $privacyUrl = PolicyLink::resolve($settings, 'privacy', 'privacy_policy_url')
            ?? (filled(config('vcookiebar.privacy_policy_url')) ? (string) config('vcookiebar.privacy_policy_url') : null);
        $cookiePolicyUrl = PolicyLink::resolve($settings, 'cookie_policy')
            ?? (filled(config('vcookiebar.cookie_policy_url')) ? (string) config('vcookiebar.cookie_policy_url') : null);

        return [
            'endpoint' => route('vcookiebar.consent.store'),
            'csrf' => csrf_token(),
            'preferences' => $preferences,
            'savedPreferences' => self::currentPreferences(),
            'categories' => $categories,
            'privacyPolicyUrl' => $privacyUrl,
            'cookiePolicyUrl' => $cookiePolicyUrl,
            'privacyPolicyNewTab' => PolicyLink::opensInNewTab($settings, 'privacy'),
            'cookiePolicyNewTab' => PolicyLink::opensInNewTab($settings, 'cookie_policy'),
            'appearance' => $appearance,
            'cssVars' => Appearance::cssVariables($appearance),
            'copy' => [
                'title' => (string) __('vcookiebar::runtime.banner.title'),
                'message' => (string) __('vcookiebar::runtime.banner.message'),
                'acceptAll' => (string) __('vcookiebar::runtime.banner.accept_all'),
                'rejectOptional' => (string) __('vcookiebar::runtime.banner.reject_optional'),
                'customize' => (string) __('vcookiebar::runtime.banner.customize'),
                'save' => (string) __('vcookiebar::runtime.banner.save'),
                'privacy' => (string) __('vcookiebar::runtime.banner.privacy'),
                'cookiePolicy' => (string) __('vcookiebar::runtime.banner.cookie_policy'),
                'error' => (string) __('vcookiebar::runtime.banner.error'),
                'reopen' => (string) __('vcookiebar::runtime.banner.reopen'),
            ],
        ];
    }
}
