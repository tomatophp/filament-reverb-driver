<?php

namespace TomatoPHP\FilamentReverbDriver;

use Illuminate\Support\ServiceProvider;

class FilamentReverbDriverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
            \TomatoPHP\FilamentReverbDriver\Console\FilamentReverbDriverInstall::class,
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-reverb-driver.php', 'filament-reverb-driver');

        //Publish Config
        $this->publishes([
            __DIR__ . '/../config/filament-reverb-driver.php' => config_path('filament-reverb-driver.php'),
        ], 'filament-reverb-driver-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        //Publish Migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'filament-reverb-driver-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-reverb-driver');

        //Publish Views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-reverb-driver'),
        ], 'filament-reverb-driver-views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-reverb-driver');

        //Publish Lang
        $this->publishes([
            __DIR__ . '/../resources/lang' => base_path('lang/vendor/filament-reverb-driver'),
        ], 'filament-reverb-driver-lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

    }

    public function boot(): void
    {
        //you boot methods here
    }
}
