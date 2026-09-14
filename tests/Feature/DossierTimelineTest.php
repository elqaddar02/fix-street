<?php

namespace Tests\Feature;

use App\Enums\ConfirmationType;
use App\Enums\DossierEventType;
use App\Enums\DossierStage;
use App\Exceptions\InvalidDossierTransition;
use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Dossier;
use App\Models\Report;
use App\Models\User;
use App\Services\DossierTimeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class DossierTimelineTest extends TestCase
{
    use RefreshDatabase;

    private function city(): City
    {
        return City::firstOrCreate(
            ['name' => 'Salé'],
            ['name_ar' => 'سلا', 'active' => true, 'latitude' => 34.0333, 'longitude' => -6.8333]
        );
    }

    private function makeDossier(string $slug = 'rue-test'): Dossier
    {
        return Dossier::create([
            'slug' => $slug,
            'short_code' => strtoupper(substr(md5($slug), 0, 6)),
            'title' => 'Potholes on a test street',
            'city_id' => $this->city()->id,
        ]);
    }

    private function makeReport(?Dossier $dossier, string $status = 'OPEN', ?User $owner = null): Report
    {
        $category = Category::firstOrCreate(['name' => 'Pothole'], ['name_ar' => 'حفرة']);
        $district = District::firstOrCreate(
            ['slug' => 'bettana'],
            ['city_id' => $this->city()->id, 'name_fr' => 'Bettana', 'name_ar' => 'بطانة']
        );

        $report = Report::create([
            'title' => 'Pothole near the pharmacy',
            'description' => 'Deep pothole near the pharmacy.',
            'status' => $status,
            'user_id' => ($owner ?? User::factory()->create())->id,
            'category_id' => $category->id,
            'city_id' => $this->city()->id,
            'district_id' => $district->id,
            'latitude' => 34.022,
            'longitude' => -6.811,
        ]);

        if ($dossier) {
            $report->dossier()->associate($dossier)->save();
        }

        return $report;
    }

    private function confirm(Dossier $dossier, ConfirmationType $type, ?User $user = null, ?string $photo = null, bool $hidden = false): void
    {
        $dossier->confirmations()->create([
            'user_id' => ($user ?? User::factory()->create())->id,
            'type' => $type,
            'photo' => $photo,
            'hidden_at' => $hidden ? now() : null,
        ]);
    }

    private function timeline(): DossierTimeline
    {
        return app(DossierTimeline::class);
    }

    private function recordVerifiedRepair(Dossier $dossier): void
    {
        $this->timeline()->record($dossier, DossierEventType::RepairReported);
        $this->confirm($dossier, ConfirmationType::Fixed, photo: 'dossiers/after.jpg');
        $this->confirm($dossier, ConfirmationType::Fixed);
        $this->confirm($dossier, ConfirmationType::Fixed);
        $this->timeline()->record($dossier, DossierEventType::VerifiedFixed);
    }

    public function test_a_new_case_starts_collecting(): void
    {
        $this->assertSame(DossierStage::Collecting, $this->makeDossier()->refresh()->stage);
    }

    public function test_events_move_the_case_through_its_stages(): void
    {
        $dossier = $this->makeDossier();

        $submitted = $this->timeline()->record($dossier, DossierEventType::SubmittedToAuthority, [
            'occurred_at' => now()->subDays(3),
            'channel' => 'in_person',
            'reference_number' => 'SAL-2026-0042',
        ]);
        $this->assertSame(DossierStage::Submitted, $dossier->stage);
        $this->assertSame('SAL-2026-0042', $submitted->reference_number);

        $this->timeline()->record($dossier, DossierEventType::AuthorityAcknowledged);
        $this->assertSame(DossierStage::Acknowledged, $dossier->stage);

        $this->timeline()->record($dossier, DossierEventType::WorkObserved);
        $this->assertSame(DossierStage::WorkObserved, $dossier->stage);

        $this->timeline()->record($dossier, DossierEventType::RepairReported);
        $this->assertSame(DossierStage::Repaired, $dossier->fresh()->stage);

        $this->assertSame(4, $dossier->events()->count());
    }

    public function test_events_that_do_not_change_the_stage_are_still_recorded(): void
    {
        $dossier = $this->makeDossier();

        $this->timeline()->record($dossier, DossierEventType::BaselineDocumented, ['description' => 'Residents say it has been there since 2025.']);
        $this->timeline()->record($dossier, DossierEventType::Note, ['description' => 'Flyers placed at the pharmacy.']);

        $this->assertSame(DossierStage::Collecting, $dossier->fresh()->stage);
        $this->assertSame(2, $dossier->events()->count());
    }

    public function test_a_case_cannot_skip_ahead(): void
    {
        $dossier = $this->makeDossier();

        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::AuthorityAcknowledged),
            InvalidDossierTransition::class
        );

        $this->assertSame(DossierStage::Collecting, $dossier->fresh()->stage);
        $this->assertSame(0, $dossier->events()->count());
    }

    public function test_events_cannot_be_dated_in_the_future(): void
    {
        $dossier = $this->makeDossier();

        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::SubmittedToAuthority, ['occurred_at' => now()->addDay()]),
            InvalidDossierTransition::class
        );
    }

    public function test_verification_counts_only_different_visible_neighbors_after_the_repair(): void
    {
        $dossier = $this->makeDossier();
        $repeatUser = User::factory()->create();

        $this->travel(-2)->days();
        $this->confirm($dossier, ConfirmationType::Fixed, photo: 'dossiers/too-early.jpg'); // before the repair
        $this->travelBack();

        $this->timeline()->record($dossier, DossierEventType::RepairReported);

        $this->confirm($dossier, ConfirmationType::Fixed, $repeatUser, 'dossiers/after.jpg');
        $this->confirm($dossier, ConfirmationType::Fixed, $repeatUser); // same person twice
        $this->confirm($dossier, ConfirmationType::Fixed, hidden: true); // hidden by moderation
        $this->confirm($dossier, ConfirmationType::NotFixed); // wrong type

        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::VerifiedFixed),
            InvalidDossierTransition::class
        );

        $this->confirm($dossier, ConfirmationType::Fixed);
        $this->confirm($dossier, ConfirmationType::Fixed);

        $this->timeline()->record($dossier, DossierEventType::VerifiedFixed);
        $this->assertSame(DossierStage::Verified, $dossier->fresh()->stage);
    }

    public function test_verification_needs_an_after_photo(): void
    {
        $dossier = $this->makeDossier();
        $this->timeline()->record($dossier, DossierEventType::RepairReported);
        $this->confirm($dossier, ConfirmationType::Fixed);
        $this->confirm($dossier, ConfirmationType::Fixed);
        $this->confirm($dossier, ConfirmationType::Fixed);

        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::VerifiedFixed),
            InvalidDossierTransition::class
        );

        $this->timeline()->record($dossier, DossierEventType::VerifiedFixed, ['attachment' => 'dossiers/admin-after.jpg']);
        $this->assertSame(DossierStage::Verified, $dossier->fresh()->stage);
    }

    public function test_durability_can_only_be_recorded_thirty_days_after_verification(): void
    {
        $dossier = $this->makeDossier();
        $this->recordVerifiedRepair($dossier);

        $this->travel(10)->days();
        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::DurabilityConfirmed),
            InvalidDossierTransition::class
        );

        $this->travel(21)->days();
        $this->timeline()->record($dossier, DossierEventType::DurabilityConfirmed);
        $this->assertSame(DossierStage::Durable, $dossier->fresh()->stage);
    }

    public function test_a_closed_case_only_accepts_notes_and_corrections(): void
    {
        $dossier = $this->makeDossier();
        $this->timeline()->record($dossier, DossierEventType::SubmittedToAuthority);
        $this->timeline()->record($dossier, DossierEventType::ClosedUnresolved, ['description' => 'No response after 90 days.']);
        $this->assertSame(DossierStage::ClosedUnresolved, $dossier->fresh()->stage);

        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::FollowUpSent),
            InvalidDossierTransition::class
        );

        $note = $this->timeline()->record($dossier, DossierEventType::Note, ['description' => 'Case study published.']);
        $this->timeline()->record($dossier, DossierEventType::Correction, [
            'corrects_event_id' => $note->id,
            'description' => 'The case study was published a day later.',
        ]);

        $this->assertSame(4, $dossier->events()->count());
    }

    public function test_a_correction_must_reference_an_event_of_the_same_case_and_explain_itself(): void
    {
        $dossier = $this->makeDossier();
        $other = $this->makeDossier('other-street');
        $otherEvent = $this->timeline()->record($other, DossierEventType::Note, ['description' => 'Other case.']);
        $ownEvent = $this->timeline()->record($dossier, DossierEventType::Note, ['description' => 'Own case.']);

        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::Correction, [
                'corrects_event_id' => $otherEvent->id,
                'description' => 'Wrong case.',
            ]),
            InvalidDossierTransition::class
        );

        $this->assertThrows(
            fn () => $this->timeline()->record($dossier, DossierEventType::Correction, ['corrects_event_id' => $ownEvent->id]),
            InvalidDossierTransition::class
        );
    }

    public function test_reopening_returns_to_the_furthest_authority_stage_reached(): void
    {
        $acknowledged = $this->makeDossier();
        $this->timeline()->record($acknowledged, DossierEventType::SubmittedToAuthority);
        $this->timeline()->record($acknowledged, DossierEventType::AuthorityAcknowledged);
        $this->timeline()->record($acknowledged, DossierEventType::RepairReported);
        $this->timeline()->record($acknowledged, DossierEventType::Reopened, ['description' => 'The pothole reopened after rain.']);
        $this->assertSame(DossierStage::Acknowledged, $acknowledged->fresh()->stage);

        $neverSubmitted = $this->makeDossier('never-submitted');
        $this->timeline()->record($neverSubmitted, DossierEventType::RepairReported);
        $this->timeline()->record($neverSubmitted, DossierEventType::Reopened);
        $this->assertSame(DossierStage::Collecting, $neverSubmitted->fresh()->stage);
    }

    public function test_timeline_events_cannot_be_edited_or_deleted(): void
    {
        $dossier = $this->makeDossier();
        $event = $this->timeline()->record($dossier, DossierEventType::Note, ['description' => 'Original.']);

        $this->assertThrows(fn () => $event->update(['description' => 'Rewritten.']), LogicException::class);
        $this->assertThrows(fn () => $event->delete(), LogicException::class);

        $this->assertSame('Original.', $event->fresh()->description);
    }

    public function test_linked_report_statuses_follow_the_case(): void
    {
        $dossier = $this->makeDossier();
        $linked = $this->makeReport($dossier);
        $rejected = $this->makeReport($dossier, 'REJECTED');
        $unlinked = $this->makeReport(null);

        $this->timeline()->record($dossier, DossierEventType::WorkObserved);
        $this->assertSame('IN_PROGRESS', $linked->fresh()->status);
        $this->assertSame('REJECTED', $rejected->fresh()->status);
        $this->assertSame('OPEN', $unlinked->fresh()->status);

        $this->recordVerifiedRepair($dossier);
        $this->assertSame('RESOLVED', $linked->fresh()->status);
        $this->assertSame('REJECTED', $rejected->fresh()->status);

        $this->timeline()->record($dossier, DossierEventType::Reopened);
        $this->assertSame('OPEN', $linked->fresh()->status);
        $this->assertSame('OPEN', $unlinked->fresh()->status);
    }

    public function test_confirmer_count_ignores_duplicates_hidden_and_old_confirmations(): void
    {
        $dossier = $this->makeDossier();
        $regular = User::factory()->create();

        $this->travel(-20)->days();
        $this->confirm($dossier, ConfirmationType::StillThere);
        $this->travelBack();

        $this->confirm($dossier, ConfirmationType::StillThere, $regular);
        $this->confirm($dossier, ConfirmationType::StillThere, $regular);
        $this->confirm($dossier, ConfirmationType::StillThere, hidden: true);

        $this->assertSame(2, $dossier->confirmerCount(ConfirmationType::StillThere));
        $this->assertSame(1, $dossier->confirmerCount(ConfirmationType::StillThere, now()->subDays(14)));
    }

    public function test_a_report_in_a_case_keeps_its_photo_and_location_and_cannot_be_deleted(): void
    {
        $owner = User::factory()->create();
        $dossier = $this->makeDossier();
        $report = $this->makeReport($dossier, owner: $owner);
        $otherCity = City::create(['name' => 'Rabat', 'name_ar' => 'الرباط', 'active' => true]);

        $this->actingAs($owner)->patch(route('reports.update', $report), [
            'title' => 'Pothole near the pharmacy (corrected title)',
            'description' => 'Deep pothole near the pharmacy.',
            'category_id' => $report->category_id,
            'city_id' => $otherCity->id,
            'latitude' => 33.0,
            'longitude' => -7.0,
        ])->assertRedirect();

        $report->refresh();
        $this->assertSame('Pothole near the pharmacy (corrected title)', $report->title);
        $this->assertEquals(34.022, (float) $report->latitude);
        $this->assertSame($this->city()->id, $report->city_id);

        $this->actingAs($owner)->delete(route('reports.destroy', $report))->assertForbidden();
        $this->assertModelExists($report);
    }
}
