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
        Schema::table('evaluations', function (Blueprint $table) {
            // Add the new column to store the Google Drive file ID.
            $table->string('google_drive_file_id')->nullable()->after('file_path');

            // Make the old file_path column nullable, as new uploads won't use it.
            $table->string('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Drop the new column if we roll back.
            $table->dropColumn('google_drive_file_id');

            // Revert the file_path column back to its original state.
            // Note: Adjust '->nullable(false)' if your original column could be null.
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
