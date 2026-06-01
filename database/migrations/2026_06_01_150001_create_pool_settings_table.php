<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pool_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tweet_window_min')->default(10)->comment('Tweet toplama penceresi minimum dakika');
            $table->unsignedSmallInteger('tweet_window_max')->default(40)->comment('Tweet toplama penceresi maksimum dakika');
            $table->unsignedSmallInteger('selection_interval_min')->default(10)->comment('Secim calisma araligi minimum dakika');
            $table->unsignedSmallInteger('selection_interval_max')->default(15)->comment('Secim calisma araligi maksimum dakika');
            $table->unsignedTinyInteger('tweet_count_min')->default(3)->comment('Secilecek minimum tweet sayisi');
            $table->unsignedTinyInteger('tweet_count_max')->default(5)->comment('Secilecek maksimum tweet sayisi');
            $table->boolean('is_active')->default(true)->comment('Havuz secimi aktif mi');
            $table->timestamp('next_run_at')->nullable()->comment('Bir sonraki calisma zamani');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pool_settings');
    }
};
