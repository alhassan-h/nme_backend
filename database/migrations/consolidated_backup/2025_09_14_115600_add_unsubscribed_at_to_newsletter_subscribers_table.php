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
        // Consolidated into create_newsletter_subscribers_table.php
        // Schema::table('newsletter_subscribers', function (Blueprint $table) {
        //     $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_newsletter_subscribers_table.php
        // Schema::table('newsletter_subscribers', function (Blueprint $table) {
        //     $table->dropColumn('unsubscribed_at');
        // });
    }
};