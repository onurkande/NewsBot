<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('source_categories', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('source_categories', function (Blueprint $table) {
            $table->dropUnique('source_categories_slug_unique');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::table('source_categories', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->unique('slug');
            $table->dropSoftDeletes();
        });
    }
};
