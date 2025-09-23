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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Core Foreign Keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');

            // Snapshot of Applicant's Info at Time of Application
            $table->string('applicant_name');
            $table->string('applicant_current_rank');

            // Application Status
            $table->string('status')->default('submitted'); // e.g., submitted, under_review, approved, rejected

            // KRA Scores (to be filled by Evaluators)
            $table->decimal('kra1_score', 8, 2)->nullable();
            $table->decimal('kra2_score', 8, 2)->nullable();
            $table->decimal('kra3_score', 8, 2)->nullable();
            $table->decimal('kra4_score', 8, 2)->nullable();

            // Final Score (to be calculated by Admin)
            $table->decimal('final_score', 8, 2)->nullable();

            // Evaluator/Admin Remarks
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
