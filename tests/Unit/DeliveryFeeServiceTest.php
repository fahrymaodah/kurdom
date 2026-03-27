<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\DeliveryFeeConfig;
use App\Repositories\Contracts\DeliveryFeeConfigRepositoryInterface;
use App\Services\DeliveryFeeService;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class DeliveryFeeServiceTest extends TestCase
{
    private DeliveryFeeService $service;

    private DeliveryFeeConfigRepositoryInterface $configRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configRepo = Mockery::mock(DeliveryFeeConfigRepositoryInterface::class);
        $this->service = new DeliveryFeeService($this->configRepo);
    }

    public function test_calculate_distance_returns_correct_haversine(): void
    {
        // Dompu center to ~1km away
        $distance = $this->service->calculateDistance(-8.5365, 118.4633, -8.5365, 118.4733);

        $this->assertGreaterThan(0, $distance);
        $this->assertLessThan(2, $distance); // roughly ~1.1 km
    }

    public function test_calculate_distance_same_point_returns_zero(): void
    {
        $distance = $this->service->calculateDistance(-8.5365, 118.4633, -8.5365, 118.4633);

        $this->assertEquals(0.0, $distance);
    }

    public function test_calculate_returns_zero_when_no_config(): void
    {
        $this->configRepo->shouldReceive('getActiveConfig')->andReturn(null);

        $result = $this->service->calculate(-8.5365, 118.4633, -8.5400, 118.4700);

        $this->assertEquals(0, $result['distance_km']);
        $this->assertEquals(0, $result['delivery_fee']);
        $this->assertFalse($result['is_night']);
    }

    public function test_calculate_uses_near_rate_for_short_distance(): void
    {
        $config = $this->makeConfig(3.0, 5000, 10000);
        $this->configRepo->shouldReceive('getActiveConfig')->andReturn($config);

        // Two points ~1km apart
        $result = $this->service->calculate(-8.5365, 118.4633, -8.5365, 118.4733, Carbon::parse('12:00'));

        $this->assertLessThanOrEqual(3.0, $result['distance_km']);
        $this->assertEquals(round($result['distance_km'] * 5000, 2), $result['delivery_fee']);
        $this->assertFalse($result['is_night']);
    }

    public function test_calculate_uses_far_rate_for_long_distance(): void
    {
        $config = $this->makeConfig(1.0, 5000, 10000); // threshold only 1km
        $this->configRepo->shouldReceive('getActiveConfig')->andReturn($config);

        // Two points ~5km apart
        $result = $this->service->calculate(-8.5365, 118.4633, -8.5365, 118.5133, Carbon::parse('12:00'));

        $this->assertGreaterThan(1.0, $result['distance_km']);
        $this->assertEquals(round($result['distance_km'] * 10000, 2), $result['delivery_fee']);
    }

    public function test_calculate_adds_night_surcharge(): void
    {
        $config = $this->makeConfig(3.0, 5000, 10000, '22:00', '06:00', 5000);
        $this->configRepo->shouldReceive('getActiveConfig')->andReturn($config);

        $result = $this->service->calculate(-8.5365, 118.4633, -8.5365, 118.4733, Carbon::parse('23:00'));

        $this->assertTrue($result['is_night']);
        $baseFee = round($result['distance_km'] * 5000, 2);
        $this->assertEquals($baseFee + 5000, $result['delivery_fee']);
    }

    public function test_calculate_no_night_surcharge_during_day(): void
    {
        $config = $this->makeConfig(3.0, 5000, 10000, '22:00', '06:00', 5000);
        $this->configRepo->shouldReceive('getActiveConfig')->andReturn($config);

        $result = $this->service->calculate(-8.5365, 118.4633, -8.5365, 118.4733, Carbon::parse('14:00'));

        $this->assertFalse($result['is_night']);
    }

    public function test_night_surcharge_crosses_midnight(): void
    {
        $config = $this->makeConfig(3.0, 5000, 10000, '22:00', '06:00', 5000);
        $this->configRepo->shouldReceive('getActiveConfig')->andReturn($config);

        // 02:00 should be night
        $result = $this->service->calculate(-8.5365, 118.4633, -8.5365, 118.4733, Carbon::parse('02:00'));

        $this->assertTrue($result['is_night']);
    }

    private function makeConfig(
        float $threshold = 3.0,
        float $nearRate = 5000,
        float $farRate = 10000,
        ?string $nightStart = null,
        ?string $nightEnd = null,
        float $nightSurcharge = 0,
    ): DeliveryFeeConfig {
        $config = new DeliveryFeeConfig;
        $config->distance_threshold_km = $threshold;
        $config->near_rate = $nearRate;
        $config->far_rate = $farRate;
        $config->night_start_time = $nightStart;
        $config->night_end_time = $nightEnd;
        $config->night_surcharge = $nightSurcharge;
        $config->is_active = true;

        return $config;
    }
}
