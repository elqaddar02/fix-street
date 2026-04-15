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
        // Create quartiers table
        Schema::create('quartiers', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr');
            $table->string('name_ar')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Add quartier_id to reports table
        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('quartier_id')->nullable()->constrained()->onDelete('cascade')->after('district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['quartier_id']);
            $table->dropColumn('quartier_id');
        });

        Schema::dropIfExists('quartiers');
    }
};
