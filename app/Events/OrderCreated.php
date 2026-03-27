<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    /**
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('orders'),
        ];

        if ($this->order->buyer_id) {
            $channels[] = new PrivateChannel('buyer.' . $this->order->buyer_id);
        }

        return $channels;
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'seller_name' => $this->order->seller?->name,
            'pickup_address' => $this->order->pickup_address_text,
            'delivery_address' => $this->order->delivery_address_text,
            'distance_km' => $this->order->distance_km,
            'delivery_fee' => $this->order->delivery_fee,
            'status' => $this->order->status->value,
        ];
    }
}
