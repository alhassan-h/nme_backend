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
        //     // Drop the old string columns
        //     $table->dropColumn(['unit', 'location']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_products_table.php
        // Schema::table('products', function (Blueprint $table) {
        //     // Add back the old string columns
        //     $table->string('unit')->nullable();
        //     $table->string('location')->nullable();
        // });
    }
};
