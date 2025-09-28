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
        // Consolidated into create_products_table.php
        // Schema::table('products', function (Blueprint $table) {
        //     $table->string('min_order')->nullable();
        //     $table->json('specifications')->nullable();
        //     $table->boolean('featured')->default(false);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_products_table.php
        // Schema::table('products', function (Blueprint $table) {
        //     $table->dropColumn(['min_order', 'specifications', 'featured']);
        // });
    }
};
