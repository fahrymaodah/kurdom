<?php

declare(strict_types=1);

namespace App\Filament\Courier\Pages;

use App\Models\CourierProfile;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ToggleAvailability extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::Signal;

    protected static ?string $title = 'Status Online';

    protected static ?string $navigationLabel = 'Status Online';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.courier.pages.toggle-availability';

    public bool $isOnline = false;

    public function mount(): void
    {
        $profile = Filament::auth()->user()->courierProfile;
        $this->isOnline = $profile?->is_online ?? false;
    }

    public function toggle(): void
    {
        $user = Filament::auth()->user();

        $profile = CourierProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['is_online' => ! $this->isOnline]
        );

        $this->isOnline = $profile->is_online;

        Notification::make()
            ->title($this->isOnline ? 'Anda sekarang ONLINE' : 'Anda sekarang OFFLINE')
            ->color($this->isOnline ? 'success' : 'danger')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggle')
                ->label(fn () => $this->isOnline ? 'Go Offline' : 'Go Online')
                ->color(fn () => $this->isOnline ? 'danger' : 'success')
                ->icon(fn (): Heroicon => $this->isOnline ? Heroicon::XCircle : Heroicon::CheckCircle)
                ->action('toggle')
                ->size('lg'),
        ];
    }
}
