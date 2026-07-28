<?php

declare(strict_types=1);

namespace Voodflow\VoodbuilderCookiebar\Support;

/**
 * GrapesJS editor labels owned by the Cookie Bar Filament plugin.
 */
final class VoodbuilderCookiebarEditorLabels
{
    /**
     * @return array<string, mixed>
     */
    public static function grapesJsLabels(): array
    {
        return [
            'cookiebarTitle' => __('voodbuilder-cookiebar::cookiebar.editor.title'),
            'cookiebarHint' => __('voodbuilder-cookiebar::cookiebar.editor.hint'),
            'cookiebarEmpty' => __('voodbuilder-cookiebar::cookiebar.editor.empty'),
            'cookiebarPlaceholder' => __('voodbuilder-cookiebar::cookiebar.editor.placeholder'),
        ];
    }
}
