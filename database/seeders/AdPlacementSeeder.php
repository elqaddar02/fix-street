<?php

namespace Database\Seeders;

use App\Models\AdPlacement;
use Illuminate\Database\Seeder;

class AdPlacementSeeder extends Seeder
{
    public function run(): void
    {
        $placements = [
            [
                'slug' => 'home_top',
                'name' => 'Home Page - Top Banner',
                'description' => 'Horizontal banner shown near the top of the home page.',
                'type' => 'horizontal',
            ],
        ];

        foreach ($placements as $placement) {
            AdPlacement::firstOrCreate(
                ['slug' => $placement['slug']],
                $placement
            );
        }
    }
}
