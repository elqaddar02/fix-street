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
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // e.g. "home_top", used in <x-ad-banner placement="...">
            $table->string('name'); // admin-facing label
            $table->text('description')->nullable();
            $table->enum('type', ['horizontal', 'square'])->default('horizontal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_placements');
    }
};
