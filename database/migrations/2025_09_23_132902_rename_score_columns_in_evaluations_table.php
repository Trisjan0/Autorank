<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Drop the original score column if it still exists
            if (Schema::hasColumn('evaluations', 'score')) {
                $table->dropColumn('score');
            }

            // Add the new sub-category score columns
            $table->decimal('sub_cat1_score', 8, 2)->nullable()->after('type'); // For Teaching Effectiveness
            $table->decimal('sub_cat2_score', 8, 2)->nullable()->after('sub_cat1_score'); // For Mentorship
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Add the old column back for rollbacks
            $table->decimal('score', 8, 2)->nullable();

            // Drop the new columns
            $table->dropColumn('sub_cat1_score');
            $table->dropColumn('sub_cat2_score');
        });
    }
};
