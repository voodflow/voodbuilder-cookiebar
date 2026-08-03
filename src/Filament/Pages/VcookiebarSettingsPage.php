<?php

declare(strict_types=1);

namespace Voodflow\Vcookiebar\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
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
        return $schema->components([
            Section::make(__('vcookiebar::admin.sections.general'))
                ->schema([
                    Toggle::make('enabled')
                        ->label(__('vcookiebar::admin.fields.enabled'))
                        ->helperText(__('vcookiebar::admin.fields.enabled_help')),
                    TextInput::make('privacy_policy_url')
                        ->label(__('vcookiebar::admin.fields.privacy_policy_url'))
                        ->url()
                        ->maxLength(2048),
                    TextInput::make('consent_cookie')
                        ->label(__('vcookiebar::admin.fields.consent_cookie'))
                        ->required()
                        ->alphaDash()
                        ->maxLength(64),
                ]),
            Section::make(__('vcookiebar::admin.sections.defaults'))
                ->description(__('vcookiebar::admin.sections.defaults_help'))
                ->schema([
                    Toggle::make('defaults.necessary')
                        ->label(__('vcookiebar::admin.fields.category_necessary'))
                        ->disabled()
                        ->dehydrated(),
                    Toggle::make('defaults.preferences')
                        ->label(__('vcookiebar::admin.fields.category_preferences')),
                    Toggle::make('defaults.analytics')
                        ->label(__('vcookiebar::admin.fields.category_analytics')),
                    Toggle::make('defaults.marketing')
                        ->label(__('vcookiebar::admin.fields.category_marketing')),
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
