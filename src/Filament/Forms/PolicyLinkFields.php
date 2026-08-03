<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Filament\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Voodflow\Vcookiebar\Support\Lang;

/**
 * Policy link fields mirroring the Voodbuilder button link picker:
 * URL | Site page | Menu item + Open in.
 */
final class PolicyLinkFields
{
    /**
     * @return array<int, mixed>
     */
    public static function fields(string $prefix, string $fieldsetLabel): array
    {
        $hasBuilder = class_exists('Voodflow\\Voodbuilder\\Support\\Editor\\EditorLinkTargets');

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
            Fieldset::make($fieldsetLabel)
                ->schema([
                    Select::make("{$prefix}_link_type")
                        ->label(Lang::get('admin.fields.link_type', 'Link type'))
                        ->options($typeOptions)
                        ->default('url')
                        ->live()
                        ->native(false)
                        ->afterStateUpdated(function (Set $set) use ($prefix): void {
                            $set("{$prefix}_link", null);
                        }),
                    TextInput::make("{$prefix}_link")
                        ->key("{$prefix}_link_url")
                        ->label(Lang::get('admin.fields.link_url', 'Link URL'))
                        ->placeholder('https://… or /path')
                        ->helperText(Lang::get('admin.fields.link_target_url_help', 'Absolute URL (https://…) or site path (/pages/example).'))
                        ->maxLength(2048)
                        ->visible(fn (Get $get): bool => in_array($get("{$prefix}_link_type"), ['url', 'path', null, ''], true)),
                    Select::make("{$prefix}_link")
                        ->key("{$prefix}_link_page")
                        ->label(Lang::get('admin.fields.link_page', 'Page'))
                        ->options(fn (): array => self::pageOptions())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->visible(fn (Get $get): bool => $get("{$prefix}_link_type") === 'page'),
                    Select::make("{$prefix}_link")
                        ->key("{$prefix}_link_menu")
                        ->label(Lang::get('admin.fields.link_menu', 'Menu item'))
                        ->options(fn (): array => self::menuOptions())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->visible(fn (Get $get): bool => $get("{$prefix}_link_type") === 'menu'),
                    Select::make("{$prefix}_open_in")
                        ->label(Lang::get('admin.fields.link_open_in', 'Open in'))
                        ->options([
                            '' => Lang::get('admin.fields.link_same_tab', 'Same tab'),
                            '_blank' => Lang::get('admin.fields.link_new_tab', 'New tab'),
                        ])
                        ->default('')
                        ->native(false),
                ])
                ->columns(1),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function pageOptions(): array
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
