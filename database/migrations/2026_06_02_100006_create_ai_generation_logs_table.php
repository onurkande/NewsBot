<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_queue_id')->nullable()->constrained('ai_queues')->nullOnDelete();
            $table->foreignId('ai_generation_id')->nullable()->constrained('ai_generations')->nullOnDelete();
            $table->string('level')->default('info');
            $table->text('message');
            $table->json('context_json')->nullable();
            $table->timestamps();

            $table->index(['ai_queue_id', 'level']);
            $table->index(['ai_generation_id', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generation_logs');
    }
};
