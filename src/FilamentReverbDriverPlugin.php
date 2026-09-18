<?php

namespace TomatoPHP\FilamentReverbDriver;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use TomatoPHP\FilamentAlerts\Facades\FilamentAlerts;
use TomatoPHP\FilamentAlerts\Services\Concerns\NotificationDriver;
use TomatoPHP\FilamentReverbDriver\Filament\Pages\ReverbSettingsPage;
use TomatoPHP\FilamentReverbDriver\Services\ReverbDriver;
use TomatoPHP\FilamentSettingsHub\Facades\FilamentSettingsHub;
use TomatoPHP\FilamentSettingsHub\Services\Contracts\SettingHold;

class FilamentReverbDriverPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-reverb-driver';
    }

    public function register(Panel $panel): void
    {
        if (class_exists(FilamentSettingsHub::class) && $panel->getPlugin('filament-alerts')->useSettingsHub) {
            $panel->pages([
                ReverbSettingsPage::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // The browser side listener, rendered once per panel page.
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => config('filament-reverb-driver.listen')
                ? view('filament-reverb-driver::reverb')->render()
                : '',
        );

        if (class_exists(FilamentSettingsHub::class) && filament('filament-alerts')->useSettingsHub) {
            FilamentSettingsHub::register([
                SettingHold::make()
                    ->label('filament-reverb-driver::messages.settings.reverb.title')
                    ->icon('bxl-javascript')
                    ->page(ReverbSettingsPage::class)
                    ->order(2)
                    ->description('filament-reverb-driver::messages.settings.reverb.description')
                    ->group('filament-alerts::messages.settings.group'),
            ]);
        }

        FilamentAlerts::register([
            NotificationDriver::make('reverb')
                ->label('Reverb')
                ->driver(ReverbDriver::class),
        ]);
    }

    public static function make(): self
    {
        return new FilamentReverbDriverPlugin;
    }
}
