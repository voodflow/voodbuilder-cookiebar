<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\SettingsStore;
use Voodflow\Vcookiebar\Tests\TestCase;
use Voodflow\Vcookiebar\Vcookiebar;

class ConfigIsolationTest extends TestCase
{
    public function test_vcookiebar_config_does_not_bleed_into_unrelated_keys(): void
    {
        config(['app.name' => 'Host App']);
        config(['vcookiebar.enabled' => false]);

        $this->assertSame('Host App', config('app.name'));
        $this->assertFalse(config('vcookiebar.enabled'));
        $this->assertFalse(Vcookiebar::isEnabled());
        $this->assertNull(config('voodbuilder-cookiebar'));
        $this->assertNull(config('voodbuilder.modules.cookiebar'));
    }

    public function test_settings_store_mirrors_only_vcookiebar_keys(): void
    {
        config(['app.debug' => true]);

        SettingsStore::save([
            'enabled' => false,
            'privacy_policy_url' => 'https://example.test/privacy',
            'consent_cookie' => 'custom_consent',
            'defaults' => [
                'necessary' => false,
                'preferences' => true,
                'analytics' => true,
                'marketing' => false,
                'evil' => true,
            ],
        ]);

        $this->assertTrue(config('app.debug'));
        $this->assertFalse(config('vcookiebar.enabled'));
        $this->assertSame('https://example.test/privacy', config('vcookiebar.privacy_policy_url'));
        $this->assertSame('custom_consent', config('vcookiebar.consent_cookie'));

        $defaults = config('vcookiebar.defaults');
        $this->assertTrue($defaults['necessary']);
        $this->assertTrue($defaults['preferences']);
        $this->assertTrue($defaults['analytics']);
        $this->assertFalse($defaults['marketing']);
        $this->assertArrayNotHasKey('evil', $defaults);
    }

    public function test_allowed_categories_ignore_non_string_entries(): void
    {
        config(['vcookiebar.categories' => ['necessary', 12, '', 'analytics', null]]);

        $this->assertSame(['necessary', 'analytics'], Vcookiebar::allowedCategories());
    }
}
