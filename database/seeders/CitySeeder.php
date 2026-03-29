<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Casablanca', 'active' => false],
            ['name' => 'Rabat', 'active' => false],
            ['name' => 'Marrakech', 'active' => false],
            ['name' => 'Fes', 'active' => false],
            ['name' => 'Tangier', 'active' => false],
            ['name' => 'Agadir', 'active' => false],
            ['name' => 'Meknes', 'active' => false],
            ['name' => 'Oujda', 'active' => false],
            ['name' => 'Kenitra', 'active' => false],
            ['name' => 'Tetouan', 'active' => false],
            ['name' => 'Salé', 'active' => true],
        ];

        foreach ($cities as $cityData) {
            City::firstOrCreate(
                ['name' => $cityData['name']],
                ['active' => $cityData['active']]
            );
        }
    }
}
