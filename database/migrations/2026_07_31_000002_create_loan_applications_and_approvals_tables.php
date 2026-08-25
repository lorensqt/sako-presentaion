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
            $table->string('category'); // e.g., 'travel', 'commodity', 'regular', 'special', 'seasonal', 'bonus_buyout', 'emergency', 'health', 'upcoming'
            $table->string('type_key'); // e.g., 'travel_loan', 'appliance_gadget', 'maxi'
            $table->string('name'); // e.g., 'Travel Loan'
            $table->string('partner')->nullable(); // For partner info if text, or JSON
            $table->string('loanable_amount')->nullable(); // e.g., '100000' or '80% of Share Capital'
            $table->decimal('fixed_deposit', 12, 2)->default(0);
            $table->json('comakers')->nullable(); // can store integer (e.g. 4) or array (e.g. {"<=2000": 0, ">2000": 1})
            $table->decimal('interest_rate', 5, 2)->default(0.00); // flat rate in %
            $table->integer('max_term_months')->nullable();
            $table->integer('minimum_membership_months')->nullable();
            $table->boolean('hrmd_approval')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // for remaining dynamic config attributes
            $table->timestamps();
        });

        // 2. Create updated loan_applications table
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->nullable()->constrained('loans')->onDelete('set null');
            
            $table->string('loan_category'); // e.g., 'regular', 'commodity'
            $table->string('loan_type');     // e.g., 'maxi', 'appliance_gadget'
            
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
            $table->string('current_stage')->default('sako_staff'); // comakers, sako_staff, etc.
            $table->enum('status', ['pending', 'approved', 'released', 'rejected', 'cancelled'])->default('pending');
            
            // Disbursement dates
            $table->date('release_date')->nullable();
            $table->date('maturity_date')->nullable();
            
            $table->json('form_data')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // 3. Create loan_comakers table for relational tracking
        Schema::create('loan_comakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The co-maker member
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
        });

        // 4. Create loan_approvals table for stage-by-stage auditing
        Schema::create('loan_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->constrained()->onDelete('cascade');
            $table->string('stage_role_slug'); // e.g., 'credit_committee'
            $table->foreignId('actioned_by_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('decision', ['approved', 'rejected'])->default('approved');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_approvals');
        Schema::dropIfExists('loan_comakers');
        Schema::dropIfExists('loan_applications');
        Schema::dropIfExists('loans');
    }
};
