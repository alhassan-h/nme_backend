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
        //     $table->dropIndex(['category', 'featured']);
        //     $table->dropColumn('category');
        //     $table->foreignId('category_id')->nullable()->constrained('market_insight_categories')->onDelete('set null');
        //     $table->index('category_id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_market_insights_table.php
        // Schema::table('market_insights', function (Blueprint $table) {
        //     $table->dropIndex(['category_id']);
        //     $table->dropForeign(['category_id']);
        //     $table->dropColumn('category_id');
        //     $table->string('category')->nullable();
        //     $table->index(['category', 'featured']);
        // });
    }
};
