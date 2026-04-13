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
        Schema::table('reports', function (Blueprint $table) {
            // Drop the foreign key constraint for quartier_id
            $table->dropForeign(['quartier_id']);
            // Drop the quartier_id column
            $table->dropColumn('quartier_id');
            // Add the district_id foreign key
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('cascade')->after('city_id');
        });

        // Drop the quartiers table
        Schema::dropIfExists('quartiers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate quartiers table
        Schema::create('quartiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('reports', function (Blueprint $table) {
            // Drop district_id foreign key
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
            // Recreate quartier_id foreign key
            $table->foreignId('quartier_id')->nullable()->after('city_id')->constrained('quartiers')->onDelete('set null');
        });
    }
};
