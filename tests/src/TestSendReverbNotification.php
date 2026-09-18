<?php

namespace TomatoPHP\FilamentReverbDriver\Tests;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentAlerts\Facades\FilamentAlerts;
use TomatoPHP\FilamentReverbDriver\Events\AlertBroadcasted;
use TomatoPHP\FilamentReverbDriver\Services\ReverbDriver;
use TomatoPHP\FilamentReverbDriver\Tests\Models\NotificationsTemplate;
use TomatoPHP\FilamentReverbDriver\Tests\Models\User;

use function Pest\Laravel\assertDatabaseHas;

it('can use FilamentAlerts Facade To Notify User Over Websockets', function () {
    Event::fake([AlertBroadcasted::class]);

    $user = User::factory()->create();
    $template = NotificationsTemplate::factory()->create();

    FilamentAlerts::notify($user)
        ->template($template->id)
        ->drivers([ReverbDriver::class])
        ->title(['name' => $user->name])
        ->body(['date' => now()->toDateTimeString()])
        ->send();

    Event::assertDispatched(AlertBroadcasted::class);

    assertDatabaseHas('notifications_logs', [
        'title' => $template->title,
        'description' => $template->body,
        'provider' => 'reverb',
        'type' => 'info',
    ]);
});

it('can send notification using Filament Native Notification', function () {
    Event::fake([AlertBroadcasted::class]);

    $user = User::factory()->create();

    Notification::make()
        ->title('Test title')
        ->body('Test body')
        ->icon('heroicon-o-bell')
        ->info()
        ->sendUse($user, ReverbDriver::class);

    Event::assertDispatched(AlertBroadcasted::class);

    assertDatabaseHas('notifications_logs', [
        'title' => 'Test title',
        'description' => 'Test body',
        'provider' => 'reverb',
        'type' => 'info',
    ]);
});
