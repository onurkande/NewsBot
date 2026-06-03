<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_generation_id')->constrained('ai_generations')->cascadeOnDelete();
            $table->foreignId('raw_tweet_id')->constrained('raw_tweets')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ai_generation_id', 'raw_tweet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generation_items');
    }
};
