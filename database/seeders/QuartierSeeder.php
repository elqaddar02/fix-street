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
                ['fr' => 'Médina', 'ar' => 'المدينة القديمة'],
                ['fr' => 'Mellah', 'ar' => 'الملاح'],
                ['fr' => 'La Marina', 'ar' => 'مارينا'],
                ['fr' => 'Sidi Moussa', 'ar' => 'سيدي موسى'],
                ['fr' => 'Said Hajji', 'ar' => 'سعيد حجي'],
            ],
            'Bettana' => [
                ['fr' => 'Hay Moulay Ismaïl', 'ar' => 'حي مولاي إسماعيل'],
                ['fr' => 'Rmel', 'ar' => 'الرمل'],
                ['fr' => 'Quartier Administratif', 'ar' => 'الحي الإداري'],
                ['fr' => 'Al Inbiat', 'ar' => 'الانبعاث'],
            ],
            'Tabriquet' => [
                ['fr' => 'Hay Es-Salam', 'ar' => 'حي السلام'],
                ['fr' => 'Hay Karima', 'ar' => 'حي كريمة'],
                ['fr' => 'Wadi Dahab', 'ar' => 'وادي الذهب'],
                ['fr' => 'Dyour Ben Cheikh', 'ar' => 'ديور بن الشيخ'],
            ],
            'Layayda' => [
                ['fr' => 'Karia', 'ar' => 'القرية'],
                ['fr' => 'Oulad el-Qadi', 'ar' => 'أولاد القاضي'],
                ['fr' => 'Sidi Bouknadel', 'ar' => 'سيدي بوقنادل'],
                ['fr' => 'Moussa Ben Noussair', 'ar' => 'موسى بن نصير'],
            ],
            'Hssaine' => [
                ['fr' => 'Sala Al Jadida', 'ar' => 'سلا الجديدة'],
                ['fr' => 'Technopolis', 'ar' => 'تكنوبوليس'],
                ['fr' => 'Hay El Baraka', 'ar' => 'حي البركة'],
                ['fr' => 'Quartier Al Amal', 'ar' => 'حي الأمل'],
            ],
        ];

        foreach ($data as $districtName => $quartiers) {
            // Find the district created by your previous seeder
            $district = District::where('name_fr', $districtName)->first();

            if ($district) {
                foreach ($quartiers as $q) {
                    Quartier::firstOrCreate(
                        ['slug' => Str::slug($q['fr'])],
                        [
                            'name_fr' => $q['fr'],
                            'name_ar' => $q['ar'],
                            'district_id' => $district->id,
                        ]
                    );
                }
            } else {
                $this->command->warn("District '$districtName' not found. Skipping its quartiers.");
            }
        }
    }
}
