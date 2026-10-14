<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeReport(User $user, string $status = 'OPEN', string $category = 'Roads'): Report
    {
        $category = Category::firstOrCreate(['name' => $category], ['name_ar' => $category]);
        $city = City::firstOrCreate(['name' => 'Casablanca'], ['active' => true]);

        return Report::create([
            'title' => 'Nid-de-poule ' . uniqid(),
            'description' => 'Nid-de-poule',
            'status' => $status,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
        ]);
    }

    public function test_a_user_with_no_reports_sees_an_invitation_not_empty_charts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('dashboard.empty.headline'))
            // No breakdown panels to render at zero.
            ->assertDontSee(__('dashboard.breakdown.title'))
            ->assertDontSee(__('dashboard.table.title'));
    }

    public function test_the_new_report_action_is_present_before_any_statistics(): void
    {
        // It used to sit inside its own card below a full-height hero, so the
        // primary action was off screen on arrival.
        $user = User::factory()->create();
        $this->makeReport($user);

        $content = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($content, __('dashboard.table.title')),
            strpos($content, route('reports.create')),
            'The create-report action should appear before the reports list.'
        );
    }

    public function test_breakdown_appears_once_there_are_reports(): void
    {
        $user = User::factory()->create();
        $this->makeReport($user, 'OPEN');
        $this->makeReport($user, 'RESOLVED');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('dashboard.breakdown.title'))
            ->assertSee(__('dashboard.table.title'))
            ->assertDontSee(__('dashboard.empty.headline'));
    }

    public function test_category_breakdown_is_skipped_when_there_is_only_one_category(): void
    {
        // A bar chart of a single bar carries no information.
        $user = User::factory()->create();
        $this->makeReport($user, 'OPEN', 'Roads');
        $this->makeReport($user, 'OPEN', 'Roads');

        $content = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertSame(
            1,
            substr_count($content, __('dashboard.breakdown.categories')),
            'The single-category summary should replace the bar list, not appear alongside it.'
        );
    }

    public function test_a_user_only_sees_their_own_reports(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $mine = $this->makeReport($user);
        $theirs = $this->makeReport($other);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($mine->title)
            ->assertDontSee($theirs->title);
    }
}
