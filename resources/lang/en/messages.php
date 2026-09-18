<?php

return [
    'offline' => 'Live notifications are offline',
    'settings' => [
        'reverb' => [
            'title' => 'Reverb Integration',
            'description' => 'Configure your realtime websocket notifications.',
            'active' => 'Reverb Active',
            'websocket' => 'Websocket',
            'websocket_help' => 'The credentials stay in config/broadcasting.php, only the connection is picked here.',
            'connection' => 'Broadcast Connection',
            'connection_help' => 'The broadcast connection the alerts are published on, usually reverb.',
            'listen' => 'Listen In The Panel',
            'listen_help' => 'Show incoming alerts as Filament notifications while the user is signed in.',
            'database' => 'Also Store In The Database',
            'database_help' => 'Keep the alert in the notifications table so it survives a page reload.',
        ],
    ],
];
