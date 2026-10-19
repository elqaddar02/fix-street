<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminModerationTest extends TestCase
{
    use RefreshDatabase;

    private const PHOTO = 'reports/admin-moderation-test.jpg';

    protected function tearDown(): void
    {
        Storage::disk('public')->delete(self::PHOTO);
        parent::tearDown();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function report(array $attributes = []): Report
    {
        return Report::create($attributes + [
            'title' => 'Broken streetlight',
            'description' => 'Broken streetlight',
            'status' => 'OPEN',
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::firstOrCreate(['name' => 'Lighting'], ['name_ar' => 'الإنارة'])->id,
            'city_id' => City::firstOrCreate(['name' => 'Rabat'], ['active' => true])->id,
        ]);
    }

    public function test_the_report_page_shows_what_the_moderator_needs(): void
    {
        Storage::disk('public')->put(self::PHOTO, 'photo');
        $report = $this->report(['image' => self::PHOTO, 'latitude' => 34.02, 'longitude' => -6.84]);
        $report->likes()->create(['user_id' => User::factory()->create()->id]);

        $this->actingAs($this->admin())->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('storage/' . self::PHOTO, false)
            ->assertSee('admin-report-map', false)
            ->assertSee('Confirmations')
            ->assertSee($report->user->name);
    }

    public function test_status_changes_are_recorded_in_the_history(): void
    {
        $report = $this->report();
        $admin = $this->admin();

        $this->actingAs($admin)->patchJson(route('admin.reports.updateStatus', $report), ['status' => 'RESOLVED'])->assertOk();

        $this->actingAs($admin)->get(route('admin.reports.show', $report))
            ->assertSee('OPEN → RESOLVED')
            ->assertSee($admin->name);
    }

    public function test_reports_can_be_found_by_reference_and_filtered(): void
    {
        $wanted = $this->report(['title' => 'Pothole near the school']);
        $other = $this->report([
            'title' => 'Overflowing bin',
            'category_id' => Category::create(['name' => 'Waste', 'name_ar' => 'نفايات'])->id,
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.reports.index', ['q' => '#' . $wanted->id]))
            ->assertSee('Pothole near the school')
            ->assertDontSee('Overflowing bin');

        $this->actingAs($admin)->get(route('admin.reports.index', ['category' => $other->category_id]))
            ->assertSee('Overflowing bin')
            ->assertDontSee('Pothole near the school');
    }

    public function test_deleting_a_report_removes_its_photo_and_is_logged(): void
    {
        Storage::disk('public')->put(self::PHOTO, 'photo');
        $report = $this->report(['image' => self::PHOTO]);

        $this->actingAs($this->admin())->delete(route('admin.reports.destroy', $report))->assertRedirect(route('admin.reports.index'));

        $this->assertModelMissing($report);
        Storage::disk('public')->assertMissing(self::PHOTO);
        $this->assertDatabaseHas('audit_logs', ['action' => 'Delete', 'model' => 'Report', 'model_id' => $report->id]);
    }
}
