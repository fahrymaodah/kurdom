<?php

declare(strict_types=1);

namespace App\Filament\Courier\Resources;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $modelLabel = 'Pesanan Saya';

    protected static ?string $pluralModelLabel = 'Pesanan Saya';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'my-orders';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('courier_id', Filament::auth()->id());
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
                TextColumn::make('buyer_name')
                    ->label('Pembeli'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label())
                    ->color(fn (OrderStatus $state): string => $state->color()),
                TextColumn::make('delivery_fee')
                    ->label('Ongkir')
                    ->money('IDR'),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $s): array => [$s->value => $s->label()])),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\Action::make('pickedUp')
                    ->label('Picked Up')
                    ->color('warning')
                    ->icon(Heroicon::ArchiveBox)
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->canTransitionTo(OrderStatus::PickedUp))
                    ->action(fn (Order $record) => app(OrderService::class)->updateStatus($record, OrderStatus::PickedUp)),
                Actions\Action::make('startDelivery')
                    ->label('Mulai Antar')
                    ->color('primary')
                    ->icon(Heroicon::Truck)
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->canTransitionTo(OrderStatus::InDelivery))
                    ->action(fn (Order $record) => app(OrderService::class)->updateStatus($record, OrderStatus::InDelivery)),
                Actions\Action::make('complete')
                    ->label('Selesai')
                    ->color('success')
                    ->icon(Heroicon::CheckCircle)
                    ->requiresConfirmation()
                    ->visible(fn (Order $record) => $record->canTransitionTo(OrderStatus::Completed))
                    ->action(fn (Order $record) => app(OrderService::class)->updateStatus($record, OrderStatus::Completed)),
                Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->color('danger')
                    ->icon(Heroicon::XCircle)
                    ->visible(fn (Order $record) => $record->canTransitionTo(OrderStatus::Cancelled))
                    ->schema([
                        Textarea::make('cancel_reason')
                            ->label('Alasan Pembatalan')
                            ->required(),
                    ])
                    ->action(fn (Order $record, array $data) => app(OrderService::class)->cancelOrder($record, $data['cancel_reason'])),
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
            'index' => MyOrderResource\Pages\ListMyOrders::route('/'),
            'view' => MyOrderResource\Pages\ViewMyOrder::route('/{record}'),
        ];
    }
}
