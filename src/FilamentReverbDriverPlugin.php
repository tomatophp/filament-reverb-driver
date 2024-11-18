<?php

namespace TomatoPHP\FilamentReverbDriver;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentReverbDriverPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-reverb-driver';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): self
    {
        return new FilamentReverbDriverPlugin;
    }
}
