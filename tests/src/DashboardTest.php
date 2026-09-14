<?php

use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use TomatoPHP\FilamentReverbDriver\Tests\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());

    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('renders the panel dashboard with the plugin registered', function () {
    livewire(Dashboard::class)->assertSuccessful();
});
