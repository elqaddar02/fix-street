<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\District;
use App\Models\Quartier;
use App\Services\LocationResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationResolverTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every quartier's own coordinates must resolve back to itself, and to
     * its real district/city via the actual relations — not to whichever
     * city centroid happens to be nearest. This is what broke before the
     * resolver was made bottom-up: a quartier in Salé could resolve to
     * Rabat (a different city, geographically close) because Rabat's city
     * centroid was nearer than Salé's, even though the point was inside a
     * real, known Salé quartier.
     */
    public function test_every_seeded_quartier_resolves_to_itself(): void
    {
        $this->seed();

        $resolver = app(LocationResolver::class);

        $quartiers = Quartier::with('district.city')->get();
        $this->assertGreaterThan(0, $quartiers->count(), 'Expected seeded quartiers to test against.');

        foreach ($quartiers as $quartier) {
            $resolved = $resolver->resolve((float) $quartier->latitude, (float) $quartier->longitude);

            $this->assertSame(
                $quartier->id,
                $resolved['quartier_id'],
                "Quartier '{$quartier->name_fr}' did not resolve back to itself."
            );
            $this->assertSame(
                $quartier->district_id,
                $resolved['district_id'],
                "Quartier '{$quartier->name_fr}' resolved to the wrong district."
            );
            $this->assertSame(
                $quartier->district->city_id,
                $resolved['city_id'],
                "Quartier '{$quartier->name_fr}' resolved to the wrong city."
            );
        }
    }

    public function test_a_point_far_from_any_known_quartier_or_district_falls_back_to_city_only(): void
    {
        $this->seed();

        $resolved = app(LocationResolver::class)->resolve(31.6295, -7.9811); // Marrakech

        $marrakech = City::where('name', 'Marrakech')->first();

        $this->assertSame($marrakech->id, $resolved['city_id']);
        $this->assertNull($resolved['district_id']);
        $this->assertNull($resolved['quartier_id']);
    }

    public function test_district_and_quartier_are_never_returned_without_their_parent(): void
    {
        $this->seed();

        $resolved = app(LocationResolver::class)->resolve(34.025, -6.822); // inside Salé

        if ($resolved['quartier_id'] !== null) {
            $quartier = Quartier::find($resolved['quartier_id']);
            $this->assertSame($resolved['district_id'], $quartier->district_id);
        }

        if ($resolved['district_id'] !== null) {
            $district = District::find($resolved['district_id']);
            $this->assertSame($resolved['city_id'], $district->city_id);
        }

        $this->assertNotNull($resolved['city_id']);
    }
}
