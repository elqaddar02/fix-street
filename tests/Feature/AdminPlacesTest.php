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
use Tests\TestCase;

class AdminPlacesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function city(): City
    {
        return City::create(['name' => 'Salé', 'active' => true, 'latitude' => 34.04, 'longitude' => -6.80]);
    }

    private function district(City $city, string $name = 'Tabriquet'): District
    {
        return $city->districts()->create(['name_fr' => $name, 'name_ar' => 'تابريكت', 'slug' => str($name)->slug(), 'lat' => 34.05, 'lng' => -6.79]);
    }

    public function test_the_city_page_lists_districts_and_their_quartiers(): void
    {
        $city = $this->city();
        $district = $this->district($city);
        $district->quartiers()->create(['name_fr' => 'Hay Karima', 'slug' => 'hay-karima']);

        $this->actingAs($this->admin())->get(route('admin.cities.places', $city))
            ->assertOk()
            ->assertSee('Tabriquet')
            ->assertSee('Hay Karima')
            ->assertSee(__('admin.no_position'));

        $this->actingAs($this->admin())->get(route('admin.cities.index'))
            ->assertSee(route('admin.cities.places', $city), false);
    }

    public function test_a_district_and_a_quartier_can_be_created_with_a_map_point(): void
    {
        $city = $this->city();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.districts.create', $city))->assertOk()->assertSee('place-map', false);

        $this->actingAs($admin)->post(route('admin.districts.store', $city), [
            'name_fr' => 'Bettana', 'name_ar' => 'بطانة', 'latitude' => '34.0400', 'longitude' => '-6.8100',
        ])->assertRedirect(route('admin.cities.places', $city));

        $district = District::where('name_fr', 'Bettana')->firstOrFail();
        $this->assertSame($city->id, $district->city_id);
        $this->assertSame('bettana', $district->slug);
        $this->assertEqualsWithDelta(34.04, (float) $district->lat, 0.0001);

        $this->actingAs($admin)->post(route('admin.quartiers.store', $district), [
            'name_fr' => 'Hay Salam', 'latitude' => '34.0410', 'longitude' => '-6.8120',
        ])->assertRedirect(route('admin.cities.places', $city));

        $quartier = Quartier::where('name_fr', 'Hay Salam')->firstOrFail();
        $this->assertSame($district->id, $quartier->district_id);

        // The point is what the automatic location uses.
        $this->assertSame($quartier->id, app(LocationResolver::class)->resolve(34.0411, -6.8121)['quartier_id']);
    }

    public function test_slugs_stay_unique(): void
    {
        $city = $this->city();
        $this->district($city, 'Centre');

        $this->actingAs($this->admin())->post(route('admin.districts.store', $city), ['name_fr' => 'Centre', 'name_ar' => 'المركز']);

        $this->assertSame(['centre', 'centre-2'], District::orderBy('id')->pluck('slug')->all());
    }

    public function test_points_outside_morocco_and_half_points_are_refused(): void
    {
        $city = $this->city();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.districts.store', $city), ['name_fr' => 'X', 'name_ar' => 'س', 'latitude' => '48.85', 'longitude' => '2.35'])
            ->assertSessionHasErrors(['latitude', 'longitude']);
        $this->actingAs($admin)->post(route('admin.districts.store', $city), ['name_fr' => 'X', 'name_ar' => 'س', 'latitude' => '34.0'])
            ->assertSessionHasErrors('longitude');

        $this->assertSame(0, District::count());
    }

    public function test_editing_updates_names_and_point(): void
    {
        $district = $this->district($this->city());
        $quartier = $district->quartiers()->create(['name_fr' => 'Ancien', 'slug' => 'ancien']);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.quartiers.edit', $quartier))->assertOk()->assertSee('Ancien');
        $this->actingAs($admin)->patch(route('admin.quartiers.update', $quartier), ['name_fr' => 'Nouveau', 'name_ar' => 'جديد', 'latitude' => '34.06', 'longitude' => '-6.78'])
            ->assertRedirect();
        $this->assertSame('Nouveau', $quartier->fresh()->name_fr);
        $this->assertSame('ancien', $quartier->fresh()->slug);

        // Clearing the point is allowed.
        $this->actingAs($admin)->patch(route('admin.districts.update', $district), ['name_fr' => 'Tabriquet', 'name_ar' => 'تابريكت', 'latitude' => '', 'longitude' => '']);
        $this->assertNull($district->fresh()->lat);
    }

    public function test_places_in_use_cannot_be_deleted(): void
    {
        $city = $this->city();
        $district = $this->district($city);
        $quartier = $district->quartiers()->create(['name_fr' => 'Hay Karima', 'slug' => 'hay-karima']);
        Report::create([
            'title' => 'Trou', 'description' => 'Trou', 'status' => 'OPEN',
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::create(['name' => 'Voirie'])->id,
            'city_id' => $city->id, 'district_id' => $district->id, 'quartier_id' => $quartier->id,
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('admin.quartiers.destroy', $quartier))->assertSessionHas('error');
        $this->actingAs($admin)->delete(route('admin.districts.destroy', $district))->assertSessionHas('error');
        $this->assertModelExists($quartier);
        $this->assertModelExists($district);

        $empty = $district->quartiers()->create(['name_fr' => 'Vide', 'slug' => 'vide']);
        $this->actingAs($admin)->delete(route('admin.quartiers.destroy', $empty))->assertSessionHas('success');
        $this->assertModelMissing($empty);
        $this->assertDatabaseHas('audit_logs', ['action' => 'Delete', 'model' => 'Quartier', 'model_id' => $empty->id]);
    }

    public function test_citizens_cannot_reach_the_pages(): void
    {
        $city = $this->city();

        $this->actingAs(User::factory()->create())->get(route('admin.cities.places', $city))->assertForbidden();
    }
}
