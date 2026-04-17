<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Quartier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuartierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Bab Lamrissa' => [
                ['fr' => 'Médina', 'ar' => 'المدينة القديمة', 'lat' => 34.0336, 'lng' => -6.8261],
                ['fr' => 'Mellah', 'ar' => 'الملاح', 'lat' => 34.0310, 'lng' => -6.8210],
                ['fr' => 'La Marina', 'ar' => 'مارينا', 'lat' => 34.0255, 'lng' => -6.8235],
                ['fr' => 'Sidi Moussa', 'ar' => 'سيدي موسى', 'lat' => 34.0410, 'lng' => -6.8320],
                ['fr' => 'Said Hajji', 'ar' => 'سعيد حجي', 'lat' => 34.0450, 'lng' => -6.8300],
            ],
            'Bettana' => [
                ['fr' => 'Hay Moulay Ismaïl', 'ar' => 'حي مولاي إسماعيل', 'lat' => 34.0195, 'lng' => -6.8120],
                ['fr' => 'Rmel', 'ar' => 'الرمل', 'lat' => 34.0240, 'lng' => -6.8080],
                ['fr' => 'Quartier Administratif', 'ar' => 'الحي الإداري', 'lat' => 34.0180, 'lng' => -6.8150],
                ['fr' => 'Al Inbiat', 'ar' => 'الانبعاث', 'lat' => 34.0320, 'lng' => -6.8120],
                ['fr' => 'Mazzer', 'ar' => 'مازر', 'lat' => 34.0150, 'lng' => -6.8050],
            ],
            'Tabriquet' => [
                ['fr' => 'Hay Es-Salam', 'ar' => 'حي السلام', 'lat' => 34.0430, 'lng' => -6.8010],
                ['fr' => 'Hay Karima', 'ar' => 'حي كريمة', 'lat' => 34.0480, 'lng' => -6.7950],
                ['fr' => 'Wadi Dahab', 'ar' => 'وادي الذهب', 'lat' => 34.0385, 'lng' => -6.8040],
                ['fr' => 'Dyour Ben Cheikh', 'ar' => 'ديور بن الشيخ', 'lat' => 34.0350, 'lng' => -6.8050],
                ['fr' => 'Riad', 'ar' => 'رياض', 'lat' => 34.0400, 'lng' => -6.8150],
            ],
            'Layayda' => [
                ['fr' => 'Karia', 'ar' => 'القرية', 'lat' => 34.0650, 'lng' => -6.7850],
                ['fr' => 'Oulad el-Qadi', 'ar' => 'أولاد القاضي', 'lat' => 34.0680, 'lng' => -6.7750],
                ['fr' => 'Sidi Bouknadel', 'ar' => 'سيدي بوقنادل', 'lat' => 34.1330, 'lng' => -6.7330],
                ['fr' => 'Moussa Ben Noussair', 'ar' => 'موسى بن نصير', 'lat' => 34.0580, 'lng' => -6.7920],
            ],
            'Hssaine' => [
                ['fr' => 'Sala Al Jadida', 'ar' => 'سلا الجديدة', 'lat' => 33.9880, 'lng' => -6.7450],
                ['fr' => 'Technopolis', 'ar' => 'تكنوبوليس', 'lat' => 33.9760, 'lng' => -6.7300],
                ['fr' => 'Hay El Baraka', 'ar' => 'حي البركة', 'lat' => 34.0050, 'lng' => -6.7550],
                ['fr' => 'Quartier Al Amal', 'ar' => 'حي الأمل', 'lat' => 34.0030, 'lng' => -6.7400],
                ['fr' => 'Sidi Abdallah', 'ar' => 'سيدي عبد الله', 'lat' => 34.0200, 'lng' => -6.7200],
            ],
        ];

        foreach ($data as $districtName => $quartiers) {
            $district = District::where('name_fr', $districtName)->first();

            if ($district) {
                foreach ($quartiers as $q) {
                    Quartier::updateOrCreate(
                        ['slug' => Str::slug($q['fr'])],
                        [
                            'name_fr' => $q['fr'],
                            'name_ar' => $q['ar'],
                            'district_id' => $district->id,
                            'latitude' => $q['lat'],
                            'longitude' => $q['lng'],
                        ]
                    );
                }
            } else {
                $this->command->warn("District '$districtName' not found. Skipping its quartiers.");
            }
        }
    }
}