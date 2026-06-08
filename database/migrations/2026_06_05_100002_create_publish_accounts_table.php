<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publish_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('display_name')->nullable();
            $table->unsignedInteger('followers_count')->default(0);
            $table->unsignedInteger('following_count')->default(0);
            $table->unsignedInteger('statuses_count')->default(0);
            $table->string('profile_image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->text('auth_token')->nullable();
            $table->text('ct0')->nullable();
            $table->json('cookies_json')->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publish_accounts');
    }
};
