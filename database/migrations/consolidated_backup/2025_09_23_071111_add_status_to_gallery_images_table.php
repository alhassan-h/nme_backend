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
        //     $table->enum('status', ['published', 'pending', 'unpublished', 'hidden'])->default('pending')->after('description');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consolidated into create_gallery_images_table.php
        // Schema::table('gallery_images', function (Blueprint $table) {
        //     $table->dropColumn('status');
        // });
    }
};
