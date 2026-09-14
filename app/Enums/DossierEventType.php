<?php

namespace App\Enums;

enum DossierEventType: string
{
    case BaselineDocumented = 'baseline_documented';
    case SubmittedToAuthority = 'submitted_to_authority';
    case AuthorityAcknowledged = 'authority_acknowledged';
    case FollowUpSent = 'follow_up_sent';
    case Escalated = 'escalated';
    case WorkObserved = 'work_observed';
    case RepairReported = 'repair_reported';
    case VerifiedFixed = 'verified_fixed';
    case DurabilityConfirmed = 'durability_confirmed';
    case Reopened = 'reopened';
    case ClosedUnresolved = 'closed_unresolved';
    case Note = 'note';
    case Correction = 'correction';

    /**
     * Stages this event may be recorded from. Null means any non-terminal stage.
     *
     * @return list<DossierStage>|null
     */
    public function allowedFrom(): ?array
    {
        return match ($this) {
            self::SubmittedToAuthority => [DossierStage::Collecting],
            self::AuthorityAcknowledged => [DossierStage::Submitted],
            self::WorkObserved => [DossierStage::Collecting, DossierStage::Submitted, DossierStage::Acknowledged],
            self::RepairReported => [DossierStage::Collecting, DossierStage::Submitted, DossierStage::Acknowledged, DossierStage::WorkObserved],
            self::VerifiedFixed => [DossierStage::Repaired],
            self::DurabilityConfirmed => [DossierStage::Verified],
            self::Reopened => [DossierStage::Repaired, DossierStage::Verified],
            default => null,
        };
    }

    /**
     * Stage the case moves to once this event is recorded, or null when the stage is unchanged.
     * Reopened depends on the case's history, so DossierTimeline resolves it.
     */
    public function stageAfter(): ?DossierStage
    {
        return match ($this) {
            self::SubmittedToAuthority => DossierStage::Submitted,
            self::AuthorityAcknowledged => DossierStage::Acknowledged,
            self::WorkObserved => DossierStage::WorkObserved,
            self::RepairReported => DossierStage::Repaired,
            self::VerifiedFixed => DossierStage::Verified,
            self::DurabilityConfirmed => DossierStage::Durable,
            self::ClosedUnresolved => DossierStage::ClosedUnresolved,
            default => null,
        };
    }

    public function allowedOnTerminalStage(): bool
    {
        return in_array($this, [self::Note, self::Correction], true);
    }
}
