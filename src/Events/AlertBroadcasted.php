<?php

namespace TomatoPHP\FilamentReverbDriver\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertBroadcasted implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $model,
        public int | string $modelId,
        public array $payload,
    ) {}

    /**
     * Always a private per user channel: an alert addressed to one notifiable
     * must never be readable by every visitor on a public channel.
     *
     * The name matches Laravel's own notification channel, so the
     * `App.Models.User.{id}` authorization callback shipped in
     * `routes/channels.php` already guards it.
     *
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(static::channelName($this->model, $this->modelId)),
        ];
    }

    public function broadcastAs(): string
    {
        return 'filament-alerts.notification';
    }

    /**
     * Reuse the broadcast connection the host application already configured.
     *
     * @return array<int, string|null>
     */
    public function broadcastConnections(): array
    {
        return [config('filament-reverb-driver.connection')];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return $this->payload;
    }

    /**
     * The private channel a notifiable listens on.
     */
    public static function channelName(string $model, int | string $modelId): string
    {
        return str_replace('\\', '.', $model) . '.' . $modelId;
    }
}
