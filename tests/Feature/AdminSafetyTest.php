<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin actions that used to destroy data or silently do nothing.
 */
class AdminSafetyTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function report(?User $owner = null, ?Category $category = null, ?City $city = null): Report
    {
        return Report::create([
            'title' => 'Pothole',
            'description' => 'Pothole',
            'status' => 'OPEN',
            'user_id' => ($owner ?? User::factory()->create())->id,
            'category_id' => ($category ?? Category::create(['name' => 'Roads', 'name_ar' => 'الطرق']))->id,
            'city_id' => ($city ?? City::create(['name' => 'Rabat', 'active' => true]))->id,
        ]);
    }

    public function test_a_category_in_use_cannot_be_deleted(): void
    {
        $report = $this->report();

        $this->actingAs($this->admin())
            ->delete(route('admin.categories.destroy', $report->category))
            ->assertSessionHas('error');

        $this->assertModelExists($report->category);
        $this->assertModelExists($report);
    }

    public function test_an_unused_category_can_still_be_deleted(): void
    {
        $category = Category::create(['name' => 'Unused', 'name_ar' => 'غير مستعمل']);

        $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $category));

        $this->assertModelMissing($category);
    }

    public function test_a_city_in_use_cannot_be_deleted(): void
    {
        $report = $this->report();

        $this->actingAs($this->admin())
            ->delete(route('admin.cities.destroy', $report->city))
            ->assertSessionHas('error');

        $this->assertModelExists($report);
    }

    public function test_deleting_a_user_keeps_their_reports_as_anonymous(): void
    {
        $citizen = User::factory()->create();
        $report = $this->report($citizen);

        $this->actingAs($this->admin())->delete(route('admin.users.destroy', $citizen));

        $this->assertModelMissing($citizen);
        $this->assertNull($report->fresh()->user_id);
        $this->get(route('reports.show', $report))->assertOk();
    }

    public function test_admins_cannot_deactivate_or_delete_themselves_or_other_admins(): void
    {
        $admin = $this->admin();
        $otherAdmin = $this->admin();

        $this->actingAs($admin)
            ->patchJson(route('admin.users.updateStatus', $admin), ['active' => false])
            ->assertStatus(422);
        $this->actingAs($admin)
            ->patchJson(route('admin.users.updateStatus', $otherAdmin), ['active' => false])
            ->assertStatus(422);
        $this->actingAs($admin)->delete(route('admin.users.destroy', $otherAdmin))->assertSessionHas('error');

        $this->assertTrue((bool) $admin->fresh()->active);
        $this->assertModelExists($otherAdmin);
    }

    public function test_a_deactivated_user_cannot_sign_in(): void
    {
        $user = User::factory()->create(['password' => bcrypt('a-long-password'), 'active' => false]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'a-long-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_signed_in_user_is_signed_out_once_deactivated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('dashboard'))->assertOk();

        $user->update(['active' => false]);

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_category_detail_url_no_longer_crashes(): void
    {
        $category = Category::create(['name' => 'Roads', 'name_ar' => 'الطرق']);

        $this->actingAs($this->admin())->get('/admin/categories/' . $category->id)->assertRedirect(route('admin.categories.edit', $category));
    }

    public function test_admins_are_turned_away_before_the_report_form(): void
    {
        $this->actingAs($this->admin())->get(route('reports.create'))->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->admin())->post(route('reports.store'), [])->assertForbidden();
    }
}
