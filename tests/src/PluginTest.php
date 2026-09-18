<?php

use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;
use TomatoPHP\FilamentAlerts\Facades\FilamentAlerts;
use TomatoPHP\FilamentReverbDriver\Filament\Pages\ReverbSettingsPage;
use TomatoPHP\FilamentReverbDriver\FilamentReverbDriverPlugin;
use TomatoPHP\FilamentReverbDriver\Services\ReverbDriver;

it('registers plugin', function () {
    $panel = Filament::getCurrentOrDefaultPanel();

    $panel->plugins([
        FilamentReverbDriverPlugin::make(),
    ]);

    expect($panel->getPlugin('filament-reverb-driver'))
        ->not()
        ->toThrow(Exception::class);
});

it('registers the settings page on the panel', function () {
    expect(Filament::getCurrentOrDefaultPanel()->getPages())
        ->toContain(ReverbSettingsPage::class);
});

it('registers the reverb driver with filament alerts', function () {
    expect(FilamentAlerts::loadDrivers()->pluck('driver')->toArray())
        ->toContain(ReverbDriver::class);
});

it('registers the package stylesheet and script with FilamentAsset', function () {
    $package = 'tomatophp/filament-reverb-driver';

    $styles = collect(FilamentAsset::getStyles([$package]))->map(fn ($asset): string => $asset->getId());
    $scripts = collect(FilamentAsset::getScripts([$package], withCore: false))->map(fn ($asset): string => $asset->getId());

    expect($styles)->toContain('filament-reverb-driver')
        ->and($scripts)->toContain('filament-reverb-driver');

    expect(file_get_contents(__DIR__ . '/../../resources/dist/filament-reverb-driver.css'))
        ->not->toContain('@layer');
});
