<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('source_accounts', function (Blueprint $table) {
            $table->unsignedSmallInteger('min_check_interval_minutes')->default(15)->after('check_interval_minutes');
            $table->unsignedSmallInteger('max_check_interval_minutes')->default(15)->after('min_check_interval_minutes');
            $table->unsignedSmallInteger('next_check_interval_minutes')->nullable()->after('max_check_interval_minutes');
            $table->timestamp('next_check_at')->nullable()->after('next_check_interval_minutes');

            $table->index(['is_active', 'next_check_at']);
        });

        DB::table('source_accounts')->update([
            'min_check_interval_minutes' => DB::raw('check_interval_minutes'),
            'max_check_interval_minutes' => DB::raw('check_interval_minutes'),
        ]);
    }

    public function down(): void
    {
        Schema::table('source_accounts', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'next_check_at']);
            $table->dropColumn([
                'min_check_interval_minutes',
                'max_check_interval_minutes',
                'next_check_interval_minutes',
                'next_check_at',
            ]);
        });
    }
};
