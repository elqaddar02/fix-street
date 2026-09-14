<?php

namespace App\Enums;

/**
 * How strongly the evidence shows Madinup contributed to a case's outcome, recorded when it closes.
 */
enum ContributionStrength: string
{
    // The authority cited the case, or the repair targeted exactly these sites after submission.
    case Strong = 'strong';
    // The repair followed the submission, without an explicit link.
    case Moderate = 'moderate';
    // Coincidence, or the works were already planned.
    case None = 'none';
}
