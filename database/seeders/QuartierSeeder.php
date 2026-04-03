<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Quartier;

class QuartierSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [

            'Salé' => [
                ['name' => 'Hay Essalam', 'name_ar' => 'حي السلام'],
                ['name' => 'Tabriquet', 'name_ar' => 'تبريكيت'],
                ['name' => 'Bettana', 'name_ar' => 'بطانة'],
                ['name' => 'Sidi Bouknadel', 'name_ar' => 'سيدي بوكنادل'],
                ['name' => 'Centre Ville', 'name_ar' => 'وسط المدينة'],
                ['name' => 'Bab Lamrissa', 'name_ar' => 'باب المريسة'],
                ['name' => 'Hay Rahma', 'name_ar' => 'حي الرحمة'],
                ['name' => 'Hay Al Amal', 'name_ar' => 'حي الأمل'],
                ['name' => 'Hay Al Qods', 'name_ar' => 'حي القدس'],
            ],

            'Rabat' => [
                ['name' => 'Agdal', 'name_ar' => 'أكدال'],
                ['name' => 'Hassan', 'name_ar' => 'الحسن'],
                ['name' => 'Hay Riad', 'name_ar' => 'حي الرياض'],
                ['name' => 'Yacoub El Mansour', 'name_ar' => 'يعقوب المنصور'],
                ['name' => 'Souissi', 'name_ar' => 'السويسي'],
                ['name' => 'Takadoum', 'name_ar' => 'التكادوم'],
                ['name' => 'Akkari', 'name_ar' => 'العكاري'],
                ['name' => 'Océan', 'name_ar' => 'المحيط'],
                ['name' => 'Diour Jamaa', 'name_ar' => 'ديور الجامع'],
            ],

            'Casablanca' => [
                ['name' => 'Maarif', 'name_ar' => 'المعاريف'],
                ['name' => 'Anfa', 'name_ar' => 'انفا'],
                ['name' => 'Ain Diab', 'name_ar' => 'عين الذئب'],
                ['name' => 'Sidi Moumen', 'name_ar' => 'سيدي مومن'],
                ['name' => 'Hay Hassani', 'name_ar' => 'حي الحسني'],
                ['name' => 'Bernoussi', 'name_ar' => 'البرنوصي'],
                ['name' => 'Roches Noires', 'name_ar' => 'الصخور السوداء'],
                ['name' => 'Derb Sultan', 'name_ar' => 'درب السلطان'],
                ['name' => 'Sbata', 'name_ar' => 'السباطة'],
            ],

            'Marrakech' => [
                ['name' => 'Gueliz', 'name_ar' => 'كوليز'],
                ['name' => 'Hivernage', 'name_ar' => 'الإيويرناج'],
                ['name' => 'Medina', 'name_ar' => 'المدينة'],
                ['name' => 'Sidi Youssef Ben Ali', 'name_ar' => 'سيدي يوسف بن علي'],
                ['name' => 'Daoudiate', 'name_ar' => 'الداوديات'],
                ['name' => 'Targa', 'name_ar' => 'التركا'],
            ],

            'Tanger' => [
                ['name' => 'Malabata', 'name_ar' => 'الملاباطة'],
                ['name' => 'Marshan', 'name_ar' => 'المرشان'],
                ['name' => 'Iberia', 'name_ar' => 'إيبيريا'],
                ['name' => 'Boukhalef', 'name_ar' => 'بوخالف'],
                ['name' => 'Branes', 'name_ar' => 'برانيس'],
                ['name' => 'Moghogha', 'name_ar' => 'موغوغا'],
            ],

            'Fès' => [
                ['name' => 'Fès El Bali', 'name_ar' => 'فاس البالي'],
                ['name' => 'Fès El Jdid', 'name_ar' => 'فاس الجديد'],
                ['name' => 'Zouagha', 'name_ar' => 'الزواغة'],
                ['name' => 'Narjiss', 'name_ar' => 'النرجس'],
                ['name' => 'Saiss', 'name_ar' => 'السايس'],
            ],

            'Agadir' => [
                ['name' => 'Talborjt', 'name_ar' => 'تالبورجت'],
                ['name' => 'Founty', 'name_ar' => 'فونطي'],
                ['name' => 'Dakhla', 'name_ar' => 'الداخلة'],
                ['name' => 'Hay Mohammadi', 'name_ar' => 'حي محمدي'],
                ['name' => 'Anza', 'name_ar' => 'عنزة'],
            ],
        ];

        foreach ($cities as $cityName => $quartiers) {
            $city = City::where('name', $cityName)->first();

            if (!$city) {
                continue;
            }

            foreach ($quartiers as $quartierData) {
                Quartier::firstOrCreate([
                    'name' => $quartierData['name'],
                    'city_id' => $city->id,
                ], [
                    'name_ar' => $quartierData['name_ar'] ?? null,
                    'active' => true,
                ]);
            }
        }
    }
}