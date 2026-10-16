<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Publishing (reports, confirmations, comments) requires a confirmed email.
 */
class EmailVerificationGateTest extends TestCase
{
    use RefreshDatabase;

    private function report(): Report
    {
        return Report::create([
            'title' => 'Broken bench',
            'description' => 'Broken bench',
            'status' => 'OPEN',
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::create(['name' => 'Furniture', 'name_ar' => 'أثاث'])->id,
            'city_id' => City::create(['name' => 'Rabat', 'active' => true])->id,
        ]);
    }

    public function test_registering_sends_a_verification_email(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'Nadia',
            'email' => 'nadia@example.test',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
        ]);

        Notification::assertSentTo(User::where('email', 'nadia@example.test')->first(), VerifyEmail::class);
    }

    public function test_unverified_users_cannot_publish_until_they_confirm(): void
    {
        $user = User::factory()->unverified()->create();
        $report = $this->report();

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('verification.notice'));
        $this->actingAs($user)->get(route('reports.create'))->assertRedirect(route('verification.notice'));
        $this->actingAs($user)->post(route('reports.store'), [])->assertRedirect(route('verification.notice'));
        $this->actingAs($user)->postJson(route('reports.likes.toggle', $report))->assertForbidden();
    }

    public function test_unverified_users_can_still_browse_and_fix_their_email(): void
    {
        $user = User::factory()->unverified()->create();
        $report = $this->report();

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
        $this->actingAs($user)->get(route('reports.index'))->assertOk();

        // The confirm button becomes a way to the "check your email" screen.
        $this->actingAs($user)->get(route('reports.show', $report))
            ->assertOk()
            ->assertSee(route('verification.notice'), false)
            ->assertSee(__('signin.verify_banner', ['email' => $user->email]));
    }

    public function test_verified_users_publish_as_before(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->actingAs($user)->get(route('reports.create'))->assertOk();
        $this->actingAs($user)->get(route('home'))->assertDontSee(__('signin.resend_link'));
    }
}
