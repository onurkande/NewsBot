<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twscrape_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // login, fetch, command, relogin, health_check
            $table->string('username')->nullable();
            $table->text('command')->nullable();
            $table->text('output')->nullable();
            $table->text('error')->nullable();
            $table->boolean('is_successful')->default(true);
            $table->integer('duration_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twscrape_logs');
    }
};
