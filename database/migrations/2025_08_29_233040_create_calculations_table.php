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

            // One calculation per driver per week (int week number)
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->integer('week_number');

            // Parsed from PDF
            $table->decimal('total_invoice', 10, 2)->nullable();
            $table->integer('parcel_rows_count')->nullable();

            // Inputs (only broker_percentage required)
            $table->decimal('vehicule_rental_price', 10, 2)->nullable();
            $table->decimal('broker_percentage', 5, 2);
            $table->decimal('bonus', 10, 2)->nullable();
            $table->decimal('cash_advance', 10, 2)->nullable();

            // Computed result
            $table->decimal('final_amount', 10, 2)->nullable();

            // Uploaded PDF path
            $table->string('pdf_path')->nullable();

            $table->timestamps();

            $table->unique(['driver_id', 'week_number'], 'calculations_driver_week_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};