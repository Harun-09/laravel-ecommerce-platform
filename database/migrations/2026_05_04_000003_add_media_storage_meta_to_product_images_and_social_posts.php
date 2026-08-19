<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table): void {
            if (Schema::hasColumn('product_images', 'path')) {
                $table->json('storage_meta')->nullable();
            } else {
                $table->json('storage_meta')->nullable();
            }
        });

        Schema::table('social_posts', function (Blueprint $table): void {
            $table->json('media_meta')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $table): void {
            $table->dropColumn('media_meta');
        });

        Schema::table('product_images', function (Blueprint $table): void {
            $table->dropColumn('storage_meta');
        });
    }
};
