<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCardsTest extends TestCase
{
    use RefreshDatabase;

    private function makeReport(?string $description = null): Report
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Roads', 'name_ar' => 'الطرق']);
        $city = City::create(['name' => 'Casablanca', 'active' => true]);

        $title = 'Un nid-de-poule dangereux';

        return Report::create([
            'title' => $title,
            // store() mirrors the title into description for new reports.
            'description' => $description ?? $title,
            'status' => 'OPEN',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
        ]);
    }

    public function test_home_does_not_print_the_same_sentence_twice(): void
    {
        $report = $this->makeReport();

        $content = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(
            1,
            substr_count($content, $report->title),
            'Title and description hold the same text, so a card should print it once.'
        );
    }

    public function test_listing_does_not_print_the_same_sentence_twice(): void
    {
        $report = $this->makeReport();

        $content = $this->get(route('reports.index'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($content, $report->title));
    }

    public function test_a_genuinely_different_description_is_still_shown(): void
    {
        // Older reports, and edits, can carry a description of their own.
        $report = $this->makeReport('Il est profond et proche de l\'école.');

        $this->get(route('reports.show', $report))
            ->assertOk()
            ->assertSee($report->description);
    }

    public function test_cards_show_confirmations_rather_than_a_comment_count(): void
    {
        $this->makeReport();

        $this->get(route('home'))
            ->assertOk()
            // The heart/comment bubble is gone; the count is labelled as confirmations.
            ->assertSee(trans_choice('report.confirmations_count', 0, ['count' => 0]));
    }
}
