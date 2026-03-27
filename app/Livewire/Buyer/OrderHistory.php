<?php

declare(strict_types=1);

namespace App\Livewire\Buyer;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use WithPagination;

    #[Computed]
    public function orders(): LengthAwarePaginator
    {
        return Order::where(function ($q) {
            $q->where('buyer_id', Auth::id())
                ->orWhere('buyer_phone', Auth::user()->phone);
        })
        ->orderByDesc('created_at')
        ->paginate(10);
    }

    public function render()
    {
        return view('livewire.buyer.order-history')
            ->layout('layouts.buyer', ['title' => 'Riwayat Pesanan']);
    }
}
