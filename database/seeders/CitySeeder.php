<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Rabat', 'name_ar' => 'الرباط', 'active' => true, 'latitude' => 34.0209, 'longitude' => -6.8416],
            ['name' => 'Salé', 'name_ar' => 'سلا', 'active' => true, 'latitude' => 34.0333, 'longitude' => -6.8333],
            ['name' => 'Casablanca', 'name_ar' => 'الدار البيضاء', 'active' => true, 'latitude' => 33.5731, 'longitude' => -7.5898],
            ['name' => 'Marrakech', 'name_ar' => 'مراكش', 'active' => true, 'latitude' => 31.6295, 'longitude' => -7.9811],
            ['name' => 'Fès', 'name_ar' => 'فاس', 'active' => true, 'latitude' => 34.0181, 'longitude' => -5.0078],
            ['name' => 'Tanger', 'name_ar' => 'طنجة', 'active' => true, 'latitude' => 35.7595, 'longitude' => -5.8340],
            ['name' => 'Agadir', 'name_ar' => 'أكادير', 'active' => true, 'latitude' => 30.4278, 'longitude' => -9.5981],
            ['name' => 'Meknès', 'name_ar' => 'مكناس', 'active' => true, 'latitude' => 33.8935, 'longitude' => -5.5473],
            ['name' => 'Oujda', 'name_ar' => 'وجدة', 'active' => true, 'latitude' => 34.6814, 'longitude' => -1.9075],
            ['name' => 'Kénitra', 'name_ar' => 'القنيطرة', 'active' => true, 'latitude' => 34.2570, 'longitude' => -6.5890],
            ['name' => 'Tétouan', 'name_ar' => 'تطوان', 'active' => true, 'latitude' => 35.5889, 'longitude' => -5.3626],
            ['name' => 'Safi', 'name_ar' => 'آسفي', 'active' => true, 'latitude' => 32.2994, 'longitude' => -9.2372],
            ['name' => 'El Jadida', 'name_ar' => 'الجديدة', 'active' => true, 'latitude' => 33.2316, 'longitude' => -8.5007],
            ['name' => 'Béni Mellal', 'name_ar' => 'بني ملال', 'active' => true, 'latitude' => 32.3373, 'longitude' => -6.3498],
            ['name' => 'Nador', 'name_ar' => 'الناظور', 'active' => true, 'latitude' => 35.1667, 'longitude' => -2.9333],
        ];

        foreach ($cities as $cityData) {
            City::updateOrCreate(
                ['name' => $cityData['name']],
                $cityData
            );
        }
    }
}
