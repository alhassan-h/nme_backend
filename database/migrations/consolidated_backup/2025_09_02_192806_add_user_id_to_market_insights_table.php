<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Consolidated into create_market_insights_table.php
        // if (!Schema::hasColumn('market_insights', 'user_id')) {
        //     Schema::table('market_insights', function (Blueprint $table) {
        //         $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     });

        //     // Set a default user_id for existing records (assuming user with id 1 exists)
        //     DB::table('market_insights')->update(['user_id' => 1]);
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_market_insights_table.php
        // Schema::table('market_insights', function (Blueprint $table) {
        //     $table->dropForeign(['user_id']);
        //     $table->dropColumn('user_id');
        // });
    }
};
