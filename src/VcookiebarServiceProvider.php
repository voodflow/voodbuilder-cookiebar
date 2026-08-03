<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar;

use Illuminate\Support\Facades\View;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Voodflow\Vcookiebar\Support\SettingsStore;

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

    public function packageBooted(): void
    {
        SettingsStore::hydrateRuntimeConfig();
        $this->registerPublicBannerInjection();
    }

    /**
     * Auto-inject the consent banner into known public layouts when enabled.
     * Hosts without those views can include <x-vcookiebar::banner /> manually.
     */
    private function registerPublicBannerInjection(): void
    {
        if (! Vcookiebar::isEnabled()) {
            return;
        }

        if (! (bool) config('vcookiebar.auto_inject', true)) {
            return;
        }

        /** @var list<string>|mixed $views */
        $views = config('vcookiebar.auto_inject_views', []);

        if (! is_array($views) || $views === []) {
            return;
        }

        $views = array_values(array_filter(
            $views,
            static fn (mixed $view): bool => is_string($view) && $view !== '',
        ));

        if ($views === []) {
            return;
        }

        View::composer($views, function (): void {
            static $pushed = false;

            if ($pushed) {
                return;
            }

            $pushed = true;

            View::startPush('overlays');
            echo view('vcookiebar::components.banner')->render();
            View::stopPush();
        });
    }
}
