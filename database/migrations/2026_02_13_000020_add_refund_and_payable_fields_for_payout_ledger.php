<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'refunded_amount')) {
                    $table->decimal('refunded_amount', 12, 2)->default(0);
                }
            });
        }

        if (Schema::hasTable('vendor_payout_items')) {
            Schema::table('vendor_payout_items', function (Blueprint $table) {
                if (!Schema::hasColumn('vendor_payout_items', 'refund_amount')) {
                    $table->decimal('refund_amount', 12, 2)->default(0);
                }

                if (!Schema::hasColumn('vendor_payout_items', 'payable_amount')) {
                    $table->decimal('payable_amount', 12, 2)->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendor_payout_items')) {
            Schema::table('vendor_payout_items', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_payout_items', 'refund_amount')) {
                    $table->dropColumn('refund_amount');
                }

                if (Schema::hasColumn('vendor_payout_items', 'payable_amount')) {
                    $table->dropColumn('payable_amount');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'refunded_amount')) {
                    $table->dropColumn('refunded_amount');
                }
            });
        }
    }
};

