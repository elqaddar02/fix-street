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
        Schema::create('dossier_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained()->cascadeOnDelete();
            $table->string('source', 32)->default('direct'); // flyer placement, from ?s=
            $table->date('date');
            $table->unsignedInteger('count')->default(0);

            // Aggregate counts only: no IP address, user agent or user id is stored.
            $table->unique(['dossier_id', 'source', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_scans');
    }
};
