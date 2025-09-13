<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // This adds the new 'publish_date' column.
            // It's nullable so existing records don't cause errors.
            $table->timestamp('publish_date')->nullable()->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn('publish_date');
        });
    }
};
