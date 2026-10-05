<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('facebook_page_id')->nullable()->unique();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('url', 2048)->nullable();
            $table->string('category', 100)->nullable()->index();
            $table->boolean('enabled')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_pages');
    }
};
