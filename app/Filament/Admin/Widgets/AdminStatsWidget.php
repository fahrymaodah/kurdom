<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Penjual', User::role(UserRole::Seller)->count())
                ->icon(Heroicon::BuildingStorefront),
            Stat::make('Total Kurir', User::role(UserRole::Courier)->count())
                ->icon(Heroicon::Truck),
            Stat::make('Pesanan Hari Ini', Order::whereDate('created_at', today())->count())
                ->icon(Heroicon::ShoppingBag),
            Stat::make('Selesai Hari Ini', Order::where('status', OrderStatus::Completed)->whereDate('completed_at', today())->count())
                ->icon(Heroicon::CheckCircle)
                ->color('success'),
        ];
    }
}
