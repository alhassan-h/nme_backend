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
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Unique identifier for the profile field
            $table->text('value')->nullable(); // The actual data
            $table->enum('type', ['string', 'json', 'integer', 'boolean', 'float', 'image'])->default('string'); // Data type
            $table->string('description')->nullable(); // Optional description
            $table->boolean('is_public')->default(true); // Whether this field is publicly visible
            $table->integer('sort_order')->default(0); // For ordering fields
            $table->timestamps();

            $table->index(['key', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
