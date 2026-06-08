<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publish_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('publish_expiration_hours')->default(6)->after('warmup_60_plus_daily_limit');
        });
    }

    public function down(): void
    {
        Schema::table('publish_settings', function (Blueprint $table) {
            $table->dropColumn('publish_expiration_hours');
        });
    }
};
