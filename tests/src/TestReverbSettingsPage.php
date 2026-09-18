<?php

namespace TomatoPHP\FilamentReverbDriver\Tests;

use TomatoPHP\FilamentReverbDriver\Filament\Pages\ReverbSettingsPage;
use TomatoPHP\FilamentReverbDriver\Settings\ReverbSettings;
use TomatoPHP\FilamentReverbDriver\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('can render Reverb Settings Page', function () {
    get(ReverbSettingsPage::getUrl())->assertSuccessful();
});

it('saves the settings', function () {
    livewire(ReverbSettingsPage::class)
        ->fillForm([
            'reverb_active' => true,
            'reverb_connection' => 'reverb',
            'reverb_listen' => true,
            'reverb_database' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(ReverbSettings::class);

    expect($settings->reverb_active)->toBeTrue()
        ->and($settings->reverb_connection)->toBe('reverb')
        ->and($settings->reverb_listen)->toBeTrue()
        ->and($settings->reverb_database)->toBeTrue();
});

it('offers the broadcast connections of the application', function () {
    expect(ReverbSettingsPage::connections())->toHaveKey('reverb');
});

it('never renders the broadcaster secret on the settings page', function () {
    config()->set('broadcasting.connections.reverb.secret', 'super-secret-app-secret');

    get(ReverbSettingsPage::getUrl())
        ->assertSuccessful()
        ->assertDontSee('super-secret-app-secret');
});
