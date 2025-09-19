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
        Schema::table('professional_developments', function (Blueprint $table) {
            // Add the new column for the Google Drive file ID
            $table->string('google_drive_file_id')->nullable()->after('date');

            // Make the old file_path column nullable
            $table->string('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_developments', function (Blueprint $table) {
            // Drop the new column if we roll back
            $table->dropColumn('google_drive_file_id');

            // Revert the file_path column
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
