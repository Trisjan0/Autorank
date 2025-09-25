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
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('criterion'); // 'service-community', 'extension-involvement', 'admin-designation'

            // Common Fields for All Criteria
            $table->string('title'); // Holds Service Title, Program Title, or Designation
            $table->string('google_drive_file_id')->nullable();
            $table->string('filename')->nullable();

            // Fields used by multiple criteria
            $table->string('role')->nullable(); // For Service & Extension Program
            $table->date('start_date')->nullable(); // For Service, Extension Program, & Designation
            $table->date('end_date')->nullable(); // For Service, Extension Program, & Designation

            // Fields for "Service to the Institution/Community"
            $table->string('category')->nullable(); // e.g., Community Service, Institutional Service
            $table->string('target_community')->nullable();

            // Fields for "Extension Program/Project Involvement"
            $table->string('funding_source')->nullable();

            // Fields for "Administrative Designation"
            $table->string('office_unit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extensions');
    }
};
