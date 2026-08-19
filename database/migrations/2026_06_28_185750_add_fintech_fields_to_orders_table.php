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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_term')) $table->string('payment_term')->default('cash'); // cash, net30, net60
            if (!Schema::hasColumn('orders', 'due_date')) $table->dateTime('due_date')->nullable();
            if (!Schema::hasColumn('orders', 'escrow_status')) $table->string('escrow_status')->default('held'); // held, released, refunded
            if (!Schema::hasColumn('orders', 'delivery_status')) $table->string('delivery_status')->default('pending'); // pending, shipped, delivered
            if (!Schema::hasColumn('orders', 'commission_amount')) $table->decimal('commission_amount', 12, 2)->default(0);
            if (!Schema::hasColumn('orders', 'late_fee_amount')) $table->decimal('late_fee_amount', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_term', 'due_date', 'escrow_status', 'delivery_status', 'commission_amount', 'late_fee_amount']);
        });
    }
};
