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
        Schema::table('professional_developments', function (Blueprint $table) {
            $table->decimal('score', 8, 2)->nullable()->after('filename');
            $table->string('status')->default('For Submission')->after('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_developments', function (Blueprint $table) {
            $table->dropColumn(['score', 'status']);
        });
    }
};
