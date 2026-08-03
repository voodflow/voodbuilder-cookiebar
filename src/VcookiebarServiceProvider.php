<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VcookiebarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'vcookiebar';

    public static string $viewNamespace = 'vcookiebar';

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
