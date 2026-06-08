<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publish_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publish_queue_id')->constrained('publish_queue')->cascadeOnDelete();
            $table->foreignId('ai_generation_id')->constrained('ai_generations')->cascadeOnDelete();
            $table->foreignId('publish_account_id')->nullable()->constrained('publish_accounts')->nullOnDelete();
            $table->string('status');
            $table->string('tweet_id_x')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['ai_generation_id', 'status']);
            $table->index(['publish_account_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publish_logs');
    }
};
