<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderClaimedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    /**
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Kurir Ditemukan: ' . $this->order->order_code,
            'body' => $this->order->courier?->name . ' akan mengantarkan pesanan Anda.',
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'status' => $this->order->status->value,
        ];
    }
}
