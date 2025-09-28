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
        // Consolidated into create_gallery_images_table.php
        // Schema::table('gallery_images', function (Blueprint $table) {
        //     // Drop the old index that includes the location column
        //     $table->dropIndex(['category', 'location']);

        //     // Drop the old location column
        //     $table->dropColumn('location');

        //     // Add new index with location_id
        //     $table->index(['category', 'location_id']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_gallery_images_table.php
        // Schema::table('gallery_images', function (Blueprint $table) {
        //     // Drop the new index
        //     $table->dropIndex(['category', 'location_id']);

        //     // Add back the old location column
        //     $table->string('location')->nullable()->after('category');

        //     // Add back the old index
        //     $table->index(['category', 'location']);
        // });
    }
};
