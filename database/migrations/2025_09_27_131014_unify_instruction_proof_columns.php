<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            // Drop old teaching-effectiveness columns if they still exist
            if (Schema::hasColumn('instructions', 'student_proof_file_id')) {
                $table->dropColumn([
                    'student_proof_file_id',
                    'student_proof_filename',
                    'supervisor_proof_file_id',
                    'supervisor_proof_filename',
                    'academic_period',
                    'student_score',
                    'supervisor_score',
                ]);
            }

            // Add unified columns if missing
            if (!Schema::hasColumn('instructions', 'google_drive_file_id')) {
                $table->string('google_drive_file_id')->nullable()->after('criterion');
            }

            if (!Schema::hasColumn('instructions', 'proof_filename')) {
                $table->string('proof_filename')->nullable()->after('google_drive_file_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            // rollback: re-add teaching-effectiveness columns
            $table->string('student_proof_file_id')->nullable();
            $table->string('student_proof_filename')->nullable();
            $table->string('supervisor_proof_file_id')->nullable();
            $table->string('supervisor_proof_filename')->nullable();
            $table->string('academic_period')->nullable();
            $table->double('student_score')->nullable();
            $table->double('supervisor_score')->nullable();

            $table->dropColumn(['google_drive_file_id', 'proof_filename']);
        });
    }
};
