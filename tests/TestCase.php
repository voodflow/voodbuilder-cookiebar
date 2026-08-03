<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Voodflow\Vookiebar\Vookiebar;
use Voodflow\Vookiebar\VookiebarServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Vookiebar::reset();
    }

    protected function getPackageProviders($app): array
    {
        return [
            VookiebarServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
    }
}
