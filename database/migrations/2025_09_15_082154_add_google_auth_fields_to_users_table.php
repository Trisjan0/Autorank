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
        Schema::table('users', function (Blueprint $table) {
            // Make google_id nullable in case users sign up with email/password
            $table->string('google_id')->nullable()->change();
            $table->text('google_token')->nullable(); // To store the access token
            $table->text('google_refresh_token')->nullable(); // To store the refresh token
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable(false)->change();
            $table->dropColumn(['google_token', 'google_refresh_token']);
        });
    }
};
