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
        Schema::create('application_table', function (Blueprint $table) {
            $table->id();
            $table->string('instructor_name');
            $table->string('current_rank');
            $table->string('aimed_rank');
            $table->float('estimated_score')->nullable();
            $table->string('evaluation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_table');
    }
};
