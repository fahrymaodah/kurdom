<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Events\OrderTaken;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderClaimedNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\OrderStatusNotification;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Str;
use LogicException;

class OrderService
{
    public function __construct(
        protected DeliveryFeeService $deliveryFeeService,
        protected OrderRepositoryInterface $orderRepository,
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

        return tap($this->orderRepository->create([
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
        ]), function (Order $order) {
            OrderCreated::dispatch($order->load('seller'));

            if ($order->buyer_id) {
                $order->buyer?->notify(new OrderCreatedNotification($order));
            }
        });
    }

    public function claimOrder(Order $order, User $courier): Order
    {
        if (! $order->canTransitionTo(OrderStatus::CourierAssigned)) {
            throw new LogicException('Order cannot be claimed in its current status.');
        }

        return tap($this->orderRepository->update($order, [
            'courier_id' => $courier->id,
            'status' => OrderStatus::CourierAssigned,
            'courier_assigned_at' => now(),
        ]), function (Order $o) {
            OrderTaken::dispatch($o->load('courier'));

            $o->seller?->notify(new OrderClaimedNotification($o));
            if ($o->buyer_id) {
                $o->buyer?->notify(new OrderClaimedNotification($o));
            }
        });
    }

    public function updateStatus(Order $order, OrderStatus $newStatus, ?string $cancelReason = null): Order
    {
        if (! $order->canTransitionTo($newStatus)) {
            throw new LogicException("Cannot transition from {$order->status->label()} to {$newStatus->label()}.");
        }

        $timestamps = match ($newStatus) {
            OrderStatus::PickedUp => ['picked_up_at' => now()],
            OrderStatus::InDelivery => ['delivery_started_at' => now()],
            OrderStatus::Completed => ['completed_at' => now()],
            OrderStatus::Cancelled => ['cancelled_at' => now(), 'cancel_reason' => $cancelReason],
            default => [],
        };

        return tap($this->orderRepository->update($order, [
            'status' => $newStatus,
            ...$timestamps,
        ]), function (Order $o) {
            OrderStatusUpdated::dispatch($o);

            $o->seller?->notify(new OrderStatusNotification($o));
            $o->courier?->notify(new OrderStatusNotification($o));
            if ($o->buyer_id) {
                $o->buyer?->notify(new OrderStatusNotification($o));
            }
        });
    }

    public function cancelOrder(Order $order, ?string $reason = null): Order
    {
        return $this->updateStatus($order, OrderStatus::Cancelled, $reason);
    }
}
