<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Drop the old "score" column if it still exists
            if (Schema::hasColumn('evaluations', 'score')) {
                $table->dropColumn('score');
            }

            // Add sub_cat1_score if missing
            if (!Schema::hasColumn('evaluations', 'sub_cat1_score')) {
                $table->decimal('sub_cat1_score', 8, 2)->nullable()->after('type');
            }

            // Add sub_cat2_score if missing
            if (!Schema::hasColumn('evaluations', 'sub_cat2_score')) {
                $table->decimal('sub_cat2_score', 8, 2)->nullable()->after('sub_cat1_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Recreate old score column
            if (!Schema::hasColumn('evaluations', 'score')) {
                $table->decimal('score', 8, 2)->nullable();
            }

            // Drop the new ones
            if (Schema::hasColumn('evaluations', 'sub_cat1_score')) {
                $table->dropColumn('sub_cat1_score');
            }
            if (Schema::hasColumn('evaluations', 'sub_cat2_score')) {
                $table->dropColumn('sub_cat2_score');
            }
        });
    }
};
