<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professional_developments', function (Blueprint $table) {
            $table->string('type')->nullable()->after('category');
            $table->string('filename')->nullable()->after('google_drive_file_id');

            // Subcategory scoress
            $table->unsignedInteger('sub_cat1_score')->nullable()->after('filename');
            $table->unsignedInteger('sub_cat2_score')->nullable()->after('sub_cat1_score');
            $table->unsignedInteger('sub_cat3_score')->nullable()->after('sub_cat2_score');
        });
    }

    public function down(): void
    {
        Schema::table('professional_developments', function (Blueprint $table) {
            $table->dropColumn(['type', 'filename', 'sub_cat1_score', 'sub_cat2_score', 'sub_cat3_score']);
        });
    }
};
