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
        Schema::create('page_copies', function (Blueprint $table) {
            $table->id();
            $table->string('page', 40);
            $table->string('key', 80);
            $table->string('label', 120);
            $table->string('type', 20)->default('text');
            $table->text('content')->nullable();
            $table->unsignedSmallInteger('max_length')->default(300);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_copies');
    }
};
