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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name', 120);
            $table->string('summary', 250);
            $table->text('intro')->nullable();
            $table->string('price_prefix', 20)->nullable();
            $table->unsignedInteger('starting_price')->nullable();
            $table->string('starting_unit', 80)->nullable();
            $table->string('starting_label', 80)->nullable();
            $table->json('includes')->nullable();
            $table->json('notes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
