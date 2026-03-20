<?php

namespace App\Filament\Resources\DeliveryFeeConfigResource\Pages;

use App\Filament\Resources\DeliveryFeeConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryFeeConfigs extends ListRecords
{
    protected static string $resource = DeliveryFeeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
