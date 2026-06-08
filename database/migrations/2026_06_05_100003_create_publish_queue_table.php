<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publish_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_generation_id')->constrained('ai_generations')->cascadeOnDelete();
            $table->foreignId('publish_account_id')->nullable()->constrained('publish_accounts')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->string('tweet_id_x')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index(['ai_generation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publish_queue');
    }
};
