<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Zinc Concentrate product
        $zincCategoryId = DB::table('mineral_categories')->where('name', 'Zinc')->value('id');
        if ($zincCategoryId) {
            DB::table('products')
                ->where('title', 'Zinc Concentrate - Premium Quality')
                ->whereNull('mineral_category_id')
                ->update(['mineral_category_id' => $zincCategoryId]);
        }

        // Update Uranium Ore product
        $uraniumCategoryId = DB::table('mineral_categories')->where('name', 'Uranium')->value('id');
        if ($uraniumCategoryId) {
            DB::table('products')
                ->where('title', 'Uranium Ore - Low Grade')
                ->whereNull('mineral_category_id')
                ->update(['mineral_category_id' => $uraniumCategoryId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove mineral_category_id from the updated products
        DB::table('products')
            ->where('title', 'Zinc Concentrate - Premium Quality')
            ->update(['mineral_category_id' => null]);

        DB::table('products')
            ->where('title', 'Uranium Ore - Low Grade')
            ->update(['mineral_category_id' => null]);
    }
};
