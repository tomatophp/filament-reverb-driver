<?php

namespace TomatoPHP\FilamentReverbDriver\Tests\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TomatoPHP\FilamentAlerts\Traits\InteractsWithNotifications;
use TomatoPHP\FilamentReverbDriver\Tests\Database\Factories\UserFactory;
use TomatoPHP\FilamentReverbDriver\Traits\InteractsWithReverb;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory;
    use InteractsWithNotifications;
    use InteractsWithReverb;
    use Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
