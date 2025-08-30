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
            $table->unsignedBigInteger('calculation_id');
            $table->unsignedBigInteger('user_id'); // who made the change
            $table->decimal('total_invoice', 10, 2)->nullable();
            $table->integer('parcel_rows_count')->nullable();
            $table->decimal('vehicule_rental_price', 10, 2)->nullable();
            $table->decimal('broker_percentage', 5, 2);
            $table->decimal('bonus', 10, 2)->nullable();
            $table->decimal('cash_advance', 10, 2)->nullable();
            $table->decimal('final_amount', 10, 2)->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('action')->default('update'); // or 'create', 'delete', etc.
            $table->timestamps();

            $table->foreign('calculation_id')->references('id')->on('calculations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_logs');
    }
};