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
        Schema::table('research_documents', function (Blueprint $table) {
            // Add file_path if it doesn't exist
            if (!Schema::hasColumn('research_documents', 'file_path')) {
                $table->string('file_path')->nullable()->after('publish_date');
            }

            if (!Schema::hasColumn('research_documents', 'google_drive_file_id')) {
                $table->string('google_drive_file_id')->nullable()->after('file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_documents', function (Blueprint $table) {
            if (Schema::hasColumn('research_documents', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('research_documents', 'google_drive_file_id')) {
                $table->dropColumn('google_drive_file_id');
            }
        });
    }
};
