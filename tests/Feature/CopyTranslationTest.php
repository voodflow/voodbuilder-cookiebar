<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Voodflow\Vcookiebar\Support\ContentLocales;
use Voodflow\Vcookiebar\Support\CopyTranslation;
use Voodflow\Vcookiebar\Support\SettingsStore;
use Voodflow\Vcookiebar\Tests\TestCase;

class CopyTranslationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SettingsStore::forget();
        config([
            'app.default_locale' => 'en',
            'app.locales' => [
                'en' => 'English',
                'it' => 'Italiano',
                'de' => 'Deutsch',
            ],
        ]);
    }

    public function test_defaults_register_only_primary_locale(): void
    {
        $settings = SettingsStore::all();

        $this->assertSame(['en'], SettingsStore::registeredCopyLocales($settings));
        $this->assertArrayHasKey('en', $settings['copy']);
        $this->assertArrayNotHasKey('it', $settings['copy']);
    }

    public function test_translate_clones_primary_into_target_locale(): void
    {
        SettingsStore::upsertCopyLocale('en', [
            'title' => 'Cookie preferences',
            'message' => 'Hello',
        ], register: true);

        CopyTranslation::createFrom('en', 'it');

        $settings = SettingsStore::all();

        $this->assertSame(['en', 'it'], SettingsStore::registeredCopyLocales($settings));
        $this->assertSame('Cookie preferences', $settings['copy']['it']['title']);
        $this->assertSame('Hello', $settings['copy']['it']['message']);

        $available = CopyTranslation::availableTargetLocales('en');
        $this->assertArrayHasKey('de', $available);
        $this->assertArrayNotHasKey('it', $available);
        $this->assertArrayNotHasKey('en', $available);
    }

    public function test_cannot_delete_primary_locale(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        CopyTranslation::delete(ContentLocales::default());
    }

    public function test_delete_removes_derived_translation(): void
    {
        CopyTranslation::createFrom('en', 'it');
        CopyTranslation::delete('it');

        $settings = SettingsStore::all();

        $this->assertSame(['en'], SettingsStore::registeredCopyLocales($settings));
        $this->assertArrayNotHasKey('it', $settings['copy']);
    }
}
