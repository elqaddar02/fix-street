<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_in_user_can_reach_their_own_reports_from_any_page(): void
    {
        // The reason this exists: the nav previously had no link to the
        // dashboard at all, so once a user clicked "All Reports" there was no
        // way back to their own reports short of editing the URL.
        $user = User::factory()->create();

        foreach (['home', 'reports.index', 'help'] as $page) {
            $this->actingAs($user)
                ->get(route($page))
                ->assertOk()
                ->assertSee(route('dashboard'), false);
        }
    }

    public function test_guest_is_not_offered_my_reports(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee(route('dashboard'), false)
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false);
    }

    public function test_home_is_reachable_by_a_labelled_link_not_only_the_logo(): void
    {
        $this->get(route('reports.index'))
            ->assertOk()
            ->assertSee(__('nav.home'), false);
    }

    public function test_admin_is_offered_the_admin_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_non_admin_is_not_offered_the_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee(route('admin.dashboard'), false);
    }

    public function test_account_actions_are_present_for_mobile_users(): void
    {
        // The account cluster used to be desktop-only (hidden sm:flex), which
        // left phone users with no way to log out or open their profile.
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'))->assertOk();

        $mobileMenu = strstr($response->getContent(), 'id="mobile-menu"');

        $this->assertStringContainsString(route('profile.edit'), $mobileMenu);
        $this->assertStringContainsString(route('logout'), $mobileMenu);
        $this->assertStringContainsString(route('dashboard'), $mobileMenu);
    }
}
