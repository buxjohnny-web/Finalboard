<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            if (!Schema::hasColumn('calculations', 'week')) {
                $table->string('week')->after('driver_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            if (Schema::hasColumn('calculations', 'week')) {
                $table->dropColumn('week');
            }
        });
    }
};