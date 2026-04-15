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

        $this->call([
            CategorySeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            QuartierSeeder::class,
            QuartierCoordinatesSeeder::class,
        ]);
    }
}
