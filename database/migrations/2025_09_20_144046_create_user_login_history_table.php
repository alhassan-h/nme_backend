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
        Schema::create('user_login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('login_at');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable(); // web, mobile, api
            $table->string('browser')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('location')->nullable(); // Could be derived from IP
            $table->boolean('successful')->default(true);
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'login_at']);
            $table->index('ip_address');
            $table->index('device_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_history');
    }
};
