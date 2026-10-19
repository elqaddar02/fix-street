<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['password' => bcrypt('a-long-password')]);
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    public function test_admin_lands_on_the_admin_dashboard_even_after_visiting_my_reports(): void
    {
        $admin = $this->admin();

        // Visiting "My Reports" while signed out stores it as the intended URL.
        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'a-long-password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_heading_to_an_admin_page_still_gets_there(): void
    {
        $admin = $this->admin();

        $this->get(route('admin.users.index'))->assertRedirect(route('login'));

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'a-long-password'])
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_my_reports_sends_admins_to_their_dashboard(): void
    {
        $this->actingAs($this->admin())->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_citizens_still_return_to_where_they_were(): void
    {
        $user = User::factory()->create(['password' => bcrypt('a-long-password')]);

        $this->get(route('reports.create'))->assertRedirect(route('login'));

        $this->post(route('login'), ['email' => $user->email, 'password' => 'a-long-password'])
            ->assertRedirect(route('reports.create'));
    }
}
