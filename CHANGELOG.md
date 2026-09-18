# v5.0.0

- first working release: a `reverb` driver for `tomatophp/filament-alerts` ^5.0
- alerts are broadcast on the private per user channel only, never on a public one
- browser listener registered through FilamentAsset, shows incoming alerts as Filament notifications
- reuses the broadcast connection of the host application, no credentials are duplicated
- Reverb settings page registered on the settings hub
- support Filament v5 and Laravel 12 / 13
- Pest 4 / Testbench 10–11 test suite and GitHub Actions matrix (PHP 8.3–8.4, Laravel 12–13)

# V1.0.0

First release of the package
