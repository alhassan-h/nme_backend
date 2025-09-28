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
        //     $table->dropColumn('name');
        //     $table->string('first_name')->nullable()->after('id');
        //     $table->string('last_name')->nullable()->after('first_name');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_users_table.php
        // Schema::table('users', function (Blueprint $table) {
        //     $table->string('name')->after('id');
        //     $table->dropColumn(['first_name', 'last_name']);
        // });
    }
};
