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
        //     if (!Schema::hasColumn('market_insights', 'status')) {
        //         $table->enum('status', ['draft', 'published'])->default('draft');
        //     }
        //     if (!Schema::hasColumn('market_insights', 'published_at')) {
        //         $table->timestamp('published_at')->nullable();
        //     }

        //     $table->index(['status', 'published_at']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_market_insights_table.php
        // Schema::table('market_insights', function (Blueprint $table) {
        //     $table->dropIndex(['status', 'published_at']);
        //     if (Schema::hasColumn('market_insights', 'published_at')) {
        //         $table->dropColumn('published_at');
        //     }
        //     if (Schema::hasColumn('market_insights', 'status')) {
        //         $table->dropColumn('status');
        //     }
        // });
    }
};
