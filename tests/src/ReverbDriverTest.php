<?php

namespace TomatoPHP\FilamentReverbDriver\Tests;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Event;
use TomatoPHP\FilamentReverbDriver\Events\AlertBroadcasted;
use TomatoPHP\FilamentReverbDriver\Services\ReverbDriver;
use TomatoPHP\FilamentReverbDriver\Tests\Models\User;

use function Pest\Laravel\assertDatabaseHas;

it('broadcasts the alert payload for the notifiable', function () {
    Event::fake([AlertBroadcasted::class]);

    $user = User::factory()->create();

    app(ReverbDriver::class)->sendIt(
        title: 'Test title',
        model: User::class,
        modelId: $user->id,
        body: 'Test body',
        url: 'https://tomatophp.com',
        icon: 'heroicon-o-bell',
        type: 'success',
    );

    Event::assertDispatched(AlertBroadcasted::class, function (AlertBroadcasted $event) use ($user): bool {
        $payload = $event->broadcastWith();

        return $event->model === User::class
            && $event->modelId === $user->id
            && $payload['title'] === 'Test title'
            && $payload['body'] === 'Test body'
            && $payload['url'] === 'https://tomatophp.com'
            && $payload['icon'] === 'heroicon-o-bell'
            && $payload['type'] === 'success';
    });
});

it('broadcasts on a private per user channel and never on a public one', function () {
    Event::fake([AlertBroadcasted::class]);

    $user = User::factory()->create();

    app(ReverbDriver::class)->sendIt(
        title: 'Test title',
        model: User::class,
        modelId: $user->id,
    );

    Event::assertDispatched(AlertBroadcasted::class, function (AlertBroadcasted $event) use ($user): bool {
        $channels = $event->broadcastOn();

        expect($channels)->toHaveCount(1);

        foreach ($channels as $channel) {
            expect($channel)->toBeInstanceOf(PrivateChannel::class)
                ->and($channel::class)->not->toBe(Channel::class)
                ->and($channel)->not->toBeInstanceOf(PresenceChannel::class)
                // Laravel prefixes the name of a private channel, a public one would not be prefixed.
                ->and((string) $channel->name)->toBe('private-' . str_replace('\\', '.', User::class) . '.' . $user->id);
        }

        return true;
    });
});

it('names the broadcast event so Echo can listen for it', function () {
    $event = new AlertBroadcasted(User::class, 1, []);

    expect($event->broadcastAs())->toBe('filament-alerts.notification')
        ->and($event->broadcastConnections())->toBe(['reverb']);
});

it('broadcasts on the connection chosen in the settings', function () {
    config()->set('filament-reverb-driver.connection', 'pusher');

    expect((new AlertBroadcasted(User::class, 1, []))->broadcastConnections())->toBe(['pusher']);
});

it('logs the notification after broadcasting', function () {
    Event::fake([AlertBroadcasted::class]);

    $user = User::factory()->create();

    app(ReverbDriver::class)->sendIt(
        title: 'Logged title',
        model: User::class,
        modelId: $user->id,
        body: 'Logged body',
    );

    assertDatabaseHas('notifications_logs', [
        'title' => 'Logged title',
        'description' => 'Logged body',
        'provider' => 'reverb',
        'type' => 'info',
    ]);
});

it('skips broadcasting when the driver is turned off', function () {
    Event::fake([AlertBroadcasted::class]);

    config()->set('filament-reverb-driver.active', false);

    $user = User::factory()->create();

    app(ReverbDriver::class)->sendIt(
        title: 'Test title',
        model: User::class,
        modelId: $user->id,
    );

    Event::assertNotDispatched(AlertBroadcasted::class);
});

it('skips broadcasting when there is no notifiable to address', function () {
    Event::fake([AlertBroadcasted::class]);

    app(ReverbDriver::class)->sendIt(
        title: 'Test title',
        model: User::class,
        modelId: null,
    );

    Event::assertNotDispatched(AlertBroadcasted::class);
});

it('can notify the model directly through the trait', function () {
    Event::fake([AlertBroadcasted::class]);

    $user = User::factory()->create();

    $user->notifyReverb('Direct title', 'Direct body');

    expect($user->reverbChannelName())->toBe(str_replace('\\', '.', User::class) . '.' . $user->id);

    Event::assertDispatched(AlertBroadcasted::class, fn (AlertBroadcasted $event): bool => $event->broadcastWith()['title'] === 'Direct title');
});
