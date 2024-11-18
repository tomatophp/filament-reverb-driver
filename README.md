![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-reverb-driver/master/art/3x1io-tomato-reverb-driver.jpg)

# Filament reverb driver

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-reverb-driver/version.svg)](https://packagist.org/packages/tomatophp/filament-reverb-driver)
[![License](https://poser.pugx.org/tomatophp/filament-reverb-driver/license.svg)](https://packagist.org/packages/tomatophp/filament-reverb-driver)
[![Downloads](https://poser.pugx.org/tomatophp/filament-reverb-driver/d/total.svg)](https://packagist.org/packages/tomatophp/filament-reverb-driver)

Laravel Reverb Realtime Notification for Filament Alerts Sender

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


## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-reverb-driver-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-reverb-driver-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-reverb-driver-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-reverb-driver-migrations"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
