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
        // Consolidated into create_forum_posts_table.php
        // Schema::table('forum_posts', function (Blueprint $table) {
        //     $table->dropColumn('replies_count');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_forum_posts_table.php
        // Schema::table('forum_posts', function (Blueprint $table) {
        //     $table->unsignedInteger('replies_count')->default(0);
        // });
    }
};
