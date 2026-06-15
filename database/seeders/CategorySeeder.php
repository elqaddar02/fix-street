<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pothole', 'name_ar' => 'حفرة'],
            ['name' => 'Garbage', 'name_ar' => 'قمامة'],
            ['name' => 'Broken Light', 'name_ar' => 'إضاءة مكسورة'],
            ['name' => 'Flooding', 'name_ar' => 'فيضان'],
            ['name' => 'Road Damage', 'name_ar' => 'تلف الطريق'],
            ['name' => 'Graffiti', 'name_ar' => 'كتابة على الجدران'],
            ['name' => 'Broken Sidewalk', 'name_ar' => 'رصيف مكسور'],
            ['name' => 'Other', 'name_ar' => 'أخرى'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                ['name_ar' => $categoryData['name_ar']]
            );
        }
    }
}
