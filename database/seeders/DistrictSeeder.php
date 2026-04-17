<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Salé city
        $saleCity = City::where('name', 'Salé')->first();
        
        if (!$saleCity) {
            return;
        }

        $districts = [
            [
                'name_fr' => 'Bab Lamrissa',
                'name_ar' => 'باب لمريسة',
                'description_fr' => 'Historical hub, Marina area',
                'description_ar' => 'منطقة تاريخية، منطقة البحرية',
                'lat' => 34.028,
                'lng' => -6.824,
            ],
            [
                'name_fr' => 'Bettana',
                'name_ar' => 'بطانة',
                'description_fr' => 'Residential, administrative area',
                'description_ar' => 'منطقة سكنية، منطقة إدارية',
                'lat' => 34.022,
                'lng' => -6.811,
            ],
            [
                'name_fr' => 'Tabriquet',
                'name_ar' => 'تابريكت',
                'description_fr' => 'Commercial center, high density',
                'description_ar' => 'مركز تجاري، كثافة عالية',
                'lat' => 34.041,
                'lng' => -6.805,
            ],
            [
                'name_fr' => 'Layayda',
                'name_ar' => 'لعيايدة',
                'description_fr' => 'Northern expansion area',
                'description_ar' => 'منطقة التوسع الشمالية',
                'lat' => 34.062,
                'lng' => -6.791,
            ],
            [
                'name_fr' => 'Hssaine',
                'name_ar' => 'احصين',
                'description_fr' => 'Includes Sala Al Jadida and Hssaine zones',
                'description_ar' => 'تشمل مناطق سلا الجديدة واحصين',
                'lat' => 34.004,
                'lng' => -6.748,
            ],
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['slug' => Str::slug($district['name_fr'])],
                array_merge($district, [
                    'city_id' => $saleCity->id,
                ])
            );
        }
    }
}
