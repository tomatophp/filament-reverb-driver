<?php

return [
    /**
     * ---------------------------------------
     * Allow Reverb To Deliver Notifications
     * ---------------------------------------
     */
    'active' => env('REVERB_DRIVER_ACTIVE', true),

    /**
     * ---------------------------------------
     * The Broadcast Connection The Alerts Are Sent On
     * ---------------------------------------
     * The credentials are never duplicated here, the driver reuses the
     * connection already declared in `config/broadcasting.php`.
     */
    'connection' => env('REVERB_DRIVER_CONNECTION', 'reverb'),

    /**
     * ---------------------------------------
     * Also Store The Alert In The Notifications Table
     * ---------------------------------------
     */
    'database' => env('REVERB_DRIVER_DATABASE', false),

    /**
     * ---------------------------------------
     * Inject The Browser Listener Into The Panel
     * ---------------------------------------
     */
    'listen' => env('REVERB_DRIVER_LISTEN', true),

    /**
     * ---------------------------------------
     * The Laravel Echo Build The Listener Loads
     * ---------------------------------------
     * Only used when the host app does not already expose `window.Echo`.
     */
    'echo-cdn' => env('REVERB_DRIVER_ECHO_CDN', 'https://cdn.jsdelivr.net/npm/laravel-echo@2/dist/echo.iife.js'),
];
