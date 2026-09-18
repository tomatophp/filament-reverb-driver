<?php

namespace TomatoPHP\FilamentReverbDriver\Settings;

use Spatie\LaravelSettings\Settings;

class ReverbSettings extends Settings
{
    public ?bool $reverb_active = true;

    public ?string $reverb_connection = 'reverb';

    public ?bool $reverb_database = false;

    public ?bool $reverb_listen = true;

    public static function group(): string
    {
        return 'reverb';
    }
}
