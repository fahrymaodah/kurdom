<?php

namespace App\Filament\Courier\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CourierStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $courierId = Filament::auth()->id();
        $isOnline = Filament::auth()->user()->courierProfile?->is_online ?? false;

        return [
            Stat::make('Status', $isOnline ? 'ONLINE' : 'OFFLINE')
                ->icon($isOnline ? 'heroicon-o-signal' : 'heroicon-o-signal-slash')
                ->color($isOnline ? 'success' : 'danger'),
            Stat::make('Pesanan Aktif', Order::where('courier_id', $courierId)->inProgress()->count())
                ->icon('heroicon-o-truck'),
            Stat::make('Selesai Hari Ini', Order::where('courier_id', $courierId)->where('status', OrderStatus::Completed)->whereDate('completed_at', today())->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Ongkir Hari Ini', 'Rp ' . number_format(
                Order::where('courier_id', $courierId)->where('status', OrderStatus::Completed)->whereDate('completed_at', today())->sum('delivery_fee'),
                0, ',', '.'
            ))
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
