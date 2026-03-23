<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Casablanca',
            'Rabat',
            'Marrakech',
            'Fes',
            'Tangier',
            'Agadir',
            'Meknes',
            'Oujda',
            'Kenitra',
            'Tetouan',
        ];

        foreach ($cities as $name) {
            City::firstOrCreate(['name' => $name]);
        }
    }
}
