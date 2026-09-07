<?php

namespace App\Services;

use App\Models\City;
use App\Models\District;
use App\Models\Quartier;

/**
 * Resolves a City/District/Quartier from a map lat/lng.
 *
 * Bottom-up by design: find the nearest quartier (or, failing that, the
 * nearest district) and walk its real Eloquent relations up to get its
 * district and city, rather than matching each tier independently. Two
 * independently-nearest tiers can disagree — e.g. a pin can be closer to
 * Rabat's city centroid while actually sitting inside a Salé quartier,
 * since Rabat and Salé sit on opposite banks of the same river a few km
 * apart. Walking up from the quartier avoids that: whatever quartier is
 * closest, its own district and city are what gets returned, always
 * consistent with what's already in the database.
 *
 * District/Quartier coverage is sparse (only seeded for one city today),
 * so those two are always "best effort" — only returned when the nearest
 * known row is within a plausible distance for that tier, and only when
 * its parent city is active. City on its own always resolves to the
 * nearest active city with coordinates as a last resort; callers should
 * still treat a null return as "no confident match" rather than assume
 * one always exists.
 */
class LocationResolver
{
    private const MAX_DISTRICT_KM = 25;
    private const MAX_QUARTIER_KM = 15;

    // Generous bounding box around Morocco (not a distance-to-nearest-city
    // cutoff — the largest real gap between two seeded cities is ~200km
    // around Agadir, so a distance cutoff would falsely reject legitimate
    // photos in real coverage gaps). This only rejects coordinates that are
    // clearly not Morocco at all: another continent, the ocean, (0,0)
    // "null island" from a corrupted EXIF tag, etc.
    private const MIN_LAT = 20.0;
    private const MAX_LAT = 36.0;
    private const MIN_LNG = -18.0;
    private const MAX_LNG = -1.0;

    public function isWithinSupportedArea(float $lat, float $lng): bool
    {
        return $lat >= self::MIN_LAT && $lat <= self::MAX_LAT
            && $lng >= self::MIN_LNG && $lng <= self::MAX_LNG;
    }

    /**
     * @return array{city_id: int|null, district_id: int|null, quartier_id: int|null}
     */
    public function resolve(float $lat, float $lng): array
    {
        if (!$this->isWithinSupportedArea($lat, $lng)) {
            return ['city_id' => null, 'district_id' => null, 'quartier_id' => null];
        }

        $quartier = $this->nearestWithin(
            Quartier::with('district.city')->whereNotNull('latitude')->whereNotNull('longitude')->get(),
            $lat,
            $lng,
            fn ($q) => [$q->latitude, $q->longitude],
            self::MAX_QUARTIER_KM
        );

        if ($quartier && $quartier->district && $quartier->district->city?->active) {
            return [
                'city_id' => $quartier->district->city->id,
                'district_id' => $quartier->district->id,
                'quartier_id' => $quartier->id,
            ];
        }

        $district = $this->nearestWithin(
            District::with('city')->whereNotNull('lat')->whereNotNull('lng')->get(),
            $lat,
            $lng,
            fn ($d) => [$d->lat, $d->lng],
            self::MAX_DISTRICT_KM
        );

        if ($district && $district->city?->active) {
            return [
                'city_id' => $district->city->id,
                'district_id' => $district->id,
                'quartier_id' => null,
            ];
        }

        $city = $this->nearest(
            City::where('active', true)->whereNotNull('latitude')->whereNotNull('longitude')->get(),
            $lat,
            $lng,
            fn ($c) => [$c->latitude, $c->longitude]
        );

        return [
            'city_id' => $city?->id,
            'district_id' => null,
            'quartier_id' => null,
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
