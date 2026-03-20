<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('order_source', 20)->default('wa_fb');
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('courier_id')->nullable()->constrained('users');
            $table->foreignId('buyer_id')->nullable()->constrained('users');
            $table->string('buyer_name');
            $table->string('buyer_phone');
            $table->decimal('pickup_latitude', 10, 7);
            $table->decimal('pickup_longitude', 10, 7);
            $table->string('pickup_address_text');
            $table->decimal('delivery_latitude', 10, 7);
            $table->decimal('delivery_longitude', 10, 7);
            $table->string('delivery_address_text');
            $table->decimal('distance_km', 8, 2);
            $table->decimal('item_price', 12, 2);
            $table->decimal('delivery_fee', 12, 2);
            $table->decimal('total', 12, 2);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamp('courier_assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivery_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('buyer_phone');
            $table->index('order_source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
