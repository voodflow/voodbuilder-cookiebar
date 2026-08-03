<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Voodflow\Vookiebar\Tests\TestCase;

class ConsentEndpointSecurityTest extends TestCase
{
    public function test_consent_endpoint_is_guarded_by_web_stack(): void
    {
        $route = Route::getRoutes()->getByName('vookiebar.consent.store');

        $this->assertNotNull($route);

        $middleware = $route->gatherMiddleware();

        // The web group includes CSRF verification in production apps.
        $this->assertContains('web', $middleware);
        $this->assertSame('POST', $route->methods()[0]);
    }

    public function test_consent_rejects_unknown_categories(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->postJson(route('vookiebar.consent.store'), [
            'preferences' => [
                'necessary' => true,
                'tracking_pixel' => true,
            ],
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['preferences']);
    }

    public function test_consent_forces_necessary_and_sets_httponly_cookie(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $response = $this->postJson(route('vookiebar.consent.store'), [
            'preferences' => [
                'necessary' => false,
                'preferences' => true,
                'analytics' => false,
                'marketing' => false,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('preferences.necessary', true)
            ->assertJsonPath('preferences.preferences', true);

        $response->assertCookie('vookiebar_consent');

        $cookie = collect($response->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === 'vookiebar_consent');

        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', strtolower((string) $cookie->getSameSite()));
    }

    public function test_consent_rejects_non_boolean_preference_values(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->postJson(route('vookiebar.consent.store'), [
            'preferences' => [
                'necessary' => true,
                'analytics' => 'yes',
            ],
        ])->assertStatus(422);
    }

    public function test_consent_route_uses_web_and_throttle_middleware(): void
    {
        $route = Route::getRoutes()->getByName('vookiebar.consent.store');

        $this->assertNotNull($route);

        $middleware = $route->gatherMiddleware();

        $this->assertContains('web', $middleware);
        $this->assertTrue(
            collect($middleware)->contains(
                fn (mixed $item): bool => is_string($item) && str_starts_with($item, 'throttle:'),
            ),
        );
    }

    public function test_consent_returns_404_when_package_disabled(): void
    {
        config(['vookiebar.enabled' => false]);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // Route may still be registered from boot; controller must refuse.
        if (! $this->app['router']->has('vookiebar.consent.store')) {
            $this->markTestSkipped('Consent route not registered while disabled at boot.');
        }

        $this->postJson(route('vookiebar.consent.store'), [
            'preferences' => ['necessary' => true],
        ])->assertNotFound();
    }
}
