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
        // Consolidated into create_market_insight_categories_table.php
        // Schema::table('market_insight_categories', function (Blueprint $table) {
        //     $table->boolean('is_active')->default(true)->after('description');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_market_insight_categories_table.php
        // Schema::table('market_insight_categories', function (Blueprint $table) {
        //     $table->dropColumn('is_active');
        // });
    }
};
