<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('research_documents', function (Blueprint $table) {
            $table->string('filename')->nullable();
            $table->decimal('sub_cat1_score', 8, 2)->nullable();
            $table->decimal('sub_cat2_score', 8, 2)->nullable();
            $table->decimal('sub_cat3_score', 8, 2)->nullable();
            $table->decimal('sub_cat4_score', 8, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('research_documents', function (Blueprint $table) {
            $table->dropColumn(['filename', 'sub_cat1_score', 'sub_cat2_score', 'sub_cat3_score', 'sub_cat4_score']);
        });
    }
};
