<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professional_developments', function (Blueprint $table) {
            // Rename the column
            $table->renameColumn('date', 'publish_date');
        });
        // Make the column nullable in a separate step for compatibility
        Schema::table('professional_developments', function (Blueprint $table) {
            $table->date('publish_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('professional_developments', function (Blueprint $table) {
            // Revert the column to non-nullable
            $table->date('publish_date')->nullable(false)->change();
        });
        Schema::table('professional_developments', function (Blueprint $table) {
            // Rename it back
            $table->renameColumn('publish_date', 'date');
        });
    }
};
