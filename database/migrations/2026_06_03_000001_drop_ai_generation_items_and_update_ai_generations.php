<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ai_generation_items');

        Schema::table('ai_generations', function (Blueprint $table) {
            $table->foreignId('raw_tweet_id')->nullable()->after('ai_queue_id')->constrained('raw_tweets')->cascadeOnDelete();
            $table->foreignId('source_account_id')->nullable()->after('raw_tweet_id')->constrained('source_accounts')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('source_account_id')->constrained('source_categories')->nullOnDelete();
            $table->string('provider')->nullable()->after('category_id');

            $table->index(['raw_tweet_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['provider', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_generations', function (Blueprint $table) {
            $table->dropIndex(['provider', 'status']);
            $table->dropIndex(['category_id', 'status']);
            $table->dropIndex(['raw_tweet_id', 'status']);

            $table->dropForeign(['provider']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['source_account_id']);
            $table->dropForeign(['raw_tweet_id']);

            $table->dropColumn('provider');
            $table->dropColumn('category_id');
            $table->dropColumn('source_account_id');
            $table->dropColumn('raw_tweet_id');
        });

        Schema::create('ai_generation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_generation_id')->constrained('ai_generations')->cascadeOnDelete();
            $table->foreignId('raw_tweet_id')->constrained('raw_tweets')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ai_generation_id', 'raw_tweet_id']);
        });
    }
};
