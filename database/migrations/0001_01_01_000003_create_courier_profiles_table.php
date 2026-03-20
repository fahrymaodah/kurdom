<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('is_online')->default(false);
            $table->decimal('zone_latitude', 10, 7)->nullable();
            $table->decimal('zone_longitude', 10, 7)->nullable();
            $table->decimal('zone_radius_km', 5, 2)->nullable();
            $table->string('vehicle', 20)->default('motorcycle');
            $table->string('license_plate')->nullable();
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_profiles');
    }
};
