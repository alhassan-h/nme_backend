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
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category', 'location']);
            $table->foreignId('mineral_category_id')->nullable()->constrained('mineral_categories')->onDelete('set null');
            $table->index('mineral_category_id');
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['mineral_category_id']);
            $table->dropColumn('mineral_category_id');
            $table->string('category')->nullable();
            $table->index(['category', 'location']);
        });
    }
};
