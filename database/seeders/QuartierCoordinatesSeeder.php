<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Quartier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuartierCoordinatesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordinates = [
            'Bab Lamrissa' => ['latitude' => 34.0331, 'longitude' => -6.8203],
            'Bettana' => ['latitude' => 34.0313, 'longitude' => -6.8108],
            'Tabriquet' => ['latitude' => 34.0555, 'longitude' => -6.7887],
            'Layayda' => ['latitude' => 34.0596, 'longitude' => -6.7698],
            'Hssaine' => ['latitude' => 33.9827, 'longitude' => -6.7422],
        ];

        foreach ($coordinates as $districtName => $coords) {
            $district = District::where('name_fr', $districtName)->first();

            if (!$district) {
                continue;
            }

            // This seeder updates all quartiers in a district to the district's center coordinates
            // as a fallback if specific quartier coordinates are not set.
            Quartier::where('district_id', $district->id)
                ->whereNull('latitude')
                ->update($coords);
        }
    }
}
