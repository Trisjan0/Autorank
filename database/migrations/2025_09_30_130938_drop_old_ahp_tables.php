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
        Schema::dropIfExists('positions');
        Schema::dropIfExists('ahp_weights');
        Schema::dropIfExists('ahp_criteria');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('ahp_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->timestamps();
        });

        Schema::create('ahp_weights', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('criterion_id')->nullable();
            $table->double('weight')->nullable();
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->json('requirements')->nullable();
            $table->boolean('is_available')->default(false);
            $table->timestamps();
            $table->integer('available_slots')->default(0);
        });
    }
};
