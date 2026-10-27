<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Salé's centre point was seeded in the Bouregreg river mouth. Choosing Salé
 * in the report form drops the pin there, so it moves to the medina.
 * Only rows still holding the old seeded value are touched.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('cities')
            ->where('name', 'Salé')
            ->where('latitude', 34.0333)
            ->where('longitude', -6.8333)
            ->update(['latitude' => 34.0386, 'longitude' => -6.8159]);
    }

    public function down(): void
    {
        DB::table('cities')
            ->where('name', 'Salé')
            ->where('latitude', 34.0386)
            ->where('longitude', -6.8159)
            ->update(['latitude' => 34.0333, 'longitude' => -6.8333]);
    }
};
