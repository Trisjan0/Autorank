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
        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('criterion'); // e.g., 'research-outputs', 'inventions-creative-works'

            // Common Fields
            $table->string('title');
            $table->string('role');
            $table->string('google_drive_file_id')->nullable();
            $table->string('filename')->nullable();

            // Fields for "Research Outputs Published"
            $table->string('category')->nullable(); // Journal Article, Book, etc.
            $table->string('journal_name')->nullable();
            $table->string('indexing')->nullable(); // Scopus, WoS, etc.
            $table->string('doi')->nullable();
            $table->date('publication_date')->nullable();

            // Fields for "Inventions, Innovation, & Creative Works"
            $table->string('type')->nullable(); // Invention, Creative Work, etc.
            $table->string('sub_type')->nullable(); // Patent, Utility Model, Exhibition, etc.
            $table->string('status_level')->nullable(); // National, International, Patented, etc.
            $table->date('exhibition_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
