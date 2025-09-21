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
        Schema::table('credentials', function (Blueprint $table) {
            // Rename the existing 'name' column to 'title'
            $table->renameColumn('name', 'title');

            // Add the new columns
            $table->string('filename')->after('name'); // 'name' is now 'title'
            $table->string('google_drive_file_id')->after('filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            // Reverse the changes in the opposite order
            $table->dropColumn('google_drive_file_id');
            $table->dropColumn('filename');
            $table->renameColumn('title', 'name');
        });
    }
};
