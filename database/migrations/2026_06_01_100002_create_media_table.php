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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'youtube', 'vimeo', 'photo', etc.
            $table->string('url')->nullable(); // For external video URLs
            $table->string('path')->nullable(); // For local file paths
            $table->string('title')->nullable(); // Caption/Title
            $table->integer('sort_order')->default(0); // To sort media elements
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
