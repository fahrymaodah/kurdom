<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->constrained('users');
            $table->tinyInteger('score');
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['order_id', 'from_user_id', 'to_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
