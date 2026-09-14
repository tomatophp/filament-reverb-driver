<?php

use Filament\Facades\Filament;
use TomatoPHP\FilamentReverbDriver\FilamentReverbDriverPlugin;

it('makes the plugin with its id', function () {
    expect(FilamentReverbDriverPlugin::make())
        ->toBeInstanceOf(FilamentReverbDriverPlugin::class)
        ->getId()->toBe('filament-reverb-driver');
});

it('registers the plugin on the panel', function () {
    expect(Filament::getPanel('admin')->getPlugin('filament-reverb-driver'))
        ->toBeInstanceOf(FilamentReverbDriverPlugin::class);
});
