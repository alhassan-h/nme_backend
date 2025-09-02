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
        Schema::table('market_insights', function (Blueprint $table) {
            $table->string('price_trend')->nullable();
            $table->string('market_volume')->nullable();
            $table->string('investor_confidence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_insights', function (Blueprint $table) {
            $table->dropColumn(['price_trend', 'market_volume', 'investor_confidence']);
        });
    }
};
