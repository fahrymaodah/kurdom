<?php

namespace App\Filament\Resources\DeliveryFeeConfigResource\Pages;

use App\Filament\Resources\DeliveryFeeConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryFeeConfig extends EditRecord
{
    protected static string $resource = DeliveryFeeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
