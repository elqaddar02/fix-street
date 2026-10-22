<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagination_speaks_the_page_language(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        // Cities paginate by 10, so 12 gives a second page.
        foreach (range(1, 12) as $i) {
            City::create(['name' => "Ville {$i}", 'active' => true]);
        }

        $this->actingAs($admin)->withSession(['locale' => 'fr'])->get(route('admin.cities.index'))
            ->assertSee('1–10 sur 12')
            ->assertSee(__('pagination.next', [], 'fr'))
            ->assertDontSee('Showing');

        $this->actingAs($admin)->withSession(['locale' => 'ar'])->get(route('admin.cities.index', ['page' => 2]))
            // The range is isolated as a left-to-right unit, or the dash splits it in RTL.
            ->assertSee("\u{2066}11–12\u{2069} من 12")
            ->assertSee('aria-current="page"', false);
    }
}
