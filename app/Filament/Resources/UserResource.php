<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;
use BackedEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Select::make('role')
                    ->options(collect(UserRole::cases())->mapWithKeys(fn ($role) => [$role->value => $role->label()]))
                    ->required(),
                TextInput::make('address_text')
                    ->label('Alamat')
                    ->maxLength(500),
                TextInput::make('latitude')
                    ->numeric()
                    ->step(0.0000001),
                TextInput::make('longitude')
                    ->numeric()
                    ->step(0.0000001),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn (UserRole $state) => $state->label()),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(collect(UserRole::cases())->mapWithKeys(fn ($role) => [$role->value => $role->label()])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn (User $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn (User $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update(['is_active' => ! $record->is_active])),
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
            'index' => UserResource\Pages\ListUsers::route('/'),
            'create' => UserResource\Pages\CreateUser::route('/create'),
            'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
