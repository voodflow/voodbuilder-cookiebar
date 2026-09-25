<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Voodflow\Vcookiebar\Filament\Forms\PolicyLinkFields;
use Voodflow\Vcookiebar\Support\Appearance;
use Voodflow\Vcookiebar\Support\ContentLocales;
use Voodflow\Vcookiebar\Support\CopyTranslation;
use Voodflow\Vcookiebar\Support\Lang;
use Voodflow\Vcookiebar\Support\Navigation;
use Voodflow\Vcookiebar\Support\SettingsStore;

/**
 * Independent Vcookiebar settings (not under any page-builder settings screen).
 *
 * Banner texts follow the SitePage / Vtuts pattern: one primary configuration,
 * then derived translations via an explicit “Translate” action.
 *
 * @property-read Schema $form
 */
class VcookiebarSettingsPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 90;

    protected static ?string $slug = 'vcookiebar.settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public string $copyLocale = '';

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
        $settings = SettingsStore::all();
        $this->copyLocale = SettingsStore::copyPrimaryLocale($settings);
        $this->form->fill($this->formStateFromSettings($settings, $this->copyLocale));
    }

    public function save(): void
    {
        $this->persistFormIntoStore();

        Notification::make()
            ->title(Lang::get('admin.notifications.settings_saved', 'Vcookiebar settings saved.'))
            ->success()
            ->send();

        $this->reloadFormForLocale($this->copyLocale);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $hasVoodbuilderTheme = Appearance::hasVoodbuilder();

        return $schema->components([
            Tabs::make('vcookiebar_settings')
                ->persistTabInQueryString('vcookiebarTab')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('general')
                        ->label(Lang::get('admin.tabs.general', 'General'))
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Grid::make(2)->schema([
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
                                    ])
                                    ->columnSpan(1),
                                Section::make(Lang::get('admin.sections.appearance', 'Appearance'))
                                    ->description(Lang::get(
                                        'admin.sections.appearance_help',
                                        'Placement, theme, and colors. “VoodBuilder” inherits the page palette when the builder is installed.',
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
                                                Appearance::THEME_BASE => Lang::get(
                                                    'admin.fields.theme_base',
                                                    'Base (built-in light / dark)',
                                                ),
                                                Appearance::THEME_VOODBUILDER => $hasVoodbuilderTheme
                                                    ? Lang::get(
                                                        'admin.fields.theme_voodbuilder',
                                                        'VoodBuilder (page theme colors)',
                                                    )
                                                    : null,
                                                Appearance::THEME_CUSTOM => Lang::get('admin.fields.theme_custom', 'Custom colors'),
                                            ]))
                                            ->default($hasVoodbuilderTheme ? Appearance::THEME_VOODBUILDER : Appearance::THEME_BASE)
                                            ->live()
                                            ->native(false)
                                            ->helperText(Lang::get(
                                                'admin.fields.theme_help',
                                                'Base uses packaged light/dark presets. VoodBuilder follows the page theme. Custom unlocks the color pickers below.',
                                            )),
                                        Toggle::make('appearance.reopen_icon')
                                            ->label(Lang::get('admin.fields.reopen_icon', 'Show reopen preferences icon'))
                                            ->helperText(Lang::get('admin.fields.reopen_icon_help', 'Subtle floating control after consent so visitors can change preferences.'))
                                            ->default(true),
                                        Grid::make(2)->schema([
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
                                    ])
                                    ->columnSpan(1),
                            ]),
                            Section::make(Lang::get('admin.sections.categories', 'Categories'))
                                ->description(Lang::get(
                                    'admin.sections.categories_help',
                                    'Choose which optional categories appear under Customize. Necessary is always shown. Optional categories stay off until the visitor accepts them (GDPR opt-in).',
                                ))
                                ->schema([
                                    Grid::make(4)->schema(self::categoryVisibilityToggles()),
                                ]),
                        ]),
                    Tab::make('content')
                        ->label(Lang::get('admin.tabs.content', 'Banner texts'))
                        ->icon('heroicon-o-language')
                        ->schema([
                            Grid::make([
                                'default' => 1,
                                'lg' => 12,
                            ])->schema([
                                Select::make('copy_locale')
                                    ->label(Lang::get('admin.fields.copy_language', 'Language'))
                                    ->options(fn (): array => $this->registeredCopyLocaleOptions())
                                    ->live()
                                    ->native(false)
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 4,
                                    ])
                                    ->afterStateUpdated(function (?string $state): void {
                                        if (! is_string($state) || $state === '') {
                                            return;
                                        }

                                        $this->persistFormIntoStore();
                                        $this->reloadFormForLocale($state);
                                    }),
                                Placeholder::make('copy_translation_links')
                                    ->label(Lang::get('admin.fields.translations', 'Translations'))
                                    ->content(fn (): HtmlString | string => $this->translationLinksContent())
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 5,
                                    ]),
                                Actions::make([
                                    Action::make('createCopyTranslation')
                                        ->label(Lang::get('admin.actions.translate', 'Translate'))
                                        ->icon('heroicon-o-language')
                                        ->color('gray')
                                        ->visible(fn (): bool => CopyTranslation::availableTargetLocales($this->copyLocale) !== [])
                                        ->modalHeading(Lang::get('admin.translation.modal_heading', 'Create banner text translation'))
                                        ->modalDescription(Lang::get(
                                            'admin.translation.modal_description',
                                            'Clones the current texts and policy links into another site language. You can edit the new translation afterwards.',
                                        ))
                                        ->schema([
                                            Select::make('locale')
                                                ->label(Lang::get('admin.fields.target_language', 'Target language'))
                                                ->options(fn (): array => CopyTranslation::availableTargetLocales($this->copyLocale))
                                                ->required()
                                                ->native(false),
                                        ])
                                        ->action(function (array $data): void {
                                            $this->persistFormIntoStore();
                                            CopyTranslation::createFrom($this->copyLocale, (string) $data['locale']);

                                            Notification::make()
                                                ->title(Lang::get('admin.notifications.translation_created', 'Translation created'))
                                                ->body(Lang::get('admin.notifications.translation_created_body', 'Banner texts cloned for :locale.', [
                                                    'locale' => ContentLocales::label((string) $data['locale']),
                                                ]))
                                                ->success()
                                                ->send();

                                            $this->reloadFormForLocale((string) $data['locale']);
                                        }),
                                    Action::make('deleteCopyTranslation')
                                        ->label(Lang::get('admin.actions.delete_translation', 'Delete translation'))
                                        ->icon('heroicon-o-trash')
                                        ->color('danger')
                                        ->requiresConfirmation()
                                        ->visible(function (): bool {
                                            $settings = SettingsStore::all();

                                            return $this->copyLocale !== ''
                                                && $this->copyLocale !== SettingsStore::copyPrimaryLocale($settings);
                                        })
                                        ->action(function (): void {
                                            CopyTranslation::delete($this->copyLocale);

                                            Notification::make()
                                                ->title(Lang::get('admin.notifications.translation_deleted', 'Translation deleted'))
                                                ->success()
                                                ->send();

                                            $this->reloadFormForLocale(SettingsStore::copyPrimaryLocale(SettingsStore::all()));
                                        }),
                                ])
                                    ->verticalAlignment(VerticalAlignment::End)
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 3,
                                    ]),
                            ]),
                            Tabs::make('copy_editor')
                                ->contained(false)
                                ->persistTabInQueryString('vcookiebarCopyTab')
                                ->tabs([
                                    Tab::make('texts')
                                        ->label(Lang::get('admin.tabs.copy_texts', 'Texts'))
                                        ->schema([
                                            TextInput::make('copy_fields.title')
                                                ->label(Lang::get('admin.fields.copy_title', 'Title'))
                                                ->maxLength(120)
                                                ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.title', [], $this->copyLocale ?: null)),
                                            Textarea::make('copy_fields.message')
                                                ->label(Lang::get('admin.fields.copy_message', 'Message'))
                                                ->rows(3)
                                                ->maxLength(1000)
                                                ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.message', [], $this->copyLocale ?: null)),
                                            Grid::make(2)->schema([
                                                TextInput::make('copy_fields.accept_all')
                                                    ->label(Lang::get('admin.fields.copy_accept_all', 'Accept all'))
                                                    ->maxLength(80)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.accept_all', [], $this->copyLocale ?: null)),
                                                TextInput::make('copy_fields.reject_optional')
                                                    ->label(Lang::get('admin.fields.copy_reject_optional', 'Reject optional'))
                                                    ->maxLength(80)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.reject_optional', [], $this->copyLocale ?: null)),
                                                TextInput::make('copy_fields.customize')
                                                    ->label(Lang::get('admin.fields.copy_customize', 'Customize'))
                                                    ->maxLength(80)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.customize', [], $this->copyLocale ?: null)),
                                                TextInput::make('copy_fields.save')
                                                    ->label(Lang::get('admin.fields.copy_save', 'Save preferences'))
                                                    ->maxLength(80)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.save', [], $this->copyLocale ?: null)),
                                                TextInput::make('copy_fields.privacy')
                                                    ->label(Lang::get('admin.fields.copy_privacy', 'Privacy link label'))
                                                    ->maxLength(80)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.privacy', [], $this->copyLocale ?: null)),
                                                TextInput::make('copy_fields.cookie_policy')
                                                    ->label(Lang::get('admin.fields.copy_cookie_policy', 'Cookie policy link label'))
                                                    ->maxLength(80)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.cookie_policy', [], $this->copyLocale ?: null)),
                                                TextInput::make('copy_fields.reopen')
                                                    ->label(Lang::get('admin.fields.copy_reopen', 'Reopen button label'))
                                                    ->maxLength(80)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.reopen', [], $this->copyLocale ?: null)),
                                                TextInput::make('copy_fields.error')
                                                    ->label(Lang::get('admin.fields.copy_error', 'Error message'))
                                                    ->maxLength(200)
                                                    ->placeholder(fn (): string => (string) trans('vcookiebar::runtime.banner.error', [], $this->copyLocale ?: null)),
                                            ]),
                                        ]),
                                    Tab::make('privacy')
                                        ->label(Lang::get('admin.fields.privacy_policy', 'Privacy policy'))
                                        ->schema([
                                            ...PolicyLinkFields::fields('privacy', 'copy_fields'),
                                        ]),
                                    Tab::make('cookie_policy')
                                        ->label(Lang::get('admin.fields.cookie_policy', 'Cookie policy'))
                                        ->schema([
                                            ...PolicyLinkFields::fields('cookie_policy', 'copy_fields'),
                                        ]),
                                ]),
                        ]),
                ]),
        ]);
    }

    /**
     * @return array<int, mixed>
     */
    private static function categoryVisibilityToggles(): array
    {
        $categories = [
            'necessary' => [Lang::get('admin.fields.category_necessary', 'Necessary'), true],
            'preferences' => [Lang::get('admin.fields.category_preferences', 'Preferences'), false],
            'analytics' => [Lang::get('admin.fields.category_analytics', 'Analytics'), false],
            'marketing' => [Lang::get('admin.fields.category_marketing', 'Marketing'), false],
        ];

        $toggles = [];

        foreach ($categories as $key => [$label, $locked]) {
            $toggle = Toggle::make("visible.{$key}")
                ->label($label)
                ->inline(false)
                ->default(true);

            if ($locked) {
                $toggle->disabled()->dehydrated()
                    ->helperText(Lang::get('admin.fields.category_necessary_help', 'Always on.'));
            }

            $toggles[] = $toggle;
        }

        foreach (array_keys($categories) as $key) {
            $toggles[] = Toggle::make("defaults.{$key}")
                ->default($key === 'necessary')
                ->hidden()
                ->dehydrated();
        }

        return $toggles;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private function formStateFromSettings(array $settings, string $locale): array
    {
        $settings['copy_locale'] = $locale;
        $settings['copy_fields'] = is_array($settings['copy'][$locale] ?? null)
            ? $settings['copy'][$locale]
            : SettingsStore::emptyCopyFields();

        unset($settings['copy']);

        return $settings;
    }

    private function persistFormIntoStore(): void
    {
        $state = $this->form->getState();
        $locale = (string) ($state['copy_locale'] ?? $this->copyLocale);
        $existing = SettingsStore::all();

        if ($locale === '') {
            $locale = SettingsStore::copyPrimaryLocale($existing);
        }

        $copy = is_array($existing['copy'] ?? null) ? $existing['copy'] : [];
        $copy[$locale] = SettingsStore::normalizeCopyFields(
            is_array($state['copy_fields'] ?? null) ? $state['copy_fields'] : [],
        );

        $registered = SettingsStore::registeredCopyLocales($existing);
        if (! in_array($locale, $registered, true)) {
            $registered[] = $locale;
        }

        unset($state['copy_fields'], $state['copy_locale']);
        $state['copy'] = $copy;
        $state['copy_locales'] = $registered;
        $state['copy_primary_locale'] = SettingsStore::copyPrimaryLocale($existing);

        SettingsStore::save($state);
        $this->copyLocale = $locale;
    }

    private function reloadFormForLocale(string $locale): void
    {
        $this->copyLocale = $locale;
        $this->form->fill($this->formStateFromSettings(SettingsStore::all(), $locale));
    }

    /**
     * @return array<string, string>
     */
    private function registeredCopyLocaleOptions(): array
    {
        $settings = SettingsStore::all();
        $options = [];

        foreach (SettingsStore::registeredCopyLocales($settings) as $code) {
            $label = ContentLocales::label($code);

            if ($code === SettingsStore::copyPrimaryLocale($settings)) {
                $label .= ' (' . Lang::get('admin.fields.primary_locale_badge', 'primary') . ')';
            }

            $options[$code] = $label;
        }

        return $options;
    }

    private function translationLinksContent(): HtmlString | string
    {
        $settings = SettingsStore::all();
        $primary = SettingsStore::copyPrimaryLocale($settings);
        $siblings = array_values(array_filter(
            SettingsStore::registeredCopyLocales($settings),
            fn (string $code): bool => $code !== $this->copyLocale,
        ));

        if ($siblings === []) {
            return Lang::get('admin.translation.none_yet', 'No other translations yet. Use Translate to derive one.');
        }

        $links = collect($siblings)
            ->map(function (string $code) use ($primary): string {
                $label = ContentLocales::label($code);

                if ($code === $primary) {
                    $label .= ' (' . Lang::get('admin.fields.primary_locale_badge', 'primary') . ')';
                }

                return '<button type="button" wire:click="switchCopyLocale(\'' . e($code) . '\')" class="text-primary-600 hover:underline dark:text-primary-400">'
                    . e($label)
                    . '</button>';
            })
            ->implode(' · ');

        return new HtmlString($links);
    }

    public function switchCopyLocale(string $locale): void
    {
        if ($locale === '' || $locale === $this->copyLocale) {
            return;
        }

        $this->persistFormIntoStore();
        $this->reloadFormForLocale($locale);
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

    public function getTitle(): string | Htmlable
    {
        return Lang::get('admin.pages.settings_title', 'Vcookiebar settings');
    }
}
