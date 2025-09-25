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
        Schema::table('instructions', function (Blueprint $table) {
            $table->renameColumn('filename', 'student_proof_filename');
            $table->renameColumn('google_drive_file_id', 'student_proof_file_id');

            $table->string('supervisor_proof_filename')->nullable()->after('student_proof_file_id');
            $table->string('supervisor_proof_file_id')->nullable()->after('supervisor_proof_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            //
        });
    }
};
