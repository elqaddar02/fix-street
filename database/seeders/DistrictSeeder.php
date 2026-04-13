<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [
                'name_fr' => 'Bab Lamrissa',
                'name_ar' => 'باب لمريسة',
                'description_fr' => 'Historical hub, Marina area',
                'description_ar' => 'منطقة تاريخية، منطقة البحرية',
            ],
            [
                'name_fr' => 'Bettana',
                'name_ar' => 'بطانة',
                'description_fr' => 'Residential, administrative area',
                'description_ar' => 'منطقة سكنية، منطقة إدارية',
            ],
            [
                'name_fr' => 'Tabriquet',
                'name_ar' => 'تابريكت',
                'description_fr' => 'Commercial center, high density',
                'description_ar' => 'مركز تجاري، كثافة عالية',
            ],
            [
                'name_fr' => 'Layayda',
                'name_ar' => 'لعيايدة',
                'description_fr' => 'Northern expansion area',
                'description_ar' => 'منطقة التوسع الشمالية',
            ],
            [
                'name_fr' => 'Hssaine',
                'name_ar' => 'احصين',
                'description_fr' => 'Includes Sala Al Jadida and Hssaine zones',
                'description_ar' => 'تشمل مناطق سلا الجديدة واحصين',
            ],
        ];

        foreach ($districts as $district) {
            District::create([
                'name_fr' => $district['name_fr'],
                'name_ar' => $district['name_ar'],
                'slug' => Str::slug($district['name_fr']),
                'description_fr' => $district['description_fr'],
                'description_ar' => $district['description_ar'],
            ]);
        }
    }
}
