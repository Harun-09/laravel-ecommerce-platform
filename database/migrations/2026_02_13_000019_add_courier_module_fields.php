<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('shipping_zones')) {
            Schema::table('shipping_zones', function (Blueprint $table) {
                if (!Schema::hasColumn('shipping_zones', 'code')) {
                    $table->string('code')->nullable();
                }
            });

            DB::table('shipping_zones')
                ->whereNull('code')
                ->whereRaw('LOWER(name) LIKE ?', ['%inside dhaka%'])
                ->update(['code' => 'inside_dhaka']);

            DB::table('shipping_zones')
                ->whereNull('code')
                ->whereRaw('LOWER(name) LIKE ?', ['%outside dhaka%'])
                ->update(['code' => 'outside_dhaka']);
        }

        if (Schema::hasTable('shipping_methods')) {
            Schema::table('shipping_methods', function (Blueprint $table) {
                if (!Schema::hasColumn('shipping_methods', 'cod_fee')) {
                    $table->decimal('cod_fee', 10, 2)->default(0);
                }

                if (!Schema::hasColumn('shipping_methods', 'is_cod_available')) {
                    $table->boolean('is_cod_available')->default(true);
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'delivery_zone')) {
                    $table->string('delivery_zone')->nullable();
                }

                if (!Schema::hasColumn('orders', 'cod_fee')) {
                    $table->decimal('cod_fee', 10, 2)->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'delivery_zone')) {
                    $table->dropColumn('delivery_zone');
                }

                if (Schema::hasColumn('orders', 'cod_fee')) {
                    $table->dropColumn('cod_fee');
                }
            });
        }

        if (Schema::hasTable('shipping_methods')) {
            Schema::table('shipping_methods', function (Blueprint $table) {
                if (Schema::hasColumn('shipping_methods', 'cod_fee')) {
                    $table->dropColumn('cod_fee');
                }

                if (Schema::hasColumn('shipping_methods', 'is_cod_available')) {
                    $table->dropColumn('is_cod_available');
                }
            });
        }

        if (Schema::hasTable('shipping_zones')) {
            Schema::table('shipping_zones', function (Blueprint $table) {
                if (Schema::hasColumn('shipping_zones', 'code')) {
                    $table->dropColumn('code');
                }
            });
        }
    }
};
