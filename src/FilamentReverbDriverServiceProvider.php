<?php

namespace TomatoPHP\FilamentReverbDriver;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomatoPHP\FilamentReverbDriver\Console\FilamentReverbDriverInstall;
use TomatoPHP\FilamentReverbDriver\Livewire\Reverb;

class FilamentReverbDriverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register generate command
        $this->commands([
            FilamentReverbDriverInstall::class,
        ]);

        // Register Config file
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-reverb-driver.php', 'filament-reverb-driver');

        // Publish Config
        $this->publishes([
            __DIR__ . '/../config/filament-reverb-driver.php' => config_path('filament-reverb-driver.php'),
        ], 'filament-reverb-driver-config');

        // Register Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish Migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'filament-reverb-driver-migrations');
        // Register views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-reverb-driver');

        // Publish Views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-reverb-driver'),
        ], 'filament-reverb-driver-views');

        // Register Langs
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-reverb-driver');

        // Publish Lang
        $this->publishes([
            __DIR__ . '/../resources/lang' => base_path('lang/vendor/filament-reverb-driver'),
        ], 'filament-reverb-driver-lang');

        // Register Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        Livewire::component('filament-reverb-driver', Reverb::class);
    }

    public function boot(): void
    {
        // A Filament panel only compiles Filament's own CSS, so the package ships its own sheet.
        FilamentAsset::register([
            Css::make('filament-reverb-driver', __DIR__ . '/../resources/dist/filament-reverb-driver.css'),
            Js::make('filament-reverb-driver', __DIR__ . '/../resources/dist/filament-reverb-driver.js'),
        ], 'tomatophp/filament-reverb-driver');

        try {
            // Settings saved from the settings hub win over the env based config, empty settings keep the config value.
            foreach ([
                'active' => 'reverb_active',
                'connection' => 'reverb_connection',
                'database' => 'reverb_database',
                'listen' => 'reverb_listen',
            ] as $config => $setting) {
                $value = setting($setting);

                if (filled($value)) {
                    Config::set("filament-reverb-driver.{$config}", $value);
                }
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }
}
