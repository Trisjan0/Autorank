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
        Schema::create('application_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');

            // These two columns create the "polymorphic" relationship,
            // allows link to records in different tables (instructions, researches, etc.)
            $table->unsignedBigInteger('submission_id');
            $table->string('submission_type');

            $table->timestamps();

            // Add an index for faster queries
            $table->index(['submission_id', 'submission_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_submissions');
    }
};
