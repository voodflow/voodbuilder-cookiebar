<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Voodflow\Vcookiebar\Tests\TestCase;
use Voodflow\Vcookiebar\Vcookiebar;

/**
 * Auto-injection stands down while a visual editor owns the viewport.
 *
 * An editor pulls host page markup into its authoring canvas, so an injected banner would
 * arrive as editable content covering the author's footer. The page builder used to delete
 * those nodes after the fact; the banner now simply declines to render.
 */
class EditorContextOptOutTest extends TestCase
{
    public function test_banner_is_injected_on_a_normal_page_response(): void
    {
        $this->assertStringContainsString('data-vcookiebar-shell', $this->renderHostLayout());
    }

    public function test_banner_is_withheld_while_the_editor_owns_the_viewport(): void
    {
        $this->markEditorContext();

        $this->assertStringNotContainsString('data-vcookiebar-shell', $this->renderHostLayout());
    }

    public function test_stand_down_is_driven_by_the_request_attribute_only(): void
    {
        $this->assertFalse(Vcookiebar::shouldStandDownForEditor());

        $this->markEditorContext();

        $this->assertTrue(Vcookiebar::shouldStandDownForEditor());
    }

    public function test_the_edit_query_flag_alone_does_not_silence_the_banner(): void
    {
        // Any visitor can append ?edit=1. Honouring it would drop the consent banner for
        // the public, which is a compliance failure and not a rendering detail.
        $this->app->instance('request', Request::create('http://localhost/about?edit=1'));

        $this->assertFalse(Vcookiebar::shouldStandDownForEditor());
        $this->assertStringContainsString('data-vcookiebar-shell', $this->renderHostLayout());
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // The composer binds at boot, so the target view has to be known before it.
        $app['config']->set('vcookiebar.auto_inject', true);
        $app['config']->set('vcookiebar.auto_inject_views', ['host-layout']);
        $app['config']->set('view.paths', [
            __DIR__.'/../fixtures/views',
            ...(array) $app['config']->get('view.paths', []),
        ]);
    }

    private function markEditorContext(): void
    {
        $request = Request::create('http://localhost/about?edit=1');
        $request->attributes->set('voodbuilder.editor_active', true);

        $this->app->instance('request', $request);
    }

    private function renderHostLayout(): string
    {
        return View::make('host-layout')->render();
    }
}
