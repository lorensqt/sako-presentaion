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
        // 1. Add hrmd_sequence to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('hrmd_sequence')->nullable()->after('role');
        });

        // 2. Add current_hrmd_sequence to loan_applications table
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->unsignedInteger('current_hrmd_sequence')->nullable()->after('current_stage');
        });

        // 3. Add hrmd_sequence to loan_approvals table
        Schema::table('loan_approvals', function (Blueprint $table) {
            $table->unsignedInteger('hrmd_sequence')->nullable()->after('stage_role_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_approvals', function (Blueprint $table) {
            $table->dropColumn('hrmd_sequence');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn('current_hrmd_sequence');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('hrmd_sequence');
        });
    }
};
