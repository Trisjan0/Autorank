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
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('criterion');

            // Common Fields
            $table->string('filename')->nullable();
            $table->string('google_drive_file_id')->nullable();

            // Fields for Teaching Effectiveness (Criterion A) -- NOT NEEDED FOR CCE
            // $table->string('academic_period')->nullable();
            // $table->float('student_score')->nullable();
            // $table->float('supervisor_score')->nullable();

            // Fields for Instructional Materials (Criterion B)
            $table->string('title')->nullable();
            $table->string('category')->nullable();
            $table->string('type')->nullable();
            $table->string('role')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('level_of_use')->nullable();

            // Fields for Mentorship Services (Criterion C)
            $table->string('service_type')->nullable();
            $table->string('student_or_competition')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('level')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructions');
    }
};
