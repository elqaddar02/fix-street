<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A city's real boundary, so a pin across the river in Rabat is no longer
 * filed under Salé (finding F-17). Salé's comes from OpenStreetMap; see
 * database/data/README.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->json('boundary')->nullable()->after('longitude');
        });

        DB::table('cities')
            ->where('name', 'Salé')
            ->update(['boundary' => file_get_contents(database_path('data/sale-boundary.geojson'))]);
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('boundary');
        });
    }
};
