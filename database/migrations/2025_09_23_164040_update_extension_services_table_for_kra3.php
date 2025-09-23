<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extension_services', function (Blueprint $table) {
            // Rename service_type → category if it exists
            if (Schema::hasColumn('extension_services', 'service_type')) {
                $table->renameColumn('service_type', 'category');
            }

            // Add new columns if they don’t already exist
            if (!Schema::hasColumn('extension_services', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('extension_services', 'filename')) {
                $table->string('filename')->nullable();
            }

            if (!Schema::hasColumn('extension_services', 'sub_cat1_score')) {
                $table->decimal('sub_cat1_score', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('extension_services', 'sub_cat2_score')) {
                $table->decimal('sub_cat2_score', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('extension_services', 'sub_cat3_score')) {
                $table->decimal('sub_cat3_score', 8, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('extension_services', function (Blueprint $table) {
            // Rollback: category → service_type
            if (Schema::hasColumn('extension_services', 'category')) {
                $table->renameColumn('category', 'service_type');
            }

            $table->dropColumn([
                'type',
                'filename',
                'sub_cat1_score',
                'sub_cat2_score',
                'sub_cat3_score',
            ]);
        });
    }
};
