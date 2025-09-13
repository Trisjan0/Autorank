<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Change the column type from TIMESTAMP to DATETIME
            $table->dateTime('publish_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Defines how to reverse the change
            $table->timestamp('publish_date')->nullable()->change();
        });
    }
};
