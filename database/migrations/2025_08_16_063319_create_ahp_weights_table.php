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
        Schema::create('ahp_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criterion_id')->constrained('ahp_criteria')->onDelete('cascade');
            $table->float('weight'); // The normalized AHP weight (0 to 1)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahp_weights');
    }
};
