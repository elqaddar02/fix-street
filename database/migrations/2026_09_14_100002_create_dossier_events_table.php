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
        Schema::create('dossier_events', function (Blueprint $table) {
            $table->id();
            // Restrict: a case with a recorded timeline must not disappear along with its evidence.
            $table->foreignId('dossier_id')->constrained()->restrictOnDelete();
            $table->string('type', 32); // App\Enums\DossierEventType
            $table->dateTime('occurred_at'); // when it happened in the real world, not when it was entered
            $table->text('description')->nullable();
            $table->string('channel', 32)->nullable(); // e.g. in_person, letter, chikaya, phone, email
            $table->string('reference_number')->nullable();
            $table->string('attachment')->nullable(); // path on the "public" disk: receipt, letter, photo
            $table->boolean('is_public')->default(true);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('corrects_event_id')->nullable()->constrained('dossier_events')->nullOnDelete();

            // Append-only log: created_at only, no updated_at.
            $table->timestamp('created_at')->nullable();

            $table->index(['dossier_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_events');
    }
};
