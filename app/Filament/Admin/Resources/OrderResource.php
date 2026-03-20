<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ShoppingBag;

    protected static string | UnitEnum | null $navigationGroup = 'Operasional';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Pesanan';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('buyer_name')
                    ->label('Pembeli')
                    ->searchable(),
                TextColumn::make('seller.name')
                    ->label('Penjual'),
                TextColumn::make('courier.name')
                    ->label('Kurir')
                    ->default('-'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label())
                    ->color(fn (OrderStatus $state): string => $state->color()),
                TextColumn::make('order_source')
                    ->label('Sumber')
                    ->formatStateUsing(fn (OrderSource $state): string => $state->label()),
                TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $s): array => [$s->value => $s->label()])),
                SelectFilter::make('order_source')
                    ->label('Sumber')
                    ->options(collect(OrderSource::cases())->mapWithKeys(fn (OrderSource $s): array => [$s->value => $s->label()])),
            ])
            ->actions([
                Actions\ViewAction::make(),
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
