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
        Schema::create('market_insights', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->boolean('featured')->default(false);
            $table->string('price_trend')->nullable();
            $table->string('market_volume')->nullable();
            $table->string('investor_confidence')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('tags')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('market_insight_categories')->onDelete('set null');
            $table->timestamps();

            $table->index('category_id');
            $table->index(['status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_insights');
    }
};
