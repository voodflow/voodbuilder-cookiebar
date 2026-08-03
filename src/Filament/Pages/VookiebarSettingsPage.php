<?php

declare(strict_types=1);

namespace Voodflow\Vookiebar\Filament\Pages;

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
use Voodflow\Vookiebar\Support\Navigation;
use Voodflow\Vookiebar\Support\SettingsStore;

/**
 * Independent Vookiebar settings (not under any page-builder settings screen).
 *
 * @property-read Schema $form
 */
class VookiebarSettingsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 90;

    protected static ?string $slug = 'vookiebar.settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return Navigation::group();
    }

    public static function getNavigationLabel(): string
    {
        return (string) __('vookiebar::admin.navigation.settings');
    }

    public function mount(): void
    {
        $this->form->fill(SettingsStore::all());
    }

    public function save(): void
    {
        SettingsStore::save($this->form->getState());

        Notification::make()
            ->title(__('vookiebar::admin.notifications.settings_saved'))
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
            Section::make(__('vookiebar::admin.sections.general'))
                ->schema([
                    Toggle::make('enabled')
                        ->label(__('vookiebar::admin.fields.enabled'))
                        ->helperText(__('vookiebar::admin.fields.enabled_help')),
                    TextInput::make('privacy_policy_url')
                        ->label(__('vookiebar::admin.fields.privacy_policy_url'))
                        ->url()
                        ->maxLength(2048),
                    TextInput::make('consent_cookie')
                        ->label(__('vookiebar::admin.fields.consent_cookie'))
                        ->required()
                        ->alphaDash()
                        ->maxLength(64),
                ]),
            Section::make(__('vookiebar::admin.sections.defaults'))
                ->description(__('vookiebar::admin.sections.defaults_help'))
                ->schema([
                    Toggle::make('defaults.necessary')
                        ->label(__('vookiebar::admin.fields.category_necessary'))
                        ->disabled()
                        ->dehydrated(),
                    Toggle::make('defaults.preferences')
                        ->label(__('vookiebar::admin.fields.category_preferences')),
                    Toggle::make('defaults.analytics')
                        ->label(__('vookiebar::admin.fields.category_analytics')),
                    Toggle::make('defaults.marketing')
                        ->label(__('vookiebar::admin.fields.category_marketing')),
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
            ->id('vookiebar.settings-form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')
                        ->label(__('vookiebar::admin.actions.save'))
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ]),
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('vookiebar::admin.pages.settings_title');
    }
}
