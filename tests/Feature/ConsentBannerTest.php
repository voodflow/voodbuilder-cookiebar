<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\Banner;
use Voodflow\Vcookiebar\Support\ConsentPayload;
use Voodflow\Vcookiebar\Tests\TestCase;

class ConsentBannerTest extends TestCase
{
    public function test_banner_renders_when_no_consent_cookie_present(): void
    {
        $html = view('vcookiebar::components.banner')->render();

        $this->assertStringContainsString('data-vcookiebar', $html);
        $this->assertStringContainsString('vcookiebar:consent', $html);
        $this->assertStringContainsString('X-CSRF-TOKEN', $html);
        $this->assertStringContainsString('"endpoint"', $html);
        $this->assertTrue(Banner::shouldRender());
    }

    public function test_banner_is_hidden_when_consent_cookie_exists(): void
    {
        $payload = ConsentPayload::encode([
            'necessary' => true,
            'preferences' => false,
            'analytics' => true,
            'marketing' => false,
        ]);

        $this->withCookie('vcookiebar_consent', $payload);

        // Re-bind request cookie for the current test request context.
        request()->cookies->set('vcookiebar_consent', $payload);

        $this->assertFalse(Banner::shouldRender());
        $this->assertStringNotContainsString('data-vcookiebar', view('vcookiebar::components.banner')->render());
    }

    public function test_banner_is_hidden_when_package_disabled(): void
    {
        config(['vcookiebar.enabled' => false]);

        $this->assertFalse(Banner::shouldRender());
        $this->assertStringNotContainsString('data-vcookiebar', view('vcookiebar::components.banner')->render());
    }

    public function test_runtime_config_exposes_categories_and_endpoint(): void
    {
        $config = Banner::runtimeConfig();

        $this->assertSame(route('vcookiebar.consent.store'), $config['endpoint']);
        $this->assertNotSame('', $config['csrf']);
        $this->assertTrue($config['preferences']['necessary']);
        $this->assertContains('necessary', array_column($config['categories'], 'key'));
        $this->assertArrayHasKey('acceptAll', $config['copy']);
    }
}
