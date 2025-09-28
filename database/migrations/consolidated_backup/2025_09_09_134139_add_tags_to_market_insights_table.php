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
        // Consolidated into create_market_insights_table.php
        // Schema::table('market_insights', function (Blueprint $table) {
        //     $table->json('tags')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_market_insights_table.php
        // Schema::table('market_insights', function (Blueprint $table) {
        //     $table->dropColumn(['tags']);
        // });
    }
};
