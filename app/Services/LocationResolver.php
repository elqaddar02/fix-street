<?php

namespace App\Services;

use App\Models\City;
use App\Models\District;
use App\Models\Quartier;

/**
 * Resolves a City/District/Quartier from a map lat/lng.
 *
 * City first, by boundary: an active city with a boundary (GeoJSON from
 * OpenStreetMap) claims exactly the pins inside it. Rabat and Salé face
 * each other across the Bouregreg, a few hundred metres apart, so
 * nearest-point matching filed Rabat pins under Salé; a boundary can't.
 * A pin inside no covered city resolves to nothing ("outside the area").
 *
 * Inside the city, district and quartier are best effort: the nearest one
 * within a plausible distance, walking up its real relations so the three
 * levels always agree. Cities without a boundary yet fall back to the
 * older nearest-point matching, with the nearest active city as a last
 * resort; callers should treat a null city as "no confident match".
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
    /** [min lat, max lat, min lng, max lng]: shared with the report and place forms. */
    public const SUPPORTED_AREA = [20.0, 36.0, -18.0, -1.0];

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
        $none = ['city_id' => null, 'district_id' => null, 'quartier_id' => null];

        if (!$this->isWithinSupportedArea($lat, $lng)) {
            return $none;
        }

        $active = City::where('active', true)->get();

        // A city with a known boundary claims every point inside it, and only
        // those: a pin across the river in Rabat is not Salé, however close.
        $city = $active->first(fn (City $city) => $city->contains($lat, $lng));
        if ($city) {
            return $this->placeWithin(collect([$city->id]), $lat, $lng, fallbackCity: $city);
        }

        // Cities without a boundary yet keep the older nearest-point matching.
        $unbounded = $active->reject(fn (City $city) => $city->hasBoundary());
        if ($unbounded->isEmpty()) {
            return $none;
        }

        $nearestCity = $this->nearest(
            $unbounded->filter(fn (City $city) => $city->latitude && $city->longitude),
            $lat,
            $lng,
            fn ($c) => [$c->latitude, $c->longitude]
        );

        return $this->placeWithin($unbounded->pluck('id'), $lat, $lng, fallbackCity: $nearestCity);
    }

    /**
     * Nearest quartier, then nearest district, among the given cities; the
     * fallback city alone when neither is close enough.
     *
     * @return array{city_id: int|null, district_id: int|null, quartier_id: int|null}
     */
    private function placeWithin($cityIds, float $lat, float $lng, ?City $fallbackCity): array
    {
        $quartier = $this->nearestWithin(
            Quartier::with('district')
                ->whereHas('district', fn ($q) => $q->whereIn('city_id', $cityIds))
                ->whereNotNull('latitude')->whereNotNull('longitude')->get(),
            $lat,
            $lng,
            fn ($q) => [$q->latitude, $q->longitude],
            self::MAX_QUARTIER_KM
        );

        if ($quartier) {
            return [
                'city_id' => $quartier->district->city_id,
                'district_id' => $quartier->district_id,
                'quartier_id' => $quartier->id,
            ];
        }

        $district = $this->nearestWithin(
            District::whereIn('city_id', $cityIds)->whereNotNull('lat')->whereNotNull('lng')->get(),
            $lat,
            $lng,
            fn ($d) => [$d->lat, $d->lng],
            self::MAX_DISTRICT_KM
        );

        if ($district) {
            return [
                'city_id' => $district->city_id,
                'district_id' => $district->id,
                'quartier_id' => null,
            ];
        }

        return [
            'city_id' => $fallbackCity?->id,
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
