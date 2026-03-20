<?php

namespace Database\Seeders;

use App\Models\DeliveryFeeConfig;
use Illuminate\Database\Seeder;

class DeliveryFeeConfigSeeder extends Seeder
{
    public function run(): void
    {
        DeliveryFeeConfig::updateOrCreate(
            ['is_active' => true],
            [
                'distance_threshold_km' => 3.00,
                'near_rate' => 5000.00,
                'far_rate' => 10000.00,
                'night_start_time' => '22:00',
                'night_end_time' => '06:00',
                'night_surcharge' => 5000.00,
            ],
        );
    }
}
