<?php

use Illuminate\Support\Facades\Artisan;
use TomatoPHP\FilamentReverbDriver\FilamentReverbDriverServiceProvider;

it('boots the service provider', function () {
    expect(app()->getProviders(FilamentReverbDriverServiceProvider::class))->not->toBeEmpty();
});

it('merges the package config', function () {
    expect(config()->has('filament-reverb-driver'))->toBeTrue()
        ->and(config('filament-reverb-driver'))->toBeArray();
});

it('registers the install command', function () {
    expect(Artisan::all())->toHaveKey('filament-reverb-driver:install');
});
