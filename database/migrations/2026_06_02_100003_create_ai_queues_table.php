<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_batch_id')->constrained('pool_batches')->cascadeOnDelete();
            $table->string('batch_no')->unique();
            $table->unsignedInteger('tweet_count')->default(0);
            $table->decimal('story_score', 8, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_queues');
    }
};
