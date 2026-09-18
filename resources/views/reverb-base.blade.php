@php
    $connection = config('filament-reverb-driver.connection');
    $options = config("broadcasting.connections.{$connection}.options", []);
@endphp
<div
    id="filament-reverb-driver"
    @if (filled($channel))
        data-reverb="{{ json_encode([
            // Only the public handshake values: the app secret and id never leave the server.
            'key' => config("broadcasting.connections.{$connection}.key"),
            'host' => $options['host'] ?? request()->getHost(),
            'port' => (int) ($options['port'] ?? 8080),
            'scheme' => $options['scheme'] ?? 'http',
            'channel' => $channel,
            'echoCdn' => config('filament-reverb-driver.echo-cdn'),
        ]) }}"
    @endif
>
    <div class="tomato-reverb-offline" data-reverb-offline hidden>
        {{ trans('filament-reverb-driver::messages.offline') }}
    </div>
</div>
