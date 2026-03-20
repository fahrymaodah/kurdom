<?php

declare(strict_types=1);

namespace App\Filament\Seller\Pages;

use App\Enums\OrderSource;
use App\Services\DeliveryFeeService;
use App\Services\OrderService;
use App\Services\PhoneLookupService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CreateOrder extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::PlusCircle;

    protected static ?string $title = 'Buat Pesanan';

    protected static ?string $navigationLabel = 'Buat Pesanan';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public ?float $estimatedFee = null;

    public ?float $estimatedDistance = null;

    public function mount(): void
    {
        $user = Filament::auth()->user();

        $this->form->fill([
            'pickup_latitude' => $user->latitude,
            'pickup_longitude' => $user->longitude,
            'pickup_address_text' => $user->address_text,
            'order_source' => OrderSource::WaFb->value,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Select::make('order_source')
                    ->label('Sumber Pesanan')
                    ->options(collect(OrderSource::cases())->mapWithKeys(fn (OrderSource $s): array => [$s->value => $s->label()]))
                    ->required(),
                TextInput::make('buyer_phone')
                    ->label('No. Telepon Pembeli')
                    ->tel()
                    ->required()
                    ->suffixAction(
                        Action::make('lookupBuyer')
                            ->icon(Heroicon::MagnifyingGlass)
                            ->action(function ($state, $set) {
                                $user = app(PhoneLookupService::class)->findByPhone($state);
                                if ($user) {
                                    $set('buyer_name', $user->name);
                                    $set('delivery_address_text', $user->address_text ?? '');
                                    $set('delivery_latitude', $user->latitude ?? '');
                                    $set('delivery_longitude', $user->longitude ?? '');
                                    Notification::make()->title('Pembeli ditemukan: ' . $user->name)->success()->send();
                                } else {
                                    Notification::make()->title('Pembeli tidak ditemukan')->warning()->send();
                                }
                            })
                    ),
                TextInput::make('buyer_name')
                    ->label('Nama Pembeli')
                    ->required()
                    ->maxLength(255),
                TextInput::make('pickup_address_text')
                    ->label('Alamat Pickup')
                    ->required()
                    ->maxLength(500),
                TextInput::make('pickup_latitude')
                    ->label('Latitude Pickup')
                    ->numeric()
                    ->required(),
                TextInput::make('pickup_longitude')
                    ->label('Longitude Pickup')
                    ->numeric()
                    ->required(),
                TextInput::make('delivery_address_text')
                    ->label('Alamat Pengiriman')
                    ->required()
                    ->maxLength(500),
                TextInput::make('delivery_latitude')
                    ->label('Latitude Pengiriman')
                    ->numeric()
                    ->required(),
                TextInput::make('delivery_longitude')
                    ->label('Longitude Pengiriman')
                    ->numeric()
                    ->required(),
                TextInput::make('item_price')
                    ->label('Harga Barang (Rp)')
                    ->numeric()
                    ->required()
                    ->default(0),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2),
                Placeholder::make('fee_estimate')
                    ->label('Estimasi Ongkir')
                    ->content(fn () => $this->estimatedFee !== null
                        ? 'Rp ' . number_format($this->estimatedFee, 0, ',', '.') . ' (' . $this->estimatedDistance . ' km)'
                        : 'Isi koordinat dan klik Hitung Ongkir'),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('submit')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment($this->getFormActionsAlignment())
                            ->key('form-actions'),
                    ]),
            ]);
    }

    public function calculateFee(): void
    {
        $data = $this->form->getState();

        if (empty($data['pickup_latitude']) || empty($data['delivery_latitude'])) {
            Notification::make()->title('Isi koordinat terlebih dahulu')->warning()->send();
            return;
        }

        $result = app(DeliveryFeeService::class)->calculate(
            (float) $data['pickup_latitude'],
            (float) $data['pickup_longitude'],
            (float) $data['delivery_latitude'],
            (float) $data['delivery_longitude'],
        );

        $this->estimatedFee = $result['delivery_fee'];
        $this->estimatedDistance = $result['distance_km'];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('calculateFee')
                ->label('Hitung Ongkir')
                ->color('gray')
                ->action('calculateFee'),
            Action::make('submit')
                ->label('Buat Pesanan')
                ->submit('submit'),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $seller = Filament::auth()->user();

        $order = app(OrderService::class)->createOrder($data, $seller);

        Notification::make()
            ->title('Pesanan berhasil dibuat: ' . $order->order_code)
            ->success()
            ->send();

        $this->form->fill([
            'pickup_latitude' => $seller->latitude,
            'pickup_longitude' => $seller->longitude,
            'pickup_address_text' => $seller->address_text,
            'order_source' => OrderSource::WaFb->value,
        ]);

        $this->estimatedFee = null;
        $this->estimatedDistance = null;
    }
}
