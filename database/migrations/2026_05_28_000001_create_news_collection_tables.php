<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('source_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('source_categories')->nullOnDelete();
            $table->string('username')->unique();
            $table->string('display_name')->nullable();
            $table->unsignedTinyInteger('priority_score')->default(50);
            $table->unsignedSmallInteger('check_interval_minutes')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->string('last_seen_tweet_id')->nullable();
            $table->unsignedTinyInteger('trust_score')->default(50);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'last_checked_at']);
        });

        Schema::create('raw_tweets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_account_id')->constrained('source_accounts')->cascadeOnDelete();
            $table->string('tweet_id')->unique();
            $table->string('tweet_url')->nullable();
            $table->text('tweet_text');
            $table->json('raw_payload')->nullable();
            $table->timestamp('tweeted_at')->nullable();
            $table->unsignedBigInteger('like_count')->default(0);
            $table->unsignedBigInteger('retweet_count')->default(0);
            $table->unsignedBigInteger('reply_count')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('quote_count')->default(0);
            $table->timestamp('fetched_at')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();

            $table->index(['source_account_id', 'tweeted_at']);
            $table->index('is_processed');
        });

        Schema::create('tweet_normalized_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_tweet_id')->unique()->constrained('raw_tweets')->cascadeOnDelete();
            $table->text('normalized_text');
            $table->string('normalized_hash', 64)->index();
            $table->string('language', 12)->nullable();
            $table->json('tokens')->nullable();
            $table->timestamps();
        });

        Schema::create('duplicate_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_tweet_id')->constrained('raw_tweets')->cascadeOnDelete();
            $table->foreignId('matched_tweet_id')->nullable()->constrained('raw_tweets')->nullOnDelete();
            $table->enum('match_type', ['tweet_id', 'url', 'text', 'none'])->default('none');
            $table->decimal('similarity_score', 5, 2)->default(0);
            $table->boolean('is_duplicate')->default(false);
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->index(['match_type', 'is_duplicate']);
        });

        Schema::create('story_clusters', function (Blueprint $table) {
            $table->id();
            $table->string('cluster_hash', 64)->unique();
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('source_categories')->nullOnDelete();
            $table->foreignId('main_source_account_id')->nullable()->constrained('source_accounts')->nullOnDelete();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->enum('status', ['open', 'selected', 'published', 'ignored'])->default('open');
            $table->decimal('story_score', 8, 2)->default(0);
            $table->unsignedBigInteger('published_post_id')->nullable();
            $table->timestamps();

            $table->index(['status', 'story_score']);
            $table->index('last_updated_at');
        });

        Schema::create('story_cluster_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_cluster_id')->constrained('story_clusters')->cascadeOnDelete();
            $table->foreignId('raw_tweet_id')->unique()->constrained('raw_tweets')->cascadeOnDelete();
            $table->enum('relation_type', ['same_news', 'confirmation', 'alternate_source'])->default('same_news');
            $table->decimal('item_score', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('story_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_cluster_id')->constrained('story_clusters')->cascadeOnDelete();
            $table->decimal('source_trust_score', 8, 2)->default(0);
            $table->decimal('engagement_score', 8, 2)->default(0);
            $table->decimal('recency_score', 8, 2)->default(0);
            $table->decimal('velocity_score', 8, 2)->default(0);
            $table->decimal('duplicate_penalty', 8, 2)->default(0);
            $table->decimal('similarity_bonus', 8, 2)->default(0);
            $table->decimal('final_score', 8, 2)->default(0);
            $table->timestamp('scored_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_cluster_id')->constrained('story_clusters')->cascadeOnDelete();
            $table->text('prompt_text')->nullable();
            $table->longText('ai_response')->nullable();
            $table->string('draft_title')->nullable();
            $table->text('draft_body')->nullable();
            $table->enum('draft_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('published_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_cluster_id')->constrained('story_clusters')->cascadeOnDelete();
            $table->foreignId('ai_draft_id')->nullable()->constrained('ai_drafts')->nullOnDelete();
            $table->string('x_post_id')->nullable()->unique();
            $table->text('posted_text')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->enum('publish_status', ['success', 'failed', 'retry'])->default('retry');
            $table->timestamps();
        });

        Schema::create('publish_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_cluster_id')->constrained('story_clusters')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->string('module')->index();
            $table->text('message');
            $table->json('context_json')->nullable();
            $table->timestamps();
        });

        Schema::create('package_health_logs', function (Blueprint $table) {
            $table->id();
            $table->string('package_name')->index();
            $table->string('status');
            $table->string('version')->nullable();
            $table->timestamp('last_check_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type');
            $table->string('title');
            $table->text('message');
            $table->string('severity')->default('warning');
            $table->boolean('is_sent')->default(false);
            $table->string('sent_via')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('monitoring_checks', function (Blueprint $table) {
            $table->id();
            $table->string('check_name');
            $table->string('status');
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->json('details_json')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_checks');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('package_health_logs');
        Schema::dropIfExists('system_logs');
        Schema::dropIfExists('publish_jobs');
        Schema::dropIfExists('published_posts');
        Schema::dropIfExists('ai_drafts');
        Schema::dropIfExists('story_scores');
        Schema::dropIfExists('story_cluster_items');
        Schema::dropIfExists('story_clusters');
        Schema::dropIfExists('duplicate_checks');
        Schema::dropIfExists('tweet_normalized_texts');
        Schema::dropIfExists('raw_tweets');
        Schema::dropIfExists('source_accounts');
        Schema::dropIfExists('source_categories');
    }
};
