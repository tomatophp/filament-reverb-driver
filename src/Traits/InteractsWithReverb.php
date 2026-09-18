<?php

namespace TomatoPHP\FilamentReverbDriver\Traits;

use TomatoPHP\FilamentReverbDriver\Events\AlertBroadcasted;
use TomatoPHP\FilamentReverbDriver\Jobs\NotifyReverbJob;

trait InteractsWithReverb
{
    /**
     * Push a live alert to this record's private websocket channel.
     */
    public function notifyReverb(
        string $title,
        ?string $body = null,
        ?string $url = null,
        ?string $icon = null,
        ?string $type = 'info',
    ): void {
        dispatch(new NotifyReverbJob([
            'model' => static::class,
            'modelId' => $this->getKey(),
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => $icon,
            'type' => $type,
        ]));
    }

    /**
     * The private channel this record receives its alerts on.
     */
    public function reverbChannelName(): string
    {
        return AlertBroadcasted::channelName(static::class, $this->getKey());
    }
}
