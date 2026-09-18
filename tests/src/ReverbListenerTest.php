<?php

namespace TomatoPHP\FilamentReverbDriver\Tests;

use Filament\Notifications\Notification;
use TomatoPHP\FilamentReverbDriver\Livewire\Reverb;
use TomatoPHP\FilamentReverbDriver\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Livewire\livewire;

it('renders the listener with the private channel of the signed in user', function () {
    $user = User::factory()->create();

    actingAs($user);

    livewire(Reverb::class)
        ->assertSuccessful()
        ->assertSee(str_replace('\\', '.', User::class) . '.' . $user->id, escape: false)
        ->assertSee('test-reverb-key', escape: false);
});

it('never exposes the broadcaster secret to the browser', function () {
    config()->set('broadcasting.connections.reverb.secret', 'super-secret-app-secret');
    config()->set('broadcasting.connections.reverb.app_id', '999999');

    actingAs(User::factory()->create());

    livewire(Reverb::class)
        ->assertDontSee('super-secret-app-secret', escape: false)
        ->assertDontSee('999999', escape: false);
});

it('renders no channel for a guest', function () {
    livewire(Reverb::class)
        ->assertSuccessful()
        ->assertDontSee('data-reverb=', escape: false);
});

it('renders no channel when the driver is turned off', function () {
    config()->set('filament-reverb-driver.active', false);

    actingAs(User::factory()->create());

    livewire(Reverb::class)->assertDontSee('data-reverb=', escape: false);
});

it('turns an incoming broadcast into a filament notification', function () {
    actingAs(User::factory()->create());

    livewire(Reverb::class)
        ->call('reverbNotification', [
            'id' => 'alert-1',
            'title' => 'Live title',
            'body' => 'Live body',
            'type' => 'success',
            'url' => 'https://tomatophp.com',
        ]);

    Notification::assertNotified('Live title');
});

it('stores the incoming broadcast in the database when asked to', function () {
    actingAs(User::factory()->create());

    livewire(Reverb::class)
        ->call('reverbNotification', [
            'id' => 'alert-2',
            'title' => 'Stored title',
            'body' => 'Stored body',
            'database' => true,
        ]);

    assertDatabaseCount('notifications', 1);
});

it('ignores an incoming broadcast without a title', function () {
    actingAs(User::factory()->create());

    livewire(Reverb::class)->call('reverbNotification', ['id' => 'alert-3']);

    assertDatabaseCount('notifications', 0);
});
