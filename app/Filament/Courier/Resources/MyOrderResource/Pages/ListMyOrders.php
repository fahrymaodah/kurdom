<?php

namespace App\Filament\Courier\Resources\MyOrderResource\Pages;

use App\Filament\Courier\Resources\MyOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListMyOrders extends ListRecords
{
    protected static string $resource = MyOrderResource::class;
}
