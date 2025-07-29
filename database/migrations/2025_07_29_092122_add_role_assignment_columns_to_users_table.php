<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add 'role_assigned_at' column
            $table->timestamp('role_assigned_at')->nullable()->after('updated_at');

            // Add 'role_assigned_by' column
            $table->string('role_assigned_by')->nullable()->after('role_assigned_at');
        });

        // This will update ALL existing users with the default values.
        DB::table('users')->update([
            'role_assigned_at' => Carbon::now(),
            'role_assigned_by' => 'System Default'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role_assigned_at', 'role_assigned_by']);
        });
    }
};
