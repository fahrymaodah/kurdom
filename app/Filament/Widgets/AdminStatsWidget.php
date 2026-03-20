<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Penjual', User::role(UserRole::Seller)->count())
                ->icon('heroicon-o-building-storefront'),
            Stat::make('Total Kurir', User::role(UserRole::Courier)->count())
                ->icon('heroicon-o-truck'),
            Stat::make('Pesanan Hari Ini', Order::whereDate('created_at', today())->count())
                ->icon('heroicon-o-shopping-bag'),
            Stat::make('Selesai Hari Ini', Order::where('status', OrderStatus::Completed)->whereDate('completed_at', today())->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
