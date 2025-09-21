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
        // This line will drop the table.
        Schema::dropIfExists('phone_verifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is the original schema for rolling back.
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone_number', 20);
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }
};
