<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DeliveryFeeConfigResource\Pages;

use App\Filament\Admin\Resources\DeliveryFeeConfigResource;
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
