<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'active', 'latitude', 'longitude', 'boundary'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            // GeoJSON Polygon or MultiPolygon geometry, [lng, lat] pairs.
            'boundary' => 'array',
        ];
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function getDisplayNameAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->name_ar) {
            return $this->name_ar;
        }
        return $this->name;
    }

    public function hasBoundary(): bool
    {
        return in_array($this->boundary['type'] ?? null, ['Polygon', 'MultiPolygon'], true);
    }

    /**
     * Whether a point lies inside the city's boundary. Rabat and Salé face
     * each other across a river, so distance alone can't tell them apart.
     */
    public function contains(float $lat, float $lng): bool
    {
        if (! $this->hasBoundary()) {
            return false;
        }

        $polygons = $this->boundary['type'] === 'Polygon'
            ? [$this->boundary['coordinates']]
            : $this->boundary['coordinates'];

        foreach ($polygons as $rings) {
            // Inside the outer ring and in none of its holes.
            if (self::inRing($lat, $lng, $rings[0])) {
                foreach (array_slice($rings, 1) as $hole) {
                    if (self::inRing($lat, $lng, $hole)) {
                        continue 2;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /** Ray casting over a ring of [lng, lat] pairs. */
    private static function inRing(float $lat, float $lng, array $ring): bool
    {
        $inside = false;
        $count = count($ring);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            [$xi, $yi] = $ring[$i];
            [$xj, $yj] = $ring[$j];

            if ((($yi > $lat) !== ($yj > $lat)) && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi)) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }
}
