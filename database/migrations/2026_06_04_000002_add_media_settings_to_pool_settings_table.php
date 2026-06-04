<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pool_settings', function (Blueprint $table) {
            $table->boolean('media_download_enabled')->default(true)->after('is_active')->comment('Havuza secilen tweet medyalarini indir');
            $table->unsignedSmallInteger('published_media_retention_hours')->default(24)->after('media_download_enabled')->comment('Yayinlanmis medya tutma suresi');
            $table->unsignedSmallInteger('unpublished_media_retention_hours')->default(48)->after('published_media_retention_hours')->comment('Yayinlanmamis medya tutma suresi');
        });
    }

    public function down(): void
    {
        Schema::table('pool_settings', function (Blueprint $table) {
            $table->dropColumn(['media_download_enabled', 'published_media_retention_hours', 'unpublished_media_retention_hours']);
        });
    }
};
