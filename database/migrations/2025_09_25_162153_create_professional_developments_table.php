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
        Schema::create('professional_developments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('criterion'); // 'prof-organizations', 'prof-training', 'prof-awards'

            // Common Fields
            $table->string('title'); // Holds Organization Name, Training Title, or Award Title
            $table->string('google_drive_file_id')->nullable();
            $table->string('filename')->nullable();

            // Fields for "Involvement in Professional Organizations"
            $table->string('membership_type')->nullable(); // e.g., Member, Life Member, Fellow
            $table->string('role')->nullable(); // Role if an officer
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable(); // Also used for Training Completion & Date Awarded

            // Fields for "Continuing Professional Education & Training"
            $table->string('type')->nullable(); // e.g., Post-Doctoral, Master's Degree, Training
            $table->string('organizer')->nullable();
            $table->integer('hours')->nullable();
            $table->string('level')->nullable(); // Also used for Award Level

            // Fields for "Awards and Recognitions"
            $table->string('awarding_body')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_developments');
    }
};
