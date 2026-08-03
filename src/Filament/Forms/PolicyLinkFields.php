<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Filament\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

/**
 * Privacy / cookie policy link fields — ResolvableLinkForm when Voodbuilder is present.
 */
final class PolicyLinkFields
{
    /**
     * @return array<int, mixed>
     */
    public static function fields(string $prefix, string $typeLabel): array
    {
        $resolvable = 'Voodflow\\Voodbuilder\\Filament\\Forms\\ResolvableLinkForm';

        if (class_exists($resolvable)) {
            /** @var array<int, mixed> $fields */
            $fields = $resolvable::fields($prefix, [
                'required' => false,
                'type_label' => $typeLabel,
            ]);

            return $fields;
        }

        return [
            Select::make("{$prefix}_link_type")
                ->label($typeLabel)
                ->options([
                    'url' => __('vcookiebar::admin.fields.link_type_url'),
                    'path' => __('vcookiebar::admin.fields.link_type_path'),
                ])
                ->default('url')
                ->live()
                ->native(false),
            TextInput::make("{$prefix}_link")
                ->label(__('vcookiebar::admin.fields.link_target'))
                ->helperText(fn (Get $get): string => $get("{$prefix}_link_type") === 'path'
                    ? (string) __('vcookiebar::admin.fields.link_target_path_help')
                    : (string) __('vcookiebar::admin.fields.link_target_url_help'))
                ->maxLength(2048)
                ->url(fn (Get $get): bool => $get("{$prefix}_link_type") !== 'path'),
        ];
    }
}
