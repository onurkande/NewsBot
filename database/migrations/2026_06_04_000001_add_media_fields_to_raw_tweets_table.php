<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_tweets', function (Blueprint $table) {
            $table->json('media_urls')->nullable()->after('raw_payload')->comment('Tweet medya URL listesi');
            $table->unsignedTinyInteger('media_count')->default(0)->after('media_urls');
            $table->string('media_type', 20)->nullable()->after('media_count')->comment('photo, video, animated_gif, mixed');
            $table->timestamp('media_downloaded_at')->nullable()->after('media_type');
            $table->json('media_paths')->nullable()->after('media_downloaded_at')->comment('Lokal depolama yolları');

            $table->index(['media_downloaded_at']);
        });
    }

    public function down(): void
    {
        Schema::table('raw_tweets', function (Blueprint $table) {
            $table->dropIndex(['media_downloaded_at']);
            $table->dropColumn(['media_urls', 'media_count', 'media_type', 'media_downloaded_at', 'media_paths']);
        });
    }
};
