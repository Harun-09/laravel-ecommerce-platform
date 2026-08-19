<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type', 32)->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'employees')) {
                $table->string('employees', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 120)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'account_type',
                'company_name',
                'job_title',
                'phone',
                'employees',
                'country',
            ]);
        });
    }
};
