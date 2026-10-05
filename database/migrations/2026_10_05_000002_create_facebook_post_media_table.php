<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_post_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('facebook_post_id')->constrained('facebook_posts')->cascadeOnDelete();
            $table->string('media_type', 30);
            $table->string('media_url', 2048);
            $table->string('thumbnail_url', 2048)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_post_media');
    }
};
