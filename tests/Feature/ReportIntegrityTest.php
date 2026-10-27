<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Dossier;
use App\Models\Quartier;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\MakesReportPhotos;
use Tests\TestCase;

/**
 * Findings F-03, F-05, F-06, F-07, F-18 and F-19 of the Arabic end-to-end run
 * (docs/testing/arabic-e2e-results.md): the server, not the page, decides
 * whether a report is complete and where it belongs.
 */
class ReportIntegrityTest extends TestCase
{
    use MakesReportPhotos;
    use RefreshDatabase;

    private City $sale;
    private District $tabriquet;
    private Quartier $hayKarima;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->sale = City::create(['name' => 'Salé', 'name_ar' => 'سلا', 'active' => true, 'latitude' => 34.0386, 'longitude' => -6.8159]);
        $this->tabriquet = District::create(['city_id' => $this->sale->id, 'name_fr' => 'Tabriquet', 'name_ar' => 'تابريكت', 'slug' => 'tabriquet', 'lat' => 34.045, 'lng' => -6.80]);
        $this->hayKarima = Quartier::create(['district_id' => $this->tabriquet->id, 'name_fr' => 'Hay Karima', 'name_ar' => 'حي كريمة', 'slug' => 'hay-karima', 'latitude' => 34.048, 'longitude' => -6.795]);
        $this->category = Category::create(['name' => 'Pothole', 'name_ar' => 'حفرة']);
    }

    private function citizen(): User
    {
        return User::factory()->create();
    }

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'title' => 'Trou devant l’école',
            'category_id' => $this->category->id,
            'city_id' => $this->sale->id,
            'latitude' => 34.0481,
            'longitude' => -6.7951,
            'image' => $this->reportPhoto(),
        ];
    }

    public function test_a_report_needs_a_photo(): void
    {
        $payload = $this->payload();
        unset($payload['image']);

        $this->actingAs($this->citizen())->post(route('reports.store'), $payload)
            ->assertSessionHasErrors(['image' => __('validation.image_missing')]);

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_a_report_cannot_be_filed_in_an_inactive_city(): void
    {
        $rabat = City::create(['name' => 'Rabat', 'active' => false, 'latitude' => 34.02, 'longitude' => -6.84]);

        $this->actingAs($this->citizen())->post(route('reports.store'), $this->payload(['city_id' => $rabat->id]))
            ->assertSessionHasErrors('city_id');

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_a_pin_outside_morocco_is_refused(): void
    {
        $this->actingAs($this->citizen())->post(route('reports.store'), $this->payload(['latitude' => 48.8584, 'longitude' => 2.2945]))
            ->assertSessionHasErrors(['latitude', 'longitude']);

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_the_server_fills_in_the_area_of_a_hand_placed_pin(): void
    {
        // The form sends the city only, as it does when the pin is placed by hand.
        $this->actingAs($this->citizen())->post(route('reports.store'), $this->payload())
            ->assertRedirect(route('dashboard'));

        $report = Report::sole();
        $this->assertSame($this->tabriquet->id, $report->district_id);
        $this->assertSame($this->hayKarima->id, $report->quartier_id);
        Storage::disk('public')->assertExists($report->image);
    }

    public function test_moving_the_pin_on_edit_updates_the_area(): void
    {
        $owner = $this->citizen();
        $report = Report::create([
            'title' => 'Old place', 'description' => 'Old place', 'status' => 'OPEN', 'user_id' => $owner->id,
            'category_id' => $this->category->id, 'city_id' => $this->sale->id, 'latitude' => 34.0386, 'longitude' => -6.8159,
        ]);

        $this->actingAs($owner)->patch(route('reports.update', $report), [
            'title' => 'New place', 'category_id' => $this->category->id, 'city_id' => $this->sale->id,
            'latitude' => 34.0481, 'longitude' => -6.7951,
        ])->assertRedirect(route('dashboard'));

        $this->assertSame($this->hayKarima->id, $report->fresh()->quartier_id);
    }

    public function test_a_report_keeps_its_city_after_the_city_is_switched_off(): void
    {
        $owner = $this->citizen();
        $report = Report::create([
            'title' => 'Before', 'description' => 'Before', 'status' => 'OPEN', 'user_id' => $owner->id,
            'category_id' => $this->category->id, 'city_id' => $this->sale->id, 'latitude' => 34.0481, 'longitude' => -6.7951,
        ]);
        $this->sale->update(['active' => false]);

        $this->actingAs($owner)->patch(route('reports.update', $report), [
            'title' => 'After', 'category_id' => $this->category->id, 'city_id' => $this->sale->id,
            'latitude' => 34.0481, 'longitude' => -6.7951,
        ])->assertSessionHasNoErrors();

        $this->assertSame('After', $report->fresh()->title);
    }

    public function test_reporters_and_admins_cannot_confirm(): void
    {
        $owner = $this->citizen();
        $report = Report::create([
            'title' => 'Mine', 'description' => 'Mine', 'status' => 'OPEN', 'user_id' => $owner->id,
            'category_id' => $this->category->id, 'city_id' => $this->sale->id, 'latitude' => 34.0481, 'longitude' => -6.7951,
        ]);
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($owner)->postJson(route('reports.likes.toggle', $report))->assertForbidden();
        $this->actingAs($admin)->postJson(route('reports.likes.toggle', $report))->assertForbidden();
        $this->assertSame(0, $report->likes()->count());

        // A neighbour can.
        $this->actingAs($this->citizen())->postJson(route('reports.likes.toggle', $report))
            ->assertOk()->assertJson(['liked' => true, 'likes_count' => 1]);

        // The reporter sees the count and a note, not a button.
        $this->actingAs($owner)->get(route('reports.show', $report))
            ->assertSee(__('report.own_report_note'))
            ->assertDontSee('x-data="reportLike(', false);
    }

    public function test_confirming_without_javascript_answers_in_the_page_language(): void
    {
        $report = Report::create([
            'title' => 'Theirs', 'description' => 'Theirs', 'status' => 'OPEN', 'user_id' => $this->citizen()->id,
            'category_id' => $this->category->id, 'city_id' => $this->sale->id, 'latitude' => 34.0481, 'longitude' => -6.7951,
        ]);

        $this->actingAs($this->citizen())->withSession(['locale' => 'ar'])
            ->post(route('reports.likes.toggle', $report))
            ->assertSessionHas('success', 'شكرًا، تم تسجيل تأكيدك.');
    }

    public function test_a_report_in_a_case_file_offers_no_delete_button(): void
    {
        $owner = $this->citizen();
        $dossier = Dossier::create(['slug' => 'case', 'short_code' => 'CASE01', 'title' => 'Case', 'city_id' => $this->sale->id]);
        $locked = Report::create([
            'title' => 'Locked', 'description' => 'Locked', 'status' => 'OPEN', 'user_id' => $owner->id,
            'category_id' => $this->category->id, 'city_id' => $this->sale->id, 'latitude' => 34.0481, 'longitude' => -6.7951,
        ]);
        $locked->dossier()->associate($dossier)->save();
        $free = Report::create([
            'title' => 'Free', 'description' => 'Free', 'status' => 'OPEN', 'user_id' => $owner->id,
            'category_id' => $this->category->id, 'city_id' => $this->sale->id, 'latitude' => 34.0481, 'longitude' => -6.7951,
        ]);

        $this->actingAs($owner)->get(route('dashboard'))
            ->assertSee(route('reports.destroy', $free), false)
            ->assertDontSee('action="' . route('reports.destroy', $locked) . '"', false);
    }
}
