<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twscrape_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->boolean('is_active')->default(false);
            $table->integer('weight')->default(50);
            $table->integer('total_usage')->default(0);
            $table->integer('error_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->text('last_error_message')->nullable();
            $table->string('status')->default('active'); // active, suspended, locked
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twscrape_accounts');
    }
};
