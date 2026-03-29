<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuartierSeeder extends Seeder
{
    public function run(): void
    {
        $sale = \App\Models\City::where('name', 'Salé')->first();

        if ($sale) {
            $quartiers = [
                'Hay Essalam',
                'Tabriquet',
                'Bettana',
                'Lakhmis',
                'Sidi Bouknadel',
                'Centre Ville',
                'Bab Lamrissa',
                'Hay Rahma',
                'Hay Al Amal',
                'Hay Al Qods',
            ];

            foreach ($quartiers as $name) {
                \App\Models\Quartier::firstOrCreate([
                    'name' => $name,
                    'city_id' => $sale->id,
                ]);
            }
        }
    }
}
