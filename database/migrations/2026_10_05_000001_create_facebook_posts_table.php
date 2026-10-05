<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('facebook_page_id')->constrained('facebook_pages')->cascadeOnDelete();
            $table->string('facebook_post_id')->nullable()->unique();
            $table->text('message')->nullable();
            $table->string('permalink', 2048)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_posts');
    }
};
