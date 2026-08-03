<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Tests\Feature;

use Voodflow\Vookiebar\Support\Navigation;
use Voodflow\Vookiebar\Tests\TestCase;
use Voodflow\Vookiebar\Vookiebar;
use Voodflow\Vookiebar\VookiebarPlugin;

class ProviderBootTest extends TestCase
{
    public function test_service_provider_merges_config(): void
    {
        $this->assertTrue(config('vookiebar.enabled'));
        $this->assertSame('vookiebar', config('vookiebar.route_prefix'));
        $this->assertContains('necessary', config('vookiebar.categories'));
    }

    public function test_plugin_id_is_stable_and_independent(): void
    {
        $plugin = VookiebarPlugin::make();

        $this->assertSame('vookiebar', $plugin->getId());
        $this->assertSame('Vookiebar', Navigation::group());
    }

    public function test_activation_is_opt_in_via_plugin(): void
    {
        $this->assertFalse(Vookiebar::isActivated());

        Vookiebar::activate();

        $this->assertTrue(Vookiebar::isActivated());
    }

    public function test_consent_route_is_registered_when_enabled(): void
    {
        $this->assertTrue(
            $this->app['router']->has('vookiebar.consent.store'),
        );
    }

    public function test_package_boots_without_page_builder_classes(): void
    {
        $this->assertFalse(class_exists(
            'Voodflow\\Voodbuilder\\Voodbuilder',
            autoload: false,
        ));
        $this->assertTrue(Vookiebar::isEnabled());
    }
}
