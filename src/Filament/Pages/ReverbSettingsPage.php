<?php

namespace TomatoPHP\FilamentReverbDriver\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use TomatoPHP\FilamentReverbDriver\Settings\ReverbSettings;
use TomatoPHP\FilamentSettingsHub\Pages\SettingsHub;

class ReverbSettingsPage extends SettingsPage
{
    protected static BackedEnum | null | string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = ReverbSettings::class;

    public function getTitle(): string
    {
        return trans('filament-reverb-driver::messages.settings.reverb.title');
    }

    protected function getActions(): array
    {
        return [
            Action::make('back')->url(SettingsHub::getUrl()),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function form(Schema $form): Schema
    {
        return $form->columns(1)
            ->schema([
                Section::make()->schema([
                    Toggle::make('reverb_active')
                        ->live()
                        ->label(trans('filament-reverb-driver::messages.settings.reverb.active'))
                        ->hint(config('filament-alerts.show_hint') ? 'setting("reverb_active")' : null),
                ]),
                Section::make(trans('filament-reverb-driver::messages.settings.reverb.websocket'))
                    ->description(trans('filament-reverb-driver::messages.settings.reverb.websocket_help'))
                    ->visible(fn (Get $get): bool => (bool) $get('reverb_active'))
                    ->schema([
                        // Only the name of the connection: the credentials stay in config/broadcasting.php.
                        Select::make('reverb_connection')
                            ->options(fn (): array => static::connections())
                            ->native(false)
                            ->label(trans('filament-reverb-driver::messages.settings.reverb.connection'))
                            ->helperText(trans('filament-reverb-driver::messages.settings.reverb.connection_help'))
                            ->hint(config('filament-alerts.show_hint') ? 'setting("reverb_connection")' : null),
                        Toggle::make('reverb_listen')
                            ->label(trans('filament-reverb-driver::messages.settings.reverb.listen'))
                            ->helperText(trans('filament-reverb-driver::messages.settings.reverb.listen_help'))
                            ->hint(config('filament-alerts.show_hint') ? 'setting("reverb_listen")' : null),
                        Toggle::make('reverb_database')
                            ->label(trans('filament-reverb-driver::messages.settings.reverb.database'))
                            ->helperText(trans('filament-reverb-driver::messages.settings.reverb.database_help'))
                            ->hint(config('filament-alerts.show_hint') ? 'setting("reverb_database")' : null),
                    ]),
            ]);
    }

    /**
     * The broadcast connections declared by the host application.
     *
     * @return array<string, string>
     */
    public static function connections(): array
    {
        return collect(array_keys((array) config('broadcasting.connections', [])))
            ->mapWithKeys(fn (string $name): array => [$name => $name])
            ->all();
    }

    public function afterSave(): void
    {
        Artisan::call('cache:clear');
    }
}
