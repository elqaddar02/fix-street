<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Js;
use Tests\TestCase;

/**
 * Bugs found by the mobile end-to-end run of the citizen pages.
 */
class MobileE2EFixesTest extends TestCase
{
    use RefreshDatabase;

    private function makeReport(User $owner): Report
    {
        return Report::create([
            'title' => 'Streetlight out',
            'description' => 'Streetlight out',
            'status' => 'OPEN',
            'user_id' => $owner->id,
            'category_id' => Category::create(['name' => 'Lighting', 'name_ar' => 'الإنارة'])->id,
            'city_id' => City::create(['name' => 'Rabat', 'active' => true])->id,
        ]);
    }

    public function test_confirm_and_comment_requests_use_the_apps_own_urls(): void
    {
        // They were hard-coded as "/reports/…", which 404s whenever the app
        // isn't served from the domain root.
        $report = $this->makeReport(User::factory()->create());

        $content = $this->actingAs(User::factory()->create())
            ->get(route('reports.show', $report))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString((string) Js::from(route('reports.likes.toggle', ['report' => '__ID__'])), $content);
        $this->assertStringNotContainsString('fetch(`/reports/', $content);
    }

    public function test_help_contact_form_delivers_the_message(): void
    {
        config(['app.contact_email' => 'team@madinup.test', 'mail.default' => 'array']);

        $this->post(route('help.contact'), [
            'topic' => 'feedback',
            'name' => 'Sara',
            'email' => 'sara@example.test',
            'subject' => 'Idea',
            'message' => 'Please add a map view.',
        ])->assertRedirect(route('help', ['tab' => 'contact']))->assertSessionHas('success');

        $sent = app('mailer')->getSymfonyTransport()->messages();
        $this->assertCount(1, $sent);
        $email = $sent[0]->getOriginalMessage();
        $this->assertSame('team@madinup.test', $email->getTo()[0]->getAddress());
        $this->assertSame('sara@example.test', $email->getReplyTo()[0]->getAddress());
        $this->assertStringContainsString('Please add a map view.', $email->getTextBody());
    }

    public function test_help_contact_form_rejects_bots_and_missing_fields(): void
    {
        config(['app.contact_email' => 'team@madinup.test', 'mail.default' => 'array']);

        $this->post(route('help.contact'), ['topic' => 'feedback', 'website' => 'spam.example'])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message', 'website']);

        $this->assertCount(0, app('mailer')->getSymfonyTransport()->messages());
    }

    public function test_successful_actions_are_confirmed_on_screen(): void
    {
        $owner = User::factory()->create();
        $report = $this->makeReport($owner);

        $this->actingAs($owner)
            ->followingRedirects()
            ->delete(route('reports.destroy', $report))
            ->assertOk()
            ->assertSee(__('flash.report_deleted'));
    }

    public function test_filtered_listing_returns_a_fresh_count(): void
    {
        $this->makeReport(User::factory()->create());

        $this->getJson(route('reports.index'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['html', 'summary']);
    }

    public function test_login_redirect_never_sends_people_off_site(): void
    {
        $this->get(route('login.redirect', ['intended' => 'https://evil.example/phish']));
        $this->assertSame(route('dashboard'), session('url.intended'));

        $this->get(route('login.redirect', ['intended' => url('/') . '.evil.example/phish']));
        $this->assertSame(route('dashboard'), session('url.intended'));

        $this->get(route('login.redirect', ['intended' => route('reports.index')]));
        $this->assertSame(route('reports.index'), session('url.intended'));
    }

    public function test_phones_get_a_tab_bar_with_new_report_in_reach(): void
    {
        $content = $this->actingAs(User::factory()->create())->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('aria-label="' . __('nav.main') . '"', $content);
        $this->assertStringContainsString(route('reports.create'), $content);
    }
}
