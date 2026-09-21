<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'active' => true]);
    }

    public function test_dashboard_renders_with_no_data(): void
    {
        // The trend maths divides by the previous period, so an empty
        // database is the case most likely to blow up.
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Vue d\'ensemble', false);
    }

    public function test_dashboard_renders_with_data(): void
    {
        $admin = $this->admin();
        $city = City::create(['name' => 'Casablanca', 'active' => true]);
        $category = Category::create(['name' => 'Voirie']);

        foreach (['OPEN', 'IN_PROGRESS', 'RESOLVED'] as $index => $status) {
            Report::create([
                'title' => "Signalement {$index}",
                'description' => 'Description de test.',
                'status' => $status,
                'user_id' => $admin->id,
                'category_id' => $category->id,
                'city_id' => $city->id,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Voirie');
    }

    public function test_index_pages_render(): void
    {
        $admin = $this->admin();

        $routes = [
            'admin.users.index',
            'admin.reports.index',
            'admin.cities.index',
            'admin.comments.index',
            'admin.categories.index',
            'admin.ads.index',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_city_status_toggle_returns_json_for_ajax(): void
    {
        $city = City::create(['name' => 'Rabat', 'active' => true]);

        $this->actingAs($this->admin())
            ->patchJson(route('admin.cities.updateStatus', $city), ['active' => 0])
            ->assertOk()
            ->assertJson(['ok' => true, 'active' => false]);

        $this->assertFalse($city->fresh()->active);
    }

    public function test_city_status_toggle_still_redirects_without_javascript(): void
    {
        $city = City::create(['name' => 'Fès', 'active' => true]);

        $this->actingAs($this->admin())
            ->patch(route('admin.cities.updateStatus', $city), ['active' => 0])
            ->assertRedirect();

        $this->assertFalse($city->fresh()->active);
    }

    public function test_user_status_toggle_returns_json_for_ajax(): void
    {
        $target = User::factory()->create(['active' => true]);

        $this->actingAs($this->admin())
            ->patchJson(route('admin.users.updateStatus', $target), ['active' => 0])
            ->assertOk()
            ->assertJson(['ok' => true, 'active' => false]);

        $this->assertFalse($target->fresh()->active);
    }
}
