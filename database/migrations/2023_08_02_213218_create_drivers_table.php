<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->string('full_name'); // Full name
            $table->string('phone_number'); // Phone number
            $table->string('driver_id')->unique(); // Driver ID
            $table->string('license_number'); // License number
            $table->string('ssn'); // SSN
            $table->unsignedBigInteger('added_by'); // Added by (user ID)
            $table->boolean('active')->default(true); // Active status
            $table->timestamps(); // Created at and updated at

            // Foreign key for added_by (assuming 'users' table exists)
            $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}