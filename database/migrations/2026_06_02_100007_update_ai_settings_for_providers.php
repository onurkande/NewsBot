<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->text('opencode_api_key')->nullable()->after('concurrent_jobs');
            $table->string('opencode_base_url')->nullable()->default('https://opencode.ai/zen/go/v1')->after('opencode_api_key');
            $table->string('opencode_model')->nullable()->default('deepseek-v4-flash')->after('opencode_base_url');
        });
    }

    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn([
                'opencode_api_key',
                'opencode_base_url',
                'opencode_model',
            ]);
        });
    }
};
