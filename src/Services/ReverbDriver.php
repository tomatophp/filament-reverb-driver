<?php

namespace TomatoPHP\FilamentReverbDriver\Services;

use Filament\Notifications\Notification;
use TomatoPHP\FilamentAlerts\Services\Drivers\Driver;
use TomatoPHP\FilamentReverbDriver\Jobs\NotifyReverbJob;

class ReverbDriver extends Driver
{
    public function setup(): void
    {
        // TODO: Implement setup() method.
    }

    public function sendIt(
        string $title,
        string $model,
        int | string | null $modelId = null,
        ?string $body = null,
        ?string $url = null,
        ?string $icon = null,
        ?string $image = null,
        ?string $type = 'info',
        ?string $action = 'system',
        ?array $data = [],
        ?int $template_id = null,
        ?Notification $notification = null
    ): void {
        dispatch(new NotifyReverbJob([
            'model' => $model,
            'modelId' => $modelId,
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => $icon,
            'image' => $image,
            'type' => $type,
        ]))->onQueue(config('filament-alerts.queue'));
    }
}
