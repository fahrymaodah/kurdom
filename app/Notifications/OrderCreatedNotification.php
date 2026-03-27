<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification
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
            'title' => 'Pesanan Baru: ' . $this->order->order_code,
            'body' => 'Pesanan baru dari ' . $this->order->seller?->name . ' menunggu kurir.',
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'status' => $this->order->status->value,
        ];
    }
}
