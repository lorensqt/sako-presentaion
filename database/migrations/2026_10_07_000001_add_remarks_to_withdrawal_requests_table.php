<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('status');
        });

        // Copy existing transaction_id to remarks if available
        DB::table('withdrawal_requests')
            ->whereNotNull('transaction_id')
            ->whereNull('remarks')
            ->update([
                'remarks' => DB::raw('transaction_id')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
