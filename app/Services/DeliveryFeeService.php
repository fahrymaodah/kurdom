<?php

namespace App\Services;

use App\Models\DeliveryFeeConfig;
use Carbon\Carbon;

class DeliveryFeeService
{
    /**
     * Calculate the Haversine distance between two coordinates in kilometers.
     */
    public function calculateDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2,
    ): float {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 2);
    }

    /**
     * Calculate the delivery fee based on distance and time of day.
     *
     * @return array{distance_km: float, delivery_fee: float, is_night: bool}
     */
    public function calculate(
        float $pickupLat,
        float $pickupLng,
        float $deliveryLat,
        float $deliveryLng,
        ?Carbon $at = null,
    ): array {
        $config = DeliveryFeeConfig::active()->latest()->first();

        if (! $config) {
            return [
                'distance_km' => 0,
                'delivery_fee' => 0,
                'is_night' => false,
            ];
        }

        $distance = $this->calculateDistance($pickupLat, $pickupLng, $deliveryLat, $deliveryLng);
        $at ??= Carbon::now();

        $rate = $distance <= $config->distance_threshold_km
            ? $config->near_rate
            : $config->far_rate;

        $fee = $rate * $distance;

        $isNight = $this->isNightTime($at, $config->night_start_time, $config->night_end_time);
        if ($isNight) {
            $fee += $config->night_surcharge;
        }

        return [
            'distance_km' => $distance,
            'delivery_fee' => round($fee, 2),
            'is_night' => $isNight,
        ];
    }

    protected function isNightTime(Carbon $time, ?string $nightStart, ?string $nightEnd): bool
    {
        if (! $nightStart || ! $nightEnd) {
            return false;
        }

        $current = $time->format('H:i');

        // Night period crosses midnight (e.g., 22:00 - 05:00)
        if ($nightStart > $nightEnd) {
            return $current >= $nightStart || $current < $nightEnd;
        }

        return $current >= $nightStart && $current < $nightEnd;
    }
}
