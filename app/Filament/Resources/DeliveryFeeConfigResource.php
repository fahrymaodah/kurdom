<?php

namespace App\Filament\Resources;

use App\Models\DeliveryFeeConfig;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use BackedEnum;

class DeliveryFeeConfigResource extends Resource
{
    protected static ?string $model = DeliveryFeeConfig::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = 'Tarif Pengiriman';

    protected static ?string $pluralModelLabel = 'Tarif Pengiriman';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('distance_threshold_km')
                    ->label('Batas Jarak (km)')
                    ->numeric()
                    ->step(0.01)
                    ->required(),
                TextInput::make('near_rate')
                    ->label('Tarif Dekat (Rp/km)')
                    ->numeric()
                    ->step(0.01)
                    ->required(),
                TextInput::make('far_rate')
                    ->label('Tarif Jauh (Rp/km)')
                    ->numeric()
                    ->step(0.01)
                    ->required(),
                TimePicker::make('night_start_time')
                    ->label('Mulai Malam'),
                TimePicker::make('night_end_time')
                    ->label('Akhir Malam'),
                TextInput::make('night_surcharge')
                    ->label('Surcharge Malam (Rp)')
                    ->numeric()
                    ->step(0.01)
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('distance_threshold_km')
                    ->label('Batas Jarak')
                    ->suffix(' km'),
                TextColumn::make('near_rate')
                    ->label('Tarif Dekat')
                    ->money('IDR'),
                TextColumn::make('far_rate')
                    ->label('Tarif Jauh')
                    ->money('IDR'),
                TextColumn::make('night_surcharge')
                    ->label('Surcharge Malam')
                    ->money('IDR'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DeliveryFeeConfigResource\Pages\ListDeliveryFeeConfigs::route('/'),
            'create' => DeliveryFeeConfigResource\Pages\CreateDeliveryFeeConfig::route('/create'),
            'edit' => DeliveryFeeConfigResource\Pages\EditDeliveryFeeConfig::route('/{record}/edit'),
        ];
    }
}
