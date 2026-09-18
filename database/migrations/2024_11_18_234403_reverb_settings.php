<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('reverb.reverb_active', true);
        $this->migrator->add('reverb.reverb_connection', 'reverb');
        $this->migrator->add('reverb.reverb_database', false);
        $this->migrator->add('reverb.reverb_listen', true);
    }
};
