<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pool_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no', 32)->unique()->index()->comment('Orn: B-20260601-001');
            $table->unsignedSmallInteger('tweet_window_minutes')->comment('Kullanilan tweet araligi dakika');
            $table->unsignedInteger('candidate_count')->comment('Toplam aday tweet sayisi');
            $table->unsignedInteger('selected_count')->comment('Secilen tweet sayisi');
            $table->unsignedSmallInteger('wait_duration_minutes')->comment('Bir sonraki calismaya kadar bekleme dakika');
            $table->timestamp('next_run_at')->nullable()->comment('Bir sonraki calisma zamani');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['running', 'completed', 'failed'])->default('running');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pool_batches');
    }
};
