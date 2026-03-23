<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Pothole',
            'Garbage',
            'Broken Light',
            'Flooding',
            'Road Damage',
            'Graffiti',
            'Broken Sidewalk',
            'Other',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
