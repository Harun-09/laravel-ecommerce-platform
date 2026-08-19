<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_event_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('provider', 40)->default('system');
            $table->string('payment_method', 40)->nullable();
            $table->string('event_type', 120);
            $table->string('status', 40)->nullable();
            $table->string('severity', 20)->default('info');
            $table->boolean('is_retry')->default(false);
            $table->string('message', 255)->nullable();
            $table->json('context')->nullable();
            $table->timestamp('happened_at')->useCurrent();
            $table->timestamps();

            $table->index(['provider', 'event_type']);
            $table->index(['status', 'severity', 'happened_at']);
            $table->index(['order_id', 'happened_at']);
            $table->index(['payment_id', 'happened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_event_logs');
    }
};
