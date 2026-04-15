<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\Quartier;

class ReportCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all reports without coordinates
        $reports = Report::whereNull('latitude')->orWhereNull('longitude')->get();

        foreach ($reports as $report) {
            // If report has a quartier, use its coordinates
            if ($report->quartier && $report->quartier->latitude && $report->quartier->longitude) {
                $report->update([
                    'latitude' => $report->quartier->latitude,
                    'longitude' => $report->quartier->longitude,
                ]);
            } else {
                // Generate random coordinates around Moroccan cities
                $coordinates = $this->getRandomMoroccanCoordinates();
                $report->update([
                    'latitude' => $coordinates['lat'],
                    'longitude' => $coordinates['lng'],
                ]);
            }
        }

        $this->command->info('Report coordinates seeded successfully!');
    }

    /**
     * Get random coordinates around Moroccan cities
     */
    private function getRandomMoroccanCoordinates(): array
    {
        $cities = [
            ['name' => 'Casablanca', 'lat' => 33.5731, 'lng' => -7.5898],
            ['name' => 'Rabat', 'lat' => 34.0209, 'lng' => -6.8416],
            ['name' => 'Marrakech', 'lat' => 31.6295, 'lng' => -7.9811],
            ['name' => 'Fès', 'lat' => 34.0331, 'lng' => -5.0003],
            ['name' => 'Salé', 'lat' => 34.0531, 'lng' => -6.7985],
            ['name' => 'Meknès', 'lat' => 33.8731, 'lng' => -5.5407],
            ['name' => 'Oujda', 'lat' => 34.6867, 'lng' => -1.9114],
            ['name' => 'Kenitra', 'lat' => 34.2610, 'lng' => -6.5802],
            ['name' => 'Agadir', 'lat' => 30.4278, 'lng' => -9.5981],
            ['name' => 'Tétouan', 'lat' => 35.5784, 'lng' => -5.3684],
        ];

        $city = $cities[array_rand($cities)];

        // Add some random variation (±0.01 degrees ≈ 1km)
        $lat = $city['lat'] + (mt_rand(-100, 100) / 10000);
        $lng = $city['lng'] + (mt_rand(-100, 100) / 10000);

        return ['lat' => $lat, 'lng' => $lng];
    }
}