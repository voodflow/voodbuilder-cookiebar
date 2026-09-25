<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\ContentLocales;
use Voodflow\Vcookiebar\Tests\TestCase;

class ContentLocalesTest extends TestCase
{
    public function test_uses_host_app_locales_and_default(): void
    {
        config([
            'app.locales' => [
                'en' => 'English',
                'it' => 'Italiano',
            ],
            'app.default_locale' => 'it',
            'vcookiebar.content_locales' => ['de'],
        ]);

        app()->setLocale('en');

        $this->assertSame(['en', 'it'], ContentLocales::codes());
        $this->assertSame('it', ContentLocales::default());
        $this->assertSame('Italiano', ContentLocales::label('it'));
    }

    public function test_reads_comma_separated_env_list(): void
    {
        config([
            'app.locales' => 'en, fr',
            'app.default_locale' => 'en',
        ]);

        $this->assertSame(['en', 'fr'], ContentLocales::codes());
    }

    public function test_falls_back_to_default_locale_when_list_missing(): void
    {
        config([
            'app.locales' => null,
            'app.default_locale' => 'en',
        ]);

        $this->assertSame(['en'], ContentLocales::codes());
    }
}
