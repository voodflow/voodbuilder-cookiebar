<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\Appearance;
use Voodflow\Vcookiebar\Support\Banner;
use Voodflow\Vcookiebar\Support\PolicyLink;
use Voodflow\Vcookiebar\Support\SettingsStore;
use Voodflow\Vcookiebar\Tests\TestCase;

class AppearanceAndPolicyLinkTest extends TestCase
{
    public function test_appearance_normalize_rejects_unknown_placement(): void
    {
        $normalized = Appearance::normalize([
            'placement' => 'center',
            'theme' => 'custom',
            'colors' => ['panel_bg' => '#abc', 'button_primary_bg' => 'not-a-color'],
        ]);

        $this->assertSame('bottom', $normalized['placement']);
        $this->assertSame('custom', $normalized['theme']);
        $this->assertSame('#aabbcc', $normalized['colors']['panel_bg']);
        $this->assertNull($normalized['colors']['button_primary_bg']);
    }

    public function test_appearance_theme_aliases_legacy_ids(): void
    {
        $this->assertSame(Appearance::THEME_BASE, Appearance::normalizeTheme('auto'));
        $this->assertSame(Appearance::THEME_VOODBUILDER, Appearance::normalizeTheme('voodflow'));
        $this->assertSame(Appearance::THEME_BASE, Appearance::normalizeTheme('nope'));
        $this->assertSame(
            Appearance::THEME_BASE,
            Appearance::normalize(['theme' => 'auto'])['theme'],
        );
    }

    public function test_policy_link_resolves_path_and_legacy_url(): void
    {
        $path = PolicyLink::resolve([
            'privacy_link_type' => 'path',
            'privacy_link' => 'privacy',
        ], 'privacy');

        $this->assertSame(url('/privacy'), $path);

        $legacy = PolicyLink::resolve([
            'privacy_policy_url' => 'https://example.test/privacy',
        ], 'privacy', 'privacy_policy_url');

        $this->assertSame('https://example.test/privacy', $legacy);
    }

    public function test_settings_store_persists_visible_and_cookie_policy(): void
    {
        SettingsStore::forget();

        SettingsStore::save([
            'enabled' => true,
            'consent_cookie' => 'vcookiebar_consent',
            'defaults' => [
                'necessary' => true,
                'preferences' => true,
                'analytics' => true,
                'marketing' => true,
            ],
            'visible' => [
                'necessary' => true,
                'preferences' => true,
                'analytics' => false,
                'marketing' => true,
            ],
            'appearance' => [
                'placement' => 'bottom-right',
                'theme' => 'custom',
                'reopen_icon' => true,
                'colors' => [
                    'button_primary_bg' => '#0ea5e9',
                ],
            ],
            'copy' => [
                'en' => [
                    'title' => 'Custom EN title',
                    'message' => '',
                    'privacy_link_type' => 'url',
                    'privacy_link' => 'https://example.test/privacy',
                    'cookie_policy_link_type' => 'path',
                    'cookie_policy_link' => '/cookies',
                ],
            ],
            'copy_locales' => ['en'],
            'copy_primary_locale' => 'en',
        ]);

        $this->assertSame('https://example.test/privacy', config('vcookiebar.privacy_policy_url'));
        $this->assertSame(url('/cookies'), config('vcookiebar.cookie_policy_url'));
        $this->assertFalse(config('vcookiebar.visible.analytics'));
        $this->assertTrue(config('vcookiebar.visible.marketing'));
        $this->assertSame('bottom-right', config('vcookiebar.appearance.placement'));
        $this->assertSame('#0ea5e9', config('vcookiebar.appearance.colors.button_primary_bg'));

        // GDPR: optional defaults cannot stay pre-ticked from admin input.
        $this->assertFalse(config('vcookiebar.defaults.analytics'));
        $this->assertFalse(config('vcookiebar.defaults.marketing'));
        $this->assertTrue(config('vcookiebar.defaults.necessary'));

        app()->setLocale('en');
        $copy = SettingsStore::resolveCopy(SettingsStore::all());
        $this->assertSame('Custom EN title', $copy['title']);
        $this->assertNotSame('', $copy['message']);

        SettingsStore::forget();
    }

    public function test_runtime_config_exposes_cleanup_cookies(): void
    {
        $config = Banner::runtimeConfig();

        $this->assertArrayHasKey('cleanupCookies', $config);
        $this->assertArrayHasKey('analytics', $config['cleanupCookies']);
        $this->assertContains('_ga', $config['cleanupCookies']['analytics']);
    }
}
