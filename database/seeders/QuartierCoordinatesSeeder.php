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
            'Bab Lamrissa' => ['latitude' => 34.0519, 'longitude' => -6.7937],
            'Bettana' => ['latitude' => 34.0495, 'longitude' => -6.8125],
            'Tabriquet' => ['latitude' => 34.0642, 'longitude' => -6.8098],
            'Layayda' => ['latitude' => 34.0832, 'longitude' => -6.8247],
            'Hssaine' => ['latitude' => 34.0405, 'longitude' => -6.8234],
        ];

        foreach ($coordinates as $districtName => $coords) {
            $district = District::where('name_fr', $districtName)->first();

            if (!$district) {
                continue;
            }

            Quartier::where('district_id', $district->id)
                ->update($coords);
        }
    }
}

