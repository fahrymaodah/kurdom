<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Models\DeliveryFeeConfig;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);

        DeliveryFeeConfig::create([
            'distance_threshold_km' => 3.00,
            'near_rate' => 5000.00,
            'far_rate' => 10000.00,
            'night_start_time' => '22:00',
            'night_end_time' => '06:00',
            'night_surcharge' => 5000.00,
            'is_active' => true,
        ]);
    }

    public function test_create_order_persists_and_calculates_fee(): void
    {
        $seller = User::factory()->seller()->create([
            'address_text' => 'Jl. Test, Dompu',
            'latitude' => -8.5370,
            'longitude' => 118.4631,
        ]);

        $order = $this->orderService->createOrder([
            'order_source' => OrderSource::WaFb,
            'buyer_name' => 'Test Buyer',
            'buyer_phone' => '08111111111',
            'pickup_latitude' => -8.5370,
            'pickup_longitude' => 118.4631,
            'pickup_address_text' => 'Jl. Test, Dompu',
            'delivery_latitude' => -8.5400,
            'delivery_longitude' => 118.4700,
            'delivery_address_text' => 'Jl. Tujuan, Dompu',
            'item_price' => 50000,
            'notes' => 'Test order',
        ], $seller);

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertStringStartsWith('KD-', $order->order_code);
        $this->assertEquals(OrderStatus::New, $order->status);
        $this->assertEquals($seller->id, $order->seller_id);
        $this->assertGreaterThan(0, $order->delivery_fee);
        $this->assertGreaterThan(0, $order->distance_km);
        $this->assertEquals(50000 + (float) $order->delivery_fee, (float) $order->total);
    }

    public function test_claim_order_assigns_courier(): void
    {
        $seller = User::factory()->seller()->create();
        $courier = User::factory()->courier()->create();

        $order = $this->createTestOrder($seller);

        $claimed = $this->orderService->claimOrder($order, $courier);

        $this->assertEquals(OrderStatus::CourierAssigned, $claimed->status);
        $this->assertEquals($courier->id, $claimed->courier_id);
        $this->assertNotNull($claimed->courier_assigned_at);
    }

    public function test_cannot_claim_non_new_order(): void
    {
        $seller = User::factory()->seller()->create();
        $courier1 = User::factory()->courier()->create();
        $courier2 = User::factory()->courier()->create();

        $order = $this->createTestOrder($seller);
        $this->orderService->claimOrder($order, $courier1);

        $this->expectException(LogicException::class);
        $this->orderService->claimOrder($order, $courier2);
    }

    public function test_update_status_follows_state_machine(): void
    {
        $seller = User::factory()->seller()->create();
        $courier = User::factory()->courier()->create();

        $order = $this->createTestOrder($seller);
        $order = $this->orderService->claimOrder($order, $courier);

        $order = $this->orderService->updateStatus($order, OrderStatus::PickedUp);
        $this->assertEquals(OrderStatus::PickedUp, $order->status);
        $this->assertNotNull($order->picked_up_at);

        $order = $this->orderService->updateStatus($order, OrderStatus::InDelivery);
        $this->assertEquals(OrderStatus::InDelivery, $order->status);
        $this->assertNotNull($order->delivery_started_at);

        $order = $this->orderService->updateStatus($order, OrderStatus::Completed);
        $this->assertEquals(OrderStatus::Completed, $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function test_invalid_status_transition_throws(): void
    {
        $seller = User::factory()->seller()->create();

        $order = $this->createTestOrder($seller);

        $this->expectException(LogicException::class);
        $this->orderService->updateStatus($order, OrderStatus::Completed);
    }

    public function test_cancel_order(): void
    {
        $seller = User::factory()->seller()->create();

        $order = $this->createTestOrder($seller);
        $cancelled = $this->orderService->cancelOrder($order, 'Customer changed mind');

        $this->assertEquals(OrderStatus::Cancelled, $cancelled->status);
        $this->assertNotNull($cancelled->cancelled_at);
        $this->assertEquals('Customer changed mind', $cancelled->cancel_reason);
    }

    public function test_cannot_cancel_completed_order(): void
    {
        $seller = User::factory()->seller()->create();
        $courier = User::factory()->courier()->create();

        $order = $this->createTestOrder($seller);
        $order = $this->orderService->claimOrder($order, $courier);
        $order = $this->orderService->updateStatus($order, OrderStatus::PickedUp);
        $order = $this->orderService->updateStatus($order, OrderStatus::InDelivery);
        $order = $this->orderService->updateStatus($order, OrderStatus::Completed);

        $this->expectException(LogicException::class);
        $this->orderService->cancelOrder($order);
    }

    private function createTestOrder(User $seller): Order
    {
        return $this->orderService->createOrder([
            'order_source' => OrderSource::WaFb,
            'buyer_name' => 'Test Buyer',
            'buyer_phone' => '08111111111',
            'pickup_latitude' => -8.5370,
            'pickup_longitude' => 118.4631,
            'pickup_address_text' => 'Toko Test',
            'delivery_latitude' => -8.5400,
            'delivery_longitude' => 118.4700,
            'delivery_address_text' => 'Rumah Test',
            'item_price' => 25000,
        ], $seller);
    }
}
