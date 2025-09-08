<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_plan')->nullable(); // Can be useful for logging
            $table->string('subscription_id')->nullable(); // If using Paddle, etc
            $table->decimal('total_price', 10, 2)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->timestamp('ends_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};