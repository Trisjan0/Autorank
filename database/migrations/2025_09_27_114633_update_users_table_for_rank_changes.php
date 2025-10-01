<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop rank column if it exists
            if (Schema::hasColumn('users', 'rank')) {
                $table->dropColumn('rank');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Rename ahp_score to general_score
            if (Schema::hasColumn('users', 'ahp_score')) {
                $table->renameColumn('ahp_score', 'general_score');
            }

            // Faculty rank (add or update default)
            if (!Schema::hasColumn('users', 'faculty_rank')) {
                $table->string('faculty_rank')->default('Unset');
            } else {
                $table->string('faculty_rank')->default('Unset')->change();
            }

            // Add new rank_assigned_at and rank_assigned_by if missing
            if (!Schema::hasColumn('users', 'rank_assigned_at')) {
                $table->timestamp('rank_assigned_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'rank_assigned_by')) {
                $table->string('rank_assigned_by')->default('N/A');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename general_score back to ahp_score
            if (Schema::hasColumn('users', 'general_score')) {
                $table->renameColumn('general_score', 'ahp_score');
            }

            // Recreate rank column
            if (!Schema::hasColumn('users', 'rank')) {
                $table->string('rank')->nullable();
            }

            // Reset faculty_rank to nullable
            if (Schema::hasColumn('users', 'faculty_rank')) {
                $table->string('faculty_rank')->nullable()->change();
            }

            // Drop new columns if they exist
            if (Schema::hasColumn('users', 'rank_assigned_at')) {
                $table->dropColumn('rank_assigned_at');
            }

            if (Schema::hasColumn('users', 'rank_assigned_by')) {
                $table->dropColumn('rank_assigned_by');
            }
        });
    }
};
