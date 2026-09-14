<?php

namespace App\Exceptions;

use DomainException;

/**
 * Thrown when a timeline event would break a case's rules (skipping a stage, claiming a
 * verified repair without enough evidence, and so on).
 */
class InvalidDossierTransition extends DomainException
{
}
