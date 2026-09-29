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
        // 1. Elections table
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('upcoming'); // upcoming, active, completed
            $table->timestamps();
        });

        // 2. Positions table
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 3. Candidates table
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });

        // 4. Votes table
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to prevent duplicate voting by the same user for a specific position in an election
            $table->unique(['election_id', 'position_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('elections');
    }
};
