<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected DeliveryFeeService $deliveryFeeService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data, User $seller): Order
    {
        $feeResult = $this->deliveryFeeService->calculate(
            $data['pickup_latitude'],
            $data['pickup_longitude'],
            $data['delivery_latitude'],
            $data['delivery_longitude'],
        );

        $itemPrice = (float) ($data['item_price'] ?? 0);
        $deliveryFee = $feeResult['delivery_fee'];
        $total = $itemPrice + $deliveryFee;

        return Order::create([
            'order_code' => 'KD-' . strtoupper(Str::random(8)),
            'order_source' => $data['order_source'],
            'seller_id' => $seller->id,
            'buyer_name' => $data['buyer_name'],
            'buyer_phone' => $data['buyer_phone'],
            'buyer_id' => $data['buyer_id'] ?? null,
            'pickup_latitude' => $data['pickup_latitude'],
            'pickup_longitude' => $data['pickup_longitude'],
            'pickup_address_text' => $data['pickup_address_text'] ?? $seller->address_text,
            'delivery_latitude' => $data['delivery_latitude'],
            'delivery_longitude' => $data['delivery_longitude'],
            'delivery_address_text' => $data['delivery_address_text'],
            'distance_km' => $feeResult['distance_km'],
            'item_price' => $itemPrice,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'notes' => $data['notes'] ?? null,
            'status' => OrderStatus::New,
        ]);
    }

    public function claimOrder(Order $order, User $courier): Order
    {
        if (! $order->canTransitionTo(OrderStatus::CourierAssigned)) {
            throw new \LogicException('Order cannot be claimed in its current status.');
        }

        $order->update([
            'courier_id' => $courier->id,
            'status' => OrderStatus::CourierAssigned,
            'courier_assigned_at' => now(),
        ]);

        return $order->refresh();
    }

    public function updateStatus(Order $order, OrderStatus $newStatus, ?string $cancelReason = null): Order
    {
        if (! $order->canTransitionTo($newStatus)) {
            throw new \LogicException("Cannot transition from {$order->status->label()} to {$newStatus->label()}.");
        }

        $timestamps = match ($newStatus) {
            OrderStatus::PickedUp => ['picked_up_at' => now()],
            OrderStatus::InDelivery => ['delivery_started_at' => now()],
            OrderStatus::Completed => ['completed_at' => now()],
            OrderStatus::Cancelled => ['cancelled_at' => now(), 'cancel_reason' => $cancelReason],
            default => [],
        };

        $order->update([
            'status' => $newStatus,
            ...$timestamps,
        ]);

        return $order->refresh();
    }

    public function cancelOrder(Order $order, ?string $reason = null): Order
    {
        return $this->updateStatus($order, OrderStatus::Cancelled, $reason);
    }
}
