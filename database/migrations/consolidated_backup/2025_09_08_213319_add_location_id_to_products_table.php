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
        //     $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_products_table.php
        // Schema::table('products', function (Blueprint $table) {
        //     $table->dropForeign(['location_id']);
        //     $table->dropColumn('location_id');
        // });
    }
};
