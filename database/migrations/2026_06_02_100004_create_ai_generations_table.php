<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_queue_id')->constrained('ai_queues')->cascadeOnDelete();
            $table->foreignId('prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->string('model')->nullable();
            $table->unsignedSmallInteger('prompt_version')->default(1);
            $table->string('title')->nullable();
            $table->json('input')->nullable();
            $table->text('prompt')->nullable();
            $table->text('full_prompt')->nullable();
            $table->longText('ai_response')->nullable();
            $table->text('generated_news')->nullable();
            $table->json('token_usage')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->string('status')->default('draft');
            $table->text('error')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generations');
    }
};
