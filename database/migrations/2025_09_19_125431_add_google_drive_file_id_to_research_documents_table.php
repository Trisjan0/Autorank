<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research_documents', function (Blueprint $table) {
            $table->dateTime('publish_date')->nullable()->after('title');

            $table->string('google_drive_file_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('research_documents', function (Blueprint $table) {
            $table->dropColumn(['publish_date', 'google_drive_file_id']);
        });
    }
};
