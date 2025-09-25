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
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('extension_services');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('professional_developments');
        Schema::dropIfExists('promotion_applications');
        Schema::dropIfExists('research_documents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // evaluations
        Schema::create('evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->string('category', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->decimal('sub_cat1_score', 8, 2)->nullable();
            $table->decimal('sub_cat2_score', 8, 2)->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('google_drive_file_id', 255)->nullable();
            $table->string('filename', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->timestamps();
        });

        // extension_services
        Schema::create('extension_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->string('category', 255);
            $table->string('google_drive_file_id', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->timestamps();
            $table->string('type', 255)->nullable();
            $table->string('filename', 255)->nullable();
            $table->decimal('sub_cat1_score', 8, 2)->nullable();
            $table->decimal('sub_cat2_score', 8, 2)->nullable();
            $table->decimal('sub_cat3_score', 8, 2)->nullable();
        });

        // materials
        Schema::create('materials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->string('type', 255)->nullable();
            $table->decimal('sub_cat1_score', 8, 2)->nullable();
            $table->string('category', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('google_drive_file_id', 255)->nullable();
            $table->string('filename', 255)->nullable();
            $table->timestamps();
        });

        // professional_developments
        Schema::create('professional_developments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 255);
            $table->string('category', 255);
            $table->string('type', 255)->nullable();
            $table->date('publish_date')->nullable();
            $table->string('google_drive_file_id', 255)->nullable();
            $table->string('filename', 255)->nullable();
            $table->integer('sub_cat1_score')->nullable();
            $table->integer('sub_cat2_score')->nullable();
            $table->integer('sub_cat3_score')->nullable();
            $table->string('link', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->timestamps();
        });

        // promotion_applications
        Schema::create('promotion_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->double('ahp_score');
            $table->string('eligible_rank', 255);
            $table->string('status', 255)->default('pending');
            $table->timestamps();
        });

        // research_documents
        Schema::create('research_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('google_drive_file_id', 255)->nullable();
            $table->string('title', 255);
            $table->dateTime('publish_date')->nullable();
            $table->string('type', 255)->nullable();
            $table->string('category', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->timestamps();
            $table->string('filename', 255)->nullable();
            $table->decimal('sub_cat1_score', 8, 2)->nullable();
            $table->decimal('sub_cat2_score', 8, 2)->nullable();
            $table->decimal('sub_cat3_score', 8, 2)->nullable();
            $table->decimal('sub_cat4_score', 8, 2)->nullable();
        });
    }
};
