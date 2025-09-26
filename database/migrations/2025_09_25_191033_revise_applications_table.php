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
        Schema::table('applications', function (Blueprint $table) {
            // Drop redundant columns if they exist
            if (Schema::hasColumn('applications', 'applicant_name')) {
                $table->dropColumn('applicant_name');
            }
            if (Schema::hasColumn('applications', 'applicant_curr')) {
                $table->dropColumn('applicant_curr');
            }

            $table->dropForeign(['user_id']);
            $table->dropForeign(['position_id']);

            // Re-add the foreign key constraints.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');

            // Refine the status column
            $table->string('status')->default('Pending Evaluation')->change();

            // Rename final_score to general_score for consistency
            if (Schema::hasColumn('applications', 'final_score')) {
                $table->renameColumn('final_score', 'general_score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'applicant_name')) {
                $table->string('applicant_name');
            }
            if (!Schema::hasColumn('applications', 'applicant_curr')) {
                $table->string('applicant_curr');
            }
        });
    }
};
