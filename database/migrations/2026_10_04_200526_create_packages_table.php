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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name', 120);
            $table->string('tagline', 160)->nullable();
            $table->string('type_label', 120);
            $table->text('description')->nullable();
            $table->string('billing_unit', 40)->default('month');
            $table->string('tier_column_label', 80)->default('Monthly plan');
            $table->string('price_column_label', 80)->default('LKR');
            $table->string('alt_price_column_label', 80)->nullable();
            $table->unsignedInteger('additional_item_price')->nullable();
            $table->string('additional_item_unit', 60)->nullable();
            $table->json('includes')->nullable();
            $table->json('notes')->nullable();
            $table->boolean('is_featured')->default(false);
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
        Schema::dropIfExists('packages');
    }
};
