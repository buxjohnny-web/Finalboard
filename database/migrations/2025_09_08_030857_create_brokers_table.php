<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brokers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('logo')->nullable();
            $table->enum('subscription_tier', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brokers');
    }
};