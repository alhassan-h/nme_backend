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
        //     $table->string('status')->default('active')->after('verified');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_users_table.php
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('status');
        // });
    }
};
