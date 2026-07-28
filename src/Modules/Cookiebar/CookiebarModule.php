<?php

declare(strict_types=1);

namespace Voodflow\Voodbuilder\Modules\Cookiebar;

use Illuminate\Routing\Router;
use Voodflow\Voodbuilder\Contracts\RegistersRoutes;
use Voodflow\Voodbuilder\Modules\AbstractVoodBuilderModule;
use Voodflow\Voodbuilder\Modules\ModuleContext;
use Voodflow\Voodbuilder\Modules\ModuleRegistry;

/**
 * Cookie Bar admin, editor APIs, and runtime endpoints.
 *
 * Will host cookie consent bar runtime, preference storage, and admin configuration. Integrates with analytics/tracking modules when enabled.
 */
final class CookiebarModule extends AbstractVoodBuilderModule implements RegistersRoutes
{
    public const ID = 'cookiebar';

    public function id(): string
    {
        return self::ID;
    }

    public function name(): string
    {
        return 'Cookie Bar';
    }

    public function capabilities(): array
    {
        return [
            'cookiebar.runtime',
        ];
    }

    public function registerRoutes(Router $router, ModuleContext $context): void
    {
        // Routes will move here from voodbuilder core during extraction. Example placeholders:
        //
        // Route::middleware(['web', 'auth', 'throttle:60,1'])
        //     ->prefix('voodbuilder/grapesjs')
        //     ->name('voodbuilder.grapesjs.')
        //     ->group(function (): void {
        //         // ...
        //     });
    }

    public function register(ModuleContext $context): void
    {
        $this->registerRoutes($context->app->make(Router::class), $context);
    }

    public static function isEnabled(): bool
    {
        $registry = app(ModuleRegistry::class);

        return $registry->has(self::ID) && $registry->isEnabled(self::ID);
    }
}
