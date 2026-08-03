<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Voodflow\Vcookiebar\Filament\Forms\PolicyLinkFields;
use Voodflow\Vcookiebar\Support\Appearance;
use Voodflow\Vcookiebar\Support\Navigation;
use Voodflow\Vcookiebar\Support\SettingsStore;

/**
 * Independent Vcookiebar settings (not under any page-builder settings screen).
 *
 * @property-read Schema $form
 */
class VcookiebarSettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 90;

    protected static ?string $slug = 'vcookiebar.settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return Navigation::group();
    }

    public static function getNavigationLabel(): string
    {
        return (string) __('vcookiebar::admin.navigation.settings');
    }

    public function mount(): void
    {
        $this->form->fill(SettingsStore::all());
    }

    public function save(): void
    {
        SettingsStore::save($this->form->getState());

        Notification::make()
            ->title(__('vcookiebar::admin.notifications.settings_saved'))
            ->success()
            ->send();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $hasVoodflowTheme = class_exists(\Voodflow\Voodbuilder\Support\ThemePalette::class)
            || class_exists(\Voodflow\Voodbuilder\Voodbuilder::class);

        return $schema->components([
            Section::make(__('vcookiebar::admin.sections.general'))
                ->schema([
                    Toggle::make('enabled')
                        ->label(__('vcookiebar::admin.fields.enabled'))
                        ->helperText(__('vcookiebar::admin.fields.enabled_help')),
                    TextInput::make('consent_cookie')
                        ->label(__('vcookiebar::admin.fields.consent_cookie'))
                        ->required()
                        ->alphaDash()
                        ->maxLength(64),
                ]),
            Section::make(__('vcookiebar::admin.sections.policies'))
                ->description(__('vcookiebar::admin.sections.policies_help'))
                ->schema([
                    ...PolicyLinkFields::fields('privacy', (string) __('vcookiebar::admin.fields.privacy_policy')),
                    ...PolicyLinkFields::fields('cookie_policy', (string) __('vcookiebar::admin.fields.cookie_policy')),
                ]),
            Section::make(__('vcookiebar::admin.sections.visible'))
                ->description(__('vcookiebar::admin.sections.visible_help'))
                ->schema([
                    Toggle::make('visible.necessary')
                        ->label(__('vcookiebar::admin.fields.category_necessary'))
                        ->disabled()
                        ->dehydrated()
                        ->default(true),
                    Toggle::make('visible.preferences')
                        ->label(__('vcookiebar::admin.fields.category_preferences')),
                    Toggle::make('visible.analytics')
                        ->label(__('vcookiebar::admin.fields.category_analytics')),
                    Toggle::make('visible.marketing')
                        ->label(__('vcookiebar::admin.fields.category_marketing')),
                ]),
            Section::make(__('vcookiebar::admin.sections.defaults'))
                ->description(__('vcookiebar::admin.sections.defaults_help'))
                ->schema([
                    Toggle::make('defaults.necessary')
                        ->label(__('vcookiebar::admin.fields.category_necessary'))
                        ->disabled()
                        ->dehydrated(),
                    Toggle::make('defaults.preferences')
                        ->label(__('vcookiebar::admin.fields.category_preferences'))
                        ->visible(fn (Get $get): bool => (bool) $get('visible.preferences')),
                    Toggle::make('defaults.analytics')
                        ->label(__('vcookiebar::admin.fields.category_analytics'))
                        ->visible(fn (Get $get): bool => (bool) $get('visible.analytics')),
                    Toggle::make('defaults.marketing')
                        ->label(__('vcookiebar::admin.fields.category_marketing'))
                        ->visible(fn (Get $get): bool => (bool) $get('visible.marketing')),
                ]),
            Section::make(__('vcookiebar::admin.sections.appearance'))
                ->description(__('vcookiebar::admin.sections.appearance_help'))
                ->schema([
                    Select::make('appearance.placement')
                        ->label(__('vcookiebar::admin.fields.placement'))
                        ->options([
                            'bottom' => __('vcookiebar::admin.fields.placement_bottom'),
                            'bottom-right' => __('vcookiebar::admin.fields.placement_bottom_right'),
                            'bottom-left' => __('vcookiebar::admin.fields.placement_bottom_left'),
                            'top' => __('vcookiebar::admin.fields.placement_top'),
                        ])
                        ->default('bottom')
                        ->native(false),
                    Select::make('appearance.theme')
                        ->label(__('vcookiebar::admin.fields.theme'))
                        ->options(array_filter([
                            Appearance::THEME_VOODFLOW => $hasVoodflowTheme
                                ? __('vcookiebar::admin.fields.theme_voodflow')
                                : null,
                            Appearance::THEME_AUTO => __('vcookiebar::admin.fields.theme_auto'),
                            Appearance::THEME_CUSTOM => __('vcookiebar::admin.fields.theme_custom'),
                        ]))
                        ->default($hasVoodflowTheme ? Appearance::THEME_VOODFLOW : Appearance::THEME_AUTO)
                        ->live()
                        ->native(false)
                        ->helperText(__('vcookiebar::admin.fields.theme_help')),
                    Toggle::make('appearance.reopen_icon')
                        ->label(__('vcookiebar::admin.fields.reopen_icon'))
                        ->helperText(__('vcookiebar::admin.fields.reopen_icon_help'))
                        ->default(true),
                    ColorPicker::make('appearance.colors.panel_bg')
                        ->label(__('vcookiebar::admin.fields.color_panel_bg'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.text')
                        ->label(__('vcookiebar::admin.fields.color_text'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.muted')
                        ->label(__('vcookiebar::admin.fields.color_muted'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_primary_bg')
                        ->label(__('vcookiebar::admin.fields.color_button_primary_bg'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_primary_text')
                        ->label(__('vcookiebar::admin.fields.color_button_primary_text'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_bg')
                        ->label(__('vcookiebar::admin.fields.color_button_bg'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_text')
                        ->label(__('vcookiebar::admin.fields.color_button_text'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
        ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('vcookiebar.settings-form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')
                        ->label(__('vcookiebar::admin.actions.save'))
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ]),
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('vcookiebar::admin.pages.settings_title');
    }
}
