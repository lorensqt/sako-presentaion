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
        // 1. Create loans (definitions/products) table
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // travel, commodity, regular, special, seasonal, bonus_buyout, emergency, health, upcoming
            $table->string('type_key'); // travel_loan, appliance_gadget, maxi, etc.
            $table->string('name');
            $table->string('partner')->nullable();
            $table->string('loanable_amount')->nullable();
            $table->decimal('fixed_deposit', 12, 2)->default(0);
            $table->json('comakers')->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0.00);
            $table->integer('max_term_months')->nullable();
            $table->integer('minimum_membership_months')->nullable();
            $table->boolean('hrmd_approval')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('approval_flow')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 2. Create loan_applications table
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->nullable()->constrained('loans')->onDelete('set null');

            $table->string('loan_category');
            $table->string('loan_type');

            // Financial calculations
            $table->decimal('requested_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('term_months')->nullable();
            $table->decimal('total_interest', 12, 2)->nullable();
            $table->decimal('total_payable', 12, 2)->nullable();
            $table->decimal('monthly_amortization', 12, 2)->nullable();
            $table->decimal('service_charge', 12, 2)->default(0.00);
            $table->decimal('net_proceeds', 12, 2)->nullable();

            // Approval flow status
            $table->string('current_stage')->default('sako_staff');
            $table->string('status', 50)->default('pending');

            // Disbursement dates
            $table->date('release_date')->nullable();
            $table->date('maturity_date')->nullable();

            $table->json('form_data')->nullable();
            $table->string('ledger_path')->nullable();
            $table->string('schedule_path')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 3. Create loan_comakers table for relational tracking
        Schema::create('loan_comakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
        });

        // 4. Create loan_approvals table for stage-by-stage auditing
        Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->string('stage_role_slug');
            $table->foreignId('actioned_by_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('decision', ['approved', 'rejected'])->default('approved');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 5. Create loan_activities table for timeline history
        Schema::create('loan_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action');
            $table->text('description');
            $table->timestamps();
        });

        // 6. Create loan_documents table for compliance attachments
        Schema::create('loan_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained('loan_applications')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->default('application/pdf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_documents');
        Schema::dropIfExists('loan_activities');
        Schema::dropIfExists('loan_approvals');
        Schema::dropIfExists('loan_comakers');
        Schema::dropIfExists('loan_applications');
        Schema::dropIfExists('loans');
    }
};
