<?php

declare(strict_types=1);

namespace App\Filament\Courier\Resources\MyOrderResource\Pages;

use App\Filament\Courier\Resources\MyOrderResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMyOrder extends ViewRecord
{
    protected static string $resource = MyOrderResource::class;

    protected string $view = 'filament.courier.pages.view-my-order';
}
