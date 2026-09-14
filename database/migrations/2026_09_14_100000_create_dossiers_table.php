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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // public page: /cases/{slug}
            $table->string('short_code', 16)->unique(); // printed QR link: /c/{short_code}
            $table->string('title');
            $table->text('summary')->nullable();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quartier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('authority_name')->nullable();
            $table->string('cover_image')->nullable(); // path on the "public" disk

            // Plain string validated by App\Enums\DossierStage, so adding a stage later
            // needs no column change. Only App\Services\DossierTimeline changes it.
            $table->string('stage', 32)->default('collecting')->index();

            $table->timestamp('published_at')->nullable(); // null = draft, not publicly visible
            $table->string('contribution_strength', 16)->nullable(); // filled when the case closes
            $table->text('contribution_notes')->nullable();
            $table->json('scorecard_targets')->nullable(); // thresholds agreed before launch
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
