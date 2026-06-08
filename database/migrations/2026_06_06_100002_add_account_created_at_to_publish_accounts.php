<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publish_accounts', function (Blueprint $table) {
            $table->timestamp('account_created_at')->nullable()->after('last_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('publish_accounts', function (Blueprint $table) {
            $table->dropColumn('account_created_at');
        });
    }
};
