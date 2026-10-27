<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MakesReportPhotos;
use Tests\TestCase;

class ReportLocationConsistencyTest extends TestCase
{
    use MakesReportPhotos;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * City/District/Quartier can arrive from either the auto-locked panel
     * (populated client-side from /api/resolve-location) or the manual
     * cascade selects — either way, store() must reject a district that
     * doesn't actually belong to the submitted city rather than silently
     * saving an inconsistent hierarchy.
     */
    public function test_store_rejects_a_district_that_does_not_belong_to_the_submitted_city(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $category = Category::first();
        $wrongCity = City::where('name', '!=', 'Salé')->first();
        $saleDistrict = District::first(); // every seeded district belongs to Salé

        $response = $this->actingAs($user)->post('/reports', [
            'title' => 'Mismatched hierarchy',
            'category_id' => $category->id,
            'city_id' => $wrongCity->id,
            'district_id' => $saleDistrict->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
            'image' => $this->reportPhoto(),
        ]);

        $response->assertSessionHasErrors('district_id');
        $this->assertDatabaseCount('reports', 0);
    }

    public function test_store_accepts_a_city_with_no_district_data(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $category = Category::first();
        $marrakech = City::where('name', 'Marrakech')->first();

        $response = $this->actingAs($user)->post('/reports', [
            'title' => 'City-only report',
            'category_id' => $category->id,
            'city_id' => $marrakech->id,
            'latitude' => 31.6295,
            'longitude' => -7.9811,
            'image' => $this->reportPhoto(),
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('reports', [
            'title' => 'City-only report',
            'city_id' => $marrakech->id,
            'district_id' => null,
            'quartier_id' => null,
        ]);
    }

    public function test_store_accepts_a_fully_consistent_hierarchy(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $category = Category::first();
        $district = District::with('city')->first();
        $quartier = $district->quartiers()->first();

        $response = $this->actingAs($user)->post('/reports', [
            'title' => 'Consistent hierarchy',
            'category_id' => $category->id,
            'city_id' => $district->city_id,
            'district_id' => $district->id,
            'quartier_id' => $quartier->id,
            'latitude' => (float) $quartier->latitude,
            'longitude' => (float) $quartier->longitude,
            'image' => $this->reportPhoto(),
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('reports', [
            'title' => 'Consistent hierarchy',
            'city_id' => $district->city_id,
            'district_id' => $district->id,
            'quartier_id' => $quartier->id,
        ]);
    }
}
