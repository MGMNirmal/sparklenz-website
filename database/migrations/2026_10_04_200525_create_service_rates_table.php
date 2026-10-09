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
        Schema::create('service_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('group_label', 120)->nullable();
            $table->string('label', 160);
            $table->string('detail', 200)->nullable();
            $table->string('price_prefix', 20)->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('previous_price')->nullable();
            $table->string('unit', 60)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_rates');
    }
};
