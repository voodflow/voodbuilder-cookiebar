<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Voodflow\Vcookiebar\Filament\Forms\PolicyLinkFields;
use Voodflow\Vcookiebar\Support\Appearance;
use Voodflow\Vcookiebar\Support\Lang;
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
        return Lang::get('admin.navigation.settings', 'Settings');
    }

    public function mount(): void
    {
        $this->form->fill(SettingsStore::all());
    }

    public function save(): void
    {
        SettingsStore::save($this->form->getState());

        Notification::make()
            ->title(Lang::get('admin.notifications.settings_saved', 'Vcookiebar settings saved.'))
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
            Section::make(Lang::get('admin.sections.general', 'General'))
                ->schema([
                    Toggle::make('enabled')
                        ->label(Lang::get('admin.fields.enabled', 'Enable cookie bar'))
                        ->helperText(Lang::get('admin.fields.enabled_help', 'Disables the public consent banner, endpoint, and runtime when off.')),
                    TextInput::make('consent_cookie')
                        ->label(Lang::get('admin.fields.consent_cookie', 'Consent cookie name'))
                        ->required()
                        ->alphaDash()
                        ->maxLength(64),
                ]),
            Section::make(Lang::get('admin.sections.policies', 'Policy links'))
                ->description(Lang::get(
                    'admin.sections.policies_help',
                    'Optional. Same link types as Voodbuilder buttons: URL, site page, or menu item.',
                ))
                ->schema([
                    ...PolicyLinkFields::fields('privacy', Lang::get('admin.fields.privacy_policy', 'Privacy policy')),
                    ...PolicyLinkFields::fields('cookie_policy', Lang::get('admin.fields.cookie_policy', 'Cookie policy')),
                ]),
            Section::make(Lang::get('admin.sections.categories', 'Categories'))
                ->description(Lang::get(
                    'admin.sections.categories_help',
                    'Show = appear in Customize. Default = pre-checked before the visitor chooses. Necessary stays on.',
                ))
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Placeholder::make('cat_matrix_label')
                                ->hiddenLabel()
                                ->content(''),
                            Placeholder::make('cat_matrix_show')
                                ->hiddenLabel()
                                ->content(new HtmlString(
                                    '<span class="text-sm font-semibold text-gray-700 dark:text-gray-200">'
                                    .e(Lang::get('admin.fields.matrix_show', 'Show'))
                                    .'</span>'
                                )),
                            Placeholder::make('cat_matrix_default')
                                ->hiddenLabel()
                                ->content(new HtmlString(
                                    '<span class="text-sm font-semibold text-gray-700 dark:text-gray-200">'
                                    .e(Lang::get('admin.fields.matrix_default', 'Default on'))
                                    .'</span>'
                                )),
                            ...self::categoryMatrixRows(),
                        ]),
                ]),
            Section::make(Lang::get('admin.sections.appearance', 'Appearance'))
                ->description(Lang::get(
                    'admin.sections.appearance_help',
                    'Placement, theme, and colors. With Voodbuilder, “Voodflow theme” follows the site light/dark palette automatically.',
                ))
                ->schema([
                    Select::make('appearance.placement')
                        ->label(Lang::get('admin.fields.placement', 'Banner placement'))
                        ->options([
                            'bottom' => Lang::get('admin.fields.placement_bottom', 'Bottom (full width)'),
                            'bottom-right' => Lang::get('admin.fields.placement_bottom_right', 'Bottom right (floating)'),
                            'bottom-left' => Lang::get('admin.fields.placement_bottom_left', 'Bottom left (floating)'),
                            'top' => Lang::get('admin.fields.placement_top', 'Top (full width)'),
                        ])
                        ->default('bottom')
                        ->native(false),
                    Select::make('appearance.theme')
                        ->label(Lang::get('admin.fields.theme', 'Color theme'))
                        ->options(array_filter([
                            Appearance::THEME_VOODFLOW => $hasVoodflowTheme
                                ? Lang::get('admin.fields.theme_voodflow', 'Voodflow theme (auto light/dark)')
                                : null,
                            Appearance::THEME_AUTO => Lang::get('admin.fields.theme_auto', 'System auto (prefers-color-scheme)'),
                            Appearance::THEME_CUSTOM => Lang::get('admin.fields.theme_custom', 'Custom colors'),
                        ]))
                        ->default($hasVoodflowTheme ? Appearance::THEME_VOODFLOW : Appearance::THEME_AUTO)
                        ->live()
                        ->native(false)
                        ->helperText(Lang::get('admin.fields.theme_help', 'Custom colors apply only when “Custom colors” is selected.')),
                    Toggle::make('appearance.reopen_icon')
                        ->label(Lang::get('admin.fields.reopen_icon', 'Show reopen preferences icon'))
                        ->helperText(Lang::get('admin.fields.reopen_icon_help', 'Subtle floating control after consent so visitors can change preferences.'))
                        ->default(true),
                    ColorPicker::make('appearance.colors.panel_bg')
                        ->label(Lang::get('admin.fields.color_panel_bg', 'Panel background'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.text')
                        ->label(Lang::get('admin.fields.color_text', 'Text'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.muted')
                        ->label(Lang::get('admin.fields.color_muted', 'Muted text'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_primary_bg')
                        ->label(Lang::get('admin.fields.color_button_primary_bg', 'Primary button background'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_primary_text')
                        ->label(Lang::get('admin.fields.color_button_primary_text', 'Primary button text'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_bg')
                        ->label(Lang::get('admin.fields.color_button_bg', 'Secondary button background'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                    ColorPicker::make('appearance.colors.button_text')
                        ->label(Lang::get('admin.fields.color_button_text', 'Secondary button text'))
                        ->visible(fn (Get $get): bool => $get('appearance.theme') === Appearance::THEME_CUSTOM),
                ]),
        ]);
    }

    /**
     * @return array<int, mixed>
     */
    private static function categoryMatrixRows(): array
    {
        $categories = [
            'necessary' => [Lang::get('admin.fields.category_necessary', 'Necessary'), true],
            'preferences' => [Lang::get('admin.fields.category_preferences', 'Preferences'), false],
            'analytics' => [Lang::get('admin.fields.category_analytics', 'Analytics'), false],
            'marketing' => [Lang::get('admin.fields.category_marketing', 'Marketing'), false],
        ];

        $rows = [];
        foreach ($categories as $key => [$label, $locked]) {
            $rows[] = Placeholder::make("cat_label_{$key}")
                ->hiddenLabel()
                ->content(new HtmlString(
                    '<span class="text-sm font-medium text-gray-950 dark:text-white">'.e($label).'</span>'
                ));

            $visible = Toggle::make("visible.{$key}")
                ->hiddenLabel()
                ->inline(false)
                ->default(true)
                ->live();

            $default = Toggle::make("defaults.{$key}")
                ->hiddenLabel()
                ->inline(false)
                ->default($key === 'necessary');

            if ($locked) {
                $visible->disabled()->dehydrated();
                $default->disabled()->dehydrated();
            } else {
                $default->disabled(fn (Get $get): bool => ! (bool) $get("visible.{$key}"));
            }

            $rows[] = $visible;
            $rows[] = $default;
        }

        return $rows;
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
                        ->label(Lang::get('admin.actions.save', 'Save settings'))
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ]),
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return Lang::get('admin.pages.settings_title', 'Vcookiebar settings');
    }
}
