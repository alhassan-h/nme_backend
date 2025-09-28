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
        // Consolidated into create_users_table.php
        // Schema::table('users', function (Blueprint $table) {
        //     $table->timestamp('last_login_at')->nullable()->after('status');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_users_table.php
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('last_login_at');
        // });
    }
};
