<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();

            // Week identifier (e.g., "2025-W35")
            $table->string('week');

            // Parsed/entered values
            $table->decimal('total_invoice', 10, 2)->nullable();      // digits only, nullable
            $table->integer('parcel_rows_count')->nullable();          // digits only, nullable

            // Inputs (only broker_percentage is required)
            $table->decimal('vehicule_rental_price', 10, 2)->nullable(); // digits only, nullable
            $table->decimal('broker_percentage', 5, 2);                  // required
            $table->decimal('bonus', 10, 2)->nullable();
            $table->decimal('cash_advance', 10, 2)->nullable();

            // Computed result
            $table->decimal('final_amount', 10, 2)->nullable();

            // Stored PDF path
            $table->string('pdf_path')->nullable();

            $table->timestamps();

            // One calculation per driver per week
            $table->unique(['driver_id', 'week'], 'calculations_driver_week_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};