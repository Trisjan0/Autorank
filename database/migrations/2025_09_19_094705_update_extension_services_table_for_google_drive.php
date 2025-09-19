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
        Schema::table('extension_services', function (Blueprint $table) {
            $table->string('type')->after('title');
            $table->string('google_drive_file_id')->nullable()->after('date');

            $table->string('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extension_services', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['type', 'google_drive_file_id']);

            // Revert file_path to not be nullable
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
