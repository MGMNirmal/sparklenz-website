<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 180)->unique();
            $table->string('client_label', 120)->nullable();
            $table->string('category', 120);
            $table->string('summary', 400);
            $table->text('description')->nullable();
            $table->string('media_type', 20)->default('video');
            $table->string('cover_path')->nullable();
            $table->string('cover_alt', 250)->nullable();
            $table->unsignedSmallInteger('cover_width')->nullable();
            $table->unsignedSmallInteger('cover_height')->nullable();
            $table->string('youtube_id', 11)->nullable();
            $table->string('video_orientation', 12)->default('landscape');
            $table->string('teaser_path')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_demo')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 12)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
