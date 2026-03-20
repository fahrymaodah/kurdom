<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DeliveryFeeConfigResource\Pages;

use App\Filament\Admin\Resources\DeliveryFeeConfigResource;
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
