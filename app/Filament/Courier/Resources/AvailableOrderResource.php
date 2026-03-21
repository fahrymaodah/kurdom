<?php

declare(strict_types=1);

namespace App\Filament\Courier\Resources;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvailableOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::QueueList;

    protected static ?string $modelLabel = 'Pesanan Tersedia';

    protected static ?string $pluralModelLabel = 'Pesanan Tersedia';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'available-orders';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', OrderStatus::New);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('seller.name')
                    ->label('Penjual'),
                TextColumn::make('pickup_address_text')
                    ->label('Pickup')
                    ->limit(30),
                TextColumn::make('delivery_address_text')
                    ->label('Tujuan')
                    ->limit(30),
                TextColumn::make('distance_km')
                    ->label('Jarak')
                    ->suffix(' km'),
                TextColumn::make('delivery_fee')
                    ->label('Ongkir')
                    ->money('IDR'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Actions\Action::make('claim')
                    ->label('Ambil Pesanan')
                    ->color('success')
                    ->icon(Heroicon::HandRaised)
                    ->requiresConfirmation()
                    ->modalHeading('Ambil Pesanan?')
                    ->modalDescription('Anda akan bertanggung jawab mengantarkan pesanan ini.')
                    ->action(function (Order $record) {
                        app(OrderService::class)->claimOrder($record, Filament::auth()->user());
                    }),
            ])
            ->poll('15s');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function getPages(): array
    {
        return [
            'index' => AvailableOrderResource\Pages\ListAvailableOrders::route('/'),
        ];
    }
}
