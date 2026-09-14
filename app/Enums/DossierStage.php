<?php

namespace App\Enums;

enum DossierStage: string
{
    case Collecting = 'collecting';
    case Submitted = 'submitted';
    case Acknowledged = 'acknowledged';
    case WorkObserved = 'work_observed';
    case Repaired = 'repaired';
    case Verified = 'verified';
    case Durable = 'durable';
    case ClosedUnresolved = 'closed_unresolved';

    /**
     * Durable and closed cases only accept notes and corrections.
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::Durable, self::ClosedUnresolved], true);
    }
}
