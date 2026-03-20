<?php

namespace App\Filament\Seller\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SellerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $sellerId = Filament::auth()->id();

        return [
            Stat::make('Pesanan Menunggu', Order::where('seller_id', $sellerId)->where('status', OrderStatus::New)->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('Sedang Dikirim', Order::where('seller_id', $sellerId)->inProgress()->count())
                ->icon('heroicon-o-truck'),
            Stat::make('Selesai Hari Ini', Order::where('seller_id', $sellerId)->where('status', OrderStatus::Completed)->whereDate('completed_at', today())->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Total Hari Ini', 'Rp ' . number_format(
                Order::where('seller_id', $sellerId)->whereDate('created_at', today())->sum('total'),
                0, ',', '.'
            ))
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
