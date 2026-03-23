<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'is_admin' => true, 'password' => bcrypt('password')]
        );

        foreach (['Road Damage', 'Broken Light', 'Illegal Dumping', 'Flooding'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        foreach (['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta'] as $name) {
            City::firstOrCreate(['name' => $name]);
        }

        Report::firstOrCreate(
            ['title' => 'Pothole on Main Road', 'user_id' => $user->id],
            [
                'description' => 'Large pothole causing traffic and safety issues.',
                'status' => 'OPEN',
                'category_id' => Category::first()->id,
                'city_id' => City::first()->id,
            ]
        );
    }
}
