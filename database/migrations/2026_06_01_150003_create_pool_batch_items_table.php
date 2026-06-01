<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pool_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_batch_id')->constrained('pool_batches')->cascadeOnDelete();
            $table->foreignId('raw_tweet_id')->constrained('raw_tweets')->cascadeOnDelete();
            $table->decimal('priority_score', 8, 2)->default(0)->comment('Kaynak oncelik puani (0-100)');
            $table->decimal('engagement_score', 8, 2)->default(0)->comment('Etkilesim puani (0-100)');
            $table->decimal('final_score', 8, 2)->default(0)->comment('Final hesaplanmis puan');
            $table->boolean('is_selected')->default(false)->comment('Bu tweet secildi mi');
            $table->unsignedInteger('rank')->default(0)->comment('Sirada kacinci');
            $table->timestamps();

            $table->index(['pool_batch_id', 'is_selected']);
            $table->index(['pool_batch_id', 'final_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pool_batch_items');
    }
};
