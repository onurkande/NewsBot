<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publish_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('daily_post_limit')->default(10);
            $table->unsignedSmallInteger('hourly_post_limit')->default(1);
            $table->unsignedSmallInteger('min_delay_minutes')->default(45);
            $table->unsignedSmallInteger('max_delay_minutes')->default(90);
            $table->unsignedTinyInteger('publish_start_hour')->default(8);
            $table->unsignedTinyInteger('publish_end_hour')->default(23);
            $table->unsignedSmallInteger('min_gap_minutes')->default(3);
            $table->boolean('auto_publish_enabled')->default(false);
            $table->boolean('auto_scale_enabled')->default(false);
            $table->boolean('follower_scale_enabled')->default(false);
            $table->boolean('engagement_scale_enabled')->default(false);
            $table->boolean('warmup_mode_enabled')->default(false);
            $table->unsignedSmallInteger('warmup_0_30_daily_limit')->default(3);
            $table->unsignedSmallInteger('warmup_30_60_daily_limit')->default(5);
            $table->unsignedSmallInteger('warmup_60_plus_daily_limit')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publish_settings');
    }
};
