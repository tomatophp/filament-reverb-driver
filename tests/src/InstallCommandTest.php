<?php

use function Pest\Laravel\artisan;

it('runs the install command', function () {
    artisan('filament-reverb-driver:install')->assertSuccessful();
});
