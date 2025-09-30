<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Faculty ranks
        Schema::create('faculty_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('rank_name', 255);
            $table->timestamps();
        });

        // KRA weights per rank
        Schema::create('faculty_rank_kra_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_rank_id')->constrained()->cascadeOnDelete();
            $table->string('kra_name', 100); // Instruction, Research, Extension, Professional Dev
            $table->decimal('weight', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculty_rank_kra_weights');
        Schema::dropIfExists('faculty_ranks');
    }
};
