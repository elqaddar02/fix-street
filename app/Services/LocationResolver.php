<?php

namespace App\Services;

use App\Models\City;
use App\Models\District;
use App\Models\Quartier;

/**
 * Resolves a City/District/Quartier from a map lat/lng.
 *
 * District/Quartier coverage in this app is sparse (only seeded for one
 * city today), so those two are always "best effort": a match is only
 * returned when the nearest known row is within a plausible distance for
 * that tier. City is only matched among active cities (the set already
 * offered elsewhere in the app), and is expected to always resolve since
 * every active city has coordinates — but callers should still treat a
 * null return as "no confident match" rather than assume one always exists.
 */
class LocationResolver
{
    private const MAX_DISTRICT_KM = 25;
    private const MAX_QUARTIER_KM = 15;

    /**
     * @return array{city_id: int|null, district_id: int|null, quartier_id: int|null}
     */
    public function resolve(float $lat, float $lng): array
    {
        $city = $this->nearest(
            City::where('active', true)->whereNotNull('latitude')->whereNotNull('longitude')->get(),
            $lat,
            $lng,
            fn ($c) => [$c->latitude, $c->longitude]
        );

        $district = null;
        if ($city) {
            $district = $this->nearestWithin(
                District::where('city_id', $city->id)->whereNotNull('lat')->whereNotNull('lng')->get(),
                $lat,
                $lng,
                fn ($d) => [$d->lat, $d->lng],
                self::MAX_DISTRICT_KM
            );
        }

        $quartier = null;
        if ($district) {
            $quartier = $this->nearestWithin(
                Quartier::where('district_id', $district->id)->whereNotNull('latitude')->whereNotNull('longitude')->get(),
                $lat,
                $lng,
                fn ($q) => [$q->latitude, $q->longitude],
                self::MAX_QUARTIER_KM
            );
        }

        return [
            'city_id' => $city?->id,
            'district_id' => $district?->id,
            'quartier_id' => $quartier?->id,
        ];
    }

    /**
     * Nearest row regardless of distance (used for city — always pick the closest known city).
     */
    private function nearest($rows, float $lat, float $lng, callable $coords)
    {
        return $this->nearestWithin($rows, $lat, $lng, $coords, null);
    }

    /**
     * Nearest row, or null if even the closest one is farther than $maxKm (when given).
     */
    private function nearestWithin($rows, float $lat, float $lng, callable $coords, ?float $maxKm)
    {
        $best = null;
        $bestDistance = null;

        foreach ($rows as $row) {
            [$rowLat, $rowLng] = $coords($row);
            $distance = $this->haversineKm($lat, $lng, (float) $rowLat, (float) $rowLng);

            if ($bestDistance === null || $distance < $bestDistance) {
                $best = $row;
                $bestDistance = $distance;
            }
        }

        if ($best === null) {
            return null;
        }

        if ($maxKm !== null && $bestDistance > $maxKm) {
            return null;
        }

        return $best;
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
