<?php

namespace TomatoPHP\FilamentReverbDriver\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TomatoPHP\FilamentAlerts\Models\NotificationsLogs;
use TomatoPHP\FilamentReverbDriver\Events\AlertBroadcasted;

class NotifyReverbJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?string $model;

    public int | string | null $modelId;

    public ?string $title;

    public ?string $body;

    public ?string $url;

    public ?string $icon;

    public ?string $image;

    public ?string $type;

    /**
     * @param  array{model?: ?string, modelId?: int|string|null, title?: ?string, body?: ?string, url?: ?string, icon?: ?string, image?: ?string, type?: ?string}  $arg
     */
    public function __construct(array $arg)
    {
        $this->model = $arg['model'] ?? null;
        $this->modelId = $arg['modelId'] ?? null;
        $this->title = $arg['title'] ?? null;
        $this->body = $arg['body'] ?? null;
        $this->url = $arg['url'] ?? null;
        $this->icon = $arg['icon'] ?? null;
        $this->image = $arg['image'] ?? null;
        $this->type = $arg['type'] ?? 'info';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Turned off, or there is no notifiable to address the private channel to.
        if (! config('filament-reverb-driver.active') || blank($this->model) || blank($this->modelId)) {
            return;
        }

        AlertBroadcasted::dispatch($this->model, $this->modelId, [
            'id' => (string) str()->uuid(),
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'icon' => $this->icon,
            'image' => $this->image,
            'type' => $this->type,
            'database' => (bool) config('filament-reverb-driver.database'),
        ]);

        $log = new NotificationsLogs;
        $log->model_type = $this->model;
        $log->model_id = $this->modelId;
        $log->title = $this->title;
        $log->description = $this->body;
        $log->provider = 'reverb';
        $log->type = $this->type;
        $log->save();
    }
}
