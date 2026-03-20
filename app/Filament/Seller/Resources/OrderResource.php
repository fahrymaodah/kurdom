<?php

namespace App\Filament\Seller\Resources;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Pesanan Saya';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('seller_id', Filament::auth()->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('buyer_name')
                    ->label('Pembeli'),
                TextColumn::make('courier.name')
                    ->label('Kurir')
                    ->default('-'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->color(fn (OrderStatus $state) => $state->color()),
                TextColumn::make('total')
                    ->money('IDR'),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->canTransitionTo(OrderStatus::Cancelled))
                    ->action(fn (Order $record) => app(\App\Services\OrderService::class)->cancelOrder($record, 'Dibatalkan oleh seller')),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function getPages(): array
    {
        return [
            'index' => OrderResource\Pages\ListOrders::route('/'),
            'view' => OrderResource\Pages\ViewOrder::route('/{record}'),
        ];
    }
}
