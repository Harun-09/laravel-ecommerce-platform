<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $table = DB::getTablePrefix() . 'orders';

        DB::statement("UPDATE `{$table}` SET `status` = 'paid' WHERE `status` = 'confirmed'");
        DB::statement("UPDATE `{$table}` SET `status` = 'shipped' WHERE `status` = 'out_for_delivery'");
        DB::statement("UPDATE `{$table}` SET `status` = 'cancelled' WHERE `status` = 'canceled'");
        DB::statement("UPDATE `{$table}` SET `status` = 'returned' WHERE `status` = 'refunded'");

        DB::statement("
            ALTER TABLE `{$table}`
            MODIFY `status` ENUM(
                'pending',
                'paid',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'returned'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $table = DB::getTablePrefix() . 'orders';

        DB::statement("UPDATE `{$table}` SET `status` = 'confirmed' WHERE `status` = 'paid'");

        DB::statement("
            ALTER TABLE `{$table}`
            MODIFY `status` ENUM(
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'out_for_delivery',
                'delivered',
                'cancelled',
                'returned',
                'refunded'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
