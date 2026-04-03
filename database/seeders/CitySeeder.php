<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Rabat', 'name_ar' => 'الرباط', 'active' => true],
            ['name' => 'Salé', 'name_ar' => 'سلا', 'active' => true],
            ['name' => 'Casablanca', 'name_ar' => 'الدار البيضاء', 'active' => true],
            ['name' => 'Marrakech', 'name_ar' => 'مراكش', 'active' => true],
            ['name' => 'Fès', 'name_ar' => 'فاس', 'active' => true],
            ['name' => 'Tanger', 'name_ar' => 'طنجة', 'active' => true],
            ['name' => 'Agadir', 'name_ar' => 'أكادير', 'active' => true],
            ['name' => 'Meknès', 'name_ar' => 'مكناس', 'active' => true],
            ['name' => 'Oujda', 'name_ar' => 'وجدة', 'active' => true],
            ['name' => 'Kénitra', 'name_ar' => 'القنيطرة', 'active' => true],
            ['name' => 'Tétouan', 'name_ar' => 'تطوان', 'active' => true],
            ['name' => 'Safi', 'name_ar' => 'آسفي', 'active' => true],
            ['name' => 'El Jadida', 'name_ar' => 'الجديدة', 'active' => true],
            ['name' => 'Béni Mellal', 'name_ar' => 'بني ملال', 'active' => true],
            ['name' => 'Nador', 'name_ar' => 'الناظور', 'active' => true],
        ];

        foreach ($cities as $cityData) {
            City::firstOrCreate(
                ['name' => $cityData['name']],
                $cityData
            );
        }
    }
}
