<?php

namespace TomatoPHP\FilamentReverbDriver\Tests;

use Illuminate\Support\Facades\DB;
use TomatoPHP\FilamentReverbDriver\FilamentReverbDriverServiceProvider;

function saveReverbSetting(string $name, mixed $value): void
{
    DB::table('settings')->updateOrInsert(
        ['group' => 'reverb', 'name' => $name],
        ['payload' => json_encode($value), 'locked' => false],
    );
}

function bootReverbProvider(): void
{
    (new FilamentReverbDriverServiceProvider(app()))->boot();
}

it('loads the settings saved from the settings hub', function () {
    saveReverbSetting('reverb_active', true);
    saveReverbSetting('reverb_connection', 'pusher');
    saveReverbSetting('reverb_database', true);
    saveReverbSetting('reverb_listen', false);

    bootReverbProvider();

    expect(config('filament-reverb-driver.active'))->toBeTrue()
        ->and(config('filament-reverb-driver.connection'))->toBe('pusher')
        ->and(config('filament-reverb-driver.database'))->toBeTrue()
        ->and(config('filament-reverb-driver.listen'))->toBeFalse();
});

it('keeps the env connection when the setting is empty', function () {
    config()->set('filament-reverb-driver.connection', 'from-env');
    saveReverbSetting('reverb_connection', '');

    bootReverbProvider();

    expect(config('filament-reverb-driver.connection'))->toBe('from-env');
});

it('turns the driver off when the hub toggle is off', function () {
    saveReverbSetting('reverb_active', false);

    bootReverbProvider();

    expect(config('filament-reverb-driver.active'))->toBeFalse();
});
