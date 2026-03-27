<?php

declare(strict_types=1);

namespace App\Livewire\Buyer;

use App\Models\Order;
use Livewire\Component;

class TrackOrder extends Component
{
    public string $orderCode = '';

    public ?Order $order = null;

    public bool $notFound = false;

    public function mount(string $code = ''): void
    {
        if ($code) {
            $this->orderCode = $code;
            $this->track();
        }
    }

    public function track(): void
    {
        $this->notFound = false;
        $this->order = null;

        if (empty($this->orderCode)) {
            return;
        }

        $this->order = Order::where('order_code', $this->orderCode)->first();

        if (! $this->order) {
            $this->notFound = true;
        }
    }

    public function render()
    {
        return view('livewire.buyer.track-order')
            ->layout('layouts.buyer', ['title' => 'Lacak Pesanan']);
    }
}
