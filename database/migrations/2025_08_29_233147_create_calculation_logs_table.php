<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('calculation_id')->constrained('calculations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Snapshot of fields at time of action
            $table->decimal('total_invoice', 10, 2)->nullable();
            $table->integer('parcel_rows_count')->nullable();
            $table->decimal('vehicule_rental_price', 10, 2)->nullable();
            $table->decimal('broker_percentage', 5, 2)->default(0);
            $table->decimal('bonus', 10, 2)->nullable();
            $table->decimal('cash_advance', 10, 2)->nullable();
            $table->decimal('final_amount', 10, 2)->nullable();
            $table->string('pdf_path')->nullable();

            $table->string('action', 32)->default('update');
            $table->timestamps();

            $table->index(['calculation_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_logs');
    }
};