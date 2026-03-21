<?php

declare(strict_types=1);

namespace App\Filament\Seller\Pages;

use App\Services\SellerProfileService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class StoreProfile extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::BuildingStorefront;

    protected static ?string $title = 'Profil Toko';

    protected static ?string $navigationLabel = 'Profil Toko';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Filament::auth()->user();
        $profile = $user->sellerProfile;

        $this->form->fill([
            'store_name' => $profile?->store_name ?? '',
            'store_description' => $profile?->store_description ?? '',
            'opening_time' => $profile?->opening_time,
            'closing_time' => $profile?->closing_time,
            'is_open' => $profile?->is_open ?? false,
            'address_text' => $user->address_text ?? '',
            'phone' => $user->phone,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('store_name')
                    ->label('Nama Toko')
                    ->required()
                    ->maxLength(255),
                Textarea::make('store_description')
                    ->label('Deskripsi Toko')
                    ->rows(3),
                TimePicker::make('opening_time')
                    ->label('Jam Buka'),
                TimePicker::make('closing_time')
                    ->label('Jam Tutup'),
                Toggle::make('is_open')
                    ->label('Buka Sekarang'),
                TextInput::make('address_text')
                    ->label('Alamat Toko')
                    ->maxLength(500),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->disabled(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment($this->getFormActionsAlignment())
                            ->key('form-actions'),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Filament::auth()->user();

        app(SellerProfileService::class)->updateProfile($user, $data);

        Notification::make()
            ->title('Profil toko berhasil disimpan')
            ->success()
            ->send();
    }
}
