<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('photo')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_preorder')->default(false);
            $table->integer('min_preorder_hours')->nullable();
            $table->timestamps();

            $table->index('seller_id');
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
