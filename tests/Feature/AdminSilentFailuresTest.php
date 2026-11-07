<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Findings F-16, F-31, F-36 and F-38 of the Arabic end-to-end run: things
 * that looked like they worked but didn't.
 */
class AdminSilentFailuresTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function report(string $status = 'OPEN'): Report
    {
        return Report::create([
            'title' => 'Trou', 'description' => 'Trou', 'status' => $status,
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::firstOrCreate(['name' => 'Pothole'], ['name_ar' => 'حفرة'])->id,
            'city_id' => City::firstOrCreate(['name' => 'Salé'], ['active' => true])->id,
        ]);
    }

    public function test_bulk_status_applies_the_chosen_status(): void
    {
        [$a, $b] = [$this->report(), $this->report()];

        // The real form also sends every row's own "status" menu; it must not win.
        $this->actingAs($this->admin())->post(route('admin.reports.bulkUpdateStatus'), [
            'report_ids' => [$a->id, $b->id],
            'bulk_status' => 'RESOLVED',
            'status' => 'OPEN',
        ])->assertSessionHas('success', __('admin.flash_bulk_status', ['count' => 2]));

        $this->assertSame(['RESOLVED', 'RESOLVED'], [$a->fresh()->status, $b->fresh()->status]);
        $this->assertDatabaseHas('audit_logs', ['model_id' => $a->id, 'description' => 'OPEN → RESOLVED (bulk)']);
    }

    public function test_the_bulk_form_sends_its_own_field(): void
    {
        $this->report();

        $this->actingAs($this->admin())->get(route('admin.reports.index'))
            ->assertSee('id="bulk-status" name="bulk_status"', false);
    }

    public function test_an_expired_form_returns_with_a_notice_instead_of_a_419_page(): void
    {
        // Tests switch the CSRF check off, so raise the same exception it would.
        Route::middleware('web')->post('/_test/expired-form', fn () => throw new TokenMismatchException('CSRF token mismatch.'));

        $this->from('/reports')->post('/_test/expired-form', ['title' => 'Kept'])
            ->assertRedirect('/reports')
            ->assertSessionHas('notice', __('signin.session_expired'))
            ->assertSessionHasInput('title', 'Kept');

        // Background requests still get a plain 419 they can handle.
        $this->postJson('/_test/expired-form')->assertStatus(419);
    }

    public function test_a_citizen_is_not_sent_to_an_admin_page_after_signing_in(): void
    {
        $citizen = User::factory()->create(['password' => bcrypt('secret-password')]);

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post(route('login'), ['email' => $citizen->email, 'password' => 'secret-password'])
            ->assertRedirect(route('dashboard'));
    }

    public function test_a_citizen_still_returns_to_the_report_they_wanted(): void
    {
        $citizen = User::factory()->create(['password' => bcrypt('secret-password')]);
        $report = $this->report();

        $this->get(route('login.redirect', ['intended' => route('reports.show', $report)]));

        $this->post(route('login'), ['email' => $citizen->email, 'password' => 'secret-password'])
            ->assertRedirect(route('reports.show', $report));
    }

    public function test_the_history_speaks_the_page_language(): void
    {
        $report = $this->report();
        $admin = $this->admin();
        $this->actingAs($admin)->patchJson(route('admin.reports.updateStatus', $report), ['status' => 'REJECTED'])->assertOk();

        $this->actingAs($admin)->withSession(['locale' => 'ar'])->get(route('admin.reports.show', $report))
            ->assertSee('مفتوح ← مرفوض')
            ->assertDontSee('OPEN → REJECTED');
    }
}
