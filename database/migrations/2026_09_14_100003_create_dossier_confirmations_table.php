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
        Schema::create('dossier_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            // Deleted with the account: people can remove their data, and counts follow.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 16); // App\Enums\ConfirmationType
            $table->string('photo')->nullable(); // path on the "public" disk
            $table->text('note')->nullable();
            $table->boolean('cosign_consent')->default(false); // agreed to be named on the printed complaint
            $table->timestamp('hidden_at')->nullable(); // hidden by moderation, never deleted
            $table->timestamps();

            $table->index(['dossier_id', 'type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_confirmations');
    }
};
