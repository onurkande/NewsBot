<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cluster_scan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_cluster_id')->nullable()->constrained('story_clusters')->nullOnDelete();
            $table->foreignId('source_account_id')->nullable()->constrained('source_accounts')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable();
            $table->unsignedInteger('previous_scan_elapsed_minutes')->nullable();
            $table->unsignedInteger('fetched_tweet_count')->default(0);
            $table->unsignedInteger('new_tweet_count')->default(0);
            $table->unsignedInteger('processed_tweet_count')->default(0);
            $table->unsignedInteger('skipped_tweet_count')->default(0);
            $table->string('last_tweet_id')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('status')->default('success');
            $table->boolean('has_error')->default(false);
            $table->text('error_message')->nullable();
            $table->unsignedSmallInteger('next_check_interval_minutes')->nullable();
            $table->timestamp('next_check_at')->nullable();
            $table->timestamps();

            $table->index(['story_cluster_id', 'scanned_at']);
            $table->index(['source_account_id', 'scanned_at']);
            $table->index(['status', 'has_error']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cluster_scan_histories');
    }
};
