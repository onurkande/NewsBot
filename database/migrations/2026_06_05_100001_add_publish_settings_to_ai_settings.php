<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->boolean('auto_publish')->default(false)->after('auto_approve');
            $table->unsignedSmallInteger('publish_delay')->default(0)->after('auto_publish');
        });
    }

    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn(['auto_publish', 'publish_delay']);
        });
    }
};
