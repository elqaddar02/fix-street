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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_placement_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('image')->nullable(); // path on the "public" disk, same convention as reports.image
            $table->string('target_url');

            // "house" = our own banner (image + target_url above). Other providers
            // (e.g. "adsense") render a third-party unit via external_slot_id instead.
            $table->string('provider')->default('house');
            $table->string('external_slot_id')->nullable();

            $table->boolean('is_active')->default(true); // manual on/off switch
            $table->timestamp('starts_at')->nullable(); // scheduling window; both null = always eligible
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('weight')->default(1); // relative pick weight when a placement has multiple active ads

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['ad_placement_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
