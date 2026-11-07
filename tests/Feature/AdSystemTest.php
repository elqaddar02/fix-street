<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\AdEvent;
use App\Models\AdPlacement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdSystemTest extends TestCase
{
    use RefreshDatabase;

    private AdPlacement $placement;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->placement = AdPlacement::create([
            'slug' => 'home_top',
            'name' => 'Home Page - Top Banner',
            'type' => 'horizontal',
        ]);
    }

    public function test_eligible_house_ad_is_rendered_and_impression_is_recorded(): void
    {
        $ad = Ad::create([
            'ad_placement_id' => $this->placement->id,
            'title' => 'Local Bakery Sponsor',
            'image' => 'ads/bakery.jpg',
            'target_url' => 'https://example.com/bakery',
            'provider' => Ad::PROVIDER_HOUSE,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('storage/ads/bakery.jpg')
            ->assertSee(route('ads.click', $ad), false);

        $this->assertDatabaseHas('ad_events', [
            'ad_id' => $ad->id,
            'type' => AdEvent::TYPE_IMPRESSION,
        ]);
    }

    public function test_clicking_an_ad_records_click_and_redirects_to_target(): void
    {
        $ad = Ad::create([
            'ad_placement_id' => $this->placement->id,
            'title' => 'Hardware Store',
            'image' => 'ads/hardware.jpg',
            'target_url' => 'https://example.com/hardware',
            'provider' => Ad::PROVIDER_HOUSE,
            'is_active' => true,
            'created_by' => $this->admin->id,
        ]);

        $this->get(route('ads.click', $ad))
            ->assertRedirect('https://example.com/hardware');

        $this->assertDatabaseHas('ad_events', [
            'ad_id' => $ad->id,
            'type' => AdEvent::TYPE_CLICK,
        ]);
    }

    public function test_inactive_ad_is_not_displayed_and_logs_no_impression(): void
    {
        $inactive = Ad::create([
            'ad_placement_id' => $this->placement->id,
            'title' => 'Expired Deal',
            'image' => 'ads/expired.jpg',
            'target_url' => 'https://example.com/expired',
            'provider' => Ad::PROVIDER_HOUSE,
            'is_active' => false,
            'created_by' => $this->admin->id,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('storage/ads/expired.jpg');

        $this->assertDatabaseMissing('ad_events', [
            'ad_id' => $inactive->id,
        ]);
    }
}
