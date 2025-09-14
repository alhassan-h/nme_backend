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
        Schema::table('newsletters', function (Blueprint $table) {
            // Change the enum to include 'sending' status
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            // Revert back to the original enum values
            $table->enum('status', ['draft', 'scheduled', 'sent'])->default('draft')->change();
        });
    }
};