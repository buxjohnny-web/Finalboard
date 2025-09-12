<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'default_percentage')) {
                $table->decimal('default_percentage', 5, 2)->nullable()->after('ssn');
            }
            if (!Schema::hasColumn('drivers', 'default_rental_price')) {
                $table->decimal('default_rental_price', 10, 2)->nullable()->after('default_percentage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'default_percentage')) {
                $table->dropColumn('default_percentage');
            }
            if (Schema::hasColumn('drivers', 'default_rental_price')) {
                $table->dropColumn('default_rental_price');
            }
        });
    }
};