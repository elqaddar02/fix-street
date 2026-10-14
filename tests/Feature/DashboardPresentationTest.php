<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardPresentationTest extends TestCase
{
    use RefreshDatabase;

    private const PHOTO_PATH = 'dashboard-presentation-test.jpg';

    protected function tearDown(): void
    {
        Storage::disk('public')->delete(self::PHOTO_PATH);
        parent::tearDown();
    }

    public function test_dashboard_uses_horizontal_report_rows_and_shows_each_nonzero_status(): void
    {
        $user = User::factory()->create();
        Storage::disk('public')->put(self::PHOTO_PATH, 'test image placeholder');

        $this->makeReport($user, 'OPEN', 'Roads', self::PHOTO_PATH);
        $this->makeReport($user, 'IN_PROGRESS', 'Lighting');
        $this->makeReport($user, 'RESOLVED', 'Roads');
        $this->makeReport($user, 'REJECTED', 'Other');

        $content = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

        $this->assertTrue(str_contains($content, '<table class="w-full min-w-[640px] text-start">'), 'Reports should use a horizontal table layout.');
        $this->assertTrue(str_contains($content, 'overflow-x-auto'), 'The report table should scroll within its own container on narrow screens.');
        $this->assertTrue(str_contains($content, 'storage/' . self::PHOTO_PATH), 'A report photo should be shown when its file exists.');
        $this->assertTrue(str_contains($content, 'M3 19V5a2 2 0 012-2h14a2 2 0 012 2v14'), 'Reports without a photo should show a neutral placeholder.');
        $this->assertTrue(str_contains($content, 'id="status-breakdown-title"'), 'The report status summary should be rendered.');

        foreach (['OPEN', 'IN_PROGRESS', 'RESOLVED', 'REJECTED'] as $status) {
            $this->assertTrue(str_contains($content, __('dashboard.status.' . strtolower($status))), 'The ' . $status . ' status should appear in the breakdown.');
        }

        $this->assertTrue(str_contains($content, 'Nid-de-poule Roads'));
        $this->assertTrue(str_contains($content, 'Nid-de-poule Lighting'));
        $this->assertTrue(str_contains($content, 'Nid-de-poule Other'));
    }

    public function test_dashboard_create_action_uses_the_active_locale(): void
    {
        $user = User::factory()->create();

        foreach (['fr' => 'Nouveau signalement', 'ar' => 'إبلاغ جديد'] as $locale => $label) {
            App::setLocale($locale);
            $this->actingAs($user)->get(route('dashboard'))->assertOk()->assertSee($label);
        }
    }

    private function makeReport(User $user, string $status, string $categoryName, ?string $image = null): Report
    {
        $category = Category::firstOrCreate(['name' => $categoryName], ['name_ar' => $categoryName]);
        $city = City::firstOrCreate(['name' => 'Casablanca'], ['active' => true]);

        return Report::create([
            'title' => 'Nid-de-poule ' . $categoryName,
            'description' => 'Report presentation test',
            'image' => $image,
            'status' => $status,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
        ]);
    }
}
