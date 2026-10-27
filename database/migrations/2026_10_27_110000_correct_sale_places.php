<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrects Salé's district and quartier points, approved on 2026-10-27
 * (finding F-17, docs/testing/arabic-e2e-results.md). Several sat on the
 * Rabat bank of the Bouregreg, outside Salé's boundary. New positions come
 * from OpenStreetMap (neighbourhood places, district boundaries, tram stops).
 *
 * A point only moves if it still holds its original seeded value, so a
 * correction made in the admin since is never overwritten.
 */
return new class extends Migration
{
    /** name => [seeded lat, seeded lng, new lat, new lng] */
    private const DISTRICTS = [
        'Bab Lamrissa' => [34.028, -6.824, 34.0331, -6.8203],
        'Bettana' => [34.022, -6.811, 34.0313, -6.8108],
        'Tabriquet' => [34.041, -6.805, 34.0555, -6.7887],
        'Layayda' => [34.062, -6.791, 34.0596, -6.7698],
        'Hssaine' => [34.004, -6.748, 33.9827, -6.7422],
    ];

    private const QUARTIERS = [
        'La Marina' => [34.0255, -6.8235, 34.0326, -6.8290],
        'Hay Moulay Ismaïl' => [34.0195, -6.8120, 34.0270, -6.7817],
        'Hay Es-Salam' => [34.0430, -6.8010, 34.0342, -6.7933],
        'Hay Karima' => [34.0480, -6.7950, 34.0541, -6.7944],
        'Sala Al Jadida' => [33.9880, -6.7450, 33.9990, -6.7440],
        'Technopolis' => [33.9760, -6.7300, 33.9906, -6.7194],
    ];

    /** Not Salé quartiers: Rabat's administrative quarter, and the separate town of Sidi Bouknadel. */
    private const REMOVE = ['Quartier Administratif', 'Sidi Bouknadel'];

    public function up(): void
    {
        $this->move('districts', 'lat', 'lng', self::DISTRICTS, forward: true);
        $this->move('quartiers', 'latitude', 'longitude', self::QUARTIERS, forward: true);

        foreach (DB::table('quartiers')->whereIn('name_fr', self::REMOVE)->pluck('id') as $id) {
            $inUse = DB::table('reports')->where('quartier_id', $id)->exists()
                || DB::table('dossiers')->where('quartier_id', $id)->exists();

            // Never strip a quartier from existing reports: clear its point instead,
            // so it stops attracting new ones.
            $inUse
                ? DB::table('quartiers')->where('id', $id)->update(['latitude' => null, 'longitude' => null])
                : DB::table('quartiers')->where('id', $id)->delete();
        }
    }

    public function down(): void
    {
        $this->move('districts', 'lat', 'lng', self::DISTRICTS, forward: false);
        $this->move('quartiers', 'latitude', 'longitude', self::QUARTIERS, forward: false);
        // Removed quartiers are not recreated: the QuartierSeeder no longer lists them.
    }

    private function move(string $table, string $latColumn, string $lngColumn, array $points, bool $forward): void
    {
        foreach ($points as $name => [$oldLat, $oldLng, $newLat, $newLng]) {
            [$fromLat, $fromLng, $toLat, $toLng] = $forward
                ? [$oldLat, $oldLng, $newLat, $newLng]
                : [$newLat, $newLng, $oldLat, $oldLng];

            DB::table($table)
                ->where('name_fr', $name)
                ->where($latColumn, $fromLat)
                ->where($lngColumn, $fromLng)
                ->update([$latColumn => $toLat, $lngColumn => $toLng]);
        }
    }
};
