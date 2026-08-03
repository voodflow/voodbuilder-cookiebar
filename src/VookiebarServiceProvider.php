<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VookiebarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'vookiebar';

    public static string $viewNamespace = 'vookiebar';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews(static::$viewNamespace)
            ->hasRoutes('web');
    }
}
