<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PilotReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function makeReport(User $owner, string $status = 'OPEN'): Report
    {
        $category = Category::create(['name' => 'Pothole', 'name_ar' => 'حفرة']);
        $city = City::create(['name' => 'Salé', 'name_ar' => 'سلا', 'active' => true, 'latitude' => 34.0333, 'longitude' => -6.8333]);
        $district = District::create(['city_id' => $city->id, 'name_fr' => 'Bettana', 'name_ar' => 'بطانة', 'slug' => 'bettana']);

        return Report::create([
            'title' => 'Pothole on the main street',
            'description' => 'Deep pothole near the pharmacy.',
            'status' => $status,
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'district_id' => $district->id,
            'latitude' => 34.022,
            'longitude' => -6.811,
        ]);
    }

    public function test_home_page_states_the_platform_is_independent(): void
    {
        $this->makeReport(User::factory()->create());

        $this->get('/')
            ->assertOk()
            ->assertSee('Madinup is an independent citizen initiative.')
            ->assertDontSee('Official City Street Maintenance Portal');
    }

    public function test_rejected_comments_are_hidden_on_the_report_page(): void
    {
        $report = $this->makeReport(User::factory()->create());
        $commenter = User::factory()->create();

        $report->comments()->create(['user_id' => $commenter->id, 'comment' => 'Unreviewed comment text', 'approved' => null]);
        $report->comments()->create(['user_id' => $commenter->id, 'comment' => 'Approved comment text', 'approved' => true]);
        $report->comments()->create(['user_id' => $commenter->id, 'comment' => 'Rejected comment text', 'approved' => false]);

        $this->get(route('reports.show', $report))
            ->assertOk()
            ->assertSee('Unreviewed comment text')
            ->assertSee('Approved comment text')
            ->assertDontSee('Rejected comment text');
    }

    public function test_admin_can_mark_a_report_as_rejected(): void
    {
        $report = $this->makeReport(User::factory()->create());
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.reports.updateStatus', $report), ['status' => 'REJECTED'])
            ->assertRedirect();

        $this->assertSame('REJECTED', $report->fresh()->status);
    }

    public function test_dashboard_renders_with_a_rejected_report(): void
    {
        $owner = User::factory()->create();
        $this->makeReport($owner, 'REJECTED');

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rejected');
    }

    public function test_comment_posting_is_rate_limited(): void
    {
        $report = $this->makeReport(User::factory()->create());
        $user = User::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($user)
                ->postJson(route('reports.comments.store', $report), ['comment' => "Comment {$i}"])
                ->assertOk();
        }

        $this->actingAs($user)
            ->postJson(route('reports.comments.store', $report), ['comment' => 'One too many'])
            ->assertStatus(429);
    }
}
