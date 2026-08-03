<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Tests\Feature;

use Voodflow\Vookiebar\Support\SettingsStore;
use Voodflow\Vookiebar\Tests\TestCase;
use Voodflow\Vookiebar\Vookiebar;

class ConfigIsolationTest extends TestCase
{
    public function test_vookiebar_config_does_not_bleed_into_unrelated_keys(): void
    {
        config(['app.name' => 'Host App']);
        config(['vookiebar.enabled' => false]);

        $this->assertSame('Host App', config('app.name'));
        $this->assertFalse(config('vookiebar.enabled'));
        $this->assertFalse(Vookiebar::isEnabled());
        $this->assertNull(config('voodbuilder-cookiebar'));
        $this->assertNull(config('voodbuilder.modules.cookiebar'));
    }

    public function test_settings_store_mirrors_only_vookiebar_keys(): void
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
        $this->assertFalse(config('vookiebar.enabled'));
        $this->assertSame('https://example.test/privacy', config('vookiebar.privacy_policy_url'));
        $this->assertSame('custom_consent', config('vookiebar.consent_cookie'));

        $defaults = config('vookiebar.defaults');
        $this->assertTrue($defaults['necessary']);
        $this->assertTrue($defaults['preferences']);
        $this->assertTrue($defaults['analytics']);
        $this->assertFalse($defaults['marketing']);
        $this->assertArrayNotHasKey('evil', $defaults);
    }

    public function test_allowed_categories_ignore_non_string_entries(): void
    {
        config(['vookiebar.categories' => ['necessary', 12, '', 'analytics', null]]);

        $this->assertSame(['necessary', 'analytics'], Vookiebar::allowedCategories());
    }
}
