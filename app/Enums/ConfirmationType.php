<?php

namespace App\Enums;

enum ConfirmationType: string
{
    case StillThere = 'still_there';
    case Fixed = 'fixed';
    case NotFixed = 'not_fixed';
}
