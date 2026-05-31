<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('source_accounts', function (Blueprint $table) {
            $table->boolean('limited_initial_fetch_pending')
                ->default(true)
                ->after('last_seen_tweet_id');
        });
    }

    public function down(): void
    {
        Schema::table('source_accounts', function (Blueprint $table) {
            $table->dropColumn('limited_initial_fetch_pending');
        });
    }
};
