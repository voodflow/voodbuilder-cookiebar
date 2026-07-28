<?php

declare(strict_types=1);

namespace Voodflow\VoodbuilderCookiebar;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VoodbuilderCookiebarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'voodbuilder-cookiebar';

    public static string $viewNamespace = 'voodbuilder-cookiebar';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$viewNamespace);
    }

    public function packageRegistered(): void
    {
        // Package install alone does not activate the module. The Filament plugin does.
        // Testbench / headless hosts may opt into auto-registration.
        $this->app->booting(function (): void {
            if (! (bool) config('voodbuilder-cookiebar.auto_register_module', false)) {
                return;
            }

            VoodbuilderCookiebar::activate();
        });
    }

    public function packageBooted(): void
    {
        // Views: merge under both namespaces (Finder merges hints).
        // Translations: MUST use voodbuilder-cookiebar — Laravel FileLoader::addNamespace overwrites.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voodbuilder');
    }
}
