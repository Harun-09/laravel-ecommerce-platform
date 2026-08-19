<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vendor_payout_items')) {
            return;
        }

        Schema::table('vendor_payout_items', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_payout_items', 'posted_at')) {
                $table->timestamp('posted_at')->nullable();
            }

            if (!Schema::hasColumn('vendor_payout_items', 'posted_by')) {
                $table->foreignId('posted_by')
                    ->nullable()
                    
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // Backfill historical completed payout items as already posted.
        $rows = DB::table('vendor_payout_items as items')
            ->join('vendor_payouts as payouts', 'payouts.id', '=', 'items.vendor_payout_id')
            ->where('payouts.status', 'completed')
            ->whereNull('items.posted_at')
            ->select([
                'items.id as item_id',
                'payouts.processed_at as processed_at',
                'payouts.updated_at as updated_at',
                'payouts.created_at as created_at',
                'payouts.processed_by as processed_by',
            ])
            ->get();

        foreach ($rows as $row) {
            $postedAt = $row->processed_at ?? $row->updated_at ?? $row->created_at;

            DB::table('vendor_payout_items')
                ->where('id', (int) $row->item_id)
                ->update([
                    'posted_at' => $postedAt,
                    'posted_by' => $row->processed_by ? (int) $row->processed_by : null,
                ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('vendor_payout_items')) {
            return;
        }

        Schema::table('vendor_payout_items', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_payout_items', 'posted_by')) {
                $table->dropConstrainedForeignId('posted_by');
            }

            if (Schema::hasColumn('vendor_payout_items', 'posted_at')) {
                $table->dropColumn('posted_at');
            }
        });
    }
};

