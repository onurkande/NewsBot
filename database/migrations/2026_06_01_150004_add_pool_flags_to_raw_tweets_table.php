<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_tweets', function (Blueprint $table) {
            $table->boolean('selected_for_pool')->default(false)->after('is_processed')->comment('Bir kez havuza secildi mi');
            $table->timestamp('selected_at')->nullable()->after('selected_for_pool')->comment('Havuza secilme zamani');
            $table->boolean('selected_for_ai')->default(false)->after('selected_at')->comment('AI asamasina gonderilmek uzere secildi mi');
            $table->timestamp('ai_sent_at')->nullable()->after('selected_for_ai')->comment('AI asamasina gonderilme zamani');

            $table->index(['selected_for_pool', 'tweeted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('raw_tweets', function (Blueprint $table) {
            $table->dropIndex(['selected_for_pool', 'tweeted_at']);
            $table->dropColumn(['selected_for_pool', 'selected_at', 'selected_for_ai', 'ai_sent_at']);
        });
    }
};
