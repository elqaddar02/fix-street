<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Quartier;
use App\Models\Report;
use App\Models\User;
use App\Services\LocationResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MakesReportPhotos;
use Tests\TestCase;

/**
 * Finding F-17 of the Arabic end-to-end run: a photo taken by the Hassan
 * Tower in Rabat was filed under Salé, because Salé's nearest quartier point
 * is a few hundred metres away across the river. Cities now have a boundary.
 */
class CoverageAreaTest extends TestCase
{
    use MakesReportPhotos;
    use RefreshDatabase;

    private const HASSAN_TOWER = [34.0240, -6.8226];
    private const HAY_KARIMA = [34.0481, -6.7951];

    private City $sale;
    private Quartier $marina;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->sale = City::create([
            'name' => 'Salé', 'name_ar' => 'سلا', 'active' => true, 'latitude' => 34.0386, 'longitude' => -6.8159,
            'boundary' => json_decode(file_get_contents(database_path('data/sale-boundary.geojson')), true),
        ]);
        $district = District::create(['city_id' => $this->sale->id, 'name_fr' => 'Bab Lamrissa', 'name_ar' => 'باب لمريسة', 'slug' => 'bab-lamrissa', 'lat' => 34.0331, 'lng' => -6.8203]);
        // The seeded La Marina point that sat on the Rabat bank: still the nearest quartier to the tower.
        $this->marina = Quartier::create(['district_id' => $district->id, 'name_fr' => 'La Marina', 'slug' => 'la-marina', 'latitude' => 34.0255, 'longitude' => -6.8235]);
        Quartier::create(['district_id' => $district->id, 'name_fr' => 'Hay Karima', 'slug' => 'hay-karima', 'latitude' => 34.048, 'longitude' => -6.795]);
    }

    public function test_the_boundary_separates_salé_from_rabat(): void
    {
        $this->assertTrue($this->sale->hasBoundary());
        $this->assertFalse($this->sale->contains(...self::HASSAN_TOWER));
        $this->assertTrue($this->sale->contains(...self::HAY_KARIMA));
    }

    public function test_a_rabat_pin_is_outside_the_covered_area(): void
    {
        $resolved = app(LocationResolver::class)->resolve(...self::HASSAN_TOWER);
        $this->assertNull($resolved['city_id']);

        $this->getJson(route('location.resolve', ['lat' => self::HASSAN_TOWER[0], 'lng' => self::HASSAN_TOWER[1]]))
            ->assertOk()
            ->assertJson(['outside_area' => true, 'city_id' => null]);
    }

    public function test_a_salé_pin_still_resolves_inside_salé(): void
    {
        $resolved = app(LocationResolver::class)->resolve(...self::HAY_KARIMA);

        $this->assertSame($this->sale->id, $resolved['city_id']);
        $this->assertNotNull($resolved['quartier_id']);
    }

    public function test_a_report_in_rabat_cannot_be_filed_under_salé(): void
    {
        $this->actingAs(User::factory()->create())->post(route('reports.store'), [
            'title' => 'Près de la tour Hassan',
            'category_id' => Category::create(['name' => 'Pothole', 'name_ar' => 'حفرة'])->id,
            'city_id' => $this->sale->id,
            'latitude' => self::HASSAN_TOWER[0],
            'longitude' => self::HASSAN_TOWER[1],
            'image' => $this->reportPhoto(),
        ])->assertSessionHasErrors('latitude');

        $this->assertSame(0, Report::count());
    }

    public function test_cities_without_a_boundary_keep_nearest_point_matching(): void
    {
        $marrakech = City::create(['name' => 'Marrakech', 'active' => true, 'latitude' => 31.6295, 'longitude' => -7.9811]);

        $this->assertSame($marrakech->id, app(LocationResolver::class)->resolve(31.63, -7.99)['city_id']);
    }

    public function test_every_seeded_salé_place_lies_inside_salé(): void
    {
        // Start from the real seeders rather than this test's fixtures.
        City::query()->delete();
        $this->seed();

        $sale = City::where('name', 'Salé')->firstOrFail();
        $this->assertTrue($sale->hasBoundary());

        foreach ($sale->districts()->with('quartiers')->get() as $district) {
            $this->assertTrue($sale->contains((float) $district->lat, (float) $district->lng), "District {$district->name_fr} is outside Salé.");

            foreach ($district->quartiers as $quartier) {
                $this->assertTrue($sale->contains((float) $quartier->latitude, (float) $quartier->longitude), "Quartier {$quartier->name_fr} is outside Salé.");
            }
        }

        $this->assertDatabaseMissing('quartiers', ['name_fr' => 'Quartier Administratif']);
        $this->assertDatabaseMissing('quartiers', ['name_fr' => 'Sidi Bouknadel']);
    }

    public function test_the_admin_cannot_place_a_quartier_outside_the_city(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)->patch(route('admin.quartiers.update', $this->marina), [
            'name_fr' => 'La Marina', 'latitude' => self::HASSAN_TOWER[0], 'longitude' => self::HASSAN_TOWER[1],
        ])->assertSessionHasErrors('latitude');

        $this->actingAs($admin)->patch(route('admin.quartiers.update', $this->marina), [
            'name_fr' => 'La Marina', 'latitude' => 34.0326, 'longitude' => -6.8290,
        ])->assertSessionHasNoErrors();
    }
}
