<?php

namespace TomatoPHP\FilamentReverbDriver\Livewire;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Livewire\Component;
use TomatoPHP\FilamentReverbDriver\Events\AlertBroadcasted;

class Reverb extends Component
{
    /**
     * A live alert arrived on the private channel of the signed in user.
     *
     * @param  array<string, mixed>  $data
     */
    #[On('reverb-notification')]
    public function reverbNotification(array $data): void
    {
        if (blank($data['title'] ?? null)) {
            return;
        }

        $notification = Notification::make((string) ($data['id'] ?? str()->uuid()))
            ->title((string) $data['title'])
            ->body($data['body'] ?? null)
            ->icon($data['icon'] ?? null)
            ->status($this->status($data['type'] ?? 'info'))
            ->actions($this->actionsFrom($data['url'] ?? null));

        $notification->send();

        if (($data['database'] ?? false) && auth()->user()) {
            $notification->sendToDatabase(auth()->user());
        }
    }

    /**
     * Map the alert type onto a Filament notification status.
     */
    protected function status(mixed $type): string
    {
        $type = is_string($type) ? $type : 'info';

        if ($type === 'error') {
            return 'danger';
        }

        return in_array($type, ['success', 'warning', 'danger', 'info'], true) ? $type : 'info';
    }

    /**
     * @return array<int, Action>
     */
    protected function actionsFrom(mixed $url): array
    {
        if (blank($url) || ! is_string($url)) {
            return [];
        }

        return [
            Action::make('view')
                ->label(trans('filament-actions::view.single.label'))
                ->url($url)
                ->markAsRead(),
        ];
    }

    /**
     * The private channel the signed in user listens on, if anybody is signed in.
     */
    public function channel(): ?string
    {
        $user = auth()->user();

        if (! $user || ! config('filament-reverb-driver.active')) {
            return null;
        }

        return AlertBroadcasted::channelName($user::class, $user->getKey());
    }

    public function render()
    {
        return view('filament-reverb-driver::reverb-base', [
            'channel' => $this->channel(),
        ]);
    }
}
