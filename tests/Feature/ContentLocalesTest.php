<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\ContentLocales;
use Voodflow\Vcookiebar\Tests\TestCase;

class ContentLocalesTest extends TestCase
{
    public function test_explicit_config_overrides_discovery(): void
    {
        config(['vcookiebar.content_locales' => ['it', 'de']]);

        $this->assertSame(['it', 'de'], ContentLocales::codes());
    }

    public function test_discovers_app_locales_without_plugins(): void
    {
        config([
            'vcookiebar.content_locales' => null,
            'vcookiebar.site_locales' => null,
            'app.locales' => [
                'en' => 'English',
                'it' => 'Italiano',
            ],
            'app.locale' => 'en',
            'cosmolab.locales' => null,
        ]);

        $this->assertSame(['en', 'it'], ContentLocales::codes());
        $this->assertSame('en', ContentLocales::default());
    }

    public function test_falls_back_to_app_locales_env_list(): void
    {
        config([
            'vcookiebar.content_locales' => null,
            'vcookiebar.site_locales' => 'en,fr',
            'app.locales' => null,
            'cosmolab.locales' => null,
            'app.locale' => 'en',
        ]);

        $this->assertSame(['en', 'fr'], ContentLocales::codes());
    }

    public function test_options_include_human_labels(): void
    {
        config([
            'vcookiebar.content_locales' => ['en', 'it'],
            'app.locales' => [
                'en' => 'English',
                'it' => 'Italiano',
            ],
        ]);

        $options = ContentLocales::options();

        $this->assertSame('English', $options['en']);
        $this->assertSame('Italiano', $options['it']);
    }
}
