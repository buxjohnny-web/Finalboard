<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backup your DB before running this.

        // Drop existing foreign key (name comes from your dump: drivers_added_by_foreign)
        DB::statement('ALTER TABLE `drivers` DROP FOREIGN KEY `drivers_added_by_foreign`');

        // Make columns nullable using raw ALTER statements (no doctrine/dbal required)
        DB::statement(<<<'SQL'
ALTER TABLE `drivers`
  MODIFY `phone_number` VARCHAR(255) NULL,
  MODIFY `license_number` VARCHAR(255) NULL,
  MODIFY `ssn` VARCHAR(255) NULL,
  MODIFY `added_by` BIGINT UNSIGNED NULL,
  MODIFY `active` TINYINT(1) NULL DEFAULT '1'
SQL
        );

        // Recreate foreign key with ON DELETE SET NULL so added_by can be null safely
        DB::statement('ALTER TABLE `drivers` ADD CONSTRAINT `drivers_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop modified foreign key
        DB::statement('ALTER TABLE `drivers` DROP FOREIGN KEY `drivers_added_by_foreign`');

        // Revert columns to NOT NULL and restore previous FK behaviour (ON DELETE CASCADE)
        DB::statement(<<<'SQL'
ALTER TABLE `drivers`
  MODIFY `phone_number` VARCHAR(255) NOT NULL,
  MODIFY `license_number` VARCHAR(255) NOT NULL,
  MODIFY `ssn` VARCHAR(255) NOT NULL,
  MODIFY `added_by` BIGINT UNSIGNED NOT NULL,
  MODIFY `active` TINYINT(1) NOT NULL DEFAULT '1'
SQL
        );

        // Recreate original foreign key with ON DELETE CASCADE
        DB::statement('ALTER TABLE `drivers` ADD CONSTRAINT `drivers_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE');
    }
};