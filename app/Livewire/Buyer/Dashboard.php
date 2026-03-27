<?php

declare(strict_types=1);

namespace App\Livewire\Buyer;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function activeOrders()
    {
        return Order::where(function ($q) {
            $q->where('buyer_id', Auth::id())
                ->orWhere(function ($q2) {
                    $q2->where('buyer_phone', Auth::user()->phone);
                });
        })
        ->whereNotIn('status', [OrderStatus::Completed, OrderStatus::Cancelled])
        ->orderByDesc('created_at')
        ->get();
    }

    public function render()
    {
        return view('livewire.buyer.dashboard')
            ->layout('layouts.buyer', ['title' => 'Dashboard']);
    }
}
