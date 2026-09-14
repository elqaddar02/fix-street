<?php

namespace App\Services;

use App\Enums\ConfirmationType;
use App\Enums\DossierEventType;
use App\Enums\DossierStage;
use App\Exceptions\InvalidDossierTransition;
use App\Models\Dossier;
use App\Models\DossierEvent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The only way to add to a case's timeline and move it between stages.
 *
 * Every rule deciding whether an outcome can be claimed publicly lives here, so the admin
 * panel (or anything added later) cannot bypass it.
 */
class DossierTimeline
{
    public const MIN_FIXED_CONFIRMERS = 3;
    public const DURABILITY_DAYS = 30;

    /**
     * @param  array{occurred_at?: \DateTimeInterface|string, description?: string|null, channel?: string|null,
     *               reference_number?: string|null, attachment?: string|null, is_public?: bool,
     *               corrects_event_id?: int|null}  $data
     *
     * @throws InvalidDossierTransition
     */
    public function record(Dossier $dossier, DossierEventType $type, array $data = [], ?User $recordedBy = null): DossierEvent
    {
        return DB::transaction(function () use ($dossier, $type, $data, $recordedBy) {
            $dossier->refresh();

            $occurredAt = Carbon::parse($data['occurred_at'] ?? now());
            $this->guard($dossier, $type, $data, $occurredAt);

            $event = $dossier->events()->create([
                'type' => $type,
                'occurred_at' => $occurredAt,
                'description' => $data['description'] ?? null,
                'channel' => $data['channel'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'is_public' => $data['is_public'] ?? true,
                'recorded_by' => $recordedBy?->id,
                'corrects_event_id' => $type === DossierEventType::Correction ? $data['corrects_event_id'] : null,
            ]);

            $newStage = $type === DossierEventType::Reopened
                ? $this->stageAfterReopening($dossier)
                : $type->stageAfter();

            if ($newStage) {
                $dossier->forceFill(['stage' => $newStage])->save();
                $this->syncReportStatuses($dossier, $type);
            }

            return $event;
        });
    }

    private function guard(Dossier $dossier, DossierEventType $type, array $data, Carbon $occurredAt): void
    {
        $stage = $dossier->stage;

        if ($occurredAt->gt(now()->addMinutes(5))) {
            throw new InvalidDossierTransition('An event cannot be dated in the future.');
        }

        if ($stage->isTerminal() && ! $type->allowedOnTerminalStage()) {
            throw new InvalidDossierTransition("A {$stage->value} case only accepts notes and corrections.");
        }

        $allowedFrom = $type->allowedFrom();
        if ($allowedFrom !== null && ! in_array($stage, $allowedFrom, true)) {
            throw new InvalidDossierTransition("Cannot record {$type->value} while the case is {$stage->value}.");
        }

        match ($type) {
            DossierEventType::VerifiedFixed => $this->guardVerification($dossier, $data),
            DossierEventType::DurabilityConfirmed => $this->guardDurability($dossier, $occurredAt),
            DossierEventType::Correction => $this->guardCorrection($dossier, $data),
            default => null,
        };
    }

    /**
     * A repair is only "verified" once enough different neighbors confirmed it after the
     * repair, with at least one after-photo (from a confirmation or attached to this event).
     */
    private function guardVerification(Dossier $dossier, array $data): void
    {
        $repairedAt = $dossier->latestEventOfType(DossierEventType::RepairReported)?->occurred_at;
        if (! $repairedAt) {
            throw new InvalidDossierTransition('Verification needs a recorded repair first.');
        }

        $fixedSinceRepair = fn () => $dossier->visibleConfirmations()
            ->where('type', ConfirmationType::Fixed)
            ->where('created_at', '>=', $repairedAt);

        $confirmers = $fixedSinceRepair()->distinct()->count('user_id');
        if ($confirmers < self::MIN_FIXED_CONFIRMERS) {
            throw new InvalidDossierTransition(sprintf(
                'Verification needs at least %d different neighbors confirming the fix after the repair (currently %d).',
                self::MIN_FIXED_CONFIRMERS,
                $confirmers
            ));
        }

        $hasAfterPhoto = ! empty($data['attachment']) || $fixedSinceRepair()->whereNotNull('photo')->exists();
        if (! $hasAfterPhoto) {
            throw new InvalidDossierTransition('Verification needs at least one after-photo.');
        }
    }

    private function guardDurability(Dossier $dossier, Carbon $occurredAt): void
    {
        $verifiedAt = $dossier->latestEventOfType(DossierEventType::VerifiedFixed)?->occurred_at;

        if (! $verifiedAt || $occurredAt->lt($verifiedAt->copy()->addDays(self::DURABILITY_DAYS))) {
            throw new InvalidDossierTransition(sprintf(
                'The durability check can only be recorded %d days after verification.',
                self::DURABILITY_DAYS
            ));
        }
    }

    private function guardCorrection(Dossier $dossier, array $data): void
    {
        $correctsId = $data['corrects_event_id'] ?? null;

        if (! $correctsId || ! $dossier->events()->whereKey($correctsId)->exists()) {
            throw new InvalidDossierTransition('A correction must reference an event of the same case.');
        }

        if (empty($data['description'])) {
            throw new InvalidDossierTransition('A correction must explain what was wrong.');
        }
    }

    /**
     * A reopened case goes back to where the authority process had reached.
     */
    private function stageAfterReopening(Dossier $dossier): DossierStage
    {
        if ($dossier->latestEventOfType(DossierEventType::AuthorityAcknowledged)) {
            return DossierStage::Acknowledged;
        }

        if ($dossier->latestEventOfType(DossierEventType::SubmittedToAuthority)) {
            return DossierStage::Submitted;
        }

        return DossierStage::Collecting;
    }

    /**
     * Keep the linked reports' own statuses consistent with the case. Rejected reports are left alone.
     */
    private function syncReportStatuses(Dossier $dossier, DossierEventType $type): void
    {
        $reports = fn () => $dossier->reports()->where('status', '!=', 'REJECTED');

        match ($type) {
            DossierEventType::WorkObserved,
            DossierEventType::RepairReported => $reports()->where('status', 'OPEN')->update(['status' => 'IN_PROGRESS']),
            DossierEventType::VerifiedFixed => $reports()->update(['status' => 'RESOLVED']),
            DossierEventType::Reopened => $reports()->update(['status' => 'OPEN']),
            default => null,
        };
    }
}
