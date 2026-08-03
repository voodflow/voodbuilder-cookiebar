<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\Navigation;
use Voodflow\Vcookiebar\Tests\TestCase;
use Voodflow\Vcookiebar\Vcookiebar;
use Voodflow\Vcookiebar\VcookiebarPlugin;

class ProviderBootTest extends TestCase
{
    public function test_service_provider_merges_config(): void
    {
        $this->assertTrue(config('vcookiebar.enabled'));
        $this->assertSame('vcookiebar', config('vcookiebar.route_prefix'));
        $this->assertContains('necessary', config('vcookiebar.categories'));
    }

    public function test_plugin_id_is_stable_and_independent(): void
    {
        $plugin = VcookiebarPlugin::make();

        $this->assertSame('vcookiebar', $plugin->getId());
        $this->assertSame('Vcookiebar', Navigation::group());
    }

    public function test_activation_is_opt_in_via_plugin(): void
    {
        $this->assertFalse(Vcookiebar::isActivated());

        Vcookiebar::activate();

        $this->assertTrue(Vcookiebar::isActivated());
    }

    public function test_consent_route_is_registered_when_enabled(): void
    {
        $this->assertTrue(
            $this->app['router']->has('vcookiebar.consent.store'),
        );
    }

    public function test_package_boots_without_page_builder_classes(): void
    {
        $this->assertFalse(class_exists(
            'Voodflow\\Voodbuilder\\Voodbuilder',
            autoload: false,
        ));
        $this->assertTrue(Vcookiebar::isEnabled());
    }
}
