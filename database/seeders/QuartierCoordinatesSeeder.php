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
            'Bab Lamrissa' => ['latitude' => 34.028, 'longitude' => -6.824],
            'Bettana' => ['latitude' => 34.022, 'longitude' => -6.811],
            'Tabriquet' => ['latitude' => 34.041, 'longitude' => -6.805],
            'Layayda' => ['latitude' => 34.062, 'longitude' => -6.791],
            'Hssaine' => ['latitude' => 34.004, 'longitude' => -6.748],
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
