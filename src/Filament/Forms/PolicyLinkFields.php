<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Filament\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Voodflow\Vcookiebar\Support\Lang;

/**
 * Policy link fields mirroring the Voodbuilder button link picker:
 * URL | Site page | Menu item + Open in.
 *
 * Site pages are listed once per translation group so a single pick follows the
 * visitor locale on the public banner.
 */
final class PolicyLinkFields
{
    /**
     * Flat fields (no Fieldset) for use inside tabs / grids.
     *
     * @return array<int, mixed>
     */
    public static function fields(string $prefix, string $statePath = ''): array
    {
        $hasBuilder = class_exists('Voodflow\\Voodbuilder\\Support\\Editor\\EditorLinkTargets');
        $base = $statePath !== '' ? rtrim($statePath, '.').'.' : '';

        $typeOptions = $hasBuilder
            ? [
                'url' => Lang::get('admin.fields.link_type_url', 'URL'),
                'page' => Lang::get('admin.fields.link_type_page', 'Site page'),
                'menu' => Lang::get('admin.fields.link_type_menu', 'Menu item'),
            ]
            : [
                'url' => Lang::get('admin.fields.link_type_url', 'URL'),
                'path' => Lang::get('admin.fields.link_type_path', 'Site path'),
            ];

        return [
            Grid::make(3)->schema([
                Select::make("{$base}{$prefix}_link_type")
                    ->label(Lang::get('admin.fields.link_type', 'Link type'))
                    ->options($typeOptions)
                    ->default('url')
                    ->live()
                    ->native(false)
                    ->afterStateUpdated(function (Set $set) use ($prefix, $base): void {
                        $set("{$base}{$prefix}_link", null);
                    }),
                TextInput::make("{$base}{$prefix}_link")
                    ->key("{$base}{$prefix}_link_url")
                    ->label(Lang::get('admin.fields.link_url', 'Link URL'))
                    ->placeholder('https://… or /path')
                    ->helperText(Lang::get('admin.fields.link_target_url_help', 'Absolute URL (https://…) or site path (/pages/example).'))
                    ->maxLength(2048)
                    ->columnSpan(1)
                    ->visible(fn (Get $get): bool => in_array($get("{$base}{$prefix}_link_type"), ['url', 'path', null, ''], true)),
                Select::make("{$base}{$prefix}_link")
                    ->key("{$base}{$prefix}_link_page")
                    ->label(Lang::get('admin.fields.link_page', 'Page'))
                    ->options(fn (): array => self::pageOptions())
                    ->helperText(Lang::get(
                        'admin.fields.link_page_help',
                        'One entry per page family. The public banner opens the translation for the visitor’s language when available.',
                    ))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(1)
                    ->visible(fn (Get $get): bool => $get("{$base}{$prefix}_link_type") === 'page'),
                Select::make("{$base}{$prefix}_link")
                    ->key("{$base}{$prefix}_link_menu")
                    ->label(Lang::get('admin.fields.link_menu', 'Menu item'))
                    ->options(fn (): array => self::menuOptions())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpan(1)
                    ->visible(fn (Get $get): bool => $get("{$base}{$prefix}_link_type") === 'menu'),
                Select::make("{$base}{$prefix}_open_in")
                    ->label(Lang::get('admin.fields.link_open_in', 'Open in'))
                    ->options([
                        '' => Lang::get('admin.fields.link_same_tab', 'Same tab'),
                        '_blank' => Lang::get('admin.fields.link_new_tab', 'New tab'),
                    ])
                    ->default('')
                    ->native(false),
            ]),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function pageOptions(): array
    {
        $pageClass = 'Voodflow\\Voodbuilder\\Models\\SitePage';

        if (! class_exists($pageClass)) {
            return self::legacyPageOptionsFromCatalog();
        }

        $options = ['' => '—'];
        $seenGroups = [];

        $pages = $pageClass::query()
            ->orderByDesc('is_home')
            ->orderBy('locale')
            ->orderBy('title')
            ->get();

        foreach ($pages as $page) {
            $groupId = trim((string) ($page->translation_group_id ?? ''));
            $key = $groupId !== '' ? $groupId : (string) $page->slug;

            if ($key === '' || isset($seenGroups[$key])) {
                continue;
            }

            $seenGroups[$key] = true;

            $label = (string) $page->title;
            $locales = method_exists($page, 'translationLocaleCodes')
                ? $page->translationLocaleCodes()
                : [strtoupper((string) ($page->locale ?? ''))];

            if ($locales !== []) {
                $label .= ' ('.implode(', ', $locales).')';
            }

            if ((bool) ($page->is_home ?? false)) {
                $label .= ' ('.__('Home').')';
            } elseif (! (bool) ($page->published ?? true)) {
                $label .= ' ('.__('Draft').')';
            }

            $options[$key] = $label;
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private static function legacyPageOptionsFromCatalog(): array
    {
        $catalog = 'Voodflow\\Voodbuilder\\Support\\Editor\\EditorLinkTargets';
        if (! class_exists($catalog)) {
            return [];
        }

        $options = ['' => '—'];
        foreach ($catalog::pages() as $page) {
            $options[(string) $page['id']] = (string) $page['label'];
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    private static function menuOptions(): array
    {
        $catalog = 'Voodflow\\Voodbuilder\\Support\\Editor\\EditorLinkTargets';
        if (! class_exists($catalog)) {
            return [];
        }

        $options = ['' => '—'];
        foreach ($catalog::menuItems() as $item) {
            $options[(string) $item['id']] = (string) $item['label'];
        }

        return $options;
    }
}
