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
        // Consolidated into create_newsletters_table.php
        // Schema::table('newsletters', function (Blueprint $table) {
        //     $table->enum('status', ['draft', 'scheduled', 'sent'])->default('draft')->after('html_content');
        //     $table->timestamp('sent_at')->nullable()->after('status');
        //     $table->timestamp('scheduled_for')->nullable()->after('sent_at');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_newsletters_table.php
        // Schema::table('newsletters', function (Blueprint $table) {
        //     $table->dropColumn(['status', 'sent_at', 'scheduled_for']);
        // });
    }
};
