<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('gpt4free');
            $table->text('api_key')->nullable();
            $table->string('model_name')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(3);
            $table->unsignedInteger('timeout')->default(300);
            $table->unsignedTinyInteger('concurrent_jobs')->default(1);
            $table->foreignId('active_prompt_id')->nullable()->constrained('prompts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
