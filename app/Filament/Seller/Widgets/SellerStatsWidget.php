<?php

declare(strict_types=1);

namespace App\Filament\Seller\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SellerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $sellerId = Filament::auth()->id();

        return [
            Stat::make('Pesanan Menunggu', Order::where('seller_id', $sellerId)->where('status', OrderStatus::New)->count())
                ->icon(Heroicon::Clock)
                ->color('warning'),
            Stat::make('Sedang Dikirim', Order::where('seller_id', $sellerId)->inProgress()->count())
                ->icon(Heroicon::Truck),
            Stat::make('Selesai Hari Ini', Order::where('seller_id', $sellerId)->where('status', OrderStatus::Completed)->whereDate('completed_at', today())->count())
                ->icon(Heroicon::CheckCircle)
                ->color('success'),
            Stat::make('Total Hari Ini', 'Rp ' . number_format(
                (float) Order::where('seller_id', $sellerId)->whereDate('created_at', today())->sum('total'),
                0, ',', '.'
            ))
                ->icon(Heroicon::Banknotes),
        ];
    }
}
