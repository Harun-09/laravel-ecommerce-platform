<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vendors -> Suppliers
        if (Schema::hasTable('vendors') && !Schema::hasTable('suppliers')) {
            Schema::rename('vendors', 'suppliers');
            Schema::table('suppliers', function (Blueprint $table) {
                if (Schema::hasColumn('suppliers', 'shop_name')) {
                    $table->renameColumn('shop_name', 'company_name');
                }
                if (!Schema::hasColumn('suppliers', 'wallet_balance')) {
                    $table->decimal('wallet_balance', 12, 2)->default(0);
                }
            });
        }

        // Products
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vendor_id')) {
                // To rename a foreign key column safely we must drop the FK first in some DBs, 
                // but Laravel supports renameColumn since 8.x for simple renaming
                $table->renameColumn('vendor_id', 'supplier_id');
            }
            if (Schema::hasColumn('products', 'price')) {
                $table->renameColumn('price', 'base_price');
            }
            if (Schema::hasColumn('products', 'quantity')) {
                $table->renameColumn('quantity', 'stock_quantity');
            }
            if (!Schema::hasColumn('products', 'reserved_quantity')) {
                $table->integer('reserved_quantity')->default(0);
            }
            if (!Schema::hasColumn('products', 'moq')) {
                $table->integer('moq')->default(1);
            }
        });

        // Orders
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->renameColumn('user_id', 'buyer_id');
            }
            if (Schema::hasColumn('orders', 'vendor_id')) {
                $table->renameColumn('vendor_id', 'supplier_id');
            }
            if (!Schema::hasColumn('orders', 'grand_total') && Schema::hasColumn('orders', 'total')) {
                $table->renameColumn('total', 'grand_total');
            }
            if (!Schema::hasColumn('orders', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->index();
            }
        });

        // Order Items
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'vendor_id')) {
                $table->renameColumn('vendor_id', 'supplier_id');
            }
        });
        
        // Cart Items
        if (Schema::hasTable('cart_items')) {
            Schema::table('cart_items', function (Blueprint $table) {
                if (Schema::hasColumn('cart_items', 'vendor_id')) {
                    $table->renameColumn('vendor_id', 'supplier_id');
                }
            });
        }
    }

    public function down(): void
    {
        // Add down logic if necessary
    }
};
