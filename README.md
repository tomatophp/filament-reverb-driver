![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/arts/fadymondy-tomato-reverb-driver.jpg)

# Filament Reverb Driver

[![Dependabot Updates](https://github.com/tomatophp/filament-reverb-driver/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/tomatophp/filament-reverb-driver/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/tomatophp/filament-reverb-driver/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/tomatophp/filament-reverb-driver/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/tomatophp/filament-reverb-driver/actions/workflows/tests.yml/badge.svg)](https://github.com/tomatophp/filament-reverb-driver/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-reverb-driver/version.svg)](https://packagist.org/packages/tomatophp/filament-reverb-driver)
[![License](https://poser.pugx.org/tomatophp/filament-reverb-driver/license.svg)](https://packagist.org/packages/tomatophp/filament-reverb-driver)
[![Downloads](https://poser.pugx.org/tomatophp/filament-reverb-driver/d/total.svg)](https://packagist.org/packages/tomatophp/filament-reverb-driver)

Laravel Reverb Realtime Websocket Notification Driver for [Filament Alerts Sender](https://github.com/tomatophp/filament-alerts)

Alerts are broadcast on the **private** channel of the notifiable, so a signed in user sees them
appear as Filament notifications without reloading the page.

## Screenshots

| Light | Dark |
|-------|------|
| ![Settings](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/arts/settings-light.png) | ![Settings](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/arts/settings-dark.png) |
| ![Settings Hub](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/arts/settings-hub-light.png) | ![Settings Hub](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/arts/settings-hub-dark.png) |
| ![Driver](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/arts/drivers-light.png) | ![Driver](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/arts/drivers-dark.png) |

## Requirements

| Package version | Filament | Laravel     | PHP  |
|-----------------|----------|-------------|------|
| 5.x             | 5.x      | 12.x, 13.x  | 8.2+ |

## Installation

```bash
composer require tomatophp/filament-reverb-driver
```
after install your package please run this command

```bash
php artisan filament-reverb-driver:install
```

finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentReverbDriver\FilamentReverbDriverPlugin::make())
```

## What The Host Application Must Provide

The driver never re-declares any websocket credentials, it publishes on the broadcast connection
your application already has. Install and configure Reverb once:

```bash
php artisan install:broadcasting
composer require laravel/reverb
php artisan reverb:start
```

and make sure these are in place:

1. `config/broadcasting.php` has a `reverb` connection, fed by the usual env values:

```dotenv
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

Only `REVERB_APP_KEY`, host, port and scheme are handed to the browser, which is what the
websocket handshake needs. `REVERB_APP_SECRET` and `REVERB_APP_ID` never leave the server.

2. `routes/channels.php` authorizes the per user channel. This is Laravel's default callback and
   it is exactly the channel this package broadcasts on:

```php
Broadcast::channel('App.Models.User.{id}', fn ($user, $id) => (int) $user->id === (int) $id);
```

3. A queue worker, because alerts are queued like every other Filament Alerts driver:

```bash
php artisan queue:work
```

## Configuration

Open **Settings Hub → Reverb Integration**, or set the env values:

```dotenv
REVERB_DRIVER_ACTIVE=true
REVERB_DRIVER_CONNECTION=reverb
REVERB_DRIVER_DATABASE=false
REVERB_DRIVER_LISTEN=true
```

| Setting key | Config key | Env | Meaning |
|-------------|------------|-----|---------|
| `reverb_active` | `filament-reverb-driver.active` | `REVERB_DRIVER_ACTIVE` | send live alerts at all |
| `reverb_connection` | `filament-reverb-driver.connection` | `REVERB_DRIVER_CONNECTION` | the broadcast connection to publish on |
| `reverb_database` | `filament-reverb-driver.database` | `REVERB_DRIVER_DATABASE` | also store the alert in the notifications table |
| `reverb_listen` | `filament-reverb-driver.listen` | `REVERB_DRIVER_LISTEN` | inject the browser listener into the panel |
| — | `filament-reverb-driver.echo-cdn` | `REVERB_DRIVER_ECHO_CDN` | the Laravel Echo build loaded when the app does not bundle Echo |

The settings hub wins over the env values. Nothing is broadcast when the driver is off or when the
alert has no notifiable to address.

If your application already builds Laravel Echo into its own bundle and exposes `window.Echo`,
the listener reuses that instance and never touches the CDN.

## Usage

to set up any model to get notifications you

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TomatoPHP\FilamentReverbDriver\Traits\InteractsWithReverb;

class User extends Authenticatable
{
    use Notifiable;
    use InteractsWithReverb;
    ...
```

### Use Filament Native Notification

```php
use Filament\Notifications\Notification;

Notification::make('send')
    ->title('Test Notifications')
    ->body('This is a test notification')
    ->sendUse(auth()->user(), \TomatoPHP\FilamentReverbDriver\Services\ReverbDriver::class);
```

### Send Notification

```php
use TomatoPHP\FilamentAlerts\Facades\FilamentAlerts;

FilamentAlerts::notify(User::first())
    ->template($template->id)
    ->title([
        "find-text" => "change with this"
    ])
    ->body([
        "find-text" => "change with this"
    ])
    ->drivers([\TomatoPHP\FilamentReverbDriver\Services\ReverbDriver::class])
    ->send();
```

### Notification Channels

it can be working with direct user methods like

```php
$user->notifyReverb(string $title, ?string $body = null, ?string $url = null, ?string $icon = null, ?string $type = 'info');
$user->reverbChannelName(); // the private channel this record listens on
```

## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-reverb-driver-config"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-reverb-driver-lang"
```

## Testing

if you like to run `PEST` testing just use this command

```bash
composer test
```

## Code Style

if you like to fix the code style just use this command

```bash
composer format
```

## PHPStan

if you like to check the code by `PHPStan` just use this command

```bash
composer analyse
```

## Other Filament Packages

Checkout our [Awesome TomatoPHP](https://github.com/tomatophp/awesome)
