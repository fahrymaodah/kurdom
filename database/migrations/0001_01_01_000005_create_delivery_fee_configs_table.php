<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_fee_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('distance_threshold_km', 5, 2)->default(3.00);
            $table->decimal('near_rate', 12, 2)->default(5000.00);
            $table->decimal('far_rate', 12, 2)->default(10000.00);
            $table->time('night_start_time')->default('22:00');
            $table->time('night_end_time')->default('06:00');
            $table->decimal('night_surcharge', 12, 2)->default(5000.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_fee_configs');
    }
};
