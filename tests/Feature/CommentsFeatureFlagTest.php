<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsFeatureFlagTest extends TestCase
{
    use RefreshDatabase;

    private function report(): Report
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Roads', 'name_ar' => 'الطرق']);
        $city = City::create(['name' => 'Casablanca', 'active' => true]);

        return Report::create([
            'title' => 'Nid-de-poule',
            'description' => 'Nid-de-poule',
            'status' => 'OPEN',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
        ]);
    }

    public function test_comments_are_hidden_when_the_feature_is_off(): void
    {
        config(['features.comments' => false]);
        $report = $this->report();

        ReportComment::create([
            'report_id' => $report->id,
            'user_id' => $report->user_id,
            'comment' => 'Toujours pas réparé',
            'approved' => true,
        ]);

        $this->get(route('reports.show', $report))
            ->assertOk()
            ->assertDontSee('id="comment-form"', false)
            ->assertDontSee('Toujours pas réparé');
    }

    public function test_posting_a_comment_is_refused_when_the_feature_is_off(): void
    {
        // Hiding the form is not enough: the endpoint must refuse too.
        config(['features.comments' => false]);
        $report = $this->report();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('reports.comments.store', $report), ['comment' => 'Bonjour'])
            ->assertNotFound();

        $this->assertDatabaseCount('report_comments', 0);
    }

    public function test_comments_come_back_when_the_feature_is_on(): void
    {
        // Nothing is deleted, so flipping the flag restores the feature.
        config(['features.comments' => true]);
        $report = $this->report();
        $user = User::factory()->create();

        $this->get(route('reports.show', $report))
            ->assertOk()
            ->assertSee('id="comment-form"', false);

        // Non-JSON posts redirect back on success.
        $this->actingAs($user)
            ->post(route('reports.comments.store', $report), ['comment' => 'Bonjour'])
            ->assertRedirect();

        $this->assertDatabaseCount('report_comments', 1);
    }

    public function test_existing_comments_survive_while_hidden(): void
    {
        config(['features.comments' => false]);
        $report = $this->report();

        ReportComment::create([
            'report_id' => $report->id,
            'user_id' => $report->user_id,
            'comment' => 'Ancien commentaire',
            'approved' => true,
        ]);

        $this->get(route('reports.show', $report))->assertOk();

        $this->assertDatabaseCount('report_comments', 1);
    }
}
