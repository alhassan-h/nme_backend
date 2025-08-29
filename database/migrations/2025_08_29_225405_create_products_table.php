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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->string('unit');
            $table->string('location');
            $table->json('images');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'pending', 'sold'])->default('pending');
            $table->integer('views')->default(0);
            $table->timestamps();

            $table->index(['category', 'location']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
