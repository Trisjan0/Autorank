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
        // Drop the credentials table
        Schema::dropIfExists('credentials');

        // Drop selected columns from instructions table
        Schema::table('instructions', function (Blueprint $table) {
            $columns = [
                'student_proof_filename',
                'student_proof_file_id',
                'supervisor_proof_filename',
                'supervisor_proof_file_id',
                'academic_period',
                'student_score',
                'supervisor_score',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('instructions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the credentials table
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('title', 255);
            $table->string('type', 255)->nullable();
            $table->string('filename', 255);
            $table->string('google_drive_f', 255)->nullable();
            $table->timestamps();
        });

        // Add back the dropped columns in instructions table
        Schema::table('instructions', function (Blueprint $table) {
            $table->string('student_proof_filename', 255)->nullable();
            $table->string('student_proof_file_id', 255)->nullable();
            $table->string('supervisor_proof_filename', 255)->nullable();
            $table->string('supervisor_proof_file_id', 255)->nullable();
            $table->string('academic_period', 255)->nullable();
            $table->double('student_score')->nullable();
            $table->double('supervisor_score')->nullable();
        });
    }
};
